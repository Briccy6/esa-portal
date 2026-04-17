<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Reports - {{ $reportCards->first()?->course->course_name ?? 'Class' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            width: 100%;
            height: 100%;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Main content wrapper - centered A4 layout */
        body > * {
            max-width: 170mm;
            margin-left: auto;
            margin-right: auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            overflow: visible;
        }
        
        .school-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            overflow: visible;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            flex-shrink: 0;
        }
        
        .school-details {
            flex: 1;
            text-align: center;
            margin: 0 15px;
            min-width: 0;
            box-sizing: border-box;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .school-address {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }
        
        .school-contact {
            font-size: 8px;
            color: #666;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .class-info {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
            color: #2c3e50;
        }
        
        .student-report {
            page-break-after: always;
            margin-bottom: 30px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            overflow: visible;
        }
        
        .student-report:last-child {
            page-break-after: auto;
        }

        .term-section-banner {
            text-align: center;
            padding: 10px 12px;
            margin: 0 0 14px 0;
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .term-section-banner strong {
            font-size: 12px;
            color: #212529;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 3px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            overflow: visible;
            min-width: 0;
        }

        .student-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .student-photo {
            width: 48px;
            height: 48px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .student-photo-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 4px;
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #999;
        }
        
        .student-name {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .student-details {
            font-size: 9px;
            color: #666;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
            table-layout: fixed;
        }
        
        .marks-table th {
            background: #34495e;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #2c3e50;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .marks-table td {
            padding: 4px;
            text-align: center;
            border: 1px solid #ddd;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .marks-table .subject-name {
            text-align: left;
            font-weight: 500;
        }
        
        .marks-table .total-row {
            background: #ecf0f1;
            font-weight: bold;
        }
        
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 9px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            overflow: visible;
            gap: 3px;
        }
        
        .summary-section {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 3px;
            flex: 1;
            margin: 0;
            box-sizing: border-box;
            word-wrap: break-word;
            overflow-wrap: break-word;
            min-width: 0;
        }
        
        .summary-section h4 {
            font-size: 10px;
            margin-bottom: 5px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            gap: 10px;
        }
        
        .signature-box {
            text-align: center;
            flex: 1;
            min-width: 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 3px;
            height: 20px;
        }
        
        /* Global constraints to prevent content overflow */
        div, section, article, header, footer, main, nav, ul, ol, li, p, span, a, table {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        img, svg, canvas, video, iframe {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
                height: 100%;
            }
            
            body {
                background: white;
            }
            
            body > * {
                max-width: 170mm;
                margin-left: auto;
                margin-right: auto;
            }
            
            .student-report {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        $byTerm = $reportCards->sortBy(function ($r) {
            return ($r->student->full_name ?? '').'_'.$r->term_id;
        })->groupBy('term_id');
    @endphp
    @foreach($byTerm as $termId => $cardsForTerm)
        @if(!$loop->first)
            <div style="page-break-before: always;"></div>
        @endif
        @php $bannerTerm = $cardsForTerm->first()->term; @endphp
        <div class="term-section-banner">
            <strong>{{ $bannerTerm->name ?? ('Term #'.$termId) }}</strong>
        </div>
        @foreach($cardsForTerm as $reportCard)
        <div class="student-report">
            <div class="header">
                <div class="school-info">
                    @if($schoolInfo && $schoolInfo->logo_url)
                        <img src="{{ $schoolInfo->logo_url }}" alt="School Logo" class="logo">
                    @else
                        <div style="width: 60px; height: 60px; background: #ecf0f1; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <span style="color: #7f8c8d; font-size: 8px;">LOGO</span>
                        </div>
                    @endif
                    
                    <div class="school-details">
                        <div class="school-name">
                            {{ $schoolInfo->school_name ?? 'SCHOOL NAME' }}
                        </div>
                        <div class="school-address">
                            {{ $schoolInfo->address ?? 'School Address' }}
                        </div>
                        <div class="school-contact">
                            @if($schoolInfo->phone) Tel: {{ $schoolInfo->phone }} @endif
                            @if($schoolInfo->email) | Email: {{ $schoolInfo->email }} @endif
                        </div>
                    </div>
                    
                    <div style="width: 60px;"></div>
                </div>
                
                <div class="report-title">
                    Report Card
                </div>
                
                <div class="class-info">
                    {{ $reportCard->course->course_name }} | {{ $reportCard->academicYear->year }} | {{ $reportCard->term->name ?? ('Term #'.$reportCard->term_id) }}
                </div>
            </div>

            <div class="student-header">
                <div class="student-header-left">
                    @php
                        $studentPhoto = $studentPhotosByReportId[$reportCard->id] ?? null;
                    @endphp
                    @if(!empty($studentPhoto['base64']))
                        <img src="data:{{ $studentPhoto['mime'] ?? 'image/jpeg' }};base64,{{ $studentPhoto['base64'] }}" alt="Student Photo" class="student-photo">
                    @else
                        <div class="student-photo-placeholder">
                            PHOTO
                        </div>
                    @endif
                    <div>
                        <div class="student-name">{{ $reportCard->student->full_name }}</div>
                        <div class="student-details">
                            Reg: {{ $reportCard->student->reg_number ?? 'N/A' }} | 
                            Sex: {{ ucfirst($reportCard->student->sex ?? 'N/A') }} |
                            Position: {{ $reportCard->position_display }}
                        </div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div class="student-details">
                        Status: {{ ucfirst($reportCard->status) }}<br>
                        @if($reportCard->approved_at)
                        Approved: {{ $reportCard->approved_at->format('d M Y') }}
                        @endif
                    </div>
                </div>
            </div>

            <table class="marks-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Subject</th>
                        <th style="width: 18%;">Exam (60%)</th>
                        <th style="width: 18%;">Test Avg (40%)</th>
                        <th style="width: 12%;">Total</th>
                        <th style="width: 22%;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportCard->subjects as $subject)
                        <tr>
                            <td class="subject-name">{{ $subject->subject_name }}</td>
                            <td>{{ number_format($subject->exam_mark, 2) }}</td>
                            <td>{{ number_format($subject->test_average, 2) }}</td>
                            <td><strong>{{ number_format($subject->total_mark, 2) }}</strong></td>
                            <td>{{ $subject->remarks ?? '-' }}</td>
                        </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td><strong>TOTAL/AVERAGE</strong></td>
                        <td><strong>{{ number_format($reportCard->subjects->avg('exam_mark'), 2) }}</strong></td>
                        <td><strong>{{ number_format($reportCard->subjects->avg('test_average'), 2) }}</strong></td>
                        <td><strong>{{ number_format($reportCard->average_marks, 2) }}</strong></td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-box">
                <div class="summary-section">
                    <h4>Performance</h4>
                    <div class="summary-item">
                        <span>Subjects:</span>
                        <strong>{{ $reportCard->subjects->count() }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>Average:</span>
                        <strong>{{ number_format($reportCard->average_marks, 2) }}%</strong>
                    </div>
                </div>
                
                <div class="summary-section">
                    <h4>Class Standing</h4>
                    <div class="summary-item">
                        <span>Position:</span>
                        <strong>{{ $reportCard->class_position ?? 'N/A' }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>Total Students:</span>
                        <strong>{{ $reportCard->total_students }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>Term:</span>
                        <strong>{{ $reportCard->term->name ?? ('#'.$reportCard->term_id) }}</strong>
                    </div>
                </div>
            </div>

            @if($reportCard->remarks)
                <div style="background: #fff3cd; padding: 8px; border-radius: 3px; margin-bottom: 10px; font-size: 9px;">
                    <strong>Remarks:</strong> {{ $reportCard->remarks }}
                </div>
            @endif

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        @if($schoolInfo && $schoolInfo->principal_signature_url)
                            <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal Signature" style="max-height: 20px;">
                        @endif
                        @if($schoolInfo && $schoolInfo->stamp_signature_url)
                            <img src="{{ $schoolInfo->stamp_signature_url }}" alt="School Stamp" style="max-height: 20px; margin-left: 6px;">
                        @endif
                    </div>
                    <div>
                        {{ $schoolInfo->principal_name ?? 'Principal' }}<br>
                        Principal Signature
                    </div>
                </div>
                
                <div class="signature-box">
                    <div class="signature-line">
                        @if($reportCard->approvedBy)
                            <img src="{{ asset('storage/' . $reportCard->approvedBy->signature ?? '') }}" alt="Teacher Signature" style="max-height: 20px;">
                        @endif
                    </div>
                    <div>
                        {{ $reportCard->approvedBy->name ?? 'Class Teacher' }}<br>
                        Class Teacher Signature
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endforeach
</body>
</html>
