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
            $request->validate([
                'activities' => 'required|array',
                'course' => 'required|integer',
                'lesson' => 'required|integer',
                'unit' => 'required|integer',
                'comment' => 'required|string',
                'hours_studied' => 'required|numeric',
                'total_students' => 'required|integer',
                'date' => 'nullable|date',
                // Add other fields as needed
            ]);

            $teacher = Auth::guard('teacher')->user();

            $daily = TeacherDaily::create([
                'teacher_id'    => $teacher->id,
                'course_id'     => $request->course,
                'lesson_id'     => $request->lesson,
                'unit_id'       => $request->unit,
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

            // Validate course and lesson exist
            $course = Course::find($request->course);
            $lesson = Lesson::find($request->lesson);
            
            if (!$course) {
                return back()->with('error', 'Selected course not found. Please try again.');
            }
            
            if (!$lesson) {
                return back()->with('error', 'Selected lesson not found. Please try again.');
            }

            // Get all students for the course
            $students = $course->students()->get();
            
            if ($students->isEmpty()) {
                \Log::warning("No students found for course {$request->course}");
                // Continue without attendance if no students
            } else {
                // If attendance array is present, use it (manual marking)
                if ($request->has('attendance')) {
                    foreach ($request->attendance as $studentId => $status) {
                        try {
                            \App\Models\Attendance::create([
                                'student_id'   => $studentId,
                                'course_id'    => $request->course,
                                'lesson_id'    => $request->lesson,
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
                                'course_id'    => $request->course,
                                'lesson_id'    => $request->lesson,
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

            return back()->with('success', 'Daily activity and attendance submitted successfully!');
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
        // Check both direct teacher_id and pivot table assignments
        $lessons = \App\Models\Lesson::where('course_id', $courseId)
            ->where(function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                      ->orWhereHas('teachers', function($q) use ($teacher) {
                          $q->where('teachers.id', $teacher->id);
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
        $lesson = \App\Models\Lesson::findOrFail($lessonId);
        $units = $lesson->units()->get(['id', 'unit_name']);
        return response()->json($units);
    }

    public function getStudentCount($courseId)
    {
        $course = \App\Models\Course::findOrFail($courseId);
        $count = $course->students()->count();
        return response()->json(['count' => $count]);
    }
    public function getStudents($courseId)
    {
        $course = Course::findOrFail($courseId);
        $students = $course->students()->get(['id', 'full_name as name']);
        return response()->json($students);
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
