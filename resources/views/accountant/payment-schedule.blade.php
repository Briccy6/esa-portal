@extends('staff.partials.sidebar_sec')

@section('title', 'Payment Schedule - Full Year Overview')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-calendar-alt me-2"></i>Payment Schedule - Full Year Overview</h2>
            <p class="text-muted">Complete payment schedule for all courses and terms</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-1"></i> Print Schedule
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <h3>{{ $courses->count() }}</h3>
                    <small>Active courses with fee structures</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Expected</h5>
                    <h3>{{ number_format(collect($paymentSchedule)->sum('total_expected'), 0) }} RWF</h3>
                    <small>For all terms and courses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Collected</h5>
                    <h3>{{ number_format(collect($paymentSchedule)->sum('total_collected'), 0) }} RWF</h3>
                    <small>Payments received to date</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Collection Rate</h5>
                    <h3>
                        @php
                            $totalExpected = collect($paymentSchedule)->sum('total_expected');
                            $totalCollected = collect($paymentSchedule)->sum('total_collected');
                            $rate = $totalExpected > 0 ? ($totalCollected / $totalExpected) * 100 : 0;
                        @endphp
                        {{ number_format($rate, 1) }}%
                    </h3>
                    <small>Overall collection efficiency</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Term Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i>Filter by Term</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Select Term:</label>
                    <select id="termFilter" class="form-select">
                        <option value="">All Terms</option>
                        @foreach($allTerms as $term)
                            <option value="{{ $term->id }}">
                                {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                {{ $term->start_date->format('Y') }}/{{ $term->end_date->format('Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current Term:</label>
                    <input type="text" class="form-control" readonly 
                           value="{{ $currentTerm ? $currentTerm->name . ' - ' . $currentTerm->academicYear->year : 'No active term' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Schedule Table -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-table me-2"></i>Payment Schedule Details</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="paymentScheduleTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Course</th>
                            <th>Term</th>
                            <th>Total Students</th>
                            <th>Paid Students</th>
                            <th>Expected Amount</th>
                            <th>Collected Amount</th>
                            <th>Collection Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentSchedule as $schedule)
                        <tr data-term-id="{{ $schedule['term']->id }}">
                            <td>
                                <strong>{{ $schedule['course']->course_name }}</strong><br>
                                <small class="text-muted">{{ $schedule['course']->abbreviation }}</small>
                            </td>
                            <td>
                                {{ $schedule['term']->name }}<br>
                                <small class="text-muted">{{ $schedule['term']->academicYear->year ?? '' }}</small>
                            </td>
                            <td>{{ $schedule['total_students'] }}</td>
                            <td>
                                {{ $schedule['paid_students'] }}
                                @if($schedule['total_students'] > 0)
                                    <small class="text-muted">
                                        ({{ number_format(($schedule['paid_students'] / $schedule['total_students']) * 100, 1) }}%)
                                    </small>
                                @endif
                            </td>
                            <td>{{ number_format($schedule['total_expected'], 0) }} RWF</td>
                            <td>{{ number_format($schedule['total_collected'], 0) }} RWF</td>
                            <td>
                                @php
                                    $rate = $schedule['total_expected'] > 0 ? 
                                           ($schedule['total_collected'] / $schedule['total_expected']) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar @if($rate >= 80) bg-success @elseif($rate >= 50) bg-warning @else bg-danger @endif" 
                                         role="progressbar" style="width: {{ $rate }}%">
                                        {{ number_format($rate, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($schedule['total_collected'] >= $schedule['total_expected'])
                                    <span class="badge bg-success">Complete</span>
                                @elseif($schedule['total_collected'] > 0)
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-danger">Not Started</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.accountant.payment-schedule.course-details', $schedule['course']->id) }}"
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.students.index') }}"
                                       class="btn btn-outline-success" title="Record Payment">
                                        <i class="fas fa-money-bill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const termFilter = document.getElementById('termFilter');
    const tableRows = document.querySelectorAll('#paymentScheduleTable tbody tr');
    
    termFilter.addEventListener('change', function() {
        const selectedTerm = this.value;
        
        tableRows.forEach(row => {
            if (selectedTerm === '' || row.dataset.termId === selectedTerm) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

<style>
@media print {
    .btn, .card-header h5, .form-label, #termFilter {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
}
</style>
@endsection
