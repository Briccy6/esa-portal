<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Expense Recorded</title>
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
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .header-icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .content {
            padding: 40px 35px;
        }
        
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 5px solid #f39c12;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .alert-text {
            color: #856404;
            font-size: 14px;
            font-weight: 500;
            margin: 0;
        }
        
        .expense-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 0;
            margin: 25px 0;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .details-header {
            background: #343a40;
            color: white;
            padding: 18px 25px;
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            padding: 18px 25px;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }
        
        .detail-row:hover {
            background-color: #f1f3f4;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-value {
            color: #212529;
            font-size: 14px;
            font-weight: 500;
            word-break: break-word;
        }
        
        .amount-highlight {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 16px;
            display: inline-block;
        }
        
        .payment-method {
            background: #007bff;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .expense-type {
            background: #6f42c1;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .action-section {
            background: #e8f4fd;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
            text-align: center;
        }
        
        .action-title {
            color: #0c5460;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .action-text {
            color: #0c5460;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-text {
            color: #6c757d;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }
        
        .timestamp {
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .detail-row {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 15px 20px;
            }
            
            .detail-label {
                font-size: 13px;
                margin-bottom: 5px;
            }
            
            .detail-value {
                font-size: 13px;
            }
            
            .details-header {
                padding: 15px 20px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="header-content">
                <span class="header-icon">💸</span>
                <h1>New Expense Recorded</h1>
                <p>A new expense has been added to the system</p>
            </div>
        </div>
        
        <div class="content">
            <div class="alert-box">
                <p class="alert-text">
                    ⚠️ <strong>Admin Notification:</strong> This expense requires your attention and review.
                </p>
            </div>
            
            <div class="expense-details">
                <h3 class="details-header">📋 Expense Details</h3>
                <div class="details-grid">
                    <div class="detail-row">
                        <div class="detail-label">
                            📅 <span>Date:</span>
                        </div>
                        <div class="detail-value">
                            <span class="timestamp">{{ $expense->expense_date }}</span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            🏷️ <span>Type:</span>
                        </div>
                        <div class="detail-value">
                            <span class="expense-type">{{ $expense->expense_type }}</span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            💰 <span>Amount:</span>
                        </div>
                        <div class="detail-value">
                            <span class="amount-highlight">Rwf {{ number_format($expense->amount, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            📝 <span>Description:</span>
                        </div>
                        <div class="detail-value">{{ $expense->description }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            👤 <span>Paid By:</span>
                        </div>
                        <div class="detail-value">{{ $expense->paid_by }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            🏢 <span>Paid To:</span>
                        </div>
                        <div class="detail-value">{{ $expense->paid_to }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            📞 <span>Contact:</span>
                        </div>
                        <div class="detail-value">{{ $expense->paid_to_contact }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            💳 <span>Payment Method:</span>
                        </div>
                        <div class="detail-value">
                            <span class="payment-method">{{ ucfirst($expense->payment_method) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="action-section">
                <h4 class="action-title">📊 Review Required</h4>
                <p class="action-text">
                    Please review this expense entry and verify all details are correct.
                </p>
                <a href="#" class="btn-primary">View in Dashboard</a>
            </div>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                This is an automated notification from your expense management system.<br>
                Please do not reply to this email. For support, contact your system administrator.
            </p>
        </div>
    </div>
</body>
</html>