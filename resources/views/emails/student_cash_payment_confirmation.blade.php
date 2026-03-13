<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .payment-info {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .payment-amount {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }
        
        .balance-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .balance-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .balance-item:last-child {
            margin-bottom: 0;
        }
        
        .balance-label {
            font-weight: 600;
            color: #666;
        }
        
        .balance-value {
            font-weight: bold;
            color: #333;
        }
        
        .warning {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .warning-text {
            color: #721c24;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .warning-deadline {
            font-weight: bold;
            color: #dc3545;
        }
        
        .thank-you {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 5px;
            color: #155724;
            font-weight: 500;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        
        .icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .header {
                padding: 20px;
            }
            
            .balance-item {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✅ Payment Confirmed</h1>
            <p>Your cash payment has been successfully received</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $student->full_name }}</strong>,
            </div>
            
            <div class="payment-info">
                <div class="payment-amount">
                    💰 Payment Received: <span style="color: #28a745;">{{ number_format($amountPaid, 2) }} RWF</span>
                </div>
                <p style="color: #666; font-size: 14px;">Thank you for your cash payment for <strong>{{ $feeType === 'school_fee' ? 'Tuition' : ucfirst($feeType) }} Fee</strong>. We have successfully processed and recorded your transaction.</p>
            </div>
            
            <div class="balance-info">
                <h3 style="margin-bottom: 15px; color: #856404; font-size: 16px;">📊 Fee Breakdown</h3>
                <div class="balance-item">
                    <span class="balance-label">Registration Fee:</span>
                    <span class="balance-value">{{ number_format($registrationFee, 2) }} RWF</span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Tuition Fee:</span>
                    <span class="balance-value">{{ number_format($tuitionFee, 2) }} RWF</span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Trip Fee:</span>
                    <span class="balance-value">{{ number_format($tripFee, 2) }} RWF</span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Practical Fee:</span>
                    <span class="balance-value">{{ number_format($practicalFee, 2) }} RWF</span>
                </div>
                @if($discountAmount > 0)
                <div class="balance-item">
                    <span class="balance-label">Discount Applied:</span>
                    <span class="balance-value" style="color: #28a745;">-{{ number_format($discountAmount, 2) }} RWF</span>
                </div>
                @endif
                <div class="balance-item" style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                    <span class="balance-label"><strong>Total Fees:</strong></span>
                    <span class="balance-value"><strong>{{ number_format($totalFees, 2) }} RWF</strong></span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Total Paid:</span>
                    <span class="balance-value" style="color: #28a745;">{{ number_format($paidFees, 2) }} RWF</span>
                </div>
                <div class="balance-item" style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                    <span class="balance-label"><strong>Remaining Balance:</strong></span>
                    <span class="balance-value" style="color: #dc3545;"><strong>{{ number_format($balance, 2) }} RWF</strong></span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Payment Deadline:</span>
                    <span class="balance-value">{{ $deadline->format('F j, Y') }}</span>
                </div>
            </div>
            
            <div class="warning">
                <h3 style="margin-bottom: 10px; color: #721c24; font-size: 16px;">⚠️ Important Notice</h3>
                <div class="warning-text">
                    Please note: You must pay the remaining balance of <strong>{{ number_format($balance, 2) }} RWF</strong> by <span class="warning-deadline">{{ $deadline->format('F j, Y') }}</span> (30 days from today). 
                    After this date, you will not have access to school facilities until your balance is cleared.
                </div>
            </div>
            
            <div class="thank-you">
                🙏 Thank you for your prompt payment!
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, please contact the school administration office.</p>
        </div>
    </div>
</body>
</html>