@extends('staff.partials.sidebar_sec')

@section('title', 'Payment Schedule')

@section('content')
@php
    $allExpected = collect($paymentSchedule)->sum('total_expected');
    $allCollected = collect($paymentSchedule)->sum('total_collected');
    $overallRate = $allExpected > 0 ? ($allCollected / $allExpected) * 100 : 0;
@endphp
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Finance management</p>
            <h1 class="h3 fw-bold mb-1">Payment Schedule</h1>
            <p class="text-muted mb-0">Review expected fees, collections, and outstanding balances by course and term.</p>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print me-2" aria-hidden="true"></i>Print Schedule</button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><div class="text-muted small mb-1">Courses</div><div class="h3 fw-bold mb-1">{{ $courses->count() }}</div><small class="text-muted">With active fee structures</small></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><div class="text-muted small mb-1">Total Expected</div><div class="h3 fw-bold mb-1">{{ number_format($allExpected, 0) }}</div><small class="text-muted">Across all listed terms</small></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><div class="text-muted small mb-1">Total Collected</div><div class="h3 fw-bold text-success mb-1">{{ number_format($allCollected, 0) }}</div><small class="text-muted">Successful payments recorded</small></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><div class="text-muted small mb-1">Collection Rate</div><div class="h3 fw-bold mb-1">{{ number_format($overallRate, 1) }}%</div><small class="text-muted">Overall collection progress</small></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label for="termFilter" class="form-label small text-muted">Filter by Term</label>
                    <select id="termFilter" class="form-select">
                        <option value="">All Terms</option>
                        @foreach($allTerms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}{{ $term->academicYear ? ' - ' . $term->academicYear->year : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Current Term</label>
                    <input type="text" class="form-control" readonly value="{{ $currentTerm ? $currentTerm->name . ($currentTerm->academicYear ? ' - ' . $currentTerm->academicYear->year : '') : 'No active term' }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-semibold mb-0">Schedule details</h2>
            <span class="text-muted small"><span id="visibleScheduleCount">{{ count($paymentSchedule) }}</span> course-term records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="paymentScheduleTable">
                <thead class="table-light">
                    <tr><th>Course</th><th>Term</th><th>Students</th><th>Paid</th><th>Expected</th><th>Collected</th><th>Outstanding</th><th>Collection Rate</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($paymentSchedule as $schedule)
                        @php
                            $scheduleExpected = $schedule['total_expected'];
                            $scheduleCollected = $schedule['total_collected'];
                            $rate = $scheduleExpected > 0 ? ($scheduleCollected / $scheduleExpected) * 100 : 0;
                            $scheduleOutstanding = max(0, $scheduleExpected - $scheduleCollected);
                        @endphp
                        <tr data-term-id="{{ $schedule['term']->id }}" data-expected="{{ $scheduleExpected }}" data-collected="{{ $scheduleCollected }}">
                            <td><div class="fw-semibold">{{ $schedule['course']->course_name }}</div><small class="text-muted">{{ $schedule['course']->abbreviation }}</small></td>
                            <td>{{ $schedule['term']->name }}<br><small class="text-muted">{{ $schedule['term']->academicYear->year ?? '' }}</small></td>
                            <td>{{ $schedule['total_students'] }}</td>
                            <td>{{ $schedule['paid_students'] }} @if($schedule['total_students'] > 0)<small class="text-muted">({{ number_format(($schedule['paid_students'] / $schedule['total_students']) * 100, 1) }}%)</small>@endif</td>
                            <td class="text-nowrap">{{ number_format($scheduleExpected, 0) }} RWF</td>
                            <td class="text-nowrap">{{ number_format($scheduleCollected, 0) }} RWF</td>
                            <td class="text-danger fw-semibold text-nowrap">{{ number_format($scheduleOutstanding, 0) }} RWF</td>
                            <td style="min-width: 130px">
                                <div class="progress" role="progressbar" aria-label="Collection rate" aria-valuenow="{{ min(100, $rate) }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar @if($rate >= 80) bg-success @elseif($rate >= 50) bg-warning @else bg-danger @endif" style="width: {{ min(100, $rate) }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($rate, 1) }}%</small>
                            </td>
                            <td>
                                @if($scheduleCollected >= $scheduleExpected)
                                    <span class="badge text-bg-success">Complete</span>
                                @elseif($scheduleCollected > 0)
                                    <span class="badge text-bg-warning">In Progress</span>
                                @else
                                    <span class="badge text-bg-secondary">Not Started</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('staff.accountant.payment-schedule.course-details', $schedule['course']->id) }}" class="btn btn-sm btn-outline-primary" title="View course details" aria-label="View course details"><i class="fas fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-5"><i class="fas fa-calendar-xmark fa-2x d-block mb-2" aria-hidden="true"></i><div class="fw-semibold">No payment schedule available</div><small>Active course fee structures will appear here.</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filter = document.getElementById('termFilter');
    const rows = Array.from(document.querySelectorAll('#paymentScheduleTable tbody tr[data-term-id]'));
    const count = document.getElementById('visibleScheduleCount');

    filter.addEventListener('change', function () {
        let visibleCount = 0;
        rows.forEach(function (row) {
            const visible = !filter.value || row.dataset.termId === filter.value;
            row.classList.toggle('d-none', !visible);
            if (visible) visibleCount++;
        });
        count.textContent = visibleCount;
    });
});
</script>
<style>
@media print {
    .sidebar, .topbar, .sidebar-overlay, .btn, #termFilter, .card-header { display: none !important; }
    .main-content { margin: 0 !important; }
    .page-content { padding: 0 !important; }
    .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
    .table-responsive { overflow: visible !important; }
}
</style>
@endpush
