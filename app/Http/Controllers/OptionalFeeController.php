<?php

namespace App\Http\Controllers;

use App\Models\CourseTermBranchFee;
use App\Models\StudentOptionalFeeResponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $students = Student::query()
            ->whereIn('course_id', $fees->pluck('course_id')->unique())
            ->whereIn('branch_id', $fees->pluck('branch_id')->unique())
            ->whereIn('academic_year_id', $fees->pluck('term.academic_year_id')->filter()->unique())
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'reg_number', 'course_id', 'branch_id', 'academic_year_id']);

        foreach ($fees as $fee) {
            $fee->setAttribute('assignable_students', $students->filter(function ($student) use ($fee) {
                return (int) $student->course_id === (int) $fee->course_id
                    && (int) $student->branch_id === (int) $fee->branch_id
                    && (int) $student->academic_year_id === (int) $fee->term->academic_year_id;
            })->values());
        }

        return view('accountant.optional_fees.index', compact('fees'));
    }

    /**
     * Assign selected optional fees to a student as payable balances.
     */
    public function assign(Request $request, $id)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_types' => 'required|array|min:1',
            'fee_types.*' => 'required|in:practical,trip',
        ]);

        $staff = Auth::guard('staff')->user();
        $branchIds = $staff->branches->pluck('id')->toArray();
        if (empty($branchIds)) {
            $branchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        $fee = CourseTermBranchFee::with('term')
            ->whereIn('branch_id', $branchIds)
            ->where('is_active', true)
            ->findOrFail($id);
        $student = Student::findOrFail($validated['student_id']);

        abort_unless(
            (int) $student->course_id === (int) $fee->course_id
                && (int) $student->branch_id === (int) $fee->branch_id
                && (int) $student->academic_year_id === (int) $fee->term->academic_year_id,
            422,
            'Select a student enrolled in this course, branch, and academic year.'
        );

        $actorName = $staff->name ?? $staff->email ?? 'accountant';
        DB::transaction(function () use ($validated, $student, $fee, $actorName) {
            foreach (array_unique($validated['fee_types']) as $feeType) {
                $amount = $feeType === 'practical' ? $fee->practical_fee : $fee->trip_fee;
                $mandatory = $feeType === 'practical'
                    ? $fee->is_practical_mandatory
                    : $fee->is_trip_mandatory;

                if ((float) $amount <= 0 || $mandatory) {
                    abort(422, 'Only configured optional fees can be assigned.');
                }

                StudentOptionalFeeResponse::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_term_branch_fee_id' => $fee->id,
                        'fee_type' => $feeType,
                    ],
                    [
                        'response' => 'accepted',
                        'responded_at' => now(),
                        'notes' => 'Assigned by accountant: ' . $actorName,
                    ]
                );
            }
        });

        return back()->with('success', "Selected optional fee(s) assigned and added to the student's balance.");
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
            'accepted' => (clone $query)->where('response', 'accepted')
                ->where(function ($q) {
                    $q->whereNull('notes')
                        ->orWhere('notes', 'not like', 'Assigned by accountant:%');
                })
                ->count(),
            'assigned' => (clone $query)->where('notes', 'like', 'Assigned by accountant:%')->count(),
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
                    str_starts_with((string) $response->notes, 'Assigned by accountant:')
                        ? 'Assigned'
                        : ucfirst($response->response),
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
        $fee = CourseTermBranchFee::with('term')->findOrFail($request->course_term_branch_fee_id);

        // Check if student belongs to this course and branch
        if (
            (int) $student->course_id !== (int) $fee->course_id
            || (int) $student->branch_id !== (int) $fee->branch_id
            || (int) $student->academic_year_id !== (int) $fee->term->academic_year_id
        ) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to respond to this fee.'], 403);
        }

        $existingResponse = StudentOptionalFeeResponse::query()
            ->where('student_id', $student->id)
            ->where('course_term_branch_fee_id', $fee->id)
            ->where('fee_type', $request->fee_type)
            ->first();

        if ($existingResponse && str_starts_with((string) $existingResponse->notes, 'Assigned by accountant:')) {
            return response()->json(['success' => false, 'message' => 'This fee was assigned by the accountant and cannot be changed here.'], 409);
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

        return response()->json(['success' => true, 'message' => 'Your response has been recorded.']);
    }

    /**
     * Get pending optional fees for student
     */
    public function getPendingFees()
    {
        $student = Auth::guard('student')->user();
        $today = now()->toDateString();

        $fees = CourseTermBranchFee::with(['course', 'branch', 'term'])
            ->where('course_id', $student->course_id)
            ->where('branch_id', $student->branch_id)
            ->where('is_active', true)
            ->where('notify_students', true)
            ->whereHas('term', function($q) use ($student) {
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
            ->orderBy('term_id')
            ->get();

        if ($fees->isEmpty()) {
            return response()->json(['pending' => false]);
        }

        $pendingFees = [];
        $responses = StudentOptionalFeeResponse::query()
            ->where('student_id', $student->id)
            ->whereIn('course_term_branch_fee_id', $fees->pluck('id'))
            ->get()
            ->keyBy(function ($response) {
                return $response->course_term_branch_fee_id . ':' . $response->fee_type;
            });

        foreach ($fees as $fee) {
            foreach (['practical', 'trip'] as $feeType) {
                $amount = $feeType === 'practical' ? $fee->practical_fee : $fee->trip_fee;
                $mandatory = $feeType === 'practical'
                    ? $fee->is_practical_mandatory
                    : $fee->is_trip_mandatory;
                $dueDate = $feeType === 'practical' ? $fee->practical_due_date : $fee->trip_due_date;
                $response = $responses->get($fee->id . ':' . $feeType);

                if ((float) $amount <= 0 || $mandatory || ($dueDate && $dueDate->lt(today()))) {
                    continue;
                }

                if (!$response || $response->response === 'pending') {
                    $pendingFees[] = [
                        'fee_id' => $fee->id,
                        'type' => $feeType,
                        'name' => $feeType === 'practical' ? 'Practical Fee' : 'Trip Fee',
                        'amount' => $amount,
                        'due_date' => $dueDate ? $dueDate->format('d M Y') : null,
                        'current_response' => $response?->response ?? 'pending',
                    ];
                }
            }
        }

        return response()->json([
            'pending' => !empty($pendingFees),
            'fees' => $pendingFees,
        ]);
    }
}
