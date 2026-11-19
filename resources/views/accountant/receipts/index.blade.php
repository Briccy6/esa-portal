@extends('staff.partials.sidebar_sec')

@section('title', 'Receipts')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-file-invoice me-2"></i>Receipts</h2>
            <p class="text-muted">View and print payment receipts</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.receipts.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Student</label>
                    <input type="text" name="student_search" class="form-control form-control-sm" placeholder="Name or Reg Number" value="{{ request('student_search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
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
                    <a href="{{ route('staff.accountant.receipts.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td><small>{{ $transaction->tx_ref }}</small></td>
                            <td>
                                <div class="fw-bold">{{ $transaction->student->full_name }}</div>
                                <small class="text-muted">{{ $transaction->student->reg_number }}</small>
                            </td>
                            <td>{{ $transaction->student->course->course_name ?? 'N/A' }}</td>
                            <td class="fw-bold text-success">{{ number_format($transaction->amount, 0) }} RWF</td>
                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($transaction->status === 'successful' || $transaction->status === 'paid')
                                    <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.accountant.receipts.show', $transaction->id) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.accountant.receipts.print', $transaction->id) }}" class="btn btn-outline-primary" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                                No receipts found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
