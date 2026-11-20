@php
    $portalName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'Excellent Servant Academy');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7f4">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') | {{ $portalName }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/portal-login.css') }}" rel="stylesheet">
</head>
<body>
    <main class="portal-login-shell">
        <aside class="portal-story-panel" aria-label="Life at Excellent Servant Academy">
            <div class="portal-story-slides" aria-live="polite">
                <img class="portal-story-image is-active" src="{{ asset('images/1.jpg') }}" alt="Students at Excellent Servant Academy" fetchpriority="high">
                <img class="portal-story-image" src="{{ asset('images/2.jpg') }}" alt="Students learning together at the academy" loading="lazy">
                <img class="portal-story-image" src="{{ asset('images/4.jpg') }}" alt="A moment from student life at the academy" loading="lazy">
                <img class="portal-story-image" src="{{ asset('images/5.jpg') }}" alt="Students taking part in academy activities" loading="lazy">
            </div>
            <div class="portal-story-brand">{{ $portalName }}</div>
            <div class="portal-story-copy">
                <div class="portal-story-kicker">Learning with purpose</div>
                <h2 class="portal-story-title">A place to learn. A future to shape.</h2>
                <p class="portal-story-caption">A community of learners and educators, building the next chapter together.</p>
                <div class="portal-story-controls">
                    <div class="portal-story-arrows">
                        <button class="portal-story-arrow" type="button" data-story-previous aria-label="Show previous student photo">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        </button>
                        <button class="portal-story-arrow" type="button" data-story-next aria-label="Show next student photo">
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="portal-story-dots" role="group" aria-label="Choose a student photo">
                        <button class="portal-story-dot is-active" type="button" aria-label="Show photo 1" aria-current="true"></button>
                        <button class="portal-story-dot" type="button" aria-label="Show photo 2"></button>
                        <button class="portal-story-dot" type="button" aria-label="Show photo 3"></button>
                        <button class="portal-story-dot" type="button" aria-label="Show photo 4"></button>
                    </div>
                    <span class="portal-story-count" aria-hidden="true">01 / 04</span>
                </div>
            </div>
        </aside>

        <section class="portal-auth-panel" aria-labelledby="portalLoginHeading">
            <div class="portal-auth-content">
                <a class="portal-auth-brand" href="{{ url('/') }}" aria-label="{{ $portalName }} home">
                    <img src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="" class="portal-auth-logo">
                    <span>
                        <span class="portal-auth-brand-name">{{ $portalName }}</span>
                        <span class="portal-auth-brand-label">@yield('portal-brand-label', 'Secure portal access')</span>
                    </span>
                </a>

                <div class="portal-auth-heading">
                    <span class="portal-auth-eyebrow">@yield('portal-eyebrow', 'Welcome')</span>
                    <h1 id="portalLoginHeading">@yield('portal-heading', 'Sign in to your account')</h1>
                    <p>@yield('portal-description', 'Enter your account details to continue.')</p>
                </div>

                @if(session('error'))
                    <div class="portal-alert portal-alert-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('success') || session('status'))
                    <div class="portal-alert portal-alert-success" role="status">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span>{{ session('success') ?? session('status') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="portal-alert portal-alert-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="portal-auth-card">
                    @yield('content')
                </div>

                @hasSection('portal-links')
                    <div class="portal-auth-links">
                        @yield('portal-links')
                    </div>
                @endif

                <div class="portal-auth-footnote">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                    <span>Your account details are protected.</span>
                </div>
            </div>
        </section>
    </main>

    <script>
        (function () {
            const firstField = document.querySelector('[data-autofocus]');
            if (firstField && window.matchMedia('(min-width: 701px)').matches) {
                firstField.focus({ preventScroll: true });
            }

            document.querySelectorAll('[data-password-toggle]').forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    const input = document.getElementById(toggle.getAttribute('aria-controls'));
                    if (!input) return;
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    toggle.setAttribute('aria-pressed', String(show));
                    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                    toggle.innerHTML = '<i class="fa-solid ' + (show ? 'fa-eye-slash' : 'fa-eye') + '" aria-hidden="true"></i>';
                });
            });

            document.querySelectorAll('.portal-auth-card form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const submit = form.querySelector('[type="submit"]');
                    if (!submit) return;
                    submit.disabled = true;
                    submit.classList.add('is-submitting');
                    submit.setAttribute('aria-busy', 'true');
                });
            });

            const panel = document.querySelector('.portal-story-panel');
            const slides = Array.from(document.querySelectorAll('.portal-story-image'));
            const dots = Array.from(document.querySelectorAll('.portal-story-dot'));
            const title = document.querySelector('.portal-story-title');
            const caption = document.querySelector('.portal-story-caption');
            const count = document.querySelector('.portal-story-count');
            const messages = [
                ['A place to learn. A future to shape.', 'A community of learners and educators, building the next chapter together.'],
                ['Every day brings a new possibility.', 'Discover strengths, grow new skills, and make the most of every opportunity.'],
                ['Growing together, one step at a time.', 'Learn alongside a community committed to curiosity, care, and achievement.'],
                ['Your next chapter starts here.', 'Sign in and continue your journey with Excellent Servant Academy.']
            ];
            let current = 0;
            let timer;

            function showSlide(index) {
                current = (index + slides.length) % slides.length;
                slides.forEach(function (slide, i) {
                    const active = i === current;
                    slide.classList.toggle('is-active', active);
                    slide.setAttribute('aria-hidden', String(!active));
                });
                dots.forEach(function (dot, i) {
                    const active = i === current;
                    dot.classList.toggle('is-active', active);
                    if (active) dot.setAttribute('aria-current', 'true');
                    else dot.removeAttribute('aria-current');
                });
                title.textContent = messages[current][0];
                caption.textContent = messages[current][1];
                count.textContent = String(current + 1).padStart(2, '0') + ' / ' + String(slides.length).padStart(2, '0');
            }

            function restartTimer() {
                window.clearInterval(timer);
                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && !document.hidden) {
                    timer = window.setInterval(function () { showSlide(current + 1); }, 6500);
                }
            }

            document.querySelector('[data-story-previous]').addEventListener('click', function () {
                showSlide(current - 1);
                restartTimer();
            });
            document.querySelector('[data-story-next]').addEventListener('click', function () {
                showSlide(current + 1);
                restartTimer();
            });
            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    showSlide(index);
                    restartTimer();
                });
            });
            panel.addEventListener('mouseenter', function () { window.clearInterval(timer); });
            panel.addEventListener('mouseleave', restartTimer);
            panel.addEventListener('focusin', function () { window.clearInterval(timer); });
            panel.addEventListener('focusout', function (event) {
                if (!panel.contains(event.relatedTarget)) restartTimer();
            });
            document.addEventListener('visibilitychange', restartTimer);
            showSlide(0);
            restartTimer();
        })();
    </script>
</body>
</html>
