<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TeacherDaily;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Teacher;
use App\Services\TeacherPaymentService;

class DosTeacherDailyController extends Controller
{
    protected $paymentService;

    public function __construct(TeacherPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /**
     * Display all teacher daily activities for DOS approval
     */
    public function index(Request $request)
    {
        $query = TeacherDaily::with([
            'teacher:id,teacher_name',
            'course:id,course_name',
            'lesson:id,title',
            'unit:id,unit_name',
            'branch:id,name',
        ])
            ->orderByDesc('date');

        // Filter by branch if selected
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by course if selected
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by teacher if selected
        if ($request->has('teacher_id') && $request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by approval status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'approved') {
                $query->where('approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('approved', false);
            }
        }

        $dailies = $query->paginate(20);

        $courseIds = $dailies->getCollection()
            ->flatMap(fn (TeacherDaily $daily) => $daily->course_ids ?? [])
            ->unique()
            ->values();
        $courseNamesById = Course::whereIn('id', $courseIds)->pluck('course_name', 'id');

        $branches = Branch::query()->select('id', 'name')->get();
        $courses = Course::query()->select('id', 'course_name')->get();
        $teachers = Teacher::query()->select('id', 'teacher_name')->orderBy('teacher_name')->get();

        return view('dos.teacher-dailies.index', compact(
            'dailies',
            'branches',
            'courses',
            'teachers',
            'courseNamesById'
        ));
    }

    /**
     * Show details of a specific teacher daily activity
     */
    public function show($id)
    {
        $daily = TeacherDaily::with(['teacher', 'course', 'lesson', 'unit', 'branch'])
            ->findOrFail($id);

        // Get attendance records for this daily activity
        $attendances = Attendance::where('date', $daily->date)
            ->where('course_id', $daily->course_id)
            ->where('lesson_id', $daily->lesson_id)
            ->where('branch_id', $daily->branch_id)
            ->with('student')
            ->get()
            ->sortBy(function($attendance) {
                return $attendance->student->full_name ?? '';
            });

        return view('dos.teacher-dailies.show', compact('daily', 'attendances'));
    }

    /**
     * Approve a teacher daily activity
     */
    public function approve($id)
    {
        try {
            $daily = TeacherDaily::findOrFail($id);

            \Log::info('Approving TeacherDaily - START', [
                'daily_id' => $id,
                'current_approved_status' => $daily->approved,
                'staff_id' => Auth::guard('staff')->user()->id,
                'daily_data' => $daily->toArray(),
            ]);

            $daily->approved = true;
            $daily->approved_by = Auth::guard('staff')->user()->id;
            $daily->approved_at = now();
            
            $saved = $daily->save();

            \Log::info('TeacherDaily save result', [
                'daily_id' => $id,
                'saved' => $saved,
                'new_approved_status' => $daily->approved,
                'approved_at' => $daily->approved_at,
                'approved_by' => $daily->approved_by,
            ]);

            // Create payment record for approved daily
            $approver = Auth::guard('staff')->user();
            $this->paymentService->approvePayment($daily, $approver);

            \Log::info('Payment record created for approved daily', [
                'daily_id' => $id,
                'teacher_id' => $daily->teacher_id,
            ]);

            // Also approve related attendance records
            $attendanceUpdated = Attendance::where('date', $daily->date)
                ->where('course_id', $daily->course_id)
                ->where('lesson_id', $daily->lesson_id)
                ->where('branch_id', $daily->branch_id)
                ->update([
                    'approved' => true,
                    'approved_by' => Auth::guard('staff')->user()->id,
                    'approved_at' => now(),
                ]);

            \Log::info('Attendance records updated', [
                'attendance_count' => $attendanceUpdated,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Daily activity and attendance approved successfully!'
                ]);
            }

            return back()->with('success', 'Daily activity and attendance approved successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to approve TeacherDaily', [
                'daily_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve daily activity: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to approve daily activity: ' . $e->getMessage());
        }
    }

    /**
     * Reject a teacher daily activity
     */
    public function reject(Request $request, $id)
    {
        try {
            $daily = TeacherDaily::findOrFail($id);
            $daily->approved = false;
            $daily->rejection_reason = $request->rejection_reason;
            $daily->rejected_by = Auth::guard('staff')->user()->id;
            $daily->rejected_at = now();
            $daily->save();

            // Also reject related attendance records
            Attendance::where('date', $daily->date)
                ->where('course_id', $daily->course_id)
                ->where('lesson_id', $daily->lesson_id)
                ->where('branch_id', $daily->branch_id)
                ->update([
                    'approved' => false,
                    'rejection_reason' => $request->rejection_reason,
                ]);

            return back()->with('success', 'Daily activity rejected successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject daily activity: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve multiple daily activities
     */
    public function bulkApprove(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return back()->with('error', 'No items selected for approval.');
            }

            $staffId = Auth::guard('staff')->user()->id;
            $now = now();

            TeacherDaily::whereIn('id', $ids)->update([
                'approved' => true,
                'approved_by' => $staffId,
                'approved_at' => $now,
            ]);

            // Create payment records for approved dailies
            $approver = Auth::guard('staff')->user();
            $dailies = TeacherDaily::whereIn('id', $ids)->get();
            foreach ($dailies as $daily) {
                $this->paymentService->approvePayment($daily, $approver);
            }

            // Also approve related attendance records
            foreach ($ids as $id) {
                $daily = TeacherDaily::find($id);
                if ($daily) {
                    Attendance::where('date', $daily->date)
                        ->where('course_id', $daily->course_id)
                        ->where('lesson_id', $daily->lesson_id)
                        ->where('branch_id', $daily->branch_id)
                        ->update([
                            'approved' => true,
                            'approved_by' => $staffId,
                            'approved_at' => $now,
                        ]);
                }
            }

            return back()->with('success', count($ids) . ' daily activities approved successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to bulk approve: ' . $e->getMessage());
        }
    }

    /**
     * Delete a teacher daily activity
     */
    public function destroy($id)
    {
        try {
            $daily = TeacherDaily::findOrFail($id);

            \Log::info('Deleting TeacherDaily', [
                'daily_id' => $id,
                'teacher_id' => $daily->teacher_id,
                'date' => $daily->date,
            ]);

            // Delete related attendance records
            $attendanceDeleted = Attendance::where('date', $daily->date)
                ->where('course_id', $daily->course_id)
                ->where('lesson_id', $daily->lesson_id)
                ->where('branch_id', $daily->branch_id)
                ->delete();

            \Log::info('Attendance records deleted', [
                'attendance_count' => $attendanceDeleted,
            ]);

            // Delete the daily record
            $daily->delete();

            \Log::info('TeacherDaily deleted successfully', [
                'daily_id' => $id,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Daily activity and related attendance deleted successfully!'
                ]);
            }

            return back()->with('success', 'Daily activity and related attendance deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete TeacherDaily', [
                'daily_id' => $id,
                'error' => $e->getMessage(),
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete daily activity: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to delete daily activity: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get total hours studied by each lesson
     */
    public function getLessonHoursApi(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $branchIds = $staff->branches()->pluck('branches.id');

        // Get lesson hours data with lesson status
        $lessonHours = TeacherDaily::where('approved', true)
            ->whereIn('branch_id', $branchIds)
            ->with(['lesson:id,title,status,ended_at', 'course:id,course_name', 'teacher:id,teacher_name'])
            ->selectRaw('lesson_id, course_id, teacher_id, SUM(hours_studied) as total_hours, COUNT(*) as sessions')
            ->groupBy('lesson_id', 'course_id', 'teacher_id')
            ->orderByDesc('total_hours')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lessonHours,
            'last_updated' => now()->toIso8601String()
        ]);
    }
}
