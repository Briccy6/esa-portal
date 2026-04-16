<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportCardSetting;
use App\Models\Student;
use App\Services\FeePaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentReportCardController extends Controller
{
    /**
     * Show student's available report cards
     */
    public function index()
    {
        $student = Auth::user();
        
        $reportCards = ReportCard::query()
            ->where('student_id', $student->id)
            ->with(['course', 'academicYear', 'term'])
            ->orderByDesc('created_at')
            ->get();

        return view('reportcard.index', compact('reportCards'));
    }

    /**
     * Show a specific report card with access control
     */
    public function show($id)
    {
        $student = Auth::user();
        $reportCard = ReportCard::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['student', 'course', 'academicYear', 'term', 'subjects.lesson', 'subjects.testMarks.exam'])
            ->firstOrFail();

        // Get report card settings - use correct column name from actual schema
        $settings = ReportCardSetting::where('is_active', true)
            ->where('academic_year', $reportCard->academicYear->year ?? '')
            ->where('term', $reportCard->term->name ?? '')
            ->first();
        
        $now = \Carbon\Carbon::now();
        
        // Get outstanding fees for THIS SPECIFIC TERM ONLY
        // The student should only be blocked if they have outstanding fees for the term of this report card
        $outstanding = $this->getTermOutstandingFees($student, $reportCard->term_id);

        // BUSINESS LOGIC:
        // 1. If student has outstanding fees for THIS term => BLOCKED
        if ($outstanding > 0) {
            return view('reportcard.blocked', compact('outstanding', 'reportCard'));
        }

        // 2. If release date not reached => COUNTDOWN
        if ($settings && $settings->report_card_available_at) {
            $releaseAt = \Carbon\Carbon::parse($settings->report_card_available_at);
            if ($now->lt($releaseAt)) {
                $remaining = $now->diff($releaseAt);
                return view('reportcard.countdown', compact('releaseAt', 'remaining'));
            }
        }

        // 3. All checks passed => ACCESSIBLE
        return view('reportcard.show', compact('reportCard', 'student'));
    }

    /**
     * Download report card as PDF
     */
    public function download($id)
    {
        $student = Auth::user();
        $reportCard = ReportCard::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['student', 'course', 'academicYear', 'term', 'subjects.lesson', 'subjects.testMarks.exam'])
            ->firstOrFail();

        // Get outstanding fees for THIS SPECIFIC TERM ONLY
        $outstanding = $this->getTermOutstandingFees($student, $reportCard->term_id);

        // Block download if fees are outstanding
        if ($outstanding > 0) {
            return redirect()->route('student.report-cards.show', $id)
                ->with('error', 'Cannot download report card. Outstanding fees: RWF ' . number_format($outstanding, 2));
        }

        // Generate PDF using the Barryvdh DomPDF package
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportcard.pdf', compact('reportCard'))
            ->setPaper('a4', 'portrait');

        $filename = 'report-card-' . \Illuminate\Support\Str::slug($reportCard->student->full_name) . '-' . $reportCard->academicYear->year . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get outstanding fees for a student for a SPECIFIC TERM
     * This ensures we only block access if the student has outstanding fees for that term
     * 
     * @param Student $student
     * @param int $termId
     * @return float Outstanding fees for the specific term
     */
    private function getTermOutstandingFees($student, $termId)
    {
        $paymentStatus = FeePaymentService::getPaymentStatus($student, [(int) $termId]);
        return (float) ($paymentStatus['outstanding'] ?? 0);
    }

    /**
     * Get outstanding fees for a student across all terms
     * Adjust this method based on your database schema for tracking fees
     */
    private function getOutstandingFees($student)
    {
        // Use the model method which handles multiple fee tracking methods
        return $student->getOutstandingFees();
    }

    /**
     * List report cards with access status badges
     */
    public function accessStatus()
    {
        $student = Auth::user();
        
        $reportCards = ReportCard::query()
            ->where('student_id', $student->id)
            ->with(['course', 'academicYear', 'term'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($reportCard) use ($student) {
                $settings = ReportCardSetting::where('is_active', true)
                    ->where('academic_year', $reportCard->academicYear->year ?? '')
                    ->where('term', $reportCard->term->name ?? '')
                    ->first();
                    
                $now = \Carbon\Carbon::now();
                $releaseAt = $settings && $settings->report_card_available_at 
                    ? \Carbon\Carbon::parse($settings->report_card_available_at) 
                    : null;
                
                // Get outstanding fees for THIS SPECIFIC TERM ONLY
                $outstanding = $this->getTermOutstandingFees($student, $reportCard->term_id);

                // Determine access status
                if ($outstanding > 0) {
                    $status = 'blocked_fees';
                    $message = "Blocked: Outstanding fees RWF " . number_format($outstanding, 2);
                } elseif ($releaseAt && $now->lt($releaseAt)) {
                    $status = 'countdown';
                    $remaining = $now->diff($releaseAt);
                    $message = "Available in " . $remaining->d . " days";
                } else {
                    $status = 'accessible';
                    $message = 'Available for download';
                }

                return [
                    'reportCard' => $reportCard,
                    'status' => $status,
                    'message' => $message,
                    'outstanding' => $outstanding,
                    'releaseAt' => $releaseAt
                ];
            });

        return view('reportcard.access-status', compact('reportCards'));
    }
}
