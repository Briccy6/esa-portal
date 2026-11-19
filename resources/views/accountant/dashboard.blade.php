@extends('accountant.layout')

@section('title', 'Accountant Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="dashboard-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="dashboard-eyebrow">FINANCE OVERVIEW</span>
            <h1 class="h3 mb-1">Accountant dashboard</h1>
            <p class="text-muted mb-0">A clear view of fee collection, expenses, and balances.</p>
        </div>
        <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-primary">
            <i class="fas fa-book-open me-2"></i>View Expenses
        </a>
    </div>

    <!-- Filters Section -->
    <div class="card dashboard-filter mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.dashboard') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small text-muted">Academic Year</label>
                    <select name="academic_year_id" class="form-select form-select-sm">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $filters['academic_year_id'] == $year->id ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Branch</label>
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $filters['branch_id'] == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Term</label>
                    <select name="term_id" class="form-select form-select-sm">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ $filters['term_id'] == $term->id ? 'selected' : '' }}>
                                {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary h-100" data-link="{{ route('staff.accountant.payment-schedule.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Total Fees Expected</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['total_fees_expected'] }}">
                        {{ number_format($kpiData['total_fees_expected'], 0) }}
                    </div>
                    <div class="stat-card-trend">
                        @php
                            $prev = $kpiData['previous_period']['fees_collected'] ?? 0;
                            $current = $kpiData['fees_collected'] ?? 0;
                            $trend = $prev > 0 ? (($current - $prev) / $prev) * 100 : 0;
                        @endphp
                        @if($trend >= 0)
                            <span class="text-success"><i class="fas fa-arrow-up"></i> {{ number_format($trend, 1) }}%</span>
                        @else
                            <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ number_format(abs($trend), 1) }}%</span>
                        @endif
                        <span class="text-muted small">vs prev period</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success h-100" data-link="{{ route('staff.accountant.payment-schedule.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Fees Collected</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['fees_collected'] }}">
                        {{ number_format($kpiData['fees_collected'], 0) }}
                    </div>
                    <div class="stat-card-trend">
                        <span class="text-success small">RWF</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning h-100" data-link="{{ route('staff.accountant.payment-schedule.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Outstanding Balance</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['outstanding_balance'] }}">
                        {{ number_format($kpiData['outstanding_balance'], 0) }}
                    </div>
                    <div class="stat-card-trend">
                        <span class="text-warning small">RWF</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info h-100" data-link="{{ route('staff.accountant.payment-schedule.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Collection Rate</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['collection_rate'] }}">
                        {{ number_format($kpiData['collection_rate'], 1) }}%
                    </div>
                    <div class="stat-card-trend">
                        @if($kpiData['collection_rate'] >= 80)
                            <span class="badge bg-success">Excellent</span>
                        @elseif($kpiData['collection_rate'] >= 50)
                            <span class="badge bg-warning">Good</span>
                        @else
                            <span class="badge bg-danger">Needs Attention</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-danger h-100" data-link="{{ route('staff.accountant.expenses.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Total Expenses</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['total_expenses'] }}">
                        {{ number_format($kpiData['total_expenses'], 0) }}
                    </div>
                    <div class="stat-card-trend">
                        @php
                            $prev = $kpiData['previous_period']['expenses'] ?? 0;
                            $current = $kpiData['total_expenses'] ?? 0;
                            $trend = $prev > 0 ? (($current - $prev) / $prev) * 100 : 0;
                        @endphp
                        @if($trend <= 0)
                            <span class="text-success"><i class="fas fa-arrow-down"></i> {{ number_format(abs($trend), 1) }}%</span>
                        @else
                            <span class="text-danger"><i class="fas fa-arrow-up"></i> {{ number_format($trend, 1) }}%</span>
                        @endif
                        <span class="text-muted small">vs prev period</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-secondary h-100">
                <div class="stat-card-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Net Balance</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['net_balance'] }}">
                        {{ number_format($kpiData['net_balance'], 0) }}
                    </div>
                    <div class="stat-card-trend">
                        <span class="text-muted small">RWF</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary h-100" data-link="{{ route('staff.accountant.payment-schedule.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Payments Today</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['payments_today'] }}">
                        {{ number_format($kpiData['payments_today'], 0) }}
                    </div>
                    <div class="stat-card-trend">
                        <span class="text-muted small">RWF</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning h-100" data-link="{{ route('staff.accountant.payment-schedule.index') }}">
                <div class="stat-card-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Students With Arrears</div>
                    <div class="stat-card-value tabular-nums count-up" data-value="{{ $kpiData['students_with_arrears'] }}">
                        {{ $kpiData['students_with_arrears'] }}
                    </div>
                    <div class="stat-card-trend">
                        <span class="text-muted small">of {{ $kpiData['total_students'] }} students</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-card h-100">
                <div class="chart-card-header d-flex justify-content-between align-items-center">
                    <h6 class="chart-card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Fees Collected vs Expenses by Month
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container">
                        <canvas id="feesVsExpensesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h6 class="chart-card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Fee Status Distribution
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container chart-container-doughnut">
                        <canvas id="feeStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h6 class="chart-card-title mb-0">
                        <i class="fas fa-building me-2"></i>Collections by Branch
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container">
                        <canvas id="collectionsByBranchChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h6 class="chart-card-title mb-0">
                        <i class="fas fa-tags me-2"></i>Expenses by Category
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container">
                        <canvas id="expensesByCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h6 class="chart-card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>Daily Collections (Last 30 Days)
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container">
                        <canvas id="dailyCollectionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Payments</h6>
                    <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $payment['student_name'] }}</div>
                                        <small class="text-muted">{{ $payment['student_reg_number'] }}</small>
                                    </td>
                                    <td class="fw-bold text-success">{{ number_format($payment['amount'], 0) }}</td>
                                    <td><small>{{ $payment['date'] }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No recent payments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Top Outstanding Balances</h6>
                    <a href="{{ route('staff.accountant.payment-schedule.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Outstanding</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topOutstanding as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item['student']->full_name }}</div>
                                        <small class="text-muted">{{ $item['student']->reg_number }}</small>
                                    </td>
                                    <td class="fw-bold text-danger">{{ number_format($item['outstanding'], 0) }}</td>
                                    <td>
                                        @php
                                            $progress = $item['expected'] > 0 ? ($item['paid'] / $item['expected']) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar @if($progress >= 100) bg-success @elseif($progress >= 50) bg-warning @else bg-danger @endif" 
                                                 style="width: {{ min($progress, 100) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No outstanding balances</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Recent Expenses</h6>
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $expense)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $expense['type'] }}</div>
                                        <small class="text-muted">{{ $expense['paid_to'] }}</small>
                                    </td>
                                    <td class="fw-bold text-danger">{{ number_format($expense['amount'], 0) }}</td>
                                    <td><small>{{ $expense['date'] }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No recent expenses</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2 col-4">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addExpenseModal" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                        <span>Add Expense</span>
                    </a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="{{ route('staff.students.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                        <i class="fas fa-money-bill fa-2x mb-2"></i>
                        <span>Record Payment</span>
                    </a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                        <i class="fas fa-print fa-2x mb-2"></i>
                        <span>Print Receipt</span>
                    </a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                        <span>Fee Report</span>
                    </a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="{{ route('staff.accountant.payment-schedule.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                        <i class="fas fa-users-slash fa-2x mb-2"></i>
                        <span>View Defaulters</span>
                    </a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                        <i class="fas fa-balance-scale fa-2x mb-2"></i>
                        <span>Balance Sheet</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Chart data from server
const chartData = @json($chartData);

// Fees vs Expenses Chart
const feesVsExpensesCtx = document.getElementById('feesVsExpensesChart');
if (feesVsExpensesCtx) {
    new Chart(feesVsExpensesCtx, {
        type: 'line',
        data: {
            labels: chartData.fees_vs_expenses_by_month.map(d => d.month),
            datasets: [{
                label: 'Fees Collected',
                data: chartData.fees_vs_expenses_by_month.map(d => d.fees),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Expenses',
                data: chartData.fees_vs_expenses_by_month.map(d => d.expenses),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Fee Status Chart
const feeStatusCtx = document.getElementById('feeStatusChart');
if (feeStatusCtx) {
    new Chart(feeStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Partial', 'Unpaid'],
            datasets: [{
                data: [chartData.fee_status.paid, chartData.fee_status.partial, chartData.fee_status.unpaid],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// Collections by Branch Chart
const collectionsByBranchCtx = document.getElementById('collectionsByBranchChart');
if (collectionsByBranchCtx) {
    new Chart(collectionsByBranchCtx, {
        type: 'bar',
        data: {
            labels: chartData.collections_by_branch.map(d => d.branch),
            datasets: [{
                label: 'Collections (RWF)',
                data: chartData.collections_by_branch.map(d => d.amount),
                backgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Expenses by Category Chart
const expensesByCategoryCtx = document.getElementById('expensesByCategoryChart');
if (expensesByCategoryCtx) {
    new Chart(expensesByCategoryCtx, {
        type: 'bar',
        data: {
            labels: chartData.expenses_by_category.map(d => d.category),
            datasets: [{
                label: 'Expenses (RWF)',
                data: chartData.expenses_by_category.map(d => d.amount),
                backgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
}

// Daily Collections Chart
const dailyCollectionsCtx = document.getElementById('dailyCollectionsChart');
if (dailyCollectionsCtx) {
    new Chart(dailyCollectionsCtx, {
        type: 'line',
        data: {
            labels: chartData.daily_collections.map(d => d.date),
            datasets: [{
                label: 'Collections (RWF)',
                data: chartData.daily_collections.map(d => d.amount),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Count-up animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.count-up');
    
    counters.forEach(counter => {
        const target = parseFloat(counter.dataset.value);
        const duration = 1500;
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = Math.floor(target).toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, duration / steps);
    });

    // Staggered fade-in for cards
    const cards = document.querySelectorAll('.stat-card, .chart-card, .card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });

    // Clickable stat cards
    document.querySelectorAll('.stat-card[data-link]').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            window.location.href = this.dataset.link;
        });
    });
});
</script>

<style>
.tabular-nums {
    font-variant-numeric: tabular-nums;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    border: 1px solid #e6ebf2;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
}

.stat-card-primary::before { background: linear-gradient(180deg, #1d4ed8, #2563eb); }
.stat-card-success::before { background: linear-gradient(180deg, #059669, #10b981); }
.stat-card-warning::before { background: linear-gradient(180deg, #d97706, #f59e0b); }
.stat-card-danger::before { background: linear-gradient(180deg, #dc2626, #ef4444); }
.stat-card-info::before { background: linear-gradient(180deg, #0891b2, #06b6d4); }
.stat-card-secondary::before { background: linear-gradient(180deg, #4b5563, #6b7280); }

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-card-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-card-primary .stat-card-icon { background: linear-gradient(135deg, #1d4ed8, #2563eb); color: white; }
.stat-card-success .stat-card-icon { background: linear-gradient(135deg, #059669, #10b981); color: white; }
.stat-card-warning .stat-card-icon { background: linear-gradient(135deg, #d97706, #f59e0b); color: white; }
.stat-card-danger .stat-card-icon { background: linear-gradient(135deg, #dc2626, #ef4444); color: white; }
.stat-card-info .stat-card-icon { background: linear-gradient(135deg, #0891b2, #06b6d4); color: white; }
.stat-card-secondary .stat-card-icon { background: linear-gradient(135deg, #4b5563, #6b7280); color: white; }

.stat-card-content { flex: 1; }

.stat-card-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.stat-card-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
}

.stat-card-trend {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.chart-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid #e6ebf2;
    overflow: hidden;
}

.chart-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e6ebf2;
    background: #ffffff;
}

.chart-card-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1f2937;
}

.chart-card-body {
    padding: 1.25rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

.chart-container-doughnut {
    height: 350px;
}

.dashboard-heading {
    padding: 0.5rem 0 0.25rem;
}

.dashboard-heading h1 {
    color: #10233f;
    font-weight: 700;
    letter-spacing: -0.03em;
}

.dashboard-eyebrow {
    display: inline-block;
    margin-bottom: 0.4rem;
    color: #2563eb;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.12em;
}

.dashboard-filter {
    border: 1px solid #e6ebf2;
    border-radius: 14px;
    box-shadow: 0 5px 20px rgba(16, 35, 63, 0.05);
}

.stat-card[data-link]:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.35);
    outline-offset: 3px;
}

@media (max-width: 768px) {
    .stat-card-value { font-size: 1.5rem; }
    .chart-container { height: 250px; }
    .chart-container-doughnut { height: 300px; }
    .dashboard-heading .btn { width: 100%; }
}
</style>
@endsection
