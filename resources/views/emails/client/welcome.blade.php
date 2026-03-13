<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ESA Portal!</title>
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
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .feature {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .feature h4 {
            margin: 0 0 10px 0;
            color: #3b82f6;
        }
        .cta-button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
        }
        .cta-button:hover {
            background: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 ESA Portal</h1>
            <p>Your Gateway to Excellence in Learning</p>
        </div>

        <div class="welcome-section">
            <h2>Welcome, {{ $name }}! 🎉</h2>
            <p>Thank you for joining ESA Portal! Your account has been successfully created and you now have access to our comprehensive learning platform.</p>
        </div>

        <h3>🚀 What You Can Access Now:</h3>
        <div class="features">
            <div class="feature">
                <h4>📚 Practice Exams</h4>
                <p>Access a wide range of practice tests covering various subjects and difficulty levels. Perfect your skills with real exam simulations.</p>
            </div>
            <div class="feature">
                <h4>💎 Premium Subscriptions</h4>
                <p>Choose from flexible subscription plans that offer token benefits, priority access, and exclusive learning resources.</p>
            </div>
            <div class="feature">
                <h4>🪙 Token System</h4>
                <p>Purchase tokens to unlock practice exams and save money with bulk packages. Track your token balance and usage history.</p>
            </div>
            <div class="feature">
                <h4>📊 Progress Tracking</h4>
                <p>Monitor your learning journey with detailed analytics, performance metrics, and personalized recommendations.</p>
            </div>
        </div>

        <h3>🔗 Quick Links:</h3>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $dashboard_url }}" class="cta-button">Go to Dashboard</a>
            <a href="{{ $exams_url }}" class="cta-button" style="background: #10b981;">Browse Exams</a>
        </div>

        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="color: #92400e; margin-top: 0;">💡 Getting Started Tips:</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Start by browsing available practice exams</li>
                <li>Consider a subscription plan for better value</li>
                <li>Purchase tokens in bulk to save money</li>
                <li>Track your progress regularly</li>
                <li>Reach out to support if you need help</li>
            </ul>
        </div>

        <h3>🎯 Your Account Details:</h3>
        <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Login URL:</strong> <a href="{{ $login_url }}">{{ $login_url }}</a></p>
            <p><strong>Dashboard:</strong> <a href="{{ $dashboard_url }}">{{ $dashboard_url }}</a></p>
        </div>

        <h3>📞 Need Help?</h3>
        <p>Our support team is here to help you make the most of your learning experience. Feel free to reach out if you have any questions or need assistance with:</p>
        <ul>
            <li>Navigating the platform</li>
            <li>Choosing the right subscription plan</li>
            <li>Understanding token usage</li>
            <li>Technical issues</li>
        </ul>

        <div class="footer">
            <p><strong>ESA Portal</strong> - Excellence in Education</p>
            <p>This email was sent to {{ $email }} because you registered for an account.</p>
            <p>© {{ date('Y') }} ESA Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
