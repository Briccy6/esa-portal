<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\TimetableSession;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Models\LessonBranchCourse;
use App\Models\Student;
use App\Models\Term;
use App\Mail\TimetableChangedMail;
use App\Mail\TimetableCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DosTimetableController extends Controller
{
    protected function authorizeDos()
    {
        $user = Auth::guard('staff')->user();
        $role = $user?->role ? strtoupper(trim($user->role)) : null;
        abort_unless($user && $role === 'DOS', 403, 'Unauthorized');
        return $user;
    }

    protected function getAllowedBranchIds(): array
    {
        $user = Auth::guard('staff')->user();
        if (! $user) {
            return [];
        }

        $branchIds = $user->branches->pluck('id')->toArray();

        // If the DOS user has no branches assigned, allow all branches (fallback).
        if (empty($branchIds)) {
            return Branch::orderBy('name')->pluck('id')->toArray();
        }

        return $branchIds;
    }

    public function branchCourses(Request $request)
    {
        $this->authorizeDos();

        $branchId = (int) $request->get('branch_id');
        if (! $branchId) {
            return response()->json([], 200);
        }

        $courseIds = LessonBranchCourse::where('branch_id', $branchId)->pluck('course_id')->unique()->toArray();
        $courses = Course::whereIn('id', $courseIds)->orderBy('course_name')->get(['id', 'course_name']);

        return response()->json($courses);
    }

    protected function getLessonAssignments(int $branchId)
    {
        return LessonBranchCourse::with(['lesson.teacher', 'course', 'teacher'])
            ->where('branch_id', $branchId)
            ->get();
    }

    protected function getCourseIdsForLesson(int $branchId, int $lessonId): array
    {
        return LessonBranchCourse::where('branch_id', $branchId)
            ->where('lesson_id', $lessonId)
            ->pluck('course_id')
            ->unique()
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();
    }

    protected function getSessionCourseIdsFromInput(array $sessionData, int $branchId, Lesson $lesson): array
    {
        $courseIds = array_filter(array_map('intval', $sessionData['course_ids'] ?? []));
        $available = $this->getCourseIdsForLesson($branchId, $lesson->id);
        $selected = array_values(array_intersect($courseIds, $available));
        if (empty($selected)) {
            if ($lesson->course_id) {
                $selected = [$lesson->course_id];
            }
        }
        return array_unique($selected);
    }

    protected function collectCourseIdsFromSession(TimetableSession $session): array
    {
        $pivotCourses = $session->courses()->pluck('courses.id')->map(fn($id) => (int) $id)->toArray();
        return $pivotCourses ?: [$session->lesson->course_id];
    }

    protected function getActiveTermForAcademicYear($academicYearId): ?Term
    {
        if (!$academicYearId) {
            return null;
        }

        return Term::where('academic_year_id', $academicYearId)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    protected function queueTimetableChangeEmails(Timetable $timetable, array $courseIds, string $summary, array $details = [])
    {
        $emails = Student::where('branch_id', $timetable->branch_id)
            ->whereIn('course_id', $courseIds)
            ->whereNotNull('email')
            ->pluck('email')
            ->unique();

        foreach ($emails as $email) {
            Mail::to($email)->queue(new TimetableChangedMail($timetable, (object) ['full_name' => 'Student', 'email' => $email], $summary, $details));
        }
    }

    protected function getActiveBranchTimetableConflict(Timetable $timetable): ?Timetable
    {
        return Timetable::published()
            ->where('branch_id', $timetable->branch_id)
            ->where('id', '!=', $timetable->id)
            ->whereDate('valid_until', '>=', now())
            ->first();
    }

    protected function getPublishedOverlapIssues(Timetable $timetable): array
    {
        $issues = [];
        $duplicateRooms = [];
        $duplicateTeachers = [];

        $sessions = $timetable->sessions()->with('lesson.teacher')->get();

        foreach ($sessions as $index => $session) {
            foreach ($sessions->slice($index + 1) as $other) {
                if ($session->conflictsWith($other)) {
                    if ($session->classroom && $other->classroom && $session->classroom === $other->classroom) {
                        $key = $session->classroom . ':' . $session->day_of_week . ':' . min($session->id, $other->id) . ':' . max($session->id, $other->id);
                        if (!isset($duplicateRooms[$key])) {
                            $duplicateRooms[$key] = true;
                            $issues[] = "Room {$session->classroom} is double-booked on {$this->formatDay($session->day_of_week)} between {$session->start_time}–{$session->end_time} and {$other->start_time}–{$other->end_time}.";
                        }
                    }

                    $sessionTeacher = $session->lesson->teacher_id ?? null;
                    $otherTeacher = $other->lesson->teacher_id ?? null;
                    if ($sessionTeacher && $otherTeacher && $sessionTeacher === $otherTeacher) {
                        $teacherName = $session->lesson->teacher->teacher_name ?? 'Assigned teacher';
                        $key = $sessionTeacher . ':' . $session->day_of_week . ':' . min($session->id, $other->id) . ':' . max($session->id, $other->id);
                        if (!isset($duplicateTeachers[$key])) {
                            $duplicateTeachers[$key] = true;
                            $issues[] = "Teacher {$teacherName} is scheduled for overlapping sessions on {$this->formatDay($session->day_of_week)} between {$session->start_time}–{$session->end_time} and {$other->start_time}–{$other->end_time}.";
                        }
                    }
                }
            }
        }

        if ($existing = $this->getActiveBranchTimetableConflict($timetable)) {
            $issues[] = "This branch already has an active published timetable ({$existing->title}) until {$existing->valid_until}. New timetable cannot be published until the existing timetable expires.";
        }

        return $issues;
    }

    protected function formatDay(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            default => 'Sunday',
        };
    }

    public function create(Request $request)
    {
        $dos = $this->authorizeDos();
        $branches = $dos->branches;

        if ($branches->isEmpty()) {
            $branches = Branch::orderBy('name')->get();
        }

        $branchId = $request->get('branch_id', $branches->first()?->id);
        if (!$branchId && $branches->isNotEmpty()) {
            $branchId = $branches->first()->id;
        }

        if (!$branchId) {
            abort(403, 'No branches are available to create a timetable.');
        }
        if (!$branches->pluck('id')->contains($branchId)) {
            $branchId = $branches->first()->id;
        }

        try {
            $academicYears = AcademicYear::whereHas('branches', function ($query) use ($branches) {
                $query->whereIn('branches.id', $branches->pluck('id'));
            })->orderBy('year', 'desc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('AcademicYear->branches query failed: ' . $e->getMessage());
            $academicYears = collect();
        }

        // If filtering by branch yields no results (missing pivot rows or migration not run),
        // fall back to showing all academic years so the DOS can continue working.
        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::orderBy('year', 'desc')->get();
            session()->flash('warning', 'Academic years could not be filtered by branch. Showing all academic years. Please run migrations to restore pivot tables.');
        }

        $selectedAcademicYearId = session()->getOldInput('academic_year_id') ?: ($academicYears->first()->id ?? null);
        $selectedAcademicYearId = $selectedAcademicYearId ?: ($academicYears->first()->id ?? null);

        $terms = Term::where('academic_year_id', $selectedAcademicYearId)
            ->orderBy('start_date')
            ->get();

        $activeTerm = $this->getActiveTermForAcademicYear($selectedAcademicYearId);

        $lessonAssignments = $this->getLessonAssignments($branchId);
        $courseIds = $lessonAssignments->pluck('course_id')->unique();
        $teacherIds = $lessonAssignments->pluck('teacher_id')->unique();

        $courses = Course::whereIn('id', $courseIds)->get();
        $teachers = \App\Models\Teacher::whereIn('id', $teacherIds)->get();

        return view('staff.timetable.create', compact(
            'branches',
            'branchId',
            'academicYears',
            'selectedAcademicYearId',
            'activeTerm',
            'terms',
            'lessonAssignments',
            'courses',
            'teachers'
        ));
    }

    public function store(Request $request)
    {
        $dos = $this->authorizeDos();

        $allowedBranchIds = $this->getAllowedBranchIds();

        $activeTerm = $this->getActiveTermForAcademicYear($request->academic_year_id);
        $termRules = $activeTerm ? ['nullable', 'exists:terms,id'] : ['required', 'exists:terms,id'];

        $request->validate([
            'title' => 'required|string|max:255',
            'branch_id' => ['required', Rule::in($allowedBranchIds)],
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => $termRules,
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'sessions' => 'required|array|min:1',
            'sessions.*.lesson_id' => 'required|exists:lessons,id',
            'sessions.*.day_of_week' => ['required', Rule::in(['0','1','2','3','4','5','6'])],
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i',
            'sessions.*.classroom' => 'nullable|string|max:100',
            'sessions.*.notes' => 'nullable|string|max:500',
            'sessions.*.course_ids' => 'nullable|array',
            'sessions.*.course_ids.*' => 'nullable|exists:courses,id',
            'sessions.*.mark_as_test' => 'nullable|boolean',
        ]);

        $termId = $activeTerm ? $activeTerm->id : $request->term_id;

        if (!$termId) {
            return redirect()->back()->withErrors(['term_id' => 'Please select a term for the selected academic year.'])->withInput();
        }

        foreach ($request->sessions as $index => $sessionData) {
            $start = Carbon::createFromFormat('H:i', $sessionData['start_time']);
            $end = Carbon::createFromFormat('H:i', $sessionData['end_time']);
            if (!$end->gt($start)) {
                return redirect()->back()->withErrors(["sessions.{$index}.end_time" => 'End time must be after start time.'])->withInput();
            }
        }

        $timetable = Timetable::create([
            'title' => $request->title,
            'image' => $request->get('image', ''),
            'branch_id' => $request->branch_id,
            'academic_year_id' => $request->academic_year_id,
            'term_id' => $termId,
            'status' => 'draft',
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        foreach ($request->sessions as $sessionData) {
            $lesson = Lesson::find($sessionData['lesson_id']);
            if (!$lesson) {
                continue;
            }

            $session = TimetableSession::create([
                'timetable_id' => $timetable->id,
                'lesson_id' => $lesson->id,
                'day_of_week' => (int) $sessionData['day_of_week'],
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time'],
                'classroom' => $sessionData['classroom'] ?? null,
                'notes' => $sessionData['notes'] ?? null,
                'mark_as_test' => !empty($sessionData['mark_as_test']),
            ]);

            $courseIds = $this->getSessionCourseIdsFromInput($sessionData, $request->branch_id, $lesson);
            if (!empty($courseIds)) {
                $session->courses()->sync($courseIds);
            }
        }

        return redirect()->route('dos.timetable.edit', $timetable->id)
            ->with('success', 'Weekly timetable saved as draft. Please validate and publish when ready.');
    }

    public function index()
    {
        $dos = $this->authorizeDos();
        $branchIds = $dos->branches->pluck('id')->toArray();

        $timetables = Timetable::with(['branch', 'academicYear', 'term'])
            ->whereIn('branch_id', $branchIds)
            ->orderByDesc('created_at')
            ->get();

        return view('staff.timetable.index', compact('timetables'));
    }

    public function edit(Timetable $timetable)
    {
        $dos = $this->authorizeDos();
        abort_unless(in_array($timetable->branch_id, $this->getAllowedBranchIds()), 403);

        $branches = $dos->branches;
        $branchId = $timetable->branch_id;
        try {
            $academicYears = AcademicYear::whereHas('branches', function ($query) use ($branches) {
                $query->whereIn('branches.id', $branches->pluck('id'));
            })->orderBy('year', 'desc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('AcademicYear->branches query failed: ' . $e->getMessage());
            $academicYears = collect();
        }

        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::orderBy('year', 'desc')->get();
            session()->flash('warning', 'Academic years could not be filtered by branch. Showing all academic years. Please run migrations to restore pivot tables.');
        }

        $lessonAssignments = $this->getLessonAssignments($branchId);
        $courseIds = $lessonAssignments->pluck('course_id')->unique();
        $teacherIds = $lessonAssignments->pluck('teacher_id')->unique();

        $courses = Course::whereIn('id', $courseIds)->get();
        $teachers = \App\Models\Teacher::whereIn('id', $teacherIds)->get();

        $selectedAcademicYearId = session()->getOldInput('academic_year_id') ?: $timetable->academic_year_id;
        $selectedAcademicYearId = $selectedAcademicYearId ?: $timetable->academic_year_id;

        $terms = Term::where('academic_year_id', $selectedAcademicYearId)
            ->orderBy('start_date')
            ->get();

        $activeTerm = $this->getActiveTermForAcademicYear($selectedAcademicYearId);

        $timetable->load(['sessions.lesson.teacher', 'sessions.courses', 'term']);

        return view('staff.timetable.edit', compact(
            'timetable',
            'branches',
            'branchId',
            'academicYears',
            'selectedAcademicYearId',
            'activeTerm',
            'terms',
            'lessonAssignments',
            'courses',
            'teachers'
        ));
    }

    protected function gatherSessionNotifications(TimetableSession $oldSession, array $newSessionData, int $branchId): array
    {
        $notifications = [];
        $oldCourseIds = $this->collectCourseIdsFromSession($oldSession);
        $lesson = Lesson::find($newSessionData['lesson_id']);
        if (!$lesson) {
            return $notifications;
        }
        $newCourseIds = $this->getSessionCourseIdsFromInput($newSessionData, $branchId, $lesson);

        if ($oldSession->lesson_id !== $lesson->id) {
            $notifications[] = [
                'course_ids' => array_unique(array_merge($oldCourseIds, $newCourseIds)),
                'summary' => 'Timetable lesson changed',
                'details' => [
                    "A session originally assigned to lesson '{$oldSession->lesson->title}' was changed to '{$lesson->title}'.",
                ],
            ];
        }

        if ($oldSession->start_time !== $newSessionData['start_time'] || $oldSession->end_time !== $newSessionData['end_time'] || $oldSession->classroom !== ($newSessionData['classroom'] ?? null)) {
            $notifications[] = [
                'course_ids' => $oldCourseIds,
                'summary' => 'Timetable session time or room changed',
                'details' => [
                    "A session for lesson '{$lesson->title}' was rescheduled from {$oldSession->start_time}-{$oldSession->end_time} in room {$oldSession->classroom} to {$newSessionData['start_time']}-{$newSessionData['end_time']} in room " . ($newSessionData['classroom'] ?? 'TBA') . ".",
                ],
            ];
        }

        $addedCourses = array_diff($newCourseIds, $oldCourseIds);
        if (!empty($addedCourses)) {
            $addedCourseNames = Course::whereIn('id', $addedCourses)->pluck('course_name')->toArray();
            $notifications[] = [
                'course_ids' => $addedCourses,
                'summary' => 'New course added to timetable session',
                'details' => [
                    "The session for lesson '{$lesson->title}' was extended to include: " . implode(', ', $addedCourseNames) . ".",
                ],
            ];
        }

        return $notifications;
    }

    public function update(Request $request, Timetable $timetable)
    {
        $dos = $this->authorizeDos();
        abort_unless(in_array($timetable->branch_id, $this->getAllowedBranchIds()), 403);

        $activeTerm = $this->getActiveTermForAcademicYear($request->academic_year_id);
        $termRules = $activeTerm ? ['nullable', 'exists:terms,id'] : ['required', 'exists:terms,id'];

        $request->validate([
            'title' => 'required|string|max:255',
            'branch_id' => ['required', Rule::in($this->getAllowedBranchIds())],
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => $termRules,
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'sessions' => 'required|array|min:1',
            'sessions.*.lesson_id' => 'required|exists:lessons,id',
            'sessions.*.day_of_week' => ['required', Rule::in(['0','1','2','3','4','5','6'])],
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i',
            'sessions.*.classroom' => 'nullable|string|max:100',
            'sessions.*.notes' => 'nullable|string|max:500',
            'sessions.*.course_ids' => 'nullable|array',
            'sessions.*.course_ids.*' => 'nullable|exists:courses,id',
            'sessions.*.mark_as_test' => 'nullable|boolean',
            'sessions.*.session_id' => 'nullable|integer|exists:timetable_sessions,id',
        ]);

        $termId = $activeTerm ? $activeTerm->id : $request->term_id;

        if (!$termId) {
            return redirect()->back()->withErrors(['term_id' => 'Please select a term for the selected academic year.'])->withInput();
        }

        foreach ($request->sessions as $index => $sessionData) {
            $start = Carbon::createFromFormat('H:i', $sessionData['start_time']);
            $end = Carbon::createFromFormat('H:i', $sessionData['end_time']);
            if (!$end->gt($start)) {
                return redirect()->back()->withErrors(["sessions.{$index}.end_time" => 'End time must be after start time.'])->withInput();
            }
        }

        $notifications = [];
        $existingSessions = $timetable->sessions()->with(['courses', 'lesson'])->get()->keyBy('id');
        $processedIds = [];

        $termId = $activeTerm ? $activeTerm->id : $request->term_id;

        if (!$termId) {
            return redirect()->back()->withErrors(['term_id' => 'Please select a term for the selected academic year.'])->withInput();
        }

        $timetable->update([
            'title' => $request->title,
            'image' => $request->get('image', ''),
            'branch_id' => $request->branch_id,
            'academic_year_id' => $request->academic_year_id,
            'term_id' => $termId,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        foreach ($request->sessions as $sessionData) {
            $lesson = Lesson::find($sessionData['lesson_id']);
            if (!$lesson) {
                continue;
            }

            if (!empty($sessionData['session_id']) && $existingSessions->has($sessionData['session_id'])) {
                $session = $existingSessions->get($sessionData['session_id']);
                $processedIds[] = $session->id;

                if ($timetable->status === 'published') {
                    $notifications = array_merge($notifications, $this->gatherSessionNotifications($session, $sessionData, $request->branch_id));
                }

                $session->update([
                    'lesson_id' => $lesson->id,
                    'day_of_week' => (int) $sessionData['day_of_week'],
                    'start_time' => $sessionData['start_time'],
                    'end_time' => $sessionData['end_time'],
                    'classroom' => $sessionData['classroom'] ?? null,
                    'notes' => $sessionData['notes'] ?? null,
                    'mark_as_test' => !empty($sessionData['mark_as_test']),
                ]);

                $courseIds = $this->getSessionCourseIdsFromInput($sessionData, $request->branch_id, $lesson);
                $session->courses()->sync($courseIds);
            } else {
                $session = TimetableSession::create([
                    'timetable_id' => $timetable->id,
                    'lesson_id' => $lesson->id,
                    'day_of_week' => (int) $sessionData['day_of_week'],
                    'start_time' => $sessionData['start_time'],
                    'end_time' => $sessionData['end_time'],
                    'classroom' => $sessionData['classroom'] ?? null,
                    'notes' => $sessionData['notes'] ?? null,
                    'mark_as_test' => !empty($sessionData['mark_as_test']),
                ]);

                $processedIds[] = $session->id;
                $courseIds = $this->getSessionCourseIdsFromInput($sessionData, $request->branch_id, $lesson);
                $session->courses()->sync($courseIds);

                if ($timetable->status === 'published' && !empty($courseIds)) {
                    $notifications[] = [
                        'course_ids' => $courseIds,
                        'summary' => 'New timetable session added',
                        'details' => [
                            "A new session for lesson '{$lesson->title}' was added on {$this->formatDay((int) $sessionData['day_of_week'])} from {$sessionData['start_time']} to {$sessionData['end_time']}.",
                        ],
                    ];
                }
            }
        }

        $deletedSessions = $existingSessions->except($processedIds);
        foreach ($deletedSessions as $deletedSession) {
            if ($timetable->status === 'published') {
                $notifications[] = [
                    'course_ids' => $this->collectCourseIdsFromSession($deletedSession),
                    'summary' => 'Timetable session removed',
                    'details' => [
                        "A scheduled session for lesson '{$deletedSession->lesson->title}' was removed from the active timetable.",
                    ],
                ];
            }
            $deletedSession->delete();
        }

        if ($timetable->status === 'published' && !empty($notifications)) {
            foreach ($notifications as $notification) {
                $this->queueTimetableChangeEmails($timetable, $notification['course_ids'], $notification['summary'], $notification['details']);
            }
        }

        return redirect()->route('dos.timetable.edit', $timetable->id)->with('success', 'Timetable updated successfully.');
    }

    public function validateTimetable(Timetable $timetable)
    {
        $this->authorizeDos();
        abort_unless(in_array($timetable->branch_id, $this->getAllowedBranchIds()), 403);

        $issues = $this->getPublishedOverlapIssues($timetable);
        return response()->json([
            'valid' => empty($issues),
            'issues' => $issues,
        ]);
    }

    public function publish(Timetable $timetable)
    {
        $this->authorizeDos();
        abort_unless(in_array($timetable->branch_id, $this->getAllowedBranchIds()), 403);

        $issues = $this->getPublishedOverlapIssues($timetable);
        if (!empty($issues)) {
            return redirect()->back()->withErrors(['publish' => 'The timetable cannot be published until validation issues are resolved.'])->with('validation_errors', $issues);
        }

        $timetable->update(['status' => 'published']);

        $courseIds = $timetable->sessions()->with('lesson')->get()->flatMap(function ($session) {
            return $this->collectCourseIdsFromSession($session);
        })->unique()->values()->toArray();

        if (!empty($courseIds)) {
            $this->queueTimetableChangeEmails($timetable, $courseIds, 'New timetable published', [
                "A new published timetable '{$timetable->title}' is now active for your branch.",
            ]);
        }

        return redirect()->route('dos.timetable.edit', $timetable->id)->with('success', 'Timetable published successfully.');
    }
}
