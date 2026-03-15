<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Branch;

class AttendanceController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        $branches = Branch::all();
        
        return view('admin.attendance.index', compact('courses', 'branches'));
    }

    public function showMarkForm(Request $request)
    {
        $courses = Course::all();
        $branches = Branch::all();

        $today = now()->format('Y-m-d');
        $lessons = [];
        $students = [];

        if ($request->filled('course_id') && $request->filled('branch_id')) {
            $courseId = $request->course_id;
            $branchId = $request->branch_id;

            $dayOfWeek = strtolower(now()->format('l'));

            $lessons = \App\Models\Timetable::where('course_id', $courseId)
                ->where('branch_id', $branchId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->where('effective_from', '<=', $today)
                ->where(function($q) use ($today) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', $today);
                })
                ->orderBy('start_time')
                ->get();

            $students = \App\Models\Student::where('course_id', $courseId)
                ->where('branch_id', $branchId)
                ->get();
        }

        return view('staff.attendance_manage', compact('courses', 'branches', 'lessons', 'students', 'today'));
    }

    public function mark(Request $request, $timetableId)
    {
        $date = $request->date;
        foreach ($request->status as $studentId => $status) {
            \App\Models\Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'timetable_id' => $timetableId,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                ]
            );
        }
        return back()->with('success', 'Attendance saved!');
    }
}
