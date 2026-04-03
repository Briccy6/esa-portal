<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\StudentTransaction;
use App\Models\SchoolFee;
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
     */
    public static function getPaymentStatus(Student $student): array
    {
        // Simple approach: Check if student has outstanding_amount field
        $outstanding = 0;
        $requiredFees = 0;
        
        // If student has outstanding_amount field, use it directly
        if (isset($student->outstanding_amount) && $student->outstanding_amount > 0) {
            $outstanding = $student->outstanding_amount;
            $requiredFees = $outstanding; // Assume required = outstanding for now
        }
        
        // Otherwise check from school_fees table
        if ($outstanding == 0 && $student->schoolFees()->exists()) {
            $requiredFees = $student->schoolFees()->sum('amount');
            
            // Sum paid amounts from transactions
            $totalPaid = StudentTransaction::where('student_id', $student->id)
                                          ->where('type', 'school_fee')
                                          ->where('status', 'successful')
                                          ->sum('amount');
            
            $outstanding = max(0, $requiredFees - $totalPaid);
        }
        
        return [
            'has_subscription' => false,
            'subscription_terms' => null,
            'required_fees' => $requiredFees,
            'total_paid' => $requiredFees - $outstanding,
            'outstanding' => $outstanding,
            'is_fully_paid' => $outstanding <= 0,
            'payment_percentage' => $requiredFees > 0 ? (($requiredFees - $outstanding) / $requiredFees) * 100 : 100
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
}
