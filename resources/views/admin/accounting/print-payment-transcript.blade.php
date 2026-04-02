@php
    $hdrSchoolName = optional($schoolInfo)->school_name ?: 'SCHOOL NAME';
    $hdrAddress = optional($schoolInfo)->address ?: '';
    $hdrPhone = optional($schoolInfo)->phone ?: '';
    $hdrEmail = optional($schoolInfo)->email ?: '';
    $hdrWebsite = optional($schoolInfo)->website;
    $hdrPrincipal = optional($schoolInfo)->principal_name ?: 'School Manager';
    $hasLogo = $schoolInfo && !empty($schoolInfo->logo_url);
    $hasPrincipalSignature = $schoolInfo && !empty($schoolInfo->principal_signature_url);
    $hasStampSignature = $schoolInfo && !empty($schoolInfo->stamp_signature_url);

    $feeTypeMap = [
        'registration' => 'Registration',
        'school_fee' => 'Tuition (school fee)',
        'practical_fee' => 'Practical',
        'trip_fee' => 'Trip',
        'student_card' => 'Student card',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment transcript — {{ $student->full_name }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 8px;
            max-width: 100%;
        }
        .no-print {
            margin-bottom: 16px;
            padding: 12px;
            background: #f4f6f9;
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .print-header-table td {
            vertical-align: top;
            padding: 3px;
        }
        .print-header-logo { width: 70px; text-align: center; }
        .print-header-logo--right { text-align: right; }
        .school-logo-img {
            max-width: 50px;
            max-height: 50px;
            object-fit: contain;
        }
        .print-logo-placeholder {
            width: 40px;
            height: 40px;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #666;
            margin: 0 auto;
        }
        .hdr-school-name {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0 0 2px;
            letter-spacing: 0.5px;
        }
        .hdr-contact { font-size: 8px; margin: 1px 0; color: #444; }
        .doc-title {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin: 8px 0 6px;
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
            padding: 3px 0;
        }
        .student-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background: #f9f9f9;
        }
        .student-info th {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 9px;
            text-align: left;
            font-weight: 600;
        }
        .student-info td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 9px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            font-size: 9px;
        }
        table.data-table th { 
            background: #e8e8e8; 
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8px;
        }
        table.data-table tbody tr:nth-child(even) {
            background: #fafafa;
        }
        table.data-table .total-row {
            font-weight: 700;
            background: #f0f0f0;
        }
        .num { text-align: right; font-variant-numeric: tabular-nums; font-family: 'Courier New', monospace; }
        .muted { color: #666; font-size: 8px; }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin: 8px 0;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 6px;
            background: #f9f9f9;
        }
        .summary-box-label {
            font-size: 8px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }
        .summary-box-value {
            font-size: 12px;
            font-weight: 700;
            color: #000;
            margin-top: 2px;
            font-family: 'Courier New', monospace;
        }
        .signatures {
            margin-top: 12px;
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            vertical-align: bottom;
            padding: 3px;
            width: 33%;
            text-align: center;
            font-size: 8px;
        }
        .sig-img { max-height: 35px; max-width: 100px; object-fit: contain; margin-bottom: 2px; }
        .sig-line {
            border-top: 1px solid #000;
            margin-top: 20px;
            padding-top: 2px;
            min-height: 20px;
        }
        .hidden { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { 
                padding: 5px; 
                font-size: 9px;
                margin: 0;
                line-height: 1.2;
            }
            html, body { 
                height: auto;
                width: 100%;
            }
            .page-break { page-break-before: always; }
            table { page-break-inside: avoid; }
            tr { page-break-inside: avoid; }
            .signatures { page-break-inside: avoid; }
            
            /* Force to single page */
            * { page-break-inside: avoid; }
            .print-header-table { margin-bottom: 3px; }
            .doc-title { margin: 4px 0 3px; padding: 2px 0; font-size: 10px; }
            .student-info th, .student-info td { padding: 2px 3px; font-size: 8px; }
            table.data-table th, table.data-table td { padding: 2px 3px; font-size: 8px; }
            .summary-grid { gap: 4px; margin: 4px 0; }
            .summary-box { padding: 4px; }
            .summary-box-value { font-size: 11px; }
            .muted { font-size: 7px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button type="button" class="btn btn-primary" onclick="window.print()" style="padding:8px 16px;border-radius:6px;border:none;background:#2563eb;color:#fff;cursor:pointer;">Print</button>
    @if(auth('student')->check())
        <a href="{{ route('student.dashboard') }}" style="margin-left:8px;">Back to dashboard</a>
    @else
        <a href="javascript:history.back()" style="margin-left:8px;">Back</a>
    @endif
    <span class="muted">Use your browser print dialog to save as PDF if needed.</span>
</div>

<table class="print-header-table" cellspacing="0">
    <tr>
        <td class="print-header-logo">
            @if($hasLogo)
                <img src="{{ $schoolInfo->logo_url }}" alt="" class="school-logo-img">
            @else
                <div class="print-logo-placeholder">LOGO</div>
            @endif
        </td>
        <td>
            <div class="hdr-school-name">{{ $hdrSchoolName }}</div>
            @if($hdrAddress)
                <div class="hdr-contact">{{ $hdrAddress }}</div>
            @endif
            <div class="hdr-contact">
                @if($hdrPhone) Tel: {{ $hdrPhone }} @endif
                @if($hdrEmail) &nbsp;|&nbsp; Email: {{ $hdrEmail }} @endif
            </div>
            @if(!empty($hdrWebsite))
                <div class="hdr-contact">Website: {{ $hdrWebsite }}</div>
            @endif
        </td>
        <td class="print-header-logo print-header-logo--right">
            @if($hasLogo)
                <img src="{{ $schoolInfo->logo_url }}" alt="" class="school-logo-img">
            @else
                <div class="print-logo-placeholder">LOGO</div>
            @endif
        </td>
    </tr>
</table>

<div class="doc-title">Student Fee Payment Status</div>

<table class="student-info">
    <tr>
        <th>Student Name</th>
        <td>{{ $student->full_name }}</td>
        <th>Reg. Number</th>
        <td>{{ $student->reg_number ?? '—' }}</td>
    </tr>
    <tr>
        <th>Course / Class</th>
        <td>{{ $student->course->course_name ?? '—' }}</td>
        <th>Academic Year</th>
        <td>{{ $student->academicYear->year ?? '—' }}</td>
    </tr>
</table>

<!-- Summary Payment Status Grid -->
<div class="summary-grid">
    <div class="summary-box">
        <div class="summary-box-label">Total Amount Due</div>
        <div class="summary-box-value">{{ number_format($totalExpected, 0) }} RWF</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Amount Paid</div>
        <div class="summary-box-value" style="color: #28a745;">{{ number_format($totalPaid, 0) }} RWF</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Outstanding Balance</div>
        <div class="summary-box-value" style="color: #dc3545;">{{ number_format($totalOutstanding, 0) }} RWF</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Payment Status</div>
        <div class="summary-box-value">
            @if($totalOutstanding <= 0)
                ✓ FULLY PAID
            @elseif($totalPaid > 0)
                PARTIAL ({{ round(($totalPaid / $totalExpected) * 100, 1) }}%)
            @else
                UNPAID
            @endif
        </div>
    </div>
</div>

<!-- Fee Breakdown Table -->
<h3 style="font-size: 9px; margin: 6px 0 3px; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 2px;">Fee Payment Breakdown</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Fee Type</th>
            <th class="num" style="width: 20%;">Due Amount</th>
            <th class="num" style="width: 20%;">Paid</th>
            <th class="num" style="width: 20%;">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($feeRowsWithTerms as $row)
            <tr>
                <td>{{ $row['label'] }}</td>
                <td class="num">{{ number_format($row['expected'], 0) }}</td>
                <td class="num"><span style="color: #28a745;">{{ number_format($row['paid'], 0) }}</span></td>
                <td class="num"><span style="color: #dc3545;">{{ number_format($row['balance'], 0) }}</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td><strong>TOTAL</strong></td>
            <td class="num"><strong>{{ number_format($totalExpected, 0) }}</strong></td>
            <td class="num"><strong style="color: #28a745;">{{ number_format($totalPaid, 0) }}</strong></td>
            <td class="num"><strong style="color: #dc3545;">{{ number_format($totalOutstanding, 0) }}</strong></td>
        </tr>
    </tbody>
</table>

@if($discountPct > 0)
    <p class="muted" style="margin: 4px 0;">
        <strong>Discount Applied:</strong> {{ rtrim(rtrim(number_format($discountPct, 2), '0'), '.') }}% on tuition, practical & trip fees (≈ {{ number_format($discountAmount, 0) }} RWF saved)
    </p>
@endif


<p class="muted" style="margin: 6px 0 2px; border-top: 1px solid #ddd; padding-top: 4px;">
    <strong>Issued:</strong> {{ $printedAt->format('d M Y \a\t H:i') }}
    @if($totalPaid > 0) 
        | <strong>Last Payment:</strong> 
        @php
            $lastPayment = collect($transactions ?? [])->sortByDesc('paid_at')->first();
        @endphp
        @if($lastPayment)
            {{ \Carbon\Carbon::parse($lastPayment->paid_at)->format('d M Y') }}
        @endif
    @endif
</p>

<table class="signatures">
    <tr>
        <td>
            @if($hasPrincipalSignature)
                <img src="{{ $schoolInfo->principal_signature_url }}" alt="" class="sig-img">
            @endif
            <div class="sig-line">
                School Manager
                <br><strong>{{ $hdrPrincipal }}</strong>
            </div>
        </td>
        <td>
            @if($hasStampSignature)
                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="School stamp" class="sig-img">
            @endif
            <div class="sig-line">Official School Stamp</div>
        </td>
        <td>
            <div class="sig-line">
                Accountant / Bursar
            </div>
        </td>
    </tr>
</table>

<p class="muted" style="margin-top: 6px; text-align: center;">
    This transcript reflects the student payment records as of {{ $printedAt->format('d M Y') }}. For inquiries, contact the school administration office.
</p>

</body>
</html>
