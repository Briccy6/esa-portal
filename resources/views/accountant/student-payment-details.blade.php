@extends('accountant.layout')

@section('title', 'Student Payment Details - ' . $student->full_name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-user me-2"></i>{{ $student->full_name }} Payment History</h2>
            <p class="text-muted">{{ $student->reg_number }} - Complete payment history by term</p>
        </div>
        <div>
            <a href="{{ route('staff.accountant.payment-schedule.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Schedule
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Student Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-user-circle me-2"></i>Student Information</h5>
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
                    {{ $student->course->course_name }} ({{ $student->course->abbreviation }})
                </div>
                <div class="col-md-3">
                    <strong>Course Code:</strong><br>
                    {{ $student->course->course_code }}
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ count($paymentHistory) }}</h4>
                    <small>Active Terms</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ collect($paymentHistory)->where('status', 'paid')->count() }}</h4>
                    <small>Fully Paid Terms</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ collect($paymentHistory)->where('status', 'partial')->count() }}</h4>
                    <small>Partial Payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format(collect($paymentHistory)->sum('total_paid'), 0) }}</h4>
                    <small>Total Paid (RWF)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Term-wise Payment History -->
    @foreach($paymentHistory as $history)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5>
                <i class="fas fa-calendar me-2"></i>
                {{ $history['term']->name }} - {{ $history['term']->academicYear->year ?? '' }}
                ({{ $history['term']->start_date->format('M Y') }} - {{ $history['term']->end_date->format('M Y') }})
                <span class="float-end">
                    @if($history['status'] === 'paid')
                        <span class="badge bg-success">Fully Paid</span>
                    @elseif($history['status'] === 'partial')
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
                    <span class="text-primary">{{ number_format($history['total_required'], 0) }} RWF</span>
                </div>
                <div class="col-md-3">
                    <strong>Total Paid:</strong><br>
                    <span class="text-success">{{ number_format($history['total_paid'], 0) }} RWF</span>
                </div>
                <div class="col-md-3">
                    <strong>Remaining:</strong><br>
                    <span class="text-danger">{{ number_format($history['remaining'], 0) }} RWF</span>
                </div>
                <div class="col-md-3">
                    <strong>Progress:</strong><br>
                    @php
                        $progress = $history['total_required'] > 0 ? 
                                  ($history['total_paid'] / $history['total_required']) * 100 : 0;
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
                    <h6>Fee Structure:</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <small>Registration:</small><br>
                            <strong>{{ number_format($history['term_fee']->registration_fee, 0) }} RWF</strong>
                        </div>
                        <div class="col-md-2">
                            <small>Tuition:</small><br>
                            <strong>{{ number_format($history['term_fee']->tuition_fee, 0) }} RWF</strong>
                        </div>
                        <div class="col-md-2">
                            <small>Practical:</small><br>
                            <strong>{{ number_format($history['term_fee']->practical_fee, 0) }} RWF</strong>
                        </div>
                        <div class="col-md-2">
                            <small>Trip:</small><br>
                            <strong>{{ number_format($history['term_fee']->trip_fee, 0) }} RWF</strong>
                        </div>
                        <div class="col-md-2">
                            <small>Student Card:</small><br>
                            <strong>{{ number_format($history['term_fee']->student_card_fee, 0) }} RWF</strong>
                        </div>
                        <div class="col-md-2">
                            <small>Total:</small><br>
                            <strong class="text-success">{{ number_format($history['term_fee']->total_fees, 0) }} RWF</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Transactions -->
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($history['transactions']->count() > 0)
                            @foreach($history['transactions'] as $transaction)
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
                                    @php
                                        $meta = is_array($transaction->meta) ? $transaction->meta : (json_decode($transaction->meta, true) ?? []);
                                        echo $meta['method'] ?? 'Unknown';
                                    @endphp
                                </td>
                                <td>
                                    @if($transaction->status === 'successful' || $transaction->status === 'paid')
                                        <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $meta = json_decode($transaction->meta, true);
                                        echo $meta['recorded_by'] ?? 'System';
                                    @endphp
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No payment transactions found for this term
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if($history['remaining'] > 0)
            <div class="text-center mt-3">
                <button onclick="recordPayment({{ $student->id }}, {{ $history['term']->id }})" 
                        class="btn btn-success">
                    <i class="fas fa-money-bill me-1"></i>Record Payment for this Term
                </button>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

<script>
function recordPayment(studentId, termId) {
    // Redirect to payment recording with pre-selected student and term
    window.location.href = `/students/${studentId}/pay-cash?term_id=${termId}`;
}
</script>

<style>
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
