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

        $request->validate([
            'branch_id' => ['required', 'integer', Rule::in($this->getAllowedBranchIds())],
        ]);
        $branchId = (int) $request->get('branch_id');

        return response()->json(
            Course::whereIn('id', LessonBranchCourse::where('branch_id', $branchId)
                ->pluck('course_id')->unique())
                ->orderBy('course_name')
                ->get(['id', 'course_name', 'abbreviation'])
        );
    }

    public function branchLessons(Request $request)
    {
        $this->authorizeDos();

        $request->validate([
            'branch_id' => ['required', 'integer', Rule::in($this->getAllowedBranchIds())],
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        $branchId = (int) $request->get('branch_id');

        return response()->json($this->getBranchLessonOptions(
            $branchId,
            (int) $request->academic_year_id
        ));
    }

    protected function getBranchLessonOptions(int $branchId, ?int $academicYearId = null): array
    {
        if ($branchId <= 0 || ! $academicYearId) {
            return [];
        }

        return $this->getLessonAssignments($branchId)
            ->when($academicYearId, fn ($assignments) => $assignments->filter(
                fn ($assignment) => ! $assignment->lesson?->academic_year_id
                    || (int) $assignment->lesson->academic_year_id === $academicYearId
            ))
            ->filter(fn ($assignment) => $assignment->lesson && $assignment->course)
            ->groupBy('lesson_id')
            ->map(function ($assignments) {
                $first = $assignments->first();

                return [
                    'lesson_id' => (int) $first->lesson_id,
                    'lesson_title' => $first->lesson->title ?: 'Untitled lesson',
                    'courses' => $assignments->unique('course_id')->map(fn ($assignment) => [
                        'id' => (int) $assignment->course_id,
                        'name' => $assignment->course->course_name,
                        'abbreviation' => $assignment->course->abbreviation,
                        'teacher_name' => $assignment->teacher?->teacher_name,
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();
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
        return array_values(array_unique(array_intersect($courseIds, $available)));
    }

    protected function validateScheduleScope(Request $request, ?Timetable $timetable = null): void
    {
        $term = Term::whereKey($request->term_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->first();

        if (! $term) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'term_id' => 'Choose a term that belongs to the selected academic year.',
            ]);
        }

        if (Carbon::parse($request->valid_from)->lt(Carbon::parse($term->start_date))
            || Carbon::parse($request->valid_until)->gt(Carbon::parse($term->end_date))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'valid_from' => 'The timetable validity dates must fall within the selected term.',
            ]);
        }

        foreach ($request->sessions as $index => $sessionData) {
            $lesson = Lesson::find($sessionData['lesson_id']);
            $courseIds = array_values(array_unique(array_map('intval', $sessionData['course_ids'] ?? [])));
            $availableCourseIds = $lesson
                ? $this->getCourseIdsForLesson((int) $request->branch_id, $lesson->id)
                : [];

            if (! $lesson
                || ($lesson->academic_year_id && (int) $lesson->academic_year_id !== (int) $request->academic_year_id)
                || ! $courseIds
                || array_diff($courseIds, $availableCourseIds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "sessions.{$index}.lesson_id" => 'Choose a lesson and one or more courses assigned to that lesson in the selected branch and academic year.',
                ]);
            }

            if ($timetable && !empty($sessionData['session_id'])
                && ! $timetable->sessions()->whereKey($sessionData['session_id'])->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "sessions.{$index}.session_id" => 'A session does not belong to this timetable.',
                ]);
            }
        }
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
            ->whereDate('valid_from', '<=', $timetable->valid_until)
            ->whereDate('valid_until', '>=', $timetable->valid_from)
            ->first();
    }

    protected function getPublishedOverlapIssues(Timetable $timetable): array
    {
        $issues = [];
        $duplicateRooms = [];
        $duplicateTeachers = [];

        $timetable->loadMissing('term');
        $sessions = $timetable->sessions()->with(['lesson.teacher', 'courses'])->get();
        if ($sessions->isEmpty()) {
            $issues[] = 'Add at least one session before approving this timetable.';
        }

        if (! $timetable->term
            || (int) $timetable->term->academic_year_id !== (int) $timetable->academic_year_id
            || ! $timetable->valid_from
            || ! $timetable->valid_until
            || Carbon::parse($timetable->valid_from)->lt(Carbon::parse($timetable->term->start_date ?? '1900-01-01'))
            || Carbon::parse($timetable->valid_until)->gt(Carbon::parse($timetable->term->end_date ?? '1900-01-01'))) {
            $issues[] = 'Choose a term and validity dates that belong to the same academic year and fit within that term.';
        }

        foreach ($sessions as $session) {
            $courseIds = $session->courses->pluck('id')->map(fn ($id) => (int) $id)->all();
            $availableCourseIds = $this->getCourseIdsForLesson((int) $timetable->branch_id, (int) $session->lesson_id);
            if (! $courseIds || array_diff($courseIds, $availableCourseIds)) {
                $lessonTitle = $session->lesson->title ?? 'this lesson';
                $issues[] = "Choose only courses assigned to lesson '{$lessonTitle}' in this branch.";
            }
        }

        $branchTeacherIds = \DB::table('lesson_branch_course')
            ->where('branch_id', $timetable->branch_id)
            ->whereIn('lesson_id', $sessions->pluck('lesson_id')->unique())
            ->get(['lesson_id', 'course_id', 'teacher_id'])
            ->groupBy('lesson_id');

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

                    $teachersFor = function (TimetableSession $item) use ($branchTeacherIds) {
                        $courseIds = $item->courses->pluck('id')->map(fn ($id) => (int) $id);
                        return collect($branchTeacherIds->get($item->lesson_id, []))
                            ->whereIn('course_id', $courseIds)
                            ->pluck('teacher_id')
                            ->filter()
                            ->unique();
                    };
                    $sharedTeachers = $teachersFor($session)->intersect($teachersFor($other));
                    foreach ($sharedTeachers as $teacherId) {
                        $teacherName = \App\Models\Teacher::whereKey($teacherId)->value('teacher_name') ?: 'Assigned teacher';
                        $key = $teacherId . ':' . $session->day_of_week . ':' . min($session->id, $other->id) . ':' . max($session->id, $other->id);
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
        $branches = $dos->branches ?? collect();

        if (! $branches instanceof \Illuminate\Support\Collection) {
            $branches = collect($branches);
        }

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

        $terms = Term::whereIn('academic_year_id', $academicYears->pluck('id'))
            ->orderBy('start_date')
            ->get();

        $activeTerm = $this->getActiveTermForAcademicYear($selectedAcademicYearId);
        $selectedTermId = session()->getOldInput('term_id') ?: $activeTerm?->id;
        $branchLessonsData = $this->getBranchLessonOptions((int) $branchId, (int) $selectedAcademicYearId);

        return view('staff.timetable.create', compact(
            'branches',
            'branchId',
            'academicYears',
            'selectedAcademicYearId',
            'activeTerm',
            'terms',
            'branchLessonsData',
            'selectedTermId'
        ));
    }

    /**
     * Normalize session time inputs to 24-hour H:i format to satisfy validation rules.
     */
    protected function normalizeSessionTimes(Request $request): void
    {
        $data = $request->all();
        if (! empty($data['sessions']) && is_array($data['sessions'])) {
            foreach ($data['sessions'] as $i => $session) {
                foreach (['start_time', 'end_time'] as $field) {
                    if (! empty($session[$field])) {
                        $value = trim($session[$field]);
                        // If value already matches H:i, skip
                        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
                            // ensure leading zero on hour
                            $parts = explode(':', $value);
                            $hour = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                            $data['sessions'][$i][$field] = $hour . ':' . $parts[1];
                            continue;
                        }

                        try {
                            $dt = \Carbon\Carbon::parse($value);
                            $data['sessions'][$i][$field] = $dt->format('H:i');
                        } catch (\Exception $e) {
                            // leave original value; validation will catch it
                        }
                    }
                }
            }
            $request->merge($data);
        }
    }

    public function store(Request $request)
    {
        $dos = $this->authorizeDos();

        // Normalize session times (accept AM/PM or other formats from the UI)
        $this->normalizeSessionTimes($request);

        $allowedBranchIds = $this->getAllowedBranchIds();

        $request->validate([
            'title' => 'required|string|max:255',
            'branch_id' => ['required', Rule::in($allowedBranchIds)],
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date',
            'sessions' => 'required|array|min:1',
            'sessions.*.lesson_id' => 'nullable|exists:lessons,id',
            'sessions.*.day_of_week' => ['required', Rule::in(['0','1','2','3','4','5','6'])],
            'sessions.*.shift' => ['required', Rule::in(['morning', 'afternoon', 'evening'])],
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i',
            'sessions.*.classroom' => 'nullable|string|max:100',
            'sessions.*.notes' => 'nullable|string|max:500',
            'sessions.*.course_ids' => 'nullable|array',
            'sessions.*.course_ids.*' => 'nullable|exists:courses,id',
            'sessions.*.activity_type' => ['required', Rule::in(['lesson', 'self_study', 'group_work', 'reserve'])],
            'sessions.*.reserve_text' => 'nullable|string|max:500',
            'sessions.*.session_type' => ['nullable', Rule::in(['class', 'test'])],
            'sessions.*.minimum_paid_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Custom date validation
        $validFrom = Carbon::parse($request->valid_from);
        $validUntil = Carbon::parse($request->valid_until);

        if ($validUntil->lt($validFrom)) {
            return redirect()->back()->withErrors(['valid_until' => 'End date must be on or after start date.'])->withInput();
        }

        $this->validateScheduleScope($request);
        $termId = (int) $request->term_id;

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
            $lesson = isset($sessionData['lesson_id']) ? Lesson::find($sessionData['lesson_id']) : null;

            $session = TimetableSession::create([
                'timetable_id' => $timetable->id,
                'lesson_id' => $lesson ? $lesson->id : null,
                'day_of_week' => (int) $sessionData['day_of_week'],
                'shift' => $sessionData['shift'] ?? 'morning',
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time'],
                'classroom' => $sessionData['classroom'] ?? null,
                'notes' => $sessionData['notes'] ?? null,
                'reserve_text' => $sessionData['activity_type'] === 'reserve' ? ($sessionData['reserve_text'] ?? '') : null,
                'mark_as_test' => ($sessionData['session_type'] ?? 'class') === 'test',
                'session_type' => $sessionData['session_type'] ?? 'class',
                'activity_type' => $sessionData['activity_type'] ?? 'lesson',
                'minimum_paid_percentage' => ($sessionData['session_type'] ?? 'class') === 'test'
                    ? ($sessionData['minimum_paid_percentage'] ?? 0)
                    : 0,
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
        $allowedBranchIds = $this->getAllowedBranchIds();

        $timetables = Timetable::with(['branch', 'academicYear', 'term'])
            ->whereIn('branch_id', $allowedBranchIds)
            ->orderByDesc('created_at')
            ->get();

        return view('staff.timetable.index', compact('timetables'));
    }

    public function destroy(Timetable $timetable)
    {
        $this->authorizeDos();
        abort_unless(in_array($timetable->branch_id, $this->getAllowedBranchIds()), 403);

        // Delete sessions first
        $timetable->sessions()->delete();

        // Delete the timetable
        $timetable->delete();

        return redirect()->route('dos.timetable.index')
            ->with('success', 'Timetable deleted successfully.');
    }

    public function edit(Timetable $timetable)
    {
        $dos = $this->authorizeDos();
        abort_unless(in_array($timetable->branch_id, $this->getAllowedBranchIds()), 403);

        $branches = $dos->branches ?? collect();
        if (! $branches instanceof \Illuminate\Support\Collection) {
            $branches = collect($branches);
        }
        if ($branches->isEmpty()) {
            $branches = Branch::orderBy('name')->get();
        }

        $branchId = $timetable->branch_id;
        try {
            $academicYears = AcademicYear::whereHas('branches', function ($query) use ($branches) {
                $query->whereIn('branches.id', $branches->pluck('id'));
            })->orderBy('year', 'desc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('AcademicYear->branches query failed: ' . $e->getMessage());
            $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        }

        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::orderBy('year', 'desc')->get();
            session()->flash('warning', 'Academic years could not be filtered by branch. Showing all academic years. Please run migrations to restore pivot tables.');
        }

        $selectedAcademicYearId = session()->getOldInput('academic_year_id') ?: $timetable->academic_year_id;
        $selectedAcademicYearId = $selectedAcademicYearId ?: $timetable->academic_year_id;

        $terms = Term::whereIn('academic_year_id', $academicYears->pluck('id'))
            ->orderBy('start_date')
            ->get();

        $activeTerm = $this->getActiveTermForAcademicYear($selectedAcademicYearId);
        $selectedTermId = session()->getOldInput('term_id') ?: ($timetable->term_id ?: $activeTerm?->id);
        $branchLessonsData = $this->getBranchLessonOptions($branchId, (int) $selectedAcademicYearId);

        $timetable->load(['sessions.lesson.teacher', 'sessions.courses', 'term']);

        return view('staff.timetable.edit', compact(
            'timetable',
            'branches',
            'branchId',
            'academicYears',
            'selectedAcademicYearId',
            'activeTerm',
            'terms',
            'branchLessonsData',
            'selectedTermId'
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

        // Normalize session times submitted from the UI
        $this->normalizeSessionTimes($request);

        $request->validate([
            'title' => 'required|string|max:255',
            'branch_id' => ['required', Rule::in($this->getAllowedBranchIds())],
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date',
            'sessions' => 'required|array|min:1',
            'sessions.*.lesson_id' => 'nullable|exists:lessons,id',
            'sessions.*.day_of_week' => ['required', Rule::in(['0','1','2','3','4','5','6'])],
            'sessions.*.shift' => ['required', Rule::in(['morning', 'afternoon', 'evening'])],
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i',
            'sessions.*.classroom' => 'nullable|string|max:100',
            'sessions.*.notes' => 'nullable|string|max:500',
            'sessions.*.course_ids' => 'nullable|array',
            'sessions.*.course_ids.*' => 'nullable|exists:courses,id',
            'sessions.*.activity_type' => ['required', Rule::in(['lesson', 'self_study', 'group_work', 'reserve'])],
            'sessions.*.reserve_text' => 'nullable|string|max:500',
            'sessions.*.session_type' => ['nullable', Rule::in(['class', 'test'])],
            'sessions.*.minimum_paid_percentage' => 'nullable|numeric|min:0|max:100',
            'sessions.*.session_id' => 'nullable|integer|exists:timetable_sessions,id',
        ]);

        // Custom date validation
        $validFrom = Carbon::parse($request->valid_from);
        $validUntil = Carbon::parse($request->valid_until);

        if ($validUntil->lt($validFrom)) {
            return redirect()->back()->withErrors(['valid_until' => 'End date must be on or after start date.'])->withInput();
        }

        $this->validateScheduleScope($request, $timetable);
        $termId = (int) $request->term_id;

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
            $lesson = isset($sessionData['lesson_id']) ? Lesson::find($sessionData['lesson_id']) : null;

            if (!empty($sessionData['session_id']) && $existingSessions->has($sessionData['session_id'])) {
                $session = $existingSessions->get($sessionData['session_id']);
                $processedIds[] = $session->id;

                if ($timetable->status === 'published') {
                    $notifications = array_merge($notifications, $this->gatherSessionNotifications($session, $sessionData, $request->branch_id));
                }

                $session->update([
                    'lesson_id' => $lesson ? $lesson->id : null,
                    'day_of_week' => (int) $sessionData['day_of_week'],
                    'shift' => $sessionData['shift'] ?? 'morning',
                    'start_time' => $sessionData['start_time'],
                    'end_time' => $sessionData['end_time'],
                    'classroom' => $sessionData['classroom'] ?? null,
                    'notes' => $sessionData['notes'] ?? null,
                    'reserve_text' => ($sessionData['activity_type'] ?? 'lesson') === 'reserve' ? ($sessionData['reserve_text'] ?? '') : null,
                    'mark_as_test' => ($sessionData['session_type'] ?? 'class') === 'test',
                    'session_type' => $sessionData['session_type'] ?? 'class',
                    'activity_type' => $sessionData['activity_type'] ?? 'lesson',
                    'minimum_paid_percentage' => ($sessionData['session_type'] ?? 'class') === 'test'
                        ? ($sessionData['minimum_paid_percentage'] ?? 0)
                        : 0,
                ]);

                $courseIds = $this->getSessionCourseIdsFromInput($sessionData, $request->branch_id, $lesson);
                $session->courses()->sync($courseIds);
            } else {
                $session = TimetableSession::create([
                    'timetable_id' => $timetable->id,
                    'lesson_id' => $lesson ? $lesson->id : null,
                    'day_of_week' => (int) $sessionData['day_of_week'],
                    'shift' => $sessionData['shift'] ?? 'morning',
                    'start_time' => $sessionData['start_time'],
                    'end_time' => $sessionData['end_time'],
                    'classroom' => $sessionData['classroom'] ?? null,
                    'notes' => $sessionData['notes'] ?? null,
                    'reserve_text' => ($sessionData['activity_type'] ?? 'lesson') === 'reserve' ? ($sessionData['reserve_text'] ?? '') : null,
                    'mark_as_test' => ($sessionData['session_type'] ?? 'class') === 'test',
                    'session_type' => $sessionData['session_type'] ?? 'class',
                    'activity_type' => $sessionData['activity_type'] ?? 'lesson',
                    'minimum_paid_percentage' => ($sessionData['session_type'] ?? 'class') === 'test'
                        ? ($sessionData['minimum_paid_percentage'] ?? 0)
                        : 0,
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

        if ($timetable->status !== 'draft') {
            return redirect()->route('dos.timetable.edit', $timetable)
                ->with('error', 'Only draft timetables can be approved.');
        }

        $issues = $this->getPublishedOverlapIssues($timetable);
        if ($issues) {
            return redirect()->route('dos.timetable.edit', $timetable)
                ->with('validation_errors', $issues)
                ->with('error', 'Review the schedule conflicts before approving this timetable.');
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
