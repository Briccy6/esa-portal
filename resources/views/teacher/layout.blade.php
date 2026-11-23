@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo();

    $courses = $courses ?? (Auth::guard('teacher')->check() ? Auth::guard('teacher')->user()->courses()->with('lessons.units')->get() : collect());
    
    // Get teacher's course assignments with branch information
    if (Auth::guard('teacher')->check()) {
        $teacher = Auth::guard('teacher')->user();
        $branchCourses = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->distinct()
            ->get(['course_id', 'branch_id']);
        
        // Add branch_id to courses
        $courses = $courses->map(function($course) use ($branchCourses) {
            $branchAssignment = $branchCourses->firstWhere('course_id', $course->id);
            $course->branch_id = $branchAssignment ? $branchAssignment->branch_id : null;
            return $course;
        });
    }
        
        // Ensure announcements is always available
        $announcements = $announcements ?? \App\Models\Announcement::latest()->take(5)->get();
    
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $systemName ?? 'Teacher Dashboard' }}</title>
            <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --light-bg: #f8fafc;
            --sidebar-bg: #ffffff;
            --sidebar-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --hover-bg: #f1f5f9;
            --active-bg: #dbeafe;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Professional Navbar */
        .navbar {
            background: var(--primary-color);
            box-shadow: var(--shadow-md);
            border-bottom: none;
            padding: 0.75rem 0;
            min-height: 64px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.025em;
            color: white !important;
        }

        /* Enhanced Sidebar */
        .sidebar {
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            min-height: 100vh;
            padding: 1.5rem 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--secondary-color) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--text-primary);
        }

        .sidebar-header {
            padding: 1rem 1.5rem 2rem;
            border-bottom: 1px solid var(--sidebar-border);
            margin-bottom: 1rem;
        }

        .sidebar-header h4 {
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            font-size: 1.1rem;
        }

        .sidebar-header .sidebar-caption {
            display: block;
            margin-top: 0.35rem;
            color: var(--text-secondary);
            font-size: 0.82rem;
        }

        .sidebar-section-label {
            padding: 1rem 1.5rem 0.35rem;
            color: #8491a2;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .sidebar .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 0.75rem;
            margin: 0.25rem 1rem;
            padding: 0.875rem 1.25rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            text-decoration: none;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background: var(--hover-bg);
            color: var(--text-primary);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: #fff;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: #fff;
            border-radius: 0 2px 2px 0;
        }

        .sidebar .nav-link i {
            margin-right: 0.875rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .sidebar .nav-link.assignment-nav-link,
        .offcanvas .nav-link.assignment-nav-link {
            color: #38564a;
        }

        .sidebar .nav-link.assignment-nav-link:hover,
        .sidebar .nav-link.assignment-nav-link.active {
            background: #eaf3ed;
            color: #1d5b3b;
        }

        .sidebar .nav-link.assignment-nav-link.active,
        .offcanvas .nav-link.assignment-nav-link.active {
            box-shadow: inset 3px 0 #34724b;
        }

        .sidebar .nav-link.assignment-create-link,
        .offcanvas .nav-link.assignment-create-link {
            display: flex;
            align-items: center;
            margin: 0.35rem 1rem 0.75rem;
            padding: 0.8rem 1.1rem;
            border-radius: 0.75rem;
            font-weight: 650;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
            margin-top: 0.15rem;
            color: #fff;
            background: #245c40;
            box-shadow: 0 5px 12px rgb(36 92 64 / 0.18);
        }

        .sidebar .nav-link.assignment-create-link:hover,
        .sidebar .nav-link.assignment-create-link.active,
        .offcanvas .nav-link.assignment-create-link:hover,
        .offcanvas .nav-link.assignment-create-link.active {
            color: #fff;
            background: #194b33;
        }

        .sidebar .nav-link.assignment-create-link.active::before {
            background: #e6bd69;
        }

        .offcanvas .nav-link.assignment-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0.25rem 1rem;
            padding: 0.8rem 1.1rem;
            border-radius: 0.75rem;
            text-decoration: none;
        }

        .offcanvas .nav-link.assignment-nav-link i,
        .offcanvas .nav-link.assignment-create-link i {
            width: 20px;
            text-align: center;
        }

        /* Color-coded nav links */
        .nav-link.text-warning:hover { background: #fef3c7; color: var(--warning-color) !important; }
        .nav-link.text-info:hover { background: #cffafe; color: var(--info-color) !important; }
        .nav-link.text-success:hover { background: #d1fae5; color: var(--success-color) !important; }
        .nav-link.text-primary:hover { background: var(--active-bg); color: var(--primary-color) !important; }
        .nav-link.text-secondary:hover { background: #f1f5f9; color: var(--secondary-color) !important; }

        /* Announcement link styling */
        .announcement-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: block;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .announcement-link:hover {
            color: var(--primary-light);
        }

        .announcement-link:last-child {
            border-bottom: none;
        }

        /* Main content area */
        .main-content {
            margin-left: 280px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* User dropdown styling */
        .user-dropdown {
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            padding: 0.25rem;
            transition: all 0.2s ease;
        }

        .user-dropdown:hover {
            border-color: rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.1);
        }

        .user-dropdown img {
            border: 2px solid rgba(255,255,255,0.3);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: 0.75rem;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: var(--hover-bg);
            transform: translateX(4px);
        }

        /* Quick action button */
        .quick-action-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            font-weight: 500;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .quick-action-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: white;
        }

        /* Modal enhancements */
        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 1.25rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-content {
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-lg);
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            background: #f8fafc;
            border-radius: 0 0 0.75rem 0.75rem;
            padding: 1.5rem;
        }

        /* Form styling */
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Button enhancements */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* Responsive design */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .offcanvas {
                width: 280px !important;
            }

            /* Mobile tables */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -1rem 1rem -1rem;
                padding: 0 1rem;
            }

            .table {
                font-size: 0.875rem;
                min-width: 800px;
            }

            .table th,
            .table td {
                padding: 0.5rem;
                white-space: nowrap;
            }

            /* Mobile cards */
            .card {
                margin-bottom: 1rem;
                border-radius: 0.5rem;
            }

            .card-body {
                padding: 1rem;
            }

            /* Mobile forms */
            .form-control,
            .form-select {
                font-size: 16px;
                min-height: 44px;
            }

            .btn {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            /* Prevent horizontal scroll */
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .navbar {
                padding: 0.625rem 0;
                min-height: 56px;
            }

            .main-content {
                padding: 0.75rem 0.5rem;
            }

            /* Mobile tables */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
            }

            .table {
                font-size: 0.8125rem;
            }

            .table th,
            .table td {
                padding: 0.375rem;
                word-wrap: break-word;
                white-space: normal;
                min-width: 60px;
            }

            /* Extra small cards */
            .card-body {
                padding: 0.75rem;
                overflow: hidden;
                word-wrap: break-word;
            }

            /* Action buttons */
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.8125rem;
                white-space: normal;
                word-wrap: break-word;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-group .btn {
                width: auto;
            }

            /* Badges */
            .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                white-space: normal;
                word-wrap: break-word;
            }

            /* Modal adjustments */
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .modal-content {
                border-radius: 0.5rem;
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

            /* Form controls */
            .form-control,
            .form-select {
                font-size: 16px; /* Prevent iOS zoom */
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Enhanced mobile menu */
        .mobile-menu-toggle {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 6px;
            padding: 0.5rem;
            transition: all 0.2s ease;
        }

            .quick-action-btn:hover {
                background: rgba(255,255,255,0.3);
                border-color: rgba(255,255,255,0.5);
                transform: translateY(-1px);
                box-shadow: var(--shadow-md);
                color: white;
            }
    </style>
    
    <!-- MathJax for Mathematical Expressions -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true,
                processEnvironments: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
            },
            startup: {
                typeset: false
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" id="MathJax-script" async></script>
</head>
<body>
@php
    // Redirect to login if not authenticated
    if (!Auth::guard('teacher')->check()) {
        header('Location: ' . route('teacher.login'));
        exit;
    }
    $teacher = Auth::guard('teacher')->user();

    // Terms for the selected academic year (for Class Daily term selector)
    $classDailyYearId = \App\Services\AcademicYearContext::selectedId();
    $classDailyTerms = $classDailyYearId
        ? \App\Models\Term::where('academic_year_id', $classDailyYearId)->orderBy('start_date')->get(['id', 'name', 'term'])
        : collect();
@endphp

<!-- Enhanced Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <button class="mobile-menu-toggle d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
            <i class="bi bi-list fs-5"></i>
        </button>
        
        <span class="navbar-brand">
            <i class="bi bi-mortarboard me-2"></i>
            {{ $systemName ?? config('app.name', 'ESA Portal') }}
        </span>
        
        <div class="ms-auto d-flex align-items-center">
            @include('partials.academic-year-switcher', [
                'switcherClass' => 'me-3',
                'labelClass' => 'text-white-50',
                'selectClass' => 'bg-dark text-white border-secondary',
            ])

            <!-- Message Icon Dropdown -->
            <div class="dropdown me-3">
                <a href="#" class="text-white position-relative" id="messagesDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem;">
                    <i class="bi bi-envelope"></i>
                  
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.messages.inbox') }}">
                            <i class="bi bi-inbox me-2 text-primary"></i>Inbox
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.messages.sent') }}">
                            <i class="bi bi-send me-2 text-success"></i>Sent Messages
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.messages.create') }}">
                            <i class="bi bi-pencil-square me-2 text-secondary"></i>New Message
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Quick Action Button -->
            <button class="quick-action-btn me-3 d-none d-lg-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#classDailyModal">
                <i class="bi bi-journal-text me-2"></i>
                <span>Class Daily</span>
            </button>
            
            <!-- User Dropdown -->
            <div class="dropdown">
                <a href="#" class="user-dropdown d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($teacher->photo)
                        <img src="data:image/jpeg;base64,{{ $teacher->photo }}" alt="Profile" class="rounded-circle me-2" width="40" height="40" style="object-fit:cover;">
                    @else
                        <img src="{{ asset('default-user.png') }}" alt="Profile" class="rounded-circle me-2" width="40" height="40" style="object-fit:cover;">
                    @endif
                    <div class="d-none d-md-block text-start">
                        <div class="fw-semibold">{{ $teacher->teacher_name ?? 'Teacher' }}</div>
                        <small class="opacity-75"><b>Department:</b>{{$teacher->department}}</small><br>
                        <small class="opacity-70">{{ $teacher->branches->pluck('name')->join(', ') }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.profile.edit') }}">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i>Update Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('teacher.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="d-flex">
    <!-- Enhanced Sidebar (Desktop) -->
    <nav class="sidebar d-none d-lg-block">
        <div class="sidebar-header">
            <h4><i class="bi bi-mortarboard me-2"></i>Teacher workspace</h4>
            <span class="sidebar-caption">{{ Auth::guard('teacher')->user()->teacher_name ?? 'Teaching and learning' }}</span>
        </div>
        <ul class="nav flex-column">
            <li class="sidebar-section-label">Overview</li>
            <li class="nav-item">
                <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-section-label">Teaching</li>
            <li class="nav-item">
                <a href="#" class="nav-link text-warning" data-bs-toggle="modal" data-bs-target="#classDailyModal">
                    <i class="bi bi-journal-text"></i>
                    <span>Class Daily</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('teacher.courses.details') }}" class="nav-link text-info">
                    <i class="bi bi-info-circle"></i>
                    <span>Course Details</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('teacher.exams.index') }}" class="nav-link text-primary">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Exams</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('teacher.question-banks.index') }}" class="nav-link text-success">
                    <i class="bi bi-database"></i>
                    <span>Question Banks</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('teacher.marks.index') }}" class="nav-link text-success">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Marks</span>
                </a>
            </li>

            <li class="sidebar-section-label">Assignments</li>
            <li class="nav-item">
                <a href="{{ route('teacher.assignments.index') }}" class="nav-link assignment-nav-link {{ request()->routeIs('teacher.assignments.index', 'teacher.assignments.show', 'teacher.assignments.edit-deadline', 'teacher.assignments.configure') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i>
                    <span>Review submissions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('teacher.assignments.create') }}" class="nav-link assignment-create-link {{ request()->routeIs('teacher.assignments.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i>
                    <span>Create assignment</span>
                </a>
            </li>

            <li class="sidebar-section-label">Resources &amp; schedule</li>
            <li class="nav-item">
                <a href="{{ route('teacher.resources.create') }}" class="nav-link text-secondary">
                    <i class="bi bi-upload"></i>
                    <span>Upload Resources</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('teacher.timetables.index') }}" class="nav-link text-info {{ request()->routeIs('teacher.timetables.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-week"></i>
                    <span>My Timetables</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('teacher.dailies.index') }}" class="nav-link text-primary">
                    <i class="bi bi-calendar-check"></i>
                    <span>My Dailies</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Enhanced Offcanvas Sidebar (Mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title fw-bold" id="sidebarOffcanvasLabel">
                <i class="bi bi-mortarboard me-2"></i>
                {{ $systemName ?? config('app.name', 'ESA Portal') }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column py-3">
                <li class="sidebar-section-label">Overview</li>
                <li class="nav-item">
                    <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-section-label">Teaching</li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-warning" data-bs-toggle="modal" data-bs-target="#classDailyModal">
                        <i class="bi bi-journal-text"></i>
                        <span>Class Daily</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('teacher.courses.details') }}" class="nav-link text-info">
                        <i class="bi bi-info-circle"></i>
                        <span>Course Details</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('teacher.exams.index') }}" class="nav-link text-primary">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Exams</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('teacher.question-banks.index') }}" class="nav-link text-success">
                        <i class="bi bi-database"></i>
                        <span>Question Banks</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('teacher.marks.index') }}" class="nav-link text-success">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Marks</span>
                    </a>
                </li>

                <li class="sidebar-section-label">Assignments</li>
                <li class="nav-item">
                    <a href="{{ route('teacher.assignments.index') }}" class="nav-link assignment-nav-link {{ request()->routeIs('teacher.assignments.index', 'teacher.assignments.show', 'teacher.assignments.edit-deadline', 'teacher.assignments.configure') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i>
                        <span>Review submissions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('teacher.assignments.create') }}" class="nav-link assignment-create-link {{ request()->routeIs('teacher.assignments.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Create assignment</span>
                    </a>
                </li>

                <li class="sidebar-section-label">Resources &amp; schedule</li>
                <li class="nav-item">
                    <a href="{{ route('teacher.resources.create') }}" class="nav-link text-secondary">
                        <i class="bi bi-upload"></i>
                        <span>Upload Resources</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('teacher.timetables.index') }}" class="nav-link text-info {{ request()->routeIs('teacher.timetables.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-week"></i>
                        <span>My Timetables</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('teacher.dailies.index') }}" class="nav-link text-primary">
                        <i class="bi bi-calendar-check"></i>
                        <span>My Dailies</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="main-content">
        @yield('content')
        
        <!-- Notice Board Card -->
        <div class="col-md-12">
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <i class="bi bi-megaphone fs-4 text-danger"></i>
                    <h5 class="card-title d-inline ms-2">Announcements</h5>
                    <ul class="mt-3">
                        @forelse($announcements as $announcement)
                            <li>
                                <a href="#"
                                   class="announcement-link"
                                   data-bs-toggle="modal"
                                   data-bs-target="#announcementModal"
                                   data-title="{{ $announcement->title }}"
                                   data-content="{{ base64_encode($announcement->content) }}"
                                   data-date="{{ $announcement->created_at->format('M d, Y H:i') }}"
                                   data-sender="{{ $announcement->sender ?? 'Admin' }}"
                                   @if($announcement->attachment)
                                       data-attachment="{{ asset('storage/' . $announcement->attachment) }}"
                                   @endif
                                >
                                    {{ $announcement->title }}
                                </a>
                            </li>
                        @empty
                            <li>No announcements at this time.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Enhanced Class Daily Modal -->
<div class="modal fade" id="classDailyModal" tabindex="-1" aria-labelledby="classDailyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('teacher.daily.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="classDailyModalLabel">
                    <i class="bi bi-journal-text me-2"></i>Class Daily Activity
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Daily Activities Done (Multiple) -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-list-check me-1"></i>Daily Activities Done
                    </label>
                    <div id="activitiesList">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="activities[]" required placeholder="Enter activity description">
                            <button type="button" class="btn btn-outline-danger remove-activity" style="display:none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-success btn-sm" id="addActivityBtn">
                        <i class="bi bi-plus-circle me-1"></i>Add Another Activity
                    </button>
                </div>

                <!-- Branch Selection -->
                <div class="mb-3">
                    <label for="branch" class="form-label">
                        <i class="bi bi-building me-1"></i>Branch
                    </label>
                    <select class="form-select" id="branch" name="branch_id" required>
                        <option value="">Select branch</option>
                        @foreach(auth('teacher')->user()->branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select the branch where this class was held</small>
                </div>

                <!-- Term Selection -->
                <div class="mb-3">
                    <label for="daily_term" class="form-label">
                        <i class="bi bi-calendar3 me-1"></i>Term <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="daily_term" name="term_id" required>
                        <option value="">Select term</option>
                        @foreach($classDailyTerms as $term)
                            <option value="{{ $term->id }}">{{ $term->name ?: $term->term }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Only lessons for the selected term are shown</small>
                </div>

                <!-- Course Selection (Multiple with Search) -->
                <div class="mb-3">
                    <label for="courses" class="form-label">
                        <i class="bi bi-book me-1"></i>Courses Taught
                    </label>
                    <select class="form-select select2" id="courses" name="courses[]" multiple required disabled>
                        <option value="">Select branch first</option>
                    </select>
                    <small class="text-muted">Select multiple courses. Only lessons you teach in ALL selected courses will be shown.</small>
                </div>

                <!-- Lesson Selection -->
                <div class="mb-3">
                    <label for="lesson" class="form-label">
                        <i class="bi bi-journal-bookmark me-1"></i>Lesson
                    </label>
                    <select class="form-select" id="lesson" name="lesson" required>
                        <option value="">Select lesson</option>
                    </select>
                </div>

                <!-- Unit Selection -->
                <div class="mb-3">
                    <label for="unit" class="form-label">
                        <i class="bi bi-diagram-3 me-1"></i>Unit
                    </label>
                    <select class="form-select" id="unit" name="unit" required>
                        <option value="">Select unit</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- Total Students Studied -->
                        <div class="mb-3">
                            <label for="total_students" class="form-label">
                                <i class="bi bi-people me-1"></i>Total Students Present
                            </label>
                            <input type="number" class="form-control" id="total_students" name="total_students" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Hours Studied -->
                        <div class="mb-3">
                            <label for="hours_studied" class="form-label">
                                <i class="bi bi-clock me-1"></i>Hours Studied
                            </label>
                            <input type="number" class="form-control" id="hours_studied" name="hours_studied" min="0.5" step="0.5" required>
                        </div>
                    </div>
                </div>

                <!-- Quiz/Test Section -->
                <div class="mb-3">
                    <button type="button" class="btn btn-info" id="showQuizSection">
                        <i class="bi bi-question-circle me-2"></i>Add Quiz/Test Details
                    </button>
                </div>
                <div id="quizSection" style="display:none;" class="border rounded p-3 mb-3 bg-light">
                    <h6 class="text-info mb-3"><i class="bi bi-clipboard-check me-2"></i>Quiz/Test Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quiz_link" class="form-label">Quiz/Test Link</label>
                                <input type="url" class="form-control" id="quiz_link" name="quiz_link" placeholder="https://...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_marks" class="form-label">Total Marks</label>
                                <input type="number" class="form-control" id="total_marks" name="total_marks" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="marking_guide" class="form-label">Marking Guide</label>
                        <input type="text" class="form-control" id="marking_guide" name="marking_guide" placeholder="Describe marking criteria">
                    </div>
                </div>

                <!-- Challenges Section -->
                <div class="mb-3">
                    <button type="button" class="btn btn-secondary" id="showChallengesSection">
                        <i class="bi bi-exclamation-triangle me-2"></i>Add Challenges Faced
                    </button>
                </div>
                <div id="challengesSection" style="display:none;" class="border rounded p-3 mb-3 bg-light">
                    <div class="mb-3">
                        <label for="challenges" class="form-label">
                            <i class="bi bi-flag me-1"></i>Challenges Encountered
                        </label>
                        <textarea class="form-control" id="challenges" name="challenges" rows="3" placeholder="Describe any challenges faced during the lesson..."></textarea>
                    </div>
                </div>

                <!-- Comment Section -->
                <div class="mb-3" style="display:block;">
                    <label for="comment" class="form-label">
                        Comments <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="comment" name="comment" rows="4" required placeholder="Add your comments about the lesson, student engagement, etc..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Submit Activity
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff;">
        <i class="bi bi-megaphone fs-2 me-2 text-warning"></i>
        <div>
          <h5 class="modal-title mb-0" id="announcementModalLabel"></h5>
          <div id="announcementDate" class="small text-light opacity-75"></div>
          <div id="announcementSender" class="small text-light opacity-75"></div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4 px-4">
        <div id="announcementContent" class="mb-3" style="font-size:1.1rem; color:#1e293b;"></div>
        <div id="announcementAttachment" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Enhanced JavaScript -->
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select courses',
        allowClear: true
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Modal section toggles with smooth animations
    const toggleSection = (buttonId, sectionId, required = false) => {
        document.getElementById(buttonId).onclick = function() {
            const section = document.getElementById(sectionId);
            section.style.display = 'block';
            section.style.opacity = '0';
            section.style.transform = 'translateY(-10px)';
            
            requestAnimationFrame(() => {
                section.style.transition = 'all 0.3s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            });
            
            if (required) {
                const textarea = section.querySelector('textarea');
                if (textarea) textarea.required = true;
            }
            
            this.style.display = 'none';
        };
    };

    toggleSection('showQuizSection', 'quizSection');
    toggleSection('showChallengesSection', 'challengesSection');
    // Comment section is now always visible, no toggle needed

    // Dynamic selects with loading states
    const branchSelect = document.getElementById('branch');
    const lessonSelect = document.getElementById('lesson');
    const unitSelect = document.getElementById('unit');
    let courseStudentCount = 0;

    const setLoading = (element, isLoading) => {
        if (isLoading) {
            element.innerHTML = '<option value="">Loading...</option>';
            element.disabled = true;
        } else {
            element.disabled = false;
        }
    };

    // When branch changes, load courses for that branch
    branchSelect.addEventListener('change', function() {
        const branchId = this.value;
        const coursesSelect = document.getElementById('courses');
        
        // Disable courses until branch is selected
        if (!branchId) {
            coursesSelect.disabled = true;
            $('#courses').empty().append('<option value="">Select branch first</option>');
            lessonSelect.innerHTML = '<option value="">Select lesson</option>';
            unitSelect.innerHTML = '<option value="">Select unit</option>';
            
            // Remove attendance section if present
            const attendanceDiv = document.getElementById('attendanceSection');
            if (attendanceDiv) attendanceDiv.remove();
            return;
        }
        
        // Load courses for the selected branch
        setLoading(coursesSelect, true);
        $('#courses').empty().append('<option value="">Loading courses...</option>');
        
        fetch(`/api/teacher/branch/${branchId}/courses`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Failed to load courses');
                }
                return res.json();
            })
            .then(data => {
                $('#courses').empty().append('<option value="">Select courses</option>');
                if (data && data.length > 0) {
                    data.forEach(course => {
                        $('#courses').append(new Option(course.course_name, course.id, false, false));
                    });
                } else {
                    $('#courses').append('<option value="">No courses available for this branch</option>');
                }
                coursesSelect.disabled = false;
                setLoading(coursesSelect, false);
            })
            .catch(err => {
                console.error('Error fetching courses:', err);
                $('#courses').empty().append('<option value="">Error loading courses. Please check your connection.</option>');
                coursesSelect.disabled = false;
                setLoading(coursesSelect, false);
            });
        
        lessonSelect.innerHTML = '<option value="">Select lesson</option>';
        unitSelect.innerHTML = '<option value="">Select unit</option>';
        
        // Remove attendance section if present
        const attendanceDiv = document.getElementById('attendanceSection');
        if (attendanceDiv) attendanceDiv.remove();
    });

    // When courses change, load lessons (filtered by branch+courses+term) and students (branch+courses)
    $('#courses').on('change', function() {
        const selectedCourses = $(this).val();
        const branchId = branchSelect.value;
        const termId = document.getElementById('daily_term') ? document.getElementById('daily_term').value : '';
        setLoading(lessonSelect, true);
        unitSelect.innerHTML = '<option value="">Select unit</option>';

        // Remove attendance section if present
        const attendanceDiv = document.getElementById('attendanceSection');
        if (attendanceDiv) attendanceDiv.remove();

        if (selectedCourses && selectedCourses.length > 0 && branchId) {
            // Fetch lessons assigned to this teacher for branch+courses+term
            // Use the new multi-course endpoint
            let lessonsUrl = `/api/teacher/branch/${branchId}/courses/lessons`;
            const params = new URLSearchParams();
            selectedCourses.forEach(id => params.append('course_ids[]', id));
            if (termId) params.append('term_id', termId);
            lessonsUrl += `?${params.toString()}`;

            fetch(lessonsUrl)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Failed to load lessons');
                    }
                    return res.json();
                })
                .then(data => {
                    let options = '<option value="">Select lesson</option>';
                    if (data && data.length > 0) {
                        data.forEach(lesson => {
                            options += `<option value="${lesson.id}">${lesson.title}</option>`;
                        });
                    } else {
                        options += '<option value="">No lessons available for all selected courses</option>';
                    }
                    lessonSelect.innerHTML = options;
                    setLoading(lessonSelect, false);
                })
                .catch(err => {
                    console.error('Error fetching lessons:', err);
                    lessonSelect.innerHTML = '<option value="">Error loading lessons. Please check your connection.</option>';
                    setLoading(lessonSelect, false);
                });

            // Load students enrolled in selected branch + courses for the academic year
            let studentsUrl = `/api/teacher/branch/${branchId}/courses/students`;
            const studentParams = new URLSearchParams();
            selectedCourses.forEach(id => studentParams.append('course_ids[]', id));
            studentsUrl += `?${studentParams.toString()}`;

            fetch(studentsUrl)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Failed to load students');
                    }
                    return res.json();
                })
                .then(data => {
                    const students = Array.isArray(data) ? data : (data.students || []);
                    const allStudents = students.map(student => ({
                        id: student.id,
                        name: student.name || student.full_name,
                        reg_number: student.reg_number,
                    }));
                    showAttendanceList(allStudents);
                })
                .catch(err => {
                    console.error('Error fetching students:', err);
                });
        } else {
            lessonSelect.innerHTML = '<option value="">Select lesson</option>';
            setLoading(lessonSelect, false);
        }
    });

    // When term changes, reload lessons for selected courses
    const dailyTermSelect = document.getElementById('daily_term');
    if (dailyTermSelect) {
        dailyTermSelect.addEventListener('change', function() {
            $('#courses').trigger('change');
        });
    }

    function showAttendanceList(students) {
        courseStudentCount = students.length;

        // Sort students by name (A-Z by default)
        students.sort(function(a, b) {
            return a.name.localeCompare(b.name);
        });

        // Always show attendance table with all students
        let html = `<div id="attendanceSection" class="border rounded p-3 mt-3 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-primary mb-0">
                    <i class="bi bi-people me-2"></i>Attendance List (${students.length} students)
                </h6>
                <button type="button" id="sort-attendance-btn" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-sort-alpha-down me-1"></i>Sort: A-Z
                </button>
            </div>
            <p class="small text-muted mb-3">
                Mark attendance for each student:
            </p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>P</th>
                            <th>A</th>
                            <th>AR</th>
                            <th>L</th>
                        </tr>
                    </thead>
                    <tbody>`;
        students.forEach(student => {
            html += `<tr class="attendance-row">
                <td class="student-name">${student.name}</td>
                <td><input type="radio" name="attendance[${student.id}]" value="P" checked></td>
                <td><input type="radio" name="attendance[${student.id}]" value="A"></td>
                <td><input type="radio" name="attendance[${student.id}]" value="AR"></td>
                <td><input type="radio" name="attendance[${student.id}]" value="L"></td>
            </tr>`;
        });
        html += `</tbody></table></div></div>`;

        const oldSection = document.getElementById('attendanceSection');
        if (oldSection) oldSection.remove();

        // Insert after the total_students field's parent div
        const totalStudentsDiv = document.getElementById('total_students').closest('.col-md-6').parentNode;
        totalStudentsDiv.insertAdjacentHTML('afterend', html);

        // Auto-fill total students count
        document.getElementById('total_students').value = students.length;

        // Add sort button functionality
        const sortBtn = document.getElementById('sort-attendance-btn');
        if (sortBtn) {
            let isAscending = true;
            sortBtn.addEventListener('click', function() {
                const rows = Array.from(document.querySelectorAll('.attendance-row'));
                const tbody = document.querySelector('#attendanceTable tbody');

                rows.sort(function(a, b) {
                    const nameA = a.querySelector('.student-name').textContent.trim();
                    const nameB = b.querySelector('.student-name').textContent.trim();

                    if (isAscending) {
                        return nameA.localeCompare(nameB);
                    } else {
                        return nameB.localeCompare(nameA);
                    }
                });

                // Re-append rows in sorted order
                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });

                // Update button state
                isAscending = !isAscending;
                if (isAscending) {
                    sortBtn.innerHTML = '<i class="bi bi-sort-alpha-down me-1"></i>Sort: A-Z';
                } else {
                    sortBtn.innerHTML = '<i class="bi bi-sort-alpha-up me-1"></i>Sort: Z-A';
                }
            });
        }
    }

    // Student attendance validation - just validates the count
    document.getElementById('total_students').addEventListener('blur', function() {
        const entered = parseInt(this.value, 10);
        
        if (entered > courseStudentCount) {
            alert('Number of present students cannot exceed total assigned students!');
            this.value = courseStudentCount;
            this.focus();
        }
    });

    lessonSelect.addEventListener('change', function() {
        const lessonId = this.value;
        setLoading(unitSelect, true);

        if (lessonId) {
            fetch(`/teacher/lesson/${lessonId}/units`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Failed to load units');
                    }
                    return res.json();
                })
                .then(data => {
                    let options = '<option value="">Select unit</option>';
                    if (data && data.length > 0) {
                        data.forEach(unit => {
                            options += `<option value="${unit.id}">${unit.unit_name}</option>`;
                        });
                    } else {
                        options += '<option value="">No units available</option>';
                    }
                    unitSelect.innerHTML = options;
                    setLoading(unitSelect, false);
                })
                .catch(err => {
                    console.error('Error fetching units:', err);
                    unitSelect.innerHTML = '<option value="">Error loading units. Please check your connection.</option>';
                    setLoading(unitSelect, false);
                });
        } else {
            unitSelect.innerHTML = '<option value="">Select unit</option>';
            setLoading(unitSelect, false);
        }
    });

    // Student attendance validation - just validates the count
    document.getElementById('total_students').addEventListener('blur', function() {
        const entered = parseInt(this.value, 10);
        
        if (entered > courseStudentCount) {
            alert('Number of present students cannot exceed total assigned students!');
            this.value = courseStudentCount;
            this.focus();
        }
    });

    // Dynamic Activities Management
    const activitiesList = document.getElementById('activitiesList');
    const addActivityBtn = document.getElementById('addActivityBtn');

    addActivityBtn.addEventListener('click', function() {
        const group = document.createElement('div');
        group.className = 'input-group mb-2';
        group.innerHTML = `
            <input type="text" class="form-control" name="activities[]" required placeholder="Enter activity description">
            <button type="button" class="btn btn-outline-danger remove-activity">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        // Add with animation
        group.style.opacity = '0';
        group.style.transform = 'translateX(-10px)';
        activitiesList.appendChild(group);
        
        requestAnimationFrame(() => {
            group.style.transition = 'all 0.3s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateX(0)';
        });
        
        // Update remove button visibility
        updateRemoveButtons();
    });

    activitiesList.addEventListener('click', function(e) {
        if (e.target.closest('.remove-activity')) {
            const group = e.target.closest('.input-group');
            group.style.transition = 'all 0.3s ease';
            group.style.opacity = '0';
            group.style.transform = 'translateX(-10px)';
            setTimeout(() => {
                group.remove();
                updateRemoveButtons();
            }, 300);
        }
    });

    function updateRemoveButtons() {
        const removeButtons = activitiesList.querySelectorAll('.remove-activity');
        removeButtons.forEach((btn, index) => {
            // Show remove button only if there are multiple activities
            btn.style.display = removeButtons.length > 1 ? 'block' : 'none';
        });
    }

    // Form validation enhancements
    const form = document.querySelector('#classDailyModal form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });

    // Real-time field validation
    form.addEventListener('input', function(e) {
        if (e.target.hasAttribute('required') && e.target.value.trim()) {
            e.target.classList.remove('is-invalid');
        }
    });

    // Auto-save draft functionality (optional)
    const draftKey = 'class_daily_draft';
    
    // Load draft on modal open
    document.getElementById('classDailyModal').addEventListener('shown.bs.modal', function() {
        const draft = localStorage.getItem(draftKey);
        if (draft) {
            try {
                const data = JSON.parse(draft);
                // Restore form data
                Object.keys(data).forEach(key => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field && field.type !== 'file') {
                        field.value = data[key];
                    }
                });
            } catch (e) {
                console.error('Error loading draft:', e);
            }
        }
    });

    // Save draft on input
    form.addEventListener('input', debounce(function() {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        localStorage.setItem(draftKey, JSON.stringify(data));
    }, 1000));

    // Clear draft on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem(draftKey);
    });

    // Utility function for debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        }
    }

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize on page load
    updateRemoveButtons();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Shift + D to open Class Daily modal
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('classDailyModal'));
        modal.show();
    }
});

// Add smooth scrolling to all internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        // Skip if href is just "#" or empty
        if (!href || href === '#') {
            return;
        }
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});




// Show notifications for form submissions
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        showNotification('{{ session("success") }}', 'success');
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        showNotification('{{ session("error") }}', 'error');
    });
@endif

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Announcement modal script
document.addEventListener('DOMContentLoaded', function() {
    function b64DecodeUnicode(str) {
        try {
            return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
        } catch (e) {
            try { return atob(str); } catch (_) { return str; }
        }
    }

    document.querySelectorAll('.announcement-link').forEach(link => {
        link.addEventListener('click', function() {
            document.getElementById('announcementModalLabel').textContent = this.dataset.title;
            document.getElementById('announcementDate').textContent = "Date: " + this.dataset.date;
            document.getElementById('announcementSender').textContent = "Sender: " + this.dataset.sender;
            const html = this.dataset.content ? b64DecodeUnicode(this.dataset.content) : '';
            document.getElementById('announcementContent').innerHTML = html;
            if (this.dataset.attachment) {
                document.getElementById('announcementAttachment').innerHTML =
                    `<a href="${this.dataset.attachment}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-paperclip"></i> View Attachment
                    </a>`;
            } else {
                document.getElementById('announcementAttachment').innerHTML = '';
            }
        });
    });
});

// Initialize MathJax for mathematical expressions
document.addEventListener('DOMContentLoaded', function() {
    if (window.MathJax) {
        MathJax.startup.promise.then(() => {
            MathJax.typesetPromise();
        });
    }
});

// Re-render MathJax when content changes (for dynamic content)
function reRenderMathJax() {
    if (window.MathJax && MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}
</script>
@yield('scripts')

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
<!-- LiveChat script removed due to expired license -->

<!-- ESA Live Support Chat Widget -->
@auth('teacher')
<x-chat-widget />
@endauth

@vite('resources/js/realtime.js')
</body>
</html>
