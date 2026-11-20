<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7f4">
    <title>Student sign in | Excellent Servant Academy</title>
    <link rel="icon" type="image/png" href="{{ asset('images/esa.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --ink: #20352e;
            --muted: #6d7b74;
            --green: #24594a;
            --green-dark: #183e34;
            --gold: #c99e52;
            --line: #e2e8e3;
            --paper: #f6f7f4;
            --serif: Georgia, "Times New Roman", serif;
        }

        * { box-sizing: border-box; }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: "DM Sans", "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        button, input { font: inherit; }

        .login-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(450px, .94fr);
            min-height: 100vh;
        }

        .story-panel {
            position: relative;
            display: flex;
            min-height: 100vh;
            overflow: hidden;
            align-items: flex-end;
            padding: clamp(2rem, 5vw, 4.5rem);
            color: #fff;
            background: var(--green-dark);
            isolation: isolate;
        }

        .story-slides,
        .story-slides::after,
        .story-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .story-slides { z-index: -2; }

        .story-slides::after {
            z-index: 1;
            content: "";
            background:
                linear-gradient(180deg, rgba(14, 33, 28, .12) 5%, rgba(14, 33, 28, .06) 35%, rgba(14, 33, 28, .84) 100%),
                linear-gradient(90deg, rgba(15, 36, 30, .27), transparent 80%);
        }

        .story-image {
            object-fit: cover;
            object-position: center;
            opacity: 0;
            transform: scale(1.035);
            transition: opacity .9s ease, transform 6s ease;
        }

        .story-image.is-active {
            z-index: 0;
            opacity: 1;
            transform: scale(1);
        }

        .story-topline {
            position: absolute;
            z-index: 2;
            top: clamp(1.5rem, 4vw, 3rem);
            left: clamp(1.5rem, 4vw, 3rem);
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            color: rgba(255,255,255,.92);
            font-size: .79rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .story-topline::before {
            width: 26px;
            height: 1px;
            background: #e3bd78;
            content: "";
        }

        .story-copy {
            position: relative;
            z-index: 2;
            width: min(100%, 650px);
            padding-bottom: 1.5rem;
        }

        .story-kicker {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: 1.1rem;
            color: #f0d7a8;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .story-kicker::before {
            width: 24px;
            height: 1px;
            background: currentColor;
            content: "";
        }

        .story-title {
            max-width: 600px;
            margin: 0;
            font-family: var(--serif);
            font-size: clamp(2.6rem, 5.2vw, 5rem);
            font-weight: 400;
            letter-spacing: -.045em;
            line-height: 1.02;
            text-wrap: balance;
        }

        .story-caption {
            max-width: 450px;
            min-height: 3.2em;
            margin: 1.15rem 0 1.8rem;
            color: rgba(255,255,255,.8);
            font-size: 1rem;
            line-height: 1.7;
        }

        .story-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .story-arrows {
            display: flex;
            gap: .5rem;
        }

        .story-arrow {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 1px solid rgba(255,255,255,.45);
            border-radius: 50%;
            color: #fff;
            background: rgba(255,255,255,.08);
            cursor: pointer;
            transition: background .2s ease, border-color .2s ease;
        }

        .story-arrow:hover,
        .story-arrow:focus-visible {
            border-color: #fff;
            background: rgba(255,255,255,.2);
        }

        .story-dots {
            display: flex;
            align-items: center;
            gap: .42rem;
        }

        .story-dot {
            width: 7px;
            height: 7px;
            padding: 0;
            border: 0;
            border-radius: 999px;
            background: rgba(255,255,255,.52);
            cursor: pointer;
            transition: width .25s ease, background .25s ease;
        }

        .story-dot.is-active {
            width: 24px;
            background: #f0d7a8;
        }

        .story-count {
            margin-left: .25rem;
            color: rgba(255,255,255,.78);
            font-size: .76rem;
            font-variant-numeric: tabular-nums;
            letter-spacing: .06em;
        }

        .login-panel {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: clamp(1.5rem, 5vw, 4rem);
            background:
                radial-gradient(ellipse at 92% 8%, rgba(219, 228, 218, .58), transparent 31%),
                var(--paper);
        }

        .login-content {
            width: min(100%, 430px);
            padding: 1rem 0;
        }

        .academy-brand {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin-bottom: clamp(2.2rem, 6vh, 4rem);
        }

        .academy-logo {
            display: block;
            width: 49px;
            height: 49px;
            object-fit: contain;
            padding: 4px;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: #fff;
        }

        .academy-name {
            margin: 0;
            color: var(--ink);
            font-family: var(--serif);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -.015em;
        }

        .academy-label {
            margin: .2rem 0 0;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .login-heading {
            margin: 0;
            color: var(--ink);
            font-family: var(--serif);
            font-size: clamp(2.1rem, 4vw, 2.8rem);
            font-weight: 400;
            letter-spacing: -.04em;
            line-height: 1.1;
        }

        .login-intro {
            margin: .65rem 0 1.8rem;
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.65;
        }

        .login-alert {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            margin-bottom: 1.25rem;
            padding: .85rem 1rem;
            border: 1px solid #ebc7c1;
            border-radius: 10px;
            color: #733b33;
            background: #fff3f0;
            font-size: .86rem;
            line-height: 1.5;
        }

        .login-alert > i { margin-top: .15rem; }
        .login-alert.alert-success { border-color: #cfe2d4; color: #315d3d; background: #eff8f0; }
        .login-alert.alert-info { border-color: #ccdfdf; color: #355a5a; background: #f0f7f6; }
        .login-alert ul { margin: .35rem 0 0; padding-left: 1.1rem; }

        .login-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .3rem;
            margin-bottom: 1.65rem;
            padding: .3rem;
            border: 1px solid #e6eae6;
            border-radius: 10px;
            background: #edf0ec;
        }

        .login-tab {
            min-height: 42px;
            border: 0;
            border-radius: 7px;
            color: #68766f;
            background: transparent;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: color .18s ease, background .18s ease, box-shadow .18s ease;
        }

        .login-tab:hover { color: var(--green-dark); }

        .login-tab.active {
            color: var(--green-dark);
            background: #fff;
            box-shadow: 0 2px 6px rgba(27, 47, 38, .08);
        }

        .login-tab i { margin-right: .42rem; }

        .form-label {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .48rem;
            color: #34473e;
            font-size: .82rem;
            font-weight: 700;
        }

        .form-label i {
            width: 15px;
            color: #75867d;
            text-align: center;
        }

        .login-input {
            min-height: 51px;
            border: 1px solid #dce3dd;
            border-radius: 9px;
            color: #273a32;
            background: #fff;
            box-shadow: 0 1px 2px rgba(25, 45, 36, .025);
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .login-input::placeholder { color: #9aa49e; }

        .login-input:focus {
            border-color: #6b9785;
            color: #273a32;
            background: #fff;
            box-shadow: 0 0 0 .2rem rgba(36, 89, 74, .11);
        }

        .password-wrap { position: relative; }

        .password-wrap .login-input { padding-right: 3.2rem; }

        .password-visibility {
            position: absolute;
            top: 50%;
            right: .75rem;
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: 7px;
            color: #728078;
            background: transparent;
            cursor: pointer;
        }

        .password-visibility:hover { color: var(--green-dark); background: #f1f5f2; }

        .login-submit {
            min-height: 51px;
            border: 1px solid var(--green);
            border-radius: 9px;
            color: #fff;
            background: var(--green);
            font-size: .93rem;
            font-weight: 700;
            box-shadow: 0 5px 12px rgba(36, 89, 74, .12);
            transition: background .18s ease, transform .18s ease, box-shadow .18s ease;
        }

        .login-submit:hover,
        .login-submit:focus-visible {
            border-color: var(--green-dark);
            color: #fff;
            background: var(--green-dark);
            box-shadow: 0 7px 17px rgba(36, 89, 74, .2);
            transform: translateY(-1px);
        }

        .login-submit:disabled { opacity: .76; transform: none; }

        .code-input-group { display: flex; gap: .55rem; }
        .code-input-group .form-control { min-width: 0; }

        .send-code {
            flex: 0 0 auto;
            min-height: 51px;
            border: 1px solid #cbd8cf;
            border-radius: 9px;
            color: #31594a;
            background: #fff;
            font-size: .82rem;
            font-weight: 700;
        }

        .send-code:hover { border-color: #9db8a8; color: #204638; background: #f2f7f3; }
        .send-code:disabled { opacity: .65; }

        .login-footnote {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            margin-top: 1.5rem;
            color: #87928c;
            font-size: .75rem;
        }

        .login-footnote i { color: #628071; }

        .registration-link {
            margin: 1.15rem 0 0;
            color: var(--muted);
            font-size: .84rem;
            text-align: center;
        }

        .registration-link a {
            color: var(--green);
            font-weight: 700;
            text-decoration: none;
        }

        .registration-link a:hover { color: var(--green-dark); text-decoration: underline; }

        .dynamic-alert { animation: alert-in .2s ease-out; }
        .btn-loading { cursor: progress; }

        @keyframes alert-in {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 900px) {
            .login-shell { grid-template-columns: minmax(0, 1fr) minmax(400px, .9fr); }
            .story-panel { padding: 2rem; }
            .story-title { font-size: clamp(2.5rem, 6vw, 3.8rem); }
        }

        @media (max-width: 700px) {
            .login-shell { display: flex; flex-direction: column; }
            .story-panel {
                min-height: 350px;
                height: 42vh;
                max-height: 480px;
                padding: 1.5rem 1.35rem;
            }
            .story-topline { top: 1.35rem; left: 1.35rem; font-size: .68rem; }
            .story-copy { padding-bottom: .2rem; }
            .story-title { max-width: 480px; font-size: clamp(2.15rem, 9vw, 3.2rem); }
            .story-caption { margin: .75rem 0 1rem; font-size: .88rem; }
            .story-controls { gap: .7rem; }
            .story-arrow { width: 36px; height: 36px; }
            .login-panel { min-height: auto; padding: 2rem 1.3rem 2.5rem; }
            .login-content { max-width: 470px; padding: 0; }
            .academy-brand { margin-bottom: 2rem; }
            .login-heading { font-size: 2.25rem; }
        }

        @media (max-width: 400px) {
            .story-panel { min-height: 320px; }
            .story-caption { min-height: 0; }
            .code-input-group { flex-direction: column; }
            .send-code { width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="story-panel" aria-label="Student life at Excellent Servant Academy">
            <div class="story-slides" aria-live="polite">
                <img class="story-image is-active" src="{{ asset('images/1.jpg') }}" alt="Student life at Excellent Servant Academy" fetchpriority="high">
                <img class="story-image" src="{{ asset('images/2.jpg') }}" alt="Students learning and growing together" loading="lazy">
                <img class="story-image" src="{{ asset('images/4.jpg') }}" alt="A moment from the academy community" loading="lazy">
                <img class="story-image" src="{{ asset('images/5.jpg') }}" alt="Celebrating student life at the academy" loading="lazy">
            </div>
            <div class="story-topline">Excellent Servant Academy</div>

            <div class="story-copy">
                <div class="story-kicker">Our school community</div>
                <h2 class="story-title" id="storyTitle">A place to learn. A future to shape.</h2>
                <p class="story-caption" id="storyCaption">Every student has a story. Continue yours with the people and purpose that make ESA feel like home.</p>
                <div class="story-controls">
                    <div class="story-arrows">
                        <button class="story-arrow" id="previousSlide" type="button" aria-label="Show previous student photo">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        </button>
                        <button class="story-arrow" id="nextSlide" type="button" aria-label="Show next student photo">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="story-dots" role="group" aria-label="Choose a student photo">
                        <button class="story-dot is-active" type="button" aria-label="Show photo 1" aria-current="true"></button>
                        <button class="story-dot" type="button" aria-label="Show photo 2"></button>
                        <button class="story-dot" type="button" aria-label="Show photo 3"></button>
                        <button class="story-dot" type="button" aria-label="Show photo 4"></button>
                    </div>
                    <span class="story-count" id="storyCount" aria-hidden="true">01 / 04</span>
                </div>
            </div>
        </section>

        <section class="login-panel" aria-labelledby="loginHeading">
            <div class="login-content">
                <a class="academy-brand text-decoration-none" href="{{ url('/') }}" aria-label="Excellent Servant Academy home">
                    <img class="academy-logo" src="{{ asset('images/esa.png') }}" alt="">
                    <span>
                        <span class="academy-name d-block">Excellent Servant Academy</span>
                        <span class="academy-label d-block">Student portal</span>
                    </span>
                </a>

                <h1 class="login-heading" id="loginHeading">Welcome back.</h1>
                <p class="login-intro">Sign in to continue to your learning dashboard.</p>

                <div id="loginAlerts" aria-live="polite">
                    @if(session('error'))
                        <div class="login-alert" role="alert">
                            <i class="fas fa-circle-exclamation" aria-hidden="true"></i><span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('blocked'))
                        <div class="login-alert" role="alert">
                            <i class="fas fa-ban" aria-hidden="true"></i>
                            <div>
                                <strong>Account blocked</strong>
                                <div class="mt-1">Your account is currently blocked. Please contact the academy office for assistance.</div>
                                @if(session('block_reason'))
                                    <div class="mt-1"><strong>Reason:</strong> {{ session('block_reason') }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(session('auth_debug'))
                        <div class="login-alert alert-info" role="status">
                            <i class="fas fa-circle-info" aria-hidden="true"></i>
                            <span>{{ session('auth_debug') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="login-alert alert-success" role="status">
                            <i class="fas fa-circle-check" aria-hidden="true"></i><span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="login-alert" role="alert">
                            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                            <div>
                                <strong>Please check the details and try again.</strong>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="login-tabs" role="tablist" aria-label="Choose a sign-in method">
                    <button class="login-tab active" id="tabPassword" type="button" role="tab" aria-selected="true" aria-controls="passwordTab">
                        <i class="fas fa-key" aria-hidden="true"></i>Password
                    </button>
                    <button class="login-tab" id="tabCode" type="button" role="tab" aria-selected="false" aria-controls="codeTab">
                        <i class="fas fa-mobile-screen-button" aria-hidden="true"></i>Email code
                    </button>
                </div>

                <div id="passwordTab" role="tabpanel" aria-labelledby="tabPassword">
                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="reg_number" class="form-label">
                                <i class="fas fa-id-card" aria-hidden="true"></i>Registration number
                            </label>
                            <input type="text"
                                   class="form-control login-input"
                                   id="reg_number"
                                   name="reg_number"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   value="{{ old('reg_number') }}"
                                   placeholder="e.g. ESATOU102026-2027">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock" aria-hidden="true"></i>Password
                            </label>
                            <div class="password-wrap">
                                <input type="password"
                                       class="form-control login-input"
                                       id="password"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="Enter your password">
                                <button class="password-visibility" type="button" aria-label="Show password" aria-pressed="false">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn login-submit w-100">
                            <i class="fas fa-arrow-right-to-bracket me-2" aria-hidden="true"></i>Sign in
                        </button>
                    </form>
                </div>

                <div id="codeTab" role="tabpanel" aria-labelledby="tabCode" hidden>
                    <form method="POST" action="{{ route('student.login.code') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code_reg_number" class="form-label">
                                <i class="fas fa-id-card" aria-hidden="true"></i>Registration number
                            </label>
                            <input type="text"
                                   class="form-control login-input"
                                   id="code_reg_number"
                                   name="reg_number"
                                   required
                                   autocomplete="username"
                                   value="{{ old('reg_number') }}"
                                   placeholder="Enter your registration number">
                        </div>
                        <div class="mb-4">
                            <label for="verification_code" class="form-label">
                                <i class="fas fa-shield-halved" aria-hidden="true"></i>Verification code
                            </label>
                            <div class="code-input-group">
                                <input type="text"
                                       class="form-control login-input"
                                       id="verification_code"
                                       name="code"
                                       required
                                       autocomplete="one-time-code"
                                       inputmode="numeric"
                                       value="{{ old('code') }}"
                                       placeholder="Enter the code sent to your email">
                                <button type="button" class="btn send-code" id="sendCodeButton">
                                    <i class="fas fa-paper-plane me-1" aria-hidden="true"></i>Send code
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn login-submit w-100">
                            <i class="fas fa-check me-2" aria-hidden="true"></i>Verify and sign in
                        </button>
                    </form>
                </div>

                <p class="registration-link">
                    New to ESA? <a href="{{ route('students.create') }}">Start your registration</a>
                </p>
                <div class="login-footnote">
                    <i class="fas fa-shield-halved" aria-hidden="true"></i>
                    <span>Your account details are protected.</span>
                </div>
            </div>
        </section>
    </main>

    <script>
        (function () {
            const passwordTab = document.getElementById('passwordTab');
            const codeTab = document.getElementById('codeTab');
            const passwordButton = document.getElementById('tabPassword');
            const codeButton = document.getElementById('tabCode');

            function showTab(tab) {
                const useCodeTab = tab === 'code';
                passwordTab.hidden = useCodeTab;
                codeTab.hidden = !useCodeTab;
                passwordButton.classList.toggle('active', !useCodeTab);
                codeButton.classList.toggle('active', useCodeTab);
                passwordButton.setAttribute('aria-selected', String(!useCodeTab));
                codeButton.setAttribute('aria-selected', String(useCodeTab));
            }

            passwordButton.addEventListener('click', function () { showTab('password'); });
            codeButton.addEventListener('click', function () { showTab('code'); });

            @if(old('code') || $errors->has('code'))
                showTab('code');
            @endif

            const passwordInput = document.getElementById('password');
            const visibilityButton = document.querySelector('.password-visibility');
            visibilityButton.addEventListener('click', function () {
                const showPassword = passwordInput.type === 'password';
                passwordInput.type = showPassword ? 'text' : 'password';
                visibilityButton.setAttribute('aria-pressed', String(showPassword));
                visibilityButton.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
                visibilityButton.innerHTML = '<i class="fas ' + (showPassword ? 'fa-eye-slash' : 'fa-eye') + '" aria-hidden="true"></i>';
            });

            function showAlert(message, type) {
                const alerts = document.getElementById('loginAlerts');
                const existing = alerts.querySelector('.dynamic-alert');
                if (existing) existing.remove();

                const alert = document.createElement('div');
                alert.className = 'login-alert dynamic-alert' + (type === 'success' ? ' alert-success' : '');
                alert.setAttribute('role', type === 'success' ? 'status' : 'alert');

                const icon = document.createElement('i');
                icon.className = type === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-info';
                icon.setAttribute('aria-hidden', 'true');

                const text = document.createElement('span');
                text.textContent = message;
                alert.append(icon, text);
                alerts.prepend(alert);
            }

            const sendCodeButton = document.getElementById('sendCodeButton');
            sendCodeButton.addEventListener('click', function () {
                const regNumber = document.getElementById('code_reg_number').value.trim();
                if (!regNumber) {
                    showAlert('Enter your registration number before requesting a code.', 'error');
                    document.getElementById('code_reg_number').focus();
                    return;
                }

                const originalContent = sendCodeButton.innerHTML;
                sendCodeButton.disabled = true;
                sendCodeButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1" aria-hidden="true"></i>Sending';

                fetch("{{ route('students.sendVerificationCode') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ reg_number: regNumber })
                })
                .then(function (response) {
                    if (!response.ok) throw new Error('Unable to request a verification code.');
                    return response.json();
                })
                .then(function (data) {
                    showAlert(data.message || 'If your account is eligible, a verification code will be sent to your email.', 'success');
                })
                .catch(function (error) {
                    showAlert(error.message || 'Could not send the code. Please try again.', 'error');
                })
                .finally(function () {
                    sendCodeButton.disabled = false;
                    sendCodeButton.innerHTML = originalContent;
                });
            });

            document.querySelectorAll('.login-content form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (!submitButton) return;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2" aria-hidden="true"></i>Signing in';
                });
            });

            const slides = Array.from(document.querySelectorAll('.story-image'));
            const dots = Array.from(document.querySelectorAll('.story-dot'));
            const titles = [
                'A place to learn. A future to shape.',
                'Every day brings a new possibility.',
                'Growing together, one step at a time.',
                'Your next chapter starts here.'
            ];
            const captions = [
                'Every student has a story. Continue yours with the people and purpose that make ESA feel like home.',
                'Discover your strengths, build new skills, and make the most of every opportunity.',
                'A community of learners, teachers, and friends moving forward together.',
                'Sign in to reconnect with your studies and all that is waiting for you.'
            ];
            const title = document.getElementById('storyTitle');
            const caption = document.getElementById('storyCaption');
            const count = document.getElementById('storyCount');
            const storyPanel = document.querySelector('.story-panel');
            let currentSlide = 0;
            let timer;

            function showSlide(index) {
                currentSlide = (index + slides.length) % slides.length;
                slides.forEach(function (slide, slideIndex) {
                    const active = slideIndex === currentSlide;
                    slide.classList.toggle('is-active', active);
                    slide.setAttribute('aria-hidden', String(!active));
                });
                dots.forEach(function (dot, dotIndex) {
                    const active = dotIndex === currentSlide;
                    dot.classList.toggle('is-active', active);
                    if (active) dot.setAttribute('aria-current', 'true');
                    else dot.removeAttribute('aria-current');
                });
                title.textContent = titles[currentSlide];
                caption.textContent = captions[currentSlide];
                count.textContent = String(currentSlide + 1).padStart(2, '0') + ' / ' + String(slides.length).padStart(2, '0');
            }

            function stopTimer() {
                window.clearInterval(timer);
            }

            function startTimer() {
                stopTimer();
                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && !document.hidden) {
                    timer = window.setInterval(function () { showSlide(currentSlide + 1); }, 6000);
                }
            }

            document.getElementById('previousSlide').addEventListener('click', function () {
                showSlide(currentSlide - 1);
                startTimer();
            });
            document.getElementById('nextSlide').addEventListener('click', function () {
                showSlide(currentSlide + 1);
                startTimer();
            });
            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    showSlide(index);
                    startTimer();
                });
            });

            storyPanel.addEventListener('mouseenter', stopTimer);
            storyPanel.addEventListener('mouseleave', startTimer);
            storyPanel.addEventListener('focusin', stopTimer);
            storyPanel.addEventListener('focusout', function (event) {
                if (!storyPanel.contains(event.relatedTarget)) startTimer();
            });
            document.addEventListener('visibilitychange', startTimer);
            showSlide(0);
            startTimer();
        })();
    </script>
</body>
</html>
