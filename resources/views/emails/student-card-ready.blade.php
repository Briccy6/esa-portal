<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Card Ready - ESA E-Learning</title>
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
            background-color: #1e40af;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e2e8f0;
        }
        .highlight {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #64748b;
        }
        .important {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 ESA E-Learning</h1>
        <p>Your Student Card is Ready!</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $student->full_name }},</h2>
        
        <p>Great news! Your student card has been successfully generated and is now ready for pickup.</p>
        
        <div class="highlight">
            <h3>📋 Student Information</h3>
            <p><strong>Registration Number:</strong> {{ $student->reg_number }}</p>
            <p><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
            <p><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</p>
            <p><strong>Academic Year:</strong> {{ $student->academicYear->year ?? 'N/A' }}</p>
        </div>
        
        <div class="important">
            <h3>⚠️ Important: Card Pickup Instructions</h3>
            <p><strong>Please visit the administration office to collect your student card.</strong></p>
            <ul>
                <li>Bring a valid ID for verification</li>
                <li>You may be asked to sign a receipt</li>
                <li>Cards are available during office hours</li>
            </ul>
        </div>
        
        <h3>📍 Administration Office Location</h3>
        <p>Please visit our main administration office during business hours to collect your student card. Our staff will be happy to assist you.</p>
        
        <h3>📞 Contact Information</h3>
        <p>If you have any questions about your student card, please contact us:</p>
        <ul>
            <li>Email: admin@esa-elearning.com</li>
            <li>Phone: +250 XXX XXX XXX</li>
            <li>Office Hours: Monday - Friday, 8:00 AM - 5:00 PM</li>
        </ul>
        
        <p>Your student card is an important document that you'll need for:</p>
        <ul>
            <li>Accessing campus facilities</li>
            <li>Taking exams</li>
            <li>Library access</li>
            <li>Student discounts</li>
            <li>Official identification</li>
        </ul>
        
        <p>We look forward to seeing you at the administration office!</p>
        
        <p>Best regards,<br>
        <strong>ESA E-Learning Administration Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from ESA E-Learning. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html> 