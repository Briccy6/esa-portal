@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Forgot Password</title>
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
            max-width: 420px;
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
        input[type="email"] {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input[type="email"]:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
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
        <h1>Forgot teacher password</h1>
        <p class="subtitle">
            Enter your teacher email and we will send you a 6‑digit code to reset your password.
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

        <form method="POST" action="{{ route('teacher.password.email') }}">
            @csrf
            <label for="email">Teacher email</label>
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

        <a href="{{ route('teacher.login') }}" class="btn-link">
            Back to teacher login
        </a>
    </div>
</body>
</html>

