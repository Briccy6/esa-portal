@extends('admin.layout')

@section('title', 'Student Fee Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-user me-2"></i>Student Fee Details - {{ $student->full_name }}</h2>
            <p class="text-muted">Complete fee breakdown and payment history</p>
        </div>
        <div>
            <a href="{{ route('accounting.student-fees') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Students
            </a>
            <a href="{{ route('accounting.payment-transcript', $student->id) }}" class="btn btn-info">
                <i class="fas fa-file-invoice me-1"></i> Payment Transcript
            </a>
        </div>
    </div>

    <!-- Student Information -->
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
                    {{ $student->course->course_name ?? 'N/A' }} ({{ $student->course->abbreviation ?? '' }})
                </div>
                <div class="col-md-3">
                    <strong>Course Code:</strong><br>
                    {{ $student->course->course_code ?? 'N/A' }}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong>Branch:</strong><br>
                    {{ $student->branch->name ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Academic Year:</strong><br>
                    {{ $student->academicYear->year ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Email:</strong><br>
                    {{ $student->email }}
                </div>
                <div class="col-md-3">
                    <strong>Phone:</strong><br>
                    {{ $student->phone }}
                </div>
            </div>
        </div>
    </div>

    <!-- Current Term Fees -->
    @if($currentFees->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5><i class="fas fa-calendar-check me-2"></i>Current Term Fees</h5>
            </div>
            <div class="card-body">
                @foreach($currentFees as $termFee)
                    @php
                        $transactions = $transactions->where('term_id', $termFee->term_id);
                        $paid = $transactions->where('status', 'successful')->sum('amount');
                        $outstanding = max(0, $termFee->total_fees - $paid);
                    @endphp
                    <div class="mb-4 p-3 border rounded">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">
                                    {{ $termFee->term->name }} - {{ $termFee->term->academicYear->year ?? '' }}
                                </h6>
                                <small class="text-muted">
                                    {{ $termFee->term->start_date->format('M Y') }} - {{ $termFee->term->end_date->format('M Y') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                @if($outstanding > 0)
                                    <span class="badge bg-danger fs-6">Outstanding: {{ number_format($outstanding, 0) }} RWF</span>
                                @else
                                    <span class="badge bg-success fs-6">Fully Paid</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-2">
                                <small>Registration:</small><br>
                                <strong>{{ number_format($termFee->registration_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Tuition:</small><br>
                                <strong>{{ number_format($termFee->tuition_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Practical:</small><br>
                                <strong>{{ number_format($termFee->practical_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Trip:</small><br>
                                <strong>{{ number_format($termFee->trip_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Student Card:</small><br>
                                <strong>{{ number_format($termFee->student_card_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Total:</small><br>
                                <strong class="text-success">{{ number_format($termFee->total_fees, 0) }} RWF</strong>
                            </div>
                        </div>
                        
                        @if($transactions->count() > 0)
                            <div class="mt-3">
                                <h6>Payment History:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Reference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</td>
                                                    <td>{{ number_format($transaction->amount, 0) }} RWF</td>
                                                    <td>
                                                        @if($transaction->status === 'successful' || $transaction->status === 'paid')
                                                            <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span>
                                                        @else
                                                            <span class="badge bg-danger">{{ ucfirst($transaction->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $transaction->tx_ref ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Upcoming Term Fees -->
    @if($upcomingFees->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h5><i class="fas fa-calendar-alt me-2"></i>Upcoming Term Fees</h5>
            </div>
            <div class="card-body">
                @foreach($upcomingFees as $termFee)
                    <div class="mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-warning">
                                    {{ $termFee->term->name }} - {{ $termFee->term->academicYear->year ?? '' }}
                                </h6>
                                <small class="text-muted">
                                    {{ $termFee->term->start_date->format('M Y') }} - {{ $termFee->term->end_date->format('M Y') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="badge bg-warning fs-6">Total: {{ number_format($termFee->total_fees, 0) }} RWF</span>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <small>Registration:</small><br>
                                <strong>{{ number_format($termFee->registration_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Tuition:</small><br>
                                <strong>{{ number_format($termFee->tuition_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Practical:</small><br>
                                <strong>{{ number_format($termFee->practical_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Trip:</small><br>
                                <strong>{{ number_format($termFee->trip_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Student Card:</small><br>
                                <strong>{{ number_format($termFee->student_card_fee, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Total:</small><br>
                                <strong class="text-warning">{{ number_format($termFee->total_fees, 0) }} RWF</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Past Term Fees -->
    @if($pastFees->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5><i class="fas fa-history me-2"></i>Past Term Fees</h5>
            </div>
            <div class="card-body">
                @foreach($pastFees as $termFee)
                    @php
                        $pastTransactions = $transactions->where('term_id', $termFee->term_id);
                        $paid = $pastTransactions->where('status', 'successful')->sum('amount');
                        $outstanding = max(0, $termFee->total_fees - $paid);
                    @endphp
                    <div class="mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    {{ $termFee->term->name }} - {{ $termFee->term->academicYear->year ?? '' }}
                                </h6>
                                <small class="text-muted">
                                    {{ $termFee->term->start_date->format('M Y') }} - {{ $termFee->term->end_date->format('M Y') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                @if($outstanding > 0)
                                    <span class="badge bg-danger fs-6">Outstanding: {{ number_format($outstanding, 0) }} RWF</span>
                                @else
                                    <span class="badge bg-success fs-6">Fully Paid</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <small>Total Required:</small><br>
                                <strong>{{ number_format($termFee->total_fees, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Total Paid:</small><br>
                                <strong class="text-success">{{ number_format($paid, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-2">
                                <small>Outstanding:</small><br>
                                <strong class="text-danger">{{ number_format($outstanding, 0) }} RWF</strong>
                            </div>
                            <div class="col-md-6">
                                <small>Payment Count:</small><br>
                                <strong>{{ $pastTransactions->count() }} transaction(s)</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Summary Statistics -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-chart-bar me-2"></i>Fee Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-success">{{ $currentFees->count() }}</h4>
                        <small>Current Terms</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-warning">{{ $upcomingFees->count() }}</h4>
                        <small>Upcoming Terms</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-primary">{{ number_format($currentFees->sum('total_fees'), 0) }}</h4>
                        <small>Current Total (RWF)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-info">{{ number_format($upcomingFees->sum('total_fees'), 0) }}</h4>
                        <small>Upcoming Total (RWF)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.card-header {
    border: none;
    border-radius: 15px 15px 0 0 !important;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

.table th {
    border: none;
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
