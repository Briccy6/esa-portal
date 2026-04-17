<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Notification</title>
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
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }
        
        /* Header section */
        .email-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
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
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .exam-icon {
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
        
        .exam-announcement {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #4facfe;
            position: relative;
            overflow: hidden;
        }
        
        .exam-announcement::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }
        
        .exam-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        
        .exam-details {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .detail-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .detail-icon {
            font-size: 20px;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }
        
        .detail-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 120px;
            margin-right: 15px;
        }
        
        .detail-value {
            color: #2d3748;
            font-weight: 500;
            flex: 1;
        }
        
        .instructions-section {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #fc8181;
            margin-bottom: 25px;
        }
        
        .instructions-title {
            font-weight: 600;
            color: #c53030;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .instructions-title::before {
            content: '⚠️';
            margin-right: 8px;
        }
        
        .instructions-text {
            color: #742a2a;
            line-height: 1.6;
        }
        
        .good-luck-section {
            text-align: center;
            padding: 25px;
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .good-luck-text {
            font-size: 18px;
            font-weight: 600;
            color: #22543d;
            margin-bottom: 10px;
        }
        
        .good-luck-emoji {
            font-size: 30px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .motivational-quote {
            font-style: italic;
            color: #2f855a;
            font-size: 14px;
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
            color: #4facfe;
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <span class="exam-icon">📝</span>
            <h1 class="email-title">Exam Notification</h1>
            <p class="email-subtitle">Important Academic Update</p>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <div class="greeting">
                Dear Student,
            </div>
            
            <div class="exam-announcement">
                <h2 class="exam-title">{{ $exam->title }}</h2>
                <p>A new exam has been scheduled for your course. Please review the details below and prepare accordingly.</p>
            </div>
            
            <div class="exam-details">
                <div class="detail-row">
                    <span class="detail-icon">📚</span>
                    <span class="detail-label">Exam Title:</span>
                    <span class="detail-value">{{ $exam->title }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-icon">📅</span>
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">{{ $exam->date }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-icon">💯</span>
                    <span class="detail-label">Total Marks:</span>
                    <span class="detail-value">{{ $exam->total_marks }} points</span>
                </div>
            </div>
            
            @if($exam->instructions)
            <div class="instructions-section">
                <div class="instructions-title">
                    Important Instructions
                </div>
                <div class="instructions-text">
                    {{ $exam->instructions }}
                </div>
            </div>
            @endif
            
            <div class="good-luck-section">
                <div class="good-luck-text">Best of Luck!</div>
                <div class="good-luck-emoji">🍀</div>
                <div class="motivational-quote">
                    "Success is where preparation and opportunity meet."
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                This is an automated notification from your academic institution.
            </div>
            <div class="institution-name">
                ESA Education System
            </div>
        </div>
    </div>
</body>
</html>