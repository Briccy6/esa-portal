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
        'registration_fee' => 'Registration',
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
            font-size: 12px;
            color: #111;
            margin: 0;
            padding: 16px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
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
            margin-bottom: 12px;
        }
        .print-header-table td {
            vertical-align: top;
            padding: 6px;
        }
        .print-header-logo { width: 90px; text-align: center; }
        .print-header-logo--right { text-align: right; }
        .school-logo-img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }
        .print-logo-placeholder {
            width: 72px;
            height: 72px;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #666;
            margin: 0 auto;
        }
        .hdr-school-name {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 6px 0 4px;
        }
        .hdr-line { margin: 2px 0; font-size: 11px; }
        .hdr-line .lbl { font-weight: 600; }
        .hdr-contact { font-size: 10px; margin: 1px 0; color: #333; }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 10px 0 14px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th { background: #f0f0f0; font-size: 11px; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .muted { color: #555; font-size: 10px; }
        .signatures {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            vertical-align: bottom;
            padding: 8px;
            width: 33%;
            text-align: center;
            font-size: 10px;
        }
        .sig-img { max-height: 56px; max-width: 160px; object-fit: contain; margin-bottom: 4px; }
        .sig-line {
            border-top: 1px solid #000;
            margin-top: 28px;
            padding-top: 4px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 12px; }
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

<div class="doc-title">Student fee payment transcript</div>

<table class="data-table">
    <tbody>
        <tr>
            <th style="width:22%">Student name</th>
            <td>{{ $student->full_name }}</td>
            <th style="width:18%">Reg. number</th>
            <td>{{ $student->reg_number ?? '—' }}</td>
        </tr>
        <tr>
            <th>Course / class</th>
            <td>{{ $student->course->course_name ?? '—' }}</td>
            <th>Branch</th>
            <td>{{ $student->branch->name ?? '—' }}</td>
        </tr>
        <tr>
            <th>Academic year</th>
            <td>{{ $student->academicYear->year ?? '—' }}</td>
            <th>Issued on</th>
            <td>{{ $printedAt->format('d M Y, H:i') }}</td>
        </tr>
    </tbody>
</table>

<p class="muted" style="margin:0 0 8px;">
    Amounts due are based on the assigned course fees and any discount on tuition, practical and trip fees. Paid amounts in the summary are taken from successful transactions in <strong>student_transactions</strong> by fee type.
    @if($discountPct > 0)
        Current discount: <strong>{{ rtrim(rtrim(number_format($discountPct, 2), '0'), '.') }}%</strong>
        (≈ {{ number_format($discountAmount, 2) }} RWF on tuition, practical &amp; trip).
    @endif
    @if(isset($totalPaidAllTypes) && abs($totalPaidAllTypes - $totalPaid) > 0.01)
        <br>Total receipts (all payment types, e.g. subscriptions): <strong>{{ number_format($totalPaidAllTypes, 2) }} RWF</strong>.
    @endif
</p>

<h2 style="font-size:12px;margin:12px 0 6px;">Summary by fee type</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>Fee</th>
            <th class="num">Amount due (RWF)</th>
            <th class="num">Paid (RWF)</th>
            <th class="num">Balance (RWF)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($feeRows as $row)
            <tr>
                <td>{{ $row['label'] }}</td>
                <td class="num">{{ number_format($row['expected'], 2) }}</td>
                <td class="num">{{ number_format($row['paid'], 2) }}</td>
                <td class="num">{{ number_format($row['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <th>Totals</th>
            <th class="num">{{ number_format($totalExpected, 2) }}</th>
            <th class="num">{{ number_format($totalPaid, 2) }}</th>
            <th class="num">{{ number_format($totalOutstanding, 2) }}</th>
        </tr>
    </tbody>
</table>

<h2 style="font-size:12px;margin:12px 0 6px;">Transaction ledger (including unpaid / pending)</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Type</th>
            <th class="num">Amount (RWF)</th>
            <th>Status</th>
            <th>Reference</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $i => $tx)
            @php
                $meta = is_array($tx->meta) ? $tx->meta : (json_decode($tx->meta, true) ?: []);
                $displayType = $feeTypeMap[$tx->type] ?? ucfirst(str_replace('_', ' ', $tx->type));
                $displayDate = $tx->paid_at
                    ? \Carbon\Carbon::parse($tx->paid_at)->format('Y-m-d H:i')
                    : ($tx->created_at ? $tx->created_at->format('Y-m-d H:i') : '—');
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $displayDate }}</td>
                <td>{{ $displayType }}</td>
                <td class="num">{{ number_format((float) $tx->amount, 2) }}</td>
                <td>
                    @if(in_array($tx->status, ['successful', 'paid', 'completed']))
                        Paid
                    @elseif($tx->status === 'pending')
                        Pending
                    @else
                        {{ ucfirst($tx->status) }}
                    @endif
                </td>
                <td style="font-size:10px;">{{ $tx->tx_ref ?? '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#666;">No transactions recorded.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            @if($hasPrincipalSignature)
                <img src="{{ $schoolInfo->principal_signature_url }}" alt="" class="sig-img">
            @endif
            <div class="sig-line">
                School manager<br>
                <strong>{{ $hdrPrincipal }}</strong>
            </div>
        </td>
        <td>
            @if($hasStampSignature)
                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="School stamp" class="sig-img" style="max-height:72px;">
            @endif
            <div class="sig-line">Official school stamp</div>
        </td>
        <td>
            <div class="sig-line">
                Accountant / Bursar
                @if(!empty($printedByName))
                    <br><strong>{{ $printedByName }}</strong>
                @endif
            </div>
        </td>
    </tr>
</table>

<p class="muted" style="margin-top:14px;">
    This transcript reflects records in the school system as of {{ $printedAt->format('d M Y H:i') }}.
    For queries, contact the school office.
</p>

</body>
</html>
