<?php

use App\Models\ToWhomLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class);

test('verification-ready document PDF fits on one A4 page', function () {
    $letter = new ToWhomLetter([
        'reference_number' => 'ESA-LET-2026-001',
        'letter_type' => ToWhomLetter::TYPE_EMPLOYMENT_CONFIRMATION,
        'recipient_type' => 'employee',
        'recipient_name' => 'Asha Example',
        'recipient_role' => 'IT',
        'recipient_title' => 'Human Resources Manager',
        'content' => Str::limit(str_repeat('This official document confirms the employee position at the academy. ', 18), 650),
        'purpose' => Str::limit(str_repeat('Employment verification request. ', 12), 160),
    ]);
    $letter->letter_date = now();

    $schoolInfo = null;
    $verificationUrl = 'https://example.test/to-whom/verify/'.Str::random(64);
    $qrCode = QrCode::format('svg')->size(88)->margin(0)->generate($verificationUrl);
    $pdf = Pdf::loadView('to-whom.pdf', compact('letter', 'schoolInfo', 'verificationUrl', 'qrCode'))
        ->setPaper('a4', 'portrait');

    $pdf->output();

    expect($pdf->getDomPDF()->getCanvas()->get_page_count())->toBe(1);
});
