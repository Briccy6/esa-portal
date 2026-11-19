@php
    $staff = Auth::guard('staff')->user();

    if (!$staff) {
        echo "<script>alert('Login first please'); window.location.href = '" . route('staff.login') . "';</script>";
        exit;
    }

    $photoUrl = \App\Support\StaffPhoto::url($staff->photo, asset('images/default-user.png'));

    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
    $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@hasSection('page_title')@yield('page_title')@else @yield('title', 'DOS Dashboard')@endif - {{ $systemName }}</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
@yield('styles')

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --sidebar-width: 280px;
    --sidebar-collapsed-width: 70px;
    --sidebar-color: #1e293b;
    --accent-color: #3b82f6;
    --hover-color: rgba(255, 255, 255, 0.1);
    --active-color: rgba(59, 130, 246, 0.2);
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: rgba(255, 255, 255, 0.1);
    --shadow-light: 0 2px 8px rgba(15, 23, 42, 0.06);
    --shadow-medium: 0 8px 24px rgba(15, 23, 42, 0.12);
    --shadow-heavy: 0 16px 36px rgba(15, 23, 42, 0.18);
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
    background: var(--sidebar-color);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1050;
    overflow: hidden;
    box-shadow: var(--shadow-heavy);
  }

  #sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
  }

  /* Sidebar Header */
  .sidebar-header {
    position: relative;
    padding: 1.5rem 1rem;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
    background: rgba(255, 255, 255, 0.05);
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
    border-left: 4px solid var(--accent-color);
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
    display: none;
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
    background: #f4f7fb;
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
    background: rgba(255, 255, 255, 0.95);
  }

  .topbar-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .time-display {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 1.1rem;
    color: var(--text-secondary);
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
    background: rgba(0, 0, 0, 0.05);
  }

  .user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5eaf2;
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
  @media (max-width: 768px) {
    #sidebar {
      transform: translateX(-100%);
      visibility: hidden;
    }

    #sidebar.show {
      transform: translateX(0);
      visibility: visible;
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
      padding: 0.75rem 1rem;
      flex-wrap: wrap;
      gap: 0.5rem;
    }

    .topbar-title {
      font-size: 1.15rem;
    }

    .time-display {
      display: none;
    }

    .topbar .academic-year-switcher {
      order: 3;
      flex: 1 1 100%;
      margin: 0 !important;
    }

    .topbar .academic-year-switcher select {
      width: 100%;
    }

    .user-name,
    .user-btn > .bi-caret-down-fill {
      display: none;
    }

    .user-avatar {
      width: 38px;
      height: 38px;
    }

    body.sidebar-open {
      overflow: hidden;
    }

    .btn, .nav-link, button {
      min-height: 44px;
    }

    .form-control, input, select, textarea {
      font-size: 16px;
      min-height: 44px;
    }
  }

  .mobile-toggle {
    display: none;
    background: var(--accent-color);
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

  /* Content Styling */
  .content-wrapper {
    padding: clamp(1rem, 3vw, 2rem);
    flex: 1;
    width: 100%;
    min-width: 0;
  }

  .card {
    border: none;
    border-radius: 12px;
    box-shadow: var(--shadow-light);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    margin-bottom: 1.5rem;
  }

  .card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
  }

  .card-header {
    background: var(--sidebar-color);
    color: white;
    border: none;
    font-weight: 600;
    padding: 15px 20px;
  }

  .btn-primary {
    background: var(--accent-color);
    border: none;
    border-radius: 25px;
    padding: 10px 25px;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
  }

  .alert {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  @media (max-width: 576px) {
    :root {
      --sidebar-width: min(86vw, 320px);
    }

    .topbar > .d-flex:first-child {
      min-width: 0;
      flex: 1;
    }

    .topbar-title {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .user-btn {
      padding: 0.25rem;
    }
  }

</style>
@include('dos.design-system')
</head>
<body class="dos-dashboard">

<!-- Sidebar overlay for small screens -->
<div id="sidebar-overlay" onclick="toggleSidebar(false)"></div>

<div class="d-flex dos-app-shell">
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
          <a href="{{ route('staff.dashboard.dos') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('staff.dashboard.dos') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-door-fill"></i>
            <span class="nav-text">Home</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{route('staff.profile')}}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
            <i class="nav-icon bi bi-people-fill"></i>
            <span class="nav-text">Profile</span>
          </a>
        </li>

                
        <li class="nav-item">
          <a href="{{ route('staff.lessons.create') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('staff.lessons.create') ? 'active' : '' }}">
            <i class="nav-icon bi bi-plus-circle-fill"></i>
            <span class="nav-text">Create Lesson</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="{{ route('staff.lessons.index') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('staff.lessons.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-list-ul"></i>
            <span class="nav-text">Manage Lessons</span>
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
            <li>
                <a class="dropdown-item {{ request()->routeIs('dos.teacher-dailies.*') ? 'active' : '' }}" href="{{ route('dos.teacher-dailies.index') }}">
                    <i class="fas fa-calendar-check"></i> Teacher Daily Activities
                </a>
            </li>
        </ul>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.exams.index') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('dos.exams.*') ? 'active' : '' }}">
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
          <a href="{{ route('dos.marks.index') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('dos.marks.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-clipboard-check-fill"></i>
            <span class="nav-text">Marks Management</span>
          </a>
        </li>

        
        <li class="nav-item">
          <a href="{{ route('dos.dailies.index') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('dos.dailies.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-calendar-check"></i>
            <span class="nav-text">Teacher Dailies Approval</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.timetable.create') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('dos.timetable.create') ? 'active' : '' }}">
            <i class="nav-icon bi bi-calendar2-week"></i>
            <span class="nav-text">Upload Timetable</span>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('dos.timetable.index') }}" class="nav-link text-white d-flex align-items-center gap-2 {{ request()->routeIs('dos.timetable.*') ? 'active' : '' }}">
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
        <button class="mobile-toggle" id="mobile-sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
          <i class="bi bi-list fs-3" aria-hidden="true"></i>
        </button>
        <div class="topbar-title">
          @hasSection('page_title')
            @yield('page_title')
          @else
            @yield('title', 'DOS Dashboard')
          @endif
        </div>
      </div>

      <div class="time-display" id="kigali-time">
        Loading time...
      </div>

      @include('partials.academic-year-switcher', [
          'switcherClass' => 'me-3',
          'labelClass' => 'text-muted',
          'selectClass' => 'bg-white',
      ])

      <!-- User dropdown -->
      <div class="user-dropdown position-relative">
        <button class="user-btn" onclick="toggleUserDropdown()">
          <img src="{{ $photoUrl }}"
               alt="{{ $staff->name }}'s photo"
               class="user-avatar"
               style="object-fit: cover;">

          <span class="user-name">{{ $staff->name }}</span>
          <i class="bi bi-caret-down-fill"></i>
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

    document.getElementById('mobile-sidebar-toggle').setAttribute('aria-expanded', String(shouldShow));
    document.getElementById('mobile-sidebar-toggle').setAttribute('aria-label', shouldShow ? 'Close navigation menu' : 'Open navigation menu');
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

  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      toggleSidebar(false);
    }
  });

  document.querySelectorAll('#sidebar .nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768 && !link.classList.contains('dropdown-toggle')) {
        toggleSidebar(false);
      }
    });
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
      document.getElementById('mobile-sidebar-toggle').setAttribute('aria-expanded', 'false');
      document.getElementById('mobile-sidebar-toggle').setAttribute('aria-label', 'Open navigation menu');
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

</body>
</html>
