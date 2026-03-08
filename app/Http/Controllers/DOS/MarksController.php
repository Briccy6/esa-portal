<?php

namespace App\Http\Controllers\DOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarksController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::all();
        $branches = Branch::all();
        $exams = Exam::all();
        
        $query = Mark::with(['student', 'course', 'branch', 'exam', 'lesson', 'grade'])
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->exam_id, fn($q) => $q->where('exam_id', $request->exam_id))
            ->when($request->assessment_type, fn($q) => $q->where('assessment_type', $request->assessment_type))
            ->when($request->student_search, function($q) use ($request) {
                $q->whereHas('student', function($q2) use ($request) {
                    $q2->where('name', 'like', '%'.$request->student_search.'%')
                       ->orWhere('id', $request->student_search);
                });
            })
            ->orderBy('created_at', 'desc');

        $marks = $query->paginate(20);
        
        // Statistics
        $stats = [
            'total_marks' => Mark::count(),
            'exam_marks' => Mark::where('assessment_type', 'exam')->count(),
            'test_marks' => Mark::where('assessment_type', 'test')->count(),
            'average_score' => Mark::whereNotNull('value')->avg('value'),
            'highest_score' => Mark::max('value'),
            'lowest_score' => Mark::min('value'),
        ];

        return view('staff.marks.index', compact('courses', 'branches', 'exams', 'marks', 'stats'));
    }

    public function show($id)
    {
        $mark = Mark::with(['student', 'course', 'branch', 'exam', 'lesson', 'grade'])->findOrFail($id);
        return view('staff.marks.show', compact('mark'));
    }

    public function studentReport(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'nullable|exists:courses,id',
            'term' => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        
        $query = Mark::with(['course', 'exam', 'lesson', 'grade'])
            ->where('student_id', $student->id)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->orderBy('created_at', 'desc');

        $marks = $query->get();
        
        // Calculate statistics
        $stats = [
            'total_assessments' => $marks->count(),
            'average_score' => $marks->whereNotNull('value')->avg('value'),
            'highest_score' => $marks->max('value'),
            'lowest_score' => $marks->min('value'),
            'exam_count' => $marks->where('assessment_type', 'exam')->count(),
            'test_count' => $marks->where('assessment_type', 'test')->count(),
        ];

        // Group by course for better organization
        $marksByCourse = $marks->groupBy('course_id');

        return view('staff.marks.student-report', compact('student', 'marks', 'marksByCourse', 'stats'));
    }

    public function classReport(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'exam_id' => 'nullable|exists:exams,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $branch = Branch::findOrFail($request->branch_id);
        
        $query = Mark::with(['student', 'grade'])
            ->where('course_id', $course->id)
            ->where('branch_id', $branch->id)
            ->when($request->exam_id, fn($q) => $q->where('exam_id', $request->exam_id))
            ->orderBy('value', 'desc');

        $marks = $query->get();
        
        // Get all students in this course and branch
        $students = Student::where('course_id', $course->id)
                          ->where('branch_id', $branch->id)
                          ->orderBy('name')
                          ->get();

        // Class statistics
        $stats = [
            'total_students' => $students->count(),
            'students_with_marks' => $marks->pluck('student_id')->unique()->count(),
            'average_score' => $marks->whereNotNull('value')->avg('value'),
            'highest_score' => $marks->max('value'),
            'lowest_score' => $marks->min('value'),
            'pass_rate' => $this->calculatePassRate($marks),
        ];

        // Grade distribution
        $gradeDistribution = $marks->groupBy('grade.name')->map(function($group) {
            return $group->count();
        });

        return view('staff.marks.class-report', compact('course', 'branch', 'marks', 'students', 'stats', 'gradeDistribution'));
    }

    public function reportCard(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'term' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $student = Student::with(['course', 'branch'])->findOrFail($request->student_id);
        
        $query = Mark::with(['course', 'exam', 'lesson', 'grade'])
            ->where('student_id', $student->id)
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
            ->orderBy('created_at', 'asc');

        $marks = $query->get();
        
        // Group marks by assessment type and course
        $examMarks = $marks->where('assessment_type', 'exam')->groupBy('course_id');
        $testMarks = $marks->where('assessment_type', 'test')->groupBy('course_id');
        
        // Calculate overall performance
        $overallStats = [
            'total_assessments' => $marks->count(),
            'average_score' => $marks->whereNotNull('value')->avg('value'),
            'total_exams' => $marks->where('assessment_type', 'exam')->count(),
            'total_tests' => $marks->where('assessment_type', 'test')->count(),
            'best_subject' => $this->getBestSubject($marks),
            'needs_improvement' => $this->getNeedsImprovement($marks),
        ];

        return view('staff.marks.report-card', compact('student', 'marks', 'examMarks', 'testMarks', 'overallStats'));
    }

    private function calculatePassRate($marks)
    {
        if ($marks->isEmpty()) return 0;
        
        $passingMarks = $marks->filter(function($mark) {
            return $mark->value >= ($mark->marks_out_of * 0.5); // 50% pass rate
        });
        
        return ($passingMarks->count() / $marks->count()) * 100;
    }

    private function getBestSubject($marks)
    {
        $courseAverages = $marks->groupBy('course_id')->map(function($courseMarks) {
            return $courseMarks->whereNotNull('value')->avg('value');
        });
        
        return $courseAverages->max();
    }

    private function getNeedsImprovement($marks)
    {
        $courseAverages = $marks->groupBy('course_id')->map(function($courseMarks) {
            return $courseMarks->whereNotNull('value')->avg('value');
        });
        
        return $courseAverages->min();
    }
}
