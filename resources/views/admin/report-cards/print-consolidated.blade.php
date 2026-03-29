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
    $hasLogo = $schoolInfo && !empty($schoolInfo->logo_url);
    $termNames = collect($terms)->pluck('name')->implode(', ');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment — {{ $first->student->full_name }} — all terms</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        html, body { margin: 0; padding: 0; font-family: DejaVu Sans, Arial, sans-serif; font-size: 7px; color: #111; }
        .print-wrap { width: 100%; box-sizing: border-box; }
        .print-header-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid #000; margin-bottom: 4px; }
        .print-header-table td { vertical-align: middle; padding: 6px; font-size: 8px; }
        .print-header-logo { width: 18%; text-align: center; border-right: 1px solid #000; }
        .print-header-logo--right { border-left: 1px solid #000; border-right: none; }
        .print-header-center { width: 64%; text-align: center; }
        .school-logo-img { max-height: 70px; max-width: 90px; object-fit: contain; }
        .print-logo-placeholder { width: 70px; height: 70px; margin: 0 auto; background: #ecf0f1; border: 1px dashed #999; font-size: 7px; line-height: 68px; text-align: center; color: #888; }
        .hdr-line { margin: 0 0 2px 0; }
        .hdr-line .lbl { font-weight: bold; }
        .hdr-school-name { font-size: 10px; font-weight: bold; margin: 6px 0; }
        .hdr-contact { font-size: 7px; }
        .title-section { background: #f0f0f0; border: 1px solid #000; border-bottom: none; padding: 4px; text-align: center; }
        .title-section h2 { font-size: 10px; margin: 0; }
        .legend-section { border: 1px solid #000; border-top: none; padding: 3px 6px; font-size: 6px; margin-bottom: 0; }
        .matrix-table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 6.5px; }
        .matrix-table th, .matrix-table td { border: 1px solid #000; padding: 2px 3px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        .matrix-table .text-left { text-align: left; }
        .matrix-table .bg-gray { background: #f0f0f0; font-weight: bold; }
        .matrix-table .bg-blue { background: #e6f3ff; }
        .matrix-table .bg-yellow { background: #ffffe6; }
        .term-head { background: #e0e0e0; font-weight: bold; font-size: 6.5px; }
        .sub-head { background: #f5f5f5; font-size: 6px; }
        .footer { margin-top: 6px; font-size: 6px; text-align: right; color: #555; }
        .deliberation-table { width: 100%; border-collapse: collapse; font-size: 6px; margin-top: 4px; }
        .deliberation-table th, .deliberation-table td { border: 1px solid #000; padding: 2px; }
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
        <strong>TEST</strong> = test average · <strong>EXAM</strong> = exam mark · <strong>TOT</strong> = total for the subject in that term
    </div>

    <table class="matrix-table">
        <thead>
            <tr class="bg-gray">
                <th colspan="3" class="text-left">Module / Competence / Credits</th>
                @foreach($terms as $t)
                    <th colspan="3" class="term-head">{{ $t['name'] }}</th>
                @endforeach
            </tr>
            <tr class="sub-head">
                <th>Module</th>
                <th>Competence</th>
                <th>Cr</th>
                @foreach($terms as $t)
                    <th>TEST</th>
                    <th>EXAM</th>
                    <th>TOT</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr class="bg-blue">
                <td colspan="{{ 3 + 3 * $termCount }}" class="text-left font-bold" style="font-weight: bold;">Core competencies — specific</td>
            </tr>
            @forelse($mRows as $row)
                <tr>
                    <td class="text-left">{{ Str::limit($row['module_code'], 24) }}</td>
                    <td class="text-left">{{ Str::limit($row['competence_title'], 28) }}</td>
                    <td>{{ $row['credits'] }}</td>
                    @foreach($terms as $t)
                        @php $cell = $row['by_term_id'][$t['id']] ?? ['test' => '—', 'exam' => '—', 'total' => '—']; @endphp
                        <td>{{ $cell['test'] }}</td>
                        <td>{{ $cell['exam'] }}</td>
                        <td style="font-weight: bold;">{{ $cell['total'] }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 3 + 3 * $termCount }}" style="padding: 12px; color: #666;">No assessment data</td>
                </tr>
            @endforelse

            @if(count($mRows) > 0)
                <tr class="bg-white">
                    <td colspan="{{ 3 + 3 * $termCount }}"></td>
                </tr>
                <tr class="bg-blue" style="font-weight: bold;">
                    <td colspan="3" class="text-left">TOTAL</td>
                    @foreach($terms as $t)
                        @php $tot = $totalsByTerm[$t['id']] ?? null; @endphp
                        <td>{{ $tot['test_sum'] ?? '—' }}</td>
                        <td>{{ $tot['exam_sum'] ?? '—' }}</td>
                        <td>{{ $tot['total_sum'] ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr class="bg-yellow" style="font-weight: bold;">
                    <td colspan="3" class="text-left">POSITION</td>
                    @foreach($terms as $t)
                        <td colspan="3">{{ $positionByTerm[$t['id']] ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr class="bg-gray">
                    <td colspan="3" class="text-left">Percentage %</td>
                    @foreach($terms as $t)
                        @php $avg = $averageByTerm[$t['id']] ?? null; @endphp
                        <td colspan="3">{{ $avg !== null ? number_format((float) $avg, 1).'%' : '—' }}</td>
                    @endforeach
                </tr>
            @endif
        </tbody>
    </table>

    <table class="deliberation-table">
        <thead>
            <tr class="bg-gray">
                <th class="text-left">Deliberation</th>
                <th>Promotion</th>
                <th>2nd Sitting</th>
                <th>Re-assessment</th>
                <th>Repeat</th>
                <th>Dismissed</th>
                <th>Manager / SIGNATURE / {{ date('Y') }}</th>
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
                <td></td>
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
