<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Excellent Servant Academy</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .title {
            color: #1e40af;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 16px;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .welcome-message {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            margin-bottom: 25px;
        }
        
        .credentials {
            background-color: #eff6ff;
            padding: 25px;
            border-radius: 8px;
            border: 2px solid #2563eb;
            margin: 25px 0;
        }
        
        .credentials h3 {
            color: #1e40af;
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .credential-item {
            margin-bottom: 15px;
        }
        
        .credential-label {
            font-weight: bold;
            color: #475569;
            margin-bottom: 5px;
        }
        
        .credential-value {
            background-color: #ffffff;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #cbd5e1;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            word-break: break-all;
        }
        
        .password-highlight {
            background-color: #fef3c7;
            border-color: #f59e0b;
            font-weight: bold;
            color: #92400e;
        }
        
        .next-steps {
            margin-top: 30px;
        }
        
        .next-steps h3 {
            color: #1e40af;
            margin-bottom: 15px;
        }
        
        .step-list {
            list-style: none;
            padding: 0;
        }
        
        .step-list li {
            margin-bottom: 12px;
            padding-left: 25px;
            position: relative;
        }
        
        .step-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #2563eb;
            font-weight: bold;
        }
        
        .login-button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        
        .login-button:hover {
            background-color: #1d4ed8;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        
        .security-note {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #991b1b;
        }
        
        .security-note h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #7f1d1d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Excellent Servant Academy</div>
            <h1 class="title">Welcome Aboard!</h1>
            <p class="subtitle">Your client account has been created successfully</p>
        </div>

        <div class="content">
            <div class="welcome-message">
                <p>Dear <strong>{{ $client->name }}</strong>,</p>
                <p>We're delighted to welcome you to Excellent Servant Academy! Your client account has been successfully created and you now have access to our comprehensive educational platform.</p>
            </div>

            <div class="credentials">
                <h3>🔐 Your Account Credentials</h3>
                
                <div class="credential-item">
                    <div class="credential-label">Email Address:</div>
                    <div class="credential-value">{{ $client->email }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Password:</div>
                    <div class="credential-value password-highlight">{{ $plainPassword }}</div>
                </div>
            </div>

            <div class="security-note">
                <h4>🔒 Important Security Notice</h4>
                <p>Please keep your password secure and do not share it with anyone. We recommend changing your password after your first login for added security.</p>
            </div>

            <div class="next-steps">
                <h3>📋 Next Steps</h3>
                <ul class="step-list">
                    <li><strong>Login to your account</strong> using the credentials above</li>
                    <li><strong>Update your password</strong> for enhanced security</li>
                    <li><strong>Complete your profile</strong> with additional information</li>
                    <li><strong>Explore our services</strong> including exams, resources, and subscriptions</li>
                    <li><strong>Contact support</strong> if you need any assistance</li>
                </ul>
                
                <div style="text-align: center;">
                    <a href="{{ route('client.auth.login') }}" class="login-button">
                        Login to Your Account
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>Need Help?</strong></p>
            <p>If you have any questions or encounter any issues, please don't hesitate to contact our support team:</p>
            <p>Email: support@excellentservantacademy.com | Phone: +250 788 123 456</p>
            <br>
            <p>© {{ date('Y') }} Excellent Servant Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
