<?php

namespace App\Http\Controllers;

use App\Models\LessonBranchCourse;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountantTimetableController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $branchIds = $staff->branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            $branchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        $validated = $request->validate([
            'branch_id' => ['nullable', 'integer', Rule::in($branchIds)],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'shift' => ['nullable', Rule::in(['morning', 'afternoon', 'evening', 'weekend'])],
        ]);

        $timetables = Timetable::published()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->when(!empty($validated['branch_id']), function ($query) use ($validated) {
                $query->where('branch_id', $validated['branch_id']);
            })
            ->with([
                'branch',
                'academicYear',
                'term',
                'sessions.lesson.teacher',
                'sessions.lesson.teachers',
                'sessions.courses',
                'sessions.shifts',
            ])
            ->orderBy('valid_from', 'desc')
            ->get();

        $lessonIds = $timetables->flatMap(fn ($timetable) => $timetable->sessions)
            ->pluck('lesson_id')
            ->filter()
            ->unique();
        $branchTeacherAssignments = LessonBranchCourse::with('teacher')
            ->whereIn('branch_id', $timetables->pluck('branch_id')->unique())
            ->whereIn('lesson_id', $lessonIds)
            ->whereNotNull('teacher_id')
            ->get()
            ->keyBy(fn ($assignment) => implode(':', [
                $assignment->branch_id,
                $assignment->course_id,
                $assignment->lesson_id,
            ]));

        foreach ($timetables as $timetable) {
            foreach ($timetable->sessions as $session) {
                $teacherNames = $session->courses
                    ->map(function ($course) use ($branchTeacherAssignments, $session, $timetable) {
                        $key = implode(':', [$timetable->branch_id, $course->id, $session->lesson_id]);

                        return $branchTeacherAssignments->get($key)?->teacher?->teacher_name;
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $session->setAttribute('branch_teacher_names', $teacherNames);
            }
        }

        $availableCourses = $timetables
            ->flatMap(fn ($timetable) => $timetable->sessions->flatMap->courses)
            ->unique('id')
            ->sortBy('course_name')
            ->values();

        if (!empty($validated['course_id'])) {
            $timetables = $timetables->map(function ($timetable) use ($validated) {
                $timetable->setRelation('sessions', $timetable->sessions->filter(function ($session) use ($validated) {
                    $matchesCourse = $session->courses->contains('id', (int) $validated['course_id']);
                    $matchesShift = empty($validated['shift']) || $this->sessionHasShift($session, $validated['shift']);

                    return $matchesCourse && $matchesShift;
                })->values());

                return $timetable;
            })->filter(fn ($timetable) => $timetable->sessions->isNotEmpty())->values();
        } elseif (!empty($validated['shift'])) {
            $timetables = $timetables->map(function ($timetable) use ($validated) {
                $timetable->setRelation('sessions', $timetable->sessions->filter(
                    fn ($session) => $this->sessionHasShift($session, $validated['shift'])
                )->values());

                return $timetable;
            })->filter(fn ($timetable) => $timetable->sessions->isNotEmpty())->values();
        }

        $branches = \App\Models\Branch::whereIn('id', $branchIds)->orderBy('name')->get();

        return view('accountant.timetables.index', [
            'timetables' => $timetables,
            'branches' => $branches,
            'courses' => $availableCourses,
            'filters' => [
                'branch_id' => $validated['branch_id'] ?? '',
                'course_id' => $validated['course_id'] ?? '',
                'shift' => $validated['shift'] ?? '',
            ],
        ]);
    }

    private function sessionHasShift($session, string $selectedShift): bool
    {
        $acceptedValues = $selectedShift === 'morning' ? ['morning', 'day'] : [$selectedShift];
        $sessionValues = $session->shifts->isNotEmpty()
            ? $session->shifts->pluck('shift')->all()
            : [$session->shift];

        return count(array_intersect($acceptedValues, $sessionValues)) > 0;
    }
}
