<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Notification - ESA</title>
    <style>
        /* Reset styles for better email compatibility */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
        }
        
        .header-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            position: relative;
            z-index: 1;
        }
        
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .notification-message {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%);
            border-left: 4px solid #27ae60;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
            position: relative;
        }
        
        .notification-message::before {
            content: '✅';
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 20px;
        }
        
        .notification-message p {
            margin: 0;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .login-details-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }
        
        .login-details-card::before {
            content: '🔍';
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            opacity: 0.3;
        }
        
        .details-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .details-title::before {
            content: '📊';
            margin-right: 10px;
        }
        
        .details-grid {
            display: grid;
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-weight: 600;
            color: #667eea;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .detail-value {
            color: #2c3e50;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            background: #ffffff;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            border: 1px solid #e0e7ff;
        }
        
        .security-alert {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border: 2px solid #feb2b2;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
            position: relative;
        }
        
        .security-alert::before {
            content: '🚨';
            font-size: 32px;
            display: block;
            margin-bottom: 10px;
        }
        
        .security-alert h3 {
            color: #c53030;
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .security-alert p {
            color: #742a2a;
            margin-bottom: 20px;
            font-size: 15px;
        }
        
        .logout-button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .logout-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .logout-button:hover::before {
            left: 100%;
        }
        
        .support-section {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .support-section h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .support-section p {
            margin: 0;
            color: #5a6c7d;
            font-size: 14px;
        }
        
        .professional-note {
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
            border: 1px solid #ffcc02;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            position: relative;
        }
        
        .professional-note::before {
            content: '💼';
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 20px;
        }
        
        .professional-note h4 {
            color: #e65100;
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: 600;
        }
        
        .professional-note p {
            color: #bf360c;
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 30px;
            text-align: center;
            color: #ecf0f1;
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .footer-team {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .footer-timestamp {
            font-size: 12px;
            opacity: 0.6;
            font-family: 'Courier New', monospace;
        }
        
        /* Responsive design */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .login-details-card, .security-alert {
                padding: 20px;
            }
            
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .detail-icon {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="header-icon">🔐</div>
            <h1>Login Notification</h1>
            <p>Educational Success Academy</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Welcome back, {{ $teacher->teacher_name }}!
            </div>
            
            <div class="notification-message">
                <p>
                    <strong>Login Successful:</strong> You have successfully logged into your ESA account. This email is to notify and confirm your recent login activity.
                </p>
            </div>
            
            <div class="professional-note">
                <h4>Professional Notification</h4>
                <p>
                    As a valued member of our educational team, we monitor all account access for security purposes. This automated notification helps ensure your account remains secure and allows you to quickly identify any unauthorized access attempts.
                </p>
            </div>
            
            <div class="login-details-card">
                <div class="details-title">
                    Login Session Details
                </div>
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-icon">🕐</div>
                        <div class="detail-content">
                            <div class="detail-label">Login Time</div>
                            <div class="detail-value">{{ $loginTime }}</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">🌐</div>
                        <div class="detail-content">
                            <div class="detail-label">IP Address</div>
                            <div class="detail-value">{{ $ipAddress }}</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">💻</div>
                        <div class="detail-content">
                            <div class="detail-label">Device/Browser</div>
                            <div class="detail-value">{{ $userAgent }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="security-alert">
                <h3>Security Alert</h3>
                <p>
                    If this login was not authorized by you, please take immediate action to secure your account.
                </p>
                <a href="{{ $logoutUrl }}" class="logout-button">🚪 Logout Immediately</a>
            </div>
            
            <div class="support-section">
                <h4>🛡️ Security & Support</h4>
                <p>
                    If you notice any suspicious activity or have concerns about your account security, please contact our IT support team immediately. We take account security seriously and are here to help protect your professional access.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-content">
                <div class="footer-logo">ESA</div>
                <div class="footer-team">Administration & Security Team</div>
                <div class="footer-timestamp">Notification sent automatically</div>
            </div>
        </div>
    </div>
</body>
</html>