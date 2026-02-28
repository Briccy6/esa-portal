<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Login Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            display: flex;
            position: relative;
        }

        .login-tabs {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            width: 280px;
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .tab-button {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            padding: 20px 30px;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .tab-button:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .tab-button.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .tab-button.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .tab-icon {
            font-size: 20px;
            width: 24px;
        }

        .login-form-container {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #7f8c8d;
            font-size: 16px;
            font-weight: 400;
        }

        .login-form {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }

        .login-form.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e8ecef;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #bdc3c7;
            font-size: 18px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #764ba2;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
            color: #bdc3c7;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e8ecef;
            z-index: 1;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e8ecef;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .social-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .help-text {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 20px;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand-logo h2 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .brand-logo p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
                min-height: auto;
            }
            
            .login-tabs {
                width: 100%;
                padding: 20px 0;
                flex-direction: row;
                overflow-x: auto;
            }
            
            .tab-button {
                white-space: nowrap;
                padding: 15px 20px;
                min-width: 120px;
                justify-content: center;
            }
            
            .tab-button.active::before {
                left: 0;
                right: 0;
                top: auto;
                bottom: 0;
                width: auto;
                height: 4px;
            }
            
            .login-form-container {
                padding: 40px 30px;
            }
            
            .form-title {
                font-size: 24px;
            }
            
            .brand-logo {
                display: none;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-form-container {
                padding: 30px 20px;
            }
            
            .social-login {
                flex-direction: column;
            }
        }