<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESA Registration Status</title>
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
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
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
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
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
                    <p><strong>Registration Document:</strong> Your official NESA registration document is attached to this email for your records.</p>
                @endif
                
                <p><strong>Registration Date:</strong> {{ $student->nesa_registration_date ? $student->nesa_registration_date->format('d M Y') : 'N/A' }}</p>
                
                @if($student->nesa_sdms_code)
                    <p><strong>SDMS Code:</strong> {{ $student->nesa_sdms_code }}</p>
                @endif
                
                @if($student->nesa_reg_number)
                    <p><strong>Registration Number:</strong> {{ $student->nesa_reg_number }}</p>
                @endif
            </div>
        @else
            <div class="status-rejected">
                <h2>Registration Status Update</h2>
                <p>We regret to inform you that your NESA registration could not be processed at this time.</p>
                
                @if(isset($emailContent) && $emailContent)
                    <p><strong>Reason:</strong> {{ $emailContent }}</p>
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
