@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> {{ $systemName }}-@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
        {{-- In your main layout, before your custom JS --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Add to your layout head section -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- KaTeX for Mathematical Expressions (loaded synchronously so it's available on DOMContentLoaded) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js" crossorigin="anonymous"></script>
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
            --primary-blue: #2c3e50;
            --secondary-blue: #34495e;
            --accent-blue: #3498db;
            --hover-blue: #4a6741;
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            overflow-x: hidden;
        }

        /* Top Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            color: white;
        }

        /* Enhanced collapse behavior */
        .sidebar.collapsed ~ .top-navbar {
            left: var(--sidebar-collapsed) !important;
        }

        /* Ensure navbar adjusts when sidebar is collapsed */
        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed) !important;
        }

        /* Debug: Add visual indicator when sidebar is collapsed */
        .sidebar.collapsed ~ .top-navbar::before {
            content: "◀";
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .current-time {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            background: none;
            border: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .profile-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        .profile-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 200px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: none;
            overflow: hidden;
            margin-top: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown-menu-custom.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item-custom {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
            text-decoration: none;
            transition: background 0.3s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item-custom:hover {
            background: #f8f9fa;
            color: var(--accent-blue);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            box-shadow: var(--shadow);
            z-index: 1001;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }

        .sidebar-logo {
            color: var(--text-light);
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .sidebar-logo {
            opacity: 0;
        }

        .sidebar-menu {
            padding: 1rem 0;
            height: calc(100vh - 140px);
            overflow-y: auto;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        /* Professional Collapsible Sidebar */
        .nav-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            white-space: nowrap;
            opacity: 1;
            transition: all 0.3s ease;
            cursor: pointer;
            user-select: none;
            border-radius: 8px;
            margin: 0.25rem 0.8rem;
        }

        .nav-header:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-light);
        }

        .nav-header i {
            font-size: 0.75rem;
            transition: transform 0.3s ease;
        }

        .rotate-90 {
            transform: rotate(90deg);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Enhanced transitions */
        .transition-transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }

        .duration-200 {
            transition-duration: 200ms;
        }

        /* Professional section styling */
        .nav-section {
            margin-bottom: 0.5rem;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Enhanced active states */
        .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
            border-left: 3px solid #3498db;
            font-weight: 600;
        }

        /* Smooth accordion animations */
        [x-show] {
            transition: all 0.3s ease;
        }

        [x-transition\:enter] {
            transition: all 0.3s ease;
        }

        [x-transition\:leave] {
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-header {
            opacity: 0;
        }

        .nav-item {
            margin: 0.3rem 0.8rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.9rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-blue);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: var(--text-light);
            transform: translateX(8px);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 1rem;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .nav-text {
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }

        .logout-form {
            width: 100%;
        }

        .logout-btn {
            width: 100%;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border: none;
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .top-navbar {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .navbar-right {
                gap: 1rem;
            }

            .current-time {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .top-navbar {
                padding: 0 1rem;
            }

            .navbar-left h5 {
                display: none;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Reactbits Animation Classes */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .nav-item {
            animation: slideIn 0.5s ease forwards;
        }

        .nav-item:nth-child(1) { animation-delay: 0.1s; }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-item {
            animation: slideIn 0.5s ease forwards;
        }

        .nav-item:nth-child(1) { animation-delay: 0.1s; }
        .nav-item:nth-child(2) { animation-delay: 0.2s; }
        .nav-item:nth-child(3) { animation-delay: 0.3s; }
        .nav-item:nth-child(4) { animation-delay: 0.4s; }
        .nav-item:nth-child(5) { animation-delay: 0.5s; }
        .nav-item:nth-child(6) { animation-delay: 0.6s; }
        .nav-item:nth-child(7) { animation-delay: 0.7s; }
        .nav-item:nth-child(8) { animation-delay: 0.8s; }

        /* SweetAlert2 Custom Styling */
        .swal2-input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .swal2-input:focus {
            border-color: #4e73df;
            outline: none;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.2);
        }
        
        .swal2-textarea {
            min-height: 80px;
            resize: vertical;
        }

        @media (max-width: 600px) {
            .wa-chat-modal {
                width: 100vw;
                height: 100vh;
                right: 0;
                bottom: 0;
                border-radius: 0;
            }
            .wa-chat-sidebar {
                width: 90px;
            }
        }

        /* Mobile Responsive Admin Layout */
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed) !important;
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
                width: var(--sidebar-width) !important;
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0 !important;
                padding: 1rem !important;
            }
            
            .top-navbar {
                padding: 0.75rem 1rem !important;
            }
            
            .navbar-brand {
                font-size: 1rem !important;
            }
            
            .user-profile {
                padding: 0.5rem !important;
            }
            
            .user-name {
                display: none !important;
            }
            
            .mobile-menu-toggle {
                display: block !important;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1002;
                background: var(--primary-blue);
                color: white;
                border: none;
                padding: 0.75rem;
                border-radius: 0.5rem;
                cursor: pointer;
                box-shadow: var(--shadow);
            }
            
            .mobile-overlay {
                display: none !important;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
            }
            
            .mobile-overlay.active {
                display: block !important;
            }
            
            /* Mobile sidebar items */
            .sidebar-item {
                padding: 0.75rem 1rem !important;
            }
            
            .sidebar-text {
                display: none !important;
            }
            
            .sidebar.mobile-open .sidebar-text {
                display: block !important;
            }
            
            .sidebar.mobile-open .sidebar-item {
                padding: 1rem 1.5rem !important;
            }
            
            /* Mobile dropdown */
            .dropdown-menu-custom {
                width: 250px !important;
                right: 1rem !important;
            }
            
            /* Mobile cards */
            .card {
                margin: 0 0 1rem 0 !important;
                border-radius: 0.5rem !important;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            /* Mobile tables */
            .table-responsive {
                margin: 0 -1rem 1rem -1rem !important;
                padding: 0 1rem !important;
            }
            
            .table {
                font-size: 0.875rem !important;
            }
            
            .table th,
            .table td {
                padding: 0.5rem !important;
            }
            
            /* Mobile forms */
            .form-control {
                font-size: 16px !important; /* Prevents zoom on iOS */
                min-height: 44px !important;
            }
            
            .btn {
                min-height: 44px !important;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        @media (max-width: 640px) {
            .sidebar.mobile-open {
                width: 100% !important;
            }
            
            .main-content {
                padding: 0.75rem !important;
            }
            
            .top-navbar {
                padding: 0.5rem 0.75rem !important;
            }
            
            .navbar-brand {
                font-size: 0.875rem !important;
            }
            
            .card-body {
                padding: 0.75rem !important;
            }
            
            .table {
                font-size: 0.8125rem !important;
            }
        }

        /* Page Header Styling */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: -2rem -2rem 2rem 0;
            padding: 2rem;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .page-title {
            color: white;
            font-weight: 600;
            margin: 0;
            font-size: 1.75rem;
        }

        .breadcrumb {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin: 0;
        }

        .breadcrumb-item {
            color: rgba(255,255,255,0.8);
        }

        .breadcrumb-item a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb-item a:hover {
            color: rgba(255,255,255,0.8);
        }

        .breadcrumb-item.active {
            color: rgba(255,255,255,0.9);
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.6);
            content: ">";
        }

        /* Mobile Responsive Page Header */
        @media (max-width: 768px) {
            .page-header {
                margin: -1rem -1rem 1rem 0;
                padding: 1.5rem 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .breadcrumb {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
    @yield('styles')
    @livewireStyles
</head>
<body>
 @include('partials.storage-warning')
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Sidebar Overlay for Desktop -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Top Navbar -->
    <div class="top-navbar">
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
        <div class="navbar-left">
            <button class="menu-toggle d-none d-md-block" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0">Dashboard</h5>
        </div>
        
        <div class="navbar-right">
            <div class="current-time">
                <i class="fas fa-clock"></i>
                <span id="currentTime">--:--:--</span>
            </div>
            
            <div class="profile-dropdown">
                <button class="profile-btn" id="profileBtn">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>Admin</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <div class="dropdown-menu-custom" id="profileDropdown">
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item-custom">
                        <i class="fas fa-user"></i>
                        Profile Settings
                    </a>
                    <a href="{{ route('admin.preferences') }}" class="dropdown-item-custom">
                        <i class="fas fa-cog"></i>
                        Preferences
                    </a>
                    <div style="border-top: 1px solid #eee; margin: 8px 0;"></div>
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item-custom">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            @php
                $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
                $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
            @endphp

            @if($systemLogo)
                <img src="{{ asset('storage/' . $systemLogo) }}" alt="Logo" style="max-height:40px;">
            @endif
            <h4 class="sidebar-logo">
                {{ $systemName }}
            </h4>
        </div>

        <div class="sidebar-menu">
            <div class="nav-section">
                <div class="nav-header">Main</div>
                
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <i class="fas fa-blog"></i>
                        <span class="nav-text">Blog Management</span>
                    </a>
                </div>
            </div>

            <!-- Academic Management -->
            <div class="nav-section" x-data="{ isOpen: true }">
                <div class="nav-header cursor-pointer" @click="isOpen = !isOpen">
                    <i class="fas fa-chevron-right transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''"></i>
                    Academic Management
                </div>
                
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-200">
                    <div class="nav-item">
                        <a href="{{ route('admin.course.index') }}" class="nav-link {{ request()->routeIs('admin.course.*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span class="nav-text">Courses</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.terms.index') }}" class="nav-link {{ request()->routeIs('admin.terms.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">Terms</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.exams.index') }}" class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span class="nav-text">Exams</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.question-banks.index') }}" class="nav-link {{ request()->routeIs('admin.question-banks.*') ? 'active' : '' }}">
                            <i class="fas fa-database"></i>
                            <span class="nav-text">Question Banks</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.live-classes.index') }}" class="nav-link {{ request()->routeIs('admin.live-classes.*') ? 'active' : '' }}">
                            <i class="fas fa-video"></i>
                            <span class="nav-text">Live Classes</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.academic_year.create') }}" class="nav-link {{ request()->routeIs('admin.academic_year.create') ? 'active' : '' }}">
                            <i class="fas fa-calendar-plus"></i>
                            <span class="nav-text">Academic Year</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- MIS (Management Information System) -->
            <div class="nav-section" x-data="{ isOpen: false }">
                <div class="nav-header cursor-pointer" @click="isOpen = !isOpen">
                    <i class="fas fa-chevron-right transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''"></i>
                    MIS
                </div>
                
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-200">
                    <div class="nav-item">
                        <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <i class="fas fa-user-graduate"></i>
                            <span class="nav-text">Students</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.dismissed-students.index') }}" class="nav-link {{ request()->routeIs('admin.dismissed-students.*') ? 'active' : '' }}">
                            <i class="fas fa-user-times"></i>
                            <span class="nav-text">Dismissed Students</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.teachers.index') }}" class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span class="nav-text">Teachers</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Staff Members</span>
                        </a>
                    </div>
                    
                    <!-- Client Management Submenu -->
                    <div class="nav-section" x-data="{ isOpen: {{ request()->routeIs('admin.clients.*') ? 'true' : 'false' }} }">
                        <div class="nav-header cursor-pointer" @click="isOpen = !isOpen">
                            <i class="fas fa-chevron-right transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''"></i>
                            Client Management
                        </div>
                        
                        <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-200">
                            <div class="nav-item">
                                <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.index') ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span class="nav-text">View All Clients</span>
                                </a>
                            </div>
                            
                            <div class="nav-item">
                                <a href="#" onclick="showCreateClientModal()" class="nav-link">
                                    <i class="fas fa-plus"></i>
                                    <span class="nav-text">Create New Client</span>
                                </a>
                            </div>
                            
                            <div class="nav-item">
                                <a href="{{ route('admin.clients.index') }}?status=active" class="nav-link {{ request()->routeIs('admin.clients.index') && request('status') === 'active' ? 'active' : '' }}">
                                    <i class="fas fa-user-check"></i>
                                    <span class="nav-text">Active Clients</span>
                                </a>
                            </div>
                            
                            <div class="nav-item">
                                <a href="{{ route('admin.clients.index') }}?status=inactive" class="nav-link {{ request()->routeIs('admin.clients.index') && request('status') === 'inactive' ? 'active' : '' }}">
                                    <i class="fas fa-user-times"></i>
                                    <span class="nav-text">Inactive Clients</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                                        
                    <div class="nav-item">
                        <a href="{{ route('subscriptions.admin-index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                            <i class="fas fa-crown"></i>
                            <span class="nav-text">Subscriptions</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.branch.index') }}" class="nav-link {{ request()->routeIs('admin.branch.*') ? 'active' : '' }}">
                            <i class="fas fa-code-branch"></i>
                            <span class="nav-text">Branches</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.branch.create') }}" class="nav-link {{ request()->routeIs('admin.branch') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span class="nav-text">School Branch</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.accounting.index') }}" class="nav-link {{ request()->routeIs('admin.accounting.*') ? 'active' : '' }}">
                            <i class="fas fa-balance-scale"></i>
                            <span class="nav-text">Accounting Reports</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reports -->
            <div class="nav-section" x-data="{ isOpen: false }">
                <div class="nav-header cursor-pointer" @click="isOpen = !isOpen">
                    <i class="fas fa-chevron-right transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''"></i>
                    Reports
                </div>
                
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-200">
                    <div class="nav-item">
                        <a href="{{ route('admin.report') }}" class="nav-link {{ request()->routeIs('admin.report') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">System Report</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.report-cards.index') }}" class="nav-link {{ request()->routeIs('admin.report-cards.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span class="nav-text">Report Cards</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.report-card-settings.index') }}" class="nav-link {{ request()->routeIs('admin.report-card-settings.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">Report Card Settings</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.accounting.cashbook') }}" class="nav-link {{ request()->routeIs('admin.accounting.cashbook') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span class="nav-text">Cash Book</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('admin.accounting.cash-flow-forecast') }}" class="nav-link {{ request()->routeIs('admin.accounting.cash-flow-forecast') ? 'active' : '' }}">
                            <i class="fas fa-chart-area"></i>
                            <span class="nav-text">Cash Flow Forecast</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.accounting.balance-sheet') }}" class="nav-link {{ request()->routeIs('admin.accounting.balance-sheet') ? 'active' : '' }}">
                            <i class="fas fa-balance-scale"></i>
                            <span class="nav-text">Balance Sheet</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.accounting.trial-balance') }}" class="nav-link {{ request()->routeIs('admin.accounting.trial-balance') ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span class="nav-text">Trial Balance</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="nav-section" x-data="{ isOpen: false }">
                <div class="nav-header cursor-pointer" @click="isOpen = !isOpen">
                    <i class="fas fa-chevron-right transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''"></i>
                    Documents
                </div>
                
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-200">
                    <div class="nav-item">
                        <a href="{{ route('admin.resources.index') }}" class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                            <i class="fas fa-upload"></i>
                            <span class="nav-text">Resource Upload</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('babyeyi.index') }}" class="nav-link {{ request()->routeIs('babyeyi.*') ? 'active' : '' }}">
                            <i class="fas fa-file-contract"></i>
                            <span class="nav-text">Babyeyi Documents</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="nav-section" x-data="{ isOpen: false }">
                <div class="nav-header cursor-pointer" @click="isOpen = !isOpen">
                    <i class="fas fa-chevron-right transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''"></i>
                    Settings
                </div>
                
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-200">
                    <div class="nav-item">
                        <a href="{{ route('admin.school-information.edit') }}" class="nav-link {{ request()->routeIs('admin.school-information.*') ? 'active' : '' }}">
                            <i class="fas fa-school"></i>
                            <span class="nav-text">School Information</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('admin.preferences') }}" class="nav-link {{ request()->routeIs('admin.preferences') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">System Preferences</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://dashboard.sandbox.irembopay.com/assets/payment/inline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Current time update for Kigali, Rwanda
        function updateTime() {
            const now = new Date();
            const options = { 
                timeZone: 'Africa/Kigali', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false 
            };
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', options);
        }
        
        updateTime();
        setInterval(updateTime, 1000);

        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.querySelector('.sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (mobileMenuToggle && sidebar && mobileOverlay) {
                // Toggle mobile menu
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                    mobileOverlay.classList.toggle('active');
                });
                
                // Close mobile menu when clicking overlay
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    mobileOverlay.classList.remove('active');
                });
                
                // Close mobile menu on window resize if above tablet size
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('mobile-open');
                        mobileOverlay.classList.remove('active');
                    }
                });
            }
        });

        // Profile Dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    profileDropdown.classList.remove('show');
                });
                
                // Prevent dropdown from closing when clicking inside
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });

        // Sidebar Toggle (Desktop)
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.querySelector('.main-content');
            
            if (menuToggle && sidebar && sidebarOverlay && mainContent) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    sidebarOverlay.classList.toggle('active');
                    
                    if (sidebar.classList.contains('collapsed')) {
                        mainContent.style.marginLeft = 'var(--sidebar-collapsed)';
                    } else {
                        mainContent.style.marginLeft = 'var(--sidebar-width)';
                    }
                });
                
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('collapsed');
                    sidebarOverlay.classList.remove('active');
                    mainContent.style.marginLeft = 'var(--sidebar-width)';
                });
            }
        });

        // Initialize tooltips and other Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any tooltips if needed
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            if (tooltipTriggerList.length > 0) {
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });

        // Create Client Modal Function
        function showCreateClientModal() {
            // Redirect to create client page
            window.location.href = '{{ route('admin.clients.create') }}';
        }
    </script>

    <script>
        // Provide a minimal hljs stub when highlight.js is not loaded to avoid console errors
        if (typeof window.hljs === 'undefined') {
            window.hljs = {
                highlightElement: function() {},
                highlightBlock: function() {},
                highlightAll: function() {},
                registerLanguage: function() {},
                initHighlightingOnLoad: function() {}
            };
            console.warn('hljs stub injected: highlight.js not detected.');
        }
    </script>

        @yield('scripts')
        @stack('scripts')
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    </body>
</html>