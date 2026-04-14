<?php

namespace App\Http\Controllers\DOS;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\SchoolInformation;
use App\Services\FeePaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportCardReviewController extends Controller
{
    public function index(Request $request)
    {
        $reportCards = ReportCard::with(['student', 'course', 'academicYear', 'term', 'approvedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $reportCards->getCollection()->transform(function (ReportCard $reportCard) {
            $paymentStatus = FeePaymentService::getPaymentStatus($reportCard->student, [(int) $reportCard->term_id]);
            $reportCard->has_outstanding_fees = ((float) ($paymentStatus['outstanding'] ?? 0)) > 0;
            $reportCard->outstanding_amount = (float) ($paymentStatus['outstanding'] ?? 0);

            return $reportCard;
        });

        return view('staff.report-cards.index', compact('reportCards'));
    }

    public function approve(ReportCard $reportCard)
    {
        if ($reportCard->status !== 'approved') {
            $reportCard->status = 'approved';
            $reportCard->approved_at = now();
            $reportCard->save();
        }

        return back()->with('success', 'Report card approved.');
    }

    public function print(ReportCard $reportCard)
    {
        return $this->pdfResponse($reportCard, false);
    }

    public function download(ReportCard $reportCard)
    {
        return $this->pdfResponse($reportCard, true);
    }

    private function pdfResponse(ReportCard $reportCard, bool $download)
    {
        $reportCard->load(['student', 'course', 'academicYear', 'term', 'approvedBy', 'subjects.lesson', 'subjects.testMarks.exam']);
        $paymentStatus = FeePaymentService::getPaymentStatus($reportCard->student, [(int) $reportCard->term_id]);
        if (((float) ($paymentStatus['outstanding'] ?? 0)) > 0) {
            return back()->with('warning', 'Print/Download blocked. Student still has outstanding fees for this term.');
        }

        $schoolInfo = SchoolInformation::getActive();
        [$studentPhotoBase64, $studentPhotoMime] = $this->resolveStudentPhotoData($reportCard->student->passport_photo);

        $pdf = Pdf::loadView('admin.report-cards.print', compact(
            'reportCard',
            'schoolInfo',
            'studentPhotoBase64',
            'studentPhotoMime'
        ))->setPaper('a4', 'portrait');

        $termSlug = Str::slug($reportCard->term->name ?? 'term');
        $fileName = 'report-card-'.Str::slug($reportCard->student->full_name).'-'.$reportCard->academicYear->year.'-'.$termSlug.'.pdf';

        return $download ? $pdf->download($fileName) : $pdf->stream($fileName);
    }

    private function resolveStudentPhotoData(?string $photoPath): array
    {
        if (!$photoPath) {
            return [null, null];
        }

        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            $imageData = @file_get_contents($photoPath);
            if ($imageData === false) {
                return [null, null];
            }
            return [base64_encode($imageData), 'image/jpeg'];
        }

        $localPath = public_path($photoPath);
        if (!file_exists($localPath)) {
            $localPath = storage_path('app/public/' . ltrim($photoPath, '/'));
        }
        if (!file_exists($localPath)) {
            return [null, null];
        }

        $imageData = file_get_contents($localPath);
        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        return [base64_encode($imageData), $mime];
    }
}

