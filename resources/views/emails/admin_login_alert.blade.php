<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Alert</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)" /></svg>');
            opacity: 0.3;
        }
        
        .header h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .security-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .security-icon::before {
            content: '🔒';
            font-size: 24px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        
        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 4px solid #dc3545;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .info-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-title::before {
            content: 'ℹ️';
            font-size: 20px;
        }
        
        .info-list {
            list-style: none;
            padding: 0;
        }
        
        .info-list li {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .info-list li:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        
        .info-value {
            color: #6c757d;
            text-align: right;
            flex: 1;
            word-break: break-all;
        }
        
        .warning-section {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 4px solid #ffc107;
            text-align: center;
        }
        
        .warning-text {
            font-size: 16px;
            color: #856404;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .action-button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }
        
        .footer {
            background: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .footer-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-weight: 600;
            color: #495057;
            font-size: 16px;
        }
        
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #dc3545, transparent);
            margin: 20px 0;
            border-radius: 1px;
        }
        
        @media (max-width: 600px) {
            .email-wrapper {
                margin: 10px;
                border-radius: 8px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .info-list li {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .info-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="security-icon"></div>
            <h2>Security Alert</h2>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear {{ $admin->name ?? $admin->email }},
            </div>
            
            <div class="message">
                We detected a new login to your administrator account. For your security, we're notifying you of this activity.
            </div>
            
            <div class="info-card">
                <div class="info-title">Login Details</div>
                <ul class="info-list">
                    <li>
                        <span class="info-label">Time:</span>
                        <span class="info-value">{{ $loginTime }}</span>
                    </li>
                    <li>
                        <span class="info-label">IP Address:</span>
                        <span class="info-value">{{ $ipAddress }}</span>
                    </li>
                    <li>
                        <span class="info-label">Device/Browser:</span>
                        <span class="info-value">{{ $userAgent }}</span>
                    </li>
                </ul>
            </div>
            
            <div class="divider"></div>
            
            <div class="warning-section">
                <div class="warning-text">
                    If this login was <strong>not authorized by you</strong>, please take immediate action to secure your account.
                </div>
                <a href="{{ $logoutUrl }}" class="action-button">Secure My Account</a>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-text">
                This is an automated security notification from
            </div>
            <div class="company-name">ESA Administration Team</div>
        </div>
    </div>
</body>
</html>