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
     */
    public static function getPaymentStatus(Student $student): array
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
            // Get ALL course term fees for this student's course and academic year
            $courseTermFees = CourseTermFee::where('course_id', $student->course_id)
                                          ->whereHas('term', function ($query) use ($student) {
                                              $query->where('academic_year_id', $student->academic_year_id);
                                          })
                                          ->where('is_active', true)
                                          ->get();
            
            // Sum all required fees for this academic year
            $requiredFees = $courseTermFees->sum('total_fees');
            
            // Get ALL successful school fee payments for this student
            $totalPaid = StudentTransaction::where('student_id', $student->id)
                                          ->where('type', 'school_fee')
                                          ->where('status', 'successful')
                                          ->sum('amount');
            
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
     */
    public static function debugFeeCalculation(Student $student): array
    {
        $result = [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'course_id' => $student->course_id,
            'academic_year_id' => $student->academic_year_id,
            'course_term_fees' => [],
            'student_transactions' => [],
            'calculation' => [],
            'payment_status' => self::getPaymentStatus($student)
        ];

        // Get course term fees
        if ($student->course_id && $student->academic_year_id) {
            $courseTermFees = CourseTermFee::where('course_id', $student->course_id)
                                          ->whereHas('term', function ($query) use ($student) {
                                              $query->where('academic_year_id', $student->academic_year_id);
                                          })
                                          ->where('is_active', true)
                                          ->with('term')
                                          ->get();

            foreach ($courseTermFees as $ctf) {
                $result['course_term_fees'][] = [
                    'term' => $ctf->term->name ?? 'Unknown',
                    'amount' => $ctf->total_fees,
                ];
            }

            // Get student transactions
            $transactions = StudentTransaction::where('student_id', $student->id)
                                             ->where('type', 'school_fee')
                                             ->orderBy('created_at', 'desc')
                                             ->get();

            foreach ($transactions as $txn) {
                $result['student_transactions'][] = [
                    'date' => $txn->created_at->format('Y-m-d H:i'),
                    'status' => $txn->status,
                    'amount' => $txn->amount,
                    'tx_ref' => $txn->tx_ref ?? 'N/A',
                ];
            }

            // Calculation breakdown
            $totalRequired = CourseTermFee::where('course_id', $student->course_id)
                                         ->whereHas('term', function ($query) use ($student) {
                                             $query->where('academic_year_id', $student->academic_year_id);
                                         })
                                         ->where('is_active', true)
                                         ->sum('total_fees');

            $totalPaidSuccessful = StudentTransaction::where('student_id', $student->id)
                                                    ->where('type', 'school_fee')
                                                    ->where('status', 'successful')
                                                    ->sum('amount');

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
