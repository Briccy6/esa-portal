<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TeacherDaily;
use App\Models\Attendance;
use App\Models\Course;

class DosDailyController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,approved',
        ]);

        $query = TeacherDaily::with(['teacher', 'course', 'lesson', 'unit'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (($validated['status'] ?? null) === 'approved') {
            $query->where('approved', true);
        } elseif (($validated['status'] ?? null) === 'pending') {
            $query->where('approved', false);
        }

        $dailies = $query->paginate(20)->withQueryString();
        $courseIds = $dailies->getCollection()
            ->flatMap(function (TeacherDaily $daily) {
                return array_merge((array) $daily->course_ids, [$daily->course_id]);
            })
            ->filter()
            ->unique()
            ->values();
        $courseNamesById = Course::whereIn('id', $courseIds)->pluck('course_name', 'id');
        $stats = TeacherDaily::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as approved')
            ->selectRaw('SUM(CASE WHEN approved = 0 THEN 1 ELSE 0 END) as pending')
            ->first();

        return view('staff.dailies.index', compact('dailies', 'courseNamesById', 'stats'));
    }

    public function show(TeacherDaily $daily)
    {
        $attendances = Attendance::where('course_id', $daily->course_id)
            ->where('lesson_id', $daily->lesson_id)
            ->where('date', $daily->date)
            ->with('student')
            ->get()
            ->sortBy(function($attendance) {
                return $attendance->student->full_name ?? '';
            });

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
        $request = request();
        $request->merge(['status' => 'approved']);

        return $this->updateStatus($request, $daily);
    }

    public function updateStatus(Request $request, TeacherDaily $daily)
    {
        abort_unless(Auth::guard('staff')->check(), 403);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved',
        ]);

        DB::transaction(function () use ($daily, $validated) {
            $daily->approved = $validated['status'] === 'approved';
            $daily->save();

            $courseIds = collect($daily->course_ids ?? [])
                ->push($daily->course_id)
                ->filter()
                ->unique();
            $attendanceQuery = Attendance::whereIn('course_id', $courseIds)
                ->where('lesson_id', $daily->lesson_id)
                ->whereDate('date', $daily->date);

            if ($daily->branch_id) {
                $attendanceQuery->whereHas('student', function ($query) use ($daily) {
                    $query->where('branch_id', $daily->branch_id);
                });
            }

            $attendanceQuery->update(['approved' => $daily->approved]);
        });

        return back()->with('success', 'Daily report status updated to ' . $validated['status'] . '.');
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