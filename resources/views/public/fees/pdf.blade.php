<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Schedule - {{ $course->course_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 18px;
            color: #3498db;
            margin-top: 10px;
        }

        .course-info {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .course-info h2 {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .course-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
    font-size: 12px;
        }

        .detail-item {
            background: white;
            padding: 10px;
            border-left: 3px solid #3498db;
        }

        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 3px;
        }

        .detail-value {
            color: #555;
        }

        .fees-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #34495e;
            color: white;
            padding: 12px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #3498db;
            color: white;
        }

        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #3498db;
            font-size: 12px;
        }

        td {
            padding: 10px 12px;
            border: 1px solid #ecf0f1;
            font-size: 12px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #ecf0f1;
        }

        .amount {
            text-align: right;
            font-weight: 500;
        }

        .total-amount {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            font-size: 11px;
            color: #555;
            text-align: center;
        }

        .notes {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin: 20px 0;
            border-radius: 3px;
            font-size: 12px;
        }

        .notes-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }

        .notes-content {
            color: #856404;
            line-height: 1.5;
        }

        .text-center {
            text-align: center;
        }

        .printed-date {
            font-size: 11px;
            color: #999;
            margin-top: 20px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $schoolName }}</div>
            <div class="document-title">Fees Schedule</div>
        </div>

        <!-- Course Information -->
        <div class="course-info">
            <h2>Course Information</h2>
            <div class="course-details">
                <div class="detail-item">
                    <span class="detail-label">Course Name</span>
                    <span class="detail-value">{{ $course->course_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Course Code</span>
                    <span class="detail-value">{{ $course->course_code }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Abbreviation</span>
                    <span class="detail-value">{{ $course->abbreviation }}</span>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="notes">
            <div class="notes-title">📋 Important Information:</div>
            <div class="notes-content">
                <strong>For New Students (Non-Registered):</strong> The registration fee is a one-time payment required when you first enroll in the program. This is separate from the annual tuition and other term-based fees.<br><br>
                <strong>For Existing Students:</strong> You only need to pay the tuition, practical, trip, and student card fees. The registration fee does not apply to subsequent registrations.
            </div>
        </div>

        <!-- Fees Tables by Academic Year -->
        @foreach($fees as $academicYear => $yearFees)
            <div class="fees-section">
                <div class="section-title">
                    {{ $academicYear }}
                </div>

                <table>
                    <thead>
                        <tr>
                            <th width="20%">Term</th>
                            <th width="13%" class="amount">Registration Fee</th>
                            <th width="13%" class="amount">Tuition Fee</th>
                            <th width="13%" class="amount">Practical Fee</th>
                            <th width="13%" class="amount">Trip Fee</th>
                            <th width="13%" class="amount">Card Fee</th>
                            <th width="15%" class="amount">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $academicTotal = 0; @endphp
                        @foreach($yearFees as $fee)
                            @php
                                $total = $fee->registration_fee + $fee->tuition_fee + $fee->practical_fee + 
                                         $fee->trip_fee + $fee->student_card_fee;
                                $academicTotal += $total;
                            @endphp
                            <tr>
                                <td>{{ $fee->term->name }}</td>
                                <td class="amount">{{ number_format($fee->registration_fee, 0) }}</td>
                                <td class="amount">{{ number_format($fee->tuition_fee, 0) }}</td>
                                <td class="amount">{{ number_format($fee->practical_fee, 0) }}</td>
                                <td class="amount">{{ number_format($fee->trip_fee, 0) }}</td>
                                <td class="amount">{{ number_format($fee->student_card_fee, 0) }}</td>
                                <td class="amount" style="background-color: #e8f4f8; color: #2c3e50; font-weight: bold;">
                                    {{ number_format($total, 0) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-amount">
                            <td colspan="6" style="text-align: right;">Academic Year Total:</td>
                            <td class="amount">{{ number_format($academicTotal, 0) }}</td>
                        </tr>
                    </tbody>
                </table>

                @if($yearFees->first()?->notes)
                    <div class="notes" style="margin-bottom: 20px;">
                        <div class="notes-title">📝 Notes:</div>
                        <div class="notes-content">{{ $yearFees->first()->notes }}</div>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Footer -->
        <div class="footer">
            <p>This is an official fees schedule for the course listed above. All amounts are in Rwandan Francs (RWF).</p>
            <p class="printed-date">Printed on: {{ now()->format('F d, Y H:i') }}</p>
            <p>For more information, please visit our website or contact the admissions office.</p>
        </div>
    </div>
</body>
</html>
