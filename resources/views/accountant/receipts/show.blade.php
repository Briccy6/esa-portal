@extends('accountant.layout')

@section('title', 'Receipt Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-file-invoice me-2"></i>Receipt Details</h2>
            <p class="text-muted">Transaction ID: {{ $transaction->tx_ref }}</p>
        </div>
        <div>
            <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <a href="{{ route('staff.accountant.receipts.print', $transaction->id) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-print me-1"></i>Print
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Transaction ID</label>
                            <div class="fw-bold">{{ $transaction->tx_ref }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Date</label>
                            <div class="fw-bold">{{ $transaction->created_at->format('F d, Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Amount</label>
                            <div class="fw-bold text-success fs-4">{{ number_format($transaction->amount, 0) }} RWF</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                @if($transaction->status === 'successful' || $transaction->status === 'paid')
                                    <span class="badge bg-success fs-6">{{ ucfirst($transaction->status) }}</span>
                                @else
                                    <span class="badge bg-warning fs-6">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Payment Type</label>
                            <div>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Payment Method</label>
                            <div>
                                @php
                                    $meta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true);
                                    echo $meta['method'] ?? 'Unknown';
                                @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Name</label>
                            <div class="fw-bold">{{ $transaction->student->full_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Registration Number</label>
                            <div>{{ $transaction->student->reg_number }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Course</label>
                            <div>{{ $transaction->student->course->course_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Branch</label>
                            <div>{{ $transaction->student->branch->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($transaction->courseTermFee)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Fee Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Term</label>
                            <div>{{ $transaction->courseTermFee->term->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Academic Year</label>
                            <div>{{ $transaction->courseTermFee->term->academicYear->year ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('staff.accountant.receipts.print', $transaction->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </a>
                        <a href="{{ route('staff.accountant.payment-schedule.student-details', $transaction->student->id) }}" class="btn btn-info">
                            <i class="fas fa-user me-2"></i>View Student Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
