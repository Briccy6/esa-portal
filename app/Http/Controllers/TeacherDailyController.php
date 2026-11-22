<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                'attendance' => 'sometimes|array',
                'attendance.*' => 'required|in:P,A,AR,L',
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

            // Validate that the lesson is associated with at least one of the selected courses via lesson_branch_course pivot table
            $lessonCourseIds = $lesson->courses()->pluck('courses.id')->toArray();

            \Log::info('Lesson course associations', [
                'lesson_id' => $lesson->id,
                'lesson_course_ids' => $lessonCourseIds,
                'selected_courses' => $request->courses,
            ]);

            // Check if lesson is associated with at least one selected course
            $hasMatchingCourse = false;
            foreach ($request->courses as $courseId) {
                $course = Course::find($courseId);
                if (!$course) {
                    \Log::error('Course not found', ['course_id' => $courseId]);
                    return back()->with('error', 'One of the selected courses not found. Please try again.');
                }

                // Verify the lesson is associated with this course via the pivot table
                if (in_array($courseId, $lessonCourseIds)) {
                    $hasMatchingCourse = true;
                }
            }

            if (!$hasMatchingCourse) {
                \Log::error('Lesson not associated with any selected course', [
                    'lesson_id' => $lesson->id,
                    'selected_courses' => $request->courses,
                    'lesson_course_ids' => $lessonCourseIds,
                ]);
                return back()->with('error', 'Lesson does not belong to any of the selected courses. Please select a lesson that is assigned to at least one of your courses.');
            }

            \Log::info('Creating TeacherDaily record', [
                'teacher_id' => $teacher->id,
                'course_id' => $request->courses[0],
                'course_ids' => $request->courses,
                'lesson_id' => $request->lesson,
                'unit_id' => $request->unit,
                'branch_id' => $branchId,
            ]);

            $attendanceStatuses = $request->input('attendance');
            $absences = is_array($attendanceStatuses)
                ? count(array_filter($attendanceStatuses, fn ($status) => in_array($status, ['A', 'AR'], true)))
                : 0;

            $daily = DB::transaction(function () use ($request, $teacher, $branchId, $attendanceStatuses, $absences) {
                $daily = TeacherDaily::create([
                    'teacher_id'    => $teacher->id,
                    'course_id'     => $request->courses[0],
                    'course_ids'    => $request->courses,
                    'lesson_id'     => $request->lesson,
                    'unit_id'       => $request->unit,
                    'branch_id'     => $branchId,
                    'activity'      => $request->activities,
                    'hours_studied' => $request->hours_studied,
                    'total_students'=> $request->total_students,
                    'absences'      => $absences,
                    'quiz_link'     => $request->quiz_link,
                    'marking_guide' => $request->marking_guide,
                    'total_marks'   => $request->total_marks,
                    'challenges'    => $request->challenges,
                    'comment'       => $request->comment,
                    'date'          => $request->date ?? now()->toDateString(),
                ]);

                $studentsQuery = Student::whereIn('course_id', $request->courses)
                    ->where('branch_id', $branchId)
                    ->where('is_blocked', '!=', 1);
                if ($yearId = \App\Services\AcademicYearContext::selectedId()) {
                    $studentsQuery->where('academic_year_id', $yearId);
                }
                $students = $studentsQuery->get();

                if (is_array($attendanceStatuses)) {
                    $invalidStudentIds = array_diff(
                        array_map('intval', array_keys($attendanceStatuses)),
                        $students->modelKeys()
                    );
                    if ($invalidStudentIds) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'attendance' => 'Attendance can only be recorded for students in the selected courses and branch.',
                        ]);
                    }
                }

                if ($students->isEmpty()) {
                    \Log::warning("No students found for courses " . implode(',', $request->courses) . " in branch {$branchId}");
                    return $daily;
                }

                foreach ($students as $student) {
                    if (is_array($attendanceStatuses) && !array_key_exists($student->id, $attendanceStatuses)) {
                        continue;
                    }

                    \App\Models\Attendance::create([
                        'student_id' => $student->id,
                        'course_id' => $student->course_id,
                        'lesson_id' => $request->lesson,
                        'date' => $daily->date,
                        'status' => is_array($attendanceStatuses) ? $attendanceStatuses[$student->id] : 'P',
                        'marked_by' => null, // This foreign key targets users; teachers use a separate table.
                        'marked_at' => now(),
                        'approved' => false,
                    ]);
                }

                return $daily;
            });

            \Log::info('TeacherDaily created successfully', ['daily_id' => $daily->id]);

            return back()->with('success', 'Daily activity and attendance submitted successfully for ' . count($request->courses) . ' course(s)!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('TeacherDaily validation failed', [
                'errors' => $e->errors(),
            ]);
            return back()->withErrors($e->errors())->with('error', 'Validation failed. Please check all required fields.');
        } catch (\Exception $e) {
            \Log::error('TeacherDaily creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to submit daily activity: ' . $e->getMessage() . '. Please check the logs for details.');
        }
    }

    public function getLessons($courseId)
    {
        $teacher = Auth::guard('teacher')->user();
        
        \Log::info('getLessons called', [
            'teacher_id' => $teacher->id,
            'course_id' => $courseId,
        ]);

        // Get lesson IDs assigned to this teacher for this course via lesson_branch_course
        $lessonIds = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('course_id', $courseId)
            ->distinct()
            ->pluck('lesson_id')
            ->toArray();
        
        \Log::info('Lesson IDs from lesson_branch_course', [
            'lesson_ids' => $lessonIds,
        ]);

        if (empty($lessonIds)) {
            \Log::warning("No lessons found via lesson_branch_course for teacher {$teacher->id} in course {$courseId}");
            
            // Fallback: try lessons where teacher_id matches directly
            $lessons = \App\Models\Lesson::where('course_id', $courseId)
                ->where('teacher_id', $teacher->id)
                ->get(['id', 'title']);
            
            if ($lessons->isEmpty()) {
                \Log::warning("No lessons found via direct teacher_id for teacher {$teacher->id} in course {$courseId}");
                return response()->json([]);
            }
        } else {
            $lessons = \App\Models\Lesson::whereIn('id', $lessonIds)
                ->get(['id', 'title']);
        }

        \Log::info("Found {$lessons->count()} lessons for teacher {$teacher->id} in course {$courseId}");
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
        
        // Log the teacher info
        \Log::info('getCoursesByBranch - Teacher Info', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->teacher_name,
            'branch_id' => $branchId,
        ]);
        
        // Check what's in lesson_branch_course for this teacher and branch
        $assignments = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('branch_id', $branchId)
            ->get();
        
        \Log::info('getCoursesByBranch - Lesson Branch Course Assignments', [
            'count' => $assignments->count(),
            'assignments' => $assignments->toArray(),
        ]);
        
        // Get courses assigned to this teacher in this branch via lesson_branch_course
        $courseIds = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->where('branch_id', $branchId)
            ->distinct()
            ->pluck('course_id');
        
        \Log::info('getCoursesByBranch - Course IDs', [
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
            
            $teacherCourseAssignments = \DB::table('teacher_course')
                ->where('teacher_id', $teacher->id)
                ->get();
            
            \Log::info('getCoursesByBranch - Teacher Course Assignments', [
                'count' => $teacherCourseAssignments->count(),
                'assignments' => $teacherCourseAssignments->toArray(),
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
            
            $branchCourses = \DB::table('branch_course')
                ->where('branch_id', $branchId)
                ->get();
            
            \Log::info('getCoursesByBranch - Branch Course Assignments', [
                'count' => $branchCourses->count(),
                'assignments' => $branchCourses->toArray(),
            ]);
            
            $courses = Course::whereHas('branches', function($query) use ($branchId) {
                $query->where('branches.id', $branchId);
            })->get(['id', 'course_name']);
        }
        
        \Log::info('getCoursesByBranch - Final Result', [
            'courses_count' => $courses->count(),
            'courses' => $courses->toArray(),
        ]);
        
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
