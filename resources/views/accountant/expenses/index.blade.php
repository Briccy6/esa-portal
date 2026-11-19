@extends('accountant.layout')

@section('title', 'Expenses')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-receipt me-2"></i>Expenses</h2>
            <p class="text-muted">Manage and track school expenses</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fas fa-plus me-1"></i>Add Expense
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.expenses.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Search Type</label>
                    <input type="text" name="expense_type" class="form-control form-control-sm" placeholder="e.g. Stationery" value="{{ request('expense_type') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Term</label>
                    <select name="term_id" class="form-select form-select-sm">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('staff.accountant.expenses.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Paid To</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Term</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td><strong>{{ $expense->expense_type }}</strong></td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->paid_to }}</td>
                            <td class="fw-bold text-danger">{{ number_format($expense->amount, 0) }} RWF</td>
                            <td>{{ ucfirst($expense->payment_method) }}</td>
                            <td>{{ $expense->term ? $expense->term->name : 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.accountant.expenses.show', $expense->id) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.accountant.expenses.edit', $expense->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('staff.accountant.expenses.destroy', $expense->id) }}" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-receipt fa-3x mb-3 d-block"></i>
                                No expenses found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $expenses->links() }}
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('expenses.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Add Expense</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="expense_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <input type="text" name="expense_type" class="form-control" required placeholder="e.g. Stationery, Utilities">
          </div>
          <div class="mb-3">
            <label class="form-label">Amount (RWF)</label>
            <input type="number" name="amount" class="form-control" min="1" step="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Paid To</label>
            <input type="text" name="paid_to" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="paid_to_contact" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" required>
              <option value="">Select method</option>
              <option value="cash">Cash</option>
              <option value="bank">Bank</option>
              <option value="mobile_money">Mobile Money</option>
              <option value="cheque">Cheque</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Term (Optional)</label>
            <select name="term_id" class="form-select">
              <option value="">Select Term</option>
              @foreach($terms as $term)
                  <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->academicYear->year ?? '' }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Save Expense</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
