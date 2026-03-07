<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESA Registration Confirmation</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e0e0e0;
        }
        .info-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .info-item {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #2196f3;
            display: inline-block;
            width: 150px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
        .highlight {
            color: #2196f3;
            font-weight: bold;
        }
        .login-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 NESA Registration Confirmed!</h1>
        <p>You are now registered for the National Examination</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $studentData['name'] }}</strong>,</p>
        
        <p>We are pleased to inform you that you have been successfully registered with the National Examination and Accreditation Authority (NESA) for the upcoming national examinations.</p>

        <div class="info-box">
            <h3>📋 Your NESA Registration Details:</h3>
            
            <div class="info-item">
                <span class="info-label">Registration Number:</span>
                <span class="highlight">{{ $studentData['reg_number'] }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">SDMS Code:</span>
                <span class="highlight">{{ $studentData['sdms_code'] }}</span>
            </div>
            
            @if($studentData['nesa_reg_number'])
            <div class="info-item">
                <span class="info-label">NESA Reg Number:</span>
                <span class="highlight">{{ $studentData['nesa_reg_number'] }}</span>
            </div>
            @endif
            
            @if($studentData['registration_date'])
            <div class="info-item">
                <span class="info-label">Registration Date:</span>
                <span class="highlight">{{ $studentData['registration_date'] }}</span>
            </div>
            @endif
        </div>

        <p>📚 <strong>Important:</strong> Please login to your student dashboard to view your complete NESA registration details and download any necessary examination documents.</p>

        <div class="login-info">
            <strong>🔐 Login Instructions:</strong><br>
            Visit your student portal and login with your registered email and password to access your NESA registration information and examination materials.
        </div>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>For any questions or assistance, please contact the school administration.</p>
        <p>© {{ date('Y') }} ESA Portal - All Rights Reserved</p>
    </div>
</body>
</html>
