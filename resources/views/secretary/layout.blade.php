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
<title>@hasSection('page_title')@yield('page_title')@else @yield('title', 'Secretary Dashboard')@endif - {{ $systemName }}</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@livewireStyles

<style>
  :root {
    --sidebar-width: 280px;
    --sidebar-collapsed-width: 70px;
    --sidebar-color: #1e293b;
    --accent-color: #2457a7;
    --hover-color: rgba(255, 255, 255, 0.1);
    --active-color: rgba(36, 87, 167, 0.28);
    --text-primary: #172b4d;
    --text-secondary: #64748b;
    --border-color: rgba(255, 255, 255, 0.1);
    --shadow-light: 0 2px 8px rgba(15, 23, 42, 0.06);
    --shadow-medium: 0 8px 24px rgba(15, 23, 42, 0.12);
    --shadow-heavy: 0 16px 36px rgba(15, 23, 42, 0.18);
  }

  * { box-sizing: border-box; }

  body, html {
    height: 100%;
    margin: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: var(--text-primary);
    background-color: #f4f7fb;
  }

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

  #sidebar.collapsed { width: var(--sidebar-collapsed-width); }

  .sidebar-header {
    position: relative;
    padding: 1.5rem 1rem;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
    background: rgba(255, 255, 255, 0.05);
  }

  .sidebar-logo {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: #ffffff;
  }

  .sidebar-logo-img {
    max-height: 45px;
    margin-bottom: 0.5rem;
  }

  #sidebar.collapsed .sidebar-logo { font-size: 0; opacity: 0; }
  #sidebar.collapsed .sidebar-logo-img { max-height: 35px; margin-bottom: 0; }

  .sidebar-nav {
    padding: 1rem 0 2rem;
    height: calc(100vh - 120px);
    overflow-y: auto;
    overflow-x: hidden;
  }

  .sidebar-nav::-webkit-scrollbar { width: 4px; }
  .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 2px; }

  .nav-section-label {
    padding: 0.35rem 1.5rem 0.5rem;
    color: rgba(255,255,255,0.45);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  #sidebar.collapsed .nav-section-label { opacity: 0; }

  .nav-item { margin: 0.25rem 0.75rem; }

  .nav-link {
    display: flex;
    align-items: center;
    padding: 0.875rem 1rem;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.95rem;
  }

  .nav-link:hover {
    color: white;
    background: var(--hover-color);
  }

  .nav-link.active {
    background: var(--active-color);
    color: white;
    border-left: 4px solid #60a5fa;
  }

  .nav-icon {
    font-size: 1.2rem;
    min-width: 24px;
    margin-right: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .nav-text { white-space: nowrap; }

  #sidebar.collapsed .nav-text { opacity: 0; width: 0; }
  #sidebar.collapsed .nav-icon { margin-right: 0; }
  #sidebar.collapsed .nav-link { justify-content: center; padding: 0.875rem 0; }

  .nav-logout .nav-link {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
  }

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
    z-index: 1051;
    color: var(--accent-color);
  }

  #main-content {
    margin-left: var(--sidebar-width);
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100vh;
    background: #f4f7fb;
    display: flex;
    flex-direction: column;
  }

  #main-content.collapsed { margin-left: var(--sidebar-collapsed-width); }

  .topbar {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: var(--shadow-light);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    gap: 1rem;
  }

  .topbar-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .time-display {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 1rem;
    color: var(--text-secondary);
    font-weight: 600;
    white-space: nowrap;
  }

  .user-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: none;
    border: none;
    padding: 0.5rem;
    border-radius: 12px;
    cursor: pointer;
  }

  .user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5eaf2;
  }

  .user-name { font-weight: 600; color: var(--text-primary); }

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
    transition: all 0.3s ease;
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
    border: none;
    background: none;
    width: 100%;
    text-align: left;
  }

  .dropdown-item:hover { background: #f3f4f6; color: var(--accent-color); }

  .mobile-toggle {
    display: none;
    background: var(--accent-color);
    border: none;
    border-radius: 8px;
    padding: 0.5rem;
    color: white;
  }

  #sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    display: none;
  }

  #sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
    display: block;
  }

  .content-wrapper {
    padding: clamp(1rem, 3vw, 2rem);
    flex: 1;
    width: 100%;
    min-width: 0;
  }

  .page-shell { max-width: 1440px; margin: 0 auto; }

  .page-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .page-heading h1 {
    margin: 0;
    font-size: clamp(1.4rem, 3vw, 1.9rem);
    font-weight: 700;
    letter-spacing: -0.03em;
  }

  .page-heading p { margin: 0.35rem 0 0; color: var(--text-secondary); }

  .page-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
  }

  .page-actions .btn,
  .sec-btn {
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    border-radius: 0.6rem;
    font-weight: 600;
  }

  .btn-primary { background: var(--accent-color); border: none; }

  .sec-card {
    border: 1px solid #e1e8f0;
    border-radius: 0.85rem;
    background: #fff;
    box-shadow: var(--shadow-light);
    overflow: hidden;
  }

  .sec-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1.1rem 1.2rem 0.85rem;
    border-bottom: 1px solid #edf1f6;
  }

  .sec-card-header h2 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
  }

  .sec-card-header p {
    margin: 0.25rem 0 0;
    color: #94a3b8;
    font-size: 0.8rem;
  }

  .sec-card-body { padding: 1.1rem 1.2rem; }

  .sec-table-wrap { overflow-x: auto; }

  .sec-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
  }

  .sec-table th,
  .sec-table td {
    padding: 0.75rem 0.65rem;
    border-bottom: 1px solid #edf1f6;
    text-align: left;
    vertical-align: middle;
  }

  .sec-table th {
    color: #64748b;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    white-space: nowrap;
  }

  .sec-empty {
    padding: 2rem 1rem;
    color: #94a3b8;
    text-align: center;
  }

  .alert {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  }

  @media (max-width: 768px) {
    #sidebar {
      transform: translateX(-100%);
      visibility: hidden;
    }

    #sidebar.show {
      transform: translateX(0);
      visibility: visible;
    }

    #main-content { margin-left: 0; }
    .sidebar-toggle { display: none; }
    .mobile-toggle { display: block !important; }

    .topbar {
      padding: 0.75rem 1rem;
      flex-wrap: wrap;
    }

    .topbar-title { font-size: 1.1rem; }
    .time-display { display: none; }

    .topbar .academic-year-switcher {
      order: 3;
      flex: 1 1 100%;
      margin: 0 !important;
    }

    .user-name, .user-btn > .bi-caret-down-fill { display: none; }
    body.sidebar-open { overflow: hidden; }

    .page-heading { flex-direction: column; align-items: flex-start; }
    .page-actions { width: 100%; }
    .page-actions .btn { flex: 1 1 auto; }

    .btn, .nav-link, button { min-height: 44px; }
    .form-control, input, select, textarea { font-size: 16px; min-height: 44px; }
  }

  @media (max-width: 576px) {
    :root { --sidebar-width: min(86vw, 320px); }
    .topbar-title {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  }
</style>
@yield('styles')
</head>
<body>

<div id="sidebar-overlay" onclick="toggleSidebar(false)"></div>

<div class="d-flex">
  <nav id="sidebar" class="d-flex flex-column">
    <button class="sidebar-toggle" onclick="toggleCollapse()" type="button" aria-label="Collapse sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>

    <div class="sidebar-header">
      @if($systemLogo)
          <img src="{{ asset('storage/' . $systemLogo) }}" alt="Logo" class="sidebar-logo-img">
      @endif
      <h4 class="sidebar-logo">{{ $systemName }}</h4>
    </div>

    <div class="sidebar-nav">
      <div class="nav-section-label">Main</div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="{{ route('secretary.dashboard') }}" class="nav-link {{ request()->routeIs('secretary.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-door-fill"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person-circle"></i>
            <span class="nav-text">Profile</span>
          </a>
        </li>
      </ul>

      <div class="nav-section-label mt-3">Operations</div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="{{ route('secretary.student-registration.index') }}" class="nav-link {{ request()->routeIs('secretary.student-registration.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person-check-fill"></i>
            <span class="nav-text">Registrations</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('secretary.tokens.index') }}" class="nav-link {{ request()->routeIs('secretary.tokens.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-coin"></i>
            <span class="nav-text">Token Management</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('students.create') }}" class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person-plus-fill"></i>
            <span class="nav-text">Register Student</span>
          </a>
        </li>
      </ul>

      <ul class="nav flex-column mt-4">
        <li class="nav-item nav-logout">
          <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <span class="nav-text">Logout</span>
          </a>
        </li>
      </ul>
    </div>

    <form id="logout-form" action="{{ route('secretary.logout') }}" method="POST" class="d-none">
      @csrf
    </form>
  </nav>

  <div id="main-content" class="d-flex flex-column flex-grow-1">
    <nav class="topbar">
      <div class="d-flex align-items-center gap-3 min-w-0">
        <button class="mobile-toggle" id="mobile-sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Open navigation menu">
          <i class="bi bi-list fs-3"></i>
        </button>
        <div class="topbar-title">
          @hasSection('page_title')
            @yield('page_title')
          @else
            @yield('title', 'Secretary Dashboard')
          @endif
        </div>
      </div>

      <div class="time-display" id="kigali-time">--:--:--</div>

      @include('partials.academic-year-switcher', [
          'switcherClass' => 'me-3',
          'labelClass' => 'text-muted',
          'selectClass' => 'bg-white',
      ])

      <div class="user-dropdown position-relative">
        <button class="user-btn" type="button" onclick="toggleUserDropdown()">
          <img src="{{ $photoUrl }}" alt="{{ $staff->name }}'s photo" class="user-avatar">
          <span class="user-name">{{ $staff->name }}</span>
          <i class="bi bi-caret-down-fill"></i>
        </button>
        <div class="user-dropdown-menu" id="userDropdown">
          <a href="{{ route('staff.profile') }}" class="dropdown-item">Profile</a>
          <form method="POST" action="{{ route('secretary.logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">Logout</button>
          </form>
        </div>
      </div>
    </nav>

    <div class="content-wrapper">
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please check the form:</strong>
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

  function toggleCollapse() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    sidebarCollapsed = !sidebarCollapsed;
    sidebar.classList.toggle('collapsed', sidebarCollapsed);
    mainContent.classList.toggle('collapsed', sidebarCollapsed);
  }

  function toggleSidebar(force) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const isOpen = sidebar.classList.contains('show');
    const shouldShow = force === undefined ? !isOpen : force;

    sidebar.classList.toggle('show', shouldShow);
    overlay.classList.toggle('show', shouldShow);
    document.body.classList.toggle('sidebar-open', shouldShow);
    document.getElementById('mobile-sidebar-toggle').setAttribute('aria-expanded', String(shouldShow));
  }

  function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    userDropdownOpen = !userDropdownOpen;
    dropdown.classList.toggle('show', userDropdownOpen);
  }

  document.addEventListener('click', function(event) {
    const userDropdown = document.querySelector('.user-dropdown');
    const dropdown = document.getElementById('userDropdown');
    if (userDropdown && !userDropdown.contains(event.target)) {
      dropdown.classList.remove('show');
      userDropdownOpen = false;
    }
  });

  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') toggleSidebar(false);
  });

  document.querySelectorAll('#sidebar .nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) toggleSidebar(false);
    });
  });

  function updateTime() {
    const options = { timeZone: 'Africa/Kigali', hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
    document.getElementById('kigali-time').textContent = new Date().toLocaleTimeString('en-US', options);
  }
  updateTime();
  setInterval(updateTime, 1000);

  window.addEventListener('resize', function() {
    if (window.innerWidth > 768) toggleSidebar(false);
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
@yield('scripts')
@stack('scripts')
@auth('staff')
    <x-chat-widget />
@endauth
@vite('resources/js/realtime.js')
</body>
</html>
