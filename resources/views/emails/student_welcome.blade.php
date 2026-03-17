{{-- filepath: resources/views/emails/student_welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Excellent Servant Academy</title>
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
            min-height: 100vh;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        
        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .welcome-text {
            font-size: 16px;
            color: #555;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .credentials-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }
        
        .credentials-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .credentials-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .credential-item {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .credential-item:last-child {
            margin-bottom: 0;
        }
        
        .credential-label {
            color: white;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        
        .credential-value {
            color: white;
            font-size: 16px;
            font-weight: 500;
        }
        
        .password-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .password-display {
            flex: 1;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        
        .toggle-password {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .toggle-password:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-text {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .team-signature {
            font-weight: 600;
            color: #495057;
            margin-top: 10px;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .credentials-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">🎓 Excellent Servant Academy</div>
            <div class="subtitle">E-Learning Portal</div>
        </div>
        
        <div class="content">
            <div class="greeting">Welcome, {{ $student->full_name }}! 🎉</div>
            
            <p class="welcome-text">
                Congratulations! Your registration is complete and you're now part of our learning community. 
                We're excited to have you on board and can't wait to see you succeed in your educational journey.
            </p>
            
            <div class="credentials-card">
                <div class="credentials-title">🔐 Your Login Credentials</div>
                
                <div class="credential-item">
                    <div class="credential-label">Registration Number</div>
                    <div class="credential-value">{{ $student->reg_number }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Password</div>
                    <div class="password-container">
                        <div class="password-display">{{ $password }}</div>
                                           
                    </div>
                </div>
            </div>
            
            <div class="cta-section">
                <a href="{{ url('/student/login') }}" class="login-button">🚀 Start Learning Now</a>
            </div>
            
            <p class="welcome-text">
                <strong>What's next?</strong><br>
                Use your credentials to log in to the portal and explore our comprehensive courses, 
                interactive materials, and connect with fellow students and instructors.
            </p>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                Need help? Contact our support team or visit our help center.<br>
                This email contains sensitive information. Please keep it secure.
            </p>
            <p class="team-signature">
                Best regards,<br>
                The Excellent Servant Academy Team
            </p>
        </div>
    </div>
    
    </body>
</html>