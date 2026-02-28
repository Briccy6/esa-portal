<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Required - Exam Questions</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }
        
        /* Header section */
        .email-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 25s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .urgency-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: inline-block;
            animation: pulse 2s ease-in-out infinite;
            position: relative;
            z-index: 2;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        
        .action-icon {
            font-size: 60px;
            margin-bottom: 15px;
            display: block;
            position: relative;
            z-index: 2;
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .email-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }
        
        .email-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
            position: relative;
            z-index: 2;
        }
        
        /* Content section */
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .action-request {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #ff6b6b;
            position: relative;
            overflow: hidden;
        }
        
        .action-request::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255, 107, 107, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }
        
        .request-text {
            color: #742a2a;
            font-size: 16px;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }
        
        .exam-details {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            border: 1px solid #f1f5f9;
        }
        
        .detail-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #f7fafc;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .detail-icon {
            font-size: 22px;
            margin-right: 15px;
            width: 35px;
            text-align: center;
            margin-top: 2px;
        }
        
        .detail-label {
            font-weight: 700;
            color: #2d3748;
            min-width: 140px;
            margin-right: 15px;
        }
        
        .detail-value {
            color: #4a5568;
            font-weight: 500;
            flex: 1;
        }
        
        .exam-title-highlight {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 18px;
        }
        
        .lessons-section {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 4px solid #48bb78;
        }
        
        .lessons-title {
            font-weight: 700;
            color: #22543d;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 16px;
        }
        
        .lessons-title::before {
            content: '📚';
            margin-right: 10px;
            font-size: 20px;
        }
        
        .lessons-list {
            list-style: none;
            padding: 0;
        }
        
        .lesson-item {
            background: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 3px solid #48bb78;
            color: #2f855a;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .lesson-item::before {
            content: '📖';
            margin-right: 10px;
        }
        
        .lesson-item:last-child {
            margin-bottom: 0;
        }
        
        .lesson-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.2);
        }
        
        .call-to-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .call-to-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            animation: shine 3s ease-in-out infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .cta-icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: block;
            position: relative;
            z-index: 2;
        }
        
        .cta-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .cta-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            position: relative;
            z-index: 2;
            line-height: 1.6;
        }
        
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
            text-decoration: none;
            color: white;
        }
        
        /* Footer section */
        .email-footer {
            background: #2d3748;
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        .footer-text {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        
        .institution-name {
            font-weight: 600;
            font-size: 16px;
            color: #ff6b6b;
        }
        
        .priority-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            animation: blink 2s ease-in-out infinite;
        }
        
        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.5; }
        }
        
        /* Responsive design */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .email-content {
                padding: 25px 20px;
            }
            
            .email-header {
                padding: 25px 20px;
            }
            
            .email-title {
                font-size: 24px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .detail-label {
                min-width: auto;
                margin-bottom: 5px;
            }
            
            .call-to-action {
                padding: 25px 20px;
            }
        }
        
        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .email-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .login-button {
                background: #333;
                color: white;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="priority-indicator">High Priority</div>
        
        <!-- Header -->
        <div class="email-header">
            <span class="urgency-badge">Action Required</span>
            <span class="action-icon">⚡</span>
            <h1 class="email-title">Exam Questions Needed</h1>
            <p class="email-subtitle">Teacher Action Required</p>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <div class="greeting">
                Dear Teacher,
            </div>
            
            <div class="action-request">
                <div class="request-text">
                    You are requested to add questions and answers for the following exam. Your prompt attention to this matter is greatly appreciated.
                </div>
            </div>
            
            <div class="exam-details">
                <div class="detail-row">
                    <span class="detail-icon">📝</span>
                    <span class="detail-label">Exam Title:</span>
                    <span class="detail-value">
                        <span class="exam-title-highlight">{{ $exam->title }}</span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-icon">📅</span>
                    <span class="detail-label">Scheduled Date:</span>
                    <span class="detail-value">{{ $exam->date }}</span>
                </div>
            </div>
            
            <div class="lessons-section">
                <div class="lessons-title">
                    Assigned Lessons Coverage
                </div>
                <ul class="lessons-list">
                    @foreach($lessons as $lesson)
                        <li class="lesson-item">{{ $lesson->name ?? $lesson->title }}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="call-to-action">
                <span class="cta-icon">🎯</span>
                <div class="cta-title">Next Steps Required</div>
                <div class="cta-text">
                    Please log in to the system and add the required questions and answers as soon as possible. 
                    Students will be notified once the exam is fully prepared.
                </div>
                <a href="#" class="login-button">🔐 Login to System</a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                This is an automated notification from the ESA Education System.
                <br>Please respond promptly to ensure smooth academic operations.
            </div>
            <div class="institution-name">
                ESA Education System - Teacher Portal
            </div>
        </div>
    </div>
</body>
</html>