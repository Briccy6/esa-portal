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
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid black;
        }
        
        .header-cell {
            padding: 8px;
            font-size: 10px;
            flex: 1;
        }
        
        .header-cell:not(:last-child) {
            border-right: 1px solid black;
        }
        
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100px;
        }
        
        .student-photo {
            height: 100px;
            width: 100px;
            object-fit: cover;
        }
        
        .school-logo {
            height: 100px;
            width: auto;
            max-width: 100px;
            object-fit: contain;
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
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-cell">
                <!-- Student Information -->
                <div style="text-align: center; font-size: 9px;">
                    <div><span class="font-bold">NAME:</span> {{ $reportCard->student->full_name }}</div>
                    <div><span class="font-bold">REG NO:</span> {{ $reportCard->student->reg_number ?? 'N/A' }}</div>
                    <div><span class="font-bold">CLASS:</span> {{ $reportCard->course->course_name }}</div>
                    <div><span class="font-bold">YEAR:</span> {{ $reportCard->academicYear->year }}</div>
                </div>
                <div class="logo-container" style="margin-top: 8px;">
                    @if($reportCard->student->passport_photo)
                        <img src="data:image/jpeg;base64,{{ base64_encode($reportCard->student->passport_photo) }}" alt="Student Photo" class="student-photo">
                    @else
                        <div style="width: 100px; height: 100px; background: #ecf0f1; border: 2px dashed #bdc3c7; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center;">
                                <div style="color: #7f8c8d; font-size: 8px; font-weight: bold;">STUDENT<br>PHOTO</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="header-cell">
                <div class="logo-container">
                    @if($schoolInfo && $schoolInfo->logo)
                        <img src="data:image/jpeg;base64,{{ base64_encode($schoolInfo->logo) }}" alt="School Logo" class="school-logo">
                    @elseif($schoolInfo && $schoolInfo->logo_url)
                        <img src="{{ $schoolInfo->logo_url }}" alt="School Logo" class="school-logo">
                    @else
                        <div style="width: 100px; height: 100px; background: #ecf0f1; border: 2px dashed #bdc3c7; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center;">
                                <div style="color: #7f8c8d; font-size: 8px; font-weight: bold;">SCHOOL<br>LOGO</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="header-cell">
                @if($schoolInfo)
                    <div class="font-bold">{{ $schoolInfo->school_name ?? 'SCHOOL NAME' }}</div>
                    @if($schoolInfo->address)
                        <div style="margin-top: 2px;">
                            <span class="font-bold">Address:</span> {{ $schoolInfo->address }}
                        </div>
                    @endif
                    @if($schoolInfo->phone)
                        <div style="margin-top: 2px;">
                            <span class="font-bold">Phone:</span> {{ $schoolInfo->phone }}
                        </div>
                    @endif
                    @if($schoolInfo->email)
                        <div style="margin-top: 2px;">
                            <span class="font-bold">E-mail:</span> {{ $schoolInfo->email }}
                        </div>
                    @endif
                    @if($schoolInfo->website)
                        <div style="margin-top: 2px;">
                            <span class="font-bold">Website:</span> {{ $schoolInfo->website }}
                        </div>
                    @endif
                @else
                    <div class="font-bold">SCHOOL NAME</div>
                    <div style="margin-top: 2px;">
                        <span class="font-bold">Phone:</span> School Phone
                    </div>
                    <div>
                        <span class="font-bold">E-mail:</span> school@email.com
                    </div>
                    <div>
                        <span class="font-bold">Website:</span> www.schoolwebsite.rw
                    </div>
                @endif
            </div>
        </div>

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
                    <td colspan="2" class="bg-gray">{{ $reportCard->term->term ?? 'Current Term' }}</td>
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
                        <th>School Manager<br>@if($schoolInfo && $schoolInfo->principal_name){{ $schoolInfo->principal_name }}@else School Manager @endif<br>SIGNATURE<br>____/____/{{ date('Y') }}</th>
                        <th>SIGNATURE</th>
                        <th>STAMP</th>
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
                        <td>
                            @if($schoolInfo && $schoolInfo->principal_signature_data)
                                <img src="data:image/jpeg;base64,{{ base64_encode($schoolInfo->principal_signature_data) }}" alt="Principal Signature" style="max-height: 40px; max-width: 80px;">
                            @elseif($schoolInfo && $schoolInfo->principal_signature_url)
                                <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal Signature" style="max-height: 40px; max-width: 80px;">
                            @else
                                <div style="height: 40px; border-bottom: 1px solid #ccc; width: 80px; margin: 0 auto;"></div>
                            @endif
                        </td>
                        <td>
                            @if($schoolInfo && $schoolInfo->stamp_signature_data)
                                <img src="data:image/jpeg;base64,{{ base64_encode($schoolInfo->stamp_signature_data) }}" alt="School Stamp" style="max-height: 40px; max-width: 80px;">
                            @elseif($schoolInfo && $schoolInfo->stamp_signature_url)
                                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="School Stamp" style="max-height: 40px; max-width: 80px;">
                            @else
                                <div style="height: 40px; border: 1px dashed #ccc; width: 80px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 6px; color: #999;">STAMP</span>
                                </div>
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
    </div>
</body>
</html>