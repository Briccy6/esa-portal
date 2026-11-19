@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Reset Password</title>
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            padding: 32px 28px;
            width: 100%;
            max-width: 440px;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2933;
            margin-bottom: 6px;
        }
        p.subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .field {
            margin-bottom: 10px;
        }
        .btn-primary {
            width: 100%;
            margin-top: 14px;
            border: none;
            border-radius: 999px;
            padding: 11px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.35);
        }
        .btn-link {
            margin-top: 14px;
            display: inline-block;
            font-size: 13px;
            color: #4f46e5;
            text-decoration: none;
        }
        .btn-link:hover {
            text-decoration: underline;
        }
        .status {
            margin-bottom: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
        }
        .status-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Enter reset code</h1>
        <p class="subtitle">
            We have sent a 6‑digit code to your teacher email. Enter it below with your new password.
        </p>

        @if ($errors->any())
            <div class="status status-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.password.reset.code') }}">
            @csrf
            <div class="field">
                <label for="email">Teacher email</label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email', $email) }}"
                       required
                       autocomplete="email">
            </div>

            <div class="field">
                <label for="code">6‑digit code</label>
                <input id="code"
                       type="text"
                       name="code"
                       value="{{ old('code') }}"
                       maxlength="6"
                       required>
            </div>

            <div class="field">
                <label for="password">New password</label>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="new-password">
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       required
                       autocomplete="new-password">
            </div>

            <button type="submit" class="btn-primary">
                Reset password and continue
            </button>
        </form>

        <a href="{{ route('teacher.password.request') }}" class="btn-link">
            Resend code
        </a>
    </div>
</body>
</html>

