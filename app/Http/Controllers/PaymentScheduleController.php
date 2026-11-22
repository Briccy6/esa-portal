<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\CourseTermFee;
use App\Models\Term;
use App\Support\OptionalFeeBilling;
use Illuminate\Http\Request;

class PaymentScheduleController extends Controller
{
    public function index()
    {
        $currentTerm = Term::where('start_date', '<=', now())
                           ->where('end_date', '>=', now())
                           ->first();
        
        $allTerms = Term::with('academicYear')
                        ->orderBy('start_date', 'desc')
                        ->get();
        
        $courses = Course::with(['activeTermFees.term', 'branches'])
                        ->whereHas('activeTermFees')
                        ->get();
        
        $paymentSchedule = [];
        
        foreach ($courses as $course) {
            $termFees = $course->activeTermFees()->with('term')->get();
            
            foreach ($termFees as $termFee) {
                $students = Student::where('course_id', $course->id)
                                  ->with(['transactions' => function($query) use ($termFee) {
                                      $query->where('term_id', $termFee->term_id);
                                  }])
                                  ->get();
                
                $paymentSchedule[] = [
                    'course' => $course,
                    'term' => $termFee->term,
                    'term_fee' => $termFee,
                    'students' => $students,
                    'total_students' => $students->count(),
                    'paid_students' => $students->filter(function($student) use ($termFee) {
                        return $student->transactions->where('term_id', $termFee->term_id)
                                                  ->where('status', 'successful')
                                                  ->sum('amount') > 0;
                    })->count(),
                    'total_expected' => $students->count() * $termFee->total_fees,
                    'total_collected' => $students->sum(function($student) use ($termFee) {
                        return $student->transactions->where('term_id', $termFee->term_id)
                                                  ->where('status', 'successful')
                                                  ->sum('amount');
                    }),
                ];
            }
        }
        
        // Sort by term start date
        usort($paymentSchedule, function($a, $b) {
            return $a['term']->start_date <=> $b['term']->start_date;
        });
        
        return view('accountant.payment-schedule', compact(
            'paymentSchedule',
            'currentTerm',
            'allTerms',
            'courses'
        ));
    }
    
    public function courseDetails($courseId)
    {
        $course = Course::with(['activeTermFees.term', 'branches', 'students'])
                       ->findOrFail($courseId);
        
        $termDetails = [];
        
        foreach ($course->activeTermFees as $termFee) {
            $students = Student::where('course_id', $course->id)
                              ->with(['transactions' => function($query) use ($termFee) {
                                  $query->where('term_id', $termFee->term_id);
                              }])
                              ->get();
            
            $studentPayments = [];
            
            foreach ($students as $student) {
                $paidAmount = $student->transactions->where('term_id', $termFee->term_id)
                                                  ->where('status', 'successful')
                                                  ->sum('amount');
                
                $requiredFees = [
                    'registration' => $termFee->registration_fee,
                    'tuition' => $termFee->tuition_fee,
                    'practical' => $termFee->practical_fee,
                    'trip' => $termFee->trip_fee,
                    'student_card' => $termFee->student_card_fee,
                ];
                
                $paidBreakdown = [];
                foreach ($requiredFees as $type => $amount) {
                    $paidBreakdown[$type] = $student->transactions
                        ->where('term_id', $termFee->term_id)
                        ->where('status', 'successful')
                        ->where('type', $type . '_fee')
                        ->sum('amount');
                }
                
                $studentPayments[] = [
                    'student' => $student,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $termFee->total_fees - $paidAmount,
                    'required_fees' => $requiredFees,
                    'paid_breakdown' => $paidBreakdown,
                    'payment_status' => $paidAmount >= $termFee->total_fees ? 'paid' : 
                                      ($paidAmount > 0 ? 'partial' : 'unpaid'),
                    'last_payment' => $student->transactions
                        ->where('term_id', $termFee->term_id)
                        ->where('status', 'successful')
                        ->orderBy('paid_at', 'desc')
                        ->first(),
                ];
            }
            
            $termDetails[] = [
                'term' => $termFee->term,
                'term_fee' => $termFee,
                'students' => $studentPayments,
                'total_students' => $students->count(),
                'paid_students' => collect($studentPayments)->filter(function($sp) {
                    return $sp['payment_status'] === 'paid';
                })->count(),
                'partial_students' => collect($studentPayments)->filter(function($sp) {
                    return $sp['payment_status'] === 'partial';
                })->count(),
                'unpaid_students' => collect($studentPayments)->filter(function($sp) {
                    return $sp['payment_status'] === 'unpaid';
                })->count(),
                'total_expected' => $termFee->total_fees * $students->count(),
                'total_collected' => collect($studentPayments)->sum('paid_amount'),
                'total_remaining' => collect($studentPayments)->sum('remaining_amount'),
            ];
        }
        
        return view('accountant.course-payment-details', compact(
            'course',
            'termDetails'
        ));
    }
    
