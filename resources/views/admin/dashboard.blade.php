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
    margin-bottom: 2.5rem;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: var(--radius-xl);
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
}

.dashboard-header h1 {
    font-size: 2.25rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    position: relative;
    letter-spacing: -0.02em;
}

.dashboard-header p {
    font-size: 1rem;
    opacity: 0.9;
    font-weight: 500;
    position: relative;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--bg-white);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: var(--stat-color, var(--primary));
    background: linear-gradient(90deg, var(--stat-color, var(--primary)), var(--stat-color, var(--primary)));
}

.stat-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.stat-card:hover::after {
    opacity: 1;
}

.stat-card.primary::before { --stat-color: var(--primary); background: linear-gradient(90deg, #4f46e5, #7c3aed); }
.stat-card.success::before { --stat-color: var(--success); background: linear-gradient(90deg, #10b981, #059669); }
.stat-card.warning::before { --stat-color: var(--warning); background: linear-gradient(90deg, #f59e0b, #d97706); }
.stat-card.danger::before { --stat-color: var(--danger); background: linear-gradient(90deg, #ef4444, #dc2626); }
.stat-card.info::before { --stat-color: var(--info); background: linear-gradient(90deg, #3b82f6, #2563eb); }
.stat-card.purple::before { --stat-color: var(--purple); background: linear-gradient(90deg, #8b5cf6, #7c3aed); }
.stat-card.pink::before { --stat-color: var(--pink); background: linear-gradient(90deg, #ec4899, #db2777); }
.stat-card.teal::before { --stat-color: var(--teal); background: linear-gradient(90deg, #14b8a6, #0d9488); }
.stat-card.orange::before { --stat-color: var(--orange); background: linear-gradient(90deg, #f97316, #ea580c); }

.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.stat-card-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    background: var(--stat-bg, rgba(79, 70, 229, 0.15));
    color: var(--stat-color, var(--primary));
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-card:hover .stat-card-icon {
    transform: scale(1.1) rotate(5deg);
}

.stat-card.primary .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(79, 70, 229, 0.15), rgba(124, 58, 237, 0.15)); --stat-color: var(--primary); }
.stat-card.success .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); --stat-color: var(--success); }
.stat-card.warning .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.15)); --stat-color: var(--warning); }
.stat-card.danger .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15)); --stat-color: var(--danger); }
.stat-card.info .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.15)); --stat-color: var(--info); }
.stat-card.purple .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(124, 58, 237, 0.15)); --stat-color: var(--purple); }
.stat-card.pink .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(219, 39, 119, 0.15)); --stat-color: var(--pink); }
.stat-card.teal .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(20, 184, 166, 0.15), rgba(13, 148, 136, 0.15)); --stat-color: var(--teal); }
.stat-card.orange .stat-card-icon { --stat-bg: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.15)); --stat-color: var(--orange); }

.stat-card-label {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.stat-card-value {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--text-dark);
    line-height: 1.1;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--text-dark) 0%, var(--text-muted) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-card-subtext {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 500;
}

.stat-card-trend {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.4rem 0.75rem;
    border-radius: var(--radius-md);
    backdrop-filter: blur(10px);
}

.stat-card-trend.up {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05));
    color: var(--success);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.stat-card-trend.down {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
    color: var(--danger);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

/* Charts Section */
.charts-section {
    margin-bottom: 2.5rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 1.75rem;
}

.chart-card {
    background: var(--bg-white);
    border-radius: var(--radius-xl);
    padding: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.chart-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.chart-card.full-width {
    grid-column: 1 / -1;
}

.chart-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    padding-bottom: 1.25rem;
    border-bottom: 2px solid var(--border);
}

.chart-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.chart-card-title i {
    color: var(--primary);
    font-size: 1.25rem;
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
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.quick-action-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    text-decoration: none;
    color: var(--text-dark);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--action-color, var(--primary));
    background: linear-gradient(90deg, var(--action-color, var(--primary)), var(--action-color, var(--primary)));
}

