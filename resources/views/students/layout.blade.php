<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - ESA-Parnel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #27ae60;
            --secondary-color: #2ecc71;
            --accent-color: #f39c12;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --gray-color: #95a5a6;
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Layout */
        .portal-container {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 1.5rem;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            font-size: 1.5rem;
        }

        .nav-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .user-dropdown-toggle:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            min-width: 200px;
            margin-top: 0.5rem;
            z-index: 1000;
            display: none;
        }

        .user-dropdown-menu.show {
            display: block;
        }

        .user-dropdown-menu a,
        .user-dropdown-menu button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: var(--dark-color);
            text-decoration: none;
            border: none;
            background: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            text-align: left;
        }

        .user-dropdown-menu a:hover,
        .user-dropdown-menu button:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .user-dropdown-menu button {
            color: #e74c3c;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--dark-color);
            min-height: 100vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            transition: transform 0.3s ease, width 0.3s ease;
            padding-top: 1.5rem;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }

        .sidebar-header h5 {
            color: white;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 0.75rem;
        }

        .sidebar-menu-item {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--light-color);
            text-decoration: none;
            padding: 0.875rem 1.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .sidebar-menu-link:hover,
        .sidebar-menu-link.active {
            background-color: var(--accent-color);
            color: white;
            transform: translateX(4px);
        }

        .sidebar-menu-link i {
            font-size: 1.1rem;
            min-width: 1.5rem;
        }

        /* Mobile sidebar */
        @media (max-width: 991.98px) {
            .portal-container {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 260px;
                height: 100vh;
                z-index: 1040;
                transform: translateX(-100%);
                background: var(--dark-color);
                overflow-y: auto;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }

            .sidebar-overlay.show {
                display: block;
            }

            body.sidebar-open {
                overflow: hidden;
            }

            .main-content-wrapper {
                width: 100%;
            }

            .navbar {
                position: sticky;
                top: 0;
                z-index: 1050;
            }
        }

        @media (max-width: 767.98px) {
            .sidebar {
                width: 240px;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .navbar {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .sidebar {
                width: 220px;
            }

            .sidebar-header {
                padding: 0 1rem 1rem;
            }

            .sidebar-menu-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
        }

        /* Main Content */
        .main-content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 1.5rem;
        }

        /* Desktop navbar */
        @media (max-width: 991.98px) {
            .navbar {
                position: sticky;
                top: 0;
                z-index: 1050;
            }

            .toggle-sidebar-btn {
                display: inline-flex !important;
            }
        }

        .toggle-sidebar-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: none;
            background-color: rgba(255,255,255,0.2);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 1rem;
        }

        .toggle-sidebar-btn:hover {
            background-color: rgba(255,255,255,0.3);
        }

        /* Content area */
        .content-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .page-title {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar,
        .main-content::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track,
        .main-content::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb,
        .main-content::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .main-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .main-content {
                padding: 1rem;
            }

            .content-section {
                padding: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: 0.75rem;
            }

            .content-section {
                padding: 0.75rem;
                border-radius: 8px;
            }

            .page-title {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
        }

        /* Alert styles */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar-btn" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand" href="#">
                    <i class="bi bi-mortarboard-fill"></i>
                    ESA-Parnel Portal
                </a>
            </div>

            <div class="nav-dropdown">
                <div class="user-dropdown-toggle" onclick="toggleDropdown()">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-md-inline">Student Name</span>
                    <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
                </div>

                <div class="user-dropdown-menu" id="userDropdown">
                    <a href="#">
                        <i class="bi bi-person"></i>
                        Profile
                    </a>
                    <hr style="margin: 0.25rem 0; opacity: 0.1;">
                    <button onclick="logout()">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar(false)"></div>

    <!-- Main Container -->
    <div class="portal-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h5>
                    <i class="bi bi-menu-button"></i>
                    Menu
                </h5>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link active" href="#">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-book-fill"></i>
                        <span>My Courses</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-star-fill"></i>
                        <span>My Grades</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-file-text-fill"></i>
                        <span>Exams</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-credit-card-fill"></i>
                        <span>Fees Payment</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-download"></i>
                        <span>Resources</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-calendar-event-fill"></i>
                        <span>Timetable</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="#">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>Messages</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main-content-wrapper">
            <div class="main-content">
                <div class="alert alert-success d-none" id="successAlert">
                    <i class="bi bi-check-circle me-2"></i>
                    Success message appears here
                </div>

                <div class="alert alert-danger d-none" id="errorAlert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error message appears here
                </div>

                <div class="content-section">
                    <h1 class="page-title">
                        <i class="bi bi-house-door"></i>
                        Welcome to Your Dashboard
                    </h1>
                    <p>Select an option from the sidebar to get started.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar(force) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            const isOpen = sidebar.classList.contains('show');
            const shouldShow = force === undefined ? !isOpen : force;

            if (shouldShow) {
                sidebar.classList.add('show');
                overlay.classList.add('show');
                document.body.classList.add('sidebar-open');
            } else {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const toggle = event.target.closest('.user-dropdown-toggle');
            
            if (!toggle && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Close sidebar when clicking on a link
        document.querySelectorAll('.sidebar-menu-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    toggleSidebar(false);
                }
            });
        });

        function logout() {
            alert('Logout functionality would be implemented here');
        }
    </script>

</body>
</html>