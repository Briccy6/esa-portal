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
            margin-bottom: 20px;
            padding: 12px;
            background: #f0f2f5;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            text-align: center;
            display: none;
        }
        .no-print button {
            margin: 0 5px;
            padding: 8px 16px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 10px;
        }
        .no-print button:hover { background: #1a252f; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }

        /* Page setup */
        @page {
            size: A4;
            margin: 0.5in;
        }
        .page {
            width: 100%;
            max-width: 8.5in;
            margin: 0 auto;
            page-break-after: always;
            padding: 20px;
            background: white;
        }
        .page:last-child { page-break-after: avoid; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .school-logo {
            height: 50px;
            margin-bottom: 8px;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        .school-details {
            font-size: 8px;
            color: #666;
            line-height: 1.2;
        }
        .school-details div { margin: 2px 0; }

        /* Document info */
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin: 12px 0 8px 0;
        }
        .student-info {
            margin-bottom: 12px;
            border: 1px solid #ddd;
            padding: 8px;
            background: #f9f9f9;
        }
        .student-info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 4px 0;
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
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .term-section h3 {
            font-size: 11px;
            font-weight: bold;
            background: #ecf0f1;
            padding: 6px 8px;
            margin: 8px 0 6px 0;
            border-left: 4px solid #2c3e50;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 8px 0;
            background: white;
        }
        table thead tr {
            background: #34495e;
            color: white;
        }
        table th {
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #bdc3c7;
        }
        table td {
            padding: 5px 8px;
            border: 1px solid #ecf0f1;
            font-size: 9px;
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
            border-top: 2px solid #34495e;
        }
        .success { color: #27ae60; font-weight: bold; }
        .danger { color: #c0392b; font-weight: bold; }

        /* Summary */
        .summary {
            margin: 12px 0;
            border: 1px solid #ddd;
            background: #f9f9f9;
            padding: 8px;
        }
        .summary-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin: 4px 0;
            font-size: 9px;
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
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
        }
        .footer-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 20px;
        }
        .footer-cell {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            height: 40px;
            margin: 8px 0;
            position: relative;
        }
        .signature-line img {
            max-height: 35px;
            max-width: 80px;
        }
        .signature-label {
            font-size: 8px;
            font-weight: bold;
            margin-top: 2px;
            color: #333;
        }
        .printed-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
            text-align: center;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .page { margin: 0; padding: 15px; }
            @page { margin: 0.3in; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="downloadPDF()">📥 Download as PDF</button>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="header">
            @if($hasLogo)
                <img src="{{ asset($schoolInfo->logo_url) }}" alt="Logo" class="school-logo">
            @endif
            <div class="school-name">{{ $hdrSchoolName }}</div>
            <div class="school-details">
                @if($hdrAddress)
                    <div>📍 {{ $hdrAddress }}</div>
                @endif
                @if($hdrPhone)
                    <div>📞 {{ $hdrPhone }}
                        @if($hdrEmail) | 📧 {{ $hdrEmail }}@endif
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
                <div><span class="label">Student Name:</span><br><span class="value">{{ $student->full_name }}</span></div>
                <div><span class="label">Registration Number:</span><br><span class="value">{{ $student->reg_number }}</span></div>
            </div>
            <div class="student-info-row">
                <div><span class="label">Course:</span><br><span class="value">{{ $student->course->course_name ?? 'N/A' }}</span></div>
                <div><span class="label">Branch:</span><br><span class="value">{{ $student->branch->name ?? 'N/A' }}</span></div>
            </div>
            @if($student->email)
                <div class="student-info-row">
                    <div><span class="label">Email:</span><br><span class="value">{{ $student->email }}</span></div>
                    <div><span class="label">Phone:</span><br><span class="value">{{ $student->phone ?? 'N/A' }}</span></div>
                </div>
            @endif
        </div>

        <!-- Payment Details by Term -->
        @if($termGroups && count($termGroups) > 0)
            @foreach($termGroups as $termKey => $group)
                <div class="term-section">
                    <h3>📅 {{ $group['term_name'] }}</h3>
                    
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
                                                <span style="color: #27ae60;">✓ Paid</span>
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
                        @if($hasPrincipalSignature)
                            <img src="{{ asset($schoolInfo->principal_signature_url) }}" alt="Signature">
                        @endif
                    </div>
                    <div class="signature-label">{{ $hdrPrincipal }}<br>School Head</div>
                </div>
                <div class="footer-cell">
                    <div class="signature-line">
                        @if($hasStampSignature)
                            <img src="{{ asset($schoolInfo->stamp_signature_url) }}" alt="Stamp" style="max-height: 40px;">
                        @endif
                    </div>
                    <div class="signature-label">Official Stamp</div>
                </div>
                <div class="footer-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Finance Officer<br>Signature</div>
                </div>
            </div>

            <div class="printed-info">
                Printed by: {{ $printedByName ?? 'System' }}<br>
                Date: {{ $printedAt->format('d F Y H:i:s') }}<br>
                This is an official document of {{ $hdrSchoolName }}
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
