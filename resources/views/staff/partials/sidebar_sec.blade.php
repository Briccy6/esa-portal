@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Panel');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $systemName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --text-light: #64748b;
            --text-dark: #1e293b;
            --bg-light: #f8fafc;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }
        body { background: var(--bg-light); font-family: 'Inter', sans-serif; }
        .layout-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: var(--shadow-lg);
            position: fixed;
            height: 100vh;
            left: 0; top: 0; z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }
        .sidebar-header {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }
        .logo-container { display: flex; align-items: center; justify-content: center; gap: 0.75rem; }
        .logo-image, .logo-placeholder {
            width: 48px; height: 48px; border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.2);
            object-fit: cover;
        }
        .logo-placeholder {
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.5rem;
        }
        .system-name {
            color: white; font-size: 1.2rem; font-weight: 700; margin: 0; white-space: nowrap;
        }
        .sidebar.collapsed .system-name { opacity: 0; transform: translateX(-20px); }
        .sidebar-toggle {
            position: absolute; top: 50%; right: -15px; transform: translateY(-50%);
            background: white; border: none; border-radius: 50%; width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: var(--shadow); color: var(--primary-color); font-size: 12px; cursor: pointer;
            transition: all 0.3s ease;
        }
        .sidebar-toggle:hover { background: var(--primary-color); color: white; }
        .nav-container { padding: 1rem 0; flex: 1; overflow-y: auto; max-height: calc(100vh - 120px);}
        .nav-section { margin-bottom: 2rem; }
        .nav-section-title {
            color: rgba(255,255,255,0.6); font-size: 0.75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em; padding: 0 1.5rem; margin-bottom: 0.5rem;
        }
        .sidebar.collapsed .nav-section-title { opacity: 0; transform: translateX(-20px);}
        .nav-menu { list-style: none; padding: 0; margin: 0; }
        .nav-item { margin-bottom: 0.25rem; }
        .nav-link {
            display: flex; align-items: center; padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.9); text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; border-radius: 0;
        }
        .nav-link::before {
            content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 0;
            background: rgba(255,255,255,0.1); transition: width 0.3s ease;
        }
        .nav-link:hover::before, .nav-link.active::before { width: 4px; }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1); color: white; transform: translateX(4px);
        }
        .nav-link.active { background: rgba(255,255,255,0.15); font-weight: 600; }
        .nav-icon {
            width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;
            margin-right: 0.875rem; font-size: 16px;
        }
        .nav-text { flex: 1; white-space: nowrap; }
        .sidebar.collapsed .nav-text { opacity: 0; transform: translateX(-20px);}
        .sidebar.collapsed .nav-link { padding: 0.875rem; justify-content: center;}
        .sidebar.collapsed .nav-icon { margin: 0;}
        .role-badge {
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px; padding: 0.75rem 1rem; margin: 1rem; text-align: center;
        }
        .role-title { color: rgba(255,255,255,0.7); font-size: 0.75rem; font-weight: 500; margin-bottom: 0.25rem;}
        .role-name { color: white; font-size: 0.875rem; font-weight: 600;}
        .sidebar.collapsed .role-badge { opacity: 0; transform: translateY(20px);}
        .main-content { flex: 1; margin-left: var(--sidebar-width); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);}
        .sidebar.collapsed + .main-content { margin-left: var(--sidebar-collapsed);}
        .topbar {
            background: white; box-shadow: var(--shadow); padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-brand { display: flex; align-items: center; gap: 0.75rem; font-weight: 700; color: var(--text-dark); font-size: 1.1rem;}
        .topbar-logo { width: 32px; height: 32px; border-radius: 8px;}
        .user-info { display: flex; align-items: center; gap: 0.75rem; background: var(--bg-light); padding: 0.5rem 1rem; border-radius: 12px; color: var(--text-dark);}
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;}
        .page-content { padding: 2rem;}
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%);}
            .sidebar.show { transform: translateX(0);}
            .main-content { margin-left: 0;}
            .sidebar-overlay {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999; opacity: 0; visibility: hidden; transition: all 0.3s ease;
            }
            .sidebar-overlay.show { opacity: 1; visibility: visible;}
            .mobile-menu-btn { display: block; background: none; border: none; color: var(--text-dark); font-size: 1.25rem; cursor: pointer;}
        }
        @media (min-width: 769px) { .mobile-menu-btn { display: none; } }
        .nav-container::-webkit-scrollbar { width: 4px;}
        .nav-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 2px;}
        .nav-container::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.5);}
    </style>
