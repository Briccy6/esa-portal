@extends('staff.partials.sidebar_sec')

@section('title', 'Finance Dashboard')

@include('accountant.partials.theme')

@push('styles')
<style>
    .accountant-dashboard .kpi-card { transition: transform .2s ease, box-shadow .2s ease; }
    .accountant-dashboard .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(15, 23, 42, .1); }
    .accountant-dashboard .chart-wrap { position: relative; height: clamp(250px, 34vh, 360px); }
    .accountant-dashboard .optional-fee-chart-wrap { position: relative; height: clamp(280px, 42vh, 460px); }
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
    .accountant-payment-modal .modal-content {
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 20px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
    }
    .accountant-payment-modal__header {
        display: flex;
        align-items: flex-start;
        gap: .9rem;
        padding: 1.25rem 1.4rem;
        border: 0;
        background: linear-gradient(120deg, #14233a, #205f62);
        color: #fff;
    }
    .accountant-payment-modal__icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 13px;
        background: rgba(255,255,255,.1);
        color: #f0b29b;
        font-size: 1.1rem;
    }
    .accountant-payment-modal__copy { min-width: 0; flex: 1; }
    .accountant-payment-modal__eyebrow {
        display: block;
        margin-bottom: .2rem;
        color: #f0b29b;
        font-size: .64rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }
    .accountant-payment-modal__header .modal-title { color: #fff; font-size: 1.05rem; font-weight: 800; }
    .accountant-payment-modal__subtitle { margin: .25rem 0 0; color: rgba(255,255,255,.75); font-size: .78rem; }
    .accountant-payment-modal__count {
        flex: 0 0 auto;
        padding: .38rem .65rem;
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 999px;
        background: rgba(255,255,255,.09);
        color: #fff;
        font-size: .7rem;
        font-weight: 800;
    }
    .accountant-payment-modal .modal-body { max-height: min(62vh, 620px); overflow-y: auto; padding: 1.15rem 1.35rem; background: #f6f8fa; }
    .accountant-payment-modal__summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .85rem 1rem;
        margin-bottom: .8rem;
        border: 1px solid #e6e9ee;
        border-radius: 12px;
        background: #fff;
    }
    .accountant-payment-modal__summary-label { display: block; color: #728096; font-size: .69rem; font-weight: 700; }
    .accountant-payment-modal__summary strong { display: block; margin-top: .15rem; color: #17243b; font-size: 1.15rem; font-weight: 850; }
    .accountant-pending-payment {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) repeat(3, minmax(95px, .75fr));
        align-items: center;
        gap: .75rem;
        padding: .85rem 1rem;
        margin-bottom: .6rem;
        border: 1px solid #e6e9ee;
        border-radius: 12px;
        background: #fff;
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .accountant-pending-payment:hover { border-color: #cbd6d9; box-shadow: 0 5px 14px rgba(23,36,59,.05); }
    .accountant-pending-payment__teacher { display: flex; min-width: 0; align-items: center; gap: .65rem; }
    .accountant-pending-payment__avatar {
        display: grid;
        width: 37px;
        height: 37px;
        flex: 0 0 37px;
        place-items: center;
        border-radius: 11px;
        background: #edf5f4;
        color: #286d6b;
    }
    .accountant-pending-payment__teacher-copy { min-width: 0; }
    .accountant-pending-payment__name { display: block; overflow: hidden; color: #17243b; font-size: .8rem; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
    .accountant-pending-payment__phone { display: inline-block; margin-top: .15rem; color: #738096; font-size: .7rem; text-decoration: none; }
    .accountant-pending-payment__phone:hover { color: #205f62; text-decoration: underline; }
    .accountant-pending-payment__field-label { display: block; color: #8993a3; font-size: .61rem; font-weight: 750; letter-spacing: .06em; text-transform: uppercase; }
    .accountant-pending-payment__field-value { display: block; margin-top: .2rem; color: #334056; font-size: .75rem; font-weight: 750; }
    .accountant-pending-payment__amount { color: #205f62; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .accountant-payment-modal .modal-footer { gap: .5rem; padding: 1rem 1.35rem; border-top: 1px solid #e9edf1; background: #fff; }
    .accountant-payment-modal__footer-note { margin-right: auto; color: #7b8698; font-size: .72rem; }
    .accountant-payment-modal__process {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        gap: .45rem;
        padding: .62rem .9rem;
        border: 1px solid #205f62;
        border-radius: 9px;
        background: #205f62;
        color: #fff;
        font-size: .76rem;
        font-weight: 800;
        text-decoration: none;
    }
    .accountant-payment-modal__process:hover { border-color: #174b4d; background: #174b4d; color: #fff; }
    @media (max-width: 575.98px) {
        .accountant-dashboard .chart-wrap { height: 260px; }
        .accountant-dashboard .dashboard-watermark { width: 52vw; right: -10%; top: 35%; }
        .accountant-payment-modal__header { flex-wrap: wrap; padding: 1rem; }
        .accountant-payment-modal__count { margin-left: 3.3rem; }
        .accountant-payment-modal .modal-body { padding: .8rem; }
        .accountant-pending-payment { grid-template-columns: 1fr 1fr; gap: .7rem; padding: .8rem; }
        .accountant-pending-payment__teacher { grid-column: 1 / -1; }
        .accountant-payment-modal .modal-footer { align-items: stretch; flex-direction: column; padding: .85rem; }
        .accountant-payment-modal__footer-note { margin-right: 0; }
        .accountant-payment-modal__process, .accountant-payment-modal .btn { width: 100%; justify-content: center; }
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

    <div class="card mb-4">
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 py-3">
            <div>
                <h2 class="h6 fw-semibold mb-1">Optional fee decisions by course</h2>
                <p class="text-muted small mb-0">Accepted and declined student responses for the selected year and branch.</p>
            </div>
            <a href="{{ route('staff.accountant.optional-fees.responses') }}" class="btn btn-sm btn-outline-primary">
                Review student responses <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="optional-fee-chart-wrap">
                <canvas id="optionalFeeResponsesChart" aria-label="Optional fee decisions by course" role="img"></canvas>
                <div class="chart-empty {{ $optionalFeeResponseSummary->isEmpty() ? '' : 'd-none' }}" id="optionalFeeResponsesEmpty">
                    No optional fee responses are available for these filters.
                </div>
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

    <!-- Students by Branch -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Students by Branch</h2>
            <span class="text-primary"><i class="fas fa-building" aria-hidden="true"></i></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Total Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsByBranch as $item)
                            <tr>
                                <td><strong>{{ $item->branch_name }}</strong></td>
                                <td><span class="badge bg-primary">{{ number_format($item->total) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- No Payment Students by Branch -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">No Payment Students by Branch</h2>
            <div class="d-flex gap-2">
                <select id="noPaymentBranchFilter" class="form-select form-select-sm" style="width: 200px;">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary" onclick="loadNoPaymentStudents()">
                    <i class="fas fa-search me-1"></i>View
                </button>
                <button class="btn btn-sm btn-success" onclick="exportNoPaymentStudents()">
                    <i class="fas fa-file-csv me-1"></i>Export CSV
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="noPaymentStudentsTable">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Select a branch and click "View" to see students with no payments.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students At Least Payment Percentage -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Students At Least Payment Percentage</h2>
            <div class="d-flex gap-2">
                <input type="number" id="belowPercentageInput" class="form-control form-control-sm" placeholder="%" value="50" min="0" max="100" style="width: 80px;">
                <select id="belowPercentageBranchFilter" class="form-select form-select-sm" style="width: 200px;">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary" onclick="loadStudentsBelowPercentage()">
                    <i class="fas fa-search me-1"></i>View
                </button>
                <button class="btn btn-sm btn-primary" onclick="printStudentsBelowPercentage()">
                    <i class="fas fa-print me-1"></i>Print
                </button>
                <button class="btn btn-sm btn-success" onclick="exportStudentsBelowPercentage()">
                    <i class="fas fa-file-csv me-1"></i>Export CSV
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="studentsBelowPercentageTable">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Enter a percentage and click "View" to see students who paid below that percentage.</p>
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

    <!-- Students by Course in Branch -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Students by Course in Branch</h2>
            <span class="text-success"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Total Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsByCourseInBranch as $item)
                            <tr>
                                <td><strong>{{ $item->branch_name }}</strong></td>
                                <td>{{ $item->course_name }}</td>
                                <td><span class="badge bg-success">{{ number_format($item->total) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expenses by Branch -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold mb-0">Expenses by Branch</h2>
            <span class="text-danger"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Total Expenses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expensesByBranch as $item)
                            <tr>
                                <td><strong>{{ $item->branch_name ?? 'N/A' }}</strong></td>
                                <td><span class="text-danger fw-semibold">{{ number_format($item->total) }} RWF</span></td>
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

    let optionalFeeResponseCourseIds = {{ \Illuminate\Support\Js::from($optionalFeeResponseSummary->pluck('course_id')->values()) }};
    const optionalFeeResponseChart = new Chart(document.getElementById('optionalFeeResponsesChart'), {
        type: 'bar',
        data: {
            labels: {{ \Illuminate\Support\Js::from($optionalFeeResponseSummary->pluck('course_name')->values()) }},
            datasets: [
                { label: 'Accepted', data: {{ \Illuminate\Support\Js::from($optionalFeeResponseSummary->pluck('accepted')->values()) }}, backgroundColor: '#16803a', borderRadius: 4 },
                { label: 'Declined', data: {{ \Illuminate\Support\Js::from($optionalFeeResponseSummary->pluck('declined')->values()) }}, backgroundColor: '#c2413b', borderRadius: 4 },
                { label: 'Pending', data: {{ \Illuminate\Support\Js::from($optionalFeeResponseSummary->pluck('pending')->values()) }}, backgroundColor: '#d49b29', borderRadius: 4 },
                { label: 'Assigned', data: {{ \Illuminate\Support\Js::from($optionalFeeResponseSummary->pluck('assigned')->values()) }}, backgroundColor: '#5479a8', borderRadius: 4 }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } } },
            scales: {
                x: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(100, 116, 139, .12)' } },
                y: { stacked: true, grid: { display: false } }
            },
            onClick: function (event, elements) {
                if (elements.length) {
                    const courseId = optionalFeeResponseCourseIds[elements[0].index];
                    const reviewUrl = new URL({{ \Illuminate\Support\Js::from(route('staff.accountant.optional-fees.responses', array_filter([
                        'academic_year_id' => $filters['academic_year_id'] ?? null,
                        'branch_id' => $filters['branch_id'] ?? null,
                    ]))) }}, window.location.origin);
                    reviewUrl.searchParams.set('course_id', courseId);
                    window.location.href = reviewUrl.toString();
                }
            }
        }
    });

    function updateOptionalFeeChartEmptyState() {
        const hasValues = optionalFeeResponseChart.data.datasets.some(dataset =>
            dataset.data.some(value => Number(value) > 0)
        );
        optionalFeeResponseChart.canvas.classList.toggle('d-none', !hasValues);
        document.getElementById('optionalFeeResponsesEmpty').classList.toggle('d-none', hasValues);
    }

    updateOptionalFeeChartEmptyState();

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
            const [kpis, charts, optionalFeeResponses] = await Promise.all([
                getJson('{{ route('staff.accountant.kpi-data') }}?' + query),
                getJson('{{ route('staff.accountant.chart-data') }}?' + query),
                getJson('{{ route('staff.accountant.optional-fee-response-data') }}?' + query)
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
            optionalFeeResponseChart.data.labels = optionalFeeResponses.map(item => item.course_name);
            optionalFeeResponseCourseIds = optionalFeeResponses.map(item => item.course_id);
            optionalFeeResponseChart.data.datasets[0].data = optionalFeeResponses.map(item => Number(item.accepted));
            optionalFeeResponseChart.data.datasets[1].data = optionalFeeResponses.map(item => Number(item.declined));
            optionalFeeResponseChart.data.datasets[2].data = optionalFeeResponses.map(item => Number(item.pending));
            optionalFeeResponseChart.data.datasets[3].data = optionalFeeResponses.map(item => Number(item.assigned));
            optionalFeeResponseChart.update();
            updateChartEmptyState(revenueChart, document.getElementById('revenueChartEmpty'));
            updateChartEmptyState(expenseChart, document.getElementById('expenseChartEmpty'));
            updateOptionalFeeChartEmptyState();
            window.history.replaceState({}, '', filterForm.action + (query ? '?' + query : ''));
        } catch (error) {
            errorBox.textContent = error.message || 'Dashboard data could not be refreshed. Please try again.';
            errorBox.classList.remove('d-none');
        } finally {
            filterButton.disabled = false;
            filterButton.innerHTML = originalButtonText;
        }
    });
});
</script>

<script>
// Helper function for number formatting (JavaScript equivalent of PHP number_format)
function number_format(num) {
    return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(num);
}

// Load No Payment Students
function loadNoPaymentStudents() {
    const branchId = document.getElementById('noPaymentBranchFilter').value;
    const tableDiv = document.getElementById('noPaymentStudentsTable');

    tableDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>';

    fetch('/staff/accountant/no-payment-students?branch_id=' + branchId, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.students.length === 0) {
                tableDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No students with no payments found for the selected branch.
                    </div>
                `;
            } else {
                let tableHtml = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Found ${data.count} students with no payments.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Shift</th>
                                    <th>Total Fees (RWF)</th>
                                    <th>Paid (RWF)</th>
                                    <th>Remaining (RWF)</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                data.students.forEach(student => {
                    tableHtml += `
                        <tr>
                            <td><strong>${student.reg_number}</strong></td>
                            <td>${student.full_name}</td>
                            <td>${student.email || 'N/A'}</td>
                            <td>${student.phone || 'N/A'}</td>
                            <td>${student.branch ? student.branch.name : 'N/A'}</td>
                            <td>${student.course ? student.course.course_name : 'N/A'}</td>
                            <td><span class="badge bg-info">${student.shift || 'N/A'}</span></td>
                            <td class="text-end">${number_format(student.total_fees)}</td>
                            <td class="text-end text-success">${number_format(student.paid_amount)}</td>
                            <td class="text-end text-danger fw-semibold">${number_format(student.remaining_amount)}</td>
                        </tr>
                    `;
                });
                tableHtml += `
                            </tbody>
                        </table>
                    </div>
                `;
                tableDiv.innerHTML = tableHtml;
            }
        } else {
            tableDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error: Failed to load students.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tableDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: Failed to fetch data. Please try again.
            </div>
        `;
    });
}

// Export No Payment Students to CSV
function exportNoPaymentStudents() {
    const branchId = document.getElementById('noPaymentBranchFilter').value;
    const url = '/staff/accountant/no-payment-students/export?branch_id=' + branchId;
    window.location.href = url;
}

// Load Students Below Payment Percentage
function loadStudentsBelowPercentage() {
    const percentage = document.getElementById('belowPercentageInput').value;
    const branchId = document.getElementById('belowPercentageBranchFilter').value;
    const tableDiv = document.getElementById('studentsBelowPercentageTable');

    if (!percentage || percentage < 0 || percentage > 100) {
        alert('Please enter a valid percentage between 0 and 100');
        return;
    }

    tableDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>';

    fetch('/staff/accountant/students-below-percentage?percentage=' + percentage + '&branch_id=' + branchId, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.students.length === 0) {
                tableDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No students found who paid at least ${percentage}%.
                    </div>
                `;
            } else {
                let tableHtml = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Found ${data.count} students who paid at least ${percentage}%.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsBelowTable">
                            <thead>
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Shift</th>
                                    <th>Total Fees (RWF)</th>
                                    <th>Paid (RWF)</th>
                                    <th>Remaining (RWF)</th>
                                    <th>Payment %</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                data.students.forEach(student => {
                    const isBlocked = student.is_blocked;
                    tableHtml += `
                        <tr>
                            <td><strong>${student.reg_number}</strong></td>
                            <td>${student.full_name}</td>
                            <td>${student.email || 'N/A'}</td>
                            <td>${student.phone || 'N/A'}</td>
                            <td>${student.branch ? student.branch.name : 'N/A'}</td>
                            <td>${student.course ? student.course.course_name : 'N/A'}</td>
                            <td><span class="badge bg-info">${student.shift || 'N/A'}</span></td>
                            <td class="text-end">${number_format(student.total_fees)}</td>
                            <td class="text-end text-success">${number_format(student.paid_amount)}</td>
                            <td class="text-end text-danger fw-semibold">${number_format(student.remaining)}</td>
                            <td class="text-end">${student.payment_percentage}%</td>
                            <td>${isBlocked ? '<span class="badge bg-danger">Blocked</span>' : '<span class="badge bg-success">Active</span>'}</td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    ${isBlocked ? 
                                        `<button class="btn btn-sm btn-outline-success" onclick="unblockStudent(${student.id})" title="Unblock"><i class="fas fa-unlock"></i></button>` :
                                        `<button class="btn btn-sm btn-outline-danger" onclick="blockStudent(${student.id})" title="Block"><i class="fas fa-ban"></i></button>`
                                    }
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tableHtml += `
                            </tbody>
                        </table>
                    </div>
                `;
                tableDiv.innerHTML = tableHtml;
            }
        } else {
            tableDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error: Failed to load students.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tableDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: Failed to fetch data. Please try again.
            </div>
        `;
    });
}

// Print Students Below Percentage
function printStudentsBelowPercentage() {
    const printContent = document.getElementById('studentsBelowPercentageTable').innerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Students Below Payment Percentage</title>');
    printWindow.document.write('<style>table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background-color:#f2f2f2}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Export Students Below Percentage to CSV
function exportStudentsBelowPercentage() {
    const percentage = document.getElementById('belowPercentageInput').value;
    const branchId = document.getElementById('belowPercentageBranchFilter').value;
    const url = '/staff/accountant/students-below-percentage/export?percentage=' + percentage + '&branch_id=' + branchId;
    window.location.href = url;
}

// Block Student
function blockStudent(studentId) {
    if (!confirm('Are you sure you want to block this student? They will not be able to access the system.')) {
        return;
    }

    fetch('/staff/accountant/block-student', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            student_id: studentId,
            reason: 'Insufficient fee payment'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadStudentsBelowPercentage(); // Reload the table
        } else {
            alert('Failed to block student.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to block student.');
    });
}

// Unblock Student
function unblockStudent(studentId) {
    if (!confirm('Are you sure you want to unblock this student?')) {
        return;
    }

    fetch('/staff/accountant/unblock-student', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            student_id: studentId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadStudentsBelowPercentage(); // Reload the table
        } else {
            alert('Failed to unblock student.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to unblock student.');
    });
}

// Payment Percentage Filter Function - defined outside DOMContentLoaded for global access
function filterByPaymentPercentage() {
    const percentage = document.getElementById('paymentPercentage').value;
    const branchId = document.getElementById('filterBranch').value;
    const courseId = document.getElementById('filterCourse').value;

    if (!percentage || percentage < 0 || percentage > 100) {
        alert('Please enter a valid percentage between 0 and 100');
        return;
    }

    const resultsDiv = document.getElementById('paymentPercentageResults');
    resultsDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>';

    fetch('/staff/accountant/dashboard/filter-payment-percentage', {
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
}
</script>

<script>
// Load pending teacher payments for accountant
async function loadPendingTeacherPayments() {
    try {
        const response = await fetch('{{ route('staff.accountant.teacher-payments.pending') }}', {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin'
        });
        if (!response.ok) {
            throw new Error(`Pending payment request failed with status ${response.status}`);
        }

        const payments = await response.json();
        if (!Array.isArray(payments)) {
            throw new TypeError('Pending payments response was not a list');
        }

        if (payments.length > 0) {
            showPendingPaymentsNotification(payments);
        }
    } catch (error) {
        console.error('Error loading pending teacher payments:', error);
        const errorBox = document.getElementById('dashboardError');
        if (errorBox) {
            errorBox.textContent = 'Approved teacher payments could not be loaded. Please refresh the dashboard and try again.';
            errorBox.classList.remove('d-none');
        }
    }
}

function showPendingPaymentsNotification(payments) {
    if (!window.bootstrap?.Modal || document.getElementById('pending-payments-modal')) {
        throw new Error('The pending payments notification could not be opened.');
    }

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
    const moneyFormat = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });
    const hoursFormat = new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 });
    const formatMoney = value => moneyFormat.format(Number.isFinite(Number(value)) ? Number(value) : 0);
    const formatHours = value => hoursFormat.format(Number.isFinite(Number(value)) ? Number(value) : 0);
    const formatDate = value => {
        const rawDate = String(value ?? '').slice(0, 10);
        const date = new Date(`${rawDate}T00:00:00Z`);

        return Number.isNaN(date.getTime())
            ? escapeHtml(rawDate || 'Date not listed')
            : new Intl.DateTimeFormat('en-GB', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC' }).format(date);
    };
    const totalRemaining = payments.reduce((total, payment) => {
        const remaining = Number(payment.calculated_amount || 0) - Number(payment.amount_paid || 0);
        return total + Math.max(remaining, 0);
    }, 0);
    const paymentCards = payments.map(payment => {
        const teacher = payment.teacher || {};
        const daily = payment.teacher_daily || payment.teacherDaily || {};
        const phone = String(teacher.telephone || '');
        const remaining = Math.max(Number(payment.calculated_amount || 0) - Number(payment.amount_paid || 0), 0);

        return `
            <article class="accountant-pending-payment">
                <div class="accountant-pending-payment__teacher">
                    <span class="accountant-pending-payment__avatar" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                    <span class="accountant-pending-payment__teacher-copy">
                        <strong class="accountant-pending-payment__name">${escapeHtml(teacher.teacher_name || 'Teacher not listed')}</strong>
                        ${phone
                            ? `<a class="accountant-pending-payment__phone" href="tel:${escapeHtml(phone)}">${escapeHtml(phone)}</a>`
                            : '<span class="accountant-pending-payment__phone">Telephone not listed</span>'}
                    </span>
                </div>
                <div>
                    <span class="accountant-pending-payment__field-label">Work date</span>
                    <span class="accountant-pending-payment__field-value">${formatDate(payment.work_date)}</span>
                </div>
                <div>
                    <span class="accountant-pending-payment__field-label">Hours</span>
                    <span class="accountant-pending-payment__field-value">${formatHours(daily.hours_studied)} h</span>
                </div>
                <div>
                    <span class="accountant-pending-payment__field-label">Amount due</span>
                    <strong class="accountant-pending-payment__field-value accountant-pending-payment__amount">${formatMoney(remaining)} RWF</strong>
                </div>
            </article>
        `;
    }).join('');
    const modalHtml = `
        <div class="modal fade accountant-payment-modal" id="pending-payments-modal" tabindex="-1" aria-labelledby="pending-payments-title" aria-describedby="pending-payments-description">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header accountant-payment-modal__header">
                        <span class="accountant-payment-modal__icon" aria-hidden="true"><i class="fas fa-money-bill-transfer"></i></span>
                        <div class="accountant-payment-modal__copy">
                            <span class="accountant-payment-modal__eyebrow">Action required</span>
                            <h2 class="modal-title" id="pending-payments-title">Approved teacher payments</h2>
                            <p class="accountant-payment-modal__subtitle" id="pending-payments-description">Review the approved work records before processing payments.</p>
                        </div>
                        <span class="accountant-payment-modal__count">${payments.length} waiting</span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="accountant-payment-modal__summary">
                            <div>
                                <span class="accountant-payment-modal__summary-label">Total awaiting payment</span>
                                <strong>${formatMoney(totalRemaining)} RWF</strong>
                            </div>
                            <span class="text-muted small">${payments.length} approved ${payments.length === 1 ? 'record' : 'records'}</span>
                        </div>
                        <div class="accountant-pending-payment-list">${paymentCards}</div>
                    </div>
                    <div class="modal-footer">
                        <span class="accountant-payment-modal__footer-note">You can review each record and enter payment notes on the next page.</span>
                        <a href="{{ route('staff.accountant.teacher-payments.index') }}" class="accountant-payment-modal__process">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>Process payments
                        </a>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Review later</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new window.bootstrap.Modal(document.getElementById('pending-payments-modal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    loadPendingTeacherPayments();
});
</script>
@endpush
