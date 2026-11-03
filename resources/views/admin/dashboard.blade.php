@extends('admin.layout')

@section('title', 'Admin Analytics Dashboard')

@push('styles')
<style>
:root {
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --secondary: #64748b;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --purple: #8b5cf6;
    --pink: #ec4899;
    --teal: #14b8a6;
    --orange: #f97316;
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: var(--bg-light);
    color: var(--text-dark);
    line-height: 1.6;
}

/* Dashboard Container */
.dashboard-container {
    padding: 1.5rem;
    max-width: 1600px;
    margin: 0 auto;
}

/* Dashboard Header */
.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.dashboard-header p {
    color: var(--text-muted);
    font-size: 0.875rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
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
    background: var(--stat-color, var(--primary));
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-card.primary::before { --stat-color: var(--primary); }
.stat-card.success::before { --stat-color: var(--success); }
.stat-card.warning::before { --stat-color: var(--warning); }
.stat-card.danger::before { --stat-color: var(--danger); }
.stat-card.info::before { --stat-color: var(--info); }
.stat-card.purple::before { --stat-color: var(--purple); }
.stat-card.pink::before { --stat-color: var(--pink); }
.stat-card.teal::before { --stat-color: var(--teal); }
.stat-card.orange::before { --stat-color: var(--orange); }

.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: var(--stat-bg, rgba(79, 70, 229, 0.1));
    color: var(--stat-color, var(--primary));
}

.stat-card.primary .stat-card-icon { --stat-bg: rgba(79, 70, 229, 0.1); --stat-color: var(--primary); }
.stat-card.success .stat-card-icon { --stat-bg: rgba(16, 185, 129, 0.1); --stat-color: var(--success); }
.stat-card.warning .stat-card-icon { --stat-bg: rgba(245, 158, 11, 0.1); --stat-color: var(--warning); }
.stat-card.danger .stat-card-icon { --stat-bg: rgba(239, 68, 68, 0.1); --stat-color: var(--danger); }
.stat-card.info .stat-card-icon { --stat-bg: rgba(59, 130, 246, 0.1); --stat-color: var(--info); }
.stat-card.purple .stat-card-icon { --stat-bg: rgba(139, 92, 246, 0.1); --stat-color: var(--purple); }
.stat-card.pink .stat-card-icon { --stat-bg: rgba(236, 72, 153, 0.1); --stat-color: var(--pink); }
.stat-card.teal .stat-card-icon { --stat-bg: rgba(20, 184, 166, 0.1); --stat-color: var(--teal); }
.stat-card.orange .stat-card-icon { --stat-bg: rgba(249, 115, 22, 0.1); --stat-color: var(--orange); }

.stat-card-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
}

.stat-card-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-dark);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-card-subtext {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.stat-card-trend {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
}

.stat-card-trend.up {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stat-card-trend.down {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

/* Charts Section */
.charts-section {
    margin-bottom: 2rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.chart-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
}

.chart-card.full-width {
    grid-column: 1 / -1;
}

.chart-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.chart-card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-card-title i {
    color: var(--primary);
}

.chart-card-actions {
    display: flex;
    gap: 0.5rem;
}

.chart-card-actions .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    border-radius: var(--radius-sm);
}

.chart-card-body {
    position: relative;
    min-height: 300px;
}

.chart-card-body canvas {
    max-height: 350px;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.quick-action-card {
    background: var(--bg-white);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    text-decoration: none;
    color: var(--text-dark);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}

.quick-action-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: var(--primary);
    color: white;
}

.quick-action-content h4 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.125rem;
}

.quick-action-content p {
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* Recent Activity */
.recent-activity {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
}

.recent-activity-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.recent-activity-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-dark);
}

.recent-activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.recent-activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: var(--radius-md);
    background: var(--bg-light);
    transition: all 0.2s ease;
}

.recent-activity-item:hover {
    background: rgba(79, 70, 229, 0.05);
}

.recent-activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    background: var(--primary);
    color: white;
    flex-shrink: 0;
}

.recent-activity-content {
    flex: 1;
    min-width: 0;
}

.recent-activity-content h5 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.125rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.recent-activity-content p {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.recent-activity-time {
    font-size: 0.75rem;
    color: var(--text-muted);
    white-space: nowrap;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: var(--radius-md);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: var(--secondary);
    color: white;
}

.btn-secondary:hover {
    background: #475569;
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text-dark);
}

