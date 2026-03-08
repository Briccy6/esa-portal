<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherDaily;
use App\Models\Attendance;

class DosDailyController extends Controller
{
    public function index()
    {
        $dailies = TeacherDaily::orderByDesc('date')->paginate(20);
        return view('staff.dailies.index', compact('dailies'));
    }

    public function show(TeacherDaily $daily)
    {
        $attendances = Attendance::where('course_id', $daily->course_id)
            ->where('lesson_id', $daily->lesson_id)
            ->where('date', $daily->date)
            ->with('student')
            ->get();
            
        // Debug: Log attendance count
        \Log::info("Found {$attendances->count()} attendance records for daily {$daily->id}");
        
        return view('staff.dailies.show', compact('daily', 'attendances'));
    }

    public function updateAttendance(Request $request, TeacherDaily $daily)
    {
        // Check if attendance data exists
        if (!$request->has('attendance') || is_null($request->attendance)) {
            return back()->with('error', 'No attendance data provided to update.');
        }

        foreach ($request->attendance as $attendanceId => $status) {
            $attendance = Attendance::find($attendanceId);
            if ($attendance) {
                $attendance->status = $status;
                $attendance->save();
            }
        }
        return back()->with('success', 'Attendance updated!');
    }

    public function approve(TeacherDaily $daily)
    {
        // Only approve attendance records, not the daily record itself
        // (teacher_dailies table doesn't have 'approved' column)
        
        // Approve all related attendance records
        Attendance::where('course_id', $daily->course_id)
            ->where('lesson_id', $daily->lesson_id)
            ->where('date', $daily->date)
            ->update(['approved' => true]);

        return redirect()->route('dos.dailies.index')->with('success', 'Attendance approved successfully!');
    }

    public function approveAttendance(Request $request, TeacherDaily $daily)
    {
        // Get attendance records for this daily
        $attendances = Attendance::where('course_id', $daily->course_id)
            ->where('lesson_id', $daily->lesson_id)
            ->where('date', $daily->date)
            ->get();

        foreach ($attendances as $attendance) {
            $attendance->approved = true;
            $attendance->save();
        }

        return back()->with('success', 'Attendance approved!');
    }

    public function markAttendance(Request $request, TeacherDaily $daily)
    {
        // Validate attendance data
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:P,A,AR,L',
        ]);

        // Get all students for this course
        $courseStudents = \App\Models\Student::where('course_id', $daily->course_id)->get();

        // Create attendance records for each student
        foreach ($courseStudents as $student) {
            $status = $request->attendance[$student->id] ?? 'P'; // Default to Present if not specified
            
            // Check if attendance record already exists
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->where('course_id', $daily->course_id)
                ->where('lesson_id', $daily->lesson_id)
                ->where('date', $daily->date)
                ->first();

            if ($existingAttendance) {
                // Update existing record (don't set marked_by to avoid foreign key issue)
                $existingAttendance->status = $status;
                $existingAttendance->marked_at = now();
                $existingAttendance->save();
            } else {
                // Create new record (don't set marked_by to avoid foreign key issue)
                Attendance::create([
                    'student_id' => $student->id,
                    'course_id' => $daily->course_id,
                    'lesson_id' => $daily->lesson_id,
                    'date' => $daily->date,
                    'status' => $status,
                    'marked_at' => now(),
                    'approved' => false,
                ]);
            }
        }

        return redirect()->route('dos.dailies.show', $daily->id)
            ->with('success', 'Attendance marked successfully! ' . $courseStudents->count() . ' students processed.');
    }
}