@extends('staff.partials.sidebar_sec')

@section('title', 'Receipt Details')

@include('accountant.partials.theme')

@section('content')
@php
    $transactionMeta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta ?? '', true);
    $isPaid = in_array($transaction->status, ['successful', 'paid'], true);
@endphp
<div class="container-fluid py-4 accountant-module">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Payment receipts</p>
            <h1 class="h3 fw-bold mb-1">Receipt Details</h1>
            <p class="text-muted mb-0">Transaction <span class="font-monospace">{{ $transaction->tx_ref }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Receipts</a>
            <a href="{{ route('staff.accountant.receipts.print', $transaction->id) }}" class="btn btn-primary" target="_blank" rel="noopener"><i class="fas fa-print me-2" aria-hidden="true"></i>Print Receipt</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Payment details</h2></div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Transaction ID</div><div class="fw-semibold font-monospace text-break">{{ $transaction->tx_ref }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Date</div><div class="fw-semibold">{{ $transaction->created_at->format('F d, Y H:i') }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Amount</div><div class="h4 text-success fw-bold mb-0">{{ number_format($transaction->amount, 0) }} RWF</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Status</div><span class="badge {{ $isPaid ? 'text-bg-success' : ($transaction->status === 'pending' ? 'text-bg-warning' : 'text-bg-secondary') }}">{{ ucfirst($transaction->status) }}</span></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Payment Type</div><div>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Payment Method</div><div>{{ $transactionMeta['method'] ?? 'Unknown' }}</div></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Student information</h2></div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Name</div><div class="fw-semibold">{{ $transaction->student->full_name ?? 'Unknown student' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Registration Number</div><div>{{ $transaction->student->reg_number ?? '—' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Course</div><div>{{ $transaction->student->course->course_name ?? '—' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Branch</div><div>{{ $transaction->student->branch->name ?? '—' }}</div></div>
                    </div>
                </div>
            </div>

            @if($transaction->courseTermFee)
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Fee period</h2></div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-6"><div class="small text-muted mb-1">Term</div><div>{{ $transaction->courseTermFee->term->name ?? '—' }}</div></div>
                            <div class="col-12 col-md-6"><div class="small text-muted mb-1">Academic Year</div><div>{{ $transaction->courseTermFee->term->academicYear->year ?? '—' }}</div></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Quick actions</h2></div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('staff.accountant.receipts.print', $transaction->id) }}" class="btn btn-primary" target="_blank" rel="noopener"><i class="fas fa-print me-2" aria-hidden="true"></i>Print Receipt</a>
                    @if($transaction->student)
                        <a href="{{ route('staff.accountant.payment-schedule.student-details', $transaction->student->id) }}" class="btn btn-outline-primary"><i class="fas fa-user me-2" aria-hidden="true"></i>View Student Payments</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
