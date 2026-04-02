<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->full_name }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .report-card {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        
        .student-info {
            margin-bottom: 25px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-col {
            flex: 1;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            font-size: 11px;
        }
        
        .info-table td:first-child {
            width: 35%;
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #333;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .marks-table th {
            background-color: #333;
            color: white;
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        .marks-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            font-size: 11px;
            text-align: center;
        }
        
        .marks-table td:first-child {
            text-align: left;
        }
        
        .grade {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .grade-a { background-color: #28a745; color: white; }
        .grade-b { background-color: #17a2b8; color: white; }
        .grade-c { background-color: #ffc107; color: #000; }
        .grade-d { background-color: #fd7e14; color: white; }
        .grade-e { background-color: #dc3545; color: white; }
        .grade-n { background-color: #6c757d; color: white; }
        
        .summary {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #333;
            min-width: 120px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
        }
        
        .remarks {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #333;
            font-style: italic;
        }
        
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .approval-info {
            font-size: 10px;
        }
        
        .signature {
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 2px;
            min-width: 150px;
        }
        
        .signature-text {
            font-size: 9px;
            color: #666;
        }
        
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
    <div class="report-card">
        <!-- Header -->
        <div class="header">
            <h2>{{ $schoolInfo->school_name ?? 'ESA School' }}</h2>
            <p>{{ $schoolInfo->address ?? 'School Address' }}</p>
            <p>{{ $schoolInfo->phone ?? 'Phone Number' }}</p>
            <p><strong>ACADEMIC REPORT CARD</strong></p>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <div class="info-col">
                    <table class="info-table">
                        <tr>
                            <td>Student Name</td>
                            <td>{{ $reportCard->student->full_name }}</td>
                        </tr>
                        <tr>
                            <td>Registration No.</td>
                            <td>{{ $reportCard->student->registration_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Class/Course</td>
                            <td>{{ $reportCard->course->course_name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="info-col">
                    <table class="info-table">
                        <tr>
                            <td>Academic Year</td>
                            <td>{{ $reportCard->academicYear->year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Term</td>
                            <td>{{ $reportCard->term->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Date Issued</td>
                            <td>{{ $reportCard->updated_at->format('M j, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Academic Performance -->
        <div class="section-title">ACADEMIC PERFORMANCE</div>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Test Avg</th>
                    <th>Exam</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @if($reportCard->subjects && $reportCard->subjects->count() > 0)
                    @foreach($reportCard->subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_name ?? 'N/A' }}</td>
                            <td>{{ $subject->test_average ? number_format($subject->test_average, 1) : 'N/A' }}</td>
                            <td>{{ $subject->exam_mark ? number_format($subject->exam_mark, 1) : 'N/A' }}</td>
                            <td><strong>{{ $subject->total_mark ? number_format($subject->total_mark, 1) : 'N/A' }}</strong></td>
                            <td>
                                <span class="grade grade-{{ strtolower($subject->grade ?? 'n') }}">
                                    {{ $subject->grade ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ ($subject->grade === 'A' || $subject->grade === 'B') ? 'C' : 'NYC' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="text-align: center; font-style: italic;">
                            No subjects found for this report card
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <div class="summary-value">{{ $reportCard->total_marks ? number_format($reportCard->total_marks, 1) : 'N/A' }}</div>
                <div class="summary-label">Total Marks</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $reportCard->average_marks ? number_format($reportCard->average_marks, 1) : 'N/A' }}%</div>
                <div class="summary-label">Average</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $reportCard->position_display ?? 'N/A' }}</div>
                <div class="summary-label">Position</div>
            </div>
        </div>

        <!-- Remarks -->
        @if($reportCard->remarks)
        <div class="remarks">
            <strong>Remarks:</strong> {{ $reportCard->remarks }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="approval-info">
                <p><strong>Status:</strong> {{ ucfirst($reportCard->status) }}</p>
                @if($reportCard->approved_by)
                    <p><strong>Approved by:</strong> {{ $reportCard->approvedBy->name ?? 'Administrator' }}</p>
                    <p><strong>Approved on:</strong> {{ $reportCard->approved_at->format('M j, Y g:i A') }}</p>
                @endif
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-text">School Administrator Signature</div>
            </div>
        </div>
    </div>
</body>
</html>
