@extends('layouts.client')

@section('title', 'Dashboard - ESA Portal')

@section('content')
<style>
:root {
    --bg: #f4f6fb;
    --surface: #ffffff;
    --surface-2: #f8f9fc;
    --border: #e5e7eb;
    --accent: #7c3aed;
    --accent-2: #8b5cf6;
    --accent-3: #06b6d4;
    --accent-warm: #f59e0b;
    --text-primary: #1f2937;
    --text-muted: #6b7280;
    --text-dim: #9ca3af;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    line-height: 1.6;
}

/* Topbar */
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    border-radius: 12px 12px 0 0;
    margin-bottom: 24px;
}

.topbar-left h1 {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.topbar-left p {
    font-size: 14px;
    color: var(--text-muted);
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
    padding: 8px 16px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 24px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-muted);
    transition: all 0.2s ease;
}

.topbar-pill.active {
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(139, 92, 246, 0.1));
    border-color: var(--accent);
    color: var(--accent);
}

.topbar-pill i {
    font-size: 14px;
}

/* Page Content Wrapper */
.page-content {
    padding: 0 24px 24px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: var(--surface);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--accent), var(--accent-2));
}

.stat-card.purple::before {
    background: linear-gradient(90deg, #7c3aed, #8b5cf6);
}

.stat-card.blue::before {
    background: linear-gradient(90deg, #3b82f6, #06b6d4);
}

.stat-card.teal::before {
    background: linear-gradient(90deg, #06b6d4, #10b981);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-card.purple .stat-icon {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
}

.stat-card.blue .stat-icon {
    background: linear-gradient(135deg, #3b82f6, #06b6d4);
}

.stat-card.teal .stat-icon {
    background: linear-gradient(135deg, #06b6d4, #10b981);
}

.stat-period {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
}

.stat-value {
    font-family: 'Syne', sans-serif;
    font-size: 32px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 500;
}

.stat-change {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 12px;
    padding: 4px 8px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.stat-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* Quick Actions Grid */
.actions-section {
    margin-bottom: 32px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.section-title {
    font-family: 'Syne', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}

.action-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 12px;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border-color: var(--accent);
}

.action-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
}

.action-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
}

.action-description {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.4;
}

/* Two Column Layout */
.two-column {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
}

@media (max-width: 968px) {
    .two-column {
        grid-template-columns: 1fr;
    }
}

/* Panels */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.panel-title {
    font-family: 'Syne', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}

.panel-link {
    font-size: 13px;
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.panel-link:hover {
    color: var(--accent-2);
}

/* Subscription Status */
.subscription-status {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}

.status-item:last-child {
    border-bottom: none;
}

.status-label {
    font-size: 14px;
    color: var(--text-muted);
}

.status-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(34, 197, 94, 0.1));
    color: #10b981;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.activity-time {
    font-size: 12px;
    color: var(--text-muted);
}

.activity-amount {
    font-size: 14px;
    font-weight: 600;
    color: var(--accent);
}

/* Resources Grid */
.resources-section {
    margin-bottom: 32px;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.resource-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

.resource-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}

.resource-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
}

.resource-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
}

.resource-description {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 16px;
}

.resource-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--accent);
    text-decoration: none;
    transition: color 0.2s ease;
}

.resource-link:hover {
    color: var(--accent-2);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 32px;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-primary);
}

