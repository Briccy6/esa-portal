@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $systemName }} - Admin Reset Password</title>
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
        }
        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.4rem;
        }
        p.subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
        }
        input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 0.75rem 0.9rem;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .field {
            margin-bottom: 0.9rem;
        }
        .btn-primary {
            width: 100%;
            margin-top: 1.2rem;
            border: none;
            border-radius: 999px;
            padding: 0.85rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.35);
        }
        .btn-link {
            margin-top: 1.2rem;
            display: inline-block;
            font-size: 0.9rem;
            color: #4f46e5;
            text-decoration: none;
        }
        .btn-link:hover {
            text-decoration: underline;
        }
        .status {
            margin-bottom: 0.75rem;
            padding: 0.7rem 0.9rem;
            border-radius: 8px;
            font-size: 0.85rem;
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
            We have sent a 6‑digit code to your admin email. Enter it below with your new password.
        </p>

        @if ($errors->any())
            <div class="status status-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.reset.code') }}">
            @csrf
            <div class="field">
                <label for="email">Admin email</label>
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

        <a href="{{ route('admin.password.request') }}" class="btn-link">
            Resend code
        </a>
    </div>
</body>
</html>

