@extends('staff.partials.sidebar_sec')

@section('title', 'Edit Expense')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-edit me-2"></i>Edit Expense</h2>
            <p class="text-muted">Update expense information</p>
        </div>
        <a href="{{ route('staff.accountant.expenses.show', $expense->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('staff.accountant.expenses.update', $expense->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" name="expense_type" class="form-control" value="{{ $expense->expense_type }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount (RWF)</label>
                        <input type="number" name="amount" class="form-control" value="{{ $expense->amount }}" min="1" step="0.01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" {{ $expense->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ $expense->payment_method === 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="mobile_money" {{ $expense->payment_method === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ $expense->payment_method === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="other" {{ $expense->payment_method === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ $expense->description }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Paid To</label>
                        <input type="text" name="paid_to" class="form-control" value="{{ $expense->paid_to }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="paid_to_contact" class="form-control" value="{{ $expense->paid_to_contact }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Term (Optional)</label>
                        <select name="term_id" class="form-select">
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $expense->term_id == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Expense
                    </button>
                    <a href="{{ route('staff.accountant.expenses.show', $expense->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
