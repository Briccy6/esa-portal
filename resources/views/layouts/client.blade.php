<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Dashboard - ESA Portal')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Excellent Servant Academy - Quality Education for All')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

        :root {
            --sidebar-w: 260px;
            --bg: #0f1117;
            --surface: #181c27;
            --surface-2: #1e2436;
            --border: rgba(255,255,255,0.07);
            --accent: #4f8ef7;
            --accent-2: #a78bfa;
            --accent-3: #34d399;
            --accent-warm: #fbbf24;
            --text-primary: #f0f2f8;
            --text-muted: #6b7a99;
            --text-dim: #3d4663;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Main Layout */
        .main-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.3s ease, width 0.3s ease;
        }
        
        .sidebar.collapsed {
            width: 60px;
        }
        
        .sidebar-toggle {
            position: absolute;
            top: 20px;
            right: -20px;
            width: 24px;
            height: 24px;
            background: var(--accent);
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        .sidebar-toggle:hover {
            background: var(--accent-2);
            transform: scale(1.1);
        }

        .sidebar-logo {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: var(--surface-2);
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
            position: relative;
        }

        .nav-item:hover {
            background: var(--surface-2);
            color: var(--accent);
        }

        .nav-item.active {
            background: var(--surface-2);
            color: var(--accent);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--accent);
        }

        .sidebar.collapsed .sidebar-logo {
            padding: 20px 18px;
            justify-content: center;
        }
        
        .sidebar.collapsed .sidebar-logo img {
            height: 24px;
        }
        
        .sidebar.collapsed .sidebar-nav {
            padding: 10px 0;
        }
        
        .sidebar.collapsed .nav-item {
            padding: 12px;
            justify-content: center;
        }
        
        .sidebar.collapsed .nav-item span {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-user {
            padding: 10px;
            justify-content: center;
        }
        
        .sidebar.collapsed .user-info {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-logout {
            padding: 10px;
            justify-content: center;
        }

        .sidebar-user {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .user-email {
            font-size: 12px;
            color: var(--text-muted);
        }

        .sidebar-logout {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: var(--surface-2);
            color: var(--text-primary);
            border-color: rgba(79,142,247,0.3);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-w);
            background: var(--bg);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            padding: 0;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-w);
        }
        
        .main-content.collapsed {
            margin-left: 60px;
        }
        
        /* Content wrapper for proper spacing */
        .content-wrapper {
            padding: 24px 32px;
            flex: 1;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 200;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            background: var(--surface-2);
            border-color: var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .main-content.collapsed {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-b border-green-200">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-b border-red-200">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <p class="text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Toggle Button -->
            <button class="sidebar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
                <i class="fas fa-bars" id="toggleIcon"></i>
            </button>
            
            <!-- Logo -->
            <div class="sidebar-logo">
                <img src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="ESA Portal" style="height: 32px;">
            </div>
            
            <!-- Navigation -->
            <nav class="sidebar-nav">
                <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('client.exams.index') }}" class="nav-item {{ request()->routeIs('client.exams.*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Exams</span>
                </a>
                
                <a href="{{ route('client.subscriptions.index') }}" class="nav-item {{ request()->routeIs('client.subscriptions.*') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i>
                    <span>Subscriptions</span>
                </a>
                
                <a href="{{ route('client.tokens.index') }}" class="nav-item {{ request()->routeIs('client.tokens.*') ? 'active' : '' }}">
                    <i class="fas fa-coins"></i>
                    <span>Tokens</span>
                </a>
                
                <a href="{{ route('client.profile.edit') }}" class="nav-item {{ request()->routeIs('client.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                
                <a href="{{ route('client.settings.index') }}" class="nav-item {{ request()->routeIs('client.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>
            
            <!-- User Info -->
            <div class="sidebar-user">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->guard('client')->user()->name ?? 'Client User' }}</div>
                    <div class="user-email">{{ auth()->guard('client')->user()->email ?? 'client@example.com' }}</div>
                </div>
            </div>
            
            <!-- Logout -->
            <div class="sidebar-logout">
                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Desktop Sidebar Toggle -->
        <button class="sidebar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
            <i class="fas fa-bars" id="toggleIcon"></i>
        </button>

        <!-- Main Content -->
        <main class="main-content expanded" id="mainContent">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Sidebar Scripts -->
    <script>
        // Sidebar state management
        let sidebarState = localStorage.getItem('sidebarState') || 'expanded';
        
        // Apply saved state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (sidebarState === 'collapsed') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                mainContent.classList.remove('expanded');
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-chevron-right');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('expanded');
                mainContent.classList.remove('collapsed');
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-bars');
            }
        });
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (sidebar.classList.contains('collapsed')) {
                // Expand sidebar
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
                mainContent.classList.add('expanded');
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-bars');
                sidebarState = 'expanded';
            } else {
                // Collapse sidebar
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                mainContent.classList.remove('expanded');
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-chevron-right');
                sidebarState = 'collapsed';
            }
            
            // Save state to localStorage
            localStorage.setItem('sidebarState', sidebarState);
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            // Only handle sidebar toggle clicks, not form submissions
            if (window.innerWidth <= 768 && 
                (toggle.contains(event.target) || sidebar.contains(event.target)) &&
                sidebar.style.transform !== 'translateX(-100%)') {
                toggleSidebar();
            }
        });
        
        // Reset sidebar on window resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth > 768) {
                sidebar.style.transform = 'translateX(0)';
                mainContent.style.marginLeft = 'var(--sidebar-w)';
            }
        });
    </script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>
