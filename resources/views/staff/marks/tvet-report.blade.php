@extends('staff.partials.sidebar_dos')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Assessment Report - {{ $reportCard->student->full_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        .print-area {
            border: 1px solid black;
            background: white;
            margin: 20px 0;
        }
        
        .header-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 1px solid black;
        }
        
        .header-cell {
            padding: 12px;
            font-size: 11px;
        }
        
        .header-cell:not(:last-child) {
            border-right: 1px solid black;
        }
        
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 160px;
        }
        
        .student-photo {
            height: 160px;
            width: 160px;
            object-fit: cover;
        }
        
        .school-logo {
            height: 160px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
        }
        
        .title-section {
            background: #f0f0f0;
            border-bottom: 1px solid black;
            padding: 6px;
            text-align: center;
        }
        
        .title-section h2 {
            font-size: 14px;
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
            padding: 6px;
        }
        
        .qualification-cell:not(:last-child) {
            border-right: 1px solid black;
        }
        
        .legend-section {
            border-bottom: 1px solid black;
            padding: 6px;
            font-size: 9px;
        }
        
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        
        .assessment-table th,
        .assessment-table td {
            border: 1px solid black;
            padding: 4px;
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
            font-size: 10px;
            margin-top: 1px;
        }
        
        .deliberation-table th,
        .deliberation-table td {
            border: 1px solid black;
            padding: 6px;
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
            padding: 4px;
            text-align: right;
            font-size: 8px;
            border-top: 1px solid black;
        }
        
        .checkbox {
            width: 16px;
            height: 16px;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-dark fw-bold mb-1">
                    <i class="fas fa-id-card me-2 text-success"></i>
                    TVET Student Report Card
                </h2>
                <p class="text-muted mb-0">
                    {{ $reportCard->student->full_name }} (ID: {{ $reportCard->student->id }})
                    @if($reportCard->student->course)
                        - {{ $reportCard->student->course->course_name }}
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Marks
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print me-1"></i> Print Report Card
                </button>
            </div>
        </div>
    </div>

    <div class="print-area">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-cell">
                <div class="font-bold">SMARTCAMPUS360</div>
                <div style="margin-top: 4px;">
                    <span class="font-bold">Phone:</span> 0788333010
                </div>
                <div>
                    <span class="font-bold">E-mail:</span> smartcampus360@gmail.com
                </div>
                <div>
                    <span class="font-bold">Website:</span> www.smartcampus360.rw
                </div>
            </div>
            
            <div class="header-cell">
                <div class="logo-container">
                    @if($reportCard->student->passport_photo)
                        @if(str_contains($reportCard->student->passport_photo, 'http'))
                            <img src="{{ $reportCard->student->passport_photo }}" alt="Student Photo" class="student-photo">
                        @else
                            <img src="{{ asset($reportCard->student->passport_photo) }}" alt="Student Photo" class="student-photo">
                        @endif
                    @else
                        <div style="width: 160px; height: 160px; background: #fff; border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center;">
                                <div style="color: #000; font-size: 12px; font-weight: bold;">STUDENT<br>PHOTO</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="header-cell">
                <div><span class="font-bold">YEAR:</span> {{ $reportCard->academicYear->year }}</div>
                <div style="margin-top: 4px;">
                    <span class="font-bold">CLASS:</span> {{ $reportCard->course->course_name }}
                </div>
                <div style="margin-top: 4px;">
                    <span class="font-bold">NAMES:</span> {{ $reportCard->student->full_name }}
                </div>
                <div style="margin-top: 4px;">
                    <span class="font-bold">REG NO:</span> {{ $reportCard->student->reg_number ?? 'N/A' }}
                </div>
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
            <span class="font-bold">F.A:</span> Formative Assessment |
            <span class="font-bold"> LA:</span> Integrated Assessment |
            <span class="font-bold"> C.A:</span> Comprehensive Assessment |
            <span class="font-bold"> AVG:</span> Average
        </div>

        <!-- Main Assessment Table -->
        <table class="assessment-table">
            <thead>
                <tr class="bg-gray font-bold">
                    <td colspan="4" class="text-left"></td>
                    <td>MAX</td>
                    <td colspan="4" class="bg-gray">{{ $reportCard->term->term ?? 'Current Term' }}</td>
                    <td colspan="3" class="bg-gray">A.A<br>(%)</td>
                </tr>
                
                <tr class="bg-gray font-bold">
                    <td colspan="4" class="text-left">Behaviour</td>
                    <td>100</td>
                    <td colspan="4">-</td>
                    <td colspan="3">-</td>
                </tr>
                
                <tr>
                    <th colspan="3" rowspan="2" class="bg-gray">Module Code</th>
                    <th rowspan="2" class="bg-gray">Competence Title</th>
                    <th rowspan="2" class="bg-gray">Credits</th>
                    <th class="bg-gray">F.A</th>
                    <th class="bg-gray">LA</th>
                    <th class="bg-gray">C.A</th>
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
                        <td colspan="11" class="font-bold">Core competencies - Specific</td>
                    </tr>
                    @foreach($reportCard->subjects as $subject)
                        <tr>
                            <td colspan="3">{{ $subject->subject_code ?? 'N/A' }}</td>
                            <td class="text-left">{{ $subject->subject_name }}</td>
                            <td>{{ $subject->credits ?? 3 }}</td>
                            <td>{{ number_format($subject->test_average ?? 0, 1) }}</td>
                            <td>-</td>
                            <td>{{ number_format($subject->exam_mark ?? 0, 1) }}</td>
                            <td>{{ number_format($subject->total_mark ?? 0, 1) }}</td>
                            <td class="font-bold">{{ number_format($subject->total_mark ?? 0, 1) }}</td>
                            <td class="font-bold"></td>
                            <td class="font-bold">{{ $subject->grade === 'A' || $subject->grade === 'B' ? 'C' : 'NYC' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 20px; color: #666;">
                            No assessment data available for this student
                        </td>
                    </tr>
                @endif

                @if($reportCard->subjects->count() > 0)
                    <!-- Term Totals Row -->
                    <tr class="bg-white font-bold">
                        <td colspan="11" class="text-left"></td>
                    </tr>
                    <tr class="bg-blue font-bold">
                        <td colspan="4" class="text-left">TOTAL</td>
                        <td>{{ $reportCard->subjects->count() * 3 }}</td>
                        <td>{{ number_format($reportCard->subjects->sum('test_average'), 1) }}</td>
                        <td>-</td>
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
                        <td colspan="4">{{ $reportCard->class_position ?? '-' }} out of {{ $reportCard->total_students ?? '-' }}</td>
                        <td colspan="2">{{ $reportCard->class_position ?? '-' }} out of {{ $reportCard->total_students ?? '-' }}</td>
                        <td></td>
                    </tr>
                    
                    <!-- Percentage Row -->
                    <tr class="bg-gray font-bold">
                        <td colspan="4" class="text-left">Percentage %</td>
                        <td>100</td>
                        <td colspan="4">{{ number_format($reportCard->average_marks, 1) }}%</td>
                        <td colspan="3">{{ number_format($reportCard->average_marks, 1) }}%</td>
                    </tr>
                @endif

                <!-- Class Trainer's Comments & Signature -->
                <tr class="bg-white font-bold">
                    <td colspan="4" class="text-left">Class Trainer's Comments & Signature</td>
                    <td></td>
                    <td colspan="4"></td>
                    <td colspan="3"></td>
                </tr>
                
                <!-- Parents signature -->
                <tr class="bg-white font-bold">
                    <td colspan="4" class="text-left">Parents signature</td>
                    <td></td>
                    <td colspan="4"></td>
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
            Powered by SmartCampus360
        </div>
    </div>
</body>
</html>
@endsection
