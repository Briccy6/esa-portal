<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript & Payment History</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .email-container {
            max-width: 800px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header styles */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="30" r="1.5" fill="white" opacity="0.1"/><circle cx="30" cy="80" r="1" fill="white" opacity="0.1"/><circle cx="70" cy="70" r="1.5" fill="white" opacity="0.1"/></svg>') repeat;
        }
        
        .school-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: 300;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        /* Content area */
        .content {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .greeting strong {
            color: #667eea;
            font-weight: 600;
        }
        
        .intro-text {
            margin-bottom: 30px;
            font-size: 16px;
            color: #5a6c7d;
            line-height: 1.7;
        }
        
        /* Summary section */
        .summary-list {
            background: linear-gradient(135deg, #f8faff 0%, #f1f5ff 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 5px solid #667eea;
            list-style: none;
        }
        
        .summary-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e8ecf0;
        }
        
        .summary-list li:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 18px;
            color: #2c3e50;
        }
        
        .summary-label {
            font-weight: 500;
            color: #4a5568;
        }
        
        .summary-amount {
            font-weight: 600;
            color: #2d3748;
            font-size: 16px;
        }
        
        .summary-discount {
            font-weight: 600;
            color: #38a169;
            font-size: 16px;
        }
        
        /* Section titles */
        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }
        
        /* Table styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .data-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        .data-table tbody tr:hover {
            background-color: #f8faff;
            transition: background-color 0.2s ease;
        }
        
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .amount {
            font-weight: 600;
            color: #2d3748;
        }
        
        .no-data {
            text-align: center;
            color: #a0aec0;
            font-style: italic;
            padding: 30px !important;
        }
        
        /* Grade styling */
        .grade {
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .grade-a { background: #d4edda; color: #155724; }
        .grade-b { background: #cce7ff; color: #004085; }
        .grade-c { background: #fff3cd; color: #856404; }
        .grade-d { background: #f8d7da; color: #721c24; }
        .grade-f { background: #f5c6cb; color: #721c24; }
        
        /* Footer */
        .footer {
            background: #f8faff;
            padding: 30px;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer p {
            font-size: 14px;
            color: #5a6c7d;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .signature {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            font-size: 14px;
            color: #4a5568;
        }
        
        .signature strong {
            color: #2c3e50;
            font-size: 16px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header {
                padding: 20px;
            }
            
            .school-name {
                font-size: 24px;
            }
            
            .document-title {
                font-size: 16px;
            }
            
            .content {
                padding: 20px;
            }
            
            .data-table {
                font-size: 12px;
            }
            
            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }
            
            .section-title {
                font-size: 18px;
            }
        }
        
        /* Print styles */
        @media print {
            body {
                background: white;
            }
            
            .email-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="school-name">{{ \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA School') }}</div>
            <p class="document-title">Academic Transcript & Payment History</p>
        </div>
        
        <div class="content">
            <p class="greeting">Dear <strong>{{ $student->full_name }}</strong>,</p>
            
            <p class="intro-text">We are pleased to provide you with your complete academic transcript and payment history. Please review the information below and contact us if you have any questions.</p>
            
            <!-- Payment Summary Section -->
            <ul class="summary-list">
                <li>
                    <span class="summary-label">Total to be Paid:</span>
                    <span class="summary-amount">Rwf{{ number_format($totalToBePaid, 2) }}</span>
                </li>
                @if(isset($discountAmount) && $discountAmount > 0)
                <li>
                    <span class="summary-label">Discount Applied:</span>
                    <span class="summary-discount">- Rwf{{ number_format($discountAmount, 2) }}</span>
                </li>
                @endif
                <li>
                    <span class="summary-label">Total Paid:</span>
                    <span class="summary-amount">Rwf{{ number_format($totalPaid, 2) }}</span>
                </li>
                <li>
                    <span class="summary-label">Outstanding Amount:</span>
                    <span class="summary-amount">Rwf{{ number_format($outstanding, 2) }}</span>
                </li>
            </ul>
            
            <h4 class="section-title">💳 Payment History</h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Amount Paid</th>
                        <th>Fee Type</th>
                        <th>Status</th>
                        <th>Date Paid</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $payment)
                        <tr>
                            <td class="amount">Rwf{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ucfirst($payment->type ?? 'N/A') }}</td>
                            <td>
                                <span style="color:{{ $payment->status === 'successful' ? '#38a169' : ($payment->status === 'pending' ? '#e6b800' : '#e53e3e') }};">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') : '-' }}</td>
                            <td>{{ $payment->tx_ref }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <h4 class="section-title">📚 Academic Transcript</h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Grade</th>
                        <th>Term</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transcripts as $transcript)
                        <tr>
                            <td>{{ $transcript->course->course_name ?? '-' }}</td>
                            <td>{{ $transcript->score ?? '-' }}%</td>
                            <td>
                                @if($transcript->grade)
                                    <span class="grade grade-{{ strtolower($transcript->grade) }}">
                                        {{ $transcript->grade }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($transcript->course && $transcript->course->academicYear && $transcript->course->academicYear->terms->count())
                                    {{ $transcript->course->academicYear->terms->pluck('term')->join(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="no-data">No transcript records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p>This document was generated automatically on {{ now()->format('F j, Y \a\t g:i A') }}. If you notice any discrepancies or have questions about your records, please contact our academic office immediately.</p>
            
            <div class="signature">
                Best regards,<br>
                <strong>{{ \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA School') }}</strong><br>
                Academic Records Department
            </div>
        </div>
    </div>
</body>
</html>