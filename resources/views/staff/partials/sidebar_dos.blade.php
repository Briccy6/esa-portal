@php
    $staff = Auth::guard('staff')->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>DOS Dashboard - ESA System</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --sidebar-width: 280px;
    --sidebar-collapsed-width: 70px;
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --accent-color: #4f46e5;
    --hover-color: rgba(255, 255, 255, 0.1);
    --active-color: rgba(255, 255, 255, 0.2);
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: rgba(255, 255, 255, 0.1);
    --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-heavy: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  * {
    box-sizing: border-box;
  }

  body, html {
    height: 100%;
    margin: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-weight: 400;
    line-height: 1.6;
    color: var(--text-primary);
    background-color: #f8fafc;
  }

  /* Sidebar Styles */
  #sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: var(--primary-gradient);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1050;
    overflow: hidden;
    box-shadow: var(--shadow-heavy);
  }

  #sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
  }

  #sidebar::before {
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
    border-bottom: 1px solid var(--border-color);
    background: rgba(255, 255, 255, 0.05);
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

  #sidebar.collapsed .sidebar-logo {
    font-size: 0;
    opacity: 0;
  }

  #sidebar.collapsed .sidebar-logo-img {
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
    background: rgba(255, 255, 255, 0.1);
  }

  .sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
  }

  .sidebar-nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
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
    color: rgba(255, 255, 255, 0.9);
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
    background: var(--hover-color);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .nav-link.active {
    background: var(--active-color);
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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

  #sidebar.collapsed .nav-text {
    opacity: 0;
    width: 0;
    margin-left: 0;
  }

  #sidebar.collapsed .nav-icon {
    margin-right: 0;
  }

  #sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.875rem 0;
  }

  /* Logout Button */
  .nav-logout {
    margin-top: auto;
    margin-bottom: 1rem;
  }

  .nav-logout .nav-link {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
  }

  .nav-logout .nav-link:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.5);
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
    box-shadow: var(--shadow-medium);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1051;
    color: var(--accent-color);
  }

  .sidebar-toggle:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-heavy);
  }

  .sidebar-toggle i {
    font-size: 0.875rem;
    transition: transform 0.3s ease;
  }

  #sidebar.collapsed .sidebar-toggle i {
    transform: rotate(180deg);
  }

  /* Main Content */
  #main-content {
    margin-left: var(--sidebar-width);
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex;
    flex-direction: column;
  }

  #main-content.collapsed {
    margin-left: var(--sidebar-collapsed-width);
  }

  /* Top Navigation Bar */
  .topbar {
    background: white;
    box-shadow: var(--shadow-light);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
  }

  .topbar-title {
    font-size: 1.75rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .time-display {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 1.1rem;
    color: var(--text-secondary);
    font-weight: 600;
    background: linear-gradient(45deg, #667eea, #764ba2);
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
    background: rgba(0, 0, 0, 0.05);
  }

  .user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid transparent;
    background: var(--primary-gradient);
    padding: 2px;
    transition: all 0.3s ease;
  }

  .user-btn:hover .user-avatar {
    transform: scale(1.05);
  }

  .user-name {
    font-weight: 600;
    color: var(--text-primary);
  }

  .user-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 0.5rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: var(--shadow-medium);
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
    color: var(--text-primary);
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
  }

  .dropdown-item:hover {
    background: #f3f4f6;
    color: var(--accent-color);
  }

  /* Mobile Responsive */
  @media (max-width: 991.98px) {
    #sidebar {
      transform: translateX(-100%);
    }

    #sidebar.show {
      transform: translateX(0);
    }

    #main-content {
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
    background: var(--primary-gradient);
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
    background: rgba(0, 0, 0, 0.5);
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
@php
    $user = Auth::guard('staff')->user();

    if (!$user) {
        echo "<script>alert('Login first please'); window.location.href = '" . route('staff.login') . "';</script>";
        exit;
    }

    $photoUrl = $user->photo 
        ? (filter_var($user->photo, FILTER_VALIDATE_URL) ? $user->photo : asset('storage/' . $user->photo))
        : asset('images/default-user.png');

    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
@endphp

<!-- Sidebar overlay for small screens -->
<div id="sidebar-overlay" onclick="toggleSidebar(false)"></div>

