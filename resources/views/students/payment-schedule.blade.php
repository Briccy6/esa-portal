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

    <!-- Full Year Fees Summary -->
    <div class="card mb-4 bg-gradient-primary text-white">
        <div class="card-header">
            <h5><i class="fas fa-calendar-year me-2"></i>Full Year Fees Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4>{{ number_format(collect($paymentSchedule)->sum('total_required'), 0) }}</h4>
                        <small>Total Year Fees</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4>{{ number_format(collect($paymentSchedule)->sum('total_paid'), 0) }}</h4>
                        <small>Total Paid</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4>{{ number_format(collect($paymentSchedule)->sum('remaining'), 0) }}</h4>
                        <small>Total Outstanding</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        @php
                            $totalRequired = collect($paymentSchedule)->sum('total_required');
                            $totalPaid = collect($paymentSchedule)->sum('total_paid');
                            $paymentProgress = $totalRequired > 0 ? ($totalPaid / $totalRequired) * 100 : 0;
                        @endphp
                        <h4>{{ number_format($paymentProgress, 1) }}%</h4>
                        <small>Payment Progress</small>
                    </div>
                </div>
            </div>
            
            <!-- Overall Progress Bar -->
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <small>Overall Payment Progress</small>
                    <small>{{ number_format($paymentProgress, 1) }}%</small>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min($paymentProgress, 100) }}%">
                        {{ number_format($paymentProgress, 1) }}% Complete
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
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
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format(collect($paymentSchedule)->sum('total_paid'), 0) }}</h4>
                    <small>Total Paid (RWF)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Year Overview -->
    @if(count($paymentSchedule) > 0)
        @php
            $academicYears = collect($paymentSchedule)->groupBy(function($schedule) {
                return $schedule['term']->academicYear->year ?? 'Unknown Year';
            });
        @endphp
        
        @foreach($academicYears as $year => $yearSchedule)
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5>
                        <i class="fas fa-graduation-cap me-2"></i>
                        Academic Year {{ $year }}
                        <span class="float-end">
                            @php
                                $yearTotal = collect($yearSchedule)->sum('total_required');
                                $yearPaid = collect($yearSchedule)->sum('total_paid');
                                $yearProgress = $yearTotal > 0 ? ($yearPaid / $yearTotal) * 100 : 0;
                            @endphp
                            <span class="badge bg-light text-primary">{{ number_format($yearProgress, 1) }}% Complete</span>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="text-center p-2 bg-light rounded">
                                <h5 class="text-primary">{{ number_format(collect($yearSchedule)->sum('total_required'), 0) }}</h5>
                                <small>Year Total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-2 bg-light rounded">
                                <h5 class="text-success">{{ number_format(collect($yearSchedule)->sum('total_paid'), 0) }}</h5>
                                <small>Year Paid</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-2 bg-light rounded">
                                <h5 class="text-danger">{{ number_format(collect($yearSchedule)->sum('remaining'), 0) }}</h5>
                                <small>Year Outstanding</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-2 bg-light rounded">
                                <h5 class="text-info">{{ count($yearSchedule) }}</h5>
                                <small>Terms</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Year Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Year Payment Progress</small>
                            <small>{{ number_format($yearProgress, 1) }}%</small>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min($yearProgress, 100) }}%">
                                {{ number_format($yearProgress, 1) }}% of {{ number_format($yearTotal, 0) }} RWF
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms in this year -->
                    <div class="row">
                        @foreach($yearSchedule as $schedule)
                            <div class="col-md-6 mb-2">
                                <div class="p-2 border rounded @if($schedule['status'] === 'paid') border-success @elseif($schedule['status'] === 'partial') border-warning @else border-danger @endif">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $schedule['term']->name }}</strong><br>
                                            <small>{{ number_format($schedule['total_required'], 0) }} RWF</small>
                                        </div>
                                        <div class="text-end">
                                            @if($schedule['status'] === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($schedule['status'] === 'partial')
                                                <span class="badge bg-warning">Partial</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

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
                                        $meta = is_array($transaction->meta) ? $transaction->meta : (json_decode($transaction->meta, true) ?? []);
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

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
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

/* Enhanced Year Overview Styles */
.border-primary {
    border-width: 2px !important;
}

.border-success {
    border-color: #28a745 !important;
    background-color: rgba(40, 167, 69, 0.05);
}

.border-warning {
    border-color: #ffc107 !important;
    background-color: rgba(255, 193, 7, 0.05);
}

.border-danger {
    border-color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.05);
}

/* Full Year Summary Enhancement */
.bg-gradient-primary .card-body h4 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.bg-gradient-primary .card-body small {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Year Overview Enhancement */
.card-header.bg-primary h5 {
    font-size: 1.25rem;
    font-weight: 600;
}

.card-header.bg-primary .badge {
    font-size: 0.8rem;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body .row > div {
        margin-bottom: 1rem;
    }
    
    .bg-gradient-primary .card-body h4 {
        font-size: 1.5rem;
    }
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
    
    .bg-gradient-primary {
        background: #667eea !important;
        -webkit-print-color-adjust: exact;
    }
    
    .card-header {
        background: #667eea !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endsection
