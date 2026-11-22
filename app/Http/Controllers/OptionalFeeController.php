<?php

namespace App\Http\Controllers;

use App\Models\CourseTermBranchFee;
use App\Models\StudentOptionalFeeResponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalFeeController extends Controller
{
    /**
     * Show optional fee management for accountant
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();
        $branchIds = $staff->branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            $branchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        $fees = CourseTermBranchFee::with(['course', 'branch', 'term'])
            ->whereIn('branch_id', $branchIds)
            ->where('is_active', true)
            ->orderBy('term_id')
            ->orderBy('course_id')
            ->get();

        return view('accountant.optional_fees.index', compact('fees'));
    }

    /**
     * Update fee mandatory settings
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'practical_fee' => 'nullable|numeric|min:0',
            'trip_fee' => 'nullable|numeric|min:0',
            'is_practical_mandatory' => 'boolean',
            'is_trip_mandatory' => 'boolean',
            'practical_due_date' => 'nullable|date',
            'trip_due_date' => 'nullable|date',
            'notify_students' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $fee = CourseTermBranchFee::findOrFail($id);
        $fee->update([
            'practical_fee' => $request->practical_fee ?? 0,
            'trip_fee' => $request->trip_fee ?? 0,
            'is_practical_mandatory' => $request->is_practical_mandatory ?? false,
            'is_trip_mandatory' => $request->is_trip_mandatory ?? false,
            'practical_due_date' => $request->practical_due_date,
            'trip_due_date' => $request->trip_due_date,
            'notify_students' => $request->notify_students ?? false,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        return back()->with('success', 'Fee settings updated successfully.');
    }

    /**
     * Show response report
     */
    public function report(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $branchIds = $staff->branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            $branchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        $query = StudentOptionalFeeResponse::with(['student', 'student.course', 'student.branch', 'courseTermBranchFee'])
            ->whereHas('courseTermBranchFee', function($q) use ($branchIds) {
                $q->whereIn('branch_id', $branchIds);
            });

        if ($request->fee_type) {
            $query->where('fee_type', $request->fee_type);
        }

        if ($request->response) {
            $query->where('response', $request->response);
        }

        if ($request->branch_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->course_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $stats = [
            'total' => (clone $query)->count(),
            'accepted' => (clone $query)->where('response', 'accepted')->count(),
            'declined' => (clone $query)->where('response', 'declined')->count(),
            'pending' => (clone $query)->where('response', 'pending')->count(),
        ];

        $responses = $query->latest('responded_at')->paginate(50)->withQueryString();

        $branches = \App\Models\Branch::whereIn('id', $branchIds)->orderBy('name')->get();
        $courses = \App\Models\Course::orderBy('course_name')->get();

        return view('accountant.optional_fees.report', compact('responses', 'branches', 'courses', 'stats'));
    }

    /**
     * Export response report
     */
    public function exportReport(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $branchIds = $staff->branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            $branchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        $query = StudentOptionalFeeResponse::with(['student', 'student.course', 'student.branch', 'courseTermBranchFee'])
            ->whereHas('courseTermBranchFee', function($q) use ($branchIds) {
                $q->whereIn('branch_id', $branchIds);
            });

        if ($request->fee_type) {
            $query->where('fee_type', $request->fee_type);
        }

        if ($request->response) {
            $query->where('response', $request->response);
        }

        if ($request->branch_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->course_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $responses = $query->latest('responded_at')->get();

        $filename = 'optional_fee_responses_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($responses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Reg Number', 'Branch', 'Course', 'Fee Type', 'Amount', 'Response', 'Responded At', 'Notes']);

            foreach ($responses as $response) {
                $amount = $response->fee_type === 'practical' 
                    ? $response->courseTermBranchFee->practical_fee 
                    : $response->courseTermBranchFee->trip_fee;

                fputcsv($file, [
                    $response->student->full_name ?? 'N/A',
                    $response->student->reg_number ?? 'N/A',
                    $response->student->branch->name ?? 'N/A',
                    $response->student->course->course_name ?? 'N/A',
                    ucfirst($response->fee_type),
                    $amount,
                    ucfirst($response->response),
                    $response->responded_at ? $response->responded_at->format('Y-m-d H:i') : 'N/A',
                    $response->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Student respond to optional fee
     */
    public function respond(Request $request)
    {
        $request->validate([
            'course_term_branch_fee_id' => 'required|exists:course_term_branch_fees,id',
            'fee_type' => 'required|in:practical,trip',
            'response' => 'required|in:accepted,declined',
            'notes' => 'nullable|string',
        ]);

        $student = Auth::guard('student')->user();
        $fee = CourseTermBranchFee::findOrFail($request->course_term_branch_fee_id);

        // Check if student belongs to this course and branch
        if ($student->course_id != $fee->course_id || $student->branch_id != $fee->branch_id) {
            return back()->with('error', 'You are not authorized to respond to this fee.');
        }

        // Create or update response
        StudentOptionalFeeResponse::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_term_branch_fee_id' => $fee->id,
                'fee_type' => $request->fee_type,
            ],
            [
                'response' => $request->response,
                'responded_at' => now(),
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Your response has been recorded.');
    }

    /**
     * Get pending optional fees for student
     */
    public function getPendingFees()
    {
        $student = Auth::guard('student')->user();
        $today = now()->toDateString();

        $fee = CourseTermBranchFee::with(['course', 'branch', 'term'])
            ->where('course_id', $student->course_id)
            ->where('branch_id', $student->branch_id)
            ->where('is_active', true)
            ->where('notify_students', true)
            ->whereHas('term', function($q) {
                $q->where('academic_year_id', $student->academic_year_id);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', $today);
            })
            ->where(function($q) {
                $q->where('practical_fee', '>', 0)->where('is_practical_mandatory', false)
                  ->orWhere('trip_fee', '>', 0)->where('is_trip_mandatory', false);
            })
            ->first();

        if (!$fee) {
            return response()->json(['pending' => false]);
        }

        $pendingFees = [];

        if ($fee->practical_fee > 0 && !$fee->is_practical_mandatory) {
            $existingResponse = StudentOptionalFeeResponse::where('student_id', $student->id)
                ->where('course_term_branch_fee_id', $fee->id)
                ->where('fee_type', 'practical')
                ->first();

            if (!$existingResponse || $existingResponse->response === 'pending') {
                $pendingFees[] = [
                    'type' => 'practical',
                    'name' => 'Practical Fee',
                    'amount' => $fee->practical_fee,
                    'due_date' => $fee->practical_due_date ? $fee->practical_due_date->format('d M Y') : null,
                    'current_response' => $existingResponse?->response ?? 'pending',
                ];
            }
        }

        if ($fee->trip_fee > 0 && !$fee->is_trip_mandatory) {
            $existingResponse = StudentOptionalFeeResponse::where('student_id', $student->id)
                ->where('course_term_branch_fee_id', $fee->id)
                ->where('fee_type', 'trip')
                ->first();

            if (!$existingResponse || $existingResponse->response === 'pending') {
                $pendingFees[] = [
                    'type' => 'trip',
                    'name' => 'Trip Fee',
                    'amount' => $fee->trip_fee,
                    'due_date' => $fee->trip_due_date ? $fee->trip_due_date->format('d M Y') : null,
                    'current_response' => $existingResponse?->response ?? 'pending',
                ];
            }
        }

        return response()->json([
            'pending' => !empty($pendingFees),
            'fees' => $pendingFees,
            'fee_id' => $fee->id,
        ]);
    }
}
