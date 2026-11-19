@extends('accountant.layout')

@section('title', 'Expense Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-receipt me-2"></i>Expense Details</h2>
            <p class="text-muted">View expense information</p>
        </div>
        <div>
            <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <a href="{{ route('staff.accountant.expenses.edit', $expense->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Expense Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Date</label>
                            <div class="fw-bold">{{ $expense->expense_date->format('F d, Y') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Type</label>
                            <div class="fw-bold">{{ $expense->expense_type }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Amount</label>
                            <div class="fw-bold text-danger fs-4">{{ number_format($expense->amount, 0) }} RWF</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Payment Method</label>
                            <div class="fw-bold">{{ ucfirst($expense->payment_method) }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="small text-muted">Description</label>
                            <div>{{ $expense->description }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Paid To</label>
                            <div class="fw-bold">{{ $expense->paid_to }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Contact</label>
                            <div>{{ $expense->paid_to_contact }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Recorded By</label>
                            <div>{{ $expense->paid_by_staff->name ?? 'Unknown' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Term</label>
                            <div>{{ $expense->term ? $expense->term->name . ' - ' . $expense->term->academicYear->year : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('staff.accountant.expenses.edit', $expense->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Expense
                        </a>
                        <form method="POST" action="{{ route('staff.accountant.expenses.destroy', $expense->id) }}" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete Expense
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
