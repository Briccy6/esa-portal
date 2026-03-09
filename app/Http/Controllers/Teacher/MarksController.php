<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Mark;

class MarksController extends Controller
{

public function index()
{
    $teacher = auth('teacher')->user();
    
    // Get only lessons assigned to this teacher
    $lessons = Lesson::where('teacher_id', $teacher->id)->get();
    
    // Get only courses assigned to this teacher
    $courses = \App\Models\Course::whereHas('teachersManyToMany', function($query) use ($teacher) {
        $query->where('teachers.id', $teacher->id);
    })->with('branches')->get();
    
    // Get branches through the course relationships
    $branchIds = $courses->pluck('branches')->flatten()->pluck('id')->unique();
    $branches = \App\Models\Branch::whereIn('id', $branchIds)->get();
    
    // Load courses relationship for branches to use in view
    $branches = $branches->load('courses');
    
    return view('teacher.marks.index', compact('lessons', 'courses', 'branches'));
}

    public function create(Lesson $lesson)
    {
        // You can fetch students for this lesson here if needed
        return view('teacher.marks.create', compact('lesson'));
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
            $teacher = auth('teacher')->user();
            
            foreach ($request->students as $studentId) {
                $value = $request->marks[$studentId]['value'] ?? null;
                if ($value !== null) {
                    // Allow multiple test marks - don't check for duplicates
                    // Each test recording should be a separate entry with its own date

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

                    Mark::create($createData);
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

    public function viewMarks()
    {
        $teacher = auth('teacher')->user();
        
        // Get all marks recorded by this teacher
        $marks = Mark::whereHas('lesson', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with(['student', 'course', 'branch', 'lesson'])
        ->orderBy('lesson_id')
        ->orderBy('test_date')
        ->orderBy('student_id')
        ->get();

        // Group marks by lesson, then by test date
        $groupedMarks = [];
        $students = [];
        
        foreach ($marks as $mark) {
            $lessonId = $mark->lesson_id;
            $lessonTitle = $mark->lesson->title;
            $testDate = $mark->test_date ?: 'no-date'; // Handle empty dates
            $studentId = $mark->student_id;
            
            // Collect unique students
            if (!isset($students[$studentId])) {
                $students[$studentId] = [
                    'name' => $mark->student->full_name,
                    'course' => $mark->course->course_name,
                    'branch' => $mark->branch->name,
                    'reg_number' => $mark->student->reg_number
                ];
            }
            
            // Group by lesson
            if (!isset($groupedMarks[$lessonId])) {
                $groupedMarks[$lessonId] = [
                    'title' => $lessonTitle,
                    'dates' => [],
                    'students' => []
                ];
            }
            
            // Collect unique dates for this lesson
            if (!isset($groupedMarks[$lessonId]['dates'][$testDate])) {
                $groupedMarks[$lessonId]['dates'][$testDate] = [];
            }
            
            // Store marks by student for this lesson and date
            if (!isset($groupedMarks[$lessonId]['students'][$studentId])) {
                $groupedMarks[$lessonId]['students'][$studentId] = [];
            }
            
            $groupedMarks[$lessonId]['students'][$studentId][$testDate] = $mark;
        }

        // Sort dates within each lesson (put no-date at the end)
        foreach ($groupedMarks as $lessonId => &$lessonData) {
            // Separate no-date from actual dates
            $noDate = [];
            $actualDates = [];
            
            foreach ($lessonData['dates'] as $date => $dateMarks) {
                if ($date === 'no-date') {
                    $noDate[$date] = $dateMarks;
                } else {
                    $actualDates[$date] = $dateMarks;
                }
            }
            
            // Sort actual dates
            ksort($actualDates);
            
            // Combine: actual dates first, then no-date
            $lessonData['dates'] = $actualDates + $noDate;
        }
        
        // Sort students by name
        uasort($students, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return view('teacher.marks.view', compact('groupedMarks', 'students'));
    }

    public function editMarks($lessonId, $testDate)
    {
        $teacher = auth('teacher')->user();
        
        // Verify this lesson belongs to the teacher
        $lesson = Lesson::where('id', $lessonId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();
        
        // Get marks for this lesson and test date
        $marks = Mark::where('lesson_id', $lessonId)
            ->where('test_date', $testDate)
            ->with(['student', 'course', 'branch'])
            ->get();

        return view('teacher.marks.edit', compact('lesson', 'marks', 'testDate'));
    }

    public function updateMarks(Request $request, $lessonId, $testDate)
    {
        $validator = \Validator::make($request->all(), [
            'marks' => 'required|array',
            'marks.*.value' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $teacher = auth('teacher')->user();
            
            // Verify this lesson belongs to the teacher
            $lesson = Lesson::where('id', $lessonId)
                ->where('teacher_id', $teacher->id)
                ->firstOrFail();
            
            foreach ($request->marks as $markId => $markData) {
                $mark = Mark::where('id', $markId)
                    ->where('lesson_id', $lessonId)
                    ->where('test_date', $testDate)
                    ->first();
                
                if ($mark) {
                    $mark->update([
                        'value' => $markData['value'],
                        'status' => 'updated'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Marks updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update marks: ' . $e->getMessage(),
                'errors' => ['server' => [$e->getMessage()]]
            ], 500);
        }
    }
}
