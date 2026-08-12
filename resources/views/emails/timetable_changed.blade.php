<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Updated</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; background: #fff; margin: 20px auto; padding: 30px; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #667eea; }
        .header h1 { margin: 0; color: #667eea; font-size: 26px; }
        .content { margin-top: 20px; }
        .detail-list { padding-left: 20px; }
        .detail-list li { margin-bottom: 10px; }
        .cta { text-align: center; margin: 25px 0; }
        .button { display:inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; text-decoration:none; padding: 12px 24px; border-radius: 999px; }
        .footer { font-size: 13px; color: #777; text-align: center; margin-top: 30px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; background: #e8f0fe; color: #1a56db; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 Timetable Updated</h1>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $student->full_name ?? 'Student' }}</strong>,</p>
            <p>The timetable for <strong>{{ $timetable->title }}</strong> has been updated with a change that affects your schedule.</p>

            <div class="badge">{{ $summary }}</div>

            @if(!empty($details))
                <h3 style="margin-top: 20px;">What changed</h3>
                <ul class="detail-list">
                    @foreach($details as $detail)
                        <li>{{ $detail }}</li>
                    @endforeach
                </ul>
            @endif

            <p>To view the updated timetable and confirm your schedule, please sign in to your student dashboard.</p>

            <div class="cta">
                <a href="{{ route('student.dashboard') }}" class="button">View Updated Timetable</a>
            </div>

            <p>If you have questions about the change, contact your school DOS or teacher.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from the ESA Education System.</p>
            <p>&copy; {{ date('Y') }} ESA Education System</p>
        </div>
    </div>
</body>
</html>
