<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Student Information - ESA Portal</title>
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
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #2c3e50;
            margin: 0;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            width: 150px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #555;
            font-size: 16px;
        }
        .highlight {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin: 20px 0;
        }
        .nesa-status {
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
        }
        .nesa-registered {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .nesa-not-registered {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .password-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .password-box strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ESA Portal</div>
            <h1 class="title">Your Student Information</h1>
        </div>

        <p>Dear <strong>{{ $student->full_name }}</strong>,</p>

        <p>This email contains your important student information for the ESA E-learning Portal. Please keep this information safe for your records.</p>

        <div class="info-section">
            <h3>Personal Information</h3>
            <p>
                <span class="info-label">Full Name:</span>
                <span class="info-value">{{ $student->full_name }}</span><br>
                <span class="info-label">Registration No:</span>
                <span class="info-value">{{ $student->reg_number }}</span><br>
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $student->email }}</span><br>
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $student->phone }}</span><br>
                <span class="info-label">Gender:</span>
                <span class="info-value">{{ $student->sex }}</span>
            </p>
        </div>

        <div class="info-section">
            <h3>Academic Information</h3>
            <p>
                <span class="info-label">Branch:</span>
                <span class="info-value">{{ $student->branch->name ?? 'N/A' }}</span><br>
                <span class="info-label">Course:</span>
                <span class="info-value">{{ $student->course->abbreviation ?? $student->course->course_name ?? 'N/A' }}</span><br>
                <span class="info-label">Academic Year:</span>
                <span class="info-value">{{ $student->academicYear->year ?? 'N/A' }}</span>
            </p>
        </div>

        <div class="password-box">
            <h3>🔐 Login Credentials</h3>
            <p>
                <strong>Username:</strong> {{ $student->email }}<br>
                <strong>Password:</strong> {{ $password }}
            </p>
            <p><strong>⚠️ Important:</strong> Please keep your password secure and do not share it with anyone.</p>
        </div>

        <div class="highlight">
            <h3>NESA Registration Status</h3>
            @if($student->nesa_registered)
                <div class="nesa-status nesa-registered">
                    ✅ Registered with NESA
                </div>
                <p>
                    <strong>NESA SDMS Code:</strong> {{ $student->nesa_sdms_code ?? 'N/A' }}<br>
                    <strong>NESA Registration No:</strong> {{ $student->nesa_reg_number ?? 'N/A' }}<br>
                    <strong>Registration Date:</strong> {{ $student->nesa_registration_date ? $student->nesa_registration_date->format('d M Y') : 'N/A' }}
                </p>
            @else
                <div class="nesa-status nesa-not-registered">
                    ⚠️ Not Registered with NESA
                </div>
                <p>You are not yet registered with the National Examination and School Inspection (NESA). Please contact the administration for assistance with NESA registration.</p>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/student/login') }}" class="login-button">
                🚀 Login to ESA Portal
            </a>
        </div>

        <div class="footer">
            <p><strong>ESA E-learning Portal</strong></p>
            <p>If you have any questions or need assistance, please contact the administration.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
