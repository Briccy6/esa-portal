@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
    $systemLogo = \App\Helpers\LogoHelper::getLogoUrl();
    $studentUser = Auth::guard('student')->user();
    $studentProfilePhoto = null;
    $studentAccessRestricted = false; // Temporarily disabled to debug

    if (!$studentProfilePhoto && $studentUser && $studentUser->passport_photo) {
        $storedProfilePhoto = $studentUser->passport_photo;
        $profilePhotoPath = ltrim($storedProfilePhoto, '/');

        if (str_starts_with($storedProfilePhoto, 'data:') || filter_var($storedProfilePhoto, FILTER_VALIDATE_URL)) {
            $studentProfilePhoto = $storedProfilePhoto;
        } elseif (str_starts_with($profilePhotoPath, 'storage/')) {
            $studentProfilePhoto = asset($profilePhotoPath);
        } elseif (preg_match('/\.(jpe?g|png|gif|webp)$/i', $storedProfilePhoto) && file_exists(public_path($storedProfilePhoto))) {
            $studentProfilePhoto = asset($storedProfilePhoto);
        } elseif (preg_match('/\.(jpe?g|png|gif|webp)$/i', $storedProfilePhoto)
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($storedProfilePhoto)) {
            $studentProfilePhoto = \Illuminate\Support\Facades\Storage::disk('public')->url($storedProfilePhoto);
        } else {
            $profilePhotoMimeType = str_starts_with($storedProfilePhoto, 'iVBORw0KGgo') ? 'image/png' : 'image/jpeg';
            $studentProfilePhoto = 'data:' . $profilePhotoMimeType . ';base64,' . $storedProfilePhoto;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $systemName }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ $systemLogo }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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

        .student-account-trigger {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            max-width: min(340px, 42vw);
            padding: 0.375rem 0.75rem;
            color: #fff;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }

        .student-account-trigger::after {
            flex-shrink: 0;
        }

        .student-avatar {
            display: grid;
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            place-items: center;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.72);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1.15rem;
        }

        .student-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-account-copy {
            min-width: 0;
        }

        .student-account-copy .welcome-text,
        .student-account-copy .reg-number {
            display: block;
        }

        .student-account-copy .reg-number {
            overflow: hidden;
            max-width: 220px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .student-account-menu {
            min-width: 220px;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
        }

        .student-account-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            border-radius: 8px;
            padding: 0.65rem 0.75rem;
        }

        .student-account-menu .dropdown-item i {
            width: 1rem;
            text-align: center;
        }

        .student-account-menu .dropdown-item.logout-item {
            color: #b42318;
        }

        .student-account-menu .dropdown-item.logout-item:hover {
            background: #fff1f0;
            color: #8f1d15;
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
            flex: 0 0 260px;
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
            min-width: 0;
            padding: 1.5rem;
            background: var(--bg-light);
            min-height: calc(100vh - 64px);
        }

        .content-wrapper {
            width: 100%;
            min-width: 0;
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

            .student-account-trigger {
                max-width: 52vw;
                gap: 0.45rem;
                padding: 0.3rem 0.45rem;
            }

            .student-avatar {
                width: 36px;
                height: 36px;
                flex-basis: 36px;
            }

            .student-account-copy .welcome-text {
                display: none;
            }

            .student-account-copy .reg-number {
                max-width: 140px;
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

        /* Mobile Responsive Fixes */
        @media (max-width: 768px) {
            /* Prevent horizontal scroll */
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            /* Tables - make them scrollable horizontally */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
            }

            /* Card containers */
            .card {
                margin-bottom: 1rem;
                border-radius: 0.5rem;
            }

            .card-body {
                overflow: hidden;
                word-wrap: break-word;
            }

            /* Fix modal width on mobile */
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .modal-content {
                border-radius: 0.5rem;
            }

            /* Button sizing */
            .btn {
                white-space: normal;
                word-wrap: break-word;
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-group .btn {
                width: auto;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.375rem 0.75rem;
            }

            /* Badge sizing */
            .badge {
                font-size: 0.75rem;
                white-space: normal;
                word-wrap: break-word;
            }

            /* Table text */
            table {
                font-size: 0.875rem;
            }

            th, td {
                padding: 0.5rem;
                word-wrap: break-word;
                white-space: normal;
                min-width: 60px;
            }

            /* Sidebar adjustments - keep the original mobile styles from earlier */
            .sidebar {
                position: fixed;
                left: -260px;
                top: 64px;
                height: calc(100vh - 64px);
                z-index: 1050;
                transform: translateX(0);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(260px);
            }

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

            /* Main content padding on mobile */
            .main-content {
                padding: 1rem;
            }

            /* Dashboard cards */
            .stats-card {
                margin-bottom: 1rem;
            }

            /* Action buttons in tables */
            .table .btn {
                margin: 0.25rem 0;
                display: inline-block;
            }
        }

        @media (max-width: 480px) {
            /* Extra small mobile */
            .card-body {
                padding: 0.75rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
            }

            /* Collapse table columns on very small screens */
            .table-responsive {
                font-size: 0.75rem;
            }
        }

        /* Ensure images don't overflow */
        img {
            max-width: 100%;
            height: auto;
        }

        /* Prevent text selection issues */
        * {
            -webkit-tap-highlight-color: transparent;
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
            <div class="ms-auto d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="user-info student-account-trigger dropdown-toggle" type="button"
                            id="studentAccountDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            aria-label="Student account menu">
                        <span class="student-avatar">
                            @if($studentProfilePhoto)
                                <img src="{{ $studentProfilePhoto }}" alt="Student profile photo">
                            @else
                                <i class="bi bi-person-fill" aria-hidden="true"></i>
                            @endif
                        </span>
                        <span class="student-account-copy">
                            <span class="welcome-text">Welcome back</span>
                            <span class="reg-number">{{ $studentUser->reg_number ?? 'N/A' }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end student-account-menu" aria-labelledby="studentAccountDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('students.profile') }}">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                My profile
                            </a>
                        </li>
                    </ul>
                </div>
                <form action="{{ route('student.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm" aria-label="Log out">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
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

                @if(!$studentAccessRestricted)
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
                                <i class="bi bi-patch-question"></i>
                                Test Skills
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.assignments.index') }}" class="nav-link {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                                <i class="bi bi-journal-check"></i>
                                Assignments
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
                                Official Documents
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
                @endif

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

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Modal Cleanup Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix Bootstrap modal backdrop issue
        document.addEventListener('hidden.bs.modal', function(event) {
            // Remove any remaining backdrop elements
            document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                backdrop.remove();
            });
            // Ensure body styles are restored
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });

        // Handle all modal close buttons properly
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(button) {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    });
    </script>

    @yield('scripts')

    <!-- Passport Photo Rejection Alert Modal -->
    @if($studentUser && \Illuminate\Support\Facades\Schema::hasColumn('students', 'passport_photo_verification_status') && $studentUser->passport_photo_verification_status === 'rejected')
    <div class="modal fade" id="passportRejectionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>Passport Photo Rejected
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Important:</strong> Your passport photograph was rejected by our system.
                    </div>
                    
                    <p class="mb-3">The photograph you uploaded does not meet our passport photo requirements. Please upload a <strong>real passport-style photograph</strong>, not just any image you find.</p>
                    
                    @if($studentUser->passport_photo_rejection_reason)
                    <div class="alert alert-danger">
                        <strong>Reason for rejection:</strong><br>
                        {{ \App\Services\PassportPhotoVerificationService::getErrorMessage($studentUser->passport_photo_rejection_reason) }}
                    </div>
                    @endif
                    
                    <h6 class="fw-bold mb-2">Required Photo Standards:</h6>
                    <ul class="small mb-3">
                        <li>Plain white background</li>
                        <li>Professional passport-style portrait</li>
                        <li>Exactly one visible human face</li>
                        <li>Face centered and looking directly at camera</li>
                        <li>Portrait orientation (7:9 aspect ratio)</li>
                        <li>Clear and not blurry</li>
                        <li>Maximum file size: 2 MB</li>
                    </ul>
                    
                    <p class="text-muted small mb-0">
                        <strong>Not accepted:</strong> Selfies, group photos, colored backgrounds, scanned documents, landscape photos, or blurry images.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('students.profile') }}" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Upload New Photo
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show rejection modal on page load
        var rejectionModal = new bootstrap.Modal(document.getElementById('passportRejectionModal'));
        rejectionModal.show();
    });
    </script>
    @endif

    <!-- ESA Live Support Chat Widget -->
    @auth('student')
    <x-chat-widget />
    @endauth
</body>
</html>
