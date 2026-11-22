<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonAssignment;
use App\Models\LessonBranchCourse;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Term;
use App\Services\AssignmentReportCardRefresher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    private const ALLOWED_FILE_TYPES = [
        'zip', 'pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff',
    ];

    public function index()
    {
        $teacher = auth('teacher')->user();
        $assignments = LessonAssignment::with(['lesson', 'branch', 'term'])
            ->withCount(['submissions', 'marks'])
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('due_at')
            ->get();

        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $teacher = auth('teacher')->user();
        $teacherLessonBranches = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->get(['lesson_id', 'branch_id'])
            ->unique(fn (LessonBranchCourse $row) => $row->lesson_id.':'.$row->branch_id);

        $targets = collect();
        if ($teacherLessonBranches->isNotEmpty()) {
            $targets = LessonBranchCourse::with(['lesson', 'branch', 'course'])
                ->where(function ($query) use ($teacherLessonBranches) {
                    foreach ($teacherLessonBranches as $row) {
                        $query->orWhere(function ($query) use ($row) {
                            $query->where('lesson_id', $row->lesson_id)
                                ->where('branch_id', $row->branch_id);
                        });
                    }
                })
                ->get()
                ->groupBy(fn (LessonBranchCourse $row) => $row->lesson_id.':'.$row->branch_id)
                ->map(function ($rows) {
                    $first = $rows->first();

                    return [
                        'lesson' => $first->lesson,
                        'branch' => $first->branch,
                        'courses' => $rows->pluck('course.course_name')->filter()->unique()->values(),
                    ];
                })
                ->values();
        }
        $terms = Term::with('academicYear')->orderByDesc('start_date')->get();

        return view('teacher.assignments.create', compact('targets', 'terms'));
    }

    public function store(Request $request)
    {
        $teacher = auth('teacher')->user();
        $validated = $request->validate([
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'term_id' => ['required', 'integer', 'exists:terms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'submission_method' => ['required', 'in:system,paper,in_class'],
            'allowed_file_types' => ['required_if:submission_method,system', 'array', 'min:1'],
            'allowed_file_types.*' => ['required', 'in:'.implode(',', self::ALLOWED_FILE_TYPES)],
            'due_at' => ['required', 'date', 'after:now'],
            'marks_out_of' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $assigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $validated['lesson_id'])
            ->where('branch_id', $validated['branch_id'])
            ->exists();
        if (! $assigned) {
            throw ValidationException::withMessages([
                'lesson_id' => 'You are not assigned to this lesson at the selected branch.',
            ]);
        }

        $lesson = Lesson::findOrFail($validated['lesson_id']);
        $term = Term::findOrFail($validated['term_id']);
        if ($lesson->academic_year_id && (int) $term->academic_year_id !== (int) $lesson->academic_year_id) {
            throw ValidationException::withMessages([
                'term_id' => 'Choose a term from the lesson academic year.',
            ]);
        }

        LessonAssignment::create([
            ...$validated,
            'teacher_id' => $teacher->id,
            'allowed_file_types' => $validated['submission_method'] === 'system'
                ? array_values(array_unique($validated['allowed_file_types']))
                : null,
        ]);

        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment created.');
    }

    public function editDeadline(LessonAssignment $assignment)
    {
        $assignment = $this->teacherAssignment($assignment);

        return view('teacher.assignments.edit', compact('assignment'));
    }

    public function updateDeadline(Request $request, LessonAssignment $assignment)
    {
        $assignment = $this->teacherAssignment($assignment);
        $validated = $request->validate([
            'due_at' => ['required', 'date'],
        ]);

        $assignment->update($validated);

        if ($assignment->due_at->isFuture()) {
            $missingMarks = $assignment->marks()
                ->where('status', 'missing')
                ->whereNotIn('student_id', $assignment->submissions()->select('student_id'))
                ->with('student')
                ->get();
            foreach ($missingMarks as $mark) {
                $mark->delete();
                app(AssignmentReportCardRefresher::class)->refresh($assignment, $mark->student);
            }
        }

        return redirect()->route('teacher.assignments.show', $assignment)
            ->with('success', 'Submission deadline updated.');
    }

    public function configure(LessonAssignment $assignment)
    {
        $assignment = $this->teacherAssignment($assignment);

        return view('teacher.assignments.configure', compact('assignment'));
    }

    public function updateConfiguration(Request $request, LessonAssignment $assignment)
    {
        $assignment = $this->teacherAssignment($assignment);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'submission_method' => ['required', 'in:system,paper,in_class'],
            'allowed_file_types' => ['required_if:submission_method,system', 'array', 'min:1'],
            'allowed_file_types.*' => ['required', 'in:'.implode(',', self::ALLOWED_FILE_TYPES)],
            'due_at' => ['required', 'date'],
            'marks_out_of' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        if (
            $validated['submission_method'] !== $assignment->submission_method
            && ($assignment->submissions()->exists() || $assignment->marks()->exists())
        ) {
            throw ValidationException::withMessages([
                'submission_method' => 'The submission method cannot be changed after students have submitted work or marks have been recorded.',
            ]);
        }

        if ($assignment->marks()->max('value') > $validated['marks_out_of']) {
            throw ValidationException::withMessages([
                'marks_out_of' => 'The maximum cannot be lower than a mark already recorded for this assignment.',
            ]);
        }

        $studentIds = $assignment->marks()->pluck('student_id');
        $assignment->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'submission_method' => $validated['submission_method'],
            'allowed_file_types' => $validated['submission_method'] === 'system'
                ? array_values(array_unique($validated['allowed_file_types']))
                : null,
            'due_at' => $validated['due_at'],
            'marks_out_of' => $validated['marks_out_of'],
        ]);
        $assignment->marks()->update(['marks_out_of' => $assignment->marks_out_of]);

        if ($assignment->due_at->isFuture()) {
            $assignment->marks()
                ->where('status', 'missing')
                ->whereNotIn('student_id', $assignment->submissions()->select('student_id'))
                ->delete();
        }

        foreach (Student::whereIn('id', $studentIds)->get() as $student) {
            app(AssignmentReportCardRefresher::class)->refresh($assignment, $student);
        }

        return redirect()->route('teacher.assignments.show', $assignment)
            ->with('success', 'Assignment settings updated.');
    }

    public function show(LessonAssignment $assignment)
    {
        $assignment = $this->teacherAssignment($assignment);
        $students = $this->eligibleStudents($assignment)->get();
        $submissions = $assignment->submissions()->get()->keyBy('student_id');
        $marks = $assignment->marks()->get()->keyBy('student_id');

        if ($assignment->submission_method === 'system' && $assignment->due_at->isPast()) {
            foreach ($students as $student) {
                if (! $submissions->has($student->id) && ! $marks->has($student->id)) {
                    $mark = $this->saveAssignmentMark($assignment, $student, 0, 'missing');
                    $marks->put($student->id, $mark);
                    app(AssignmentReportCardRefresher::class)->refresh($assignment, $student);
                }
            }
        }

        return view('teacher.assignments.show', compact('assignment', 'students', 'submissions', 'marks'));
    }

    public function saveMarks(Request $request, LessonAssignment $assignment)
    {
        $assignment = $this->teacherAssignment($assignment);
        $validated = $request->validate([
            'marks' => ['required', 'array'],
            'marks.*' => ['nullable', 'integer', 'min:0', 'max:'.$assignment->marks_out_of],
        ]);

        $students = $this->eligibleStudents($assignment)->get()->keyBy('id');
        $submissions = $assignment->submissions()->get()->keyBy('student_id');
        $unknownStudentIds = array_diff(array_keys($validated['marks']), $students->keys()->all());
        if ($unknownStudentIds) {
            throw ValidationException::withMessages([
                'marks' => 'One or more students are not enrolled in this lesson at this branch.',
            ]);
        }

        DB::transaction(function () use ($validated, $assignment, $students, $submissions) {
            foreach ($validated['marks'] as $studentId => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                $student = $students->get($studentId);
                if (
                    $assignment->submission_method === 'system'
                    && $assignment->due_at->isPast()
                    && ! $submissions->has($student->id)
                ) {
                    $value = 0;
                }

                $this->saveAssignmentMark(
                    $assignment,
                    $student,
                    (int) $value,
                    $assignment->submission_method === 'system'
                        && $assignment->due_at->isPast()
                        && ! $submissions->has($student->id)
                        ? 'missing'
                        : 'recorded',
                );
            }
        });

        $gradedStudentIds = collect($validated['marks'])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->keys()
            ->map(fn ($id) => (int) $id);
        foreach ($students->whereIn('id', $gradedStudentIds) as $student) {
            app(AssignmentReportCardRefresher::class)->refresh($assignment, $student);
        }

        return redirect()->route('teacher.assignments.show', $assignment)
            ->with('success', 'Assignment marks saved.');
    }

    public function downloadSubmission(LessonAssignment $assignment, int $submissionId)
    {
        $assignment = $this->teacherAssignment($assignment);
        $submission = $assignment->submissions()->findOrFail($submissionId);

        abort_unless(Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->original_file_name);
    }

    private function teacherAssignment(LessonAssignment $assignment): LessonAssignment
    {
        abort_unless((int) $assignment->teacher_id === (int) auth('teacher')->id(), 403);

        return $assignment;
    }

    private function eligibleStudents(LessonAssignment $assignment)
    {
        $courseIds = LessonBranchCourse::where('lesson_id', $assignment->lesson_id)
            ->where('branch_id', $assignment->branch_id)
            ->pluck('course_id');

        return Student::with('course')
            ->where('branch_id', $assignment->branch_id)
            ->whereIn('course_id', $courseIds)
            ->orderBy('full_name');
    }

    private function saveAssignmentMark(
        LessonAssignment $assignment,
        Student $student,
        int $value,
        string $status = 'recorded',
    ): Mark {
        return Mark::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'course_id' => $student->course_id,
                'branch_id' => $assignment->branch_id,
                'lesson_id' => $assignment->lesson_id,
                'term_id' => $assignment->term_id,
                'assessment_type' => 'assignment',
                'value' => $value,
                'marks_out_of' => $assignment->marks_out_of,
                'test_date' => $assignment->due_at->toDateString(),
                'status' => $status,
            ],
        );
    }
}
