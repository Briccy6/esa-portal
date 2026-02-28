<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ESA - Teacher Account</title>
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
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
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
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
        }
        
        .header-icon i {
            font-size: 36px;
            color: #ffffff;
        }
        
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
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
        
        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-section h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .welcome-section p {
            color: #7f8c8d;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .credentials-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            border-left: 4px solid #3498db;
        }
        
        .credentials-card h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .credentials-card h3 i {
            margin-right: 10px;
            color: #3498db;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .credential-item:last-child {
            border-bottom: none;
        }
        
        .credential-label {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .credential-value {
            font-family: 'Courier New', monospace;
            background: #ffffff;
            padding: 8px 12px;
            border-radius: 6px;
            color: #e74c3c;
            font-weight: 600;
            border: 1px solid #e74c3c;
        }
        
        .courses-section {
            margin: 30px 0;
        }
        
        .courses-section h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .courses-section h3 i {
            margin-right: 10px;
            color: #27ae60;
        }
        
        .course-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        
        .course-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .course-item:last-child {
            border-bottom: none;
        }
        
        .course-item i {
            color: #27ae60;
            margin-right: 10px;
        }
        
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
        }
        
        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .security-notice h4 {
            color: #856404;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .security-notice h4 i {
            margin-right: 10px;
        }
        
        .security-notice p {
            color: #856404;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .footer {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 30px;
            text-align: center;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .footer p {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        
        .footer-links {
            margin-top: 20px;
        }
        
        .footer-links a {
            color: #3498db;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .email-wrapper {
                margin: 10px;
                border-radius: 12px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .credentials-card {
                padding: 20px;
            }
            
            .credential-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .credential-value {
                margin-top: 8px;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Welcome to ESA!</h1>
            <p>Your Teacher Account is Ready</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h2>Dear {{ $teacher->teacher_name }},</h2>
                <p>
                    We're thrilled to welcome you to the ESA teaching team! Your account has been successfully created 
                    and you're now ready to start managing your classes and engaging with students.
                </p>
            </div>
            
            <!-- Login Credentials -->
            <div class="credentials-card">
                <h3><i class="fas fa-key"></i> Your Login Credentials</h3>
                
                <div class="credential-item">
                    <span class="credential-label">Email Address:</span>
                    <span class="credential-value">{{ $teacher->email }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Temporary Password:</span>
                    <span class="credential-value">{{ $plainPassword }}</span>
                </div>
            </div>
            
            <!-- Courses Section -->
            @if($courses && count($courses) > 0)
            <div class="courses-section">
                <h3><i class="fas fa-book"></i> Assigned Courses</h3>
                <div class="course-list">
                    @foreach($courses as $course)
                    <div class="course-item">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $course->course_name }} ({{ $course->abbreviation }})</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ route('teacher.login.form') }}" class="action-button">
                    <i class="fas fa-sign-in-alt"></i> Access Your Account
                </a>
            </div>
            
            <!-- Security Notice -->
            <div class="security-notice">
                <h4><i class="fas fa-shield-alt"></i> Security Notice</h4>
                <p>
                    For your security, please change your temporary password after your first login. 
                    Never share your login credentials with anyone. If you suspect unauthorized access, 
                    contact the administration immediately.
                </p>
            </div>
            
            <!-- Next Steps -->
            <div style="margin: 30px 0;">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">
                    <i class="fas fa-tasks"></i> What's Next?
                </h3>
                <ul style="color: #7f8c8d; line-height: 1.8; padding-left: 20px;">
                    <li>Log in with your credentials</li>
                    <li>Update your profile information</li>
                    <li>Change your temporary password</li>
                    <li>Explore your dashboard and features</li>
                    <li>Start managing your classes and resources</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">ESA</div>
            <p>Empowering Education Through Technology</p>
            <p style="font-size: 12px;">
                This is an automated message. Please do not reply to this email.
            </p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Support</a>
            </div>
        </div>
    </div>
</body>
</html>
