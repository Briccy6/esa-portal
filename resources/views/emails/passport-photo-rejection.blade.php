<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passport Photo Rejection</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f5f5f5; padding: 30px; border-radius: 8px;">
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h1 style="color: #1e40af; margin-top: 0;">Your Passport Photo Was Not Approved</h1>
            
            <p>Dear {{ $studentName }},</p>
            
            <p>We have reviewed your passport photograph that you uploaded to your student profile, and unfortunately, it does not meet our requirements.</p>
            
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>Reason for rejection:</strong><br>
                {{ $rejectionReason }}
            </div>
            
            <h3 style="color: #1e40af;">Passport Photo Requirements:</h3>
            <ul style="margin: 15px 0; padding-left: 20px;">
                <li>Plain white background</li>
                <li>Professional passport-style portrait</li>
                <li>Exactly one visible human face</li>
                <li>Face centered</li>
                <li>Person facing the camera directly</li>
                <li>Portrait orientation</li>
                <li>Approximately 7:9 aspect ratio</li>
                <li>Minimum resolution 300 × 385 pixels</li>
                <li>Maximum file size 2 MB</li>
                <li>Accepted formats: JPG, JPEG, PNG</li>
                <li>Clear and not blurry</li>
            </ul>
            
            <p><strong>Photos we do not accept:</strong></p>
            <ul style="margin: 15px 0; padding-left: 20px;">
                <li>Selfies</li>
                <li>Colored or busy backgrounds</li>
                <li>Group photographs</li>
                <li>Scanned identity cards or documents</li>
                <li>Landscape photographs</li>
                <li>Blurry or excessively edited images</li>
            </ul>
            
            <p style="margin-top: 30px;">Please upload a new passport photograph that meets our requirements by logging into your student profile.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/student/profile" style="background: #1e40af; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Upload New Photo</a>
            </div>
            
            <p style="font-size: 12px; color: #666; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
                If you have any questions, please contact the school administration.<br><br>
                Excellent Servant Academy<br>
                {{ date('Y') }}
            </p>
        </div>
    </div>
</body>
</html>
