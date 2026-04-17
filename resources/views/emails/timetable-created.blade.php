<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Timetable Available</title>
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
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 28px;
        }
        .content {
            margin-bottom: 30px;
        }
        .timetable-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .timetable-info h3 {
            color: #667eea;
            margin-top: 0;
        }
        .info-row {
            display: flex;
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 New Timetable Available</h1>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $student->full_name ?? 'Student' }}</strong>,</p>

            <p>We're excited to inform you that a new timetable has been uploaded and is now available for you to view. This timetable contains important scheduling information that will help you plan your academic activities.</p>

            <div class="timetable-info">
                <h3>{{ $timetable->title }}</h3>
                
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="badge badge-success">Active</span>
                    </span>
                </div>

                @if($timetable->valid_from && $timetable->valid_until)
                <div class="info-row">
                    <span class="info-label">Valid From:</span>
                    <span class="info-value">{{ $validFrom }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Valid Until:</span>
                    <span class="info-value">{{ $validUntil }}</span>
                </div>
                @else
                <div class="info-row">
                    <span class="info-label">Validity:</span>
                    <span class="info-value">
                        <span class="badge badge-info">Always Valid</span>
                    </span>
                </div>
                @endif

                <div class="info-row">
                    <span class="info-label">Posted:</span>
                    <span class="info-value">{{ $timetable->created_at->format('M d, Y \a\t g:i A') }}</span>
                </div>

                @if($hasImage)
                <div class="info-row">
                    <span class="info-label">Includes:</span>
                    <span class="info-value">📷 Visual timetable image</span>
                </div>
                @endif
            </div>

            <p><strong>What you should do:</strong></p>
            <ul>
                <li>Log in to your student dashboard to view the complete timetable</li>
                <li>Check the schedule carefully and note important dates and times</li>
                <li>Download or save the timetable for easy reference</li>
                <li>Contact your teacher or administration if you have any questions</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ route('student.dashboard') }}" class="cta-button">
                    🎓 View Timetable in Dashboard
                </a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from the ESA Education System.</p>
            <p>If you have any questions about this timetable, please contact your school administration.</p>
            <p>&copy; {{ date('Y') }} ESA Education System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
