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
use App\Models\LessonBranchCourse;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarksController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::all();
        $branches = Branch::all();
        $exams = Exam::all();
        $lessons = Lesson::all();
        
        // Get marks and group them by lesson and date
        $query = Mark::with(['student', 'course', 'branch', 'exam', 'lesson', 'grade'])
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->exam_id, fn($q) => $q->where('exam_id', $request->exam_id))
            ->when($request->lesson_id, fn($q) => $q->where('lesson_id', $request->lesson_id))
            ->when($request->assessment_type, fn($q) => $q->where('assessment_type', $request->assessment_type))
            ->when($request->student_search, function($q) use ($request) {
                $q->whereHas('student', function($q2) use ($request) {
                    $q2->where('full_name', 'like', '%'.$request->student_search.'%')
                       ->orWhere('id', $request->student_search);
                });
            })
            ->orderBy('lesson_id')
            ->orderBy('test_date')
            ->orderBy('student_id');

        $allMarks = $query->get();
        
        // Group marks by lesson, then by test date (same as teacher)
        $groupedMarks = [];
        $students = [];
        
        foreach ($allMarks as $mark) {
            $lessonId = $mark->lesson_id;
            $lessonTitle = $mark->lesson->title;
            $testDate = $mark->test_date ?: 'no-date'; // Handle empty dates
            $studentId = $mark->student_id;
            
            // Collect unique students
            if (!isset($students[$studentId])) {
                $students[$studentId] = [
                    'name' => $mark->student->full_name ?? 'Unknown',
                    'course' => $mark->course->course_name ?? 'Unknown',
                    'branch' => $mark->branch->name ?? 'Unknown',
                    'reg_number' => $mark->student->reg_number ?? 'Unknown'
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

        // Statistics
        $stats = [
            'total_marks' => Mark::count(),
            'exam_marks' => Mark::where('assessment_type', 'exam')->count(),
            'test_marks' => Mark::where('assessment_type', 'test')->count(),
            'average_score' => Mark::whereNotNull('value')->avg('value'),
            'highest_score' => Mark::max('value'),
            'lowest_score' => Mark::min('value'),
        ];

        return view('staff.marks.index', compact('groupedMarks', 'students', 'courses', 'branches', 'exams', 'lessons', 'stats'));
    }

    public function show($id)
    {
        $mark = Mark::with(['student.course', 'student.branch', 'student.academicYear', 'course', 'branch', 'exam', 'lesson', 'grade'])->findOrFail($id);
        return view('staff.marks.show', compact('mark'));
    }

    public function destroy($id)
    {
        try {
            $mark = Mark::findOrFail($id);
            
            // Log the deletion for audit purposes
            \Log::info('Mark deleted by DOS', [
                'mark_id' => $mark->id,
                'student_id' => $mark->student_id,
                'student_name' => $mark->student->full_name ?? 'Unknown',
                'course_id' => $mark->course_id,
                'course_name' => $mark->course->course_name ?? 'Unknown',
                'lesson_id' => $mark->lesson_id,
                'lesson_title' => $mark->lesson->title ?? 'Unknown',
                'assessment_type' => $mark->assessment_type,
                'value' => $mark->value,
                'deleted_by' => auth('staff')->user()->id,
                'deleted_by_name' => auth('staff')->user()->name,
                'deleted_at' => now()
            ]);
            
            $mark->delete();
            
            return redirect()->route('dos.marks.index')
                ->with('success', 'Mark deleted successfully. Student: ' . ($mark->student->full_name ?? 'Unknown') . 
                    ', Course: ' . ($mark->course->course_name ?? 'Unknown') . 
                    ', Value: ' . $mark->value . 
                    ($mark->assessment_type === 'test' ? '/' . $mark->marks_out_of : ''));
                    
        } catch (\Exception $e) {
            \Log::error('Failed to delete mark', [
                'mark_id' => $id,
                'error' => $e->getMessage(),
                'deleted_by' => auth('staff')->user()->id
            ]);
            
            return redirect()->route('dos.marks.index')
                ->with('error', 'Failed to delete mark: ' . $e->getMessage());
        }
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

            // Fetch lessons from lesson_branch_course pivot table based on student's branch and course
            $lessons = \App\Models\LessonBranchCourse::with('lesson')
                ->where('branch_id', $student->branch_id)
                ->where('course_id', $student->course_id)
                ->get()
                ->pluck('lesson');

            \Log::info('Lessons found from pivot:', [
                'lessons_count' => $lessons->count(),
                'lesson_ids' => $lessons->pluck('id')->toArray(),
            ]);
            
            // Query marks for this student with lesson and term relationships
            $query = Mark::with(['course', 'exam', 'lesson', 'grade', 'term'])
                ->where('student_id', $student->id)
                ->where('branch_id', $student->branch_id)
                ->where('course_id', $student->course_id)
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

            // Calculate term averages and annual summary
            $marksByTerm = $marks->groupBy(function($mark) {
                return $mark->term ? $mark->term->name : 'Unassigned';
            });

            $termAverages = [];
            $termSummaries = [];
            $allTermAverages = [];

            foreach ($marksByTerm as $termName => $termMarks) {
                $termAverage = $termMarks->whereNotNull('value')->avg('value');
                $termAverages[$termName] = round($termAverage, 2);
                $allTermAverages[] = $termAverage;

                $termSummaries[$termName] = [
                    'count' => $termMarks->count(),
                    'average' => round($termAverage, 2),
                    'exam_count' => $termMarks->where('assessment_type', 'exam')->count(),
                    'test_count' => $termMarks->where('assessment_type', 'test')->count(),
                    'highest' => $termMarks->max('value'),
                    'lowest' => $termMarks->min('value'),
                ];
            }

            // Calculate annual average
            $annualAverage = count($allTermAverages) > 0 ? round(array_sum($allTermAverages) / count($allTermAverages), 2) : 0;

            $stats['annual_average'] = $annualAverage;
            $stats['term_averages'] = $termAverages;
            $stats['term_summaries'] = $termSummaries;

            \Log::info('Term averages calculated:', [
                'term_count' => count($termAverages),
                'annual_average' => $annualAverage,
                'term_averages' => $termAverages,
            ]);

            return view('staff.marks.student-report', compact('student', 'marks', 'marksByCourse', 'stats', 'lessons'));
            
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

        $student = Student::with(['course', 'branch', 'academicYear'])->findOrFail($request->student_id);
        
        // Resolve student passport photo to base64 for consistency with admin template
        $studentPhotoBase64 = null;
        $studentPhotoMime   = 'image/jpeg';

        if ($student->passport_photo) {
            $photoPath = $student->passport_photo;

            // If it's already a full URL (e.g. stored on S3 / external)
            if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
                try {
                    $imageData = file_get_contents($photoPath);
                    if ($imageData !== false) {
                        $studentPhotoMime   = mime_content_type('data://application/octet-stream;base64,' . base64_encode($imageData)) ?? 'image/jpeg';
                        $studentPhotoBase64 = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not load remote student photo: ' . $e->getMessage());
                }
            } else {
                // Local file — resolve absolute path
                // Strip leading slash if present, then resolve via storage or public
                $localPath = public_path($photoPath);

                if (!file_exists($localPath)) {
                    // Try storage path
                    $localPath = storage_path('app/public/' . ltrim($photoPath, '/'));
                }

                if (file_exists($localPath)) {
                    $imageData = file_get_contents($localPath);
                    $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
                    $studentPhotoMime   = match($ext) {
                        'png'  => 'image/png',
                        'gif'  => 'image/gif',
                        'webp' => 'image/webp',
                        default => 'image/jpeg',
                    };
                    $studentPhotoBase64 = base64_encode($imageData);
                }
            }
        }

        // Create a mock report card object for TVET template
        $reportCard = new \stdClass();
        $reportCard->student = $student;
        $reportCard->course = $student->course;
        $reportCard->academicYear = (object)['year' => $request->academic_year ?? date('Y') . '-' . (date('Y') + 1)];
        $reportCard->term = (object)['term' => $request->term ?? 'Current Term'];
        
        // ============================================================================
        // Get lessons from lesson_branch_course for this student's branch and course
        // ============================================================================
        $lessonBranchCourses = LessonBranchCourse::with('lesson')
            ->where('branch_id', $student->branch_id)
            ->where('course_id', $student->course_id)
            ->get();
        
        // Get marks for these lessons
        $marks = Mark::with(['course', 'exam', 'lesson', 'grade'])
            ->where('student_id', $student->id)
            ->whereIn('lesson_id', $lessonBranchCourses->pluck('lesson_id'))
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
            ->orderBy('created_at', 'asc')
            ->get();

        // Group marks by lesson and create subjects structure
        $subjects = collect();
        $lessonMarksGrouped = $marks->groupBy('lesson_id');
        
        foreach ($lessonMarksGrouped as $lessonId => $lessonMarks) {
            $lesson = $lessonMarks->first()->lesson;
            if ($lesson) {
                $subject = new \stdClass();
                $subject->lesson_id = $lessonId;
                $subject->subject_name = $lesson->title;
                $subject->subject_code = $lesson->code ?? strtoupper(substr($lesson->title, 0, 3));
                $subject->credits = 3;
                
                // ================================================================
                // CALCULATE TEST MARKS (multiple marks average to 100)
                // ================================================================
                $testMarks = $lessonMarks->where('assessment_type', 'test');
                if ($testMarks->isNotEmpty()) {
                    // Convert all test marks to 100-point scale, then average
                    $testValuesNormalized = $testMarks->map(function($mark) {
                        // If mark is on different scale, convert to 100
                        if ($mark->marks_out_of && $mark->marks_out_of > 0 && $mark->marks_out_of != 100) {
                            return ($mark->value / $mark->marks_out_of) * 100;
                        }
                        return $mark->value; // Already on 100 scale
                    });
                    $subject->test_average = $testValuesNormalized->avg();
                } else {
                    $subject->test_average = 0;
                }
                
                // ================================================================
                // CALCULATE EXAM MARKS (on 100 scale)
                // ================================================================
                $examMark = $lessonMarks->where('assessment_type', 'exam')->first();
                if ($examMark) {
                    // Convert exam mark to 100-point scale if needed
                    if ($examMark->marks_out_of && $examMark->marks_out_of > 0 && $examMark->marks_out_of != 100) {
                        $subject->exam_mark = ($examMark->value / $examMark->marks_out_of) * 100;
                    } else {
                        $subject->exam_mark = $examMark->value;
                    }
                } else {
                    $subject->exam_mark = 0;
                }
                
                // ================================================================
                // CALCULATE WEIGHTED AVERAGE: 40% test + 60% exam
                // ================================================================
                $testWeighted = $subject->test_average * 0.4;      // 40% weight for tests
                $examWeighted = $subject->exam_mark * 0.6;         // 60% weight for exam
                $subject->total_mark = $testWeighted + $examWeighted;
                
                // Ensure total is capped at 100
                $subject->total_mark = min($subject->total_mark, 100);
                
                // Determine grade based on TVET standards
                if ($subject->total_mark >= 73) {
                    $subject->grade = 'A'; // Distinction
                } elseif ($subject->total_mark >= 60) {
                    $subject->grade = 'B'; // Credit  
                } elseif ($subject->total_mark >= 50) {
                    $subject->grade = 'C'; // Pass
                } else {
                    $subject->grade = 'NYC'; // Not Yet Competent
                }
                
                $subjects->push($subject);
            }
        }
        
        $reportCard->subjects = $subjects;
        
        // ================================================================
        // Calculate student position/ranking in the class
        // ================================================================
        // Get all students in the same course and branch
        $classStudents = Student::where('course_id', $student->course_id)
            ->where('branch_id', $student->branch_id)
            ->get();
        
        $totalStudents = $classStudents->count();
        $studentPosition = 1; // Default position
        
        if ($totalStudents > 1) {
            // Calculate average marks for each student in the class
            $studentAverages = [];
            foreach ($classStudents as $classStudent) {
                $classStudentMarks = Mark::with('lesson')
                    ->where('student_id', $classStudent->id)
                    ->whereIn('lesson_id', $lessonBranchCourses->pluck('lesson_id'))
                    ->when($request->term, fn($q) => $q->where('term', $request->term))
                    ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
                    ->get();
                
                // Calculate average for this student
                $classStudentTotal = 0;
                $classStudentCount = 0;
                
                $classStudentLessonMarks = $classStudentMarks->groupBy('lesson_id');
                foreach ($classStudentLessonMarks as $clessonId => $clessonMarks) {
                    // Test marks (convert to 100)
                    $ctestMarks = $clessonMarks->where('assessment_type', 'test');
                    $ctestAvg = 0;
                    if ($ctestMarks->isNotEmpty()) {
                        $ctestValuesNormalized = $ctestMarks->map(function($mark) {
                            if ($mark->marks_out_of && $mark->marks_out_of > 0 && $mark->marks_out_of != 100) {
                                return ($mark->value / $mark->marks_out_of) * 100;
                            }
                            return $mark->value;
                        });
                        $ctestAvg = $ctestValuesNormalized->avg();
                    }
                    
                    // Exam marks (convert to 100)
                    $cexamMark = $clessonMarks->where('assessment_type', 'exam')->first();
                    $cexamValue = 0;
                    if ($cexamMark) {
                        if ($cexamMark->marks_out_of && $cexamMark->marks_out_of > 0 && $cexamMark->marks_out_of != 100) {
                            $cexamValue = ($cexamMark->value / $cexamMark->marks_out_of) * 100;
                        } else {
                            $cexamValue = $cexamMark->value;
                        }
                    }
                    
                    // Weighted average for this lesson: 40% test + 60% exam
                    $lessonTotal = ($ctestAvg * 0.4) + ($cexamValue * 0.6);
                    $classStudentTotal += $lessonTotal;
                    $classStudentCount++;
                }
                
                $studentAverages[$classStudent->id] = $classStudentCount > 0 ? $classStudentTotal / $classStudentCount : 0;
            }
            
            // Sort students by average (highest first) and find this student's position
            arsort($studentAverages);
            $studentPosition = array_search($student->id, array_keys($studentAverages)) + 1;
        }
        
        // Calculate totals
        $reportCard->total_marks = $subjects->sum('total_mark');
        $reportCard->average_marks = $subjects->isNotEmpty() ? $subjects->avg('total_mark') : 0;
        $reportCard->class_position = $studentPosition;
        $reportCard->total_students = $totalStudents;
        
        // Get school information
        $schoolInfo = \App\Models\SchoolInformation::getActive();

        return view('staff.marks.tvet-report', compact('reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'));
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
