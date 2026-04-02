<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\Course;
use App\Models\Term;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    /**
     * Get all transcripts with student, course, and term information
     */
    public function getAllTranscripts(Request $request)
    {
        try {
            $transcripts = Student::with(['course', 'branch', 'transactions.term'])
                ->whereHas('transactions')
                ->get()
                ->map(function ($student) {
                    $totalFees = $this->calculateTotalFees($student);
                    $totalPaid = $student->transactions->sum('amount');
                    $latestTransaction = $student->transactions->sortByDesc('created_at')->first();
                    
                    return [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'reg_number' => $student->reg_number,
                        'course_id' => $student->course_id,
                        'course_name' => $student->course->course_name ?? 'N/A',
                        'branch_name' => $student->branch->name ?? 'N/A',
                        'term_id' => $latestTransaction ? $latestTransaction->term_id : null,
                        'term_name' => $latestTransaction && $latestTransaction->term ? $latestTransaction->term->name : 'N/A',
                        'total_fees' => $totalFees,
                        'total_paid' => $totalPaid,
                        'outstanding' => $totalFees - $totalPaid,
                        'last_payment_date' => $latestTransaction ? $latestTransaction->created_at->format('Y-m-d') : null,
                    ];
                });

            return response()->json($transcripts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load transcripts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get transcripts for a specific course
     */
    public function getCourseTranscripts($courseId, Request $request)
    {
        try {
            $course = Course::findOrFail($courseId);
            
            $transcripts = Student::with(['branch', 'transactions.term'])
                ->where('course_id', $courseId)
                ->whereHas('transactions')
                ->get()
                ->map(function ($student) {
                    $totalFees = $this->calculateTotalFees($student);
                    $totalPaid = $student->transactions->sum('amount');
                    $latestTransaction = $student->transactions->sortByDesc('created_at')->first();
                    
                    return [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'reg_number' => $student->reg_number,
                        'branch_name' => $student->branch->name ?? 'N/A',
                        'term_id' => $latestTransaction ? $latestTransaction->term_id : null,
                        'term_name' => $latestTransaction && $latestTransaction->term ? $latestTransaction->term->name : 'N/A',
                        'total_fees' => $totalFees,
                        'total_paid' => $totalPaid,
                        'outstanding' => $totalFees - $totalPaid,
                        'last_payment_date' => $latestTransaction ? $latestTransaction->created_at->format('Y-m-d') : null,
                    ];
                });

            return response()->json($transcripts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load course transcripts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate total fees for a student including all applicable fees
     */
    private function calculateTotalFees($student)
    {
        $course = $student->course;
        if (!$course) {
            return 0;
        }

        $totalFees = 0;
        
        // Add registration fee
        $totalFees += $course->registration_fee ?? 0;
        
        // Add tuition fee
        $totalFees += $course->tuition_fee ?? 0;
        
        // Add practical fee
        $totalFees += $course->practical_fee ?? 0;
        
        // Add trip fee
        $totalFees += $course->trip_fee ?? 0;
        
        // Add student card fee (fixed 2000 RWF)
        $totalFees += 2000;
        
        // Apply discount if applicable
        $discount = $student->discount ?? 0;
        if ($discount > 0) {
            $discountableFees = ($course->tuition_fee ?? 0) + ($course->practical_fee ?? 0) + ($course->trip_fee ?? 0);
            $discountAmount = $discountableFees * ($discount / 100);
            $totalFees -= $discountAmount;
        }
        
        return max(0, $totalFees);
    }
}