<div class="d-flex">
  <!-- Sidebar -->
  <nav id="sidebar" class="d-flex flex-column">
    <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleCollapse()">
      <i class="bi bi-chevron-left"></i>
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
          <a href="{{ route('staff.dashboard.dos') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-house-door-fill"></i>
            <span class="nav-text">Home</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{route('staff.profile')}}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-people-fill"></i>
            <span class="nav-text">Profile</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('staff/google-meet') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-google"></i>
            <span class="nav-text">Google Meet</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="{{ route('staff.lessons.create') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-plus-circle-fill"></i>
            <span class="nav-text">Create Lesson</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="{{ route('staff.lessons.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-list-ul"></i>
            <span class="nav-text">Manage Lessons</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="{{ route('staff.lesson-units.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-diagram-3-fill"></i>
            <span class="nav-text">Manage Units</span>
          </a>
        </li>
        
        <li class="nav-item dropdown">
          <a class="nav-link text-white d-flex align-items-center gap-2 dropdown-toggle" href="#" id="academicDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="nav-icon bi bi-book-fill"></i>
            <span class="nav-text">Academic</span>
          </a>
          <ul class="dropdown-menu" aria-labelledby="academicDropdown">
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.courses.*') ? 'active' : '' }}" href="{{ route('dos.courses.index') }}">
                    <i class="fas fa-book"></i> Courses Management
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.live-classes.*') ? 'active' : '' }}" href="{{ route('dos.live-classes.index') }}">
                    <i class="fas fa-video"></i> Live Classes
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.resources.*') ? 'active' : '' }}" href="{{ route('dos.resources.index') }}">
                    <i class="fas fa-file-alt"></i> Study Resources
                </a>
            </li>
        </ul>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.exams.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-clipboard-data-fill"></i>
            <span class="nav-text">Exams</span>
          </a>
        </li>
        
        <li class="nav-item dropdown">
          <a class="nav-link text-white d-flex align-items-center gap-2 dropdown-toggle" href="#" id="studentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="nav-icon bi bi-people-fill"></i>
            <span class="nav-text">Students</span>
          </a>
          <ul class="dropdown-menu" aria-labelledby="studentsDropdown">
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.students.*') ? 'active' : '' }}" href="{{ route('dos.students.index') }}">
                    <i class="fas fa-list"></i> Student List
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.students.*') ? 'active' : '' }}" href="{{ route('dos.students.StudentCards') }}">
                    <i class="fas fa-id-card"></i> Student Cards
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.students.*') ? 'active' : '' }}" href="{{ route('dos.students.report') }}">
                    <i class="fas fa-file-alt"></i> Student Report
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.students.*') ? 'active' : '' }}" href="{{ route('dos.students.attendance') }}">
                    <i class="fas fa-calendar-check"></i> Student Attendance
                </a>
            </li>
        </ul>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.marks.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-clipboard-check-fill"></i>
            <span class="nav-text">Marks Management</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.report-cards.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-file-earmark-text"></i>
            <span class="nav-text">Report Cards Review</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('to-whom.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-file-text"></i>
            <span class="nav-text">Professional Letters</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.dailies.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-calendar-check"></i>
            <span class="nav-text">Teacher Dailies Approval</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.timetable.create') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-calendar2-week"></i>
            <span class="nav-text">Upload Timetable</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.timetable.index') }}" class="nav-link text-white d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-table"></i>
            <span class="nav-text">View Timetables</span>
          </a>
        </li>

        <li class="nav-item nav-logout">
          <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link d-flex align-items-center gap-2">
            <i class="nav-icon bi bi-box-arrow-right"></i>
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
  <div id="main-content" class="d-flex flex-column flex-grow-1">
    <!-- Top navbar -->
    <nav class="topbar">
      <div class="d-flex align-items-center gap-3">
        <!-- Sidebar toggle button for small screens -->
        <button class="mobile-toggle" type="button" onclick="toggleSidebar()">
          <i class="bi bi-list fs-3"></i>
        </button>
        <div class="topbar-title">
          DOS Dashboard
        </div>
      </div>

      <div class="time-display" id="kigali-time">
        Loading time...
      </div>

      <!-- User dropdown -->
      <div class="user-dropdown position-relative">
        <button class="user-btn" onclick="toggleUserDropdown()">
          @php
            $user = Auth::guard('staff')->user();
            $photoUrl = $user->photo && filter_var($user->photo, FILTER_VALIDATE_URL) 
                        ? $user->photo 
                        : ($user->photo ? asset('storage/' . $user->photo) : asset('images/default-user.png'));
          @endphp

          <img src="{{ $photoUrl }}"
               alt="{{ $user->name }}'s photo"
               class="user-avatar"
               style="object-fit: cover;">

          <span class="user-name">{{ $user->name }}</span>
          <i class="bi bi-caret-down-fill"></i>
        </button>

        <div class="user-dropdown-menu" id="userDropdown">
          <a href="#" class="dropdown-item">Profile</a>
          <form method="POST" action="{{ route('staff.logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">Logout</button>
          </form>
        </div>
      </div>
    </nav>

    <!-- Your page content here -->
    @yield('content')

   
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

  // Toggle sidebar for mobile (your original function)
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

  // Kigali time update (your original function)
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

<!-- Alpine.js for dropdown -->
<script src="//unpkg.com/alpinejs" defer></script>
<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>