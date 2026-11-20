@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo() ?: 'images/esa.jpg';
    $systemLogoUrl = $systemLogo === 'images/esa.jpg'
        ? asset($systemLogo)
        : asset('storage/' . $systemLogo);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> {{ $systemName }}-@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,600;6..72,700&display=swap" rel="stylesheet">
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
            --primary-blue: #19382d;
            --secondary-blue: #244b3b;
            --accent-blue: #e5b968;
            --hover-blue: #55876a;
            --text-light: #f4f3e9;
            --text-muted: #bdc9bd;
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

        /* Ensure main content uses full width on mobile */
        .main-content {
            background: #f4f7fa;
        }

        /* Prevent text overflow in cards */
        .card-body {
            overflow: hidden;
            word-wrap: break-word;
        }

        .card-body .h5,
        .card-body h5 {
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .card-body .text-xs {
            word-break: break-word;
            overflow-wrap: break-word;
        }

        /* Fix Bootstrap card layout */
        .card-body .row {
            width: 100%;
        }

        .card-body .col {
            min-width: 0;
        }

        /* Mobile Responsive Fixes */
        @media (max-width: 768px) {
            /* Prevent horizontal scroll on mobile */
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

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-group .btn {
                width: auto;
            }

            /* Collapse table columns on very small screens */
            .table-responsive {
                font-size: 0.75rem;
            }

            /* Modal adjustments */
            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
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

        /* Top Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: #19382d;
            box-shadow: var(--shadow);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem 0 calc(var(--sidebar-width) + 1rem);
            color: white;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Collapsed sidebar — JS adds class to body instead */
        body.sidebar-collapsed .top-navbar {
            padding-left: calc(var(--sidebar-collapsed) + 1rem);
        }
        body.sidebar-collapsed .main-content {
            margin-left: calc(var(--sidebar-collapsed) + 1rem);
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
            min-height: 44px;
        }

        .profile-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        .profile-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #3498db;
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
            overflow-x: hidden;
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
            min-height: 44px;
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

        /* Content area — keep everything inside the main column */
        .main-content .container-fluid {
            width: 100%;
            margin-left: 0;
            padding-left: 0;
            padding-right: 0;
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
            min-height: 44px;
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
            flex: 1;
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
            background: #e74c3c;
            border: none;
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            opacity: 0.88;
        }

        /* Main Content */
        .main-content {
            /* Keep main content fully inside the main column next to the fixed sidebar. */
            margin-left: calc(var(--sidebar-width) + 1rem);
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            box-sizing: border-box;
        }

        /* Center page content and prevent it from sliding under the sidebar */
        .main-content-inner {
            width: 100%;
            max-width: 1400px;
        }

        /* Safety override:
           If JS/DOM ends up with an unexpected combination of classes
           (for example `.sidebar.collapsed` without `body.sidebar-collapsed`),
           ensure the main content offset still matches the sidebar width. */
        .sidebar.collapsed ~ .main-content {
            margin-left: calc(var(--sidebar-collapsed) + 1rem) !important;
        }
        .sidebar:not(.collapsed) ~ .main-content {
            margin-left: calc(var(--sidebar-width) + 1rem) !important;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .top-navbar {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0.75rem !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .main-content-inner {
                max-width: none;
            }

            .main-content .container-fluid {
                width: 100%;
                margin-left: 0;
                padding-left: 0;
                padding-right: 0;
            }

            .navbar-right {
                gap: 0.5rem;
            }

            .current-time {
                display: none;
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

            /* Mobile stats cards */
            .stats-card {
                padding: 1.5rem;
            }

            .stats-number {
                font-size: 2rem;
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

            /* Mobile page header */
            .page-header {
                padding: 1.5rem;
                margin: -1rem -1rem 1rem -1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .header-actions {
                flex-direction: column;
                gap: 0.5rem;
                width: 100%;
            }

            .header-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .top-navbar {
                padding: 0.5rem 0.75rem;
                height: 60px;
            }

            .navbar-left h5 {
                display: none;
            }

            .navbar-brand {
                font-size: 0.875rem;
            }

            .main-content {
                padding: 0.75rem;
            }

            /* Extra small tables */
            .table {
                font-size: 0.8125rem;
            }

            .table th,
            .table td {
                padding: 0.375rem;
            }

            /* Extra small cards */
            .card-body {
                padding: 0.75rem;
            }

            .stats-card {
                padding: 1rem;
            }

            .stats-number {
                font-size: 1.75rem;
            }

            .stats-icon {
                font-size: 2rem;
            }

            /* Extra small forms */
            .form-control,
            .form-select {
                padding: 0.5rem;
            }

            /* Action buttons on mobile */
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.8125rem;
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
                margin-right: 0 !important;
                padding: 0.75rem !important;
                width: 100vw !important;
                max-width: 100vw !important;
                left: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .row {
                margin: 0 -0.5rem !important;
                width: 100% !important;
            }

            .col-xl-3,
            .col-md-6,
            .col-lg-4,
            .col-lg-6 {
                padding: 0.5rem !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .card {
                margin-bottom: 0.75rem !important;
                border-radius: 0.5rem !important;
                overflow: hidden;
                width: 100% !important;
            }

            .card-body {
                padding: 0.875rem !important;
                overflow: hidden;
                width: 100% !important;
            }

            .card .row {
                margin: 0 !important;
                width: 100% !important;
            }

            .card .col {
                padding: 0.25rem !important;
                min-width: 0 !important;
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

            /* Mobile nav links */
            .nav-link {
                min-height: 48px !important;
                padding: 1rem 1.5rem !important;
            }

            .nav-header {
                min-height: 48px !important;
                padding: 1rem 1.5rem !important;
            }
            
            /* Mobile dropdown */
            .dropdown-menu-custom {
                width: 250px !important;
                right: 1rem !important;
            }
            
            /* Mobile cards */
            .card {
                margin: 0 0 0.75rem 0 !important;
                border-radius: 0.5rem !important;
                overflow: hidden;
            }

            .card-body {
                padding: 0.875rem !important;
                overflow: hidden;
            }

            .card .row {
                margin: 0 !important;
            }

            .card .col {
                padding: 0.25rem !important;
            }

            /* Mobile tables */
            .table-responsive {
                margin: 0 -0.75rem 0.75rem -0.75rem !important;
                padding: 0 0.75rem !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                font-size: 0.8125rem !important;
                min-width: 600px;
            }

            .table th,
            .table td {
                padding: 0.375rem !important;
                white-space: nowrap;
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
                padding: 0.5rem !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100vw !important;
                max-width: 100vw !important;
                left: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
                width: 100% !important;
            }

            .row {
                margin: 0 -0.25rem !important;
                width: 100% !important;
            }

            .col-xl-3,
            .col-md-6,
            .col-lg-4,
            .col-lg-6 {
                padding: 0.25rem !important;
            }

            .card {
                margin: 0 0 0.5rem 0 !important;
            }

            .card-body {
                padding: 0.75rem !important;
            }

            /* Fix card layout on small screens */
            .card-body .col-auto {
                flex: 0 0 40px !important;
                max-width: 40px !important;
            }

            .card-body .col.mr-2 {
                margin-right: 0.5rem !important;
                flex: 1 !important;
                min-width: 0 !important;
            }

            .card-body .h5 {
                font-size: 0.875rem !important;
                line-height: 1.2 !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
            }

            .card-body .text-xs {
                font-size: 0.65rem !important;
                line-height: 1.1 !important;
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
            background: #2c3e50;
            margin: -2rem -2rem 2rem -2rem;
            padding: 2rem;
            border-radius: 0 0 0.5rem 0.5rem;
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
                margin: -1rem -1rem 1rem -1rem;
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

        /* Extra small screens */
        @media (max-width: 375px) {
            .main-content {
                padding: 0.5rem !important;
            }

            .card-body {
                padding: 0.625rem !important;
            }

            .card-body .col-auto {
                flex: 0 0 35px !important;
                max-width: 35px !important;
            }

            .card-body .h5 {
                font-size: 0.8125rem !important;
            }

            .card-body .text-xs {
                font-size: 0.6rem !important;
            }

            .table {
                font-size: 0.75rem !important;
            }

            .badge {
                font-size: 0.6rem !important;
                padding: 0.2rem 0.35rem !important;
            }
        }
    /* ESA Panel 2026 design system */
    :root {
        --esa-ink: #202b24;
        --esa-muted: #647168;
        --esa-primary: #477a57;
        --esa-primary-dark: #356346;
        --esa-accent: #e5b968;
        --esa-paper: #f5f4ed;
        --esa-card: #fffefa;
        --esa-line: #dfe3d8;
        --esa-shadow: 0 18px 50px rgba(38, 55, 40, 0.09);
        --esa-radius: 18px;
    }

    body {
        font-family: "DM Sans", sans-serif !important;
        background:
            radial-gradient(circle at 92% 8%, rgba(117, 153, 111, 0.13), transparent 18rem),
            var(--esa-paper) !important;
        color: var(--esa-ink);
        --bs-body-color: var(--esa-ink);
        --bs-body-bg: var(--esa-paper);
        --bs-emphasis-color: var(--esa-ink);
        --bs-secondary-color: var(--esa-muted);
        --bs-border-color: var(--esa-line);
        --bs-table-color: var(--esa-ink);
        color-scheme: light;
    }

    h1, h2, h3, .sidebar-logo {
        font-family: "Newsreader", Georgia, serif;
        letter-spacing: -0.02em;
    }

    .top-navbar {
        height: 64px;
        background: rgba(25, 56, 45, 0.97) !important;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        backdrop-filter: blur(18px);
    }

    .sidebar {
        background: #19382d !important;
        border-right: 1px solid rgba(255,255,255,0.06);
    }

    .sidebar-header {
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent !important;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .sidebar-logo {
        color: #f4f3e9;
        font-size: 1.55rem;
    }

    .nav-header {
        color: #bdc9bd;
        font-family: "DM Sans", sans-serif;
        font-size: 0.68rem;
    }

    .nav-link {
        min-height: 44px;
        margin: 2px 10px;
        border-radius: 10px;
        color: #e0e8df;
        transition: background 180ms ease, color 180ms ease, transform 180ms ease;
    }

    .nav-link:hover {
        background: rgba(143, 183, 142, 0.18);
        color: #ffffff;
        transform: translateX(2px);
    }

    .nav-link.active {
        background: var(--esa-accent) !important;
        color: var(--esa-ink) !important;
        border: 0;
        box-shadow: 0 8px 24px rgba(229, 185, 104, 0.16);
    }

    .main-content {
        background: transparent !important;
        padding-top: 64px;
        width: auto;
    }

    .esa-school-watermark {
        position: fixed;
        z-index: 900;
        top: 56%;
        left: 58%;
        width: min(38vw, 460px);
        max-height: 58vh;
        object-fit: contain;
        transform: translate(-50%, -50%);
        opacity: 0.045;
        pointer-events: none;
        user-select: none;
    }

    .main-content-inner {
        min-height: calc(100vh - 64px);
        padding: 28px 30px 44px;
    }

    .card, .table-card, .chart-card, .stat-card, .quick-action-card {
        border: 1px solid var(--esa-line) !important;
        border-radius: var(--esa-radius) !important;
        box-shadow: var(--esa-shadow) !important;
    }

    .btn {
        min-height: 42px;
        border-radius: 10px !important;
        font-weight: 700;
    }

    .btn-primary {
        background: var(--esa-primary) !important;
        border-color: var(--esa-primary) !important;
    }

    .btn-primary:hover {
        background: var(--esa-primary-dark) !important;
        border-color: var(--esa-primary-dark) !important;
    }

    .btn-success {
        background: #477a57 !important;
        border-color: #477a57 !important;
        color: #ffffff !important;
    }

    .btn-success:hover {
        background: #356346 !important;
        border-color: #356346 !important;
    }

    .form-control, .form-select {
        min-height: 44px;
        border: 1px solid var(--esa-line);
        border-radius: 10px;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--esa-primary);
        box-shadow: 0 0 0 3px rgba(71, 122, 87, 0.17);
    }

    .table-responsive {
        border-radius: 14px;
    }

    .table thead th {
        background: #e9eee4;
        color: #46564a;
        border-bottom: 1px solid var(--esa-line);
        white-space: nowrap;
    }

    .table tbody tr {
        transition: background 160ms ease;
    }

    .table tbody tr:hover {
        background: #f2f4ec;
    }

    [data-theme="dark"] body,
    body[data-theme="dark"] {
        --esa-ink: #f0f3ed;
        --esa-muted: #bdc9bd;
        --esa-primary: #9ac69e;
        --esa-primary-dark: #b3d5b5;
        --esa-accent: #e5b968;
        --esa-paper: #121b16;
        --esa-card: #1b2920;
        --esa-line: #35463a;
        --bs-body-color: var(--esa-ink);
        --bs-body-bg: var(--esa-paper);
        --bs-emphasis-color: var(--esa-ink);
        --bs-secondary-color: var(--esa-muted);
        --bs-border-color: var(--esa-line);
        --bs-table-color: var(--esa-ink);
        background: var(--esa-paper) !important;
        color: var(--esa-ink) !important;
        color-scheme: dark;
    }

    .main-content,
    .main-content h1, .main-content h2, .main-content h3,
    .main-content h4, .main-content h5, .main-content h6,
    .main-content p, .main-content label, .main-content .form-label,
    .main-content .text-dark, .main-content .table,
    .main-content .table td {
        color: var(--esa-ink);
    }

    .main-content .text-dark {
        color: var(--esa-ink) !important;
    }

    .main-content .text-black,
    .main-content .link-dark {
        color: var(--esa-ink) !important;
    }

    .main-content a:not(.btn):not(.nav-link):not(.dropdown-item-custom) {
        color: var(--esa-primary);
    }

    .main-content .text-muted,
    .main-content .form-text {
        color: var(--esa-muted) !important;
    }

    .main-content .form-control,
    .main-content .form-select {
        color: var(--esa-ink);
        background-color: var(--esa-card);
    }

    .main-content .form-control::placeholder {
        color: var(--esa-muted);
        opacity: 1;
    }

    .dropdown-menu-custom {
        background: var(--esa-card);
        border: 1px solid var(--esa-line);
    }

    .dropdown-item-custom {
        color: var(--esa-ink);
    }

    .dropdown-item-custom:hover {
        background: var(--esa-paper);
        color: var(--esa-primary);
    }

    .select2-container--default .select2-selection--single {
        background-color: var(--esa-card);
        border-color: var(--esa-line);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--esa-ink);
    }

    .main-content .table {
        --bs-table-color: var(--esa-ink);
    }

    body[data-theme="dark"] .esa-school-watermark {
        opacity: 0.07;
        mix-blend-mode: screen;
    }

    body[data-theme="dark"] .card,
    body[data-theme="dark"] .chart-card,
    body[data-theme="dark"] .stat-card,
    body[data-theme="dark"] .table-card,
    body[data-theme="dark"] .quick-action-card,
    body[data-theme="dark"] .recent-activity {
        background: var(--esa-card) !important;
    }

    body[data-theme="dark"] .main-content .bg-white,
    body[data-theme="dark"] .main-content .table-light,
    body[data-theme="dark"] .main-content .list-group-item,
    body[data-theme="dark"] .main-content .dropdown-menu,
    body[data-theme="dark"] .main-content .modal-content {
        background-color: var(--esa-card) !important;
        border-color: var(--esa-line) !important;
        color: var(--esa-ink) !important;
    }

    body[data-theme="dark"] .select2-dropdown {
        background-color: var(--esa-card);
        border-color: var(--esa-line);
    }

    body[data-theme="dark"] .select2-results__option {
        color: var(--esa-ink);
    }

    body[data-theme="dark"] .select2-results__option--highlighted[aria-selected] {
        background-color: var(--esa-primary);
        color: #19382d;
    }

    body[data-theme="dark"] .swal2-popup {
        background: var(--esa-card);
        color: var(--esa-ink);
    }

    body[data-theme="dark"] .swal2-title,
    body[data-theme="dark"] .swal2-html-container {
        color: var(--esa-ink);
    }

    body[data-theme="dark"] .dropdown-item-custom {
        color: var(--esa-ink);
    }

    body[data-theme="dark"] .table thead th,
    body[data-theme="dark"] .table tbody tr:hover {
        background: #26372b;
        color: #f0f3ed;
    }

    body[data-theme="dark"] .btn-primary {
        color: #19382d !important;
    }

    .theme-toggle {
        width: 42px;
        height: 42px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        color: white;
    }

    .mobile-menu-toggle {
        display: none;
    }

    .mobile-sidebar-close {
        display: none;
    }

    body.mobile-nav-open {
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .esa-school-watermark {
            left: 52%;
            width: min(68vw, 340px);
            opacity: 0.035;
        }

        .main-content-inner {
            padding: 18px 14px 32px;
        }

        .top-navbar {
            height: 64px;
            gap: 8px;
            padding: 0 12px !important;
            padding-left: 12px !important;
        }

        .main-content {
            margin-left: 0 !important;
            margin-right: 0 !important;
            margin-top: 0 !important;
            padding: 76px 12px 24px !important;
            width: 100% !important;
            max-width: 100% !important;
            left: 0 !important;
        }

        .sidebar.collapsed ~ .main-content,
        .sidebar:not(.collapsed) ~ .main-content,
        body.sidebar-collapsed .main-content {
            margin-left: 0 !important;
        }

        .main-content-inner {
            width: 100%;
            max-width: none;
            padding: 0;
        }

        .mobile-menu-toggle {
            display: inline-grid !important;
            position: static !important;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            place-items: center;
            padding: 0 !important;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            background: #244761 !important;
            color: #fff;
            box-shadow: none !important;
        }

        .navbar-left {
            min-width: 0;
            gap: 10px;
        }

        .navbar-left h5 {
            overflow: hidden;
            margin: 0;
            font-size: .95rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .navbar-right {
            min-width: 0;
            gap: 6px;
        }

        .navbar-right .academic-year-switcher {
            min-width: 0;
            margin-right: 0 !important;
        }

        .navbar-right .academic-year-switcher .form-select {
            width: clamp(94px, 28vw, 126px);
            min-width: 0 !important;
            padding: 6px 22px 6px 8px;
            overflow: hidden;
            font-size: .72rem;
            text-overflow: ellipsis;
        }

        .navbar-right .theme-toggle {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
        }

        .profile-btn {
            gap: 0;
            padding: 4px !important;
        }

        .profile-btn > span,
        .profile-btn > .fa-chevron-down {
            display: none;
        }

        .sidebar {
            top: 0;
            bottom: 0;
            width: min(300px, calc(100vw - 56px)) !important;
            max-width: 300px;
            transform: translateX(-105%) !important;
            box-shadow: 18px 0 48px rgba(8, 18, 30, .28);
        }

        .sidebar.collapsed {
            width: min(300px, calc(100vw - 56px)) !important;
        }

        .sidebar.mobile-open,
        .sidebar.collapsed.mobile-open {
            width: min(300px, calc(100vw - 56px)) !important;
            transform: translateX(0) !important;
        }

        .sidebar.mobile-open .sidebar-logo,
        .sidebar.mobile-open.collapsed .sidebar-logo,
        .sidebar.mobile-open .nav-header,
        .sidebar.mobile-open.collapsed .nav-header,
        .sidebar.mobile-open .nav-text,
        .sidebar.mobile-open.collapsed .nav-text {
            opacity: 1 !important;
        }

        .sidebar.mobile-open .nav-link {
            justify-content: flex-start;
            padding: 10px 16px !important;
        }

        .sidebar.mobile-open .nav-header {
            padding: 10px 16px !important;
        }

        .sidebar.mobile-open .sidebar-header {
            justify-content: space-between;
            padding: 12px 16px;
        }

        .mobile-sidebar-close {
            display: inline-grid !important;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 10px;
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .sidebar-menu {
            height: calc(100vh - 64px);
            padding-bottom: 24px;
        }

        .mobile-overlay {
            z-index: 1039;
            background: rgba(13, 23, 36, .52);
            backdrop-filter: blur(2px);
        }

        .mobile-overlay.active {
            display: block !important;
        }

        .dashboard-container {
            width: 100%;
            max-width: 100%;
            padding: 0 !important;
            overflow-x: clip;
        }

        .stats-grid,
        .charts-grid,
        .quick-actions {
            width: 100%;
            min-width: 0;
            grid-template-columns: minmax(0, 1fr) !important;
        }

        .stat-card,
        .chart-card,
        .quick-action-card,
        .recent-activity {
            width: 100%;
            min-width: 0;
        }

        .stat-card-value {
            overflow-wrap: anywhere;
            font-size: clamp(1.6rem, 8vw, 2rem);
        }

        .chart-card-body {
            min-width: 0;
            overflow: hidden;
        }

        .navbar-right .current-time {
            display: none;
        }
    }

    @media (max-width: 380px) {
        .navbar-left h5 {
            display: none;
        }

        .navbar-right .theme-toggle {
            display: none;
        }
    }
    </style>
    @yield('styles')
    @stack('styles')
    <style>
        .main-content table tbody tr:hover > td,
        .main-content table tbody tr:hover > th {
            background-color: #e9eee4 !important;
            color: #202b24 !important;
        }

        [data-theme="dark"] .main-content table tbody tr:hover > td,
        [data-theme="dark"] .main-content table tbody tr:hover > th {
            background-color: #26372b !important;
            color: #f0f3ed !important;
        }

        .esa-school-watermark {
            z-index: 0;
        }

        .main-content {
            position: relative;
        }

        .main-content-inner {
            position: relative;
        }
    </style>
    @livewireStyles
</head>
<body>
 @include('partials.storage-warning')
    <img class="esa-school-watermark" src="{{ $systemLogoUrl }}" alt="" aria-hidden="true">
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
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
            <button class="menu-toggle d-none d-md-block" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0">Dashboard</h5>
        </div>
        
        <div class="navbar-right">
            @if(isset($adminAcademicYears) && $adminAcademicYears->count())
            @include('partials.academic-year-switcher', [
                'switcherClass' => 'me-3',
                'labelClass' => 'text-white-50',
                'selectStyle' => 'background: #244b3b; color: #fff; border-color: #759b77;',
            ])
            @endif

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
                $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
                $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
            @endphp

            @if($systemLogo)
                <img src="{{ asset('storage/' . $systemLogo) }}" alt="Logo" style="max-height:40px;">
            @endif
            <h4 class="sidebar-logo">
                {{ $systemName }}
            </h4>
            <button type="button" class="mobile-sidebar-close" id="mobileSidebarClose" aria-label="Close navigation menu">
                <i class="fas fa-times"></i>
            </button>
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
                        <a href="{{ route('admin.lessons.index') }}" class="nav-link {{ request()->routeIs('admin.lessons.*') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap"></i>
                            <span class="nav-text">Lessons</span>
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
                        <a href="{{ route('admin.timetables.index') }}" class="nav-link {{ request()->routeIs('admin.timetables.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">Timetables</span>
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
                        <a href="{{ route('admin.students.StudentCards') }}" class="nav-link {{ request()->routeIs('admin.students.StudentCards') ? 'active' : '' }}">
                            <i class="fas fa-id-card"></i>
                            <span class="nav-text">Student Cards</span>
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

                    <div class="nav-item">
                        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i>
                            <span class="nav-text">Announcements</span>
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
        <div class="main-content-inner">
            @yield('content')
        </div>
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
            const mobileSidebarClose = document.getElementById('mobileSidebarClose');

            if (mobileMenuToggle && sidebar && mobileOverlay) {
                function setMobileMenuOpen(isOpen) {
                    sidebar.classList.toggle('mobile-open', isOpen);
                    sidebar.classList.toggle('show', isOpen);
                    mobileOverlay.classList.toggle('active', isOpen);
                    document.body.classList.toggle('mobile-nav-open', isOpen);
                    mobileMenuToggle.setAttribute('aria-expanded', String(isOpen));
                }

                mobileMenuToggle.addEventListener('click', function() {
                    setMobileMenuOpen(!sidebar.classList.contains('mobile-open'));
                });

                mobileOverlay.addEventListener('click', function() {
                    setMobileMenuOpen(false);
                });

                if (mobileSidebarClose) {
                    mobileSidebarClose.addEventListener('click', function() {
                        setMobileMenuOpen(false);
                    });
                }

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
                        setMobileMenuOpen(false);
                    }
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        setMobileMenuOpen(false);
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
                    document.body.classList.toggle('sidebar-collapsed');
                    sidebarOverlay.classList.toggle('active');
                    
                    if (sidebar.classList.contains('collapsed')) {
                        mainContent.style.marginLeft = 'calc(var(--sidebar-collapsed) + 1rem)';
                    } else {
                        mainContent.style.marginLeft = 'calc(var(--sidebar-width) + 1rem)';
                    }
                });
                
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('collapsed');
                    document.body.classList.remove('sidebar-collapsed');
                    sidebarOverlay.classList.remove('active');
                    mainContent.style.marginLeft = 'calc(var(--sidebar-width) + 1rem)';
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const navbarRight = document.querySelector('.navbar-right');
                if (navbarRight && !document.getElementById('esaThemeToggle')) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.id = 'esaThemeToggle';
                    button.className = 'theme-toggle';
                    button.setAttribute('aria-label', 'Toggle dark theme');
                    button.innerHTML = '<i class="fas fa-moon"></i>';
                    navbarRight.prepend(button);

                    const savedTheme = localStorage.getItem('esa-theme');
                    if (savedTheme === 'dark') {
                        document.body.dataset.theme = 'dark';
                        button.innerHTML = '<i class="fas fa-sun"></i>';
                    }
                    button.setAttribute('aria-pressed', String(savedTheme === 'dark'));

                    button.addEventListener('click', function () {
                        const dark = document.body.dataset.theme !== 'dark';
                        document.body.dataset.theme = dark ? 'dark' : 'light';
                        localStorage.setItem('esa-theme', dark ? 'dark' : 'light');
                        button.innerHTML = dark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                        button.setAttribute('aria-pressed', String(dark));
                        document.dispatchEvent(new CustomEvent('esa-theme-changed', {
                            detail: { theme: dark ? 'dark' : 'light' }
                        }));
                    });
                }

                if (window.Swal) {
                    @if(session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: @json(session('success')),
                            timer: 3500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    @endif
                    @if(session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: @json(session('error')),
                            confirmButtonColor: '#477a57'
                        });
                    @endif
                    @if($errors->any())
                        Swal.fire({
                            icon: 'error',
                            title: 'Please review the form',
                            text: @json($errors->first()),
                            confirmButtonColor: '#477a57'
                        });
                    @endif
                }
            });

            window.ESA = window.ESA || {};
            window.ESA.confirmDelete = function (options) {
                const config = Object.assign({
                    title: 'Delete this record?',
                    text: 'This action cannot be undone.',
                    confirmText: 'Delete',
                    form: null
                }, options || {});

                if (!window.Swal) return config.form && config.form.submit();

                Swal.fire({
                    icon: 'warning',
                    title: config.title,
                    text: config.text,
                    showCancelButton: true,
                    confirmButtonText: config.confirmText,
                    confirmButtonColor: '#b42318',
                    cancelButtonColor: '#6d7771'
                }).then(function (result) {
                    if (result.isConfirmed && config.form) config.form.submit();
                });
            };
        </script>
        @yield('scripts')
        @stack('scripts')
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    </body>
</html>