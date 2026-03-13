@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    <title>Teacher Login</title>
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
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 20px 20px 0 0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #718096;
            font-size: 14px;
            font-weight: 400;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            font-size: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            color: #2d3748;
        }

        .form-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .form-input::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }

        .btn {
            width: 100%;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-block;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            margin-bottom: 12px;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }

        .btn-text {
            background: none;
            border: none;
            color: #718096;
            font-size: 14px;
            padding: 12px 0;
            cursor: pointer;
            transition: color 0.3s ease;
            text-decoration: none;
            width: auto;
        }

        .btn-text:hover {
            color: #667eea;
            text-decoration: underline;
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 10px;
            border-left: 4px solid #e53e3e;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 15px;
            color: #718096;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
        }

        /* Loading animation for submit button */
        .btn-primary.loading {
            pointer-events: none;
        }

        .btn-primary.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
                margin: 10px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .form-input, .btn {
                padding: 14px 18px;
                font-size: 15px;
            }
        }

        /* Focus trap for accessibility */
        .login-container:focus-within {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Smooth transitions */
        .login-container * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Teacher Login</h1>
            <p>Welcome back! Please sign in to your account</p>
        </div>

        <form method="POST" action="{{ route('teacher.login.submit') }}" id="loginForm">
            @csrf
            
            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="Enter your email address" 
                    required 
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="Enter your password" 
                    required
                    autocomplete="current-password"
                >
            </div>

            @error('email') 
                <div class="error-message">{{ $message }}</div> 
            @enderror

            <button type="submit" class="btn btn-primary" id="submitBtn">
                Sign In
            </button>

            <a href="/" class="btn btn-secondary">
                Back to Welcome
            </a>

            <div class="divider">
                <span>Need Help?</span>
            </div>

            <div class="footer-links">
                <a href="{{ route('teacher.password.request') }}" class="btn-text">
                    Forgot your password?
                </a>
            </div>
        </form>
    </div>

    <script>
        // Add loading state to form submission
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.textContent = '';
        });

        // Handle forgot password (you can customize this)
        function handleForgotPassword() {}

        // Add smooth focus transitions
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>