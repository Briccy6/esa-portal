<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportCardSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Get report card settings
        $settings = ReportCardSetting::first();
        $now = Carbon::now();
        $releaseAt = $settings?->release_at ? Carbon::parse($settings->release_at) : null;

        // Get outstanding fees (adjust field names according to your database schema)
        $outstanding = $this->getOutstandingFees($student);

        // BUSINESS LOGIC:
        // 1. If student has outstanding fees => BLOCKED
        if ($outstanding > 0) {
            return view('reportcard.blocked', compact('outstanding'));
        }

        // 2. If release date not reached => COUNTDOWN
        if ($releaseAt && $now->lt($releaseAt)) {
            $remaining = $now->diff($releaseAt);
            return view('reportcard.countdown', compact('releaseAt', 'remaining'));
        }

        // 3. All checks passed => ACCESSIBLE
        return view('reportcard.show', compact('reportCard', 'student'));
    }

    /**
     * Get outstanding fees for a student
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
                $settings = ReportCardSetting::first();
                $now = Carbon::now();
                $releaseAt = $settings?->release_at ? Carbon::parse($settings->release_at) : null;
                $outstanding = $this->getOutstandingFees($student);

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
