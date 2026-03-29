<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESA Registration Confirmation</title>
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
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        .info-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 5px solid #2196f3;
            box-shadow: 0 4px 6px rgba(33, 150, 243, 0.1);
        }
        .info-box h3 {
            margin: 0 0 20px 0;
            color: #1e3a8a;
            font-size: 20px;
            font-weight: 600;
        }
        .info-item {
            margin: 15px 0;
            padding: 12px 0;
            border-bottom: 1px solid rgba(33, 150, 243, 0.1);
            display: flex;
            align-items: center;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #1e3a8a;
            display: inline-block;
            min-width: 160px;
            font-size: 14px;
        }
        .highlight {
            color: #1e3a8a;
            font-weight: 700;
            font-size: 16px;
            background: rgba(33, 150, 243, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
        .login-info {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border: 1px solid #ffeaa7;
            box-shadow: 0 4px 6px rgba(255, 193, 7, 0.1);
        }
        .login-info strong {
            color: #856404;
        }
        .important-notice {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        @media screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header, .content {
                padding: 25px 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
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
                <span class="info-label">🎫 Registration Number:</span>
                <span class="highlight">{{ $studentData['reg_number'] }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">🔢 SDMS Code:</span>
                <span class="highlight">{{ $studentData['sdms_code'] }}</span>
            </div>
            
            @if($studentData['nesa_reg_number'])
            <div class="info-item">
                <span class="info-label">📜 NESA Reg Number:</span>
                <span class="highlight">{{ $studentData['nesa_reg_number'] }}</span>
            </div>
            @endif
            
            @if($studentData['registration_date'])
            <div class="info-item">
                <span class="info-label">📅 Registration Date:</span>
                <span class="highlight">{{ $studentData['registration_date'] }}</span>
            </div>
            @endif
        </div>

        <div class="important-notice">
            <strong>📚 Important:</strong> Please login to your student dashboard immediately to view your complete NESA registration details and download any necessary examination documents.
        </div>

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
