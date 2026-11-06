<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TeacherDaily;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonUnit;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Models\Transcript;
class TeacherDailyController extends Controller
{
    public function store(Request $request)
    {
        try {
            \Log::info('TeacherDaily store attempt', [
                'request_data' => $request->all(),
                'teacher_id' => Auth::guard('teacher')->user()?->id,
            ]);

            $request->validate([
                'activities' => 'required|array',
                'courses' => 'required|array', // Changed from single course to multiple courses
                'lesson' => 'required|integer',
                'unit' => 'required|integer',
                'branch_id' => 'required|integer', // Added branch selection
                'comment' => 'required|string',
                'hours_studied' => 'required|numeric',
                'total_students' => 'required|integer',
                'date' => 'nullable|date',
            ]);

            $teacher = Auth::guard('teacher')->user();
            $branchId = $request->branch_id;

            \Log::info('Validation passed', [
                'teacher_id' => $teacher->id,
                'branch_id' => $branchId,
                'courses' => $request->courses,
                'lesson' => $request->lesson,
            ]);

            // Verify teacher is assigned to this branch
            if (!$teacher->branches->contains('id', $branchId)) {
                return back()->with('error', 'You are not assigned to this branch. Please select a branch you belong to.');
            }

            $lesson = Lesson::find($request->lesson);
            
            if (!$lesson) {
                return back()->with('error', 'Selected lesson not found. Please try again.');
            }

            // Validate that the lesson is associated with all selected courses via lesson_branch_course pivot table
            $lessonCourseIds = $lesson->courses()->pluck('courses.id')->toArray();

            \Log::info('Lesson course associations', [
                'lesson_id' => $lesson->id,
                'lesson_course_ids' => $lessonCourseIds,
                'selected_courses' => $request->courses,
            ]);

            foreach ($request->courses as $courseId) {
                $course = Course::find($courseId);
                if (!$course) {
                    \Log::error('Course not found', ['course_id' => $courseId]);
                    return back()->with('error', 'One of the selected courses not found. Please try again.');
                }

                // Verify the lesson is associated with this course via the pivot table
                if (!in_array($courseId, $lessonCourseIds)) {
                    \Log::error('Lesson not associated with course', [
                        'lesson_id' => $lesson->id,
                        'course_id' => $courseId,
                        'lesson_course_ids' => $lessonCourseIds,
                    ]);
                    return back()->with('error', 'Lesson does not belong to one of the selected courses. All courses must have the same lesson.');
                }
            }

            \Log::info('Creating TeacherDaily record', [
                'teacher_id' => $teacher->id,
                'course_id' => $request->courses[0],
                'lesson_id' => $request->lesson,
                'unit_id' => $request->unit,
                'branch_id' => $branchId,
            ]);

            // Create a single TeacherDaily record for the session
            $daily = TeacherDaily::create([
                'teacher_id'    => $teacher->id,
                'course_id'     => $request->courses[0], // Store primary course
                'lesson_id'     => $request->lesson,
                'unit_id'       => $request->unit,
                'branch_id'     => $branchId, // Store branch
                'activity'      => json_encode($request->activities),
                'hours_studied' => $request->hours_studied,
                'total_students'=> $request->total_students,
                'absences'      => isset($request->attendance) ? count(array_filter($request->attendance, fn($v) => $v !== 'P')) : 0,
                'quiz_link'     => $request->quiz_link,
                'marking_guide' => $request->marking_guide,
                'total_marks'   => $request->total_marks,
                'challenges'    => $request->challenges,
                'comment'       => $request->comment,
                'date'          => $request->date ?? now()->toDateString(),
            ]);

            \Log::info('TeacherDaily created successfully', ['daily_id' => $daily->id]);

            // Get all students for the selected courses AND branch
            $students = Student::whereIn('course_id', $request->courses)
                ->where('branch_id', $branchId)
                ->get();
            
            if ($students->isEmpty()) {
                \Log::warning("No students found for courses " . implode(',', $request->courses) . " in branch {$branchId}");
                // Continue without attendance if no students
            } else {
                // If attendance array is present, use it (manual marking)
                if ($request->has('attendance')) {
                    foreach ($request->attendance as $studentId => $status) {
                        try {
                            \App\Models\Attendance::create([
                                'student_id'   => $studentId,
                                'course_id'    => $request->courses[0], // Store primary course
                                'lesson_id'    => $request->lesson,
                                'branch_id'    => $branchId,
                                'date'         => $daily->date,
                                'status'       => $status, // 'P', 'A', etc.
                                'marked_by'    => $teacher->id,
                                'marked_at'    => now(),
                                'approved'     => false,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Failed to create attendance for student {$studentId}: " . $e->getMessage());
                            // Continue with other students, don't fail the entire submission
                        }
                    }
                } else {
                    // All present by default
                    foreach ($students as $student) {
                        try {
                            \App\Models\Attendance::create([
                                'student_id'   => $student->id,
                                'course_id'    => $request->courses[0], // Store primary course
                                'lesson_id'    => $request->lesson,
                                'branch_id'    => $branchId,
                                'date'         => $daily->date,
                                'status'       => 'P',
                                'marked_by'    => $teacher->id,
                                'marked_at'    => now(),
                                'approved'     => false,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Failed to create default attendance for student {$student->id}: " . $e->getMessage());
                            // Continue with other students, don't fail the entire submission
                        }
                    }
                }
            }

            return back()->with('success', 'Daily activity and attendance submitted successfully for ' . count($request->courses) . ' course(s)!');
        } catch (\Exception $e) {
            \Log::error('TeacherDaily creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit daily activity and attendance. Please try again.');
        }
    }

    public function getLessons($courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        $course = $teacher->courses()->where('courses.id', $courseId)->first();
        if (!$course) {
            \Log::error("Teacher {$teacher->id} not assigned to course {$courseId}");
            return response()->json([], 404);
        }

        // Get lessons assigned to this teacher for this course
        // Check both direct teacher_id and pivot table assignments with course_id
        $lessons = \App\Models\Lesson::where('course_id', $courseId)
            ->where(function($query) use ($teacher, $courseId) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereHas('branchCourses', function($q) use ($teacher, $courseId) {
                          $q->where('teacher_id', $teacher->id)
                            ->where('course_id', $courseId);
                      });
            })
            ->get(['id', 'title']);

        if ($lessons->isEmpty()) {
            \Log::warning("No lessons found for teacher {$teacher->id} in course {$courseId}");
        } else {
            \Log::info("Found {$lessons->count()} lessons for teacher {$teacher->id} in course {$courseId}");
        }
        return response()->json($lessons);
    }

    public function getUnits($lessonId)
    {
        $teacher = Auth::guard('teacher')->user();
        $lesson = \App\Models\Lesson::findOrFail($lessonId);
        
        // Verify teacher is assigned to this lesson
        $isAssigned = $lesson->where('id', $lessonId)
            ->where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereHas('teachers', function($q) use ($teacher) {
                          $q->where('teachers.id', $teacher->id);
                      });
            })->exists();
            
        if (!$isAssigned) {
            \Log::warning("Teacher {$teacher->id} not assigned to lesson {$lessonId}");
            return response()->json([], 403);
        }
        
        $units = $lesson->units()->get(['id', 'unit_name']);
        
        if ($units->isEmpty()) {
            \Log::warning("No units found for lesson {$lessonId}");
        } else {
            \Log::info("Found {$units->count()} units for lesson {$lessonId}");
        }
        
        return response()->json($units);
    }

    public function getStudentCount($courseId)
    {
        $course = \App\Models\Course::findOrFail($courseId);
        $count = $course->students()->count();
        return response()->json(['count' => $count]);
    }
    public function getStudents($courseId, Request $request)
    {
        $branchId = $request->query('branch_id');
        
        if (!$branchId) {
            return response()->json(['error' => 'Branch ID is required'], 400);
        }
        
        $students = Student::where('course_id', $courseId)
            ->where('branch_id', $branchId)
            ->get(['id', 'full_name as name']);
            
        return response()->json($students);
    }

    /**
     * Get courses for a teacher in a specific branch
     */
    public function getCoursesByBranch($branchId)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Get courses assigned to this teacher in this branch via lesson_branch_course
        $courseIds = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('branch_id', $branchId)
            ->distinct()
            ->pluck('course_id');
        
        // Log for debugging
        \Log::info('getCoursesByBranch', [
            'teacher_id' => $teacher->id,
            'branch_id' => $branchId,
            'course_ids' => $courseIds->toArray(),
        ]);
        
        $courses = Course::whereIn('id', $courseIds)
            ->get(['id', 'course_name']);
        
        // Fallback: if no courses found via lesson_branch_course, try teacher_course relationship
        if ($courses->isEmpty()) {
            \Log::warning('No courses found via lesson_branch_course, trying teacher_course', [
                'teacher_id' => $teacher->id,
                'branch_id' => $branchId,
            ]);
            
            $courseIds = \DB::table('teacher_course')
                ->where('teacher_id', $teacher->id)
                ->pluck('course_id');
            
            $courses = Course::whereIn('id', $courseIds)
                ->get(['id', 'course_name']);
        }
        
        // Final fallback: if still no courses, show all courses in the branch
        if ($courses->isEmpty()) {
            \Log::warning('No courses found via teacher_course, showing all branch courses', [
                'teacher_id' => $teacher->id,
                'branch_id' => $branchId,
            ]);
            
            $courses = Course::whereHas('branches', function($query) use ($branchId) {
                $query->where('branches.id', $branchId);
            })->get(['id', 'course_name']);
        }
        
        return response()->json($courses);
    }
    public function index()
    {
        $teacher = Auth::guard('teacher')->user();
        $dailies = TeacherDaily::where('teacher_id', $teacher->id)
            ->orderByDesc('date')
            ->paginate(20);

        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        return view('teacher.dailies.index', compact('dailies', 'announcements'));
    }
}
