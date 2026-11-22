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

                // Convert to float to allow decimal marks
                $value = (float) $value;

                // Validate mark is not negative
                if ($value < 0) {
                    $errorMsg = "Student ID {$studentId} - Mark cannot be negative (value: {$value})";
                    $marksFailed[] = $errorMsg;
                    \Log::warning('Mark value is negative', ['student_id' => $studentId, 'value' => $value]);
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
                    'status' => 'recorded',
                ];

                // Only add exam_id and exam_date if assessment_type is exam
                // For exams, always set marks_out_of to 100 (standard exam marks)
                if ($request->assessment_type === 'exam') {
                    $createData['exam_id'] = $request->exam_id;
                    $createData['exam_date'] = $request->exam_date;
                    $createData['marks_out_of'] = 100; // Exams are always out of 100
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
        
        // Get all marks for lessons/branches/courses assigned to this teacher
        // via lesson_branch_course table.
        $marks = Mark::query()
            ->whereExists(function ($q) use ($teacher) {
                $q->selectRaw(1)
                  ->from('lesson_branch_course as lbc')
                  ->whereColumn('lbc.lesson_id', 'marks.lesson_id')
                  ->whereColumn('lbc.branch_id', 'marks.branch_id')
                  ->whereColumn('lbc.course_id', 'marks.course_id')
                  ->where('lbc.teacher_id', $teacher->id);
            })
            ->with(['student', 'course', 'branch', 'lesson', 'term', 'assignment'])
            ->orderBy('term_id')
            ->orderBy('course_id')
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
            $testDate = $mark->assignment_id
                ? 'assignment-' . $mark->assignment_id
                : ($mark->test_date ?: 'no-date');
            $studentId = $mark->student_id;
            
            // Collect unique students
            if (!isset($students[$studentId])) {
                $students[$studentId] = [
                    'name' => $mark->student->full_name,
                    'course' => $mark->course->abbreviation ?? $mark->course->course_code ?? $mark->course->course_name,
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
                    'date_labels' => [],
                    'students' => []
                ];
            }
            
            // Collect unique dates for this lesson
            if (!isset($groupedMarks[$termId]['lessons'][$lessonId]['dates'][$testDate])) {
                $groupedMarks[$termId]['lessons'][$lessonId]['dates'][$testDate] = [];
                $groupedMarks[$termId]['lessons'][$lessonId]['date_labels'][$testDate] = $mark->assignment_id
                    ? $mark->assignment->title
                    : null;
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
        
        // Sort students by name (A-Z by default)
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
     * Download CSV template for marks recording
     */
    public function downloadTemplate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
            'term_id' => 'required|exists:terms,id',
            'assessment_type' => 'required|in:test,exam',
            'marks_out_of' => 'required_if:assessment_type,test|numeric|min:1',
            'test_date' => 'required_if:assessment_type,test|date',
            'exam_id' => 'required_if:assessment_type,exam|exists:exams,id',
            'exam_date' => 'required_if:assessment_type,exam|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $teacher = auth('teacher')->user();

        // Verify teacher is assigned to this course/branch/lesson
        $assigned = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('branch_id', $request->branch_id)
            ->where('course_id', $request->course_id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();

        if (!$assigned) {
            return back()->with('error', 'You are not assigned to this lesson in this course/branch.');
        }

        // Get students for this course and branch
        $students = \App\Models\Student::where('course_id', $request->course_id)
            ->where('branch_id', $request->branch_id)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'reg_number']);

        // Get lesson and course info for filename
        $lesson = \App\Models\Lesson::find($request->lesson_id);
        $course = \App\Models\Course::find($request->course_id);
        $branch = \App\Models\Branch::find($request->branch_id);
        $term = \App\Models\Term::find($request->term_id);

        $assessmentType = $request->assessment_type;
        $marksOutOf = $assessmentType === 'test' ? $request->marks_out_of : 100;
        $date = $assessmentType === 'test' ? $request->test_date : $request->exam_date;
        $examId = $assessmentType === 'exam' ? $request->exam_id : null;

        $filename = 'marks_template_' . \Illuminate\Support\Str::slug($lesson->title)
            . '_' . \Illuminate\Support\Str::slug($course->course_name)
            . '_' . \Illuminate\Support\Str::slug($branch->name)
            . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students, $assessmentType, $marksOutOf, $date, $lesson, $course, $branch, $term) {
            $file = fopen('php://output', 'w');

            // Write header row with metadata
            fputcsv($file, [
                '# METADATA (DO NOT EDIT)',
                'Assessment Type:' . $assessmentType,
                'Marks Out Of:' . $marksOutOf,
                'Date:' . $date,
                'Lesson ID:' . $lesson->id,
                'Course ID:' . $course->id,
                'Branch ID:' . $branch->id,
                'Term ID:' . $term->id,
                'Exam ID:' . ($examId ?? ''),
            ]);

            // Empty row as separator
            fputcsv($file, []);

            // Write column headers
            fputcsv($file, ['Student ID', 'Registration Number', 'Student Name', 'Mark']);

            // Write student rows
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id,
                    $student->reg_number,
                    $student->full_name,
                    '', // Empty mark field for teacher to fill
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Upload marks from CSV file (preview only - doesn't save)
     */
    public function uploadMarks(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
            'term_id' => 'required|exists:terms,id',
            'assessment_type' => 'required|in:test,exam',
            'marks_out_of' => 'required_if:assessment_type,test|numeric|min:1',
            'test_date' => 'required_if:assessment_type,test|date',
            'exam_id' => 'required_if:assessment_type,exam|exists:exams,id',
            'exam_date' => 'required_if:assessment_type,exam|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = auth('teacher')->user();

        // Verify teacher is assigned to this course/branch/lesson
        $assigned = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('branch_id', $request->branch_id)
            ->where('course_id', $request->course_id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();

        if (!$assigned) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this lesson in this course/branch.'
            ], 403);
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $rows = array_map('str_getcsv', file($path));

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty'
                ], 400);
            }

            // Skip metadata rows (starting with #) and find the header row
            $headerRow = null;
            $dataStartIndex = 0;

            foreach ($rows as $index => $row) {
                if (empty($row)) continue;
                if (isset($row[0]) && str_starts_with($row[0], '#')) continue;
                if (isset($row[0]) && str_starts_with($row[0], 'Student ID')) {
                    $headerRow = $row;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (!$headerRow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not find header row in CSV. Expected header: Student ID, Registration Number, Student Name, Mark'
                ], 400);
            }

            // Parse column indices
            $studentIdIndex = array_search('Student ID', $headerRow);
            $regNumberIndex = array_search('Registration Number', $headerRow);
            $studentNameIndex = array_search('Student Name', $headerRow);
            $markIndex = array_search('Mark', $headerRow);

            if ($studentIdIndex === false || $markIndex === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV header is missing required columns: Student ID, Mark'
                ], 400);
            }

            $marksCreated = 0;
            $marksFailed = [];
            $marksUpdated = 0;

            // Process data rows
            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];

                if (empty($row) || count($row) < max($studentIdIndex, $markIndex) + 1) {
                    continue; // Skip empty or malformed rows
                }

                $studentId = trim($row[$studentIdIndex] ?? '');
                $markValue = trim($row[$markIndex] ?? '');

                // Validate student ID
                if (!is_numeric($studentId) || $studentId <= 0) {
                    $marksFailed[] = "Row " . ($i + 1) . ": Invalid Student ID - {$studentId}";
                    continue;
                }

                // Validate mark value
                if ($markValue === '' || !is_numeric($markValue)) {
                    $marksFailed[] = "Row " . ($i + 1) . ": Invalid or missing mark for Student ID {$studentId}";
                    continue;
                }

                $markValue = (float) $markValue;

                // Validate mark is not negative
                if ($markValue < 0) {
                    $marksFailed[] = "Row " . ($i + 1) . ": Mark cannot be negative for Student ID {$studentId}";
                    continue;
                }

                // Check if student exists and is enrolled in this course/branch
                $student = \App\Models\Student::where('id', $studentId)
                    ->where('course_id', $request->course_id)
                    ->where('branch_id', $request->branch_id)
                    ->first();

                if (!$student) {
                    $marksFailed[] = "Row " . ($i + 1) . ": Student ID {$studentId} not found or not enrolled in this course/branch";
                    continue;
                }

                // Prepare mark data
                $createData = [
                    'student_id' => $studentId,
                    'course_id' => $request->course_id,
                    'branch_id' => $request->branch_id,
                    'lesson_id' => $request->lesson_id,
                    'term_id' => $request->term_id,
                    'assessment_type' => $request->assessment_type,
                    'value' => $markValue,
                    'status' => 'recorded',
                ];

                if ($request->assessment_type === 'exam') {
                    $createData['exam_id'] = $request->exam_id;
                    $createData['exam_date'] = $request->exam_date;
                    $createData['marks_out_of'] = 100;
                } else {
                    $createData['marks_out_of'] = $request->marks_out_of;
                    $createData['test_date'] = $request->test_date;
                }

                // Add to preview data instead of saving
                $previewData[] = [
                    'student_id' => $studentId,
                    'student_name' => $student->full_name,
                    'reg_number' => $student->reg_number,
                    'mark' => $markValue,
                    'status' => 'new'
                ];
            }

            return response()->json([
                'success' => true,
                'preview' => true,
                'message' => 'CSV parsed successfully. Please review the marks below before confirming.',
                'marksCount' => count($previewData),
                'marksFailed' => count($marksFailed),
                'failedDetails' => $marksFailed,
                'marks' => $previewData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error uploading marks from CSV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload marks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm and save marks from CSV preview
     */
    public function confirmUpload(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.mark' => 'required|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
            'term_id' => 'required|exists:terms,id',
            'assessment_type' => 'required|in:test,exam',
            'marks_out_of' => 'required_if:assessment_type,test|numeric|min:1',
            'test_date' => 'required_if:assessment_type,test|date',
            'exam_id' => 'required_if:assessment_type,exam|exists:exams,id',
            'exam_date' => 'required_if:assessment_type,exam|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = auth('teacher')->user();

        // Verify teacher is assigned to this course/branch/lesson
        $assigned = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('branch_id', $request->branch_id)
            ->where('course_id', $request->course_id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();

        if (!$assigned) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this lesson in this course/branch.'
            ], 403);
        }

        try {
            $marksCreated = 0;
            $marksUpdated = 0;
            $marksFailed = [];

            foreach ($request->marks as $markData) {
                $studentId = $markData['student_id'];
                $markValue = (float) $markData['mark'];

                // Verify student is enrolled in this course/branch
                $student = \App\Models\Student::where('id', $studentId)
                    ->where('course_id', $request->course_id)
                    ->where('branch_id', $request->branch_id)
                    ->first();

                if (!$student) {
                    $marksFailed[] = "Student ID {$studentId} not found or not enrolled in this course/branch";
                    continue;
                }

                // Prepare mark data
                $createData = [
                    'student_id' => $studentId,
                    'course_id' => $request->course_id,
                    'branch_id' => $request->branch_id,
                    'lesson_id' => $request->lesson_id,
                    'term_id' => $request->term_id,
                    'assessment_type' => $request->assessment_type,
                    'value' => $markValue,
                    'status' => 'recorded',
                ];

                if ($request->assessment_type === 'exam') {
                    $createData['exam_id'] = $request->exam_id;
                    $createData['exam_date'] = $request->exam_date;
                    $createData['marks_out_of'] = 100;
                } else {
                    $createData['marks_out_of'] = $request->marks_out_of;
                    $createData['test_date'] = $request->test_date;
                }

                // Check if mark already exists
                $existingMark = Mark::where('student_id', $studentId)
                    ->where('course_id', $request->course_id)
                    ->where('branch_id', $request->branch_id)
                    ->where('lesson_id', $request->lesson_id)
                    ->where('term_id', $request->term_id)
                    ->where('assessment_type', $request->assessment_type);

                if ($request->assessment_type === 'test') {
                    $existingMark->where('test_date', $request->test_date);
                } else {
                    $existingMark->where('exam_id', $request->exam_id);
                }

                $existingMark = $existingMark->first();

                if ($existingMark) {
                    $existingMark->update([
                        'value' => $markValue,
                        'status' => 'updated'
                    ]);
                    $marksUpdated++;
                } else {
                    Mark::create($createData);
                    $marksCreated++;
                }
            }

            // Refresh report cards if marks were created/updated
            if ($marksCreated > 0 || $marksUpdated > 0) {
                $studentIds = array_column($request->marks, 'student_id');
                $this->refreshReportCardsForMarks($request->course_id, $request->term_id, $studentIds);
            }

            $message = "Marks saved successfully! Created: {$marksCreated}, Updated: {$marksUpdated}";
            if (!empty($marksFailed)) {
                $message .= ". Failed: " . count($marksFailed);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'marksCreated' => $marksCreated,
                'marksUpdated' => $marksUpdated,
                'marksFailed' => count($marksFailed),
                'failedDetails' => $marksFailed
            ]);

        } catch (\Exception $e) {
            \Log::error('Error confirming marks upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save marks: ' . $e->getMessage()
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
