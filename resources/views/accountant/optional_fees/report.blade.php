@extends('staff.partials.sidebar_sec')

@section('title', 'Optional Fee Responses Report')

@include('accountant.partials.theme')

@section('content')
<style>
    .optional-report { --report-border: #e7edf5; --report-ink: #172033; --report-muted: #667085; }
    .optional-report .page-heading { color: var(--report-ink); letter-spacing: -.02em; }
    .optional-report .page-copy { color: var(--report-muted); }
    .optional-report .report-panel, .optional-report .report-table-card {
        border: 1px solid var(--report-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .045);
    }
    .optional-report .report-panel { padding: 1.15rem; }
    .optional-report .form-label { color: #344054; font-size: .82rem; font-weight: 650; }
    .optional-report .form-control, .optional-report .form-select {
        min-height: 43px; border-color: #d9e1ec; border-radius: 8px;
    }
    .optional-report .form-control:focus, .optional-report .form-select:focus {
        border-color: #4e83ff; box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .09);
    }
    .optional-report .stat-card {
        height: 100%; border: 1px solid var(--report-border); border-radius: 12px;
        background: #fff; padding: 1rem 1.1rem;
    }
    .optional-report .stat-icon {
        width: 38px; height: 38px; border-radius: 9px; display: grid; place-items: center;
        background: #eef4ff; color: #2563eb;
    }
    .optional-report .stat-card.accepted .stat-icon { background: #eaf8ef; color: #16803a; }
    .optional-report .stat-card.declined .stat-icon { background: #fdecec; color: #c52b2b; }
    .optional-report .stat-card.pending .stat-icon { background: #fff4d6; color: #9a6700; }
    .optional-report .stat-value { color: var(--report-ink); font-size: 1.45rem; line-height: 1; font-weight: 800; }
    .optional-report .stat-label { color: var(--report-muted); font-size: .76rem; font-weight: 650; }
    .optional-report .accepted-badge, .optional-report .declined-badge, .optional-report .pending-badge {
        display: inline-flex; align-items: center; border-radius: 999px; padding: .32rem .65rem;
        font-size: .72rem; font-weight: 750;
    }
    .optional-report .accepted-badge { background: #dcfce7; color: #166534; }
    .optional-report .declined-badge { background: #fee2e2; color: #991b1b; }
    .optional-report .pending-badge { background: #fef3c7; color: #92400e; }
    .optional-report .fee-kind {
        display: inline-flex; align-items: center; border-radius: 7px; background: #f1f5f9;
        color: #475569; padding: .28rem .48rem; font-size: .76rem; font-weight: 700;
    }
    .optional-report .table { --bs-table-hover-bg: #f8fafc; margin-bottom: 0; }
    .optional-report .table thead th {
        border-bottom-width: 1px; background: #f8fafc; color: #475569;
        font-size: .73rem; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
    }
    .optional-report .table tbody td { padding-top: .9rem; padding-bottom: .9rem; color: #344054; font-size: .85rem; vertical-align: middle; }
    .optional-report .student-name { color: var(--report-ink); font-weight: 750; }
    .optional-report .pagination { margin: 0; }
    @media (max-width: 575.98px) {
        .optional-report { padding-top: 1rem !important; }
        .optional-report .export-btn { width: 100%; }
        .optional-report .filter-actions .btn { flex: 1; }
    }
</style>

<div class="container-fluid py-4 optional-report">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold page-heading mb-1">Optional Fee Responses</h1>
            <p class="page-copy mb-0">Review student decisions for practical and trip fees.</p>
        </div>
        <div>
            <a href="{{ route('staff.accountant.optional-fees.export', request()->all()) }}" class="btn btn-success export-btn">
                <i class="fas fa-download me-2" aria-hidden="true"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="report-panel mb-4">
            <form method="GET" action="{{ route('staff.accountant.optional-fees.report') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fee Type</label>
                    <select name="fee_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="practical" {{ request('fee_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                        <option value="trip" {{ request('fee_type') == 'trip' ? 'selected' : '' }}>Trip</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Response</label>
                    <select name="response" class="form-select">
                        <option value="">All Responses</option>
                        <option value="accepted" {{ request('response') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="declined" {{ request('response') == 'declined' ? 'selected' : '' }}>Declined</option>
                        <option value="pending" {{ request('response') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex filter-actions gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('staff.accountant.optional-fees.report') }}" class="btn btn-outline-secondary">
                        Clear
                    </a>
                </div>
            </form>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        @foreach([
            ['key' => 'total', 'label' => 'Total Responses', 'icon' => 'fa-list-check', 'class' => ''],
            ['key' => 'accepted', 'label' => 'Accepted by students', 'icon' => 'fa-circle-check', 'class' => 'accepted'],
            ['key' => 'assigned', 'label' => 'Assigned by accountant', 'icon' => 'fa-user-check', 'class' => ''],
            ['key' => 'declined', 'label' => 'Declined', 'icon' => 'fa-circle-xmark', 'class' => 'declined'],
            ['key' => 'pending', 'label' => 'Pending', 'icon' => 'fa-clock', 'class' => 'pending'],
        ] as $stat)
            <div class="col-6 col-xl">
                <div class="stat-card {{ $stat['class'] }} d-flex align-items-center gap-3">
                    <div class="stat-icon flex-shrink-0"><i class="fas {{ $stat['icon'] }}" aria-hidden="true"></i></div>
                    <div>
                        <div class="stat-value">{{ number_format($stats[$stat['key']] ?? 0) }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Table -->
    <div class="report-table-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Reg Number</th>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Response</th>
                            <th>Responded At</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                            <tr>
                                <td><span class="student-name">{{ $response->student->full_name ?? 'N/A' }}</span></td>
                                <td>{{ $response->student->reg_number ?? 'N/A' }}</td>
                                <td>{{ $response->student->branch->name ?? 'N/A' }}</td>
                                <td>{{ $response->student->course->course_name ?? 'N/A' }}</td>
                                <td><span class="fee-kind">{{ ucfirst($response->fee_type) }}</span></td>
                                <td>
                                    @if($response->fee_type === 'practical')
                                        {{ number_format($response->courseTermBranchFee->practical_fee, 2) }} RWF
                                    @else
                                        {{ number_format($response->courseTermBranchFee->trip_fee, 2) }} RWF
                                    @endif
                                </td>
                                <td>
                                    @php($assignedByAccountant = str_starts_with((string) $response->notes, 'Assigned by accountant:'))
                                    <span class="badge {{ $assignedByAccountant ? 'accepted-badge' : ($response->response === 'accepted' ? 'accepted-badge' : ($response->response === 'declined' ? 'declined-badge' : 'pending-badge')) }}">
                                        {{ $assignedByAccountant ? 'Assigned' : ucfirst($response->response) }}
                                    </span>
                                </td>
                                <td>{{ $response->responded_at ? $response->responded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $response->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center justify-content-md-end px-3 py-3 border-top">
                {{ $responses->links() }}
            </div>
    </div>
</div>
@endsection
