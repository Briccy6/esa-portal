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
            ->get();
        return view('staff.dailies.show', compact('daily', 'attendances'));
    }

    public function updateAttendance(Request $request, TeacherDaily $daily)
    {
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
    $daily->approved = 1;
    $daily->save();

    return redirect()->route('dos.dailies.index')->with('success', 'Daily approved!');
}
}