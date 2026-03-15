@extends('layouts.client')

@section('title', 'Client Dashboard - ESA Portal')

@section('content')
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

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--bg);
        color: var(--text-primary);
        min-height: 100vh;
        display: flex;
    }

    /* ── SIDEBAR ── */
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
        transition: transform 0.3s ease;
    }

    .sidebar-logo {
        padding: 28px 24px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-icon {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: #fff;
        flex-shrink: 0;
    }

    .logo-text {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 17px;
        letter-spacing: -0.3px;
        color: var(--text-primary);
        line-height: 1.1;
    }

    .logo-text span {
        display: block;
        font-size: 10px;
        font-weight: 400;
        font-family: 'DM Sans', sans-serif;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 2px;
    }

    /* User profile strip */
    .sidebar-user {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-2), var(--accent));
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 14px;
        color: #fff;
        flex-shrink: 0;
    }

    .user-info .user-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .user-info .user-role {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    /* Nav */
    .sidebar-nav {
        flex: 1;
        padding: 16px 12px;
        overflow-y: auto;
    }

    .nav-section-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--text-dim);
        padding: 8px 12px 6px;
        margin-top: 8px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 400;
        transition: all 0.18s ease;
        margin-bottom: 2px;
        position: relative;
    }

    .nav-item:hover {
        background: var(--surface-2);
        color: var(--text-primary);
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(79,142,247,0.15), rgba(167,139,250,0.1));
        color: var(--accent);
        font-weight: 500;
    }

    .nav-item.active::before {
        content: '';
        position: absolute;
        left: 0; top: 20%; bottom: 20%;
        width: 3px;
        background: linear-gradient(var(--accent), var(--accent-2));
        border-radius: 0 2px 2px 0;
    }

    .nav-item i {
        width: 18px;
        text-align: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .nav-badge {
        margin-left: auto;
        background: var(--accent);
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 20px;
    }

    /* Token chip in sidebar */
    .sidebar-token-card {
        margin: 12px;
        padding: 14px 16px;
        background: linear-gradient(135deg, rgba(79,142,247,0.12), rgba(167,139,250,0.12));
        border: 1px solid rgba(79,142,247,0.2);
        border-radius: 12px;
    }

    .sidebar-token-card .label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }

    .sidebar-token-card .value {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: var(--accent);
        line-height: 1;
    }

    .sidebar-token-card .sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .btn-buy-tokens {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        margin-top: 10px;
        padding: 8px;
        background: var(--accent);
        color: #fff;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: opacity 0.18s;
    }

    .btn-buy-tokens:hover { opacity: 0.85; color: #fff; }

    /* Logout */
    .sidebar-footer {
        padding: 12px;
        border-top: 1px solid var(--border);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        color: #f87171;
        font-size: 13.5px;
        cursor: pointer;
        background: none;
        border: none;
        width: 100%;
        transition: background 0.18s;
    }

    .logout-btn:hover { background: rgba(248,113,113,0.08); }

    /* ── MAIN AREA ── */
    .main-wrapper {
        margin-left: var(--sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Topbar */
    .topbar {
        height: 64px;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 32px;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .topbar-left h1 {
        font-family: 'Syne', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .topbar-left p {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .topbar-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 20px;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .topbar-pill i { font-size: 12px; }

    /* Page content */
    .page-content {
        padding: 32px;
        flex: 1;
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, #1a2a4a 0%, #1a1a3a 50%, #2a1a3a 100%);
        border: 1px solid rgba(79,142,247,0.2);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(79,142,247,0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 30%;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(167,139,250,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-banner h2 {
        font-family: 'Syne', sans-serif;
        font-size: 26px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .hero-banner p {
        color: #8899bb;
        font-size: 14px;
        margin-bottom: 24px;
    }

    .hero-pills {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .hero-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        backdrop-filter: blur(4px);
    }

    .hero-pill i { font-size: 18px; }
    .hero-pill .pill-title { font-size: 13px; font-weight: 500; color: var(--text-primary); }
    .hero-pill .pill-sub { font-size: 11px; color: #6b7a99; }

    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        transition: border-color 0.2s, transform 0.2s;
    }

    .stat-card:hover {
        border-color: rgba(79,142,247,0.3);
        transform: translateY(-2px);
    }

    .stat-card .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        margin-bottom: 14px;
    }

    .stat-card .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-card .stat-label {
        font-size: 12px;
        color: var(--text-muted);
    }

    .stat-card .stat-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 500;
        text-decoration: none;
        margin-top: 12px;
        opacity: 0.8;
        transition: opacity 0.15s;
    }

    .stat-card .stat-link:hover { opacity: 1; }

    /* Two-col layout */
    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 28px;
    }

    @media (max-width: 900px) {
        .two-col { grid-template-columns: 1fr; }
    }

    .panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
    }

    .panel-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-title a {
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 400;
        color: var(--accent);
        text-decoration: none;
    }

    /* Subscription panel */
    .sub-active {
        background: linear-gradient(135deg, rgba(52,211,153,0.08), rgba(79,142,247,0.06));
        border-color: rgba(52,211,153,0.2);
    }

    .sub-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }

    .sub-row:last-child { border-bottom: none; }
    .sub-row .key { color: var(--text-muted); }
    .sub-row .val { font-weight: 500; color: var(--text-primary); }

    .badge-active {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        background: rgba(52,211,153,0.15);
        color: #34d399;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-active::before {
        content: '';
        width: 6px; height: 6px;
        background: #34d399;
        border-radius: 50%;
        display: inline-block;
    }

    /* Activity list */
    .activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child { border-bottom: none; }

    .activity-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .activity-item .a-type { font-size: 13px; font-weight: 500; }
    .activity-item .a-time { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
    .activity-item .a-amount { margin-left: auto; font-size: 13px; font-weight: 600; }

    /* Quick actions */
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    @media (max-width: 768px) {
        .actions-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .action-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px 12px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        text-decoration: none;
        color: var(--text-muted);
        font-size: 12.5px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-align: center;
    }

    .action-card:hover {
        transform: translateY(-3px);
        color: var(--text-primary);
        border-color: rgba(79,142,247,0.3);
    }

    .action-card .action-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }

    /* Resources */
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    @media (max-width: 640px) {
        .resources-grid { grid-template-columns: 1fr; }
    }

    .resource-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 10px;
        transition: border-color 0.2s;
    }

    .resource-card:hover { border-color: rgba(79,142,247,0.25); }

    .resource-card .r-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .resource-card h4 {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .resource-card p {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .resource-card a {
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
    }

    /* Mobile sidebar toggle */
    .mobile-toggle {
        display: none;
        align-items: center;
        justify-content: center;
        width: 36px; height: 36px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-muted);
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .main-wrapper {
            margin-left: 0;
        }
        .mobile-toggle {
            display: flex;
        }
        .page-content {
            padding: 20px;
        }
        .topbar {
            padding: 0 20px;
        }
        .hero-banner {
            padding: 24px 20px;
        }
        .hero-banner h2 {
            font-size: 20px;
        }
        .actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Overlay for mobile */
    .overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        z-index: 90;
    }

    .overlay.active { display: block; }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 24px 0;
        color: var(--text-muted);
    }

    .empty-state i { font-size: 32px; margin-bottom: 8px; opacity: 0.4; display: block; }
    .empty-state p { font-size: 13px; }
</style>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="logo-text">
            ESA Portal
            <span>E-Learning Platform</span>
        </div>
    </div>

    <!-- User Strip -->
    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->guard('client')->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <div class="user-name">{{ auth()->guard('client')->user()->name }}</div>
            <div class="user-role">Student Account</div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('client.dashboard') }}" class="nav-item active">
            <i class="fas fa-th-large"></i>
            Dashboard
        </a>

        <a href="{{ route('public.exams.index') }}" class="nav-item">
            <i class="fas fa-graduation-cap"></i>
            Practice Exams
        </a>

        <a href="{{ route('public.subscriptions.index') }}" class="nav-item">
            <i class="fas fa-crown"></i>
            Subscriptions
        </a>

        <div class="nav-section-label">Tokens</div>

        <a href="{{ route('public.tokens.purchase') }}" class="nav-item">
            <i class="fas fa-coins"></i>
            Buy Tokens
        </a>

        <a href="{{ route('public.tokens.index') }}" class="nav-item">
            <i class="fas fa-history"></i>
            Token History
        </a>

        <div class="nav-section-label">Account</div>

        <a href="#" class="nav-item">
            <i class="fas fa-user-circle"></i>
            My Profile
        </a>

        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i>
            Settings
        </a>
    </nav>

    <!-- Token card -->
    <div class="sidebar-token-card">
        <div class="label">Token Balance</div>
        <div class="value">{{ auth()->guard('client')->user()->getCurrentTokens() }}</div>
        <div class="sub">Available for practice tests</div>
        <a href="{{ route('public.tokens.purchase') }}" class="btn-buy-tokens">
            <i class="fas fa-plus"></i>
            Buy More Tokens
        </a>
    </div>

    <!-- Footer / Logout -->
    <div class="sidebar-footer">
        <form action="{{ route('client.auth.logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>
</div>

<!-- Mobile overlay -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left" style="display:flex; align-items:center; gap:14px;">
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1>Dashboard</h1>
                <p>Welcome back, {{ auth()->guard('client')->user()->name }}!</p>
            </div>
        </div>
        <div class="topbar-right">
            @if(auth()->guard('client')->user()->hasActiveSubscription())
                <div class="topbar-pill" style="color:#34d399; border-color:rgba(52,211,153,0.2);">
                    <i class="fas fa-circle" style="font-size:7px;"></i>
                    Premium Active
                </div>
            @else
                <div class="topbar-pill">
                    <i class="fas fa-crown"></i>
                    Free Plan
                </div>
            @endif
            <div class="topbar-pill">
                <i class="fas fa-coins" style="color:var(--accent-warm);"></i>
                {{ auth()->guard('client')->user()->getCurrentTokens() }} tokens
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Hero Banner -->
        <div class="hero-banner">
            <h2>Welcome to Your Learning Portal!</h2>
            <p>Access practice exams, manage your subscription, and track your learning progress.</p>
            <div class="hero-pills">
                <div class="hero-pill">
                    <i class="fas fa-graduation-cap" style="color:var(--accent);"></i>
                    <div>
                        <div class="pill-title">Practice Exams</div>
                        <div class="pill-sub">Comprehensive practice tests</div>
                    </div>
                </div>
                <div class="hero-pill">
                    <i class="fas fa-crown" style="color:var(--accent-2);"></i>
                    <div>
                        <div class="pill-title">Subscriptions</div>
                        <div class="pill-sub">Premium plans & benefits</div>
                    </div>
                </div>
                <div class="hero-pill">
                    <i class="fas fa-chart-line" style="color:var(--accent-3);"></i>
                    <div>
                        <div class="pill-title">Track Progress</div>
                        <div class="pill-sub">Monitor your journey</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="panel" style="margin-bottom:20px;">
            <div class="panel-title">Quick Actions</div>
            <div class="actions-grid">
                <a href="{{ route('public.exams.index') }}" class="action-card">
                    <div class="action-icon" style="background:rgba(79,142,247,0.12); color:var(--accent);">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    Browse Exams
                </a>
                <a href="{{ route('public.subscriptions.index') }}" class="action-card">
                    <div class="action-icon" style="background:rgba(167,139,250,0.12); color:var(--accent-2);">
                        <i class="fas fa-crown"></i>
                    </div>
                    Subscriptions
                </a>
                <a href="{{ route('public.tokens.purchase') }}" class="action-card">
                    <div class="action-icon" style="background:rgba(251,191,36,0.12); color:var(--accent-warm);">
                        <i class="fas fa-coins"></i>
                    </div>
                    Buy Tokens
                </a>
                <a href="{{ route('public.tokens.index') }}" class="action-card">
                    <div class="action-icon" style="background:rgba(52,211,153,0.12); color:var(--accent-3);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    View History
                </a>
            </div>
        </div>

        <!-- Two-col: Subscription + Activity -->
        <div class="two-col">

            <!-- Subscription Status -->
            <div class="panel {{ auth()->guard('client')->user()->hasActiveSubscription() ? 'sub-active' : '' }}">
                <div class="panel-title">
                    Subscription Status
                    <a href="{{ route('public.subscriptions.index') }}">Manage →</a>
                </div>

                @if(auth()->guard('client')->user()->hasActiveSubscription())
                    @php $activeSub = auth()->guard('client')->user()->activeSubscription; @endphp
                    <div class="sub-row">
                        <span class="key">Plan</span>
                        <span class="val">{{ $activeSub->subscription->name }}</span>
                    </div>
                    <div class="sub-row">
                        <span class="key">Status</span>
                        <span class="badge-active">Active</span>
                    </div>
                    <div class="sub-row">
                        <span class="key">Tokens Included</span>
                        <span class="val">{{ $activeSub->subscription->formatted_tokens }}</span>
                    </div>
                    <div class="sub-row">
                        <span class="key">Expires</span>
                        <span class="val">{{ $activeSub->ends_at->format('M j, Y') }}</span>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-crown"></i>
                        <p>No active subscription</p>
                    </div>
                    <a href="{{ route('public.subscriptions.index') }}"
                       style="display:flex; align-items:center; justify-content:center; gap:8px; padding:10px; background:var(--accent); color:#fff; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; margin-top:12px;">
                        <i class="fas fa-crown"></i>
                        Get a Subscription
                    </a>
                @endif
            </div>

            <!-- Recent Activity -->
            <div class="panel">
                <div class="panel-title">
                    Recent Activity
                    <a href="{{ route('public.tokens.index') }}">View All →</a>
                </div>
                @php
                    $recentTransactions = auth()->guard('client')->user()->transactions()->latest()->take(5)->get();
                @endphp
                @if($recentTransactions->count() > 0)
                    @foreach($recentTransactions as $tx)
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--accent);"></div>
                            <div>
                                <div class="a-type">{{ $tx->type }}</div>
                                <div class="a-time">{{ $tx->created_at->format('M j, H:i') }}</div>
                            </div>
                            <div class="a-amount" style="color:var(--accent-3);">{{ $tx->formatted_amount }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No recent activity yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Available Resources -->
        <div class="panel">
            <div class="panel-title">Available Resources</div>
            <div class="resources-grid">
                <div class="resource-card">
                    <div class="r-icon" style="background:rgba(79,142,247,0.12); color:var(--accent);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h4>Practice Tests</h4>
                        <p>Wide range of exams across subjects and difficulty levels.</p>
                        <a href="{{ route('public.exams.index') }}" style="color:var(--accent);">Explore Tests →</a>
                    </div>
                </div>
                <div class="resource-card">
                    <div class="r-icon" style="background:rgba(167,139,250,0.12); color:var(--accent-2);">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div>
                        <h4>Premium Plans</h4>
                        <p>Exclusive benefits and token packages with subscriptions.</p>
                        <a href="{{ route('public.subscriptions.index') }}" style="color:var(--accent-2);">View Plans →</a>
                    </div>
                </div>
                <div class="resource-card">
                    <div class="r-icon" style="background:rgba(251,191,36,0.12); color:var(--accent-warm);">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h4>Token Packages</h4>
                        <p>Bulk token purchases with discounts and bonus tokens.</p>
                        <a href="{{ route('public.tokens.purchase') }}" style="color:var(--accent-warm);">Buy Tokens →</a>
                    </div>
                </div>
                <div class="resource-card">
                    <div class="r-icon" style="background:rgba(52,211,153,0.12); color:var(--accent-3);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h4>Progress Tracking</h4>
                        <p>Monitor learning progress and track achievements over time.</p>
                        <a href="{{ route('public.tokens.index') }}" style="color:var(--accent-3);">View Progress →</a>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /page-content -->
</div><!-- /main-wrapper -->

<script>
    function toggleSidebar() {
        const s = document.getElementById('sidebar');
        const o = document.getElementById('overlay');
        s.classList.toggle('open');
        o.classList.toggle('active');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('active');
    }
</script>

@endsection