.btn-outline:hover {
    background: var(--bg-light);
    border-color: var(--primary);
    color: var(--primary);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-container {
        padding: 1rem;
    }

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .charts-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .chart-card.full-width {
        grid-column: 1;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 0.75rem;
    }

    .dashboard-header h1 {
        font-size: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .stat-card {
        padding: 1.25rem;
    }

    .stat-card-value {
        font-size: 1.75rem;
    }

    .charts-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .chart-card {
        padding: 1.25rem;
    }

    .chart-card-body {
        min-height: 250px;
    }

    .chart-card-body canvas {
        max-height: 250px;
    }

    .quick-actions {
        grid-template-columns: 1fr;
    }

    .chart-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .chart-card-actions {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 480px) {
    .dashboard-container {
        padding: 0.5rem;
    }

    .dashboard-header h1 {
        font-size: 1.25rem;
    }

    .dashboard-header p {
        font-size: 0.75rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .stat-card-value {
        font-size: 1.5rem;
    }

    .stat-card-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }

    .chart-card {
        padding: 1rem;
    }

    .chart-card-header {
        padding-bottom: 0.75rem;
    }

    .chart-card-title {
        font-size: 1rem;
    }

    .recent-activity-item {
        padding: 0.5rem;
    }

    .recent-activity-icon {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
}

/* Loading Skeleton */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: var(--radius-sm);
}

@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-light);
}

::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: var(--radius-sm);
}

::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}
</style>
@endpush

