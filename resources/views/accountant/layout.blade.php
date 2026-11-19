@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
            --primary-blue: #10233f;
            --secondary-blue: #18345b;
            --accent-blue: #3b82f6;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --danger-red: #ef4444;
            --text-light: #ffffff;
            --text-muted: #94a3b8;
            --text-dark: #1e293b;
            --bg-light: #f4f7fe;
            --bg-white: #ffffff;
            --shadow: 0 2px 15px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.12);
            --radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-blue) 0%, #0b1930 100%);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
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

        /* Navigation Section Title */
        .nav-section-title {
            padding: 0.75rem 1.5rem 0.25rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.5);
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
            transform: translateX(-20px);
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
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: white;
            border-radius: 0 2px 2px 0;
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
            color: var(--primary-blue);
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
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .time-display {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 1.1rem;
            color: var(--accent-blue);
            font-weight: 600;
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
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            padding: 2px;
            transition: all 0.3s ease;
        }

        .user-btn:hover .user-avatar {
            transform: scale(1.05);
        }

        .user-name {
            font-weight: 600;
            color: var(--primary-blue);
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
            color: var(--primary-blue);
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
            color: var(--accent-blue);
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
            background: #ffffff;
            color: var(--text-dark);
            border-bottom: 1px solid #e8edf4;
            font-weight: 600;
            padding: 15px 20px;
        }

        /* Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--secondary-blue) 100%);
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
            background: linear-gradient(135deg, var(--success-green) 0%, #047857 100%);
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
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
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
        @media (max-width: 768px) {
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

            .btn, .nav-link, button {
                min-height: 44px;
            }

            .form-control, input, select, textarea {
                font-size: 16px;
                min-height: 44px;
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
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
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

            /* Form controls */
            .form-control,
            .form-select {
                font-size: 16px; /* Prevent iOS zoom */
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

            /* Sidebar adjustments */
            .sidebar {
                position: fixed;
                z-index: 1040;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.show {
                transform: translateX(0);
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
                <!-- FINANCE Section -->
                <li class="nav-section-title">FINANCE</li>
                
                <li class="nav-item">
                    <a href="{{ route('staff.accountant.dashboard') }}" class="nav-link {{ request()->routeIs('staff.accountant.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.accountant.payment-schedule.index') }}" class="nav-link {{ request()->routeIs('staff.accountant.payment-schedule.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <span class="nav-text">School Fees</span>
                    </a>
                </li>

                <li class="nav-item">
                    <button type="button" class="nav-link w-100 text-start" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <span class="nav-text">Add Expense</span>
                    </button>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="nav-link {{ request()->routeIs('staff.accountant.expenses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <span class="nav-text">Expenses</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.accountant.receipts.index') }}" class="nav-link {{ request()->routeIs('staff.accountant.receipts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <span class="nav-text">Receipts</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('staff.accounting.cashbook') ? 'active' : '' }}" href="{{ route('staff.accounting.cashbook') }}">
                                <i class="fas fa-money-check-alt"></i> Cash Book
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('staff.accounting.balance-sheet') ? 'active' : '' }}" href="{{ route('staff.accounting.balance-sheet') }}">
                                <i class="fas fa-balance-scale"></i> Balance Sheet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('staff.accounting.income-statement') ? 'active' : '' }}" href="{{ route('staff.accounting.income-statement') }}">
                                <i class="fas fa-chart-line"></i> Income Statement
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('staff.accounting.trial-balance') ? 'active' : '' }}" href="{{ route('staff.accounting.trial-balance') }}">
                                <i class="fas fa-list-ol"></i> Trial Balance
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- OTHER Section -->
                <li class="nav-section-title">OTHER</li>

                <li class="nav-item">
                    <a href="{{ route('staff.students.index') }}" class="nav-link {{ request()->routeIs('staff.students.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <span class="nav-text">Students</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <span class="nav-text">Profile</span>
                    </a>
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
                <div class="topbar-title">@yield('title', 'Accountant Dashboard')</div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Academic Year Selector -->
                @include('partials.academic-year-switcher', [
                    'switcherClass' => 'me-2',
                    'labelClass' => 'text-muted small me-2',
                    'selectClass' => 'form-select form-select-sm',
                ])

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
                        <a href="{{ route('staff.profile') }}" class="dropdown-item">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('staff.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </div>
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

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('expenses.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" name="expense_date" id="expense_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="expense_type" class="form-label">Type</label>
            <input type="text" name="expense_type" id="expense_type" class="form-control" required placeholder="e.g. Stationery, Utilities">
          </div>
          <div class="mb-3">
            <label for="amount" class="form-label">Amount (RWF)</label>
            <input type="number" name="amount" id="amount" class="form-control" min="1" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label for="paid_to" class="form-label">Paid To</label>
            <input type="text" name="paid_to" id="paid_to" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="paid_to_contact" class="form-label">Contact Number of Paid To</label>
            <input type="text" name="paid_to_contact" id="paid_to_contact" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-select" required>
              <option value="" disabled selected>Select method</option>
              <option value="cash">Cash</option>
              <option value="bank">Bank</option>
              <option value="mobile_money">Mobile Money</option>
              <option value="cheque">Cheque</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="term_id" class="form-label">Academic Term</label>
            <select name="term_id" id="term_id" class="form-select">
              <option value="">Select Term (Optional)</option>
              @php
                  $terms = \App\Models\Term::with('academicYear')
                      ->orderBy('start_date', 'desc')
                      ->get();
              @endphp
              @foreach($terms as $term)
                  <option value="{{ $term->id }}">
                      {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                  </option>
              @endforeach
            </select>
            <small class="form-text text-muted">Select the academic term for this expense (optional)</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Save Expense</button>
        </div>
      </div>
    </form>
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
        if (window.innerWidth > 768) {
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

</body>
</html>
