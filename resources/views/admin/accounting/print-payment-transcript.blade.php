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
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            color: #111;
            line-height: 1;
        }
        .no-print {
            margin-bottom: 12px;
            padding: 10px;
            background: #f4f6f9;
            border-radius: 4px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .print-header-table td {
            vertical-align: top;
            padding: 1px 2px;
        }
        .print-header-logo { width: 50px; text-align: center; }
        .print-header-logo--right { text-align: right; }
        .school-logo-img {
            max-width: 35px;
            max-height: 35px;
            object-fit: contain;
        }
        .print-logo-placeholder {
            width: 30px;
            height: 30px;
            display: none;
        }
        .hdr-school-name {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .hdr-contact { font-size: 7px; margin: 0px; color: #444; }
        .doc-title {
            text-align: center;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 2px 0 3px;
            border-bottom: 1.5px solid #000;
            border-top: 1px solid #000;
            padding: 1px 0;
        }
        .student-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
            background: #fafafa;
        }
        .student-info th {
            background: #f0f0f0;
            border: 0.5px solid #999;
            padding: 1px 3px;
            font-size: 7px;
            text-align: left;
            font-weight: 600;
        }
        .student-info td {
            border: 0.5px solid #999;
            padding: 1px 3px;
            font-size: 7px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 2px 3px;
            text-align: left;
            font-size: 7px;
            line-height: 1.1;
        }
        table.data-table th { 
            background: #ddd; 
            font-weight: 700;
            text-transform: uppercase;
            font-size: 6px;
        }
        table.data-table tbody tr:nth-child(even) {
            background: #fbfbfb;
        }
        table.data-table .total-row {
            font-weight: 700;
            background: #e8e8e8;
        }
        .num { text-align: right; font-family: 'Courier New', monospace; }
        .muted { color: #666; font-size: 6px; }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 2px;
            margin: 2px 0;
        }
        .summary-box {
            border: 1px solid #ccc;
            padding: 2px 3px;
            background: #f9f9f9;
        }
        .summary-box-label {
            font-size: 6px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin: 0;
        }
        .summary-box-value {
            font-size: 8px;
            font-weight: 700;
            color: #000;
            margin: 1px 0 0;
            font-family: 'Courier New', monospace;
        }
        h3 {
            font-size: 7px;
            margin: 2px 0 1px;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 0.5px solid #ccc;
            padding-bottom: 1px;
        }
        .signatures {
            margin-top: 4px;
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            vertical-align: top;
            padding: 2px 1px;
            width: 33%;
            text-align: center;
            font-size: 6px;
        }
        .sig-img { max-height: 20px; max-width: 70px; object-fit: contain; margin-bottom: 1px; }
        .sig-line {
            border-top: 0.5px solid #000;
            margin-top: 12px;
            padding-top: 1px;
            min-height: 12px;
            font-weight: 600;
        }
        .footer-text {
            margin-top: 2px;
            text-align: center;
            font-size: 6px;
        }
        @media print {
            .no-print { display: none !important; }
            html, body { 
                margin: 0;
                padding: 0;
                height: 100%;
            }
            body { 
                padding: 3px; 
                font-size: 8px;
                line-height: 1;
                page-break-after: auto;
            }
            * { page-break-inside: avoid !important; }
            table { page-break-inside: avoid !important; }
            .print-header-table { margin-bottom: 1px; }
            .doc-title { margin: 1px 0 2px; padding: 0px; font-size: 8px; }
            .student-info th, .student-info td { padding: 1px 2px; font-size: 6px; }
            table.data-table th, table.data-table td { padding: 1px 2px; font-size: 6px; }
            .summary-grid { gap: 1px; margin: 2px 0; }
            .summary-box { padding: 2px; }
            .summary-box-value { font-size: 7px; }
            .muted { font-size: 5px; }
            .signatures { margin-top: 2px; }
            .signatures td { padding: 1px; font-size: 5px; }
            .sig-line { margin-top: 8px; min-height: 10px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button type="button" onclick="window.print()" style="padding:6px 12px;border-radius:4px;border:none;background:#0066cc;color:#fff;cursor:pointer;font-size:11px;">Print</button>
    <span class="muted">Single page per student. Print page 1 only.</span>
</div>

<table class="print-header-table" cellspacing="0">
    <tr>
        <td class="print-header-logo">
            @if($hasLogo)
                <img src="{{ $schoolInfo->logo_url }}" alt="" class="school-logo-img">
            @endif
        </td>
        <td style="padding-left: 4px;">
            <div class="hdr-school-name">{{ $hdrSchoolName }}</div>
            @if($hdrAddress)
                <div class="hdr-contact">{{ $hdrAddress }}</div>
            @endif
            <div class="hdr-contact">@if($hdrPhone) Tel: {{ $hdrPhone }} @endif @if($hdrEmail) Email: {{ $hdrEmail }} @endif</div>
        </td>
    </tr>
</table>

<div class="doc-title">STUDENT PAYMENT TRANSCRIPT</div>

<table class="student-info">
    <tr>
        <th>Student Name</th>
        <td>{{ $student->full_name }}</td>
        <th style="width: 12%;">Reg. #</th>
        <td>{{ $student->reg_number ?? '—' }}</td>
    </tr>
    <tr>
        <th>Course</th>
        <td>{{ $student->course->course_name ?? '—' }}</td>
        <th>Academic Year</th>
        <td>{{ $student->academicYear->year ?? '—' }}</td>
    </tr>
</table>

<!-- Summary Payment Status Grid -->
<div class="summary-grid">
    <div class="summary-box">
        <div class="summary-box-label">Amount Due</div>
        <div class="summary-box-value">{{ number_format($totalExpected, 0) }} RWF</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Amount Paid</div>
        <div class="summary-box-value" style="color: #28a745;">{{ number_format($totalPaid, 0) }} RWF</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Outstanding</div>
        <div class="summary-box-value" style="color: #dc3545;">{{ number_format($totalOutstanding, 0) }} RWF</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Status</div>
        <div class="summary-box-value">
            @if($totalOutstanding <= 0)
                ✓ PAID
            @elseif($totalPaid > 0)
                {{ round(($totalPaid / $totalExpected) * 100, 0) }}%
            @else
                UNPAID
            @endif
        </div>
    </div>
</div>

<!-- Fee Breakdown Table -->
<h3>Fee Payment Breakdown</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Fee Type</th>
            <th class="num">Due</th>
            <th class="num">Paid</th>
            <th class="num">Balance</th>
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
    <p class="muted" style="margin: 1px 0;">Discount: {{ rtrim(rtrim(number_format($discountPct, 1), '0'), '.') }}% | Saved: {{ number_format($discountAmount, 0) }} RWF</p>
@endif

<p class="muted">Issued: {{ $printedAt->format('d M Y') }} | System Record</p>

<table class="signatures">
    <tr>
        <td style="border-right: 1px solid #ddd;">
            @if($hasPrincipalSignature)
                <img src="{{ $schoolInfo->principal_signature_url }}" alt="" class="sig-img">
            @endif
            <div class="sig-line">Manager</div>
        </td>
        <td style="border-right: 1px solid #ddd;">
            @if($hasStampSignature)
                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="" class="sig-img">
            @endif
            <div class="sig-line">Stamp</div>
        </td>
        <td>
            <div class="sig-line">Accountant</div>
        </td>
    </tr>
</table>

<div class="footer-text">This payment transcript is valid for accounting and administrative purposes only.</div>

</body>
</html>
