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
    
    // Get branches where this teacher has lesson assignments (from lesson_branch_course pivot)
    $branches = \App\Models\Branch::whereHas('lessonBranchCourses', function($query) use ($teacher) {
        $query->where('teacher_id', $teacher->id);
    })->with(['lessonBranchCourses' => function($query) use ($teacher) {
        $query->where('teacher_id', $teacher->id);
    }])->get();
    
    // Get all terms for term selection
    $terms = \App\Models\Term::orderBy('name')->get();
    
    return view('teacher.marks.index', compact('branches', 'terms'));
}

    public function create(Lesson $lesson)
    {
        // You can fetch students for this lesson here if needed
        return view('teacher.marks.create', compact('lesson'));
    }

    public function store(Request $request)
    {
        // DEBUG: Log all incoming request data
        \Log::info('MarksController::store - Incoming Request Data', [
            'teacher_id' => auth('teacher')->user()?->id,
            'branch_id' => $request->branch_id,
            'course_id' => $request->course_id,
            'lesson_id' => $request->lesson_id,
            'term_id' => $request->term_id,
            'assessment_type' => $request->assessment_type,
            'students_count' => count($request->students ?? []),
            'marks_count' => count($request->marks ?? []),
            'all_request_data' => $request->all()
        ]);

        $validator = \Validator::make($request->all(), [
            'course_id'       => 'required|exists:courses,id',
            'branch_id'       => 'required|exists:branches,id',
            'lesson_id'       => 'required|exists:lessons,id',
            'term_id'         => 'required|exists:terms,id',
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

        // Additional validation to ensure term exists and is valid
        if ($request->term_id) {
            $termExists = \App\Models\Term::where('id', $request->term_id)->exists();
            if (!$termExists) {
                \Log::error('Term not found', ['term_id' => $request->term_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Selected term does not exist',
                    'debug' => "Term ID {$request->term_id} not found in database",
                    'errors' => ['term_id' => ['The selected term is invalid']]
                ], 422);
            }
        }

        // Ensure term_id is not null or zero
        if (!$request->term_id || $request->term_id == 0) {
            \Log::error('Term ID missing or zero', ['term_id' => $request->term_id]);
            return response()->json([
                'success' => false,
                'message' => 'Term selection is required',
                'debug' => "Term ID is empty or zero: " . ($request->term_id ?? 'null'),
                'errors' => ['term_id' => ['Please select a valid term for recording marks']]
            ], 422);
        }

        if ($validator->fails()) {
            \Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'debug' => 'Validation errors detected',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $teacher = auth('teacher')->user();
            $marksCreated = 0;
            $marksFailed = [];
            
            \Log::info('Starting marks creation loop', [
                'teacher_id' => $teacher->id,
                'students_to_process' => $request->students
            ]);
            
            foreach ($request->students as $studentId) {
                $value = $request->marks[$studentId]['value'] ?? null;
                
                // DEBUG: Log each student processing
                \Log::debug('Processing student mark', [
                    'student_id' => $studentId,
                    'mark_value' => $value,
                    'marks_array' => $request->marks[$studentId] ?? null
                ]);
                
                // Ensure value is provided
                if ($value === null || $value === '' || !is_numeric($value)) {
                    $errorMsg = "Student ID {$studentId} - No mark value provided (value: " . var_export($value, true) . ")";
                    $marksFailed[] = $errorMsg;
                    \Log::warning('Mark value missing or invalid', ['student_id' => $studentId, 'value' => $value]);
                    continue;
                }
                
                // Validate term_id before creating
                if (!$request->term_id || $request->term_id == 0) {
                    $errorMsg = "Student ID {$studentId} - Invalid term selected (term_id: {$request->term_id})";
                    $marksFailed[] = $errorMsg;
                    \Log::warning('Invalid term ID', ['student_id' => $studentId, 'term_id' => $request->term_id]);
                    continue;
                }
                
                // Allow multiple test marks - don't check for duplicates
                // Each test recording should be a separate entry with its own date
                $createData = [
                    'student_id' => $studentId,
                    'course_id'  => $request->course_id,
                    'branch_id'  => $request->branch_id,
                    'lesson_id'  => $request->lesson_id,
                    'term_id'    => $request->term_id, // Ensure term_id is always set
                    'assessment_type' => $request->assessment_type,
                    'value' => (float)$value,
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

                \Log::debug('Creating mark with data', ['createData' => $createData]);

                try {
                    $mark = Mark::create($createData);
                    $marksCreated++;
                    \Log::info('Mark created successfully', ['mark_id' => $mark->id, 'student_id' => $studentId]);
                } catch (\Exception $e) {
                    $errorMsg = "Student ID {$studentId} - " . $e->getMessage();
                    $marksFailed[] = $errorMsg;
                    \Log::error('Error creating mark', [
                        'student_id' => $studentId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'createData' => $createData
                    ]);
                }
            }
            
            \Log::info('Marks creation completed', [
                'marksCreated' => $marksCreated,
                'marksFailed' => count($marksFailed),
                'failedDetails' => $marksFailed
            ]);
            
            // Refresh report cards with new marks
            if ($marksCreated > 0) {
                $this->refreshReportCardsForMarks(
                    $request->course_id,
                    $request->term_id,
                    $request->students
                );
            }
            
            // Return success if at least one mark was created
            if ($marksCreated > 0) {
                $message = "Marks saved successfully for {$marksCreated} student(s)! Report cards have been updated.";
                if (!empty($marksFailed)) {
                    $message .= " " . count($marksFailed) . " student(s) had issues.";
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'marksCreated' => $marksCreated,
                    'marksFailed' => count($marksFailed)
                ]);
            } else {
                \Log::error('No marks were created', [
                    'students_count' => count($request->students),
                    'failed_count' => count($marksFailed),
                    'failures' => $marksFailed
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No marks were saved. ' . implode(", ", $marksFailed),
                    'debug' => 'Failed to save any marks - check server logs for details',
                    'errors' => ['marks' => $marksFailed]
                ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('Unexpected error in marks store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save marks: ' . $e->getMessage(),
                'debug' => 'Check server logs for full error trace',
                'errors' => ['server' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function viewMarks()
    {
        $teacher = auth('teacher')->user();
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        
        // Get all marks recorded by this teacher with term relationship
        $marks = Mark::whereHas('lesson', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with(['student', 'course', 'branch', 'lesson', 'term'])
        ->orderBy('term_id')
        ->orderBy('lesson_id')
        ->orderBy('test_date')
        ->orderBy('student_id')
        ->get();

        // Group marks by term, then by lesson, then by test date
        $groupedMarks = [];
        $students = [];
        
        foreach ($marks as $mark) {
            $termId = $mark->term_id ?: 'no-term';
            
            // Handle term_id = 0 as no-term as well
            if ($mark->term_id == 0 || $mark->term_id === null) {
                $termId = 'no-term';
                $termName = 'No Term Assigned';
            } else {
                $termName = $mark->term ? $mark->term->name : 'Unknown Term';
            }
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
            
            // Group by term first
            if (!isset($groupedMarks[$termId])) {
                $groupedMarks[$termId] = [
                    'name' => $termName,
                    'lessons' => []
                ];
            }
            
            // Group by lesson within term
            if (!isset($groupedMarks[$termId]['lessons'][$lessonId])) {
                $groupedMarks[$termId]['lessons'][$lessonId] = [
                    'title' => $lessonTitle,
                    'dates' => [],
                    'students' => []
                ];
            }
            
            // Collect unique dates for this lesson
            if (!isset($groupedMarks[$termId]['lessons'][$lessonId]['dates'][$testDate])) {
                $groupedMarks[$termId]['lessons'][$lessonId]['dates'][$testDate] = [];
            }
            
            // Store mark for this student on this date
            $groupedMarks[$termId]['lessons'][$lessonId]['dates'][$testDate][$studentId] = $mark;
            $groupedMarks[$termId]['lessons'][$lessonId]['students'][$studentId][$testDate] = $mark;
        }

        // Sort dates within each lesson (put no-date at the end)
        foreach ($groupedMarks as $termId => &$termData) {
            foreach ($termData['lessons'] as $lessonId => &$lessonData) {
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

    /**
     * Refresh report cards for students after marks are recorded
     * Removes old aggregated mark data and regenerates it from the marks table
     */
    private function refreshReportCardsForMarks($courseId, $termId, $studentIds)
    {
        try {
            $reportCardController = new \App\Http\Controllers\ReportCardController();
            
            // Get the academic year for this term
            $term = \App\Models\Term::find($termId);
            if (!$term) {
                \Log::warning('Term not found for report card refresh', ['term_id' => $termId]);
                return;
            }
            
            // For each student, find and refresh their report cards
            foreach ($studentIds as $studentId) {
                // Find all report cards for this student with the same course, year, and term
                $reportCards = \App\Models\ReportCard::where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->where('academic_year_id', $term->academic_year_id)
                    ->where('term_id', $termId)
                    ->get();
                
                foreach ($reportCards as $reportCard) {
                    // Delete old subjects and test marks
                    foreach ($reportCard->subjects as $subject) {
                        $subject->testMarks()->delete();
                        $subject->delete();
                    }
                    
                    // Regenerate the report card data from marks table
                    $reportCardController->generateReportCardData($reportCard);
                    
                    \Log::info('Report card refreshed with new marks', [
                        'report_card_id' => $reportCard->id,
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'term_id' => $termId
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error refreshing report cards', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'course_id' => $courseId,
                'term_id' => $termId
            ]);
        }
    }
}
