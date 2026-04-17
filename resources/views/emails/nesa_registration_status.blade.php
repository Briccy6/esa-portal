<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESA Registration Status</title>
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
        }
        .status-approved {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }
        .status-approved h2 {
            margin: 0 0 15px 0;
            font-size: 24px;
            color: white;
        }
        .status-rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
        }
        .status-rejected h2 {
            margin: 0 0 15px 0;
            font-size: 24px;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .info-item {
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            display: inline-block;
            min-width: 150px;
        }
        @media screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header, .content {
                padding: 20px 15px;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NESA Registration Status</h1>
        <p>Important Update Regarding Your Registration</p>
    </div>

    <div class="content">
        <p>Dear {{ $student->full_name }},</p>

        <p>We are writing to inform you about the status of your NESA (National Examination and School Assessment) registration.</p>

        @if($status === 'approved')
            <div class="status-approved">
                <h2>🎉 Congratulations! Your Registration is Approved</h2>
                <p>Your NESA registration has been successfully processed and approved. You are now officially registered for the National Examination.</p>
                
                @if($documentPath)
                    <div class="info-item">
                        <span class="info-label">📄 Registration Document:</span>
                        Your official NESA registration document is attached to this email for your records.
                    </div>
                @endif
                
                <div class="info-item">
                    <span class="info-label">📅 Registration Date:</span>
                    {{ $student->nesa_registration_date ? $student->nesa_registration_date->format('d M Y') : 'N/A' }}
                </div>
                
                @if($student->nesa_sdms_code)
                    <div class="info-item">
                        <span class="info-label">🔢 SDMS Code:</span>
                        {{ $student->nesa_sdms_code }}
                    </div>
                @endif
                
                @if($student->nesa_reg_number)
                    <div class="info-item">
                        <span class="info-label">🎫 Registration Number:</span>
                        {{ $student->nesa_reg_number }}
                    </div>
                @endif
            </div>
        @else
            <div class="status-rejected">
                <h2>❌ Registration Status Update</h2>
                <p>We regret to inform you that your NESA registration could not be processed at this time.</p>
                
                @if(isset($emailContent) && $emailContent)
                    <div class="info-item">
                        <span class="info-label">📝 Reason:</span>
                        {!! $emailContent !!}
                    </div>
                @endif
                
                <p>Please contact our administration office for further assistance and guidance on how to proceed.</p>
            </div>
        @endif

        <p>For any questions or concerns regarding your registration status, please don't hesitate to contact our support team.</p>

        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
