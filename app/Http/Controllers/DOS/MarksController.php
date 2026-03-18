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
        
        // Get marks and group by lesson
        $marks = Mark::with(['course', 'exam', 'lesson', 'grade'])
            ->where('student_id', $student->id)
            ->when($request->term, fn($q) => $q->where('term', $request->term))
            ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
            ->orderBy('created_at', 'asc')
            ->get();

        // Group marks by lesson and create subjects structure using unified calculation
        $subjects = $this->calculateStudentSubjects($student->id, $request->term, $request->academic_year);
        
        $reportCard->subjects = $subjects;
        
        // Calculate totals using the same calculation method
        $reportCard->total_marks = $subjects->sum('total_mark');
        $reportCard->average_marks = $subjects->isNotEmpty() ? $subjects->avg('total_mark') : 0;
        
        // Calculate student position based on total marks compared to all students in same course
        $reportCard->class_position = $this->calculateStudentPosition($student, $reportCard->total_marks, $request->term, $request->academic_year);
        $reportCard->total_students = $this->getTotalStudentsInCourse($student->course_id, $request->term, $request->academic_year);
        
        // Get school information
        $schoolInfo = \App\Models\SchoolInformation::getActive();

        return view('staff.marks.tvet-report', compact('reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'));
    }
    
    /**
     * Unified method to calculate student subjects with consistent logic
     */
    private function calculateStudentSubjects($studentId, $term = null, $academicYear = null)
    {
        try {
            $subjects = collect();
            
            // Get marks for this student
            $marks = Mark::with(['lesson'])
                ->where('student_id', $studentId)
                ->when($term, fn($q) => $q->where('term', $term))
                ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear))
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Group marks by lesson
            $lessons = $marks->groupBy('lesson_id');
            
            foreach ($lessons as $lessonId => $lessonMarks) {
                $lesson = $lessonMarks->first()->lesson;
                if ($lesson) {
                    $subject = new \stdClass();
                    $subject->subject_name = $lesson->title;
                    $subject->subject_code = $lesson->code ?? strtoupper(substr($lesson->title, 0, 3));
                    $subject->credits = 3;
                    
                    // Calculate test average (multiple test marks for same lesson)
                    $testMarks = $lessonMarks->where('assessment_type', 'test');
                    $subject->test_average = $testMarks->isNotEmpty() ? $testMarks->avg('value') : 0;
                    
                    // Get exam mark (should be only one exam mark per lesson)
                    $examMark = $lessonMarks->where('assessment_type', 'exam')->first();
                    $subject->exam_mark = $examMark ? $examMark->value : 0;
                    
                    // Calculate weighted average: 40% test average + 60% exam mark
                    // Convert to percentage scale (out of 100)
                    $testWeighted = $subject->test_average * 0.4; // 40% weight for tests
                    $examWeighted = $subject->exam_mark * 0.6;    // 60% weight for exam
                    
                    // Ensure we're working with percentages
                    if ($subject->test_average > 0 && $subject->test_average <= 100) {
                        $testWeighted = $subject->test_average * 0.4;
                    } else {
                        // Convert from original scale to percentage
                        $testOutOf = $testMarks->first()->marks_out_of ?? 100;
                        $testWeighted = ($subject->test_average / $testOutOf) * 100 * 0.4;
                    }
                    
                    if ($subject->exam_mark > 0 && $subject->exam_mark <= 100) {
                        $examWeighted = $subject->exam_mark * 0.6;
                    } else {
                        // Convert from original scale to percentage
                        $examOutOf = $examMark->marks_out_of ?? 100;
                        $examWeighted = ($subject->exam_mark / $examOutOf) * 100 * 0.6;
                    }
                    
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
            
            return $subjects;
            
        } catch (\Exception $e) {
            \Log::error('Error calculating student subjects', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);
            return collect(); // Return empty collection
        }
    }

    /**
     * Calculate student's position in class based on total marks
     */
    private function calculateStudentPosition($student, $studentTotalMarks, $term = null, $academicYear = null)
    {
        try {
            // Get all students in the same course with their total marks
            $allStudentsMarks = $this->getAllStudentsTotalMarks($student->course_id, $term, $academicYear);
            
            if ($allStudentsMarks->isEmpty()) {
                return 1; // Only student
            }
            
            // Sort by total marks descending
            $sortedMarks = $allStudentsMarks->sortByDesc('total_marks');
            
            // Find current student's position
            $position = 1;
            $previousMarks = null;
            $sharedPositions = [];
            
            foreach ($sortedMarks as $index => $studentMarks) {
                $currentMarks = $studentMarks->total_marks;
                
                // Handle ties: if same marks as previous, share the same position
                if ($previousMarks !== null && $currentMarks == $previousMarks) {
                    // Same marks, share position with previous student(s)
                    $sharedPositions[] = $position;
                } else {
                    // Different marks, update position
                    if ($sharedPositions) {
                        // If we were in a tie, calculate the correct position
                        $position = $index + 1;
                        $sharedPositions = [];
                    } else {
                        $position = $index + 1;
                    }
                }
                
                // Check if this is our student
                if ($studentMarks->student_id == $student->id) {
                    return $position;
                }
                
                $previousMarks = $currentMarks;
            }
            
            return 1; // Fallback
            
        } catch (\Exception $e) {
            \Log::error('Error calculating student position', [
                'student_id' => $student->id,
                'course_id' => $student->course_id,
                'error' => $e->getMessage()
            ]);
            return 1; // Fallback position
        }
    }
    
    /**
     * Get total number of students in the same course
     */
    private function getTotalStudentsInCourse($courseId, $term = null, $academicYear = null)
    {
        try {
            $query = Student::where('course_id', $courseId);
            
            // If term or academic year filtering is needed, add those conditions
            // This depends on how your student data is structured
            
            return $query->count();
            
        } catch (\Exception $e) {
            \Log::error('Error getting total students in course', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
            return 1; // Fallback
        }
    }
    
    /**
     * Get all students' total marks in the same course for ranking
     */
    private function getAllStudentsTotalMarks($courseId, $term = null, $academicYear = null)
    {
        try {
            // Get all students in this course
            $students = Student::where('course_id', $courseId)->get();
            
            $studentsTotalMarks = collect();
            
            foreach ($students as $student) {
                // Use the same unified calculation method for consistency
                $subjects = $this->calculateStudentSubjects($student->id, $term, $academicYear);
                
                if ($subjects->isNotEmpty()) {
                    $totalMarks = $subjects->sum('total_mark');
                    
                    $studentsTotalMarks->push((object) [
                        'student_id' => $student->id,
                        'total_marks' => $totalMarks
                    ]);
                }
            }
            
            return $studentsTotalMarks;
            
        } catch (\Exception $e) {
            \Log::error('Error getting all students total marks', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
            return collect(); // Return empty collection
        }
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
