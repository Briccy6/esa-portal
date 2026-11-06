<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;

class TeacherTimetableController extends Controller
{
    public function index()
    {
        $teacher = Auth::guard('teacher')->user();

        // Get teacher's branch ID
        $teacherBranchId = $teacher->branch_id;

        // Debug: Log teacher info
        \Log::info('Teacher timetable query', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->teacher_name,
            'teacher_branch_id' => $teacherBranchId,
        ]);

        // Get lesson IDs assigned to this teacher in their branch
        $assignedLessonIds = \DB::table('lesson_branch_course')
            ->where('branch_id', $teacherBranchId)
            ->where('teacher_id', $teacher->id)
            ->pluck('lesson_id')
            ->unique()
            ->toArray();

        // Also check direct teacher_id on lessons
        $directLessonIds = \App\Models\Lesson::where('teacher_id', $teacher->id)
            ->pluck('id')
            ->toArray();

        // Merge both sources
        $allLessonIds = array_unique(array_merge($assignedLessonIds, $directLessonIds));

        \Log::info('Teacher assigned lessons', [
            'assigned_lesson_ids' => $assignedLessonIds,
            'direct_lesson_ids' => $directLessonIds,
            'all_lesson_ids' => $allLessonIds,
        ]);

        // Initialize timetables as empty collection
        $timetables = collect();

        if (empty($allLessonIds)) {
            \Log::warning('No lessons assigned to teacher', ['teacher_id' => $teacher->id]);
            return view('teacher.timetables.index', compact('timetables'));
        }

        // Get timetables that have sessions with the teacher's assigned lessons
        $timetables = Timetable::published()
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->where('branch_id', $teacherBranchId)
            ->whereHas('sessions', function($query) use ($allLessonIds) {
                $query->whereIn('lesson_id', $allLessonIds);
            })
            ->with(['branch', 'academicYear', 'term', 'sessions.lesson.teacher', 'sessions.lesson.teachers', 'sessions.courses'])
            ->get();

        // Filter sessions to only show those with the teacher's assigned lessons
        foreach ($timetables as $timetable) {
            $timetable->sessions = $timetable->sessions->filter(function($session) use ($allLessonIds) {
                return in_array($session->lesson_id, $allLessonIds);
            });
        }

        // Remove timetables with no matching sessions
        $filteredTimetables = $timetables->filter(function($timetable) {
            return $timetable->sessions->isNotEmpty();
        })->values();

        // Debug: Log timetable count
        \Log::info('Teacher timetable results', [
            'total_timetables' => $timetables->count(),
            'filtered_timetables' => $filteredTimetables->count(),
        ]);

        return view('teacher.timetables.index', compact('timetables'))->with('timetables', $filteredTimetables);
    }

    public function show(Timetable $timetable)
    {
        $teacher = Auth::guard('teacher')->user();
        $teacherBranchId = $teacher->branch_id;

        // Get lesson IDs assigned to this teacher in their branch
        $assignedLessonIds = \DB::table('lesson_branch_course')
            ->where('branch_id', $teacherBranchId)
            ->where('teacher_id', $teacher->id)
            ->pluck('lesson_id')
            ->unique()
            ->toArray();

        // Also check direct teacher_id on lessons
        $directLessonIds = \App\Models\Lesson::where('teacher_id', $teacher->id)
            ->pluck('id')
            ->toArray();

        // Merge both sources
        $allLessonIds = array_unique(array_merge($assignedLessonIds, $directLessonIds));

        $timetable->load(['branch', 'academicYear', 'term', 'sessions.lesson.teacher', 'sessions.lesson.teachers', 'sessions.courses']);

        // Filter sessions to only show those with the teacher's assigned lessons
        $teacherSessions = $timetable->sessions->filter(function ($session) use ($allLessonIds) {
            return in_array($session->lesson_id, $allLessonIds);
        })->values();

        if ($teacherSessions->isEmpty()) {
            abort(403, 'You are not authorized to view this timetable.');
        }

        return view('teacher.timetables.show', compact('timetable', 'teacherSessions'));
    }
}
