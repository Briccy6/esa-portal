<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TeacherDaily;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\TeacherDailyPayment;

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
        $daily->load(['teacher', 'course', 'lesson', 'unit', 'branch']);
        $courseIds = collect($daily->course_ids ?? [])
            ->push($daily->course_id)
            ->filter()
            ->unique()
            ->values();
        $courses = Course::whereIn('id', $courseIds)
            ->orderBy('course_name')
            ->get();
        $students = Student::whereIn('course_id', $courseIds)
            ->when($daily->branch_id, function ($query) use ($daily) {
                $query->where('branch_id', $daily->branch_id);
            })
            ->orderBy('full_name')
            ->get();

        $attendanceQuery = Attendance::whereIn('course_id', $courseIds)
            ->where('lesson_id', $daily->lesson_id)
            ->whereDate('date', $daily->date)
            ->with('student');

        if ($daily->branch_id) {
            $attendanceQuery->whereHas('student', function ($query) use ($daily) {
                $query->where('branch_id', $daily->branch_id);
            });
        }

        $attendances = $attendanceQuery->get()->keyBy('student_id');
        $studentsById = $students->keyBy('id');
        foreach ($attendances as $attendance) {
            if ($attendance->student) {
                $studentsById->put($attendance->student->id, $attendance->student);
            }
        }
        $students = $studentsById->values()->sortBy('full_name')->values();
        $courseNamesById = $courses->pluck('course_name', 'id');

        return view('staff.dailies.show', compact(
            'daily',
            'courses',
            'courseNamesById',
            'students',
            'attendances'
        ));
    }

    public function updateAttendance(Request $request, TeacherDaily $daily)
    {
        abort_unless(Auth::guard('staff')->check(), 403);

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'nullable|in:P,A,AR,L',
        ]);
        $attendanceUpdates = collect($validated['attendance'])
            ->filter(function ($status) {
                return $status !== null && $status !== '';
            });

        if ($attendanceUpdates->isEmpty()) {
            return back()->with('error', 'Select an attendance status for at least one student.');
        }

        $courseIds = collect($daily->course_ids ?? [])
            ->push($daily->course_id)
            ->filter()
            ->unique()
            ->values();
        $students = Student::whereIn('id', $attendanceUpdates->keys())
            ->whereIn('course_id', $courseIds)
            ->when($daily->branch_id, function ($query) use ($daily) {
                $query->where('branch_id', $daily->branch_id);
            })
            ->get()
            ->keyBy('id');

        if ($students->count() !== $attendanceUpdates->count()) {
            return back()->with('error', 'One or more selected students are not assigned to this daily report.');
        }

        DB::transaction(function () use ($daily, $courseIds, $students, $attendanceUpdates) {
            $existingAttendancesQuery = Attendance::whereIn('student_id', $attendanceUpdates->keys())
                ->whereIn('course_id', $courseIds)
                ->where('lesson_id', $daily->lesson_id)
                ->whereDate('date', $daily->date);

            if ($daily->branch_id) {
                $existingAttendancesQuery->whereHas('student', function ($query) use ($daily) {
                    $query->where('branch_id', $daily->branch_id);
                });
            }

            $existingAttendances = $existingAttendancesQuery->get()->keyBy('student_id');

            foreach ($attendanceUpdates as $studentId => $status) {
                $student = $students->get($studentId);
                $attendance = $existingAttendances->get($studentId);

                if (!$attendance) {
                    $attendance = new Attendance([
                        'student_id' => $student->id,
                        'course_id' => $courseIds->contains($student->course_id)
                            ? $student->course_id
                            : $daily->course_id,
                        'lesson_id' => $daily->lesson_id,
                        'date' => $daily->date,
                        'approved' => (bool) $daily->approved,
                    ]);
                }

                $attendance->status = $status;
                $attendance->marked_at = now();
                $attendance->save();
            }
        });

        return back()->with('success', 'Attendance saved for ' . $attendanceUpdates->count() . ' student(s).');
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
            $wasApproved = $daily->approved;
            $daily->approved = $validated['status'] === 'approved';
            $daily->save();

            // If status is being changed from approved to pending, delete the payment record
            if ($wasApproved && !$daily->approved) {
                $payment = $daily->payment;
                if ($payment) {
                    $payment->delete();
                    \Log::info('Payment record deleted when daily status changed to pending', [
                        'daily_id' => $daily->id,
                        'payment_id' => $payment->id,
                    ]);
                }
            }

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
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:P,A,AR,L',
        ]);

        return $this->updateAttendance($request, $daily);
    }

    public function destroy(TeacherDaily $daily)
    {
        $deleted = DB::transaction(function () use ($daily) {
            $daily = TeacherDaily::query()
                ->whereKey($daily->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $courseIds = collect($daily->course_ids ?? [])
                ->push($daily->course_id)
                ->filter()
                ->unique()
                ->values();

            $paymentsDeleted = TeacherDailyPayment::where('teacher_daily_id', $daily->id)->delete();

            $attendanceQuery = Attendance::whereDate('date', $daily->date)
                ->where('lesson_id', $daily->lesson_id)
                ->whereIn('course_id', $courseIds);

            if ($daily->branch_id) {
                $attendanceQuery->whereHas('student', function ($query) use ($daily) {
                    $query->where('branch_id', $daily->branch_id);
                });
            }

            $attendanceDeleted = $attendanceQuery->delete();
            $dailyId = $daily->id;
            $daily->delete();

            return [
                'daily_id' => $dailyId,
                'payments' => $paymentsDeleted,
                'attendance' => $attendanceDeleted,
            ];
        });

        \Log::info('Teacher daily and related records deleted by DOS', [
            'daily_id' => $deleted['daily_id'],
            'payment_records_deleted' => $deleted['payments'],
            'attendance_records_deleted' => $deleted['attendance'],
            'staff_id' => Auth::guard('staff')->id(),
        ]);

        $message = 'Daily report deleted with '
            . $deleted['payments'] . ' teacher payment record(s) and '
            . $deleted['attendance'] . ' attendance record(s).';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_payment_records' => $deleted['payments'],
                'deleted_attendance_records' => $deleted['attendance'],
            ]);
        }

        return back()->with('success', $message);
    }
}
