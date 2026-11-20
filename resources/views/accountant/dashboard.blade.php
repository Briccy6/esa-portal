@extends('staff.partials.sidebar_sec')

@section('title', 'Finance Dashboard')

@include('accountant.partials.theme')

@push('styles')
<style>
    .accountant-dashboard .kpi-card { transition: transform .2s ease, box-shadow .2s ease; }
    .accountant-dashboard .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(15, 23, 42, .1); }
    .accountant-dashboard .chart-wrap { position: relative; height: clamp(250px, 34vh, 360px); }
    .accountant-dashboard .chart-empty { position: absolute; inset: 0; display: grid; place-items: center; color: #64748b; text-align: center; padding: 1rem; }
    .accountant-dashboard .dashboard-watermark {
        position: fixed;
        z-index: 5;
        top: 28%;
        right: 3%;
        width: min(32vw, 360px);
        max-height: 45vh;
        object-fit: contain;
        opacity: .045;
        pointer-events: none;
    }
    @media (max-width: 575.98px) {
        .accountant-dashboard .chart-wrap { height: 260px; }
        .accountant-dashboard .dashboard-watermark { width: 52vw; right: -10%; top: 35%; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 accountant-dashboard accountant-module">
    <img class="dashboard-watermark" src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="" aria-hidden="true">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Finance overview</p>
            <h1 class="h3 fw-bold mb-1">Finance Dashboard</h1>
            <p class="text-muted mb-0">Monitor fee collection, outstanding balances, and expenses.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2" aria-hidden="true"></i>Add Expense
            </a>
            <a href="{{ route('staff.accountant.payment-schedule.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar-check me-2" aria-hidden="true"></i>Payment Schedule
            </a>
            <a href="{{ route('staff.accounting.cashbook') }}" class="btn btn-outline-secondary">Cashbook</a>
            <a href="{{ route('staff.accounting.balance-sheet') }}" class="btn btn-outline-secondary">Balance Sheet</a>
        </div>
    </div>

    <div class="alert alert-danger d-none" id="dashboardError" role="alert"></div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header py-3">
            <h2 class="h6 fw-semibold mb-0"><i class="fas fa-sliders me-2 text-primary" aria-hidden="true"></i>Filter overview</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.dashboard') }}" id="dashboardFilters" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-xl">
                    <label for="academic_year_id" class="form-label small text-muted">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(($filters['academic_year_id'] ?? '') == $year->id)>{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-xl">
                    <label for="branch_id" class="form-label small text-muted">Branch</label>
                    <select id="branch_id" name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(($filters['branch_id'] ?? '') == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-xl">
                    <label for="term_id" class="form-label small text-muted">Term</label>
                    <select id="term_id" name="term_id" class="form-select">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" @selected(($filters['term_id'] ?? '') == $term->id)>{{ $term->name }}{{ $term->academicYear ? ' - ' . $term->academicYear->year : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-xl">
                    <label for="start_date" class="form-label small text-muted">Start Date</label>
                    <input id="start_date" type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-12 col-sm-6 col-xl">
                    <label for="end_date" class="form-label small text-muted">End Date</label>
                    <input id="end_date" type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-12 col-sm-6 col-xl-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" id="filterButton">
                        <i class="fas fa-filter me-1" aria-hidden="true"></i>Apply
                    </button>
                    <a href="{{ route('staff.accountant.dashboard') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 g-xl-4 mb-4">
        @php
            $kpis = [
                ['key' => 'total_fees_expected', 'label' => 'Total Fees Expected', 'icon' => 'fa-coins', 'tone' => 'primary'],
                ['key' => 'fees_collected', 'label' => 'Fees Collected', 'icon' => 'fa-circle-check', 'tone' => 'success'],
                ['key' => 'outstanding_balance', 'label' => 'Outstanding Balance', 'icon' => 'fa-triangle-exclamation', 'tone' => 'warning'],
                ['key' => 'total_expenses', 'label' => 'Total Expenses', 'icon' => 'fa-receipt', 'tone' => 'danger'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <p class="text-muted small mb-2">{{ $kpi['label'] }}</p>
                                <div class="h3 fw-bold mb-2" id="kpi-{{ $kpi['key'] }}">{{ number_format($kpiData[$kpi['key']] ?? 0, 0) }}</div>
                                @if($kpi['key'] === 'total_fees_expected' && ($kpiData[$kpi['key']] ?? 0) == 0)
                                    <small class="text-warning">No fee data is available for these filters.</small>
                                @elseif($kpi['key'] === 'outstanding_balance' && ($kpiData[$kpi['key']] ?? 0) == 0 && ($kpiData['total_fees_expected'] ?? 0) > 0)
                                    <small class="text-success">All expected fees are paid.</small>
                                @else
                                    <small class="text-muted">Updated for the selected filters</small>
                                @endif
                            </div>
                            <span class="rounded-3 bg-{{ $kpi['tone'] }} bg-opacity-10 text-{{ $kpi['tone'] }} p-3">
                                <i class="fas {{ $kpi['icon'] }} fa-lg" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h2 class="h6 fw-semibold mb-1">Revenue Trend</h2>
                        <p class="text-muted small mb-0">Daily collections over the last 30 days</p>
                    </div>
                    <span class="text-primary"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                </div>
                <div class="card-body"><div class="chart-wrap"><canvas id="revenueChart" aria-label="Daily fee collections chart"></canvas><div class="chart-empty d-none" id="revenueChartEmpty">No collection data is available for this selection.</div></div></div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h2 class="h6 fw-semibold mb-1">Expense Distribution</h2>
                        <p class="text-muted small mb-0">Expenses grouped by category</p>
                    </div>
                    <span class="text-danger"><i class="fas fa-chart-pie" aria-hidden="true"></i></span>
                </div>
                <div class="card-body"><div class="chart-wrap"><canvas id="expenseChart" aria-label="Expense categories chart"></canvas><div class="chart-empty d-none" id="expenseChartEmpty">No expense data is available for this selection.</div></div></div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-xl-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 fw-semibold mb-0">Recent Payments</h2>
                    <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-sm btn-outline-primary">View receipts</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Student</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td><div class="fw-semibold">{{ $payment['student_name'] ?? 'N/A' }}</div><small class="text-muted">{{ $payment['student_reg_number'] ?? '' }}</small></td>
                                    <td class="fw-semibold text-success text-nowrap">{{ number_format($payment['amount'] ?? 0, 0) }} RWF</td>
                                    <td class="text-nowrap">{{ $payment['date'] ?? 'N/A' }}</td>
                                    <td><span class="badge text-bg-success">{{ ucfirst($payment['status'] ?? 'paid') }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-5"><i class="fas fa-receipt fa-2x d-block mb-2" aria-hidden="true"></i>No recent payments for these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 fw-semibold mb-0">Recent Expenses</h2>
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Expense</th><th>Branch</th><th>Date</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @forelse($recentExpenses as $expense)
                                <tr>
                                    <td><div class="fw-semibold">{{ $expense['type'] ?? 'Expense' }}</div><small class="text-muted">{{ $expense['description'] ?? '' }}</small></td>
                                    <td><small class="text-muted">{{ $expense['branch'] ?? 'N/A' }}</small></td>
                                    <td class="text-nowrap">{{ $expense['date'] ?? 'N/A' }}</td>
                                    <td class="fw-semibold text-danger text-end text-nowrap">{{ number_format($expense['amount'] ?? 0, 0) }} RWF</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-5"><i class="fas fa-file-invoice-dollar fa-2x d-block mb-2" aria-hidden="true"></i>No recent expenses for these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Statistics Cards -->
    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <p class="text-muted small mb-2">Fully Paid Students</p>
                            <div class="h3 fw-bold mb-2">{{ number_format($fullyPaidStudents ?? 0) }}</div>
                            <small class="text-success">Students with successful payments</small>
                        </div>
                        <span class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                            <i class="fas fa-check-circle fa-lg" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <p class="text-muted small mb-2">No Payment Students</p>
                            <div class="h3 fw-bold mb-2">{{ number_format($studentsWithNoPayment ?? 0) }}</div>
                            <small class="text-danger">Students with no fee payments</small>
                        </div>
                        <span class="rounded-3 bg-danger bg-opacity-10 text-danger p-3">
                            <i class="fas fa-times-circle fa-lg" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <p class="text-muted small mb-2">Profile Completion</p>
                            <div class="h3 fw-bold mb-2">{{ number_format($studentsWithCompletedProfileByBranch->sum('total') ?? 0) }}</div>
                            <small class="text-info">Students with complete profiles</small>
                        </div>
                        <span class="rounded-3 bg-info bg-opacity-10 text-info p-3">
                            <i class="fas fa-user-check fa-lg" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <p class="text-muted small mb-2">Payment Analysis</p>
                            <div class="h3 fw-bold mb-2">Custom</div>
                            <small class="text-warning">Filter by payment percentage</small>
                        </div>
                        <span class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                            <i class="fas fa-percentage fa-lg" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students by Shift in Branch -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Students by Shift in Each Branch</h2>
            <span class="text-primary"><i class="fas fa-clock" aria-hidden="true"></i></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Day</th>
                            <th>Evening</th>
                            <th>Weekend</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $shiftData = [];
                            foreach($studentsByShiftInBranch as $item) {
                                if (!isset($shiftData[$item->branch_name])) {
                                    $shiftData[$item->branch_name] = ['day' => 0, 'evening' => 0, 'weekend' => 0];
                                }
                                $shiftData[$item->branch_name][$item->shift] = $item->total;
                            }
                        @endphp
                        @foreach($shiftData as $branch => $shifts)
                            <tr>
                                <td><strong>{{ $branch }}</strong></td>
                                <td>{{ number_format($shifts['day']) }}</td>
                                <td>{{ number_format($shifts['evening']) }}</td>
                                <td>{{ number_format($shifts['weekend']) }}</td>
                                <td><strong>{{ number_format(array_sum($shifts)) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Profile Completion by Course and Shift -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Profile Completion by Course and Shift</h2>
            <span class="text-success"><i class="fas fa-user-check" aria-hidden="true"></i></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Shift</th>
                            <th>Total Students</th>
                            <th>Completed Profiles</th>
                            <th>Completion %</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profileCompletionByCourseShift as $item)
                            @php
                                $completionPercent = $item->total > 0 ? round(($item->completed / $item->total) * 100, 1) : 0;
                                $remaining = $item->total - $item->completed;
                            @endphp
                            <tr>
                                <td><strong>{{ $item->course_name }}</strong></td>
                                <td><span class="badge bg-info">{{ ucfirst($item->shift ?? 'N/A') }}</span></td>
                                <td>{{ number_format($item->total) }}</td>
                                <td><span class="text-success fw-semibold">{{ number_format($item->completed) }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercent }}%;" aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100">{{ $completionPercent }}%</div>
                                        </div>
                                        <small class="text-muted">{{ $completionPercent }}%</small>
                                    </div>
                                </td>
                                <td><span class="text-danger">{{ number_format($remaining) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Students with Completed Profiles by Branch -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Students with Completed Profiles by Branch</h2>
            <span class="text-primary"><i class="fas fa-user-check" aria-hidden="true"></i></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Completed Profiles</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsWithCompletedProfileByBranch as $item)
                            @php
                                $branchTotal = $studentsByBranch->where('branch_name', $item->branch_name)->first()->total ?? 0;
                                $percentage = $branchTotal > 0 ? round(($item->total / $branchTotal) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $item->branch_name }}</strong></td>
                                <td>{{ number_format($item->total) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ $percentage }}%</div>
                                        </div>
                                        <small class="text-muted">{{ $percentage }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Percentage Filter Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Students by Payment Percentage</h2>
            <button class="btn btn-sm btn-outline-primary" onclick="filterByPaymentPercentage()">Filter</button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Payment Percentage (%)</label>
                    <input type="number" id="paymentPercentage" class="form-control" min="0" max="100" value="50" placeholder="Enter percentage">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select id="filterBranch" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select id="filterCourse" class="form-select">
                        <option value="">All Courses</option>
                        @foreach(\App\Models\Course::all() as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" onclick="filterByPaymentPercentage()">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
            </div>
            <div id="paymentPercentageResults">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Enter a percentage and click Search to see students who have paid at least that percentage of their fees.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('dashboardFilters');
    const filterButton = document.getElementById('filterButton');
    const errorBox = document.getElementById('dashboardError');
    const moneyFormat = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {{ \Illuminate\Support\Js::from($chartData['daily_collections']['labels'] ?? []) }},
            datasets: [{
                label: 'Collections',
                data: {{ \Illuminate\Support\Js::from($chartData['daily_collections']['amounts'] ?? []) }},
                borderColor: '#0f766e',
                backgroundColor: 'rgba(15, 118, 110, .12)',
                fill: true,
                tension: .35,
                pointRadius: 2,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: context => ' ' + moneyFormat.format(context.parsed.y) + ' RWF' } }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: 'rgba(100, 116, 139, .12)' }, ticks: { callback: value => moneyFormat.format(value) } }
            }
        }
    });

    const expenseChart = new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels: {{ \Illuminate\Support\Js::from($chartData['expenses_by_category']['labels'] ?? []) }},
            datasets: [{
                data: {{ \Illuminate\Support\Js::from($chartData['expenses_by_category']['amounts'] ?? []) }},
                backgroundColor: ['#0f766e', '#2563a6', '#b45309', '#be123c', '#5b4b9a', '#0891b2', '#64748b'],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
                tooltip: { callbacks: { label: context => ' ' + context.label + ': ' + moneyFormat.format(context.parsed) + ' RWF' } }
            }
        }
    });

    function updateChartEmptyState(chart, emptyState) {
        const hasValues = chart.data.datasets[0].data.some(value => Number(value) > 0);
        chart.canvas.classList.toggle('d-none', !hasValues);
        emptyState.classList.toggle('d-none', hasValues);
    }

    updateChartEmptyState(revenueChart, document.getElementById('revenueChartEmpty'));
    updateChartEmptyState(expenseChart, document.getElementById('expenseChartEmpty'));

    async function getJson(url) {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) {
            throw new Error('Dashboard data could not be refreshed. Please try again.');
        }
        return response.json();
    }

    filterForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        errorBox.classList.add('d-none');
        filterButton.disabled = true;
        const originalButtonText = filterButton.innerHTML;
        filterButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Loading';
        const query = new URLSearchParams(new FormData(filterForm)).toString();

        try {
            const [kpis, charts] = await Promise.all([
                getJson('{{ route('staff.accountant.kpi-data') }}?' + query),
                getJson('{{ route('staff.accountant.chart-data') }}?' + query)
            ]);

            ['total_fees_expected', 'fees_collected', 'outstanding_balance', 'total_expenses'].forEach(function (key) {
                const element = document.getElementById('kpi-' + key);
                if (element && Number.isFinite(Number(kpis[key]))) {
                    element.textContent = moneyFormat.format(Number(kpis[key]));
                }
            });

            revenueChart.data.labels = charts.daily_collections.labels || [];
            revenueChart.data.datasets[0].data = charts.daily_collections.amounts || [];
            revenueChart.update();
            expenseChart.data.labels = charts.expenses_by_category.labels || [];
            expenseChart.data.datasets[0].data = charts.expenses_by_category.amounts || [];
            expenseChart.update();
            updateChartEmptyState(revenueChart, document.getElementById('revenueChartEmpty'));
            updateChartEmptyState(expenseChart, document.getElementById('expenseChartEmpty'));
            window.history.replaceState({}, '', filterForm.action + (query ? '?' + query : ''));
        } catch (error) {
            errorBox.textContent = error.message || 'Dashboard data could not be refreshed. Please try again.';
            errorBox.classList.remove('d-none');
        } finally {
            filterButton.disabled = false;
            filterButton.innerHTML = originalButtonText;
        }
    });

    // Payment Percentage Filter Function
    window.filterByPaymentPercentage = function() {
        const percentage = document.getElementById('paymentPercentage').value;
        const branchId = document.getElementById('filterBranch').value;
        const courseId = document.getElementById('filterCourse').value;

        if (!percentage || percentage < 0 || percentage > 100) {
            alert('Please enter a valid percentage between 0 and 100');
            return;
        }

        const resultsDiv = document.getElementById('paymentPercentageResults');
        resultsDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>';

        fetch('{{ route("staff.accountant.dashboard.filter-payment-percentage") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                percentage: percentage,
                branch_id: branchId || null,
                course_id: courseId || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.students.length === 0) {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No students found who have paid at least ${percentage}% of their fees.
                        </div>
                    `;
                } else {
                    let tableHtml = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Found ${data.count} students who have paid at least ${percentage}% of their fees.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Reg Number</th>
                                        <th>Branch</th>
                                        <th>Course</th>
                                        <th>Paid</th>
                                        <th>Total Fees</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    data.students.forEach(student => {
                        tableHtml += `
                            <tr>
                                <td><strong>${student.name}</strong></td>
                                <td>${student.reg_number}</td>
                                <td>${student.branch}</td>
                                <td>${student.course}</td>
                                <td>${number_format(student.paid_amount)} RWF</td>
                                <td>${number_format(student.total_fees)} RWF</td>
                                <td><span class="badge bg-success">${student.payment_percentage}%</span></td>
                            </tr>
                        `;
                    });
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    resultsDiv.innerHTML = tableHtml;
                }
            } else {
                resultsDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error: ${data.message || 'Failed to filter students'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error: Failed to fetch data. Please try again.
                </div>
            `;
        });
    };
});
</script>
@endpush
