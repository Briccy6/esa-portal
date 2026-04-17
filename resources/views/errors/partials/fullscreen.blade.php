@php
    $statusCode = $statusCode ?? 500;
    $headline = $headline ?? 'Oops..';
    $title = $title ?? 'Something went wrong';
    $message = $message ?? 'The page is currently unavailable. Please try again later.';
    $primaryLabel = $primaryLabel ?? 'Check Again';
    $primaryAction = $primaryAction ?? 'reload';
    $secondaryLabel = $secondaryLabel ?? 'Go Back';
    $secondaryAction = $secondaryAction ?? 'back';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $statusCode }} - {{ $title }}</title>
    <style>
        :root {
            --bg-primary: #5a86d8;
            --bg-secondary: #6f99df;
            --text-main: #ffffff;
            --text-sub: rgba(255, 255, 255, 0.9);
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(180deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
            color: var(--text-main);
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            padding: 40px 7vw;
            position: relative;
            overflow: hidden;
        }
        .blob { position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.08); z-index: 0; }
        .blob.b1 { width: 320px; height: 320px; top: -100px; right: 8%; }
        .blob.b2 { width: 460px; height: 460px; bottom: -220px; left: 45%; }
        .blob.b3 { width: 190px; height: 190px; top: 80px; left: -60px; }
        .content { max-width: 520px; z-index: 1; }
        .title { font-size: clamp(50px, 7vw, 78px); font-weight: 700; line-height: 1; margin: 0 0 14px; }
        .subtitle { font-size: clamp(34px, 4.5vw, 56px); font-weight: 400; margin: 0 0 24px; }
        .message { margin: 0 0 30px; color: var(--text-sub); font-size: 30px; line-height: 1.6; max-width: 430px; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn {
            border: 0; border-radius: 10px; background: #ffffff; color: #3d6cb9; font-size: 16px;
            font-weight: 600; padding: 12px 20px; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; justify-content: center;
            transition: transform .15s ease, opacity .15s ease;
        }
        .btn:hover { transform: translateY(-1px); opacity: 0.95; }
        .art { z-index: 1; display: flex; align-items: flex-end; justify-content: center; min-width: 360px; }
        .status-card {
            width: min(44vw, 440px); max-width: 440px; aspect-ratio: 4 / 3; background: #ffffff;
            border-radius: 14px; border: 8px solid #5f8fda; box-shadow: 0 20px 45px rgba(24, 43, 95, 0.25);
            display: flex; align-items: center; justify-content: center; transform: rotate(-7deg);
        }
        .status-card span { font-size: clamp(46px, 6vw, 84px); font-weight: 800; color: #f15b6c; letter-spacing: 3px; }
        .status-note { margin-top: 16px; color: rgba(255, 255, 255, 0.8); font-size: 14px; }
        .validation-list {
            margin: 0 0 20px;
            padding: 12px 16px;
            max-width: 430px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 15px;
            line-height: 1.4;
        }
        .validation-list ul { margin: 0; padding-left: 18px; }
        @media (max-width: 980px) {
            .wrapper { flex-direction: column; justify-content: center; text-align: left; padding-top: 70px; }
            .art { min-width: 0; width: 100%; justify-content: flex-start; }
            .status-card { width: min(82vw, 420px); }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div class="blob b3"></div>

        <section class="content">
            <h1 class="title">{{ $headline }}</h1>
            <h2 class="subtitle">{{ $title }}</h2>
            <p class="message">{{ $message }}</p>

            @if(!empty($showErrors) && $showErrors === true && isset($errors) && $errors->any())
                <div class="validation-list">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="actions">
                @if($primaryAction === 'reload')
                    <button class="btn" onclick="window.location.reload()">{{ $primaryLabel }}</button>
                @elseif($primaryAction === 'back')
                    <button class="btn" onclick="window.history.back()">{{ $primaryLabel }}</button>
                @elseif(!empty($primaryUrl))
                    <a class="btn" href="{{ $primaryUrl }}">{{ $primaryLabel }}</a>
                @endif

                @if($secondaryAction === 'reload')
                    <button class="btn" onclick="window.location.reload()">{{ $secondaryLabel }}</button>
                @elseif($secondaryAction === 'back')
                    <button class="btn" onclick="window.history.back()">{{ $secondaryLabel }}</button>
                @elseif(!empty($secondaryUrl))
                    <a class="btn" href="{{ $secondaryUrl }}">{{ $secondaryLabel }}</a>
                @endif
            </div>

            <div class="status-note">HTTP {{ $statusCode }}</div>
        </section>

        <div class="art" aria-hidden="true">
            <div class="status-card"><span>{{ $statusCode }}</span></div>
        </div>
    </div>
</body>
</html>
