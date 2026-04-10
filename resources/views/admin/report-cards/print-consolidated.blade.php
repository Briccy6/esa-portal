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
        // Optimize page layout for fitness and margins
        @page {
            size: A4 portrait;
            margin-top: 5mm;
            margin-right: 5mm;
            margin-bottom: 5mm;
            margin-left: 5mm;
        }
        
        * {
            box-sizing: border-box;
        }
        
        html, body { 
            margin: 0; 
            padding: 5mm; 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 6.5px; 
            color: #111; 
            width: 100%; 
            overflow-x: hidden; 
            box-sizing: border-box;
            height: 100%;
        }
        .print-wrap { width: 100%; max-width: 100%; box-sizing: border-box; padding: 0; margin: 0; overflow: visible; }
        .print-header-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid #000; margin-bottom: 2px; table-layout: fixed; }
        .print-header-table td { vertical-align: middle; padding: 3px 4px; font-size: 7px; word-wrap: break-word; overflow-wrap: break-word; }
        .print-header-logo { width: 18%; text-align: center; border-right: 1px solid #000; }
        .print-header-logo--right { border-left: 1px solid #000; border-right: none; }
        .print-header-center { width: 64%; text-align: center; }
        .school-logo-img { max-height: 50px; max-width: 70px; object-fit: contain; }
        .print-logo-placeholder { width: 50px; height: 50px; margin: 0 auto; background: #ecf0f1; border: 1px dashed #999; font-size: 6px; line-height: 48px; text-align: center; color: #888; }
        .hdr-line { margin: 0 0 1px 0; font-size: 6.5px; word-wrap: break-word; overflow-wrap: break-word; }
        .hdr-line .lbl { font-weight: bold; }
        .hdr-school-name { font-size: 8px; font-weight: bold; margin: 2px 0; }
        .hdr-contact { font-size: 6px; }
        .title-section { background: #f0f0f0; border: 1px solid #000; border-bottom: none; padding: 2px; text-align: center; }
        .title-section h2 { font-size: 9px; margin: 0; }
        .legend-section { border: 1px solid #000; border-top: none; padding: 2px 4px; font-size: 5.5px; margin-bottom: 0; word-wrap: break-word; }
        .matrix-table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 6px; }
        .matrix-table th, .matrix-table td { border: 1px solid #000; padding: 1px 2px; text-align: center; vertical-align: middle; word-wrap: break-word; overflow-wrap: break-word; }
        .matrix-table .text-left { text-align: left; }
        .matrix-table .bg-gray { background: #f0f0f0; font-weight: bold; }
        .matrix-table .bg-blue { background: #e6f3ff; }
        .matrix-table .bg-yellow { background: #ffffe6; }
        .matrix-table .bg-green { background: #e6ffe6; }
        .term-head { background: #e0e0e0; font-weight: bold; font-size: 6px; }
        .sub-head { background: #f5f5f5; font-size: 5.5px; }
        .footer { margin-top: 3px; font-size: 5.5px; text-align: right; color: #555; }
        .deliberation-table { width: 100%; border-collapse: collapse; font-size: 5.5px; margin-top: 2px; table-layout: fixed; }
        .deliberation-table th, .deliberation-table td { border: 1px solid #000; padding: 1px; word-wrap: break-word; overflow-wrap: break-word; }
        .annual-row { font-weight: bold; background: #e6ffe6; }
        
        /* Global overflow prevention */
        div, section, article, header, footer, main, nav, ul, ol, li, p, span, a, label, td, th {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        img, svg, canvas, video, iframe {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 5mm;
            }
            
            .print-wrap {
                width: 100%;
                max-width: 100%;
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
        <strong>TEST</strong> = test average · <strong>EXAM</strong> = exam mark · <strong>TOT</strong> = total for the subject in that term · <strong>POSITION</strong> = student rank within class for that term (Rank/Total Students)
    </div>

    <table class="matrix-table">
        <thead>
            <tr class="bg-gray">
                <th colspan="2" class="text-left">Competence / Credits</th>
                @foreach($terms as $t)
                    <th colspan="3" class="term-head">{{ $t['name'] }}</th>
                @endforeach
                @if(count($terms) > 1)
                    <th colspan="3" class="term-head">ANNUAL</th>
                @endif
            </tr>
            <tr class="sub-head">
                <th>Competence</th>
                <th>Cr</th>
                @foreach($terms as $t)
                    <th>TEST</th>
                    <th>EXAM</th>
                    <th>TOT</th>
                @endforeach
                @if(count($terms) > 1)
                    <th>TEST</th>
                    <th>EXAM</th>
                    <th>TOT</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr class="bg-blue">
                <td colspan="{{ 2 + 3 * count($terms) + (count($terms) > 1 ? 3 : 0) }}" class="text-left font-bold" style="font-weight: bold;">Core competencies — specific</td>
            </tr>
            @forelse($mRows as $row)
                <tr>
                    <td class="text-left">{{ Str::limit($row['competence_title'], 40) }}</td>
                    <td>{{ $row['credits'] }}</td>
                    @php
                        $annualTest = 0;
                        $annualExam = 0;
                        $annualTotal = 0;
                        $termCount = 0;
                    @endphp
                    @foreach($terms as $t)
                        @php $cell = $row['by_term_id'][$t['id']] ?? ['test' => '—', 'exam' => '—', 'total' => '—']; @endphp
                        <td>{{ $cell['test'] }}</td>
                        <td>{{ $cell['exam'] }}</td>
                        <td style="font-weight: bold;">{{ $cell['total'] }}</td>
                        @if(is_numeric($cell['test']) && is_numeric($cell['exam']) && is_numeric($cell['total']))
                            @php
                                $annualTest += (float) $cell['test'];
                                $annualExam += (float) $cell['exam'];
                                $annualTotal += (float) $cell['total'];
                                $termCount++;
                            @endphp
                        @endif
                    @endforeach
                    @if(count($terms) > 1)
                        <td>{{ $termCount > 0 ? number_format($annualTest / $termCount, 1) : '—' }}</td>
                        <td>{{ $termCount > 0 ? number_format($annualExam / $termCount, 1) : '—' }}</td>
                        <td style="font-weight: bold;">{{ $termCount > 0 ? number_format($annualTotal / $termCount, 1) : '—' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + 3 * count($terms) + (count($terms) > 1 ? 3 : 0) }}" style="padding: 12px; color: #666;">No assessment data</td>
                </tr>
            @endforelse

            @if(count($mRows) > 0)
                <tr class="bg-white">
                    <td colspan="{{ 2 + 3 * count($terms) + (count($terms) > 1 ? 3 : 0) }}"></td>
                </tr>
                <tr class="bg-blue" style="font-weight: bold;">
                    <td colspan="2" class="text-left">TOTAL</td>
                    @php
                        $annualTestTotal = 0;
                        $annualExamTotal = 0;
                        $annualGrandTotal = 0;
                        $termCountForTotal = 0;
                    @endphp
                    @foreach($terms as $t)
                        @php $tot = $totalsByTerm[$t['id']] ?? null; @endphp
                        <td>{{ $tot['test_sum'] ?? '—' }}</td>
                        <td>{{ $tot['exam_sum'] ?? '—' }}</td>
                        <td>{{ $tot['total_sum'] ?? '—' }}</td>
                        @if($tot && is_numeric($tot['test_sum']) && is_numeric($tot['exam_sum']) && is_numeric($tot['total_sum']))
                            @php
                                $annualTestTotal += (float) $tot['test_sum'];
                                $annualExamTotal += (float) $tot['exam_sum'];
                                $annualGrandTotal += (float) $tot['total_sum'];
                                $termCountForTotal++;
                            @endphp
                        @endif
                    @endforeach
                    @if(count($terms) > 1)
                        <td>{{ $termCountForTotal > 0 ? number_format($annualTestTotal / $termCountForTotal, 1) : '—' }}</td>
                        <td>{{ $termCountForTotal > 0 ? number_format($annualExamTotal / $termCountForTotal, 1) : '—' }}</td>
                        <td>{{ $termCountForTotal > 0 ? number_format($annualGrandTotal / $termCountForTotal, 1) : '—' }}</td>
                    @endif
                </tr>
                <tr class="bg-yellow" style="font-weight: bold;">
                    <td colspan="2" class="text-left">POSITION (Rank/Total)</td>
                    @foreach($terms as $t)
                        <td></td>
                        <td></td>
                        <td class="text-center" style="font-weight: bold; font-size: 7px;">{{ $positionByTerm[$t['id']] ?? '—' }}</td>
                    @endforeach
                    @if(count($terms) > 1)
                        <td colspan="3" class="text-center">—</td>
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
                        <td></td>
                        <td></td>
                        <td>{{ $avg !== null ? number_format((float) $avg, 1).'%' : '—' }}</td>
                        @if($avg !== null)
                            @php
                                $annualPercentage += (float) $avg;
                                $percentTermCount++;
                            @endphp
                        @endif
                    @endforeach
                    @if(count($terms) > 1)
                        <td></td>
                        <td></td>
                        <td>{{ $percentTermCount > 0 ? number_format($annualPercentage / $percentTermCount, 1).'%' : '—' }}</td>
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
