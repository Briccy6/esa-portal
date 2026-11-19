@extends('staff.partials.sidebar_sec')

@section('title', 'Add Expense')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add Expense</h2>
        <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Expenses
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('staff.accountant.expenses.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expense Type *</label>
                        <input type="text" name="expense_type" class="form-control" required placeholder="e.g., Salary, Utilities, Maintenance">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Amount (RWF) *</label>
                        <input type="number" name="amount" class="form-control" required min="1" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Term</label>
                        <select name="term_id" class="form-select">
                            <option value="">Select Term (Optional)</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->academicYear->year ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Enter expense description"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Paid To *</label>
                        <input type="text" name="paid_to" class="form-control" required placeholder="Name of payee">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact *</label>
                        <input type="text" name="paid_to_contact" class="form-control" required placeholder="Phone number or email">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Check">Check</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Expense
                        </button>
                        <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
