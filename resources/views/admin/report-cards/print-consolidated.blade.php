@php
    $first = $reportCards->first();
    $terms = $assessmentMatrix['terms'];
    $mRows = $assessmentMatrix['rows'];
    $totalsByTerm = $assessmentMatrix['totals_by_term'];
    $positionByTerm = $assessmentMatrix['position_by_term'];
    $averageByTerm = $assessmentMatrix['average_by_term'];
    $termCount = count($terms);
    $hdrSchoolName = optional($schoolInfo)->school_name ?: 'SCHOOL NAME';
    $hdrPhone = optional($schoolInfo)->phone ?: 'School Phone';
    $hdrEmail = optional($schoolInfo)->email ?: 'school@email.com';
    $hdrWebsite = optional($schoolInfo)->website ?: 'www.schoolwebsite.rw';
    $hdrPrincipal = optional($schoolInfo)->principal_name ?: 'School Manager';
    $hasLogo = $schoolInfo && !empty($schoolInfo->logo_url);
    $hasPrincipalSignature = $schoolInfo && !empty($schoolInfo->principal_signature_url);
    $hasStampSignature = $schoolInfo && !empty($schoolInfo->stamp_signature_url);
    $termNames = collect($terms)->pluck('name')->implode(', ');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment — {{ $first->student->full_name }} — all terms</title>
    <style>
        /* A4 Page Setup with Proper Margins */
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }
        
        * {
            box-sizing: border-box;
        }
        
        html {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 6.5px;
            color: #111;
            width: 100%;
            height: 100%;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Centered container - A4 width minus margins = 170mm */
        .print-wrap {
            width: 100%;
            max-width: 170mm;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
            border: 2px solid #000;
            background: white;
            display: block;
        }
        
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #000;
            margin: 0;
            table-layout: fixed;
        }
        
        .print-header-table td {
            vertical-align: middle;
            padding: 4px;
            font-size: 7px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            border-right: 1px solid #000;
        }
        
        .print-header-table td:last-child {
            border-right: none;
        }
        
        .print-header-logo {
            width: 18%;
            text-align: center;
            display: table-cell;
        }
        
        .print-header-logo--right {
            width: 18%;
            display: table-cell;
        }
        
        .print-header-center {
            width: 64%;
            text-align: center;
            display: table-cell;
            padding: 4px 6px !important;
        }
        
        .school-logo-img {
            max-height: 60px;
            max-width: 80px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        
        .print-logo-placeholder {
            width: 60px;
            height: 60px;
            margin: 0 auto;
            background: #ecf0f1;
            border: 1px dashed #999;
            font-size: 6px;
            line-height: 58px;
            text-align: center;
            color: #888;
            flex-shrink: 0;
            display: inline-block;
        }
        
        .hdr-line {
            margin: 1px 0;
            font-size: 6.8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.3;
        }
        
        .hdr-line .lbl {
            font-weight: bold;
        }
        
        .hdr-school-name {
            font-size: 9px;
            font-weight: bold;
            margin: 3px 0 2px 0;
            letter-spacing: 0.3px;
        }
        
        .hdr-contact {
            font-size: 6px;
            line-height: 1.2;
        }
        
        .title-section {
            background: #f0f0f0;
            border: 1px solid #000;
            border-bottom: none;
            border-top: none;
            padding: 3px;
            text-align: center;
            margin: 0;
        }
        
        .title-section h2 {
            font-size: 10px;
            margin: 0;
            font-weight: bold;
            letter-spacing: 0.4px;
        }
        
        .legend-section {
            border: 1px solid #000;
            border-top: none;
            padding: 3px 5px;
            font-size: 5.8px;
            margin: 0;
            word-wrap: break-word;
            line-height: 1.3;
        }
        
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 6px;
            margin: 0;
            border: 1px solid #000;
        }
        
        .matrix-table th,
        .matrix-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .matrix-table .text-left {
            text-align: left;
            padding-left: 3px;
        }
        
        .matrix-table .bg-gray {
            background: #f0f0f0;
            font-weight: bold;
        }
        
        .matrix-table .bg-blue {
            background: #e6f3ff;
        }
        
        .matrix-table .bg-yellow {
            background: #ffffe6;
        }
        
        .matrix-table .bg-green {
            background: #e6ffe6;
        }
        
        .term-head {
            background: #d9e9f7;
            font-weight: bold;
            font-size: 6px;
            border: 1px solid #000;
        }
        
        .sub-head {
            background: #f0f0f0;
            font-size: 5.8px;
            font-weight: 600;
            border: 1px solid #000;
        }
        
        .footer {
            margin: 0;
            padding: 2px 4px;
            font-size: 5.8px;
            text-align: right;
            color: #555;
            border: 1px solid #000;
            border-top: none;
            box-sizing: border-box;
            width: 100%;
        }
        
        .deliberation-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 5.8px;
            margin: 0;
            table-layout: fixed;
            border: 1px solid #000;
        }
        
        .deliberation-table th,
        .deliberation-table td {
            border: 1px solid #000;
            padding: 2px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .annual-row {
            font-weight: bold;
            background: #e6ffe6;
        }
        
        /* Ensure all elements stay within bounds */
        div, section, article, header, footer, main, nav, ul, ol, li, p, span, a, label, td, th {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        img, svg, canvas, video, iframe {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        /* Print-specific adjustments */
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
                height: 100%;
            }
            
            body {
                background: white;
            }
            
            .print-wrap {
                margin: 0 auto;
                max-width: 170mm;
                width: 100%;
                page-break-inside: avoid;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
<div class="print-wrap">
    <table class="print-header-table" cellspacing="0">
        <tr>
            <td class="print-header-logo">
                @if($hasLogo)
                    <img src="{{ $schoolInfo->logo_url }}" alt="" class="school-logo-img">
                @else
                    <div class="print-logo-placeholder">LOGO</div>
                @endif
            </td>
            <td class="print-header-center">
                <div class="hdr-line"><span class="lbl">NAME:</span> {{ $first->student->full_name }}</div>
                <div class="hdr-line"><span class="lbl">REG NO:</span> {{ $first->student->reg_number ?? 'N/A' }}</div>
                <div class="hdr-line"><span class="lbl">CLASS:</span> {{ $first->course->course_name }}</div>
                <div class="hdr-line"><span class="lbl">YEAR:</span> {{ $first->academicYear->year }}</div>
                <div class="hdr-line"><span class="lbl">TERMS:</span> {{ $termNames }}</div>
                <div class="hdr-school-name">{{ $hdrSchoolName }}</div>
                <div class="hdr-contact"><span class="lbl">Phone:</span> {{ $hdrPhone }} &nbsp; <span class="lbl">E-mail:</span> {{ $hdrEmail }}</div>
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

    <div class="title-section">
        <h2>TRAINEE'S ASSESSMENT REPORT (ALL TERMS)</h2>
    </div>
    <div class="legend-section">
        <strong>TEST</strong> = test average · <strong>TOT</strong> = total for the subject in that term · <strong>POSITION</strong> = student rank within class for that term (Rank/Total Students)
    </div>

    <table class="matrix-table">
        <thead>
            <tr class="bg-gray">
                <th colspan="2" class="text-left">Competence / Credits</th>
                @foreach($terms as $t)
                    <th colspan="2" class="term-head">{{ $t['name'] }}</th>
                @endforeach
                @if(count($terms) > 1)
                    <th colspan="2" class="term-head">ANNUAL</th>
                @endif
            </tr>
            <tr class="sub-head">
                <th>Competence</th>
                <th>Cr</th>
                @foreach($terms as $t)
                    <th>TEST</th>
                    <th>TOT</th>
                @endforeach
                @if(count($terms) > 1)
                    <th>TEST</th>
                    <th>TOT</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr class="bg-blue">
                <td colspan="{{ 2 + 2 * count($terms) + (count($terms) > 1 ? 2 : 0) }}" class="text-left font-bold" style="font-weight: bold;">Core competencies — specific</td>
            </tr>
            @forelse($mRows as $row)
                <tr>
                    <td class="text-left">{{ Str::limit($row['competence_title'], 40) }}</td>
                    <td>{{ $row['credits'] }}</td>
                    @php
                        $annualTest = 0;
                        $annualTotal = 0;
                        $termCount = 0;
                    @endphp
                    @foreach($terms as $t)
                        @php $cell = $row['by_term_id'][$t['id']] ?? ['test' => '—', 'total' => '—']; @endphp
                        <td>{{ $cell['test'] }}</td>
                        <td style="font-weight: bold;">{{ $cell['total'] }}</td>
                        @if(is_numeric($cell['test']) && is_numeric($cell['total']))
                            @php
                                $annualTest += (float) $cell['test'];
                                $annualTotal += (float) $cell['total'];
                                $termCount++;
                            @endphp
                        @endif
                    @endforeach
                    @if(count($terms) > 1)
                        <td>{{ $termCount > 0 ? number_format($annualTest / $termCount, 1) : '—' }}</td>
                        <td style="font-weight: bold;">{{ $termCount > 0 ? number_format($annualTotal / $termCount, 1) : '—' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + 2 * count($terms) + (count($terms) > 1 ? 2 : 0) }}" style="padding: 12px; color: #666;">No assessment data</td>
                </tr>
            @endforelse

            @if(count($mRows) > 0)
                <tr class="bg-white">
                    <td colspan="{{ 2 + 2 * count($terms) + (count($terms) > 1 ? 2 : 0) }}"></td>
                </tr>
                <tr class="bg-blue" style="font-weight: bold;">
                    <td colspan="2" class="text-left">TOTAL</td>
                    @php
                        $annualTestTotal = 0;
                        $annualGrandTotal = 0;
                        $termCountForTotal = 0;
                    @endphp
                    @foreach($terms as $t)
                        @php $tot = $totalsByTerm[$t['id']] ?? null; @endphp
                        <td>{{ $tot['test_sum'] ?? '—' }}</td>
                        <td>{{ $tot['total_sum'] ?? '—' }}</td>
                        @if($tot && is_numeric($tot['test_sum']) && is_numeric($tot['total_sum']))
                            @php
                                $annualTestTotal += (float) $tot['test_sum'];
                                $annualGrandTotal += (float) $tot['total_sum'];
                                $termCountForTotal++;
                            @endphp
                        @endif
                    @endforeach
                    @if(count($terms) > 1)
                        <td>{{ $termCountForTotal > 0 ? number_format($annualTestTotal / $termCountForTotal, 1) : '—' }}</td>
                        <td>{{ $termCountForTotal > 0 ? number_format($annualGrandTotal / $termCountForTotal, 1) : '—' }}</td>
                    @endif
                </tr>
                <tr class="bg-yellow" style="font-weight: bold;">
                    <td colspan="2" class="text-left">POSITION (Rank/Total)</td>
                    @foreach($terms as $t)
                        <td colspan="2" class="text-center" style="font-weight: bold; font-size: 7px;">{{ $positionByTerm[$t['id']] ?? '—' }}</td>
                    @endforeach
                    @if(count($terms) > 1)
                        <td colspan="2" class="text-center">—</td>
                    @endif
                </tr>
                <tr class="bg-gray" style="font-weight: bold;">
                    <td colspan="2" class="text-left">Percentage %</td>
                    @php
                        $annualPercentage = 0;
                        $percentTermCount = 0;
                    @endphp
                    @foreach($terms as $t)
                        @php $avg = $averageByTerm[$t['id']] ?? null; @endphp
                        <td colspan="2">{{ $avg !== null ? number_format((float) $avg, 1).'%' : '—' }}</td>
                        @if($avg !== null)
                            @php
                                $annualPercentage += (float) $avg;
                                $percentTermCount++;
                            @endphp
                        @endif
                    @endforeach
                    @if(count($terms) > 1)
                        <td colspan="2">{{ $percentTermCount > 0 ? number_format($annualPercentage / $percentTermCount, 1).'%' : '—' }}</td>
                    @endif
                </tr>
            @endif
        </tbody>
    </table>

    <table class="deliberation-table">
        <thead>
            <tr class="bg-gray">
                <th class="text-left">Deliberation</th>
                <th>Promotion</th>
                <th>2nd Sit</th>
                <th>Re-assess</th>
                <th>Repeat</th>
                <th>Dismiss</th>
                <th style="font-size: 5px;">School Mgr<br>{{ $hdrPrincipal }}<br>SIGN<br>__/__</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center; padding: 2px;">
                    @if($hasPrincipalSignature)
                        <img src="{{ $schoolInfo->principal_signature_url }}" alt="" style="max-height: 18px; max-width: 60px; margin-right: 2px;">
                    @endif
                    @if($hasStampSignature)
                        <img src="{{ $schoolInfo->stamp_signature_url }}" alt="" style="max-height: 20px; max-width: 50px;">
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        @if($schoolInfo)
            {{ $schoolInfo->school_name ?? '' }} — consolidated report (all terms)
        @endif
    </div>
</div>
</body>
</html>
