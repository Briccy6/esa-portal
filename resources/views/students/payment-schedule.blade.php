@extends('students.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-calendar-alt me-2"></i>My Payment Schedule</h2>
            <p class="text-muted">Complete payment schedule for your course</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-1"></i> Print Schedule
            </button>
        </div>
    </div>

    <!-- Student Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-user me-2"></i>Student Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Full Name:</strong><br>
                    {{ $student->full_name }}
                </div>
                <div class="col-md-3">
                    <strong>Registration Number:</strong><br>
                    {{ $student->reg_number }}
                </div>
                <div class="col-md-3">
                    <strong>Course:</strong><br>
                    {{ $course->course_name }} ({{ $course->abbreviation }})
                </div>
                <div class="col-md-3">
                    <strong>Course Code:</strong><br>
                    {{ $course->course_code }}
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ count($paymentSchedule) }}</h4>
                    <small>Active Terms</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ collect($paymentSchedule)->where('status', 'paid')->count() }}</h4>
                    <small>Fully Paid Terms</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ collect($paymentSchedule)->where('status', 'partial')->count() }}</h4>
                    <small>Partial Payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format(collect($paymentSchedule)->sum('total_paid'), 0) }}</h4>
                    <small>Total Paid (RWF)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Term-wise Payment Schedule -->
    @forelse($paymentSchedule as $schedule)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5>
                <i class="fas fa-calendar me-2"></i>
                {{ $schedule['term']->name }} - {{ $schedule['term']->academicYear->year ?? '' }}
                ({{ $schedule['term']->start_date->format('M Y') }} - {{ $schedule['term']->end_date->format('M Y') }})
                <span class="float-end">
                    @if($schedule['status'] === 'paid')
                        <span class="badge bg-success">Fully Paid</span>
                    @elseif($schedule['status'] === 'partial')
                        <span class="badge bg-warning">Partial Payment</span>
                    @else
                        <span class="badge bg-danger">Unpaid</span>
                    @endif
                </span>
            </h5>
        </div>
        <div class="card-body">
            <!-- Term Summary -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Total Required:</strong><br>
                    <span class="text-primary">{{ number_format($schedule['total_required'], 0) }} RWF</span>
                </div>
                <div class="col-md-3">
                    <strong>Total Paid:</strong><br>
                    <span class="text-success">{{ number_format($schedule['total_paid'], 0) }} RWF</span>
                </div>
                <div class="col-md-3">
                    <strong>Remaining:</strong><br>
                    <span class="text-danger">{{ number_format($schedule['remaining'], 0) }} RWF</span>
                </div>
                <div class="col-md-3">
                    <strong>Progress:</strong><br>
                    @php
                        $progress = $schedule['total_required'] > 0 ? 
                                  ($schedule['total_paid'] / $schedule['total_required']) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar @if($progress >= 100) bg-success @elseif($progress > 0) bg-warning @else bg-danger @endif" 
                             role="progressbar" style="width: {{ min($progress, 100) }}%">
                            {{ number_format($progress, 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Breakdown -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6>Fee Breakdown:</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <small>Registration:</small><br>
                            <strong>{{ number_format($schedule['fee_breakdown']['registration']['required'], 0) }} RWF</strong>
                            @if($schedule['fee_breakdown']['registration']['paid'] > 0)
                                <br><small class="text-success">Paid: {{ number_format($schedule['fee_breakdown']['registration']['paid'], 0) }}</small>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <small>Tuition:</small><br>
                            <strong>{{ number_format($schedule['fee_breakdown']['tuition']['required'], 0) }} RWF</strong>
                            @if($schedule['fee_breakdown']['tuition']['paid'] > 0)
                                <br><small class="text-success">Paid: {{ number_format($schedule['fee_breakdown']['tuition']['paid'], 0) }}</small>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <small>Practical:</small><br>
                            <strong>{{ number_format($schedule['fee_breakdown']['practical']['required'], 0) }} RWF</strong>
                            @if($schedule['fee_breakdown']['practical']['paid'] > 0)
                                <br><small class="text-success">Paid: {{ number_format($schedule['fee_breakdown']['practical']['paid'], 0) }}</small>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <small>Trip:</small><br>
                            <strong>{{ number_format($schedule['fee_breakdown']['trip']['required'], 0) }} RWF</strong>
                            @if($schedule['fee_breakdown']['trip']['paid'] > 0)
                                <br><small class="text-success">Paid: {{ number_format($schedule['fee_breakdown']['trip']['paid'], 0) }}</small>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <small>Student Card:</small><br>
                            <strong>{{ number_format($schedule['fee_breakdown']['student_card']['required'], 0) }} RWF</strong>
                            @if($schedule['fee_breakdown']['student_card']['paid'] > 0)
                                <br><small class="text-success">Paid: {{ number_format($schedule['fee_breakdown']['student_card']['paid'], 0) }}</small>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <small>Total:</small><br>
                            <strong class="text-success">{{ number_format($schedule['total_required'], 0) }} RWF</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($schedule['transactions']->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule['transactions'] as $transaction)
                        <tr>
                            <td>{{ $transaction->paid_at ? $transaction->paid_at->format('M d, Y H:i') : $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $transaction->tx_ref }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                @if($transaction->meta)
                                    @php
                                        $meta = json_decode($transaction->meta, true);
                                        if (isset($meta['fee_name'])) {
                                            echo ' - ' . $meta['fee_name'];
                                        }
                                    @endphp
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($transaction->amount, 0) }} RWF</td>
                            <td>
                                @if($transaction->status === 'successful' || $transaction->status === 'paid')
                                    <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center text-muted py-3">
                <i class="fas fa-info-circle me-2"></i>No payment transactions found for this term
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h5>No Payment Schedule Found</h5>
            <p class="text-muted">No payment schedule has been set up for your course yet.</p>
            <p class="text-muted">Please contact the administration for more information.</p>
        </div>
    </div>
    @endforelse
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.3s ease;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
}

.table td {
    vertical-align: middle;
}

@media print {
    .btn, .card-header h5 {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
}
</style>
@endsection
