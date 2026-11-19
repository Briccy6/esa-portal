@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $systemName }} - Staff Forgot Password</title>
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
            max-width: 420px;
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
        input[type="email"] {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 0.75rem 0.9rem;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input[type="email"]:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
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
        .status-success {
            background: #ecfdf5;
            color: #15803d;
            border: 1px solid #bbf7d0;
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
        <h1>Forgot staff password</h1>
        <p class="subtitle">
            Enter your staff email and we will send you a 6‑digit code to reset your password.
        </p>

        @if (session('status'))
            <div class="status status-success">
                {{ session('status') }}
            </div>
        @endif

        @error('email')
            <div class="status status-error">
                {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('staff.password.email') }}">
            @csrf
            <label for="email">Staff email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="email">

            <button type="submit" class="btn-primary">
                Send reset code
            </button>
        </form>

        <a href="{{ route('staff.login') }}" class="btn-link">
            Back to staff login
        </a>
    </div>
</body>
</html>

