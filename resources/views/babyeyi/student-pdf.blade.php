<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $babyeyi->reference_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 12pt;
        }

        .container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.5in;
        }

        /* Header Section */
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            page-break-after: avoid;
        }

        .school-logo {
            max-height: 70px;
            margin-bottom: 10px;
        }

        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 5px 0;
        }

        .school-info {
            font-size: 10pt;
            color: #555;
            margin: 2px 0;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 25px 0;
            color: #1a1a1a;
            page-break-after: avoid;
        }

        /* Content */
        .content {
            margin: 20px 0;
            line-height: 1.8;
        }

        .content p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .content h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #1a1a1a;
            page-break-after: avoid;
        }

        .content h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #333;
            page-break-after: avoid;
        }

        .content h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 5px 0;
            color: #555;
            page-break-after: avoid;
        }

        .content ul,
        .content ol {
            margin: 10px 0 10px 30px;
        }

        .content li {
            margin-bottom: 6px;
        }

        .content table {
            margin: 15px 0;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .content table td,
        .content table th {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        .content table th {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .content strong {
            font-weight: bold;
        }

        .content em {
            font-style: italic;
        }

        .content u {
            text-decoration: underline;
        }

        /* Fee Details Section */
        .fee-section {
            margin: 30px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .fee-section h4 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 12px;
            color: #1a1a1a;
        }

        .fee-table {
            margin: 15px 0;
            width: 100%;
            border-collapse: collapse;
        }

        .fee-table thead {
            background-color: #e8e8e8;
        }

        .fee-table td,
        .fee-table th {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11pt;
        }

        .fee-table th {
            font-weight: bold;
            text-align: left;
        }

        .fee-table td:nth-child(2) {
            text-align: right;
        }

        .fee-table .total-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .warning-text {
            color: #d32f2f;
            font-weight: bold;
            margin: 10px 0;
        }

        /* Footer Section */
        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #ccc;
            page-break-inside: avoid;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-line {
            margin-bottom: 40px;
            border-bottom: 1px solid #000;
            min-height: 45px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 5px;
        }

        .signature-sub {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }

        /* Info Box */
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin: 15px 0;
            font-size: 11pt;
        }

        .info-box strong {
            color: #007bff;
        }

        /* Page Break Handling */
        @page {
            margin: 0.5in;
            @bottom-center {
                content: counter(page) " of " counter(pages);
                font-size: 10pt;
                color: #999;
            }
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with School Information -->
        <div class="header">
            @if ($schoolInfo && $schoolInfo->logo_path)
                <img src="{{ public_path('storage/' . $schoolInfo->logo_path) }}" class="school-logo" alt="School Logo">
            @endif
            <div class="school-name">{{ $schoolInfo->name ?? 'SCHOOL NAME' }}</div>
            @if ($schoolInfo->location)
                <div class="school-info">{{ $schoolInfo->location }}</div>
            @endif
            @if ($schoolInfo->phone)
                <div class="school-info">Tel: {{ $schoolInfo->phone }}</div>
            @endif
            @if ($schoolInfo->email)
                <div class="school-info">Email: {{ $schoolInfo->email }}</div>
            @endif
        </div>

        <!-- Document Title -->
        <div class="document-title">
            BABYEYI
            @if ($babyeyi->title)
                <div style="font-size: 12pt; font-weight: normal; margin-top: 5px;">{{ $babyeyi->title }}</div>
            @endif
        </div>

        <!-- Document Metadata -->
        <div class="info-box">
            <strong>Reference:</strong> {{ $babyeyi->reference_number }} | 
            <strong>Term:</strong> {{ $babyeyi->term->name ?? 'N/A' }} | 
            <strong>Academic Year:</strong> {{ $babyeyi->academicYear->name ?? 'N/A' }}
        </div>

        <!-- Main Content -->
        <div class="content">
            {!! $babyeyi->content !!}
        </div>

        <!-- Fee Details Section for Student -->
        @if ($courseTermFees)
            <div class="fee-section">
                <h4>YOUR FEE STRUCTURE FOR THIS TERM</h4>
                
                <table class="fee-table">
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Amount (RWF)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($courseTermFees->registration_fee > 0)
                            <tr>
                                <td>Registration Fee</td>
                                <td>{{ number_format($courseTermFees->registration_fee, 2) }}</td>
                            </tr>
                        @endif
                        @if ($courseTermFees->tuition_fee > 0)
                            <tr>
                                <td>Tuition Fee</td>
                                <td>{{ number_format($courseTermFees->tuition_fee, 2) }}</td>
                            </tr>
                        @endif
                        @if ($courseTermFees->practical_fee > 0)
                            <tr>
                                <td>Practical Fee</td>
                                <td>{{ number_format($courseTermFees->practical_fee, 2) }}</td>
                            </tr>
                        @endif
                        @if ($courseTermFees->trip_fee > 0)
                            <tr>
                                <td>Trip Fee</td>
                                <td>{{ number_format($courseTermFees->trip_fee, 2) }}</td>
                            </tr>
                        @endif
                        @if ($courseTermFees->student_card_fee > 0)
                            <tr>
                                <td>Student Card Fee</td>
                                <td>{{ number_format($courseTermFees->student_card_fee, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>TOTAL FEES DUE</td>
                            <td>
                                {{ number_format($courseTermFees->registration_fee + $courseTermFees->tuition_fee + $courseTermFees->practical_fee + $courseTermFees->trip_fee + $courseTermFees->student_card_fee, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="warning-text">
                    ⚠️ All fees must be paid before the start of the term to attend classes.
                </p>
            </div>
        @endif

        <!-- Footer with Signature Space -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Head Teacher</div>
                    <div class="signature-sub">Signature & School Stamp</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Date</div>
                    <div class="signature-sub">{{ date('F j, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Document Information Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 9pt; color: #999; text-align: center;">
            <p>This is a personalized babyeyi document for {{ $student->full_name ?? 'Student' }} ({{ $student->reg_number ?? 'Reg. No.' }})</p>
            <p>Course: {{ $student->course->name ?? 'N/A' }}</p>
            <p>Reference: {{ $babyeyi->reference_number }} | Generated: {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
