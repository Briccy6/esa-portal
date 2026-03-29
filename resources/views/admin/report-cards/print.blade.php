<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Assessment Report - {{ $reportCard->student->full_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .print-area {
            border: 1px solid black;
        }
        
        /* Header: logo | details | logo (table layout for DomPDF) */
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #000;
        }
        .print-header-table td {
            vertical-align: middle;
            padding: 10px 8px;
        }
        .print-header-logo {
            width: 22%;
            text-align: center;
            border-right: 1px solid #000;
        }
        .print-header-logo--right {
            border-right: none;
            border-left: 1px solid #000;
        }
        .print-header-center {
            width: 56%;
            text-align: center;
            padding: 12px 14px !important;
        }
        .print-header-logo img,
        .school-logo-img {
            max-height: 88px;
            max-width: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .print-logo-placeholder {
            width: 88px;
            height: 88px;
            margin: 0 auto;
            background: #ecf0f1;
            border: 2px dashed #bdc3c7;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
            font-weight: bold;
            line-height: 84px;
        }
        .hdr-line {
            font-size: 10px;
            line-height: 1.45;
            margin: 0 0 3px 0;
        }
        .hdr-line .lbl {
            font-weight: bold;
        }
        .hdr-school-name {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 6px 0;
            letter-spacing: 0.3px;
        }
        .hdr-contact {
            font-size: 9px;
            line-height: 1.5;
            margin: 2px 0;
        }
        
        .title-section {
            background: #f0f0f0;
            border-bottom: 1px solid black;
            padding: 4px;
            text-align: center;
        }
        
        .title-section h2 {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }
        
        .qualification-section {
            border-bottom: 1px solid black;
        }
        
        .qualification-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            font-size: 10px;
        }
        
        .qualification-cell {
            padding: 4px;
        }
        
        .qualification-cell:not(:last-child) {
            border-right: 1px solid black;
        }
        
        .legend-section {
            border-bottom: 1px solid black;
            padding: 4px;
            font-size: 8px;
        }
        
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        .assessment-table th,
        .assessment-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        
        .assessment-table .text-left {
            text-align: left;
        }
        
        .assessment-table .bg-gray {
            background: #f0f0f0;
        }
        
        .assessment-table .bg-blue {
            background: #e6f3ff;
        }
        
        .assessment-table .bg-green {
            background: #e6ffe6;
        }
        
        .assessment-table .bg-yellow {
            background: #ffffe6;
        }
        
        .assessment-table .font-bold {
            font-weight: bold;
        }
        
        .competent-mark {
            color: green;
            font-weight: bold;
        }
        
        .not-competent-mark {
            color: red;
            text-decoration: underline;
            font-weight: bold;
        }
        
        .deliberation-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-top: 1px;
        }
        
        .deliberation-table th,
        .deliberation-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        
        .deliberation-table .text-left {
            text-align: left;
        }
        
        .deliberation-table .bg-gray {
            background: #f0f0f0;
        }
        
        .footer {
            background: #f0f0f0;
            padding: 2px;
            text-align: right;
            font-size: 7px;
            border-top: 1px solid black;
        }
        
        .checkbox {
            width: 16px;
            height: 16px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="print-area">
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
                    <!-- Core Competencies - Specific -->
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
                    <!-- Term Totals Row -->
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

                    <!-- Position Row -->
                    <tr class="bg-yellow font-bold">
                        <td colspan="4" class="text-left">POSITION</td>
                        <td></td>
                        <td colspan="2">{{ $reportCard->class_position ?? '-' }} out of {{ $reportCard->total_students ?? '-' }}</td>
                        <td colspan="3">{{ $reportCard->class_position ?? '-' }} out of {{ $reportCard->total_students ?? '-' }}</td>
                    </tr>
                    
                    <!-- Percentage Row -->
                    <tr class="bg-gray font-bold">
                        <td colspan="4" class="text-left">Percentage %</td>
                        <td>100</td>
                        <td colspan="2">{{ number_format($reportCard->average_marks, 1) }}%</td>
                        <td colspan="3">{{ number_format($reportCard->average_marks, 1) }}%</td>
                    </tr>
                @endif

                <!-- Class Trainer's Comments & Signature -->
                <tr class="bg-white font-bold">
                    <td colspan="4" class="text-left">Class Trainer's Comments & Signature</td>
                    <td></td>
                    <td colspan="2">
                        @if($reportCard->approvedBy)
                            {{ $reportCard->approvedBy->name ?? 'Class Teacher' }}
                        @endif
                    </td>
                    <td colspan="3"></td>
                </tr>
                
                <!-- Parents signature -->
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
    </div>
</body>
</html>