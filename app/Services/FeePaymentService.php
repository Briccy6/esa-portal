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
        $academicYearName = config('app.academic_year', '2025-2026');
        $currentTermName = config('app.current_term', 'Term 1');
        
        // Find academic year and term by their names
        $academicYear = AcademicYear::where('year', $academicYearName)->first();
        $term = Term::where('name', $currentTermName)->first();
        
        if (!$academicYear || !$term) {
            return false;
        }
        
        // Define academic year date range
        $academicYearStart = '2025-09-01'; // Adjust based on your academic year start
        $academicYearEnd = '2026-08-31';   // Adjust based on your academic year end
        
        // Check if student has an active subscription for the current academic year
        $activeSubscription = StudentSubscription::where('student_id', $student->id)
                                                ->where('payment_status', 'paid')
                                                ->where('is_active', true)
                                                ->where('starts_at', '<=', $academicYearEnd)
                                                ->where('ends_at', '>=', $academicYearStart)
                                                ->first();
        
        if (!$activeSubscription) {
            return false;
        }
        
        // Additional check: Verify payment amount matches required fees
        $requiredFees = SchoolFee::where('academic_year_id', $academicYear->id)
                                ->where('term_id', $term->id)
                                ->sum('amount');
        
        $totalPaid = StudentTransaction::where('student_id', $student->id)
                                      ->where('academic_year', $academicYearName)
                                      ->where('term', $currentTermName)
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
        $academicYearName = config('app.academic_year', '2025-2026');
        $currentTermName = config('app.current_term', 'Term 1');
        
        // Find academic year and term by their names
        $academicYear = AcademicYear::where('year', $academicYearName)->first();
        $term = Term::where('name', $currentTermName)->first();
        
        // Define academic year date range
        $academicYearStart = '2025-09-01'; // Adjust based on your academic year start
        $academicYearEnd = '2026-08-31';   // Adjust based on your academic year end
        
        $subscription = StudentSubscription::where('student_id', $student->id)
                                        ->where('payment_status', 'paid')
                                        ->where('is_active', true)
                                        ->where('starts_at', '<=', $academicYearEnd)
                                        ->where('ends_at', '>=', $academicYearStart)
                                        ->first();
        
        $requiredFees = 0;
        if ($academicYear && $term) {
            $requiredFees = SchoolFee::where('academic_year_id', $academicYear->id)
                                    ->where('term_id', $term->id)
                                    ->sum('amount');
        }
        
        $totalPaid = StudentTransaction::where('student_id', $student->id)
                                      ->where('academic_year', $academicYearName)
                                      ->where('term', $currentTermName)
                                      ->where('type', 'payment')
                                      ->sum('amount');
        
        $outstanding = max(0, $requiredFees - $totalPaid);
        
        return [
            'has_subscription' => $subscription && $subscription->payment_status === 'paid',
            'subscription_terms' => null, // Not applicable in new structure
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
