@php
    $hdrSchoolName = optional($schoolInfo)->school_name ?: 'SCHOOL NAME';
    $hdrPhone = optional($schoolInfo)->phone ?: 'School Phone';
    $hdrEmail = optional($schoolInfo)->email ?: 'school@email.com';
    $hdrWebsite = optional($schoolInfo)->website ?: 'www.schoolwebsite.rw';
    $hasLogo = $schoolInfo && !empty($schoolInfo->logo_url);
@endphp

<!-- Header: left logo | student + school details | right logo -->
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

            <div class="hdr-contact"><span class="lbl">Phone:</span> {{ $hdrPhone }}</div>
            <div class="hdr-contact"><span class="lbl">E-mail:</span> {{ $hdrEmail }}</div>
            <div class="hdr-contact"><span class="lbl">Website:</span> {{ $hdrWebsite }}</div>
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

<!-- Title -->
<div class="title-section">
    <h2>TRAINEE'S ASSESSMENT REPORT</h2>
</div>

<!-- Qualification Info -->
<div class="qualification-section">
    <div class="qualification-row">
        <div class="qualification-cell font-bold">Qualification code</div>
        <div class="qualification-cell">
            <span class="font-bold">Qualification Title:</span> TVET CERTIFICATE III IN {{ $reportCard->course->course_name ?? 'SOFTWARE DEVELOPMENT' }}
        </div>
        <div class="qualification-cell"></div>
    </div>
    <div class="qualification-row">
        <div class="qualification-cell">
            <span class="font-bold">Trade:</span> {{ $reportCard->course->course_name ?? 'SOFTWARE DEVELOPMENT' }}
        </div>
        <div class="qualification-cell">
            <span class="font-bold">RQF Level:</span> LEVEL III
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
            <td colspan="2">-</td>
            <td colspan="3">-</td>
        </tr>

        <tr>
            <th colspan="3" rowspan="2" class="bg-gray">Module Code</th>
            <th rowspan="2" class="bg-gray">Competence Title</th>
            <th rowspan="2" class="bg-gray">Credits</th>
            <th class="bg-gray">TEST</th>
            <th class="bg-gray">EXAM</th>
            <th class="bg-gray">AVG</th>
            <th class="bg-gray">A.A</th>
            <th class="bg-gray">R.E</th>
            <th class="bg-gray">Observation</th>
        </tr>
    </thead>
    <tbody>
        @if($reportCard->subjects->count() > 0)
            <tr class="bg-blue">
                <td colspan="10" class="font-bold">Core competencies - Specific</td>
            </tr>
            @foreach($reportCard->subjects as $subject)
                <tr>
                    <td colspan="3">{{ $subject->subject_code ?? $subject->subject_name }}</td>
                    <td class="text-left">{{ $subject->subject_name }}</td>
                    <td>{{ $subject->credits ?? 3 }}</td>
                    <td>{{ $subject->test_average ?? '-' }}</td>
                    <td>{{ $subject->exam_mark ?? '-' }}</td>
                    <td class="font-bold">{{ $subject->total_mark ?? '-' }}</td>
                    <td class="font-bold">{{ $subject->total_mark ?? '-' }}</td>
                    <td class="font-bold"></td>
                    <td class="font-bold">{{ $subject->grade === 'A' || $subject->grade === 'B' ? 'C' : 'NYC' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px; color: #666;">
                    No assessment data available for this student
                </td>
            </tr>
        @endif

        @if($reportCard->subjects->count() > 0)
            <tr class="bg-white font-bold">
                <td colspan="10" class="text-left"></td>
            </tr>
            <tr class="bg-blue font-bold">
                <td colspan="4" class="text-left">TOTAL</td>
                <td>{{ $reportCard->subjects->count() * 3 }}</td>
                <td>{{ number_format($reportCard->subjects->sum('test_average'), 1) }}</td>
                <td>{{ number_format($reportCard->subjects->sum('exam_mark'), 1) }}</td>
                <td>{{ number_format($reportCard->subjects->sum('total_mark'), 1) }}</td>
                <td>{{ number_format($reportCard->subjects->sum('total_mark'), 1) }}</td>
                <td></td>
                <td></td>
            </tr>

            <tr class="bg-yellow font-bold">
                <td colspan="4" class="text-left">POSITION</td>
                <td></td>
                <td colspan="2">{{ $reportCard->class_position ?? '-' }} out of {{ $reportCard->total_students ?? '-' }}</td>
                <td colspan="3">{{ $reportCard->class_position ?? '-' }} out of {{ $reportCard->total_students ?? '-' }}</td>
            </tr>

            <tr class="bg-gray font-bold">
                <td colspan="4" class="text-left">Percentage %</td>
                <td>100</td>
                <td colspan="2">{{ number_format($reportCard->average_marks, 1) }}%</td>
                <td colspan="3">{{ number_format($reportCard->average_marks, 1) }}%</td>
            </tr>
        @endif

        <tr class="bg-white font-bold">
            <td colspan="4" class="text-left">Class Trainer's Comments & Signature</td>
            <td></td>
            <td colspan="2">
                @if($reportCard->approvedBy)
                    {{ $reportCard->approvedBy->email ?? $reportCard->approvedBy->name ?? 'Class Teacher' }}
                @endif
            </td>
            <td colspan="3"></td>
        </tr>

        <tr class="bg-white font-bold">
            <td colspan="4" class="text-left">Parents signature</td>
            <td></td>
            <td colspan="2"></td>
            <td colspan="3"></td>
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
                <th>School Manager<br>Hirwa Alain Brian<br>SIGNATURE<br>____/____/{{ date('Y') }}</th>
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
                <td></td>
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
