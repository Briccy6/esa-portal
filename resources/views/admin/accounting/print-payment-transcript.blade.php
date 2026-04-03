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
            font-size: 10px;
            line-height: 1.3;
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
            body { background: white; }
        }

        /* Page setup - A4 Portrait Single Page */
        @page {
            size: A4 portrait;
            margin: 0.4in 0.3in;
        }
        .page {
            width: 100%;
            max-width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            padding: 0.3in;
            background: white;
            page-break-after: avoid;
            display: flex;
            flex-direction: column;
        }
        .page:last-child { page-break-after: avoid; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        .school-logo {
            height: 35px;
            margin-bottom: 4px;
        }
        .school-name {
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
        }
        .school-details {
            font-size: 7px;
            color: #666;
            line-height: 1.1;
        }
        .school-details div { margin: 1px 0; }

        /* Document info */
        .doc-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin: 6px 0 2px 0;
            flex-shrink: 0;
        }
        .student-info {
            margin-bottom: 6px;
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 8px;
            flex-shrink: 0;
            background: #f9f9f9;
        }
        .student-info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin: 2px 0;
        }
        .student-info-row .label {
            font-weight: bold;
            font-size: 9px;
            color: #2c3e50;
        }
        .student-info-row .value {
            font-size: 9px;
            color: #333;
        }

        /* Tables */
        .term-section {
            margin-bottom: 6px;
            page-break-inside: avoid;
        }
        .term-section h3 {
            font-size: 9px;
            font-weight: bold;
            background: #ecf0f1;
            padding: 2px 4px;
            margin: 4px 0 2px 0;
            border-left: 4px solid #2c3e50;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 4px 0;
            background: white;
            font-size: 8px;
        }
        table thead tr {
            background: #34495e;
            color: white;
        }
        table th {
            padding: 3px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 7px;
            border: 1px solid #bdc3c7;
        }
        table td {
            padding: 2px 4px;
            border: 1px solid #ecf0f1;
            font-size: 8px;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        table tbody tr:hover {
            background: #ecf0f1;
        }
        .num {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .total-row {
            background: #ecf0f1 !important;
            font-weight: bold;
            border-top: 1px solid #34495e;
        }
        .success { color: #27ae60; font-weight: bold; }
        .danger { color: #c0392b; font-weight: bold; }

        /* Summary */
        .summary {
            margin: 4px 0;
            border: 1px solid #ddd;
            background: #f9f9f9;
            padding: 4px;
            page-break-inside: avoid;
        }
        .summary-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 8px;
            margin: 2px 0;
            font-size: 8px;
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
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #ddd;
            font-size: 7px;
            color: #666;
            page-break-inside: avoid;
            flex-shrink: 0;
        }
        .footer-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 4px;
            margin-bottom: 4px;
        }
        .footer-cell {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            height: 20px;
            margin: 2px 0;
            position: relative;
        }
        .signature-line img {
            max-height: 20px;
            max-width: 60px;
        }
        .signature-label {
            font-size: 7px;
            font-weight: bold;
            margin-top: 1px;
            color: #333;
        }
        .printed-info {
            margin-top: 2px;
            padding-top: 2px;
            border-top: 1px solid #ddd;
            font-size: 7px;
            color: #666;
            text-align: center;
            line-height: 1.2;
        }

        @media print {
            body { margin: 0; padding: 0; background: white; }
            .page { margin: 0; padding: 0.2in; }
            @page { margin: 0.3in 0.2in; size: A4 portrait; }
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
        <div style="text-align: center; font-size: 8px; color: #666; margin-bottom: 12px;">
            Generated on {{ now()->format('d F Y \a\t H:i') }}
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="student-info-row">
                <div style="display: flex; gap: 12px;">
                    @if($student->passport_photo)
                        <div style="flex-shrink: 0;">
                            <img src="{{ asset($student->passport_photo) }}" alt="Passport Photo" style="width: 60px; height: 75px; object-fit: cover; border: 1px solid #ddd; border-radius: 3px;">
                        </div>
                    @endif
                    <div style="flex: 1;">
                        <div style="margin: 4px 0;"><span class="label"><i class="fas fa-user"></i> Student Name:</span><br><span class="value">{{ $student->full_name }}</span></div>
                        <div style="margin: 4px 0;"><span class="label"><i class="fas fa-id-card"></i> Registration Number:</span><br><span class="value">{{ $student->reg_number }}</span></div>
                    </div>
                </div>
            </div>
            <div class="student-info-row">
                <div><span class="label"><i class="fas fa-book"></i> Course:</span><br><span class="value">{{ $student->course->course_name ?? 'N/A' }}</span></div>
                <div><span class="label"><i class="fas fa-map-marker-alt"></i> Branch:</span><br><span class="value">{{ $student->branch->name ?? 'N/A' }}</span></div>
            </div>
            @if($student->email)
                <div class="student-info-row">
                    <div><span class="label"><i class="fas fa-envelope"></i> Email:</span><br><span class="value">{{ $student->email }}</span></div>
                    <div><span class="label"><i class="fas fa-phone"></i> Phone:</span><br><span class="value">{{ $student->phone ?? 'N/A' }}</span></div>
                </div>
            @endif
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
                                    <th style="width: 40%;">Payment Type</th>
                                    <th style="width: 20%;">Amount</th>
                                    <th style="width: 20%;">Date Paid</th>
                                    <th style="width: 20%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $termTotal = 0;
                                @endphp
                                @foreach($group['transactions'] as $transaction)
                                    @php
                                        $typeLabel = [
                                            'registration' => 'Registration Fee',
                                            'school_fee' => 'Tuition (School Fee)',
                                            'practical_fee' => 'Practical Fee',
                                            'trip_fee' => 'Trip Fee',
                                            'student_card' => 'Student Card',
                                        ][$transaction->type] ?? ucfirst($transaction->type);
                                        
                                        if (in_array($transaction->status, ['successful', 'paid', 'completed'])) {
                                            $termTotal += $transaction->amount;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $typeLabel }}</td>
                                        <td class="num"><span class="success">{{ number_format($transaction->amount, 0) }}</span></td>
                                        <td>{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('d M Y') : 'N/A' }}</td>
                                        <td class="num">
                                            @if(in_array($transaction->status, ['successful', 'paid', 'completed']))
                                                <span style="color: #27ae60;"><i class="fas fa-check-circle"></i> Paid</span>
                                            @else
                                                <span style="color: #e74c3c;">{{ ucfirst($transaction->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if($termTotal > 0)
                                    <tr class="total-row">
                                        <td colspan="2">Total Paid ({{ $group['term_name'] }})</td>
                                        <td class="num" colspan="2"><span class="success">{{ number_format($termTotal, 0) }}</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <p style="font-size: 9px; color: #999; padding: 8px; background: #f5f5f5; border-radius: 3px;">
                            No payments recorded for this term.
                        </p>
                    @endif
                </div>
            @endforeach
        @else
            <div style="padding: 12px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 3px; font-size: 9px; color: #856404;">
                No payment transactions found for this student.
            </div>
        @endif

        <!-- Summary Section -->
        <div class="summary">
            <div class="summary-row">
                <div class="label">Total Expected Amount:</div>
                <div class="value">{{ number_format($totalExpected, 0) }}</div>
            </div>
            <div class="summary-row">
                <div class="label">Total Paid:</div>
                <div class="value" style="color: #27ae60;">{{ number_format($totalPaidAllTypes, 0) }}</div>
            </div>
            @if($discountAmount > 0)
                <div class="summary-row">
                    <div class="label">Discount ({{ $discountPct }}%):</div>
                    <div class="value">{{ number_format($discountAmount, 0) }}</div>
                </div>
            @endif
            <div class="summary-row" style="border-top: 1px solid #ddd; padding-top: 6px;">
                <div class="label">Outstanding Balance:</div>
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
                            @if($schoolInfo->principal_signature_data)
                                <img src="{{ $schoolInfo->principal_signature_url }}" alt="Signature">
                            @elseif($schoolInfo->principal_signature)
                                <img src="{{ asset($schoolInfo->principal_signature) }}" alt="Signature">
                            @endif
                        @endif
                    </div>
                    <div class="signature-label">{{ $hdrPrincipal }}<br><i class="fas fa-user-tie"></i> School Head</div>
                </div>
                <div class="footer-cell">
                    <div class="signature-line">
                        @if($hasStampSignature && $schoolInfo)
                            @if($schoolInfo->stamp_signature_data)
                                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Stamp" style="max-height: 40px;">
                            @elseif($schoolInfo->stamp_signature)
                                <img src="{{ asset($schoolInfo->stamp_signature) }}" alt="Stamp" style="max-height: 40px;">
                            @endif
                        @endif
                    </div>
                    <div class="signature-label"><i class="fas fa-stamp"></i> Official Stamp</div>
                </div>
                <div class="footer-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label"><i class="fas fa-signature"></i> Finance Officer<br>Signature</div>
                </div>
            </div>

            <div class="printed-info">
                <i class="fas fa-user-check"></i> Printed by: {{ $printedByName ?? 'System' }}<br>
                <i class="fas fa-clock"></i> Date: {{ $printedAt->format('d F Y H:i:s') }}<br>
                <i class="fas fa-certificate"></i> This is an official document of {{ $hdrSchoolName }}
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
