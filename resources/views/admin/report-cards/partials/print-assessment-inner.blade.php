@php
    $hdrSchoolName = optional($schoolInfo)->school_name ?: 'SCHOOL NAME';
    $hdrAddress = optional($schoolInfo)->address ?: 'School Address';
    $hdrPhone = optional($schoolInfo)->phone ?: 'School Phone';
    $hdrEmail = optional($schoolInfo)->email ?: 'school@email.com';
    $hdrWebsite = optional($schoolInfo)->website;
    $hdrPrincipal = optional($schoolInfo)->principal_name ?: 'School Manager';
    $hasLogo = $schoolInfo && !empty($schoolInfo->logo_url);
    $hasPrincipalSignature = $schoolInfo && !empty($schoolInfo->principal_signature_url);
    $hasStampSignature = $schoolInfo && !empty($schoolInfo->stamp_signature_url);
@endphp

<!-- Header: left logo | student + school details | passport photo -->
<table class="print-header-table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="print-header-logo">
            @if($hasLogo)
                <img src="{{ $schoolInfo->logo_url }}" alt="" class="school-logo-img">
            @else
                <div class="print-logo-placeholder">LOGO</div>
            @endif
        </td>
        <td class="print-header-center">
            <div class="hdr-line"><span class="lbl">NAME:</span> {{ $reportCard->student->full_name }}</div>
            <div class="hdr-line"><span class="lbl">REG NO:</span> {{ $reportCard->student->reg_number ?? 'N/A' }}</div>
            <div class="hdr-line"><span class="lbl">CLASS:</span> {{ $reportCard->course->course_name }}</div>
            <div class="hdr-line"><span class="lbl">YEAR:</span> {{ $reportCard->academicYear->year }}</div>
            <div class="hdr-line"><span class="lbl">TERM:</span> {{ $reportCard->term->name ?? '—' }}</div>

            <div class="hdr-school-name">{{ $hdrSchoolName }}</div>

            <div class="hdr-contact"><span class="lbl">Address:</span> {{ $hdrAddress }}</div>
            <div class="hdr-contact"><span class="lbl">Phone:</span> {{ $hdrPhone }}</div>
            <div class="hdr-contact"><span class="lbl">E-mail:</span> {{ $hdrEmail }}</div>
            @if(!empty($hdrWebsite))
                <div class="hdr-contact"><span class="lbl">Website:</span> {{ $hdrWebsite }}</div>
            @endif
        </td>
        <td class="print-header-logo print-header-logo--right">
            @if(!empty($studentPhotoBase64))
                <div style="text-align: center; padding: 8px;">
                    <img src="data:{{ $studentPhotoMime ?? 'image/jpeg' }};base64,{{ $studentPhotoBase64 }}" alt="Student Photo" style="width: 160px; height: 240px; object-fit: cover; border: 3px solid #2c3e50; box-shadow: 0 4px 8px rgba(0,0,0,0.15); border-radius: 4px; display: block;">
                </div>
            @else
                <div class="print-logo-placeholder" style="width: 160px; height: 240px; font-size: 14px; margin: 8px auto; border-radius: 4px;">PHOTO</div>
            @endif
        </td>
    </tr>
</table>

<!-- Title -->
<div class="title-section">
    <h2>TRAINEE'S ASSESSMENT REPORT</h2>
</div>

<!-- Qualification Info -->
<div class="qualification-section">
    <div class="qualification-row">
        <div class="qualification-cell font-bold">{{ $reportCard->course->course_name ?? 'COURSE NAME' }}</div>
        <div class="qualification-cell">
            <span class="font-bold">Qualification Title:</span> {{ $reportCard->course->course_type ?? 'TVET CERTIFICATE III' }}
        </div>
        <div class="qualification-cell"></div>
    </div>
    <div class="qualification-row">
        <div class="qualification-cell">
            <span class="font-bold">Trade:</span> {{ $reportCard->course->course_name ?? 'SOFTWARE DEVELOPMENT' }}
        </div>
        <div class="qualification-cell"></div>
    </div>
</div>

<!-- Assessment Legend -->
<div class="legend-section">
    <span class="font-bold">TEST:</span> Test Marks |
    <span class="font-bold"> EXAM:</span> Exam Marks |
    <span class="font-bold"> AVG:</span> Average
</div>