.quick-action-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.quick-action-card.primary::before { --action-color: var(--primary); background: linear-gradient(90deg, #4f46e5, #7c3aed); }
.quick-action-card.success::before { --action-color: var(--success); background: linear-gradient(90deg, #10b981, #059669); }
.quick-action-card.warning::before { --action-color: var(--warning); background: linear-gradient(90deg, #f59e0b, #d97706); }
.quick-action-card.danger::before { --action-color: var(--danger); background: linear-gradient(90deg, #ef4444, #dc2626); }
.quick-action-card.info::before { --action-color: var(--info); background: linear-gradient(90deg, #3b82f6, #2563eb); }

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: var(--action-bg, rgba(79, 70, 229, 0.1));
    color: var(--action-color, var(--primary));
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.quick-action-card:hover .quick-action-icon {
    transform: scale(1.1) rotate(5deg);
}

.quick-action-card.primary .quick-action-icon { --action-bg: linear-gradient(135deg, rgba(79, 70, 229, 0.15), rgba(124, 58, 237, 0.15)); --action-color: var(--primary); }
.quick-action-card.success .quick-action-icon { --action-bg: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); --action-color: var(--success); }
.quick-action-card.warning .quick-action-icon { --action-bg: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.15)); --action-color: var(--warning); }
.quick-action-card.danger .quick-action-icon { --action-bg: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15)); --action-color: var(--danger); }
.quick-action-card.info .quick-action-icon { --action-bg: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.15)); --action-color: var(--info); }

.quick-action-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: var(--text-dark);
}

.quick-action-desc {
    font-size: 0.8rem;
    color: var(--text-muted);
}

/* Recent Activity */
.recent-activity {
    background: var(--bg-white);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
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

/* Section Titles */
.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: var(--primary);
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

    .dashboard-header {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-header h1 {
        font-size: 1.5rem;
    }

    .dashboard-header p {
        font-size: 0.875rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .stat-card {
        padding: 1.25rem;
    }

    .stat-card-value {
        font-size: 1.75rem;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }

    .charts-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
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

    .quick-action-card {
        padding: 1.25rem;
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }

    .recent-activity {
        padding: 1.25rem;
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

/* ESA editorial analytics theme */
.dashboard-container {
    max-width: 1540px;
    padding: 0;
}

.dashboard-header {
    min-height: 184px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    background:
        linear-gradient(110deg, rgba(23,33,28,0.96) 0%, rgba(23,33,28,0.82) 64%, rgba(22,107,77,0.55) 100%),
        radial-gradient(circle at 86% 20%, #d7e95b 0, transparent 32%);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 22px;
    box-shadow: 0 24px 64px rgba(23,33,28,0.14);
}

.dashboard-header::before {
    width: 220px;
    height: 220px;
    top: -90px;
    right: 30px;
    border: 1px solid rgba(215,233,91,0.28);
    border-radius: 50%;
    background: transparent;
}

.dashboard-header h1 {
    font-family: "Newsreader", Georgia, serif;
    font-size: clamp(2.15rem, 4vw, 3.8rem);
    line-height: 0.95;
    letter-spacing: -0.04em;
}

.dashboard-header p {
    max-width: 620px;
    color: #ccd7cf;
}

.stats-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}

.stat-card {
    min-height: 178px;
    background: #fff;
    border-radius: 16px;
    padding: 20px;
}

.stat-card::before {
    height: 3px;
    opacity: 0.9;
}

.stat-card-value {
    font-family: "Newsreader", Georgia, serif;
    font-size: clamp(1.75rem, 2.4vw, 2.35rem);
    background: none;
    -webkit-text-fill-color: initial;
    color: var(--text-dark);
}

.stat-card-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
}

.chart-card, .recent-activity {
    border-radius: 18px;
}

.chart-card-title, .recent-activity-title, .section-title {
    font-family: "Newsreader", Georgia, serif;
}

.quick-action-card {
    border-radius: 16px;
}

@media (max-width: 1100px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .dashboard-header {
        min-height: 156px;
        padding: 20px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        min-height: 150px;
    }

    .charts-grid {
        grid-template-columns: minmax(0, 1fr);
    }
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
        <div style="position:relative;z-index:1;">
            <div style="margin-bottom:.75rem;color:#d7e95b;font-size:.72rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;">
                Institution overview
            </div>
            <h1>Analytics Dashboard</h1>
            <p>Live financial, academic, and operational signals across your institution.</p>
        </div>
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
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('admin.report') }}" class="quick-action-card primary">
            <div class="quick-action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="quick-action-content">
                <h4 class="quick-action-title">View Reports</h4>
                <p class="quick-action-desc">Detailed financial reports</p>
            </div>
        </a>
        <a href="{{ route('admin.students.index') }}" class="quick-action-card success">
            <div class="quick-action-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="quick-action-content">
                <h4 class="quick-action-title">Manage Students</h4>
                <p class="quick-action-desc">Student enrollment & records</p>
            </div>
        </a>
        <a href="{{ route('admin.expenses.export') }}" class="quick-action-card warning">
            <div class="quick-action-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="quick-action-content">
                <h4 class="quick-action-title">Expenses</h4>
                <p class="quick-action-desc">Track operational costs</p>
            </div>
        </a>
        <a href="{{ route('admin.clients.index') }}" class="quick-action-card info">
            <div class="quick-action-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="quick-action-content">
                <h4 class="quick-action-title">Clients</h4>
                <p class="quick-action-desc">Client management</p>
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
    <div class="stat-card primary" style="margin-bottom: 2rem;">
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
    <div class="stat-card info" style="margin-bottom: 2rem;">
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
    <div class="stat-card warning" style="margin-bottom: 2rem;">
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

@endsection
