@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $systemLogo = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
      <title>{{ $systemName}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
   
<link rel="icon" type="image/png" href="{{ $systemLogo }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
        }

        body {
            min-height: 100vh;
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Top Navigation */
        .top-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.15);
            border: none;
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.025em;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .user-info .reg-number {
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0;
        }

        .user-info .welcome-text {
            font-size: 0.75rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            min-width: 280px;
            max-width: 280px;
            min-height: calc(100vh - 76px);
            background: var(--sidebar-bg);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            border-right: 1px solid #334155;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid #334155;
            margin-bottom: 0.5rem;
            position: sticky;
            top: 0;
            background: var(--sidebar-bg);
            z-index: 10;
        }

        .sidebar-title {
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }

        /* Navigation Groups */
        .nav-group {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }

        .nav-group-title {
            color: var(--text-light);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.5rem 1.25rem 0.25rem;
            margin-bottom: 0.5rem;
            position: sticky;
            top: 60px;
            background: var(--sidebar-bg);
            z-index: 5;
        }

        .sidebar .nav-link {
            color: var(--text-light);
            padding: 0.625rem 1.25rem;
            margin: 0.125rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            min-height: 44px; /* Touch-friendly size */
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
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active i {
            color: #fff;
        }

        /* Logout Button */
        .logout-section {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #334155;
            position: sticky;
            bottom: 0;
            background: var(--sidebar-bg);
        }

        .logout-btn {
            color: #ef4444;
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
            padding: 2rem;
            background: #f8fafc;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 76px;
                height: calc(100vh - 76px);
                z-index: 1000;
                transition: left 0.3s ease;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .sidebar.show {
                left: 0;
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
            }

            .main-content {
                padding: 1rem;
                margin-left: 0;
            }

            .mobile-toggle {
                display: block !important;
            }

            /* Ensure content doesn't overlap with sidebar */
            .content-wrapper {
                margin-left: 0;
            }

            /* Prevent body scroll when sidebar is open */
            body.sidebar-open {
                overflow: hidden;
            }
        }

        /* Small mobile devices */
        @media (max-width: 480px) {
            .sidebar {
                width: 260px;
                left: -260px;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                padding: 0.75rem;
            }

            .user-info {
                padding: 0.375rem 0.75rem;
            }

            .user-info .reg-number {
                font-size: 0.75rem;
            }

            .user-info .welcome-text {
                font-size: 0.7rem;
            }
        }

        .mobile-toggle {
            display: none;
        }

        /* Content Cards */
        .content-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Mobile sidebar improvements */
        @media (max-width: 768px) {
            .sidebar {
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
            }

            .sidebar .nav-link {
                min-height: 48px; /* Larger touch target for mobile */
                padding: 0.75rem 1.25rem;
            }

            .nav-group-title {
                padding: 0.75rem 1.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .sidebar-header {
                padding: 1rem 1.25rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    {{-- Top Navigation --}}
    <nav class="navbar navbar-expand-lg navbar-dark top-navbar">
        <div class="container-fluid">
            <button class="btn btn-outline-light mobile-toggle me-3" type="button" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ $systemLogo }}" alt="Logo" style="height:40px;" class="me-2">
                <span>{{$systemName}}</span>
            </a>
            <div class="d-flex ms-auto">
                <div class="user-info">
                    <p class="welcome-text">Welcome back</p>
                    <p class="reg-number">
                        Reg: {{ Auth::guard('student')->user()->reg_number ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        {{-- Sidebar --}}
        <div class="sidebar d-flex flex-column" id="sidebar">
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
                            <a href="{{route('student.courses')}}" class="nav-link">
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
                            <a href="{{ route('student.dashboard') }}#timetables" class="nav-link">
                                <i class="bi bi-calendar2-week"></i>
                                Timetables
                            </a>
                        </li>
                        <li class="nav-item">
       
<a href="{{ route('student.resources.index') }}" class="nav-link {{ request()->routeIs('student.resources.index') ? 'active' : '' }}">
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
                            <a href="{{ route('to-whom.index') }}" class="nav-link {{ request()->routeIs('to-whom.*') ? 'active' : '' }}">
                                <i class="bi bi-file-text"></i>
                                Professional Letters
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-clipboard-check"></i>
                                Assignments
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
                            <a href="{{route('student.invoices')}}" class="nav-link">
                                <i class="bi bi-receipt"></i>
                                Invoices
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
        </div>

        {{-- Main Content --}}
        <div class="main-content">
            <!-- Laravel Flash Messages -->
            @include('components.laravel-flash')
            <div class="content-wrapper p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const body = document.body;
            
            sidebar.classList.toggle('show');
            body.classList.toggle('sidebar-open');
            
            // Prevent scrolling when sidebar is open on mobile
            if (sidebar.classList.contains('show')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                    document.body.style.overflow = '';
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const body = document.body;
            
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                body.classList.remove('sidebar-open');
                body.style.overflow = '';
            }
        });

        // Close sidebar with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                const body = document.body;
                
                if (sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    body.classList.remove('sidebar-open');
                    body.style.overflow = '';
                }
            }
        });

        // Handle touch gestures for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', function(event) {
            touchStartX = event.changedTouches[0].screenX;
        });
        
        document.addEventListener('touchend', function(event) {
            touchEndX = event.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const sidebar = document.getElementById('sidebar');
            const swipeThreshold = 50;
            const swipeDistance = touchEndX - touchStartX;
            
            if (window.innerWidth <= 768) {
                // Swipe right to open sidebar
                if (swipeDistance > swipeThreshold && touchStartX < 50) {
                    sidebar.classList.add('show');
                    document.body.classList.add('sidebar-open');
                    document.body.style.overflow = 'hidden';
                }
                // Swipe left to close sidebar
                else if (swipeDistance < -swipeThreshold && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                    document.body.style.overflow = '';
                }
            }
        }

        // Add smooth scrolling to sidebar
        document.querySelectorAll('.sidebar a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>