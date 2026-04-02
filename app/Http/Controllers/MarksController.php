<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;

class MarksController extends Controller
{
      public function index(Request $request)
    {
        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user();
            $lessons = Lesson::with('course')->where('teacher_id', $teacher->id)->get();
            $courses = $teacher->courses ?? Course::whereIn('id', $lessons->pluck('course_id'))->get();
            $branches = $teacher->branches; // <-- FIXED
            $terms = \App\Models\Term::all(); // Add missing terms variable
            
            // Get announcements for teacher layout
            $announcements = \App\Models\Announcement::latest()->take(5)->get();
            
            return view('teacher.marks.index', compact('lessons', 'courses', 'branches', 'terms', 'announcements'));
        }

        // Staff/Admin logic as before
        $courses = Course::all();
        $branches = Branch::all();
        $lessons=Lesson::with('course')
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->get();
      
        $marks = Mark::with(['student', 'course', 'branch', 'exam'])
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->student_search, function($q) use ($request) {
                $q->whereHas('student', function($q2) use ($request) {
                    $q2->where('name', 'like', '%'.$request->student_search.'%')
                       ->orWhere('id', $request->student_search);
                });
            })
            ->get();

        return view('marks.index', compact('courses', 'branches', 'marks', 'lessons'));
    }
    public function create(Request $request)
    {
        // Check if user is teacher and filter their assigned courses/lessons
        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user();
            
            // Get courses assigned to this teacher
            $teacherCourses = \App\Models\Course::whereHas('teachers', function($query) use ($teacher) {
                $query->where('teachers.id', $teacher->id);
            })->get();
            
            // Get lessons assigned to this teacher
            $teacherLessons = Lesson::where('teacher_id', $teacher->id)->get();
            
            // Get branches where teacher has courses
            $branches = Branch::whereIn('id', $teacherCourses->pluck('branch_id'))->get();
            
            // Get exams for teacher's courses
            $examsByCourse = Exam::whereIn('course_id', $teacherCourses->pluck('id'))->get()->groupBy('course_id');
            
            $courses = $teacherCourses;
        } else {
            // Staff/Admin get all courses
            $courses = Course::all();
            $branches = Branch::all();
            $examsByCourse = Exam::all()->groupBy('course_id');
            $teacherLessons = collect(); // Empty collection for non-teachers
        }
        
        $students = collect();

        // If course & branch selected, fetch students
        if ($request->course_id && $request->branch_id) {
            $students = Student::where('course_id', $request->course_id)
                               ->where('branch_id', $request->branch_id)
                               ->get();
        }

        return view('marks.create', compact('courses', 'branches', 'students', 'examsByCourse', 'teacherLessons'));
    }

   public function ajaxStudents(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'branch_id' => 'required|exists:branches,id',
        'type' => 'required|in:exam,test',
        'exam_id' => 'required|exists:exams,id', // or tests table if separated
    ]);

    $courseId = $request->course_id;
    $branchId = $request->branch_id;
    $type = $request->type;
    $examId = $request->exam_id;

    // Fetch students belonging to the branch and enrolled in the course
    $students = \App\Models\Student::where('branch_id', $branchId)
        ->whereHas('courses', function($q) use ($courseId) {
            $q->where('courses.id', $courseId);
        })
        ->get();

    if ($students->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No students found for this course and branch.'
        ]);
    }

    // Fetch existing marks for these students for the selected exam/test
    $marksQuery = \App\Models\Marks::where('exam_id', $examId)
        ->whereIn('student_id', $students->pluck('id'));

    // Optionally filter by type if you store exam/test separately
    // $marksQuery->where('type', $type);

    $marks = $marksQuery->get()->keyBy('student_id')->map(function($m) {
        return ['value' => $m->value ?? 0];
    });

    return response()->json([
        'success' => true,
        'students' => $students,
        'marks' => $marks
    ]);
}

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'course_id'       => 'required|exists:courses,id',
            'branch_id'       => 'required|exists:branches,id',
            'lesson_id'       => 'required|exists:lessons,id',
            'assessment_type' => 'required|in:test,exam',
            'exam_id'         => 'required_if:assessment_type,exam|exists:exams,id',
            'marks_out_of'    => 'required_if:assessment_type,test|numeric|min:1',
            'test_date'       => 'required_if:assessment_type,test|date',
            'exam_date'       => 'required_if:assessment_type,exam|date',
            'students'        => 'required|array',
            'students.*'      => 'exists:students,id',
            'marks'           => 'required|array',
            'marks.*.value'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->students as $studentId) {
                $value = $request->marks[$studentId]['value'] ?? null;
                if ($value !== null) {
                    $createData = [
                        'student_id' => $studentId,
                        'course_id'  => $request->course_id,
                        'branch_id'  => $request->branch_id,
                        'lesson_id'  => $request->lesson_id,
                        'assessment_type' => $request->assessment_type,
                        'value' => $value,
                        'status' => 'recorded'
                    ];

                    // Only add exam_id and exam_date if assessment_type is exam
                    if ($request->assessment_type === 'exam') {
                        $createData['exam_id'] = $request->exam_id;
                        $createData['exam_date'] = $request->exam_date;
                    }

                    // Only add marks_out_of and test_date if assessment_type is test
                    if ($request->assessment_type === 'test') {
                        $createData['marks_out_of'] = $request->marks_out_of;
                        $createData['test_date'] = $request->test_date;
                    }

                    // For tests, create new records instead of updating existing ones
                    if ($request->assessment_type === 'test') {
                        Mark::create($createData);
                    } else {
                        // For exams, use updateOrCreate to prevent duplicates
                        Mark::updateOrCreate(
                            [
                                'student_id' => $studentId,
                                'course_id'  => $request->course_id,
                                'branch_id'  => $request->branch_id,
                                'lesson_id'  => $request->lesson_id,
                                'assessment_type' => $request->assessment_type,
                                'exam_id'    => $request->exam_id,
                            ],
                            $createData
                        );
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Marks saved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save marks: ' . $e->getMessage(),
                'errors' => ['server' => [$e->getMessage()]]
            ], 500);
        }
    }
}