    public function studentDetails($studentId)
    {
        $student = Student::with(['course', 'transactions.term'])
                         ->findOrFail($studentId);

        $paymentHistory = [];

        // Get all terms with transactions for this student
        $termsWithTransactions = $student->transactions
            ->whereIn('status', ['successful', 'paid', 'completed'])
            ->whereNotNull('term_id')
            ->pluck('term_id')
            ->unique()
            ->values();

        $allTerms = \App\Models\Term::with('academicYear')
            ->whereIn('id', $termsWithTransactions)
            ->orderBy('start_date', 'desc')
            ->get();

        foreach ($allTerms as $term) {
            $transactions = $student->transactions
                ->where('term_id', $term->id)
                ->whereIn('status', ['successful', 'paid', 'completed'])
                ->sortByDesc('paid_at')
                ->values();

            $totalPaid = $transactions->sum('amount');

            // Get term fees if available
            $termFee = \App\Models\CourseTermBranchFee::where('course_id', $student->course_id)
                ->where('term_id', $term->id)
                ->where('branch_id', $student->branch_id)
                ->where('is_active', 1)
                ->first();

            $optionalAmounts = $termFee
                ? OptionalFeeBilling::payableAmounts($student, $termFee)
                : ['practical' => 0.0, 'trip' => 0.0];
            $totalRequired = $termFee
                ? ($termFee->registration_fee + $termFee->tuition_fee + $optionalAmounts['practical'] + $optionalAmounts['trip'] + $termFee->student_card_fee)
                : 0;
            $remaining = max(0, $totalRequired - $totalPaid);

            $paymentHistory[] = [
                'term' => $term,
                'term_fee' => $termFee ?: (object)[
                    'registration_fee' => 0,
                    'tuition_fee' => 0,
                    'practical_fee' => 0,
                    'trip_fee' => 0,
                    'student_card_fee' => 0,
                    'total_fees' => 0,
                ],
                'transactions' => $transactions,
                'total_paid' => $totalPaid,
                'total_required' => $totalRequired,
                'remaining' => $remaining,
                'status' => $totalRequired > 0 ? ($totalPaid >= $totalRequired ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid')) : ($totalPaid > 0 ? 'paid' : 'unpaid'),
                'fee_breakdown' => [
                    'registration' => ['required' => $termFee->registration_fee ?? 0, 'paid' => $transactions->where('type', 'registration')->sum('amount')],
                    'tuition' => ['required' => $termFee->tuition_fee ?? 0, 'paid' => $transactions->where('type', 'school_fee')->sum('amount')],
                    'practical' => ['required' => $optionalAmounts['practical'], 'paid' => $transactions->where('type', 'practical_fee')->sum('amount')],
                    'trip' => ['required' => $optionalAmounts['trip'], 'paid' => $transactions->where('type', 'trip_fee')->sum('amount')],
                    'student_card' => ['required' => $termFee->student_card_fee ?? 0, 'paid' => $transactions->where('type', 'student_card')->sum('amount')],
                ],
            ];
        }

        return view('accountant.student-payment-details', compact(
            'student',
            'paymentHistory'
        ));
    }
}
