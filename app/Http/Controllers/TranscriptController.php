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
     * Get payment statistics for a course (students who paid all, half, or nothing)
     */
    public function getPaymentStatistics($courseId, Request $request)
    {
        try {
            $course = Course::findOrFail($courseId);
            
            $students = Student::where('course_id', $courseId)
                ->with(['transactions'])
                ->get();
            
            $totalStudents = $students->count();
            $paidAll = 0;
            $paidHalf = 0;
            $paidNothing = 0;
            $totalDueAmount = 0;
            $totalPaidAmount = 0;
            
            foreach ($students as $student) {
                $totalFees = $this->calculateTotalFees($student);
                $totalPaid = $student->transactions->sum('amount');
                $totalDueAmount += ($totalFees - $totalPaid);
                $totalPaidAmount += $totalPaid;
                
                if ($totalPaid == 0) {
                    $paidNothing++;
                } elseif ($totalPaid >= $totalFees) {
                    $paidAll++;
                } else {
                    $paidHalf++;
                }
            }
            
            return response()->json([
                'total_students' => $totalStudents,
                'paid_all' => $paidAll,
                'paid_half' => $paidHalf,
                'paid_nothing' => $paidNothing,
                'total_fees' => $course->tuition_fee + $course->registration_fee + $course->trip_fee + $course->discount_fee,
                'total_due' => $totalDueAmount,
                'total_paid' => $totalPaidAmount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load payment statistics: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get fees breakdown by term for a course
     */
    public function getTermFeesBreakdown($courseId, Request $request)
    {
        try {
            $course = Course::findOrFail($courseId);
            
            // Get all terms with their fees for this course
            $termFees = Term::with(['academicYear', 'courseTermFees' => function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            }])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($term) use ($courseId) {
                // Get CourseTermFee for this course and term
                $courseFee = $term->courseTermFees->where('course_id', $courseId)->first();
                
                $tuitionFee = $courseFee ? $courseFee->tuition_fee ?? 0 : 0;
                $registrationFee = $courseFee ? $courseFee->registration_fee ?? 0 : 0;
                $practicalFee = $courseFee ? $courseFee->practical_fee ?? 0 : 0;
                $tripFee = $courseFee ? $courseFee->trip_fee ?? 0 : 0;
                $studentCardFee = $courseFee ? $courseFee->student_card_fee ?? 0 : 0;
                
                $totalFee = $tuitionFee + $registrationFee + $practicalFee + $tripFee + $studentCardFee;
                
                return [
                    'term_id' => $term->id,
                    'term_name' => $term->name,
                    'academic_year' => $term->academicYear->year ?? 'N/A',
                    'tuition_fee' => $tuitionFee,
                    'registration_fee' => $registrationFee,
                    'practical_fee' => $practicalFee,
                    'trip_fee' => $tripFee,
                    'student_card_fee' => $studentCardFee,
                    'total_fee' => max(0, $totalFee),
                    'start_date' => $term->start_date->format('Y-m-d'),
                    'end_date' => $term->end_date->format('Y-m-d'),
                ];
            });

            return response()->json($termFees);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load term fees: ' . $e->getMessage()], 500);
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
