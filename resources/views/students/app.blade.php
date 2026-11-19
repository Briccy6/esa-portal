@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $systemLogo = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $systemName }}</title>
    <link rel="icon" type="image/png" href="{{ $systemLogo }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header */
        .top-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 0.75rem 1rem;
            min-height: 64px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.125rem;
            letter-spacing: -0.025em;
            color: #fff !important;
            text-decoration: none;
        }

        .navbar-brand img {
            height: 32px;
            width: auto;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .user-info .welcome-text {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            line-height: 1.2;
        }

        .user-info .reg-number {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #fff;
            margin: 0;
            line-height: 1.2;
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .mobile-toggle i {
            font-size: 1.25rem;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            min-height: calc(100vh - 64px);
            background: var(--sidebar-bg);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            border-right: 1px solid #1e293b;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-header {
            padding: 1.25rem 1rem 0.75rem;
            border-bottom: 1px solid #1e293b;
        }

        .sidebar-title {
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin: 0;
        }

        /* Navigation Groups */
        .nav-group {
            padding: 0.75rem 0;
        }

        .nav-group-title {
            color: var(--text-light);
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.5rem 1rem 0.25rem;
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link {
            color: var(--text-light);
            padding: 0.625rem 1rem;
            margin: 0.125rem 0.5rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            min-height: 40px;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: #fff;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .sidebar .nav-link.active i {
            color: #fff;
        }

        /* Logout Button */
        .logout-section {
            padding: 0.75rem 0;
            border-top: 1px solid #1e293b;
            margin-top: auto;
        }

        .logout-btn {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .logout-btn:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.5rem;
            background: var(--bg-light);
            min-height: calc(100vh - 64px);
        }

        .content-wrapper {
            background: var(--bg-white);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--sidebar-bg);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .main-content {
                padding: 1.25rem;
            }
            .content-wrapper {
                padding: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                position: fixed;
                left: -260px;
                top: 64px;
                height: calc(100vh - 64px);
                z-index: 1050;
                transform: translateX(0);
            }

            .sidebar.show {
                transform: translateX(260px);
            }

            .main-content {
                padding: 1rem;
                margin-left: 0;
            }

            .content-wrapper {
                padding: 1rem;
                border-radius: 8px;
            }

            .user-info {
                padding: 0.375rem 0.75rem;
            }

            .user-info .welcome-text {
                font-size: 0.6875rem;
            }

            .user-info .reg-number {
                font-size: 0.75rem;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand img {
                height: 28px;
            }

            .sidebar .nav-link {
                min-height: 44px;
                padding: 0.75rem 1rem;
            }

            .nav-group-title {
                padding: 0.75rem 1rem 0.5rem;
                font-size: 0.75rem;
            }

            .sidebar-header {
                padding: 1rem 1rem 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .top-navbar {
                padding: 0.625rem 0.75rem;
                min-height: 56px;
            }

            .sidebar {
                top: 56px;
                height: calc(100vh - 56px);
            }

            .sidebar-overlay {
                top: 56px;
            }

            .main-content {
                padding: 0.75rem;
            }

            .content-wrapper {
                padding: 0.875rem;
            }

            .user-info {
                padding: 0.3125rem 0.625rem;
            }

            .user-info .welcome-text {
                font-size: 0.625rem;
            }

            .user-info .reg-number {
                font-size: 0.6875rem;
            }

            .navbar-brand {
                font-size: 0.9375rem;
            }

            .navbar-brand img {
                height: 24px;
            }

            .mobile-toggle {
                width: 36px;
                height: 36px;
            }

            .mobile-toggle i {
                font-size: 1.125rem;
            }
        }

        @media (max-width: 360px) {
            .navbar-brand span {
                display: none;
            }
            .navbar-brand img {
                margin-right: 0;
            }
        }

        /* Touch-friendly targets */
        @media (max-width: 768px) {
            .btn, .nav-link, button, a {
                min-height: 44px;
                min-width: 44px;
            }

            .form-control, input, select, textarea {
                font-size: 16px;
                min-height: 44px;
            }
        }

        /* Prevent body scroll when sidebar is open */
        body.sidebar-open {
            overflow: hidden;
        }
    </style>
</head>
<body>
    {{-- Top Navigation --}}
    <nav class="navbar navbar-dark top-navbar">
        <div class="container-fluid d-flex align-items-center">
            <button class="mobile-toggle me-2" type="button" onclick="toggleSidebar()" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="{{ route('student.dashboard') }}">
                <img src="{{ $systemLogo }}" alt="Logo" class="me-2">
                <span>{{ $systemName }}</span>
            </a>
            <div class="ms-auto">
                <div class="user-info">
                    <p class="welcome-text">Welcome back</p>
                    <p class="reg-number">{{ Auth::guard('student')->user()->reg_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </nav>

    {{-- Sidebar Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="d-flex">
        {{-- Sidebar --}}
        <aside class="sidebar d-flex flex-column" id="sidebar">
            <div class="sidebar-header">
                <h6 class="sidebar-title">Student Portal</h6>
            </div>

            <div class="flex-grow-1">
                {{-- Overview Section --}}
                <div class="nav-group">
                    <div class="nav-group-title">Overview</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('student.dashboard') }}"
                               class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Academic Section --}}
                <div class="nav-group">
                    <div class="nav-group-title">Academic</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('student.courses') }}" class="nav-link">
                                <i class="bi bi-journal-text"></i>
                                Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.live-classes.index') }}" class="nav-link {{ request()->routeIs('student.live-classes.*') ? 'active' : '' }}">
                                <i class="bi bi-broadcast"></i>
                                Live Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.timetables.index') }}" class="nav-link">
                                <i class="bi bi-calendar2-week"></i>
                                Timetables
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.resources.index') }}" class="nav-link {{ request()->routeIs('student.resources.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-text"></i>
                                Study References
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.exams') }}" class="nav-link {{ request()->routeIs('student.exams') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-text"></i>
                                Exams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.report-cards.index') }}" class="nav-link {{ request()->routeIs('student.report-cards.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-text"></i>
                                Report Cards
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.transcript.send') }}" class="nav-link" title="Send your academic transcript via email">
                                <i class="bi bi-envelope-paper"></i>
                                Academic Transcript
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('to-whom.index') }}" class="nav-link {{ request()->routeIs('to-whom.*') ? 'active' : '' }}">
                                <i class="bi bi-file-text"></i>
                                Professional Letters
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.babyeyi.dashboard') }}" class="nav-link {{ request()->routeIs('student.babyeyi.*') ? 'active' : '' }}">
                                <i class="bi bi-file-contract"></i>
                                Term Registration
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Financial Section --}}
                <div class="nav-group">
                    <div class="nav-group-title">Financial</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('subscriptions.index') }}"
                               class="nav-link {{ request()->routeIs('subscriptions.index') ? 'active' : '' }}">
                                <i class="bi bi-star"></i>
                                My Subscriptions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.invoices') }}" class="nav-link">
                                <i class="bi bi-receipt"></i>
                                Invoices
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.payment-schedule') }}" class="nav-link">
                                <i class="bi bi-calendar3"></i>
                                Payment Schedule
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Account Section --}}
                <div class="nav-group">
                    <div class="nav-group-title">Account</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('students.profile') }}"
                               class="nav-link {{ request()->routeIs('students.profile') ? 'active' : '' }}">
                                <i class="bi bi-person-circle"></i>
                                Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Logout Section --}}
            <div class="logout-section">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link logout-btn"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            @include('components.laravel-flash')
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            body.classList.toggle('sidebar-open');

            if (sidebar.classList.contains('show')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            body.classList.remove('sidebar-open');
            body.style.overflow = '';
        }

        // Close sidebar on window resize if on desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // Close sidebar with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        // Close sidebar when clicking a nav link on mobile
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
