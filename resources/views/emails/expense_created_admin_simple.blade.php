<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Expense Recorded</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background: #f9f9f9;
        }
        .expense-details {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .detail-row {
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .amount {
            color: #dc3545;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💸 New Expense Recorded</h1>
        <p>A new expense has been added to the system</p>
    </div>
    
    <div class="content">
        <p><strong>Admin Notification:</strong> This expense requires your attention and review.</p>
        
        <div class="expense-details">
            <h3>📋 Expense Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">📅 Date:</span> {{ $expense->expense_date }}
            </div>
            
            <div class="detail-row">
                <span class="detail-label">🏷️ Type:</span> {{ $expense->expense_type }}
            </div>
            
            <div class="detail-row">
                <span class="detail-label">💰 Amount:</span> 
                <span class="amount">Rwf {{ number_format($expense->amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📝 Description:</span> {{ $expense->description }}
            </div>
            
            <div class="detail-row">
                <span class="detail-label">👤 Paid By:</span> {{ $expense->paid_by }}
            </div>
            
            <div class="detail-row">
                <span class="detail-label">🏢 Paid To:</span> {{ $expense->paid_to }}
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📞 Contact:</span> {{ $expense->paid_to_contact }}
            </div>
            
            <div class="detail-row">
                <span class="detail-label">💳 Payment Method:</span> {{ ucfirst($expense->payment_method) }}
            </div>
        </div>
        
        <p style="text-align: center; margin-top: 20px;">
            Please review this expense entry and verify all details are correct.
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from your expense management system.</p>
        <p>Please do not reply to this email. For support, contact your system administrator.</p>
    </div>
</body>
</html>
