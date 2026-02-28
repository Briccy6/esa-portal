<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Team</title>
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
            background-color: #f0f2f5;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="welcome-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23welcome-pattern)"/></svg>');
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .welcome-icon {
            font-size: 60px;
            margin-bottom: 15px;
            display: block;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.95;
            font-weight: 300;
        }
        
        .content {
            padding: 40px 35px;
        }
        
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .staff-name {
            color: #667eea;
            font-weight: 700;
        }
        
        .intro-text {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .account-details {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0fe 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 25px 0;
            border: 1px solid #e1e8f0;
            position: relative;
        }
        
        .details-header {
            color: #667eea;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-grid {
            display: grid;
            gap: 15px;
        }
        
        .detail-item {
            background: white;
            padding: 18px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .detail-icon {
            font-size: 20px;
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .detail-value {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        
        .password-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .password-warning-text {
            color: #856404;
            font-size: 14px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .password-highlight {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
            display: inline-block;
            margin-top: 5px;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .role-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .cta-section {
            text-align: center;
            margin: 35px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0fe 100%);
            border-radius: 12px;
        }
        
        .cta-title {
            color: #333;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .cta-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }
        
        .login-button {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px 35px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 123, 255, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .security-notice {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .security-text {
            color: #0c5460;
            font-size: 14px;
            margin: 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .closing-section {
            text-align: center;
            margin-top: 35px;
            padding: 25px;
            background: linear-gradient(135deg, #f1f3f4 0%, #e8eaf6 100%);
            border-radius: 10px;
        }
        
        .closing-text {
            color: #555;
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .team-signature {
            color: #667eea;
            font-weight: 700;
            font-size: 18px;
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
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 26px;
            }
            
            .account-details {
                padding: 20px;
            }
            
            .login-button {
                padding: 12px 25px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="header-content">
                <span class="welcome-icon">🎉</span>
                <h1>Welcome to Our Team!</h1>
                <p>We're excited to have you on board</p>
            </div>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello <span class="staff-name">{{ $staff->name }}</span>,
            </div>
            
            <div class="intro-text">
                Congratulations! Your staff account has been successfully created. We're thrilled to welcome you to our team and look forward to working with you.
            </div>
            
            <div class="account-details">
                <div class="details-header">
                    🔐 Your Account Details
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-icon">📧</div>
                        <div class="detail-content">
                            <div class="detail-label">Email Address</div>
                            <div class="detail-value">{{ $staff->email }}</div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">👤</div>
                        <div class="detail-content">
                            <div class="detail-label">Your Role</div>
                            <div class="detail-value">
                                <span class="role-badge">{{ $staff->role }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">🔑</div>
                        <div class="detail-content">
                            <div class="detail-label">Temporary Password</div>
                            <div class="detail-value">
                                <span class="password-highlight">{{ $password }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="password-warning">
                <p class="password-warning-text">
                    ⚠️ <strong>Important:</strong> Please change your password immediately after your first login for security purposes.
                </p>
            </div>
            
            <div class="cta-section">
                <h3 class="cta-title">Ready to Get Started?</h3>
                <p class="cta-description">
                    Click the button below to access your account and begin your journey with us.
                </p>
                <a href="{{ $loginUrl }}" class="login-button">
                    🚀 Login to Your Account
                </a>
            </div>
            
            <div class="security-notice">
                <p class="security-text">
                    🔒 <strong>Security Notice:</strong> This login link will expire after your first use. Please bookmark the regular login page after accessing your account.
                </p>
            </div>
            
            <div class="closing-section">
                <p class="closing-text">
                    Welcome aboard! We're here to support you every step of the way.
                </p>
                <p class="team-signature">
                    The Management Team
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                This is an automated welcome message from your staff management system.<br>
                If you have any questions or need assistance, please contact your system administrator.<br>
                Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>