@section('content')
<div class="dashboard-container">

    @if(isset($selectedAcademicYear) && $selectedAcademicYear)
    @include('partials.academic-year-banner')
    @endif

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Analytics Dashboard</h1>
        <p>Overview of your institution's performance metrics and key indicators</p>
    </div>

    <!-- Financial Stats -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-card-trend up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12.5%</span>
                </div>
            </div>
            <div class="stat-card-label">Total Revenue</div>
            <div class="stat-card-value">{{ number_format($totalRevenue) }}</div>
            <div class="stat-card-subtext">RWF</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-card-trend down">
                    <i class="fas fa-arrow-down"></i>
                    <span>-5.2%</span>
                </div>
            </div>
            <div class="stat-card-label">Total Expenses</div>
            <div class="stat-card-value">{{ number_format($totalExpenses) }}</div>
            <div class="stat-card-subtext">RWF</div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-card-trend up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8.3%</span>
                </div>
            </div>
            <div class="stat-card-label">Net Profit</div>
            <div class="stat-card-value">{{ number_format($netProfit) }}</div>
            <div class="stat-card-subtext">RWF</div>
        </div>

        <div class="stat-card info">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
            <div class="stat-card-label">Profit Margin</div>
            <div class="stat-card-value">{{ $profitMargin }}%</div>
            <div class="stat-card-subtext">Of total revenue</div>
        </div>
    </div>

    <!-- Revenue Sources -->
    <div class="stats-grid">
        <div class="stat-card warning">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <div class="stat-card-label">Token Revenue</div>
            <div class="stat-card-value">{{ number_format($tokenRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">This month: {{ number_format($monthlyTokenRevenue ?? 0) }} RWF</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-video"></i>
                </div>
            </div>
            <div class="stat-card-label">Live Class Revenue</div>
            <div class="stat-card-value">{{ number_format($liveClassRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">This month: {{ number_format($monthlyLiveClassRevenue ?? 0) }} RWF</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
            </div>
            <div class="stat-card-label">Resource Revenue</div>
            <div class="stat-card-value">{{ number_format($resourceRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">This month: {{ number_format($monthlyResourceRevenue ?? 0) }} RWF</div>
        </div>

        <div class="stat-card pink">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-crown"></i>
                </div>
            </div>
            <div class="stat-card-label">Subscription Revenue</div>
            <div class="stat-card-value">{{ number_format($subscriptionRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">This month: {{ number_format($monthlySubscriptionRevenue ?? 0) }} RWF</div>
        </div>
    </div>

    <!-- Additional Revenue -->
    <div class="stats-grid">
        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
            <div class="stat-card-label">Exam Revenue</div>
            <div class="stat-card-value">{{ number_format($examRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">From public exam sales</div>
        </div>

        <div class="stat-card primary">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <div class="stat-card-label">Registration Revenue</div>
            <div class="stat-card-value">{{ number_format($registrationRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">Student registration fees</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
            <div class="stat-card-label">Client Revenue</div>
            <div class="stat-card-value">{{ number_format($totalClientRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">From client token purchases</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <div class="stat-card-label">Total Expenses</div>
            <div class="stat-card-value">{{ number_format($totalExpenses ?? 0) }}</div>
            <div class="stat-card-subtext">Operational expenses</div>
        </div>
    </div>

    <!-- Client Stats -->
    <div class="stats-grid">
        <div class="stat-card warning">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card-label">Total Clients</div>
            <div class="stat-card-value">{{ number_format($totalClients ?? 0) }}</div>
            <div class="stat-card-subtext">{{ number_format($activeClients ?? 0) }} active, {{ number_format($inactiveClients ?? 0) }} inactive</div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <div class="stat-card-label">Client Tokens Sold</div>
            <div class="stat-card-value">{{ number_format($totalClientTokens ?? 0) }}</div>
            <div class="stat-card-subtext">Total tokens purchased</div>
        </div>

        <div class="stat-card primary">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-card-label">Client Revenue</div>
            <div class="stat-card-value">{{ number_format($totalClientRevenue ?? 0) }}</div>
            <div class="stat-card-subtext">From token purchases</div>
        </div>

        <div class="stat-card info">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-crown"></i>
                </div>
            </div>
            <div class="stat-card-label">Active Subscriptions</div>
            <div class="stat-card-value">{{ \App\Models\ClientSubscription::where('is_active', true)->count() }}</div>
            <div class="stat-card-subtext">Currently active</div>
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('admin.report') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="quick-action-content">
                <h4>View Reports</h4>
                <p>Detailed financial reports</p>
            </div>
        </a>
        <a href="{{ route('admin.students.index') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="quick-action-content">
                <h4>Manage Students</h4>
                <p>Student enrollment & records</p>
            </div>
        </a>
        <a href="{{ route('admin.expenses.export') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="quick-action-content">
                <h4>Expenses</h4>
                <p>Track operational costs</p>
            </div>
        </a>
        <a href="{{ route('admin.clients.index') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="quick-action-content">
                <h4>Clients</h4>
                <p>Client management</p>
            </div>
        </a>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="charts-grid">
            <!-- Revenue vs Expenses -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="fas fa-balance-scale"></i>
                        Revenue vs Expenses
                    </h3>
                    <div class="chart-card-actions">
                        <button class="btn btn-outline btn-sm">Export</button>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="revenueExpenseChart"></canvas>
                </div>
            </div>

            <!-- Students by Branch -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="fas fa-building"></i>
                        Students by Branch
                    </h3>
                    <div class="chart-card-actions">
                        <button class="btn btn-outline btn-sm">Details</button>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="studentsByBranchChart"></canvas>
                </div>
            </div>

            <!-- Fee Payment Status -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="fas fa-credit-card"></i>
                        Fee Payment Status
                    </h3>
                    <div class="chart-card-actions">
                        <button class="btn btn-outline btn-sm">View All</button>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="feesBarChart"></canvas>
                </div>
            </div>

            <!-- Students by Course in Branch -->
            <div class="chart-card full-width">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="fas fa-book"></i>
                        Students by Course in Branch
                    </h3>
                    <div class="chart-card-actions">
                        <button class="btn btn-outline btn-sm">Filter</button>
                        <button class="btn btn-outline btn-sm">Export</button>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="studentsByCourseInBranchChart"></canvas>
                </div>
            </div>

            <!-- Expenses by Branch -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="fas fa-chart-bar"></i>
                        Expenses by Branch
                    </h3>
                    <div class="chart-card-actions">
                        <button class="btn btn-outline btn-sm">Details</button>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="expensesByBranchChart"></canvas>
                </div>
            </div>

            <!-- Members Distribution -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="fas fa-users"></i>
                        Members Distribution
                    </h3>
                    <div class="chart-card-actions">
                        <button class="btn btn-outline btn-sm">View</button>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="membersPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards Quick Access -->
    <div class="stat-card primary">
        <div class="chart-card-header">
            <h3 class="chart-card-title">
                <i class="fas fa-file-alt"></i>
                Report Cards Management
            </h3>
            <a href="{{ route('admin.report-cards.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-right"></i> View All
            </a>
        </div>
        <div class="stat-card-value" style="font-size: 1.5rem; margin: 1rem 0;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ App\Models\ReportCard::count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Total Report Cards</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ App\Models\ReportCard::where('status', 'approved')->count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Approved</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">{{ App\Models\ReportCard::where('status', 'pending')->count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Pending</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--info);">{{ App\Models\SchoolInformation::count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">School Configured</div>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.report-cards.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Generate New
            </a>
            <a href="{{ route('admin.school-information.edit') }}" class="btn btn-info btn-sm">
                <i class="fas fa-cog"></i> Configure School
            </a>
        </div>
    </div>

    <!-- Blog Management -->
    <div class="stat-card info">
        <div class="chart-card-header">
            <h3 class="chart-card-title">
                <i class="fas fa-blog"></i>
                Blog Management
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-list"></i> All Blogs
                </a>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Create New
                </a>
            </div>
        </div>
        <div class="stat-card-value" style="font-size: 1.5rem; margin: 1rem 0;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ \App\Models\Blog::count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Total Blogs</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ \App\Models\Blog::where('status', 'published')->count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Published</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">{{ \App\Models\Blog::where('status', 'draft')->count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Drafts</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--secondary);">{{ \App\Models\Blog::where('status', 'archived')->count() }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Archived</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Management -->
    <div class="stat-card warning">
        <div class="chart-card-header">
            <h3 class="chart-card-title">
                <i class="fas fa-users"></i>
                Client Management
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-list"></i> All Clients
                </a>
                <a href="{{ route('admin.clients.index') }}?status=active" class="btn btn-success btn-sm">
                    <i class="fas fa-user-check"></i> Active
                </a>
            </div>
        </div>
        <div class="stat-card-value" style="font-size: 1.5rem; margin: 1rem 0;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">{{ $totalClients ?? 0 }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Total Clients</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ $activeClients ?? 0 }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Active</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--danger);">{{ $inactiveClients ?? 0 }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Inactive</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ number_format($totalClientTokens ?? 0) }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Tokens Sold</div>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper function to capitalize first letter
    function ucfirst(str) {
        if (!str) return str;
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }

    // Chart defaults for professional look
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.color = '#64748b';

    // Color schemes
    const primaryColors = ['#4f46e5', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

    // Gradient helper
    function createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    // Revenue vs Expenses Chart
    const revExpCtx = document.getElementById('revenueExpenseChart');
    if (revExpCtx) {
        new Chart(revExpCtx, {
            type: 'doughnut',
            data: {
                labels: ['Revenue', 'Expenses', 'Profit'],
                datasets: [{
                    data: [{{ $totalRevenue }}, {{ $totalExpenses }}, {{ $netProfit }}],
                    backgroundColor: ['#4f46e5', '#ef4444', '#10b981'],
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toLocaleString() + ' Rwf';
                            }
                        }
                    }
                }
            }
        });
    }

    // Students by Branch Chart
    const studentsByBranchCtx = document.getElementById('studentsByBranchChart');
    if (studentsByBranchCtx) {
        const studentsByBranchData = {!! json_encode($studentsByBranch) !!};
        new Chart(studentsByBranchCtx, {
            type: 'bar',
            data: {
                labels: studentsByBranchData.length > 0 ? studentsByBranchData.map(item => item.branch_name) : ['No Data'],
                datasets: [{
                    label: 'Total Students',
                    data: studentsByBranchData.length > 0 ? studentsByBranchData.map(item => item.total) : [0],
                    backgroundColor: createGradient(studentsByBranchCtx, '#667eea', '#764ba2'),
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Students: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Fee Payment Status Chart
    const feesCtx = document.getElementById('feesBarChart');
    if (feesCtx) {
        const feeData = {!! json_encode($feePaymentData) !!};
        const feeLabels = Object.keys(feeData).map(key => key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
        const feeAmounts = Object.values(feeData);
        const feeColors = ['#4f46e5', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        new Chart(feesCtx, {
            type: 'bar',
            data: {
                labels: feeLabels,
                datasets: [{
                    label: 'Amount (Rwf)',
                    data: feeAmounts,
                    backgroundColor: feeColors,
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' Rwf';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Students by Course in Branch Chart
    const studentsByCourseInBranchCtx = document.getElementById('studentsByCourseInBranchChart');
    if (studentsByCourseInBranchCtx) {
        const studentsByCourseInBranchData = {!! json_encode($studentsByCourseInBranch) !!};
        const branchGroups = {};
        const allCourses = new Set();

        if (studentsByCourseInBranchData && studentsByCourseInBranchData.length > 0) {
            studentsByCourseInBranchData.forEach(item => {
                const branchName = item.branch_name;
                const courseName = item.course_name;
                if (!branchGroups[branchName]) branchGroups[branchName] = {};
                branchGroups[branchName][courseName] = item.total;
                allCourses.add(courseName);
            });
        }

        const courseLabels = Array.from(allCourses);
        const branchNames = Object.keys(branchGroups);
        const colors = ['#4f46e5', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        const datasets = branchNames.map((branchName, index) => {
            const data = courseLabels.map(courseName => branchGroups[branchName][courseName] || 0);
            return {
                label: branchName,
                data: data,
                backgroundColor: colors[index % colors.length],
                borderWidth: 0,
                borderRadius: 8
            };
        });

        new Chart(studentsByCourseInBranchCtx, {
            type: 'bar',
            data: {
                labels: courseLabels.length > 0 ? courseLabels : ['No Data'],
                datasets: datasets.length > 0 ? datasets : [{ label: 'No Data', data: [0], backgroundColor: '#e2e8f0' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { padding: 15, font: { size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' students';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Expenses by Branch Chart
    const expBranchCtx = document.getElementById('expensesByBranchChart');
    if (expBranchCtx) {
        const expensesByBranchData = {!! json_encode($expensesByBranch) !!};
        new Chart(expBranchCtx, {
            type: 'bar',
            data: {
                labels: expensesByBranchData.length > 0 ? expensesByBranchData.map(item => item.branch) : ['No Data'],
                datasets: [{
                    label: 'Expenses (Rwf)',
                    data: expensesByBranchData.length > 0 ? expensesByBranchData.map(item => parseFloat(item.total)) : [0],
                    backgroundColor: createGradient(expBranchCtx, '#ef4444', '#dc2626'),
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' Rwf';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Members Distribution Chart
    const membersCtx = document.getElementById('membersPieChart');
    if (membersCtx) {
        new Chart(membersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Teachers', 'Staff'],
                datasets: [{
                    data: [{{ $totalStudents ?? 0 }}, {{ $totalTeachers ?? 0 }}, {{ $totalStaff ?? 0 }}],
                    backgroundColor: ['#4f46e5', '#10b981', '#3b82f6'],
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, font: { size: 12 } } }
                }
            }
        });
    }
});
</script>
@endsection        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Revenue vs Expenses</h6>
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="card-body">
                    <canvas id="revenueExpenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 7: Students by Branch & Students by Course in Branch -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Total Students by Branch</h6>
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-body">
                    <canvas id="studentsByBranchChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Students by Course in Branch</h6>
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-body">
                    <canvas id="studentsByCourseInBranchChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 7: NESA Registration & Student Approval Status -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">NESA Registration Status</h6>
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="card-body">
                    <canvas id="nesaRegistrationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Student Approval Status</h6>
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="card-body">
                    <canvas id="studentApprovalChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 8: Recent Students with NESA Status -->
    <div class="row mb-4">
        <div class="col-xl-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Students & NESA Registration Status</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Student Name</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>NESA Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents ?? [] as $student)
                                    <tr>
                                        <td><strong>{{ $student->reg_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->nesa_registered)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Registered
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$student->nesa_registered)
                                                <button class="btn btn-sm btn-primary" onclick="openNesaModal({{ $student->id }}, '{{ $student->full_name }}', '{{ $student->reg_number }}')">
                                                    <i class="fas fa-graduation-cap mr-1"></i>Register NESA
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-info" onclick="viewNesaDetails({{ $student->id }})">
                                                    <i class="fas fa-eye mr-1"></i>View Details
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Recent Expenses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExpenses->take(5) as $expense)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d') }}</td>
                                    <td>{{ ucfirst($expense->expense_type) }}</td>
                                    <td>{{ number_format($expense->amount) }}</td>
                                    <td>{{ $expense->branch->name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Recent Expenses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExpenses->take(5) as $expense)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d') }}</td>
                                    <td>{{ ucfirst($expense->expense_type) }}</td>
                                    <td>{{ number_format($expense->amount) }}</td>
                                    <td>{{ $expense->branch->name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper function to capitalize first letter (JavaScript equivalent of PHP ucfirst)
    function ucfirst(str) {
        if (!str) return str;
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }
    
    // Chart defaults for professional look
    Chart.defaults.font.family = "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.color = '#858796';

    // Color schemes
    const primaryColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    const gradientColors = {
        blue: ['#4e73df', '#224abe'],
        green: ['#1cc88a', '#17a673'],
        orange: ['#f6c23e', '#dda20a'],
        red: ['#e74a3b', '#be2617'],
        purple: ['#6f42c1', '#59359a'],
        teal: ['#36b9cc', '#2c9faf']
    };

    // Helper function to create gradient
    function createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    // 1. Members Pie Chart
    const membersCtx = document.getElementById('membersPieChart').getContext('2d');
    new Chart(membersCtx, {
        type: 'doughnut',
        data: {
            labels: ['Students', 'Teachers', 'Staff'],
            datasets: [{
                data: [
                    {{ $totalStudents ?? 0 }},
                    {{ $totalTeachers ?? 0 }},
                    {{ $totalStaff ?? 0 }}
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // 2. Students by Gender Chart
const genderCtx = document.getElementById('genderPieChart').getContext('2d');
const genderData = {!! json_encode($studentsByGender) !!};
const genderLabels = genderData.map(g => 
    g.sex === 'male' ? 'Male' : g.sex === 'female' ? 'Female' : 'Other'
);
const genderCounts = genderData.map(g => g.total);

    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#4e73df', '#e74a3b', '#f6c23e'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // 2. Fee Payments Bar Chart
    const feesCtx = document.getElementById('feesBarChart').getContext('2d');
    
    // Debug: Check if canvas exists
    if (!feesCtx) {
        console.error('Canvas element feesBarChart not found!');
        return;
    }
    
    const feeData = {!! json_encode($feePaymentData) !!};
    console.log('Fee payment data:', feeData);
    
    // Filter out zero values and prepare chart data
    const feeLabels = [];
    const feeAmounts = [];
    const feeColors = [];
    
    if (feeData.registration_fees > 0) {
        feeLabels.push('Registration Fees');
        feeAmounts.push(feeData.registration_fees);
        feeColors.push('#f6c23e');
    }
    
    if (feeData.school_fees > 0) {
        feeLabels.push('School Fees');
        feeAmounts.push(feeData.school_fees);
        feeColors.push('#1cc88a');
    }
    
    if (feeData.subscription_fees > 0) {
        feeLabels.push('Subscription Fees');
        feeAmounts.push(feeData.subscription_fees);
        feeColors.push('#4e73df');
    }
    
    if (feeData.student_card_fees > 0) {
        feeLabels.push('Student Card Fees');
        feeAmounts.push(feeData.student_card_fees);
        feeColors.push('#36b9cc');
    }
    
    if (feeData.exam_fees > 0) {
        feeLabels.push('Exam Fees');
        feeAmounts.push(feeData.exam_fees);
        feeColors.push('#e74a3b');
    }
    
    if (feeData.token_fees > 0) {
        feeLabels.push('Token Purchases');
        feeAmounts.push(feeData.token_fees);
        feeColors.push('#858796');
    }
    
    if (feeData.live_class_fees > 0) {
        feeLabels.push('Live Class Fees');
        feeAmounts.push(feeData.live_class_fees);
        feeColors.push('#5a5c69');
    }
    
    if (feeData.resource_fees > 0) {
        feeLabels.push('Resource Fees');
        feeAmounts.push(feeData.resource_fees);
        feeColors.push('#2e59d9');
    }
    
    // If no fee data, show "No Data"
    if (feeLabels.length === 0) {
        feeLabels.push('No Fee Data');
        feeAmounts.push(1);
        feeColors.push('#e9ecef');
    }
    
    new Chart(feesCtx, {
        type: 'bar',
        data: {
            labels: feeLabels,
            datasets: [{
                label: 'Amount (Rwf)',
                data: feeAmounts,
                backgroundColor: feeColors,
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' Rwf';
                        }
                    }
                },
                x: { 
                    grid: { display: false }
                }
            }
        }
    });

    // 3. Revenue vs Expenses Doughnut
    const revExpCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    new Chart(revExpCtx, {
        type: 'doughnut',
        data: {
            labels: ['Revenue', 'Expenses', 'Profit'],
            datasets: [{
                data: [
                    {{ $totalRevenue }},
                    {{ $totalExpenses }},
                    {{ $netProfit }}
                ],
                backgroundColor: ['#1cc88a', '#e74a3b', '#4e73df'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString() + ' Rwf';
                        }
                    }
                }
            }
        }
    });

    // 4. Expenses by Branch
    const expBranchCtx = document.getElementById('expensesByBranchChart').getContext('2d');
    const expensesByBranchData = {!! json_encode($expensesByBranch) !!};
    
    new Chart(expBranchCtx, {
        type: 'bar',
        data: {
            labels: expensesByBranchData.length > 0 ? expensesByBranchData.map(item => item.branch) : ['No Data'],
            datasets: [{
                label: 'Total Expenses (Rwf)',
                data: expensesByBranchData.length > 0 ? expensesByBranchData.map(item => parseFloat(item.total)) : [0],
                backgroundColor: createGradient(expBranchCtx, '#e74a3b', '#be2617'),
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                y: { grid: { display: false } }
            }
        }
    });

    // 5. Expenses by Type
    const expTypeCtx = document.getElementById('expensesByTypeChart').getContext('2d');
    const expensesByTypeData = {!! json_encode($expensesByType) !!};
    
    new Chart(expTypeCtx, {
        type: 'doughnut',
        data: {
            labels: expensesByTypeData.length > 0 ? expensesByTypeData.map(item => ucfirst(item.expense_type)) : ['No Data'],
            datasets: [{
                data: expensesByTypeData.length > 0 ? expensesByTypeData.map(item => parseFloat(item.total)) : [1],
                backgroundColor: expensesByTypeData.length > 0 ? primaryColors : ['#e9ecef'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, font: { size: 10 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString() + ' Rwf';
                        }
                    }
                }
            }
        }
    });

    // 6. Branch Financial Health
    const branchHealthCtx = document.getElementById('branchFinancialHealthChart').getContext('2d');
    const branchHealthData = {!! json_encode($branchFinancialHealth) !!};
    
    new Chart(branchHealthCtx, {
        type: 'bar',
        data: {
            labels: branchHealthData.length > 0 ? branchHealthData.map(item => item.name) : ['No Data'],
            datasets: [
                {
                    label: 'Revenue (Rwf)',
                    data: branchHealthData.length > 0 ? branchHealthData.map(item => parseFloat(item.revenue)) : [0],
                    backgroundColor: createGradient(branchHealthCtx, '#1cc88a', '#17a673'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Expenses (Rwf)',
                    data: branchHealthData.length > 0 ? branchHealthData.map(item => parseFloat(item.expenses)) : [0],
                    backgroundColor: createGradient(branchHealthCtx, '#e74a3b', '#be2617'),
                    borderWidth: 0,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 20, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 7. Expense Trend
    const trendCtx = document.getElementById('expenseTrendChart').getContext('2d');
    
    // Debug: Check if canvas exists
    if (!trendCtx) {
        console.error('Canvas element expenseTrendChart not found!');
        return;
    }
    
    const expenseTrendData = {!! json_encode($expenseTrend) !!};
    console.log('Expense trend data:', expenseTrendData);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: expenseTrendData.length > 0 ? expenseTrendData.map(item => item.month) : ['No Data'],
            datasets: [{
                label: 'Monthly Expenses (Rwf)',
                data: expenseTrendData.length > 0 ? expenseTrendData.map(item => parseFloat(item.amount)) : [0],
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f6c23e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { 
                        padding: 15, 
                        font: { size: 12 },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' Rwf';
                        }
                    }
                },
                x: { 
                    grid: { display: false }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // 8. Branch Tuition Chart
    const tuitionCtx = document.getElementById('branchTuitionChart').getContext('2d');
    const tuitionData = {!! json_encode($tuitionByBranch) !!};
    const tuitionPaidData = {!! json_encode($tuitionPaidByBranch) !!};
    
    new Chart(tuitionCtx, {
        type: 'bar',
        data: {
            labels: tuitionData.length > 0 ? tuitionData.map(item => item.branch) : ['No Data'],
            datasets: [
                {
                    label: 'Expected (Rwf)',
                    data: tuitionData.length > 0 ? tuitionData.map(item => parseFloat(item.expected)) : [0],
                    backgroundColor: createGradient(tuitionCtx, '#36b9cc', '#2c9faf'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Paid (Rwf)',
                    data: tuitionPaidData.length > 0 ? tuitionPaidData.map(item => parseFloat(item.paid ?? 0)) : [0],
                    backgroundColor: createGradient(tuitionCtx, '#1cc88a', '#17a673'),
                    borderWidth: 0,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 15, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 9. Students by Course and Gender
    const courseGenderCtx = document.getElementById('courseGenderChart').getContext('2d');
    
    // Debug: Check if canvas exists
    if (!courseGenderCtx) {
        console.error('Canvas element courseGenderChart not found!');
        return;
    }
    
    const courseGenderData = {!! json_encode($studentsByCourseGender) !!};
    console.log('Raw course gender data:', courseGenderData);
    
    // Simple and robust data processing
    let chartData = {
        labels: [],
        maleData: [],
        femaleData: []
    };
    
    if (courseGenderData && courseGenderData.length > 0) {
        // Group by course
        const courseGroups = {};
        
        courseGenderData.forEach(item => {
            const courseName = item.course?.name || 'Unknown Course';
            if (!courseGroups[courseName]) {
                courseGroups[courseName] = { male: 0, female: 0 };
            }
            
            if (item.sex === 'M') {
                courseGroups[courseName].male = item.total || 0;
            } else if (item.sex === 'F') {
                courseGroups[courseName].female = item.total || 0;
            }
        });
        
        // Convert to chart arrays
        Object.keys(courseGroups).forEach(courseName => {
            chartData.labels.push(courseName);
            chartData.maleData.push(courseGroups[courseName].male);
            chartData.femaleData.push(courseGroups[courseName].female);
        });
    }
    
    new Chart(courseGenderCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels.length > 0 ? chartData.labels : ['No Data'],
            datasets: [
                {
                    label: 'Male',
                    data: chartData.maleData.length > 0 ? chartData.maleData : [0],
                    backgroundColor: createGradient(courseGenderCtx, '#4e73df', '#2e59d9'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Female',
                    data: chartData.femaleData.length > 0 ? chartData.femaleData : [0],
                    backgroundColor: createGradient(courseGenderCtx, '#e74a3b', '#be2617'),
                    borderWidth: 0,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 15, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' students';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 10. Students by Branch Chart
    const studentsByBranchCtx = document.getElementById('studentsByBranchChart').getContext('2d');
    const studentsByBranchData = {!! json_encode($studentsByBranch) !!};
    
    new Chart(studentsByBranchCtx, {
        type: 'bar',
        data: {
            labels: studentsByBranchData.length > 0 ? studentsByBranchData.map(item => item.branch_name) : ['No Data'],
            datasets: [{
                label: 'Total Students',
                data: studentsByBranchData.length > 0 ? studentsByBranchData.map(item => item.total) : [0],
                backgroundColor: createGradient(studentsByBranchCtx, '#667eea', '#764ba2'),
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Students: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 11. Students by Course in Branch Chart
    const studentsByCourseInBranchCtx = document.getElementById('studentsByCourseInBranchChart').getContext('2d');
    const studentsByCourseInBranchData = {!! json_encode($studentsByCourseInBranch) !!};
    
    // Group data by branch
    const branchGroups = {};
    const allCourses = new Set();
    
    if (studentsByCourseInBranchData && studentsByCourseInBranchData.length > 0) {
        studentsByCourseInBranchData.forEach(item => {
            const branchName = item.branch_name;
            const courseName = item.course_name;
            
            if (!branchGroups[branchName]) {
                branchGroups[branchName] = {};
            }
            branchGroups[branchName][courseName] = item.total;
            allCourses.add(courseName);
        });
    }
    
    const courseLabels = Array.from(allCourses);
    const branchNames = Object.keys(branchGroups);
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    
    const datasets = branchNames.map((branchName, index) => {
        const data = courseLabels.map(courseName => branchGroups[branchName][courseName] || 0);
        return {
            label: branchName,
            data: data,
            backgroundColor: colors[index % colors.length],
            borderWidth: 0,
            borderRadius: 8
        };
    });
    
    new Chart(studentsByCourseInBranchCtx, {
        type: 'bar',
        data: {
            labels: courseLabels.length > 0 ? courseLabels : ['No Data'],
            datasets: datasets.length > 0 ? datasets : [{
                label: 'No Data',
                data: [0],
                backgroundColor: '#e9ecef'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 15, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' students';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 12. NESA Registration Status Chart
const nesaCtx = document.getElementById('nesaRegistrationChart').getContext('2d');
new Chart(nesaCtx, {
    type: 'doughnut',
    data: {
        labels: ['Registered for NESA', 'Not Registered'],
        datasets: [{
            data: [
                {{ $nesaRegistered ?? 0 }},
                {{ $nesaNotRegistered ?? 0 }}
            ],
            backgroundColor: ['#1cc88a', '#e74a3b'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 11 } }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                        return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// 8. Student Approval Status Chart  
const approvalCtx = document.getElementById('studentApprovalChart').getContext('2d');
new Chart(approvalCtx, {
    type: 'doughnut',
    data: {
        labels: ['Approved (Registration Fees Paid)', 'Not Approved'],
        datasets: [{
            data: [
                {{ $approvedStudents ?? 0 }},
                {{ $notApprovedStudents ?? 0 }}
            ],
            backgroundColor: ['#4e73df', '#f6c23e'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 11 } }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                        return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// NESA Registration Functions
function openNesaModal(studentId, studentName, regNumber) {
    Swal.fire({
        title: 'NESA Registration',
        html: `
            <div style="text-align: left;">
                <p><strong>Student:</strong> ${studentName}</p>
                <p><strong>Reg Number:</strong> ${regNumber}</p>
                <div class="form-group">
                    <label>SDMS Code:</label>
                    <input type="text" id="sdmsCode" class="swal2-input" placeholder="Enter NESA SDMS code">
                </div>
                <div class="form-group">
                    <label>NESA Registration Number:</label>
                    <input type="text" id="nesaRegNumber" class="swal2-input" placeholder="Enter NESA registration number">
                </div>
                <div class="form-group">
                    <label>Remarks (optional):</label>
                    <textarea id="remarks" class="swal2-input" placeholder="Additional remarks"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Register for NESA',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const sdmsCode = document.getElementById('sdmsCode').value;
            const nesaRegNumber = document.getElementById('nesaRegNumber').value;
            const remarks = document.getElementById('remarks').value;
            
            if (!sdmsCode || !nesaRegNumber) {
                Swal.showValidationMessage('Please enter both SDMS code and NESA registration number');
                return false;
            }
            
            return { sdmsCode, nesaRegNumber, remarks };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit NESA registration
            fetch(`/admin/nesa/${studentId}/mark-registered`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'Student registered for NESA successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Error!', data.message || 'Registration failed', 'error');
                    console.error('NESA Registration Error:', data);
                }
            })
            .catch(error => {
                console.error('NESA Registration Fetch Error:', error);
                Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
            });
        }
    });
}

function viewNesaDetails(studentId) {
    window.open(`/admin/nesa/${studentId}/edit`, '_blank');
}
</script>

@endsection