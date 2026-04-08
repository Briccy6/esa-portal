<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\CourseTermFee;
use App\Models\Term;
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
        
        $courseTermFees = $student->course->activeTermFees()->with(['term.academicYear'])->get();
        
        $paymentHistory = [];

        // Registration + student card are one-time per academic year (not per term).
        // For schedule display, only include them once (in the first term of that academic year),
        // and if already paid in that academic year, exclude them from required totals.
        $termFeesByYear = $courseTermFees
            ->groupBy(fn ($tf) => $tf->term?->academic_year_id ?? 'unknown')
            ->map(fn ($group) => $group->sortBy('term_id')->values());

        foreach ($termFeesByYear as $academicYearId => $yearFees) {
            if ($academicYearId === 'unknown') {
                $yearRegistrationAlreadyPaid = false;
                $yearStudentCardAlreadyPaid = false;
            } else {
                $yearRegistrationAlreadyPaid = StudentTransaction::where('student_id', $student->id)
                    ->where('type', 'registration')
                    ->where('status', 'successful')
                    ->whereHas('term', fn ($q) => $q->where('academic_year_id', $academicYearId))
                    ->exists();

                $yearStudentCardAlreadyPaid = StudentTransaction::where('student_id', $student->id)
                    ->where('type', 'student_card')
                    ->where('status', 'successful')
                    ->whereHas('term', fn ($q) => $q->where('academic_year_id', $academicYearId))
                    ->exists();
            }

            $firstTermIdInYear = $yearFees->first()?->term_id;

            foreach ($yearFees as $termFee) {
                $transactions = $student->transactions
                    ->where('term_id', $termFee->term_id)
                    ->sortByDesc('paid_at')
                    ->values();

                $totalPaid = $transactions->where('status', 'successful')->sum('amount');

                $includeOneTimeFeesThisTerm = ($termFee->term_id === $firstTermIdInYear);

                $requiredRegistration = ($includeOneTimeFeesThisTerm && !$yearRegistrationAlreadyPaid)
                    ? (float) ($termFee->registration_fee ?? 0)
                    : 0.0;

                $requiredStudentCard = ($includeOneTimeFeesThisTerm && !$yearStudentCardAlreadyPaid)
                    ? (float) ($termFee->student_card_fee ?? 0)
                    : 0.0;

                $requiredTuition = (float) ($termFee->tuition_fee ?? 0);
                $requiredPractical = (float) ($termFee->practical_fee ?? 0);
                $requiredTrip = (float) ($termFee->trip_fee ?? 0);

                $totalRequired = $requiredRegistration + $requiredTuition + $requiredPractical + $requiredTrip + $requiredStudentCard;
                $remaining = max(0, $totalRequired - $totalPaid);

                $paymentHistory[] = [
                    'term' => $termFee->term,
                    'term_fee' => $termFee,
                    'transactions' => $transactions,
                    'total_paid' => $totalPaid,
                    'total_required' => $totalRequired,
                    'remaining' => $remaining,
                    'status' => $totalPaid >= $totalRequired ? 'paid' :
                        ($totalPaid > 0 ? 'partial' : 'unpaid'),
                    'fee_breakdown' => [
                        'registration' => ['required' => $requiredRegistration, 'paid' => $transactions->where('type', 'registration')->where('status', 'successful')->sum('amount')],
                        'tuition' => ['required' => $requiredTuition, 'paid' => $transactions->where('type', 'school_fee')->where('status', 'successful')->sum('amount')],
                        'practical' => ['required' => $requiredPractical, 'paid' => $transactions->where('type', 'practical_fee')->where('status', 'successful')->sum('amount')],
                        'trip' => ['required' => $requiredTrip, 'paid' => $transactions->where('type', 'trip_fee')->where('status', 'successful')->sum('amount')],
                        'student_card' => ['required' => $requiredStudentCard, 'paid' => $transactions->where('type', 'student_card')->where('status', 'successful')->sum('amount')],
                    ],
                ];
            }
        }
        
        return view('accountant.student-payment-details', compact(
            'student',
            'paymentHistory'
        ));
    }
}
