<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\LessonAssignment;
use App\Models\LessonBranchCourse;
use App\Models\Mark;
use App\Services\AssignmentReportCardRefresher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function index()
    {
        $student = auth('student')->user();
        $assignments = LessonAssignment::with(['lesson', 'term', 'teacher'])
            ->where('branch_id', $student->branch_id)
            ->whereHas('lesson.branchCourses', function ($query) use ($student) {
                $query->where('branch_id', $student->branch_id)
                    ->where('course_id', $student->course_id);
            })
            ->orderBy('due_at')
            ->get();

        foreach ($assignments as $assignment) {
            $submission = $assignment->submissions()->where('student_id', $student->id)->first();
            $assignment->setRelation('studentSubmission', $submission);

            if (
                $assignment->submission_method === 'system'
                && $assignment->due_at->isPast()
                && ! $submission
            ) {
                $mark = Mark::firstOrCreate(
                    ['assignment_id' => $assignment->id, 'student_id' => $student->id],
                    [
                        'course_id' => $student->course_id,
                        'branch_id' => $assignment->branch_id,
                        'lesson_id' => $assignment->lesson_id,
                        'term_id' => $assignment->term_id,
                        'assessment_type' => 'assignment',
                        'value' => 0,
                        'marks_out_of' => $assignment->marks_out_of,
                        'test_date' => $assignment->due_at->toDateString(),
                        'status' => 'missing',
                    ],
                );
                if ($mark->wasRecentlyCreated) {
                    app(AssignmentReportCardRefresher::class)->refresh($assignment, $student);
                }
            }

            $assignment->setRelation(
                'studentMark',
                $assignment->marks()->where('student_id', $student->id)->first(),
            );
        }

        return view('students.assignments.index', compact('assignments'));
    }

    public function submit(Request $request, LessonAssignment $assignment)
    {
        $student = auth('student')->user();
        $this->ensureStudentCanAccess($assignment, $student->branch_id, $student->course_id);

        if ($assignment->submission_method !== 'system') {
            abort(404);
        }
        if ($assignment->due_at->isPast()) {
            throw ValidationException::withMessages([
                'file' => 'The submission deadline has passed.',
            ]);
        }

        $types = $assignment->allowed_file_types ?? [];
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:'.$this->acceptedExtensions($types)],
        ]);

        $file = $validated['file'];
        $newPath = $file->store('assignment-submissions', 'local');
        $previousSubmission = $assignment->submissions()->where('student_id', $student->id)->first();

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'lesson_assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'file_path' => $newPath,
                'original_file_name' => $file->getClientOriginalName(),
                'submitted_at' => now(),
            ],
        );
        $removedMark = Mark::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->delete();
        if ($removedMark > 0) {
            app(AssignmentReportCardRefresher::class)->refresh($assignment, $student);
        }

        if ($previousSubmission && $previousSubmission->file_path !== $submission->file_path) {
            Storage::disk('local')->delete($previousSubmission->file_path);
        }

        return redirect()->route('student.assignments.index')->with('success', 'Your assignment was submitted.');
    }

    public function downloadSubmission(LessonAssignment $assignment)
    {
        $student = auth('student')->user();
        $this->ensureStudentCanAccess($assignment, $student->branch_id, $student->course_id);
        $submission = $assignment->submissions()->where('student_id', $student->id)->firstOrFail();

        abort_unless(Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->original_file_name);
    }

    private function ensureStudentCanAccess(LessonAssignment $assignment, ?int $branchId, ?int $courseId): void
    {
        abort_unless(
            $branchId !== null
            && $courseId !== null
            && (int) $assignment->branch_id === (int) $branchId
            && LessonBranchCourse::where('lesson_id', $assignment->lesson_id)
                ->where('branch_id', $branchId)
                ->where('course_id', $courseId)
                ->exists(),
            404,
        );
    }

    private function acceptedExtensions(array $types): string
    {
        $aliases = [
            'jpg' => ['jpg', 'jpeg'],
            'jpeg' => ['jpg', 'jpeg'],
            'tif' => ['tif', 'tiff'],
            'tiff' => ['tif', 'tiff'],
        ];

        return collect($types)
            ->flatMap(fn (string $type) => $aliases[$type] ?? [$type])
            ->unique()
            ->implode(',');
    }
}
