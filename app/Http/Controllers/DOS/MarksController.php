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
                    $q2->where('full_name', 'like', '%'.$request->student_search.'%')
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
        $mark = Mark::with(['student.course', 'student.branch', 'student.academicYear', 'course', 'branch', 'exam', 'lesson', 'grade'])->findOrFail($id);
        return view('staff.marks.show', compact('mark'));
    }

    public function studentReport(Request $request)
    {
        try {
            // Handle both GET and POST requests
            $studentId = $request->input('student_id');
            
            if (!$studentId) {
                return back()->with('error', 'Student ID is required');
            }

            $request->merge(['student_id' => $studentId]);
            
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_id' => 'nullable|exists:courses,id',
                'term' => 'nullable|string',
            ]);

            \Log::info('Student Report Request:', [
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'term' => $request->term,
                'method' => $request->method(),
            ]);

            $student = Student::with(['course', 'branch', 'academicYear'])->findOrFail($request->student_id);
            
            \Log::info('Student found:', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'course_id' => $student->course_id,
                'branch_id' => $student->branch_id,
            ]);
            
            $query = Mark::with(['course', 'exam', 'lesson', 'grade'])
                ->where('student_id', $student->id)
                ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
                ->when($request->term, fn($q) => $q->where('term', $request->term))
                ->orderBy('created_at', 'desc');

            $marks = $query->get();
            
            \Log::info('Marks found:', [
                'marks_count' => $marks->count(),
            ]);
            
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
            
        } catch (\Exception $e) {
            \Log::error('Student Report Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'Failed to generate student report: ' . $e->getMessage());
        }
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
                          ->orderBy('full_name')
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
        // Handle GET request - show the form
        if ($request->isMethod('get')) {
            $students = Student::with(['course', 'branch'])->get();
            return view('staff.marks.tvet-report-form', compact('students'));
        }
        
        // Handle POST request - generate the report card
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'term' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $student = Student::with(['course', 'branch'])->findOrFail($request->student_id);
        
        // Create a mock report card object for TVET template
        $reportCard = new \stdClass();
        $reportCard->student = $student;
        $reportCard->course = $student->course;
        $reportCard->academicYear = (object)['year' => $request->academic_year ?? date('Y') . '-' . (date('Y') + 1)];
        $reportCard->term = (object)['term' => $request->term ?? 'Current Term'];
        
        // Get marks and group by lesson
        $marks = Mark::with(['course', 'exam', 'lesson', 'grade'])
            ->where('student_id', $student->id)
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
            ->orderBy('created_at', 'asc')
            ->get();

        // Group marks by lesson and create subjects structure
        $subjects = collect();
        $lessons = $marks->groupBy('lesson_id');
        
        foreach ($lessons as $lessonId => $lessonMarks) {
            $lesson = $lessonMarks->first()->lesson;
            if ($lesson) {
                $subject = new \stdClass();
                $subject->subject_name = $lesson->title;
                $subject->subject_code = $lesson->code ?? strtoupper(substr($lesson->title, 0, 3));
                $subject->credits = 3;
                
                // Calculate test average
                $testMarks = $lessonMarks->where('assessment_type', 'test');
                $subject->test_average = $testMarks->isNotEmpty() ? $testMarks->avg('value') : 0;
                
                // Get exam mark
                $examMark = $lessonMarks->where('assessment_type', 'exam')->first();
                $subject->exam_mark = $examMark ? $examMark->value : 0;
                
                // Calculate total mark
                $subject->total_mark = ($subject->test_average * 0.4) + ($subject->exam_mark * 0.6);
                
                // Determine grade
                if ($subject->total_mark >= 80) {
                    $subject->grade = 'A';
                } elseif ($subject->total_mark >= 70) {
                    $subject->grade = 'B';
                } elseif ($subject->total_mark >= 60) {
                    $subject->grade = 'C';
                } else {
                    $subject->grade = 'NYC';
                }
                
                $subjects->push($subject);
            }
        }
        
        $reportCard->subjects = $subjects;
        
        // Calculate totals
        $reportCard->total_marks = $subjects->sum('total_mark');
        $reportCard->average_marks = $subjects->isNotEmpty() ? $subjects->avg('total_mark') : 0;
        $reportCard->class_position = 1; // Mock position
        $reportCard->total_students = 1; // Mock total students
        
        // Get school information
        $schoolInfo = \App\Models\SchoolInformation::getActive();

        return view('staff.marks.tvet-report', compact('reportCard', 'schoolInfo'));
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
