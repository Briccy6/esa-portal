@extends('staff.partials.sidebar_sec')

@section('title', 'Expense Details')

@include('accountant.partials.theme')

@section('content')
<div class="container-fluid py-4 accountant-module">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Finance management</p>
            <h1 class="h3 fw-bold mb-1">Expense Details</h1>
            <p class="text-muted mb-0">Review the recorded transaction information.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Expenses</a>
            <a href="{{ route('staff.accountant.expenses.edit', $expense->id) }}" class="btn btn-primary"><i class="fas fa-pen me-2" aria-hidden="true"></i>Edit Expense</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Expense information</h2></div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Date</div><div class="fw-semibold">{{ \Carbon\Carbon::parse($expense->expense_date)->format('F d, Y') }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Expense Type</div><div class="fw-semibold">{{ $expense->expense_type }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Amount</div><div class="h4 text-danger fw-bold mb-0">{{ number_format($expense->amount, 0) }} RWF</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Payment Method</div><div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</div></div>
                        <div class="col-12"><div class="small text-muted mb-1">Description</div><div class="text-break">{{ $expense->description }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Paid To</div><div class="fw-semibold">{{ $expense->paid_to }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Contact</div><div>{{ $expense->paid_to_contact }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Branch</div><div>{{ $expense->branch?->name ?? '—' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Recorded By</div><div>{{ $expense->paid_by_staff->name ?? 'Unknown' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted mb-1">Term</div><div>{{ $expense->term ? $expense->term->name . ($expense->term->academicYear ? ' - ' . $expense->term->academicYear->year : '') : 'Not specified' }}</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Actions</h2></div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('staff.accountant.expenses.edit', $expense->id) }}" class="btn btn-primary"><i class="fas fa-pen me-2" aria-hidden="true"></i>Edit Expense</a>
                    <form method="POST" action="{{ route('staff.accountant.expenses.destroy', $expense->id) }}" onsubmit="return confirm('Delete this expense?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100"><i class="fas fa-trash me-2" aria-hidden="true"></i>Delete Expense</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