<!-- Main Assessment Table -->
<table class="assessment-table">
    <thead>
        <tr class="bg-gray font-bold">
            <td colspan="4" class="text-left"></td>
            <td>MAX</td>
            <td colspan="2" class="bg-gray">{{ $reportCard->term->name ?? 'Current Term' }}</td>
            <td colspan="3" class="bg-gray">A.A<br>(%)</td>
        </tr>

        <tr class="bg-gray font-bold">
            <td colspan="4" class="text-left">Behaviour</td>
            <td>100</td>
            <td class="bg-gray">TEST</td>
            <td class="bg-gray">EXAM</td>
            <td class="bg-gray">AVG</td>
            <td class="bg-gray">A.A</td>
            <td class="bg-gray">STATUS</td>
        </tr>
    </thead>
    <tbody>
        @if($reportCard->subjects->count() > 0)
            <tr class="bg-blue">
                <td colspan="7" class="font-bold">Core competencies - Specific</td>
            </tr>
            @foreach($reportCard->subjects as $subject)
                @php
                    $subjectTest = min(100, max(0, (float) ($subject->test_average ?? 0)));
                    $subjectExam = min(100, max(0, (float) ($subject->exam_mark ?? 0)));
                    if ($subjectTest > 0 && $subjectExam > 0) {
                        $subjectAvg = ($subjectTest + $subjectExam) / 2;
                    } elseif ($subjectTest > 0) {
                        $subjectAvg = $subjectTest;
                    } elseif ($subjectExam > 0) {
                        $subjectAvg = $subjectExam;
                    } else {
                        $subjectAvg = 0;
                    }
                    $status = $subjectAvg >= 50 ? 'PASS' : 'FAIL';
                @endphp
                <tr>
                    <td colspan="4" class="text-left">{{ $subject->subject_name }}</td>
                    <td>{{ $subject->credits ?? 3 }}</td>
                    <td>{{ number_format($subjectTest, 2) }}</td>
                    <td>{{ number_format($subjectExam, 2) }}</td>
                    <td class="font-bold">{{ number_format($subjectAvg, 2) }}</td>
                    <td class="font-bold">{{ number_format($subjectAvg, 2) }}%</td>
                    <td class="font-bold">{{ $status }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #666;">
                    No assessment data available for this student
                </td>
            </tr>
        @endif

        @if($reportCard->subjects->count() > 0)
            <tr class="bg-white font-bold">
                <td colspan="7" class="text-left"></td>
            </tr>
            <tr class="bg-blue font-bold">
                <td colspan="5" class="text-left">TOTAL</td>
                @php
                    $cappedTest = fn ($s) => min(100, max(0, (float) ($s->test_average ?? 0)));
                    $cappedExam = fn ($s) => min(100, max(0, (float) ($s->exam_mark ?? 0)));
                    $rowAvg = function ($s) use ($cappedTest, $cappedExam) {
                        $t = $cappedTest($s);
                        $e = $cappedExam($s);
                        if ($t > 0 && $e > 0) {
                            return ($t + $e) / 2;
                        }
                        if ($t > 0) {
                            return $t;
                        }
                        if ($e > 0) {
                            return $e;
                        }

                        return 0;
                    };
                    $subjectCount = $reportCard->subjects->count();
                    $totalTest = $subjectCount > 0 ? $reportCard->subjects->sum($cappedTest) : 0;
                    $totalExam = $subjectCount > 0 ? $reportCard->subjects->sum($cappedExam) : 0;
                    $totalMark = $subjectCount > 0 ? $reportCard->subjects->sum('total_mark') : 0;
                    $averageMark = $subjectCount > 0 ? $reportCard->subjects->avg($rowAvg) : 0;
                    if ($averageMark === null) {
                        $averageMark = 0;
                    }
                    $totalStatus = $averageMark >= 50 ? 'PASS' : 'FAIL';
                @endphp
                <td>{{ number_format($totalTest, 1) }}</td>
                <td>{{ number_format($totalExam, 1) }}</td>
                <td class="font-bold">{{ number_format($totalMark, 1) }}</td>
                <td class="font-bold">{{ number_format($averageMark, 1) }}%</td>
                <td class="font-bold">{{ $totalStatus }}</td>
            </tr>

            <tr class="bg-yellow font-bold">
                <td colspan="5" class="text-left">POSITION</td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="text-center">
                    @php
                        $position = $positionData['position'] ?? $reportCard->calculated_position ?? '-';
                        $total = $positionData['total'] ?? $reportCard->calculated_total ?? '-';
                    @endphp
                    {{ $position }} out of {{ $total }}
                </td>
            </tr>

            <tr class="bg-gray font-bold">
                <td colspan="5" class="text-left">Percentage %</td>
                <td>—</td>
                <td>—</td>
                <td class="font-bold">{{ number_format($reportCard->average_marks, 1) }}%</td>
                <td></td>
                <td></td>
            </tr>
        @endif

        <tr class="bg-white font-bold">
            <td colspan="10" class="text-left">Class Trainer's Comments & Signature</td>
        </tr>

        <tr class="bg-white font-bold">
            <td colspan="10" class="text-left">Parents signature</td>
        </tr>
    </tbody>
</table>

<!-- Deliberation Table -->
<div style="border-top: 1px solid black;">
    <table class="deliberation-table">
        <thead>
            <tr class="bg-gray">
                <th class="text-left">Deliberation</th>
                <th>Promotion at 1st Sitting</th>
                <th>2nd Sitting</th>
                <th>Promoted after re-assessment</th>
                <th>Advised to Repeat</th>
                <th>Dismissed</th>
                    <th>School Manager<br>{{ $hdrPrincipal }}<br>SIGNATURE<br>____/____/{{ date('Y') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td><input type="checkbox" class="checkbox"></td>
                <td><input type="checkbox" class="checkbox"></td>
                <td><input type="checkbox" class="checkbox"></td>
                <td><input type="checkbox" class="checkbox"></td>
                <td><input type="checkbox" class="checkbox"></td>
                <td style="text-align:center;">
                    @if($hasPrincipalSignature)
                        <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal Signature" style="max-height: 48px; max-width: 140px; margin-right: 6px;">
                    @endif
                    @if($hasStampSignature)
                        <img src="{{ $schoolInfo->stamp_signature_url }}" alt="School Stamp" style="max-height: 64px; max-width: 90px;">
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    @if($schoolInfo)
        Powered by {{ $schoolInfo->school_name ?? 'School Management System' }}
    @else
        Powered by School Management System
    @endif
</div>
