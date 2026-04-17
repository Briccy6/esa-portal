<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Class Access - {{ $liveClass->title }}</title>
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
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 20px 0;
        }
        .class-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .class-info h3 {
            margin-top: 0;
            color: #495057;
        }
        .meeting-link {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .meeting-link a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
        }
        .meeting-link a:hover {
            text-decoration: underline;
        }
        .important {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .important h4 {
            color: #856404;
            margin-top: 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎥 Live Class Access</h1>
            <p>Your payment was successful!</p>
        </div>

        <div class="content">
            <p>Dear {{ $payment->name }},</p>
            
            <p>Thank you for your payment! You now have access to the live class. Below are your class details and the meeting link to join.</p>

            <div class="class-info">
                <h3>📚 Class Information</h3>
                <p><strong>Title:</strong> {{ $liveClass->title }}</p>
                <p><strong>Description:</strong> {{ $liveClass->description ?: 'No description provided' }}</p>
                <p><strong>Date:</strong> {{ $liveClass->start_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $liveClass->start_time->format('g:i A') }} - {{ $liveClass->end_time->format('g:i A') }}</p>
                @if($liveClass->teacher)
                    <p><strong>Instructor:</strong> {{ $liveClass->teacher->name }}</p>
                @endif
            </div>

            <div class="meeting-link">
                <h3>🔗 Join Your ESA Live Class</h3>
                <p>Click the button below a few minutes before the start time to enter the online classroom:</p>
                <a href="{{ route('live-classes.room', $liveClass) }}?email={{ urlencode($payment->email) }}" target="_blank">
                    Join Live Class Now
                </a>
            </div>

            <div class="important">
                <h4>⚠️ Important Information</h4>
                <ul>
                    <li><strong>Email Verification:</strong> You must use the same email address ({{ $payment->email }}) when joining the class so we can confirm your access.</li>
                    <li><strong>Join Early:</strong> Please join the online classroom 5–10 minutes before the start time.</li>
                    <li><strong>Technical Requirements:</strong> Make sure you have a stable internet connection and a device with camera and microphone.</li>
                    <li><strong>Meeting Platform:</strong> This class uses the ESA live classroom (video, audio, screen sharing and chat directly inside the portal).</li>
                </ul>
            </div>

            <p>If you have any questions or issues joining the class, please contact support.</p>

            <p>We look forward to seeing you in the class!</p>

            <p>Best regards,<br>
            The Education Team</p>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $payment->email }} because you registered for a live class.</p>
            <p>&copy; {{ date('Y') }} Educational Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
