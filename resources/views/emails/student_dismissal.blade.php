<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dismissal Notice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 28px;
        }
        .content {
            margin-bottom: 30px;
        }
        .student-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .student-info h3 {
            margin-top: 0;
            color: #dc3545;
        }
        .reason-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .reason-box h4 {
            color: #856404;
            margin-top: 0;
        }
        .wishes {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .wishes h4 {
            color: #155724;
            margin-top: 0;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .signature {
            margin-top: 30px;
            text-align: left;
        }
        .signature p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ESA Education System</h1>
            <p><strong>Important Notice Regarding Your Academic Status</strong></p>
        </div>

        <div class="content">
            <div class="student-info">
                <h3>Student Information</h3>
                <p><strong>Name:</strong> {{ $student->full_name }}</p>
                <p><strong>Registration Number:</strong> {{ $student->reg_number }}</p>
                <p><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
                <p><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</p>
                <p><strong>Date of Notice:</strong> {{ now()->format('d F Y') }}</p>
            </div>

            <div class="reason-box">
                <h4>Reason for Dismissal</h4>
                <p>{{ $dismissal_reason }}</p>
                @if($dismissalNotes)
                    <p><strong>Additional Notes:</strong></p>
                    <p>{{ $dismissalNotes }}</p>
                @endif
            </div>

            <div class="wishes">
                <h4>Message for Your Future Journey</h4>
                <p>Dear {{ $student->full_name }},</p>
                <p>We regret to inform you that you are no longer part of the ESA Education System community due to the circumstances mentioned above. However, we want to take this opportunity to wish you the very best in your future endeavors.</p>
                
                <p>Education is a continuous journey, and we encourage you to:</p>
                <ul>
                    <li>Continue learning and improving yourself</li>
                    <li>Reflect on this experience as an opportunity for growth</li>
                    <li>Strive for better performance in all your future pursuits</li>
                    <li>Never lose sight of your goals and dreams</li>
                </ul>

                <p>We believe that every challenge is an opportunity to become stronger and wiser. Use this experience as motivation to improve and excel in whatever path you choose next.</p>

                <p><em>"Success is not final, failure is not fatal: it is the courage to continue that counts." - Winston Churchill</em></p>
            </div>

            <div class="wishes">
                <h4>Wishes for Your Success</h4>
                <p>We sincerely wish you:</p>
                <ul>
                    <li>Success in your learning journey ahead</li>
                    <li>Wisdom to make the right choices</li>
                    <li>Strength to overcome any challenges</li>
                    <li>Happiness and fulfillment in all your endeavors</li>
                    <li>A bright and promising future</li>
                </ul>
                
                <p>May you find the right path that leads to your personal and professional growth. Remember that every setback is a setup for a comeback!</p>
            </div>
        </div>

        <div class="signature">
            <p><strong>ESA Education System Administration</strong></p>
            <p>Kigali, Rwanda</p>
            <p>{{ now()->format('d F Y') }}</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>For any inquiries, please contact the school administration directly.</p>
        </div>
    </div>
</body>
</html>
