<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-bg: #0f0f23;
            --card-bg: rgba(255, 255, 255, 0.1);
            --glass-bg: rgba(255, 255, 255, 0.08);
            --text-primary: #ffffff;
            --text-secondary: #b8c5d1;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-light: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--dark-bg);
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 200, 255, 0.2) 0%, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.3), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.2), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.4), transparent);
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: float 20s linear infinite;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes float {
            0% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-20px) translateX(10px); }
            100% { transform: translateY(0px) translateX(0px); }
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 2rem;
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: var(--shadow-heavy);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            box-shadow: var(--shadow-light);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Enhanced Tab Styling */
        .nav-tabs {
            border: none;
            background: var(--glass-bg);
            border-radius: 16px;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 12px;
            color: var(--text-secondary);
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-tabs .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .nav-tabs .nav-link.active,
        .nav-tabs .nav-link:hover {
            color: var(--text-primary);
            background: transparent;
        }

        .nav-tabs .nav-link.active::before,
        .nav-tabs .nav-link:hover::before {
            left: 0;
        }

        /* Enhanced Form Styling */
        .form-label {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 0.5rem;
            color: var(--text-secondary);
        }

        .form-control {
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem 1.2rem;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            background: var(--glass-bg);
            border-color: #667eea;
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        /* Enhanced Button Styling */
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
            background: var(--primary-gradient);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-success {
            background: var(--success-gradient);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
            background: var(--success-gradient);
        }

        .btn-outline-secondary {
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-outline-secondary:hover {
            background: var(--secondary-gradient);
            border-color: transparent;
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        /* Enhanced Alert Styling */
        .alert {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            animation: slideInDown 0.5s ease-out;
        }

        .alert-danger {
            border-left: 4px solid #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        /* Code input group styling */
        .code-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: stretch;
        }

        .code-input-group .form-control {
            flex: 1;
        }

        .code-input-group .btn {
            white-space: nowrap;
            min-width: 120px;
        }

        /* Tab content animation */
        .tab-content {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading animation for send code button */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .code-input-group {
                flex-direction: column;
            }
            
            .code-input-group .btn {
                min-width: auto;
            }
        }

        /* Accessibility improvements */
        .form-control:focus,
        .btn:focus {
            outline: 2px solid rgba(102, 126, 234, 0.5);
            outline-offset: 2px;
        }

        /* Success message styling */
        .success-message {
            background: rgba(0, 255, 127, 0.1);
            border: 1px solid rgba(0, 255, 127, 0.3);
            color: #00ff7f;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            animation: slideInDown 0.5s ease-out;
        }
    </style>
    
    <script>
        function showTab(tab) {
            // Hide all tabs
            document.getElementById('passwordTab').style.display = tab === 'password' ? 'block' : 'none';
            document.getElementById('codeTab').style.display = tab === 'code' ? 'block' : 'none';
            
            // Update active states
            document.getElementById('tabPassword').classList.toggle('active', tab === 'password');
            document.getElementById('tabCode').classList.toggle('active', tab === 'code');
            
            // Add animation class
            const activeTab = document.getElementById(tab + 'Tab');
            activeTab.classList.add('tab-content');
        }
        
        function sendCode() {
            const regNumber = document.getElementById('code_reg_number').value;
            const sendBtn = document.querySelector('[onclick="sendCode()"]');
            
            if (!regNumber) {
                showAlert('Please enter your registration number first.', 'warning');
                return;
            }
            
            // Add loading state
            sendBtn.classList.add('btn-loading');
            sendBtn.textContent = 'Sending...';
            sendBtn.disabled = true;
            
            fetch("{{ route('students.sendVerificationCode') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ reg_number: regNumber })
            })
            .then(response => response.json())
            .then(data => {
                showAlert(data.message || 'Verification code sent to your email if registered.', 'success');
            })
            .catch(() => {
                showAlert('Failed to send code. Please try again.', 'error');
            })
            .finally(() => {
                // Remove loading state
                sendBtn.classList.remove('btn-loading');
                sendBtn.textContent = 'Send Code';
                sendBtn.disabled = false;
            });
        }
        
        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.dynamic-alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert dynamic-alert ${type === 'success' ? 'success-message' : 'alert-danger'}`;
            alertDiv.textContent = message;
            
            // Insert at the top of the form
            const form = document.querySelector('.login-card');
            form.insertBefore(alertDiv, form.firstChild.nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Add smooth form submission
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('btn-loading');
                        submitBtn.disabled = true;
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to your student portal</p>
            </div>

            {{-- Show session error --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- Show validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <ul class="nav nav-tabs">
                <li class="nav-item flex-fill">
                    <button id="tabPassword" class="nav-link active w-100" onclick="showTab('password')" type="button">
                        <i class="fas fa-key me-2"></i>Password Login
                    </button>
                </li>
                <li class="nav-item flex-fill">
                    <button id="tabCode" class="nav-link w-100" onclick="showTab('code')" type="button">
                        <i class="fas fa-mobile-alt me-2"></i>Code Login
                    </button>
                </li>
            </ul>

            {{-- Password Login Tab --}}
            <div id="passwordTab" class="tab-content">
                <form method="POST" action="{{ route('student.login.submit') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="reg_number" class="form-label">
                            <i class="fas fa-id-card"></i>Registration Number
                        </label>
                        <input type="text" class="form-control" id="reg_number" name="reg_number" 
                               required autofocus value="{{ old('reg_number') }}" 
                               placeholder="Enter your registration number">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
            </div>

            {{-- Code Login Tab --}}
            <div id="codeTab" class="tab-content" style="display:none;">
                <form method="POST" action="{{ route('student.login.code') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="code_reg_number" class="form-label">
                            <i class="fas fa-id-card"></i>Registration Number
                        </label>
                        <input type="text" class="form-control" id="code_reg_number" name="reg_number" 
                               required value="{{ old('reg_number') }}" 
                               placeholder="Enter your registration number">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-shield-alt"></i>Verification Code
                        </label>
                        <div class="code-input-group">
                            <button type="button" class="btn btn-outline-secondary" onclick="sendCode()">
                                <i class="fas fa-paper-plane me-2"></i>Send Code
                            </button>
                            <input type="text" class="form-control" name="code" 
                                   placeholder="Enter verification code" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check-circle me-2"></i>Verify & Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize with password tab
        showTab('password');
        
        // Add enter key support for send code
        document.getElementById('code_reg_number').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendCode();
            }
        });
        
        // Add floating label effect
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>