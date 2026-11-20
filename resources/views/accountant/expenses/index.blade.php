@extends('staff.partials.sidebar_sec')

@section('title', 'Expenses')

@include('accountant.partials.theme')

@section('content')
<div class="container-fluid py-4 accountant-module">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Finance management</p>
            <h1 class="h3 fw-bold mb-1">Expenses</h1>
            <p class="text-muted mb-0">Review and manage recorded school expenses.</p>
        </div>
        <a href="{{ route('staff.accountant.expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2" aria-hidden="true"></i>Add Expense
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Please correct the following before saving:</div>
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0"><i class="fas fa-filter text-primary me-2" aria-hidden="true"></i>Filter expenses</h2></div>
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.expenses.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="academic_year_id" class="form-label small text-muted">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected(request('academic_year_id') == $academicYear->id)>{{ $academicYear->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="branch_id" class="form-label small text-muted">Branch</label>
                    <select id="branch_id" name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="term_id" class="form-label small text-muted">Term</label>
                    <select id="term_id" name="term_id" class="form-select">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" @selected(request('term_id') == $term->id)>{{ $term->name }}{{ $term->academicYear ? ' - ' . $term->academicYear->year : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="expense_type" class="form-label small text-muted">Expense Type</label>
                    <input id="expense_type" type="search" name="expense_type" class="form-control" placeholder="Search type" value="{{ request('expense_type') }}">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1" aria-label="Apply filters"><i class="fas fa-filter" aria-hidden="true"></i></button>
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary" aria-label="Clear filters"><i class="fas fa-times" aria-hidden="true"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-semibold mb-0">Expense records</h2>
            <span class="text-muted small">{{ $expenses->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Date</th><th>Type</th><th>Description</th><th>Paid To</th><th>Branch</th><th>Amount</th><th>Method</th><th>Term</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="text-nowrap">{{ is_string($expense->expense_date) ? \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') : $expense->expense_date->format('M d, Y') }}</td>
                            <td><span class="badge text-bg-light border">{{ $expense->expense_type }}</span></td>
                            <td class="text-truncate" style="max-width: 220px" title="{{ $expense->description }}">{{ $expense->description }}</td>
                            <td>{{ $expense->paid_to }}</td>
                            <td><small class="text-muted">{{ $expense->branch?->name ?? '—' }}</small></td>
                            <td class="fw-semibold text-danger text-nowrap">{{ number_format($expense->amount, 0) }} RWF</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                            <td>{{ $expense->term?->name ?? '—' }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Expense actions">
                                    <a href="{{ route('staff.accountant.expenses.show', $expense->id) }}" class="btn btn-outline-secondary" title="View" aria-label="View expense"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('staff.accountant.expenses.edit', $expense->id) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit expense"><i class="fas fa-pen" aria-hidden="true"></i></a>
                                    <form method="POST" action="{{ route('staff.accountant.expenses.destroy', $expense->id) }}" onsubmit="return confirm('Delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-start-0" title="Delete" aria-label="Delete expense"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-5"><i class="fas fa-file-invoice-dollar fa-2x d-block mb-2" aria-hidden="true"></i><div class="fw-semibold">No expenses found</div><small>Try changing the filters or add a new expense.</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white">{{ $expenses->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