</head>
<body>
<div class="layout-container">
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                @if(\App\Helpers\LogoHelper::hasLogo())
                    <img src="{{ $logoUrl }}" alt="System Logo" class="logo-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="logo-placeholder" style="display: none;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                @else
                    <div class="logo-placeholder">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                @endif
                <h5 class="system-name">{{ $systemName }}</h5>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        <div class="nav-container">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link w-100 text-start" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <span class="nav-icon"><i class="fas fa-money-check-alt"></i></span>
                            <span class="nav-text">Add Expense</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.course_fees') ? 'active' : '' }}" href="{{ route('staff.course_fees') }}">
                            <span class="nav-icon"><i class="fas fa-money-bill-wave"></i></span>
                            <span class="nav-text">School Fees</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">
                            <span class="nav-icon"><i class="fas fa-users"></i></span>
                            <span class="nav-text">Students</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.StudentCards') ? 'active' : '' }}" href="{{ route('students.StudentCards') }}">
                            <span class="nav-icon"><i class="fas fa-id-card"></i></span>
                            <span class="nav-text">Student Cards</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.subscriptions.index') ? 'active' : '' }}" href="{{ route('staff.subscriptions.index') }}">
                            <span class="nav-icon"><i class="fas fa-crown"></i></span>
                            <span class="nav-text">Subscriptions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.timetables.*') ? 'active' : '' }}" href="{{ route('staff.timetables.index') }}">
                            <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                            <span class="nav-text">Timetables</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('to-whom.*') ? 'active' : '' }}" href="{{ route('to-whom.index') }}">
                            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                            <span class="nav-text">Official Documents</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('babyeyi.*') ? 'active' : '' }}" href="{{ route('babyeyi.index') }}">
                            <span class="nav-icon"><i class="fas fa-file-contract"></i></span>
                            <span class="nav-text">Term Registration</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.students.create') ? 'active' : '' }}" href="{{ route('staff.students.create') }}">
                            <span class="nav-icon"><i class="fas fa-user-plus"></i></span>
                            <span class="nav-text">Add Student</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Account</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}" href="{{ route('staff.profile') }}">
                            <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
                            <span class="nav-text">My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                            <span class="nav-text">Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('staff.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <div class="role-badge">
            <div class="role-title">Current Role</div>
            <div class="role-name">Accountant & Secretary</div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <nav class="topbar">
            <div class="topbar-brand">
                <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
                <img src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="Logo" class="topbar-logo">
                <span>School Management System</span>
            </div>
            <div class="user-info">
                @include('partials.academic-year-switcher', [
                    'switcherClass' => 'me-3',
                    'labelClass' => 'text-muted',
                    'selectClass' => 'bg-white',
                ])
                <div class="user-avatar">
                    {{ substr(Auth::guard('staff')->user()->name ?? 'S', 0, 1) }}
                </div>
                <span>{{ Auth::guard('staff')->user()->name ?? 'Staff Member' }}</span>
            </div>
        </nav>
        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>

<!-- Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('staff.accountant.expenses.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <label for="paid_by" class="form-label">Paid By</label>
            <input type="text" name="paid_by" id="paid_by" class="form-control" value="{{ Auth::check() ? Auth::user()->name : '' }}" readonly>
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
              <option value="Cash">Cash</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Mobile Money">Mobile Money</option>
              <option value="Check">Check</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="branch_id" class="form-label">Branch *</label>
            <select name="branch_id" id="branch_id" class="form-select" required>
              <option value="">Select Branch</option>
              @php
                  $branches = \App\Models\Branch::orderBy('name')->get();
              @endphp
              @foreach($branches as $branch)
                  <option value="{{ $branch->id }}">{{ $branch->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="term_id" class="form-label">Term (Optional)</label>
            <select name="term_id" id="term_id" class="form-select">
              <option value="">Select Term</option>
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
          <button type="submit" class="btn btn-danger">Save Expense</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        const icon = sidebarToggle.querySelector('i');
        icon.className = sidebar.classList.contains('collapsed') ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
    });
    mobileMenuBtn.addEventListener('click', () => {
        sidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
    });
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        }
    });
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>