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
            margin: 1.5cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.4;
            color: #000;
        }
        
        .print-area {
            border: 2px solid #000;
            background: white;
            margin: 20px 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 2px solid #000;
        }
        
        .header-cell {
            padding: 15px;
            font-size: 11px;
            border-right: 1px solid #000;
        }
        
        .header-cell:last-child {
            border-right: none;
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
            border: 1px solid #000;
        }
        
        .school-logo {
            height: 160px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
        }
        
        .title-section {
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            border-bottom: 2px solid #000;
            padding: 12px;
            text-align: center;
        }
        
        .title-section h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .qualification-section {
            border-bottom: 1px solid black;
        }
        
        .qualification-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr;
            gap: 1px;
            background: black;
            border: 1px solid black;
        }
        
        .qualification-grid-row {
            display: contents;
        }
        
        .qualification-item {
            background: white;
            padding: 8px 12px;
            font-size: 10px;
            display: flex;
            align-items: center;
        }
        
        .qualification-item.label {
            font-weight: bold;
            background: #f8f9fa;
        }
        
        .qualification-item.value {
            font-weight: normal;
        }
        
        .qualification-item.empty {
            background: white;
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
            margin-bottom: 1px;
            border: 1px solid #000;
        }
        
        .assessment-table th,
        .assessment-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
            min-height: 24px;
        }
        
        .assessment-table .text-left {
            text-align: left !important;
            padding-left: 10px !important;
            font-weight: 500;
        }
        
        .assessment-table .text-center {
            text-align: center !important;
        }
        
        .assessment-table .text-right {
            text-align: right !important;
            padding-right: 10px !important;
        }
        
        .assessment-table .bg-gray {
            background: #f8f9fa !important;
            font-weight: bold;
        }
        
        .assessment-table .bg-blue {
            background: #e3f2fd !important;
        }
        
        .assessment-table .bg-green {
            background: #e8f5e8 !important;
        }
        
        .assessment-table .bg-yellow {
            background: #fff9e6 !important;
        }
        
        .assessment-table .bg-white {
            background: white !important;
        }
        
        .assessment-table .font-bold {
            font-weight: bold !important;
        }
        
        .assessment-table .numeric {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 10px;
        }
        
        .assessment-table .percentage {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 10px;
        }
        
        .assessment-table .position-badge {
            background: #1976d2;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .position-summary {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #2563eb;
            background: #f8fafc;
            text-align: center;
            border-radius: 8px;
        }
        
        .position-summary h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 16px;
            font-weight: bold;
        }
        
        .position-summary .position-display {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
        }
        
        .position-summary .position-details {
            font-size: 14px;
            color: #64748b;
        }
        
        .position-summary .rank-badge {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: bold;
            margin: 0 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            border: 1px solid #000;
        }
        
        .deliberation-table th,
        .deliberation-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
        }
        
        .deliberation-table .text-left {
            text-align: left !important;
            padding-left: 10px !important;
            font-weight: 500;
        }
        
        .deliberation-table .bg-gray {
            background: #f8f9fa !important;
            font-weight: bold;
        }
        
        .footer {
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            padding: 8px 15px;
            text-align: right;
            font-size: 9px;
            border-top: 2px solid #000;
            font-weight: 500;
        }
        
        .checkbox {
            width: 16px;
            height: 16px;
            border: 1px solid #000;
        }
        
        .print-break {
            page-break-inside: avoid;
        }
        
        .no-break {
            page-break-inside: avoid;
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
                    @if(isset($studentPhotoBase64) && $studentPhotoBase64)
                        <img src="data:{{ $studentPhotoMime ?? 'image/jpeg' }};base64,{{ $studentPhotoBase64 }}" alt="Student Photo" class="student-photo">
                    @elseif($reportCard->student->passport_photo)
                        @php
                            $photoPath = $reportCard->student->passport_photo;
                            // Add extension if missing (same logic as admin template)
                            if (!str_contains($photoPath, '.') && str_contains($photoPath, 'wzwnc4T6aiZMyuxDMySNfg17XGDd3ChdouadyuT3')) {
                                $photoPath .= '.png';
                            } elseif (!str_contains($photoPath, '.') && str_contains($photoPath, 'aKc2vKdlKFsMyFJ3m2N39HFU1uVIV6GJZMTuWsbj')) {
                                $photoPath .= '.jpg';
                            }
                        @endphp
                        @if(str_contains($photoPath, 'http'))
                            <img src="{{ $photoPath }}" alt="Student Photo" class="student-photo">
                        @else
                            <img src="{{ asset($photoPath) }}" alt="Student Photo" class="student-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display: none; width: 160px; height: 160px; background: #fff; border: 1px solid #000; align-items: center; justify-content: center;">
                                <div style="text-align: center;">
                                    <div style="color: #000; font-size: 12px; font-weight: bold;">STUDENT<br>PHOTO</div>
                                </div>
                            </div>
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
            <div class="qualification-grid">
                <!-- Row 1: Trainee Code | Qualification Title | Trade | RQF Level -->
                <div class="qualification-grid-row">
                    <div class="qualification-item label">Trainee Code</div>
                    <div class="qualification-item value">
                        <span class="font-bold">Qualification Title:</span> TVET CERTIFICATE III IN {{ $reportCard->course->course_name ?? 'SOFTWARE DEVELOPMENT' }}
                    </div>
                    <div class="qualification-item value">
                        <span class="font-bold">Trade:</span> {{ $reportCard->course->course_name ?? 'SOFTWARE DEVELOPMENT' }}
                    </div>
                    <div class="qualification-item value">
                        <span class="font-bold">RQF Level:</span> LEVEL III
                    </div>
                </div>
                <!-- Row 2: Values -->
                <div class="qualification-grid-row">
                    <div class="qualification-item value">{{ $reportCard->student->reg_number ?? 'N/A' }}</div>
                    <div class="qualification-item empty"></div>
                    <div class="qualification-item empty"></div>
                    <div class="qualification-item empty"></div>
                </div>
            </div>
        </div>

        <!-- Assessment Legend -->
        <div class="legend-section">
            <span class="font-bold">TEST:</span> Test Marks | 
            <span class="font-bold"> EXAM:</span> Exam Marks | 
            <span class="font-bold"> AVG:</span> Average
        </div>

        <!-- Main Assessment Table -->
        <table class="assessment-table print-break">
            <thead>
                <tr class="bg-gray font-bold">
                    <td colspan="4" class="text-left"></td>
                    <td>MAX</td>
                    <td colspan="3" class="bg-gray">{{ $reportCard->term->term ?? 'Current Term' }}</td>
                    <td colspan="3" class="bg-gray">A.A<br>(%)</td>
                </tr>
                
                <tr class="bg-gray font-bold">
                    <td colspan="4" class="text-left">Behaviour</td>
                    <td>100</td>
                    <td colspan="3">-</td>
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
                    <tr class="bg-blue font-bold">
                        <td colspan="11" class="text-left">Core competencies - Specific</td>
                    </tr>
                    @foreach($reportCard->subjects as $subject)
                        <tr>
                            <td colspan="3" class="text-left">{{ $subject->subject_code ?? 'N/A' }}</td>
                            <td class="text-left">{{ $subject->subject_name }}</td>
                            <td class="numeric">{{ $subject->credits ?? 3 }}</td>
                            <td class="numeric">{{ number_format($subject->test_average ?? 0, 2) }}</td>
                            <td class="numeric">{{ number_format($subject->exam_mark ?? 0, 2) }}</td>
                            <td class="numeric">{{ number_format($subject->total_mark ?? 0, 2) }}</td>
                            <td class="numeric font-bold">{{ number_format($subject->total_mark ?? 0, 2) }}</td>
                            <td class="numeric font-bold"></td>
                            <td class="font-bold">{{ $subject->grade === 'A' || $subject->grade === 'B' || $subject->grade === 'C' ? 'C' : 'NYC' }}</td>
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
                        <td class="numeric">{{ $reportCard->subjects->count() * 3 }}</td>
                        <td class="numeric">{{ number_format($reportCard->subjects->sum('test_average'), 1) }}</td>
                        <td class="numeric">{{ number_format($reportCard->subjects->sum('exam_mark'), 1) }}</td>
                        <td class="numeric">{{ number_format($reportCard->subjects->sum('total_mark'), 1) }}</td>
                        <td class="numeric">{{ number_format($reportCard->subjects->sum('total_mark'), 1) }}</td>
                        <td></td>
                        <td></td>
                    </tr>

                    <!-- Position Row -->
                    <tr class="bg-yellow font-bold">
                        <td colspan="4" class="text-left">POSITION</td>
                        <td></td>
                        <td colspan="4" class="text-center">
                            <span class="position-badge">{{ $reportCard->class_position ?? '-' }}</span> out of 
                            <span class="position-badge">{{ $reportCard->total_students ?? '-' }}</span>
                        </td>
                        <td colspan="2" class="text-center">
                            <span class="position-badge">{{ $reportCard->class_position ?? '-' }}</span> out of 
                            <span class="position-badge">{{ $reportCard->total_students ?? '-' }}</span>
                        </td>
                        <td></td>
                    </tr>
                    
                    <!-- Percentage Row -->
                    <tr class="bg-gray font-bold">
                        <td colspan="4" class="text-left">Percentage %</td>
                        <td class="numeric">100</td>
                        <td colspan="3" class="percentage">{{ number_format($reportCard->average_marks, 1) }}%</td>
                        <td colspan="3" class="percentage">{{ number_format($reportCard->average_marks, 1) }}%</td>
                    </tr>
                @endif

                <!-- Class Trainer's Comments & Signature -->
                <tr class="bg-white font-bold">
                    <td colspan="4" class="text-left">Class Trainer's Comments & Signature</td>
                    <td></td>
                    <td colspan="3"></td>
                    <td colspan="3"></td>
                </tr>
                
                <!-- Parents signature -->
                <tr class="bg-white font-bold">
                    <td colspan="4" class="text-left">Parents signature</td>
                    <td></td>
                    <td colspan="3"></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        <!-- Position Summary Section -->
        @if($reportCard->subjects->count() > 0)
            <div class="position-summary no-break">
                <h3>ACADEMIC POSITION SUMMARY</h3>
                <div class="position-display">
                    <span class="rank-badge">{{ $reportCard->class_position ?? '-' }}</span>
                    <span style="font-size: 18px; margin: 0 10px;">out of</span>
                    <span class="rank-badge">{{ $reportCard->total_students ?? '-' }}</span>
                </div>
                <div class="position-details">
                    Position in {{ $reportCard->course->course_name ?? 'Course' }} • 
                    {{ $reportCard->term->term ?? 'Current Term' }} • 
                    {{ $reportCard->academicYear->year ?? 'Academic Year' }}
                </div>
                <div style="margin-top: 10px; font-size: 12px; color: #94a3b8;">
                    Total Score: {{ number_format($reportCard->total_marks, 1) }}% | 
                    Average: {{ number_format($reportCard->average_marks, 1) }}%
                </div>
            </div>
        @endif

        <!-- Deliberation Table -->
        <div style="border-top: 2px solid #000;" class="print-break">
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
