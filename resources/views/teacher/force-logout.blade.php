<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Logout - ESA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23ffffff" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }
        
        .container {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #e74c3c, #f39c12, #f1c40f, #27ae60, #3498db, #9b59b6);
        }
        
        .security-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        h2 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .warning-message {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border: 2px solid #feb2b2;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            position: relative;
        }
        
        .warning-message::before {
            content: '⚠️';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 20px;
        }
        
        .warning-message p {
            color: #742a2a;
            font-size: 16px;
            line-height: 1.6;
            margin: 0;
            font-weight: 500;
        }
        
        .security-info {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%);
            border-left: 4px solid #27ae60;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
            text-align: left;
        }
        
        .security-info h4 {
            color: #27ae60;
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .security-info h4::before {
            content: '🛡️';
            margin-right: 8px;
        }
        
        .security-info p {
            color: #2c3e50;
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }
        
        .logout-form {
            margin: 30px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: #ffffff;
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
        }
        
        .btn-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(231, 76, 60, 0.4);
        }
        
        .btn-danger:hover::before {
            left: 100%;
        }
        
        .btn-danger:active {
            transform: translateY(0);
        }
        
        .btn-icon {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .additional-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .action-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin: 0 15px;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        
        .action-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #5a67d8;
        }
        
        .footer-note {
            margin-top: 25px;
            font-size: 12px;
            color: #7f8c8d;
            line-height: 1.4;
        }
        
        .esa-logo {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 14px;
            font-weight: 700;
            color: #bdc3c7;
            opacity: 0.7;
        }
        
        /* Loading state for form submission */
        .btn-danger.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-danger.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive design */
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 30px 25px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .security-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .btn {
                padding: 12px 30px;
                font-size: 15px;
                min-width: 180px;
            }
            
            .action-link {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="esa-logo">ESA</div>
        
        <div class="security-icon">🚨</div>
        
        <h2>Security Logout Required</h2>
        
        <div class="warning-message">
            <p>
                <strong>Unauthorized Access Detected:</strong> If this login was not authorized by you, immediate action is required to secure your account.
            </p>
        </div>
        
        <div class="security-info">
            <h4>What happens when you logout?</h4>
            <p>
                • All active sessions will be terminated immediately<br>
                • You'll be redirected to the login page<br>
                • Your account security will be enhanced<br>
                • IT support will be notified of the security event
            </p>
        </div>
        
        <form method="POST" action="{{ route('teacher.logout') }}" class="logout-form" id="logoutForm">
            @csrf
            <button type="submit" class="btn btn-danger" id="logoutBtn">
                <span class="btn-icon">🚪</span>
                Logout Immediately
            </button>
        </form>
        
        <div class="additional-actions">
            <a href="#" class="action-link" onclick="window.history.back();">← Go Back</a>
            <a href="mailto:brian.it@excellentservantacademy.com" class="action-link">📧 Contact Support</a>
        </div>
        
        <div class="footer-note">
            This security measure helps protect your ESA account and student data.<br>
            For assistance, contact IT support immediately.
        </div>
    </div>
    
    <script>
        // Add loading state to logout button
        document.getElementById('logoutForm').addEventListener('submit', function() {
            const btn = document.getElementById('logoutBtn');
            btn.classList.add('loading');
            btn.innerHTML = 'Logging out...';
        });
        
        // Add subtle animations
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.5s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>