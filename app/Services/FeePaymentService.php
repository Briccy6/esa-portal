<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\StudentTransaction;
use App\Models\CourseTermBranchFee;
use App\Models\CourseTermFee;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Support\OptionalFeeBilling;
use Carbon\Carbon;

class FeePaymentService
{
    /** Matches `student_transactions.status` values that count as paid (same as payment transcript). */
    public static function paidTransactionStatuses(): array
    {
        return ['successful', 'paid', 'completed'];
    }

    /** Registration fee rows may be stored as either type in `student_transactions.type`. */
    public static function registrationTransactionTypes(): array
    {
        return ['registration', 'registration_fee'];
    }

    /**
     * Determine which term IDs should be considered for fee validation for a student.
     *
     * Simplified version: Only use the current active term, not all terms in the academic year.
     * This ensures all students in the same course/branch have the same total fees.
     *
     * @return int[]
     */
    protected static function eligibleTermIdsForStudent(Student $student, ?array $requestedTermIds = null): array
    {
        if (! $student->academic_year_id) {
            return [];
        }

        // Get the current active term (the term we're currently in)
        $currentTerm = Term::query()
            ->where('academic_year_id', $student->academic_year_id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (! $currentTerm) {
            // If no current term, get the most recent term
            $currentTerm = Term::query()
                ->where('academic_year_id', $student->academic_year_id)
                ->orderBy('start_date', 'desc')
                ->first();
        }

        if (! $currentTerm) {
            return [];
        }

        $eligible = [(int) $currentTerm->id];

        if (is_array($requestedTermIds) && $requestedTermIds !== []) {
            $requested = array_values(array_unique(array_map('intval', $requestedTermIds)));
            $eligible = array_values(array_intersect($requested, $eligible));
        }

        return $eligible;
    }

    /**
     * Check if a student has paid all required fees for the current academic year
     */
    /**
     * Check if a student has paid all required fees
     * 
     * @param Student $student
     * @param array|null $termIds - Optional: specific term(s) to check. If null, checks all terms.
     * @return bool
     */
    public static function hasPaidAllFees(Student $student, ?array $termIds = null): bool
    {
        $paymentStatus = self::getPaymentStatus($student, $termIds);
        return $paymentStatus['is_fully_paid'];
    }
    
    /**
     * Get payment status details for a student
     * Checks course_term_fees for required amounts and student_transactions for paid amounts
     * 
     * IMPORTANT: Registration Fee Logic
     * - A student pays registration fee only ONCE per academic year, not per term
     * - If a course has 3 terms, registration_fee appears in all 3 course_term_fees records
     * - But we only count it ONCE in the outstanding calculation
     * - The registration fee is taken from the first term only
     * - If the student has already paid the registration fee, it should not appear again
     * 
     * @param Student $student
     * @param array|null $termIds - If provided, only check fees/payments for these specific terms
     * @return array
     */
    public static function getPaymentStatus(Student $student, ?array $termIds = null): array
    {
        $outstanding = 0;
        $requiredFees = 0;
        $totalPaid = 0;
        
        // First check if student has outstanding_amount field set
        if (isset($student->outstanding_amount) && is_numeric($student->outstanding_amount)) {
            return [
                'has_subscription' => false,
                'subscription_terms' => null,
                'required_fees' => 0,
                'total_paid' => 0,
                'outstanding' => (float) $student->outstanding_amount,
                'is_fully_paid' => $student->outstanding_amount <= 0,
                'payment_percentage' => 0
            ];
        }
        
        // Get fees from course_term_branch_fees table
        // Fees are tied to the course, term, AND branch in course_term_branch_fees
        // Fall back to course_term_fees if branch-specific fees don't exist
        if ($student->course_id && $student->academic_year_id) {
            // Apply "joined late" rule: only consider terms from the student's enrollment term onward.
            $termIds = self::eligibleTermIdsForStudent($student, $termIds);

            // If the requested terms are entirely before enrollment, the student owes nothing for this check.
            if ($termIds === []) {
                return [
                    'has_subscription' => false,
                    'subscription_terms' => null,
                    'required_fees' => 0,
                    'total_paid' => 0,
                    'outstanding' => 0,
                    'is_fully_paid' => true,
                    'payment_percentage' => 100,
                ];
            }

            $yearTermIds = Term::query()
                ->where('academic_year_id', $student->academic_year_id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            
            // Check if branch-specific fees exist, fall back to course fees if not
            $useBranchFees = false;
            if ($student->branch_id) {
                $branchFeeExists = CourseTermBranchFee::where('course_id', $student->course_id)
                    ->where('branch_id', $student->branch_id)
                    ->where('is_active', true)
                    ->exists();
                $useBranchFees = $branchFeeExists;
            }
            
            if ($useBranchFees) {
                \Log::debug('FeePaymentService: Using branch-specific fees', [
                    'student_id' => $student->id,
                    'branch_id' => $student->branch_id,
                    'course_id' => $student->course_id
                ]);
            } else {
                \Log::debug('FeePaymentService: No branch fees found, using course fees', [
                    'student_id' => $student->id,
                    'branch_id' => $student->branch_id,
                    'course_id' => $student->course_id
                ]);
            }
            
            // ========== FEES QUERY (from course_term_branch_fees or course_term_fees table) ==========
            // Student card fee is a one-time fee per academic year (not per term).
            // We take it from the first term's configured fee for the academic year.
            $studentCardFeeOnce = 0;
            // Payments often have term_id NULL — must still count (same as payment transcript).
            $studentCardAlreadyPaidInAcademicYear = StudentTransaction::where('student_id', $student->id)
                ->where('type', 'student_card')
                ->whereIn('status', self::paidTransactionStatuses())
                ->where(function ($q) use ($yearTermIds) {
                    $q->whereNull('term_id')
                        ->orWhereIn('term_id', $yearTermIds);
                })
                ->exists();

            if ($useBranchFees) {
                $firstYearFee = CourseTermBranchFee::query()
                    ->where('course_id', $student->course_id)
                    ->where('branch_id', $student->branch_id)
                    ->where('is_active', true)
                    ->whereHas('term', function ($q) use ($student) {
                        $q->where('academic_year_id', $student->academic_year_id);
                    })
                    ->whereIn('term_id', $termIds)
                    ->with('term:id,start_date')
                    ->get()
                    ->sortBy(fn ($f) => optional($f->term)->start_date ?? '9999-12-31')
                    ->first();
            } else {
                $firstYearFee = \App\Models\CourseTermFee::query()
                    ->where('course_id', $student->course_id)
                    ->where('is_active', true)
                    ->whereHas('term', function ($q) use ($student) {
                        $q->where('academic_year_id', $student->academic_year_id);
                    })
                    ->whereIn('term_id', $termIds)
                    ->with('term:id,start_date')
                    ->get()
                    ->sortBy(fn ($f) => optional($f->term)->start_date ?? '9999-12-31')
                    ->first();
            }

            if ($firstYearFee) {
                $studentCardFeeOnce = (float) ($firstYearFee->student_card_fee ?? 0);
            }

            // Get all course_term_branch_fees or course_term_fees for this student's course (and branch if available)
            if ($useBranchFees) {
                $feeQuery = CourseTermBranchFee::where('course_id', $student->course_id)
                                         ->where('branch_id', $student->branch_id)
                                         ->where('is_active', true)
                                         ->orderBy('term_id');
            } else {
                $feeQuery = \App\Models\CourseTermFee::where('course_id', $student->course_id)
                                         ->where('is_active', true)
                                         ->orderBy('term_id');
            }
            
            // Filter by eligible term IDs only (already includes requested filter if provided)
            if (!empty($termIds) && is_array($termIds)) {
                $feeQuery->whereIn('term_id', $termIds);
                \Log::debug('FeePaymentService: Filtering by term_ids', ['term_ids' => $termIds, 'student_id' => $student->id, 'branch_id' => $student->branch_id]);
            } else {
                // Otherwise, get all fees for this academic year
                $feeQuery->whereHas('term', function ($query) use ($student) {
                    $query->where('academic_year_id', $student->academic_year_id);
                });
                \Log::debug('FeePaymentService: Using all terms for year', ['year_id' => $student->academic_year_id, 'student_id' => $student->id, 'branch_id' => $student->branch_id]);
            }
            
            // Execute the query to get fee records
            $courseTermFees = $feeQuery->get();
            \Log::debug('FeePaymentService: CourseTermFees fetched', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'branch_id' => $student->branch_id,
                'course_id' => $student->course_id,
                'count' => $courseTermFees->count(),
                'term_ids_checked' => !empty($termIds) ? $termIds : 'all',
                'using_branch_fees' => $useBranchFees,
                'fee_records' => $courseTermFees->map(function($fee) {
                    return [
                        'term_id' => $fee->term_id,
                        'term_name' => $fee->term->name ?? 'N/A',
                        'registration_fee' => $fee->registration_fee,
                        'tuition_fee' => $fee->tuition_fee,
                        'practical_fee' => $fee->practical_fee,
                        'trip_fee' => $fee->trip_fee,
                        'student_card_fee' => $fee->student_card_fee,
                    ];
                }),
            ]);
            
            // Sum all required fees from course_term_fees table
            // IMPORTANT: Registration fee should only be counted ONCE per academic year, not per term
            // Registration fee comes from the first term only
            $registrationFee = 0;
            $otherFeesTotal = 0;
            
            foreach ($courseTermFees as $index => $fee) {
                // Add registration fee only from the first term
                if ($index === 0) {
                    $registrationFee = ($fee->registration_fee ?? 0);
                }
                
                $optionalAmounts = $fee instanceof CourseTermBranchFee
                    ? OptionalFeeBilling::payableAmounts($student, $fee)
                    : [
                        'practical' => (float) ($fee->practical_fee ?? 0),
                        'trip' => (float) ($fee->trip_fee ?? 0),
                    ];

                // Add all other fees for each term; student-card is handled once per academic year below.
                $otherFeesTotal += (float) ($fee->tuition_fee ?? 0)
                    + $optionalAmounts['practical']
                    + $optionalAmounts['trip'];
            }
            
            // Registration is once per academic year; exclude from required if already paid (any term in year or NULL term_id).
            $registrationAlreadyPaid = StudentTransaction::where('student_id', $student->id)
                ->whereIn('type', self::registrationTransactionTypes())
                ->whereIn('status', self::paidTransactionStatuses())
                ->where(function ($q) use ($yearTermIds) {
                    $q->whereNull('term_id')
                        ->orWhereIn('term_id', $yearTermIds);
                })
                ->exists();

            if ($registrationAlreadyPaid) {
                \Log::debug('FeePaymentService: Registration already paid for academic year, excluding from required fees', [
                    'student_id' => $student->id,
                    'term_ids' => $termIds,
                ]);
                $registrationFee = 0;
            }
            
            $requiredFees = $registrationFee + $otherFeesTotal;
            if (!$studentCardAlreadyPaidInAcademicYear) {
                $requiredFees += $studentCardFeeOnce;
            }
            
            // ========== PAYMENT QUERY (from student_transactions table) ==========
            // Term-scoped fees: tuition / practical / trip — only count payments for the terms being checked.
            $paidStatuses = self::paidTransactionStatuses();
            $termFeeTypes = ['school_fee', 'practical_fee', 'trip_fee'];

            $sumTermScoped = (float) StudentTransaction::query()
                ->where('student_id', $student->id)
                ->whereIn('status', $paidStatuses)
                ->whereIn('type', $termFeeTypes)
                ->when(! empty($termIds), fn ($q) => $q->whereIn('term_id', $termIds))
                ->sum('amount');

            // Year-wide: registration + student card (include term_id NULL so cash/online rows match transcript).
            $sumRegistrationYear = (float) StudentTransaction::query()
                ->where('student_id', $student->id)
                ->whereIn('type', self::registrationTransactionTypes())
                ->whereIn('status', $paidStatuses)
                ->where(function ($q) use ($yearTermIds) {
                    $q->whereNull('term_id')
                        ->orWhereIn('term_id', $yearTermIds);
                })
                ->sum('amount');

            $sumStudentCardYear = (float) StudentTransaction::query()
                ->where('student_id', $student->id)
                ->where('type', 'student_card')
                ->whereIn('status', $paidStatuses)
                ->where(function ($q) use ($yearTermIds) {
                    $q->whereNull('term_id')
                        ->orWhereIn('term_id', $yearTermIds);
                })
                ->sum('amount');

            $totalPaid = $sumTermScoped + $sumRegistrationYear + $sumStudentCardYear;
            
            \Log::debug('FeePaymentService: Payment summary', [
                'student_id' => $student->id,
                'required_fees' => $requiredFees,
                'total_paid' => $totalPaid,
                'outstanding' => max(0, $requiredFees - $totalPaid),
                'term_ids' => !empty($termIds) ? $termIds : 'all',
            ]);
            
            // Calculate outstanding balance
            $outstanding = max(0, $requiredFees - $totalPaid);
        }
        
        return [
            'has_subscription' => false,
            'subscription_terms' => null,
            'required_fees' => $requiredFees,
            'total_paid' => $totalPaid,
            'outstanding' => $outstanding,
            'is_fully_paid' => $outstanding <= 0,
            'payment_percentage' => $requiredFees > 0 ? ($totalPaid / $requiredFees) * 100 : 100
        ];
    }
    
    /**
     * Get students who have paid all fees (for report card eligibility)
     */
    public static function getEligibleStudents(): \Illuminate\Support\Collection
    {
        $students = Student::all();
        
        return $students->filter(function ($student) {
            return self::hasPaidAllFees($student);
        });
    }

    /**
     * Debug method to verify fee calculations for a student
     * Returns detailed breakdown of all fees and payments
     * 
     * @param Student $student
     * @param array|null $termIds - If provided, only check fees/payments for these specific terms
     * @return array
     */
    public static function debugFeeCalculation(Student $student, ?array $termIds = null): array
    {
        $termIds = self::eligibleTermIdsForStudent($student, $termIds);

        $result = [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'course_id' => $student->course_id,
            'academic_year_id' => $student->academic_year_id,
            'filtered_by_terms' => !empty($termIds),
            'term_ids' => $termIds,
            'course_term_fees' => [],
            'student_transactions' => [],
            'calculation' => [],
            'payment_status' => self::getPaymentStatus($student, $termIds)
        ];

        // Get course term branch fees or course term fees
        if ($student->course_id && $student->academic_year_id) {
            // Check if branch-specific fees exist
            $useBranchFees = false;
            if ($student->branch_id) {
                $branchFeeExists = CourseTermBranchFee::where('course_id', $student->course_id)
                    ->where('branch_id', $student->branch_id)
                    ->where('is_active', true)
                    ->exists();
                $useBranchFees = $branchFeeExists;
            }
            
            if ($useBranchFees) {
                $feeQuery = CourseTermBranchFee::where('course_id', $student->course_id)
                                         ->where('branch_id', $student->branch_id)
                                         ->where('is_active', true);
            } else {
                $feeQuery = CourseTermFee::where('course_id', $student->course_id)
                                         ->where('is_active', true);
            }
            
            if (!empty($termIds) && is_array($termIds)) {
                $feeQuery->whereIn('term_id', $termIds);
            } else {
                $feeQuery->whereHas('term', function ($query) use ($student) {
                    $query->where('academic_year_id', $student->academic_year_id);
                });
            }
            
            $courseTermFees = $feeQuery->with('term')->get();

            foreach ($courseTermFees as $ctf) {
                // Calculate total fees for this course/term
                $termTotal = ($ctf->registration_fee ?? 0) 
                           + ($ctf->tuition_fee ?? 0) 
                           + ($ctf->practical_fee ?? 0) 
                           + ($ctf->trip_fee ?? 0) 
                           + ($ctf->student_card_fee ?? 0);
                
                $result['course_term_fees'][] = [
                    'term' => $ctf->term->name ?? 'Unknown',
                    'registration_fee' => $ctf->registration_fee ?? 0,
                    'tuition_fee' => $ctf->tuition_fee ?? 0,
                    'practical_fee' => $ctf->practical_fee ?? 0,
                    'trip_fee' => $ctf->trip_fee ?? 0,
                    'student_card_fee' => $ctf->student_card_fee ?? 0,
                    'total' => $termTotal,
                ];
            }

            // Fee-related transactions (same types as live calculation; do not hide NULL term_id rows)
            $feeTypesForList = array_merge(
                self::registrationTransactionTypes(),
                ['school_fee', 'practical_fee', 'trip_fee', 'student_card']
            );
            $transactions = StudentTransaction::where('student_id', $student->id)
                ->whereIn('type', $feeTypesForList)
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();

            foreach ($transactions as $txn) {
                $result['student_transactions'][] = [
                    'date' => $txn->created_at->format('Y-m-d H:i'),
                    'type' => $txn->type,
                    'term_id' => $txn->term_id,
                    'status' => $txn->status,
                    'amount' => $txn->amount,
                    'tx_ref' => $txn->tx_ref ?? 'N/A',
                ];
            }

            $ps = $result['payment_status'];
            $result['calculation'] = [
                'total_required' => $ps['required_fees'],
                'total_paid' => $ps['total_paid'],
                'outstanding' => $ps['outstanding'],
                'percentage_paid' => round((float) $ps['payment_percentage'], 2),
            ];
        }

        return $result;
    }
}