.empty-state p {
    font-size: 14px;
    margin-bottom: 20px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.btn-outline {
    background: transparent;
    color: var(--accent);
    border: 2px solid var(--accent);
}

.btn-outline:hover {
    background: var(--accent);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
    }
    
    .topbar-right {
        width: 100%;
        justify-content: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
    }
    
    .page-content {
        padding: 0 16px 16px;
    }
    
    .stat-value {
        font-size: 28px;
    }
    
    .action-card {
        padding: 20px 16px;
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->guard('client')->user()->name }}!</p>
    </div>
    <div class="topbar-right">
        @if(auth()->guard('client')->user()->hasActiveSubscription())
            <div class="topbar-pill active">
                <i class="fas fa-crown"></i>
                Premium Active
            </div>
        @else
            <div class="topbar-pill">
                <i class="fas fa-user"></i>
                Free Plan
            </div>
        @endif
        <div class="topbar-pill">
            <i class="fas fa-coins"></i>
            {{ auth()->guard('client')->user()->getCurrentTokens() }} tokens
        </div>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-period">This Week</div>
            </div>
            <div class="stat-value">
                @php
                    $weeklyPurchases = auth()->guard('client')->user()->transactions()
                        ->where('type', 'purchase')
                        ->where('created_at', '>=', now()->startOfWeek())
                        ->sum('amount');
                @endphp
                {{ $weeklyPurchases }}
            </div>
            <div class="stat-label">Weekly Purchases</div>
            <div class="stat-change">
                <i class="fas fa-arrow-up"></i>
                12% from last week
            </div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-period">All Time</div>
            </div>
            <div class="stat-value">
                @php
                    $totalExams = auth()->guard('client')->user()->examAttempts()->count();
                @endphp
                {{ $totalExams }}
            </div>
            <div class="stat-label">Exams Taken</div>
            <div class="stat-change">
                <i class="fas fa-arrow-up"></i>
                Great progress!
            </div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-period">Available</div>
            </div>
            <div class="stat-value">{{ auth()->guard('client')->user()->getCurrentTokens() }}</div>
            <div class="stat-label">Tokens Available</div>
            <div class="stat-change">
                <i class="fas fa-check"></i>
                Ready to use
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="actions-section">
        <div class="section-header">
            <h2 class="section-title">Quick Actions</h2>
        </div>
        <div class="actions-grid">
            <a href="{{ route('client.exams.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="action-title">Browse Exams</div>
                <div class="action-description">Explore available practice tests</div>
            </a>

            <a href="{{ route('client.subscriptions.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="action-title">Subscriptions</div>
                <div class="action-description">View premium plans & benefits</div>
            </a>

            <a href="{{ route('client.tokens.purchase') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="action-title">Buy Tokens</div>
                <div class="action-description">Get more tokens for exams</div>
            </a>

            <a href="{{ route('client.tokens.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="action-title">View History</div>
                <div class="action-description">Track your activity</div>
            </a>
        </div>
    </div>

    <!-- Two Column: Subscription + Activity -->
    <div class="two-column">
        <!-- Subscription Status -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Subscription Status</h3>
                <a href="{{ route('client.subscriptions.index') }}" class="panel-link">Manage →</a>
            </div>
            @if(auth()->guard('client')->user()->hasActiveSubscription())
                @php $activeSub = auth()->guard('client')->user()->activeSubscription; @endphp
                <div class="subscription-status">
                    <div class="status-item">
                        <span class="status-label">Plan</span>
                        <span class="status-value">{{ $activeSub->subscription->name }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Status</span>
                        <span class="status-badge">Active</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Tokens Included</span>
                        <span class="status-value">{{ $activeSub->subscription->formatted_tokens }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Expires</span>
                        <span class="status-value">{{ $activeSub->ends_at->format('M j, Y') }}</span>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-crown"></i>
                    <h3>No Active Subscription</h3>
                    <p>Upgrade to premium for unlimited access</p>
                    <a href="{{ route('client.subscriptions.index') }}" class="btn btn-primary">
                        <i class="fas fa-crown"></i>
                        Get Subscription
                    </a>
                </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Recent Activity</h3>
                <a href="{{ route('client.tokens.index') }}" class="panel-link">View All →</a>
            </div>
            @php
                $recentTransactions = auth()->guard('client')->user()->transactions()->latest()->take(5)->get();
            @endphp
            @if($recentTransactions->count() > 0)
                <div class="activity-list">
                    @foreach($recentTransactions as $tx)
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div class="activity-content">
                                <div class="activity-title">{{ ucfirst($tx->type) }}</div>
                                <div class="activity-time">{{ $tx->created_at->format('M j, H:i') }}</div>
                            </div>
                            <div class="activity-amount">{{ $tx->formatted_amount }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Recent Activity</h3>
                    <p>Your activity will appear here</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Resources Section -->
    <div class="resources-section">
        <div class="section-header">
            <h2 class="section-title">Available Resources</h2>
        </div>
        <div class="resources-grid">
            <div class="resource-card">
                <div class="resource-header">
                    <div class="resource-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="resource-title">Practice Tests</div>
                </div>
                <div class="resource-description">
                    Wide range of exams across subjects and difficulty levels to test your knowledge.
                </div>
                <a href="{{ route('client.exams.index') }}" class="resource-link">
                    Explore Tests <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="resource-card">
                <div class="resource-header">
                    <div class="resource-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="resource-title">Premium Plans</div>
                </div>
                <div class="resource-description">
                    Exclusive benefits and token packages with our subscription plans.
                </div>
                <a href="{{ route('client.subscriptions.index') }}" class="resource-link">
                    View Plans <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="resource-card">
                <div class="resource-header">
                    <div class="resource-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="resource-title">Token Packages</div>
                </div>
                <div class="resource-description">
                    Bulk token purchases with discounts and bonus tokens included.
                </div>
                <a href="{{ route('client.tokens.purchase') }}" class="resource-link">
                    Buy Tokens <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="resource-card">
                <div class="resource-header">
                    <div class="resource-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="resource-title">Progress Tracking</div>
                </div>
                <div class="resource-description">
                    Monitor your learning progress and track achievements over time.
                </div>
                <a href="{{ route('client.tokens.index') }}" class="resource-link">
                    View Progress <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection