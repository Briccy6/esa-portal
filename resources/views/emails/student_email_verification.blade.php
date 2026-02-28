{{-- filepath: resources/views/emails/student_email_verification.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Excellent Servant Academy</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .verification-message {
            font-size: 18px;
            color: #333333;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .verification-code {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 6px;
            padding: 20px 40px;
            border-radius: 12px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            border: 3px solid #ffffff;
        }
        
        .expiry-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 30px 0;
            color: #856404;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .expiry-notice::before {
            content: "⏰";
            margin-right: 8px;
            font-size: 16px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .security-notice {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px 20px;
            margin: 25px 0;
            font-size: 14px;
            color: #0056b3;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        /* Responsive design */
        @media (max-width: 600px) {
            .email-container {
                margin: 20px;
                border-radius: 8px;
            }
            
            .header, .content {
                padding: 30px 20px;
            }
            
            .verification-code {
                font-size: 28px;
                letter-spacing: 4px;
                padding: 18px 30px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">🎓</div>
            <h1>Excellent Servant Academy</h1>
            <p>Email Verification Required</p>
        </div>
        
        <div class="content">
            <p class="verification-message">
                Please use the verification code below to complete your account setup:
            </p>
            
            <div class="verification-code">{{ $code }}</div>
            
            <div class="expiry-notice">
                This verification code will expire in 10 minutes
            </div>
            
            <div class="security-notice">
                <strong>Security Notice:</strong> If you didn't request this verification code, please ignore this email. Never share your verification code with anyone.
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Excellent Servant Academy. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>