<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $transaction->tx_ref }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .receipt-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>Excellent Servant Academy</h1>
            <p>Kigali, Rwanda</p>
            <p>Tel: 0788525247 | Email: info@excellentservantacademy.com</p>
        </div>

        <div class="receipt-title">PAYMENT RECEIPT</div>

        <div class="info-row">
            <span class="info-label">Receipt No:</span>
            <span>{{ $transaction->tx_ref }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date:</span>
            <span>{{ $transaction->created_at->format('F d, Y H:i') }}</span>
        </div>

        <table class="table">
            <tr>
                <th>Description</th>
                <th>Details</th>
            </tr>
            <tr>
                <td><strong>Student Name</strong></td>
                <td>{{ $transaction->student->full_name }}</td>
            </tr>
            <tr>
                <td><strong>Registration Number</strong></td>
                <td>{{ $transaction->student->reg_number }}</td>
            </tr>
            <tr>
                <td><strong>Course</strong></td>
                <td>{{ $transaction->student->course->course_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Branch</strong></td>
                <td>{{ $transaction->student->branch->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Payment Type</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</td>
            </tr>
            <tr>
                <td><strong>Payment Method</strong></td>
                <td>
                    @php
                        $meta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true);
                        echo $meta['method'] ?? 'Unknown';
                    @endphp
                </td>
            </tr>
            @if($transaction->courseTermFee)
            <tr>
                <td><strong>Term</strong></td>
                <td>{{ $transaction->courseTermFee->term->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Academic Year</strong></td>
                <td>{{ $transaction->courseTermFee->term->academicYear->year ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>

        <div class="total-row">
            <div class="info-row">
                <span class="info-label">TOTAL AMOUNT PAID:</span>
                <span style="font-size: 18px;">{{ number_format($transaction->amount, 0) }} RWF</span>
            </div>
        </div>

        <div class="footer">
            <p>This receipt serves as proof of payment. Please keep it for your records.</p>
            <p>For any inquiries, contact the finance department.</p>
        </div>

        <div class="signature">
            <div class="signature-box">
                <div>Accountant Signature</div>
                <div>_____________________</div>
            </div>
            <div class="signature-box">
                <div>Student Signature</div>
                <div>_____________________</div>
            </div>
        </div>
    </div>
</body>
</html>
