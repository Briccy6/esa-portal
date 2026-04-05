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
        
        // Get fees from course_term_fees table
        // Fees are tied to the course in course_term_fees, not individual students
        if ($student->course_id && $student->academic_year_id) {
            
            // ========== FEES QUERY (from course_term_fees table) ==========
            // Get all course_term_fees for this student's course
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
            
            // Execute the query to get course_term_fees records
            $courseTermFees = $feeQuery->get();
            
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
                
                // Add all other fees for each term
                $otherFeesTotal += ($fee->tuition_fee ?? 0) 
                                 + ($fee->practical_fee ?? 0) 
                                 + ($fee->trip_fee ?? 0) 
                                 + ($fee->student_card_fee ?? 0);
            }
            
            $requiredFees = $registrationFee + $otherFeesTotal;
            
            // ========== PAYMENT QUERY (from student_transactions table) ==========
            // Get all successful fee payments for this student (all fee types)
            // Includes: registration, school_fee (tuition), practical_fee, trip_fee, student_card
            $transactionQuery = StudentTransaction::where('student_id', $student->id)
                                                  ->whereIn('type', ['registration', 'school_fee', 'practical_fee', 'trip_fee', 'student_card'])
                                                  ->where('status', 'successful');
            
            // If specific term IDs provided, only get payments for those terms
            if (!empty($termIds) && is_array($termIds)) {
                $transactionQuery->whereIn('term_id', $termIds);
            }
            
            // Sum all amount values from student_transactions
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

            // Get student transactions (all fee types)
            // Includes: registration, school_fee (tuition), practical_fee, trip_fee, student_card
            $transactionQuery = StudentTransaction::where('student_id', $student->id)
                                                  ->whereIn('type', ['registration', 'school_fee', 'practical_fee', 'trip_fee', 'student_card'])
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
            
            $courseTermFeesForCalc = $feeCalcQuery->get();
            
            // IMPORTANT: Registration fee should only be counted ONCE per academic year, not per term
            $registrationFeeForCalc = 0;
            $totalRequired = 0;
            
            foreach ($courseTermFeesForCalc as $index => $fee) {
                // Add registration fee only from the first term
                if ($index === 0) {
                    $registrationFeeForCalc = ($fee->registration_fee ?? 0);
                }
                
                // Add all fees for calculation
                $totalRequired += ($fee->registration_fee ?? 0) 
                                + ($fee->tuition_fee ?? 0) 
                                + ($fee->practical_fee ?? 0) 
                                + ($fee->trip_fee ?? 0) 
                                + ($fee->student_card_fee ?? 0);
            }
            
            // Adjust total to only include registration fee once
            $totalRequired = $totalRequired - ($registrationFeeForCalc * max(0, count($courseTermFeesForCalc) - 1));
            $totalRequired = max(0, $totalRequired);

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
