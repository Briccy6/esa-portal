@php
    $hdrSchoolName = optional($schoolInfo)->school_name ?: 'SCHOOL NAME';
    $hdrAddress = optional($schoolInfo)->address ?: '';
    $hdrPhone = optional($schoolInfo)->phone ?: '';
    $hdrEmail = optional($schoolInfo)->email ?: '';
    $hdrPrincipal = optional($schoolInfo)->principal_name ?: 'School Manager';
    $hasLogo = $schoolInfo && !empty($schoolInfo->logo_url);
    $hasPrincipalSignature = $schoolInfo && !empty($schoolInfo->principal_signature_url);
    $hasStampSignature = $schoolInfo && !empty($schoolInfo->stamp_signature_url);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Transcript - {{ $student->full_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.1;
            color: #333;
            background: white;
        }
        .no-print {
            margin-bottom: 12px;
            padding: 8px;
            background: #f0f2f5;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            text-align: center;
            display: none;
        }
        .no-print button {
            margin: 0 4px;
            padding: 6px 12px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 9px;
        }
        .no-print button:hover { background: #1a252f; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
        }

        /* Page setup - A4 Portrait Single Page */
        @page {
            size: A4 portrait;
            margin: 0.2in 0.15in;
        }
        .page {
            width: 100%;
            max-width: 21cm;
            margin: 0 auto;
            padding: 0.15in;
            background: white;
            page-break-after: avoid;
        }
        .page:last-child { page-break-after: avoid; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 0.5px solid #2c3e50;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .school-logo {
            height: 20px;
            margin-bottom: 0.5px;
        }
        .school-name {
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0px;
        }
        .school-details {
            font-size: 5px;
            color: #666;
            line-height: 0.9;
        }
        .school-details div { margin: 0px 0; }

        /* Document info */
        .doc-title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #2c3e50;
            margin: 1px 0 0.5px 0;
        }
        .student-info {
            margin-bottom: 2px;
            border: 0.5px solid #ddd;
            padding: 1.5px 2px;
            font-size: 6px;
            background: #f9f9f9;
        }
        .student-info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            margin: 0.5px 0;
        }
        .student-info-row .label {
            font-weight: bold;
            font-size: 6px;
            color: #2c3e50;
        }
        .student-info-row .value {
            font-size: 6px;
            color: #333;
        }

        /* Tables */
        .term-section {
            margin-bottom: 1px;
            page-break-inside: avoid;
        }
        .term-section h3 {
            font-size: 7px;
            font-weight: bold;
            background: #ecf0f1;
            padding: 0.5px 1px;
            margin: 1px 0 0.5px 0;
            border-left: 1px solid #2c3e50;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 1px 0;
            background: white;
            font-size: 6px;
        }
        table thead tr {
            background: #34495e;
            color: white;
        }
        table th {
            padding: 0.5px 1px;
            text-align: left;
            font-weight: bold;
            font-size: 5px;
            border: 0.5px solid #bdc3c7;
        }
        table td {
            padding: 0.5px 1px;
            border: 0.5px solid #ecf0f1;
            font-size: 6px;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .num {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .total-row {
            background: #ecf0f1 !important;
            font-weight: bold;
            border-top: 0.5px solid #34495e;
        }
        .success { color: #27ae60; font-weight: bold; }
        .danger { color: #c0392b; font-weight: bold; }

        /* Summary */
        .summary {
            margin: 1px 0;
            border: 0.5px solid #ddd;
            background: #f9f9f9;
            padding: 1px 2px;
            page-break-inside: avoid;
        }
        .summary-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2px;
            margin: 0.5px 0;
            font-size: 6px;
        }
        .summary-row .label {
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-row .value {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 1px;
            padding-top: 1px;
            border-top: 0.5px solid #ddd;
            font-size: 5px;
            color: #666;
            page-break-inside: avoid;
        }
        .footer-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1px;
            margin-bottom: 1px;
        }
        .footer-cell {
            text-align: center;
        }
        .signature-line {
            border-top: 0.5px solid #333;
            height: 8px;
            margin: 0.5px 0;
            position: relative;
        }
        .signature-line img {
            max-height: 8px;
            max-width: 30px;
        }
        .signature-label {
            font-size: 5px;
            font-weight: bold;
            margin-top: 0px;
            color: #333;
            line-height: 1;
        }
        .printed-info {
            margin-top: 0.5px;
            padding-top: 0.5px;
            border-top: 0.5px solid #ddd;
            font-size: 5px;
            color: #666;
            text-align: center;
            line-height: 1;
        }

        @media print {
            body { margin: 0; padding: 0; background: white; }
            .page { margin: 0; padding: 0.1in; }
            @page { margin: 0.15in 0.1in; size: A4 portrait; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        <button onclick="downloadPDF()"><i class="fas fa-download"></i> Download as PDF</button>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="header">
            @if($hasLogo && $schoolInfo)
                @if($schoolInfo->logo)
                    <img src="{{ $schoolInfo->logo_url }}" alt="Logo" class="school-logo">
                @elseif($schoolInfo->logo_path)
                    <img src="{{ asset($schoolInfo->logo_path) }}" alt="Logo" class="school-logo">
                @endif
            @endif
            <div class="school-name">{{ $hdrSchoolName }}</div>
            <div class="school-details">
                @if($hdrAddress)
                    <div><i class="fas fa-map-marker-alt"></i> {{ $hdrAddress }}</div>
                @endif
                @if($hdrPhone)
                    <div><i class="fas fa-phone"></i> {{ $hdrPhone }}
                        @if($hdrEmail) | <i class="fas fa-envelope"></i> {{ $hdrEmail }}@endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Document Title and Date -->
        <div class="doc-title">PAYMENT TRANSCRIPT</div>
        <div style="text-align: center; font-size: 5px; color: #666; margin-bottom: 1px;">
            {{ now()->format('d M Y') }}
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="student-info-row">
                <div style="display: flex; gap: 2px;">
                    @if($student->passport_photo_url)
                        <div style="flex-shrink: 0;">
                            <img src="{{ $student->passport_photo_url }}" alt="Passport Photo" style="width: 25px; height: 32px; object-fit: cover; border: 0.5px solid #ddd; border-radius: 1px;">
                        </div>
                    @endif
                    <div style="flex: 1;">
                        <div style="margin: 0px 0;"><span class="label" style="font-size: 5px;"><i class="fas fa-user"></i> Name:</span> <span class="value" style="font-size: 5px;">{{ $student->full_name }}</span></div>
                        <div style="margin: 0px 0;"><span class="label" style="font-size: 5px;"><i class="fas fa-id-card"></i> Reg:</span> <span class="value" style="font-size: 5px;">{{ $student->reg_number }}</span></div>
                    </div>
                </div>
            </div>
            <div class="student-info-row">
                <div><span class="label" style="font-size: 5px;"><i class="fas fa-book"></i> Course:</span> <span class="value" style="font-size: 5px;">{{ $student->course->course_name ?? 'N/A' }}</span></div>
                <div><span class="label" style="font-size: 5px;"><i class="fas fa-map-marker-alt"></i> Branch:</span> <span class="value" style="font-size: 5px;">{{ $student->branch->name ?? 'N/A' }}</span></div>
            </div>
        </div>

        <!-- Payment Details by Term -->
        @if($termGroups && count($termGroups) > 0)
            @foreach($termGroups as $termKey => $group)
                <div class="term-section">
                    <h3><i class="fas fa-calendar-alt"></i> {{ $group['term_name'] }}</h3>
                    @if(count($group['transactions']) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Type</th>
                                    <th style="width: 28%;">Amount</th>
                                    <th style="width: 22%;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $termTotal = 0;
                                @endphp
                                @foreach($group['transactions'] as $transaction)
                                    @php
                                        $typeLabel = [
                                            'registration' => 'Registration',
                                            'school_fee' => 'Tuition',
                                            'practical_fee' => 'Practical',
                                            'trip_fee' => 'Trip',
                                            'student_card' => 'Card',
                                        ][$transaction->type] ?? $transaction->type;
                                        
                                        if (in_array($transaction->status, ['successful', 'paid', 'completed'])) {
                                            $termTotal += $transaction->amount;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $typeLabel }}</td>
                                        <td class="num"><span class="success">{{ number_format($transaction->amount, 0) }}</span></td>
                                        <td>{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('d M') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                                @if($termTotal > 0)
                                    <tr class="total-row">
                                        <td colspan="1">Total</td>
                                        <td class="num" colspan="2"><span class="success">{{ number_format($termTotal, 0) }}</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 6px; color: #999; padding: 2px; background: #f5f5f5;">No payments for this term</div>
                    @endif
                </div>
            @endforeach
        @else
            <div style="padding: 3px; background: #fff3cd; border: 0.5px solid #ffc107; border-radius: 2px; font-size: 7px; color: #856404;">
                No payment transactions found.
            </div>
        @endif

        <!-- Summary Section -->
        <div class="summary">
            <div class="summary-row">
                <div class="label">Expected:</div>
                <div class="value">{{ number_format($totalExpected, 0) }}</div>
            </div>
            <div class="summary-row">
                <div class="label">Paid:</div>
                <div class="value" style="color: #27ae60;">{{ number_format($totalPaidAllTypes, 0) }}</div>
            </div>
            @if($discountAmount > 0)
                <div class="summary-row">
                    <div class="label">Discount ({{ $discountPct }}%):</div>
                    <div class="value">{{ number_format($discountAmount, 0) }}</div>
                </div>
            @endif
            <div class="summary-row" style="border-top: 0.5px solid #ddd; padding-top: 1px;">
                <div class="label">Balance:</div>
                <div class="value" style="color: {{ $totalOutstanding > 0 ? '#c0392b' : '#27ae60' }};">
                    {{ number_format($totalOutstanding, 0) }}
                </div>
            </div>
        </div>

        <!-- Footer with Signatures -->
        <div class="footer">
            <div class="footer-row">
                <div class="footer-cell">
                    <div class="signature-line">
                        @if($hasPrincipalSignature && $schoolInfo)
                            @if($schoolInfo->principal_signature_url)
                                <img src="{{ $schoolInfo->principal_signature_url }}" alt="Signature">
                            @endif
                        @endif
                    </div>
                    <div class="signature-label">School Head</div>
                </div>
                <div class="footer-cell">
                    <div class="signature-line">
                        @if($hasStampSignature && $schoolInfo)
                            @if($schoolInfo->stamp_signature_url)
                                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Stamp" style="max-height: 12px;">
                            @endif
                        @endif
                    </div>
                    <div class="signature-label">Official Stamp</div>
                </div>
                <div class="footer-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Finance Officer</div>
                </div>
            </div>

            <div class="printed-info">
                Printed: {{ $printedAt->format('d M Y') }} | {{ $hdrSchoolName }}
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.page');
            const opt = {
                margin: 0.3,
                filename: 'Payment-Transcript-{{ $student->reg_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            
            // Check if html2pdf is available
            if (typeof html2pdf !== 'undefined') {
                html2pdf().set(opt).from(element).save();
            } else {
                alert('PDF download feature requires html2pdf library. Using print instead.');
                window.print();
            }
        }

        // Show print buttons on desktop
        if (!/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
            document.querySelector('.no-print').style.display = 'flex';
        }
    </script>
</body>
</html>
