@extends('admin.layout')

@section('title', 'Cash Book Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book mr-2"></i>Cash Book Report
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.accounting.export.cashbook', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download mr-1"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('admin.accounting.cashbook') }}" class="form-inline">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-md-auto">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.accounting.cashbook') }}" class="btn btn-secondary">
                                            <i class="fas fa-times mr-1"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-white mb-0">Total Income</h6>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-white mb-0">{{ number_format($data['total_receipts'], 2) }} RWF</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-white mb-0">Total Expenses</h6>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-white mb-0">{{ number_format($data['total_payments'], 2) }} RWF</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $data['net_cash'] >= 0 ? 'bg-primary' : 'bg-warning' }} border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-white mb-0">Net Cash</h6>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-white mb-0">{{ number_format($data['net_cash'], 2) }} RWF</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center" width="5%">#</th>
                                    <th width="12%">Date</th>
                                    <th width="15%">Reference</th>
                                    <th width="10%">Type</th>
                                    <th width="25%">Description</th>
                                    <th width="18%">Student/Recipient</th>
                                    <th class="text-right" width="10%">Amount</th>
                                    <th class="text-right" width="10%">Running Balance</th>
                                    <th class="text-center" width="5%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['transactions'] as $index => $transaction)
                                    <tr class="{{ $transaction->direction == '+' ? 'table-success' : 'table-danger' }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->entry_date)->format('M d, Y') }}</td>
                                        <td>
                                            <code class="small">{{ $transaction->tx_ref }}</code>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $transaction->direction == '+' ? 'success' : 'danger' }}">
                                                {{ $transaction->entry_type }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>
                                            <span class="{{ $transaction->direction == '+' ? 'text-success' : 'text-danger' }}">
                                                <i class="fas fa-{{ $transaction->direction == '+' ? 'user' : 'building' }} mr-1"></i>
                                                {{ $transaction->transaction_type }}
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-{{ $transaction->direction == '+' ? 'bold text-success' : 'bold text-danger' }}">
                                            @if($transaction->direction == '+')
                                                <i class="fas fa-plus-circle mr-1"></i>
                                            @else
                                                <i class="fas fa-minus-circle mr-1"></i>
                                            @endif
                                            {{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            {{ number_format($transaction->running_balance, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $transaction->status == 'Posted' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <h5>No transactions found for the selected period.</h5>
                                            <p class="mb-0">Try adjusting the date filters or check if transactions have been recorded.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($data['transactions']->count() > 0)
                                <tfoot class="table-active">
                                    <tr class="font-weight-bold">
                                        <td colspan="7" class="text-right">Totals:</td>
                                        <td class="text-right text-success">
                                            <i class="fas fa-plus-circle mr-1"></i>
                                            {{ number_format($data['total_receipts'], 2) }}
                                        </td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td colspan="7" class="text-right">Total Expenses:</td>
                                        <td class="text-right text-danger">
                                            <i class="fas fa-minus-circle mr-1"></i>
                                            {{ number_format($data['total_payments'], 2) }}
                                        </td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="7" class="text-right">Net Cash Flow:</td>
                                        <td class="text-right {{ $data['net_cash'] >= 0 ? 'text-primary' : 'text-warning' }}">
                                            <i class="fas fa-balance-scale mr-1"></i>
                                            {{ number_format($data['net_cash'], 2) }}
                                        </td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>

                    <!-- Transaction Count -->
                    @if($data['transactions']->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>{{ $data['transactions']->count() }}</strong> transactions found 
                                    @if($startDate || $endDate)
                                        for the selected period
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
