<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\StudentTransaction;
use App\Models\CourseTermFee;
use App\Models\AcademicYear;
use App\Models\Term;
use Carbon\Carbon;

class FeePaymentService
{
    /**
     * Check if a student has paid all required fees for the current academic year
     */
    public static function hasPaidAllFees(Student $student): bool
    {
        $paymentStatus = self::getPaymentStatus($student);
        return $paymentStatus['is_fully_paid'];
    }
    
    /**
     * Get payment status details for a student
     * Checks course_term_fees for required amounts and student_transactions for paid amounts
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
        
        // Get fees from course_term_fees (NOT from student)
        // Fees are tied to the course, not individual students
        if ($student->course_id && $student->academic_year_id) {
            // Query for course term fees - optionally filtered by specific terms
            $feeQuery = CourseTermFee::where('course_id', $student->course_id)
                                     ->where('is_active', true);
            
            // If specific term IDs provided, filter by those terms only
            if (!empty($termIds) && is_array($termIds)) {
                $feeQuery->whereIn('term_id', $termIds);
            } else {
                // Otherwise, get all fees for this academic year
                $feeQuery->whereHas('term', function ($query) use ($student) {
                    $query->where('academic_year_id', $student->academic_year_id);
                });
            }
            
            $courseTermFees = $feeQuery->get();
            
            // Sum all required fees for the selected terms
            $requiredFees = $courseTermFees->sum('total_fees');
            
            // Query for student transactions - optionally filtered by specific terms
            $transactionQuery = StudentTransaction::where('student_id', $student->id)
                                                  ->where('type', 'school_fee')
                                                  ->where('status', 'successful');
            
            // If specific term IDs provided, only get payments for those terms
            if (!empty($termIds) && is_array($termIds)) {
                $transactionQuery->whereIn('term_id', $termIds);
            }
            
            $totalPaid = $transactionQuery->sum('amount');
            
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

        // Get course term fees
        if ($student->course_id && $student->academic_year_id) {
            $feeQuery = CourseTermFee::where('course_id', $student->course_id)
                                     ->where('is_active', true);
            
            if (!empty($termIds) && is_array($termIds)) {
                $feeQuery->whereIn('term_id', $termIds);
            } else {
                $feeQuery->whereHas('term', function ($query) use ($student) {
                    $query->where('academic_year_id', $student->academic_year_id);
                });
            }
            
            $courseTermFees = $feeQuery->with('term')->get();

            foreach ($courseTermFees as $ctf) {
                $result['course_term_fees'][] = [
                    'term' => $ctf->term->name ?? 'Unknown',
                    'amount' => $ctf->total_fees,
                ];
            }

            // Get student transactions
            $transactionQuery = StudentTransaction::where('student_id', $student->id)
                                                  ->where('type', 'school_fee')
                                                  ->orderBy('created_at', 'desc');
            
            if (!empty($termIds) && is_array($termIds)) {
                $transactionQuery->whereIn('term_id', $termIds);
            }
            
            $transactions = $transactionQuery->get();

            foreach ($transactions as $txn) {
                $result['student_transactions'][] = [
                    'date' => $txn->created_at->format('Y-m-d H:i'),
                    'status' => $txn->status,
                    'amount' => $txn->amount,
                    'tx_ref' => $txn->tx_ref ?? 'N/A',
                ];
            }

            // Calculation breakdown
            $feeCalcQuery = CourseTermFee::where('course_id', $student->course_id)
                                         ->where('is_active', true);
            
            if (!empty($termIds) && is_array($termIds)) {
                $feeCalcQuery->whereIn('term_id', $termIds);
            } else {
                $feeCalcQuery->whereHas('term', function ($query) use ($student) {
                    $query->where('academic_year_id', $student->academic_year_id);
                });
            }
            
            $totalRequired = $feeCalcQuery->sum('total_fees');

            $txnCalcQuery = StudentTransaction::where('student_id', $student->id)
                                             ->where('type', 'school_fee')
                                             ->where('status', 'successful');
            
            if (!empty($termIds) && is_array($termIds)) {
                $txnCalcQuery->whereIn('term_id', $termIds);
            }
            
            $totalPaidSuccessful = $txnCalcQuery->sum('amount');

            $result['calculation'] = [
                'total_required' => $totalRequired,
                'total_paid_successful' => $totalPaidSuccessful,
                'outstanding' => max(0, $totalRequired - $totalPaidSuccessful),
                'percentage_paid' => $totalRequired > 0 ? round(($totalPaidSuccessful / $totalRequired) * 100, 2) : 0
            ];
        }

        return $result;
    }
}
