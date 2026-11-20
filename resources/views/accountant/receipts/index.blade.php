@extends('staff.partials.sidebar_sec')

@section('title', 'Payment Receipts')

@include('accountant.partials.theme')

@section('content')
<div class="container-fluid py-4 accountant-module">
    <div class="mb-4">
        <p class="text-primary fw-semibold small text-uppercase mb-1">Finance management</p>
        <h1 class="h3 fw-bold mb-1">Payment Receipts</h1>
        <p class="text-muted mb-0">Find transactions and view or print their receipts.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0"><i class="fas fa-filter text-primary me-2" aria-hidden="true"></i>Filter receipts</h2></div>
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.receipts.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="student_id" class="form-label small text-muted">Student ID</label>
                    <input id="student_id" type="number" min="1" name="student_id" class="form-control" placeholder="Enter student ID" value="{{ request('student_id') }}">
                </div>
                <div class="col-12 col-md-2">
                    <label for="status" class="form-label small text-muted">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach(['successful' => 'Successful', 'paid' => 'Paid', 'pending' => 'Pending'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="start_date" class="form-label small text-muted">Start Date</label>
                    <input id="start_date" type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-12 col-md-2">
                    <label for="end_date" class="form-label small text-muted">End Date</label>
                    <input id="end_date" type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1" aria-hidden="true"></i>Apply filters</button>
                    <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-semibold mb-0">Transactions</h2>
            <span class="text-muted small">{{ $transactions->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Transaction</th><th>Student</th><th>Course</th><th>Amount</th><th>Date</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td><span class="font-monospace small">{{ $transaction->tx_ref }}</span></td>
                            <td><div class="fw-semibold">{{ $transaction->student->full_name ?? 'Unknown student' }}</div><small class="text-muted">{{ $transaction->student->reg_number ?? 'No registration number' }}</small></td>
                            <td>{{ $transaction->student->course->course_name ?? '—' }}</td>
                            <td class="fw-semibold text-success text-nowrap">{{ number_format($transaction->amount, 0) }} RWF</td>
                            <td class="text-nowrap">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if(in_array($transaction->status, ['successful', 'paid'], true))
                                    <span class="badge text-bg-success">{{ ucfirst($transaction->status) }}</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="badge text-bg-warning">{{ ucfirst($transaction->status) }}</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Receipt actions">
                                    <a href="{{ route('staff.accountant.receipts.show', $transaction->id) }}" class="btn btn-outline-primary" title="View receipt" aria-label="View receipt"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('staff.accountant.receipts.print', $transaction->id) }}" class="btn btn-outline-secondary" title="Print receipt" aria-label="Print receipt"><i class="fas fa-print" aria-hidden="true"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-file-invoice fa-2x d-block mb-2" aria-hidden="true"></i><div class="fw-semibold">No receipts found</div><small>Try adjusting your filters.</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white">{{ $transactions->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
