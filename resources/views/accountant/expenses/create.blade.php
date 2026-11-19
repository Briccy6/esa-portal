@extends('staff.partials.sidebar_sec')

@section('title', 'Add Expense')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Finance management</p>
            <h1 class="h3 fw-bold mb-1">Add Expense</h1>
            <p class="text-muted mb-0">Record a school expense and its payment details.</p>
        </div>
        <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Back to Expenses</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Please review the highlighted fields.</div>
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0">Expense information</h2></div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('staff.accountant.expenses.store') }}" id="expenseForm">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                        <input id="expense_date" type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required>
                        @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="expense_type" class="form-label">Expense Type <span class="text-danger">*</span></label>
                        <input id="expense_type" type="text" name="expense_type" class="form-control @error('expense_type') is-invalid @enderror" value="{{ old('expense_type') }}" placeholder="e.g. Utilities, Supplies" required>
                        @error('expense_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="amount" class="form-label">Amount (RWF) <span class="text-danger">*</span></label>
                        <input id="amount" type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="1" step="0.01" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select id="branch_id" name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="term_id" class="form-label">Term <span class="text-muted">(optional)</span></label>
                        <select id="term_id" name="term_id" class="form-select @error('term_id') is-invalid @enderror">
                            <option value="">Select term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" @selected(old('term_id') == $term->id)>{{ $term->name }}{{ $term->academicYear ? ' - ' . $term->academicYear->year : '' }}</option>
                            @endforeach
                        </select>
                        @error('term_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="paid_to" class="form-label">Paid To <span class="text-danger">*</span></label>
                        <input id="paid_to" type="text" name="paid_to" class="form-control @error('paid_to') is-invalid @enderror" value="{{ old('paid_to') }}" required>
                        @error('paid_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="paid_to_contact" class="form-label">Contact <span class="text-danger">*</span></label>
                        <input id="paid_to_contact" type="text" name="paid_to_contact" class="form-control @error('paid_to_contact') is-invalid @enderror" value="{{ old('paid_to_contact') }}" required>
                        @error('paid_to_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select method</option>
                            @foreach(['cash' => 'Cash', 'bank' => 'Bank', 'mobile_money' => 'Mobile Money', 'cheque' => 'Cheque', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="saveExpenseButton"><i class="fas fa-save me-2" aria-hidden="true"></i>Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('expenseForm').addEventListener('submit', function () {
    const button = document.getElementById('saveExpenseButton');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Saving';
});
</script>
@endpush
