@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo();
    $user = Auth::guard('staff')->user();
    
    if (!$user) {
        echo "<script>alert('Login first please'); window.location.href = '" . route('staff.login') . "';</script>";
        exit;
    }

    $photoUrl = $user->photo 
        ? (filter_var($user->photo, FILTER_VALIDATE_URL) ? $user->photo : asset('storage/' . $user->photo))
        : asset('images/default-user.png');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $systemName }} - @yield('title', 'Accountant Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
            --primary-green: #2c3e50;
            --secondary-green: #27ae60;
            --accent-green: #3498db;
            --success-green: #27ae60;
            --warning-orange: #f39c12;
            --danger-red: #e74c3c;
            --text-light: #ecf0f1;
            --text-muted: #bdc3c7;
            --shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f4f7fa;
            color: #2c3e50;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 50%, rgba(0,0,0,0.1) 100%);
            pointer-events: none;
        }

        /* Sidebar Header */
        .sidebar-header {
            position: relative;
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
        }

        .sidebar-logo {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .sidebar-logo-img {
            max-height: 45px;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-logo {
            font-size: 0;
            opacity: 0;
        }

        .sidebar.collapsed .sidebar-logo-img {
            max-height: 35px;
            margin-bottom: 0;
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            position: relative;
            padding: 1rem 0;
            height: calc(100vh - 120px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        /* Navigation Items */
        .nav-item {
            margin: 0.25rem 0.75rem;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: white;
            border-radius: 2px 0 0 2px;
        }

        .nav-icon {
            font-size: 1.25rem;
            min-width: 24px;
            margin-right: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-text {
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.875rem 0;
        }

        /* Logout Button */
        .nav-logout {
            margin-top: auto;
            margin-bottom: 1rem;
        }

        .nav-logout .nav-link {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .nav-logout .nav-link:hover {
            background: rgba(231, 76, 60, 0.2);
            border-color: rgba(231, 76, 60, 0.5);
            transform: translateX(4px);
        }

        /* Toggle Button */
        .sidebar-toggle {
            position: absolute;
            top: 20px;
            right: -15px;
            width: 30px;
            height: 30px;
            background: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1051;
            color: var(--primary-green);
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .sidebar-toggle i {
            font-size: 0.875rem;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            flex-direction: column;
        }

        .main-content.collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        /* Top Navigation Bar */
        .topbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }

        .topbar-title {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .time-display {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 1.1rem;
            color: #6b7280;
            font-weight: 600;
            background: linear-gradient(45deg, var(--primary-green), var(--secondary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .user-btn:hover {
            background: rgba(0,0,0,0.05);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid transparent;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            padding: 2px;
            transition: all 0.3s ease;
        }

        .user-btn:hover .user-avatar {
            transform: scale(1.05);
        }

        .user-name {
            font-weight: 600;
            color: var(--primary-green);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            z-index: 1100;
        }

        .user-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--primary-green);
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
            color: var(--secondary-green);
        }

        /* Content Area */
        .content-wrapper {
            padding: 2rem;
            flex: 1;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px 20px;
        }

        /* Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-green) 0%, #2980b9 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        /* Table Styling */
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            border: none;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: none;
            }

            .mobile-toggle {
                display: block !important;
            }

            .topbar {
                padding: 1rem;
            }

            .topbar-title {
                font-size: 1.5rem;
            }

            body.sidebar-open {
                overflow: hidden;
            }
        }

        .mobile-toggle {
            display: none;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        .mobile-toggle:hover {
            transform: scale(1.05);
        }

        /* Sidebar Overlay */
        #sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            display: none;
        }

        #sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
            display: block;
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

<!-- Sidebar overlay for small screens -->
<div id="sidebar-overlay" onclick="toggleSidebar(false)"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <!-- Toggle Button -->
        <button class="sidebar-toggle" onclick="toggleCollapse()">
            <i class="fas fa-chevron-left"></i>
        </button>

        <!-- Sidebar Header -->
        <div class="sidebar-header">
            @if($systemLogo)
                <img src="{{ asset('storage/' . $systemLogo) }}" alt="Logo" class="sidebar-logo-img">
            @endif
            <h4 class="sidebar-logo">
                {{ $systemName }}
            </h4>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-nav">
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <span class="nav-text">Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.students.index') }}" class="nav-link {{ request()->routeIs('staff.students.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <span class="nav-text">Students</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.course_fees') }}" class="nav-link {{ request()->routeIs('staff.course_fees') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <span class="nav-text">Course Fees</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.lessons.index') }}" class="nav-link {{ request()->routeIs('staff.lessons.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <span class="nav-text">Lessons</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.subscriptions.index') }}" class="nav-link {{ request()->routeIs('staff.subscriptions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <span class="nav-text">Subscriptions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('to-whom.index') }}" class="nav-link {{ request()->routeIs('to-whom.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <span class="nav-text">Professional Letters</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('babyeyi.index') }}" class="nav-link {{ request()->routeIs('babyeyi.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <span class="nav-text">Babyeyi Documents</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accountingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="nav-icon fas fa-calculator"></i>
                        <span class="nav-text">Accounting</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="accountingDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('accounting.cash-book') ? 'active' : '' }}" href="{{ route('accounting.cash-book') }}">
                                <i class="fas fa-money-check-alt"></i> Cash Book
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('accounting.balance-sheet') ? 'active' : '' }}" href="{{ route('accounting.balance-sheet') }}">
                                <i class="fas fa-balance-scale"></i> Balance Sheet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('accounting.income-statement') ? 'active' : '' }}" href="{{ route('accounting.income-statement') }}">
                                <i class="fas fa-chart-line"></i> Income Statement
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('accounting.trial-balance') ? 'active' : '' }}" href="{{ route('accounting.trial-balance') }}">
                                <i class="fas fa-list-ol"></i> Trial Balance
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('accounting.general-ledger') ? 'active' : '' }}" href="{{ route('accounting.general-ledger') }}">
                                <i class="fas fa-book-open"></i> General Ledger
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item nav-logout">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <span class="nav-text">Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <form id="logout-form" action="{{ route('staff.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>

    <!-- Main content -->
    <div class="main-content" id="main-content">
        <!-- Top navbar -->
        <nav class="topbar">
            <div class="d-flex align-items-center gap-3">
                <!-- Sidebar toggle button for small screens -->
                <button class="mobile-toggle" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-3"></i>
                </button>
                <div class="topbar-title">
                    Accountant Dashboard
                </div>
            </div>

            <div class="time-display" id="kigali-time">
                Loading time...
            </div>

            <!-- User dropdown -->
            <div class="user-dropdown position-relative">
                <button class="user-btn" onclick="toggleUserDropdown()">
                    <img src="{{ $photoUrl }}"
                         alt="{{ $user->name }}'s photo"
                         class="user-avatar">

                    <span class="user-name">{{ $user->name }}</span>
                    <i class="fas fa-caret-down"></i>
                </button>

                <div class="user-dropdown-menu" id="userDropdown">
                    <a href="{{ route('staff.profile') }}" class="dropdown-item">Profile</a>
                    <form method="POST" action="{{ route('staff.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="content-wrapper">
            <!-- Laravel Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Laravel Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Validation Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script>
    let sidebarCollapsed = false;
    let userDropdownOpen = false;

    // Toggle sidebar collapse
    function toggleCollapse() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        sidebarCollapsed = !sidebarCollapsed;
        
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('collapsed');
        }
    }

    // Toggle sidebar for mobile
    function toggleSidebar(force) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const body = document.body;

        const isOpen = sidebar.classList.contains('show');
        const shouldShow = force === undefined ? !isOpen : force;

        if (shouldShow) {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            body.classList.add('sidebar-open');
        } else {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            body.classList.remove('sidebar-open');
        }
    }

    // Toggle user dropdown
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        userDropdownOpen = !userDropdownOpen;
        
        if (userDropdownOpen) {
            dropdown.classList.add('show');
        } else {
            dropdown.classList.remove('show');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const userDropdown = document.querySelector('.user-dropdown');
        const dropdown = document.getElementById('userDropdown');
        
        if (!userDropdown.contains(event.target)) {
            dropdown.classList.remove('show');
            userDropdownOpen = false;
        }
    });

    // Kigali time update
    function updateTime() {
        const options = { timeZone: 'Africa/Kigali', hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const now = new Date().toLocaleTimeString('en-US', options);
        document.getElementById('kigali-time').textContent = now;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991.98) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }
    });

    // Add loading animations
    window.addEventListener('load', function() {
        document.querySelectorAll('.nav-item').forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
            item.classList.add('slide-in');
        });
    });
</script>

<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
