<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $reportCard->student->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .school-info {
            display: grid;
            grid-template-columns: 90px 1fr 110px;
            align-items: center;
            column-gap: 20px;
            margin-bottom: 10px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .school-details {
            flex: 1;
            text-align: center;
            margin: 0 20px;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .school-address {
            font-size: 10px;
            color: #666;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
        }
        
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .student-details {
            flex: 1;
        }
        
        .student-photo {
            width: 100px;
            height: 120px;
            border: 2px solid #ddd;
            border-radius: 5px;
            object-fit: cover;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #2c3e50;
        }
        
        .info-value {
            flex: 1;
        }
        
        .grades-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .grades-table th,
        .grades-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .grades-table th {
            background: #2c3e50;
            color: white;
            font-weight: bold;
        }
        
        .grades-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .grade-excellent {
            color: #27ae60;
            font-weight: bold;
        }
        
        .grade-good {
            color: #2980b9;
            font-weight: bold;
        }
        
        .grade-average {
            color: #f39c12;
            font-weight: bold;
        }
        
        .grade-poor {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .summary-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .summary-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 48%;
        }
        
        .summary-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 30px;
        }
        
        .signature-title {
            font-size: 11px;
            color: #666;
        }
        
        @media print {
            body {
                font-size: 10px;
            }
            
            .grades-table th,
            .grades-table td {
                padding: 6px;
                font-size: 9px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="school-info">
            @if($schoolInfo && $schoolInfo->logo_url)
                <img src="{{ $schoolInfo->logo_url }}" alt="School Logo" class="logo">
            @else
                <div class="logo"></div>
            @endif
            
            <div class="school-details">
                <div class="school-name">{{ $schoolInfo->school_name ?? 'Excellent Servant Academy' }}</div>
                <div class="school-address">
                    {{ $schoolInfo->address ?? '' }} 
                    @if($schoolInfo->phone) | Tel: {{ $schoolInfo->phone }} @endif
                    @if($schoolInfo->email) | Email: {{ $schoolInfo->email }} @endif
                </div>
                @if($schoolInfo->motto)
                    <div style="font-style: italic; color: #666; margin-top: 5px;">
                        "{{ $schoolInfo->motto }}"
                    </div>
                @endif
            </div>
            
            @if($studentPhotoBase64)
                <img src="data:{{ $studentPhotoMime }};base64,{{ $studentPhotoBase64 }}" 
                     alt="Student Photo" class="student-photo">
            @else
                <div class="student-photo" style="display: flex; align-items: center; justify-content: center; color: #999;">
                    No Photo
                </div>
            @endif
        </div>
        
        <div class="report-title">
            STUDENT REPORT CARD<br>
            <small>{{ $reportCard->academicYear->year ?? '' }} - {{ $reportCard->term->name ?? '' }}</small>
        </div>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="student-details">
            <div class="info-row">
                <div class="info-label">Student Name:</div>
                <div class="info-value">{{ $reportCard->student->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Admission No:</div>
                <div class="info-value">{{ $reportCard->student->admission_number ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Class/Course:</div>
                <div class="info-value">{{ $reportCard->course->course_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Academic Year:</div>
                <div class="info-value">{{ $reportCard->academicYear->year ?? 'N/A' }}</div>
            </div>
        </div>
        
        <div class="student-details">
            <div class="info-row">
                <div class="info-label">Term:</div>
                <div class="info-value">{{ $reportCard->term->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Marks:</div>
                <div class="info-value">{{ $reportCard->total_marks ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Average:</div>
                <div class="info-value">
                    @if(!is_null($reportCard->average_marks))
                        {{ $reportCard->average_marks }}%
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Grade:</div>
                <div class="info-value">
                    <span class="grade-{{ strtolower($reportCard->grade ?? 'average') }}">
                        {{ $reportCard->grade ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects and Grades -->
    <div class="grades-section">
        <div class="section-title">SUBJECTS PERFORMANCE</div>
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Test Marks</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportCard->subjects as $subject)
                    <tr>
                        <td>{{ $subject->lesson->lesson_name ?? 'N/A' }}</td>
                        <td>
                            @if($subject->testMarks->count() > 0)
                                @foreach($subject->testMarks as $testMark)
                                    {{ $testMark->exam->title ?? 'Test' }}: {{ $testMark->marks_obtained ?? '-' }}/{{ $testMark->total_marks ?? '-' }}<br>
                                @endforeach
                            @else
                                No tests
                            @endif
                        </td>
                        <td>{{ $subject->total_marks ?? '-' }}</td>
                        <td>
                            <span class="grade-{{ strtolower($subject->grade ?? 'average') }}">
                                {{ $subject->grade ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ $subject->remarks ?? 'Good' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No subjects found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="summary-section">
        <div class="summary-box">
            <div class="summary-title">ACADEMIC SUMMARY</div>
            <div class="summary-row">
                <span>Total Subjects:</span>
                <span>{{ $reportCard->subjects->count() }}</span>
            </div>
            <div class="summary-row">
                <span>Total Marks:</span>
                <span>{{ $reportCard->total_marks ?? '-' }}</span>
            </div>
            <div class="summary-row">
                <span>Average Marks:</span>
                <span>
                    @if(!is_null($reportCard->average_marks))
                        {{ $reportCard->average_marks }}%
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="summary-row">
                <span>Overall Grade:</span>
                <span class="grade-{{ strtolower($reportCard->grade ?? 'average') }}">
                    {{ $reportCard->grade ?? 'N/A' }}
                </span>
            </div>
        </div>
        
        <div class="summary-box">
            <div class="summary-title">CLASS POSITION</div>
            <div class="summary-row">
                <span>Position in Class:</span>
                <span>{{ $reportCard->class_position ?? 'N/A' }} / {{ $reportCard->total_students ?? 'N/A' }}</span>
            </div>
            <div class="summary-row">
                <span>Total Students:</span>
                <span>{{ $reportCard->total_students ?? '-' }}</span>
            </div>
            <div class="summary-row">
                <span>Attendance:</span>
                <span>Good</span>
            </div>
            <div class="summary-row">
                <span>Conduct:</span>
                <span>Excellent</span>
            </div>
        </div>
    </div>

    <!-- Remarks -->
    @if($reportCard->remarks)
    <div class="grades-section">
        <div class="section-title">TEACHER'S REMARKS</div>
        <div style="padding: 10px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #2c3e50;">
            {{ $reportCard->remarks }}
        </div>
    </div>
    @endif

    <!-- Footer with Signatures -->
    <div class="footer">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-title">Class Teacher's Signature</div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-title">Head Teacher's Signature</div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>
    