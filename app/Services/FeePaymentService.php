<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Subscription;
use App\Models\StudentTransaction;
use App\Models\SchoolFee;
use Carbon\Carbon;

class FeePaymentService
{
    /**
     * Check if a student has paid all required fees for the current academic year
     */
    public static function hasPaidAllFees(Student $student): bool
    {
        $academicYear = config('app.academic_year', '2025-2026');
        $currentTerm = config('app.current_term', 'Term 1');
        
        // Check if student has an active subscription for the current academic year
        $activeSubscription = Subscription::where('student_id', $student->id)
                                        ->where('academic_year', $academicYear)
                                        ->where('status', 'active')
                                        ->first();
        
        if (!$activeSubscription) {
            return false;
        }
        
        // Check if subscription covers the current term
        $subscriptionTerms = explode(',', $activeSubscription->terms);
        if (!in_array($currentTerm, $subscriptionTerms)) {
            return false;
        }
        
        // Additional check: Verify payment amount matches required fees
        $requiredFees = SchoolFee::where('academic_year', $academicYear)
                                ->where('term', $currentTerm)
                                ->sum('amount');
        
        $totalPaid = StudentTransaction::where('student_id', $student->id)
                                      ->where('academic_year', $academicYear)
                                      ->where('term', $currentTerm)
                                      ->where('type', 'payment')
                                      ->sum('amount');
        
        // Allow small variations due to discounts or adjustments
        $tolerance = $requiredFees * 0.05; // 5% tolerance
        
        return $totalPaid >= ($requiredFees - $tolerance);
    }
    
    /**
     * Get payment status details for a student
     */
    public static function getPaymentStatus(Student $student): array
    {
        $academicYear = config('app.academic_year', '2025-2026');
        $currentTerm = config('app.current_term', 'Term 1');
        
        $subscription = Subscription::where('student_id', $student->id)
                                  ->where('academic_year', $academicYear)
                                  ->first();
        
        $requiredFees = SchoolFee::where('academic_year', $academicYear)
                                ->where('term', $currentTerm)
                                ->sum('amount');
        
        $totalPaid = StudentTransaction::where('student_id', $student->id)
                                      ->where('academic_year', $academicYear)
                                      ->where('term', $currentTerm)
                                      ->where('type', 'payment')
                                      ->sum('amount');
        
        $outstanding = max(0, $requiredFees - $totalPaid);
        
        return [
            'has_subscription' => $subscription && $subscription->status === 'active',
            'subscription_terms' => $subscription ? $subscription->terms : null,
            'required_fees' => $requiredFees,
            'total_paid' => $totalPaid,
            'outstanding' => $outstanding,
            'is_fully_paid' => $outstanding <= ($requiredFees * 0.05), // 5% tolerance
            'payment_percentage' => $requiredFees > 0 ? ($totalPaid / $requiredFees) * 100 : 0
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
