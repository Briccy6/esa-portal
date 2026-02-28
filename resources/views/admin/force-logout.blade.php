<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert - Force Logout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .security-container {
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .security-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .security-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)" /></svg>');
            opacity: 0.3;
        }

        .warning-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            animation: pulse 2s infinite;
        }

        .warning-icon::before {
            content: '⚠️';
            font-size: 32px;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .security-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .security-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }

        .content-section {
            padding: 40px 30px;
            text-align: center;
        }

        .alert-message {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            position: relative;
        }

        .alert-message::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #ffc107, #fd7e14, #dc3545, #6f42c1, #0d6efd, #198754, #ffc107);
            border-radius: 14px;
            z-index: -1;
            animation: borderRotate 3s linear infinite;
        }

        @keyframes borderRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert-text {
            font-size: 18px;
            color: #856404;
            font-weight: 500;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .emphasis {
            font-weight: 700;
            color: #dc3545;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logout-form {
            margin-top: 30px;
        }

        .logout-button {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .logout-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .logout-button:hover::before {
            left: 100%;
        }

        .logout-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        .logout-button:active {
            transform: translateY(-1px);
        }

        .security-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            border-left: 4px solid #17a2b8;
        }

        .info-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-title::before {
            content: 'ℹ️';
            font-size: 18px;
        }

        .info-text {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.5;
        }

        .footer-note {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }

        @media (max-width: 600px) {
            .security-container {
                margin: 10px;
                border-radius: 12px;
            }

            .security-header {
                padding: 30px 20px;
            }

            .content-section {
                padding: 30px 20px;
            }

            .security-title {
                font-size: 24px;
            }

            .logout-button {
                width: 100%;
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- This would normally extend your admin layout, but showing the styled version -->
    <div class="security-container">
        <div class="security-header">
            <div class="warning-icon"></div>
            <h2 class="security-title">Security Alert</h2>
            <p class="security-subtitle">Unauthorized Access Detected</p>
        </div>

        <div class="content-section">
            <div class="alert-message">
                <p class="alert-text">
                    If this login was <span class="emphasis">not you</span>, please click the button below to logout immediately and secure your account.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-button">
                    🔒 Logout Now
                </button>
            </form>

            <div class="security-info">
                <div class="info-title">Security Recommendation</div>
                <p class="info-text">
                    After logging out, consider changing your password and enabling two-factor authentication for enhanced security.
                </p>
            </div>

            <div class="footer-note">
                This action will immediately terminate your current session
            </div>
        </div>
    </div>
</body>
</html>