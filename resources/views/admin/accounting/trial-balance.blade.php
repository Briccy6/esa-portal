@extends('admin.layout')

@section('title', 'Trial Balance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trial Balance Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('accounting.export.trial-balance', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('accounting.trial-balance') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="as_of_date">As of Date</label>
                                <input type="date" id="as_of_date" name="as_of_date" class="form-control" value="{{ $asOfDate->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('accounting.trial-balance') }}" class="btn btn-secondary">Reset to Today</a>
                            </div>
                        </div>
                    </form>

                    <!-- Balance Status -->
                    <div class="alert {{ $data['is_balanced'] ? 'alert-success' : 'alert-danger' }} mb-3">
                        <h5 class="alert-heading">
                            {{ $data['is_balanced'] ? '✓ Trial Balance is Balanced' : '✗ Trial Balance is NOT Balanced' }}
                        </h5>
                        <p class="mb-0">
                            Total Debits: {{ number_format($data['total_debits'], 2) }} RWF | 
                            Total Credits: {{ number_format($data['total_credits'], 2) }} RWF
                        </p>
                        @if(!$data['is_balanced'])
                            <p class="mb-0">
                                Difference: {{ number_format(abs($data['total_debits'] - $data['total_credits']), 2) }} RWF
                            </p>
                        @endif
                    </div>

                    <!-- Trial Balance Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Account Type</th>
                                    <th class="text-right">Debit Balance</th>
                                    <th class="text-right">Credit Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Assets -->
                                <tr class="table-info">
                                    <td colspan="5"><strong>ASSETS</strong></td>
                                </tr>
                                @foreach($data['accounts'] as $account)
                                    @if($account['account_type'] == 'Asset')
                                        <tr>
                                            <td><code>{{ $account['account_code'] }}</code></td>
                                            <td>{{ $account['account_name'] }}</td>
                                            <td>{{ $account['account_type'] }}</td>
                                            <td class="text-right">{{ number_format($account['debit_balance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($account['credit_balance'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                <!-- Liabilities -->
                                <tr class="table-warning">
                                    <td colspan="4"><strong>LIABILITIES</strong></td>
                                </tr>
                                @foreach($data['accounts'] as $account)
                                    @if($account['account_type'] == 'Liability')
                                        <tr>
                                            <td><code>{{ $account['account_code'] }}</code></td>
                                            <td>{{ $account['account_name'] }}</td>
                                            <td>{{ $account['account_type'] }}</td>
                                            <td class="text-right">{{ number_format($account['debit_balance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($account['credit_balance'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                <!-- Equity -->
                                <tr class="table-success">
                                    <td colspan="4"><strong>EQUITY</strong></td>
                                </tr>
                                @foreach($data['accounts'] as $account)
                                    @if($account['account_type'] == 'Equity')
                                        <tr>
                                            <td><code>{{ $account['account_code'] }}</code></td>
                                            <td>{{ $account['account_name'] }}</td>
                                            <td>{{ $account['account_type'] }}</td>
                                            <td class="text-right">{{ number_format($account['debit_balance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($account['credit_balance'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                <!-- Revenue -->
                                <tr class="table-primary">
                                    <td colspan="4"><strong>REVENUE</strong></td>
                                </tr>
                                @foreach($data['accounts'] as $account)
                                    @if($account['account_type'] == 'Revenue')
                                        <tr>
                                            <td><code>{{ $account['account_code'] }}</code></td>
                                            <td>{{ $account['account_name'] }}</td>
                                            <td>{{ $account['account_type'] }}</td>
                                            <td class="text-right">{{ number_format($account['debit_balance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($account['credit_balance'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                <!-- Expenses -->
                                <tr class="table-danger">
                                    <td colspan="4"><strong>EXPENSES</strong></td>
                                </tr>
                                @foreach($data['accounts'] as $account)
                                    @if($account['account_type'] == 'Expense')
                                        <tr>
                                            <td><code>{{ $account['account_code'] }}</code></td>
                                            <td>{{ $account['account_name'] }}</td>
                                            <td>{{ $account['account_type'] }}</td>
                                            <td class="text-right">{{ number_format($account['debit_balance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($account['credit_balance'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                <!-- Totals -->
                                <tr class="font-weight-bold bg-dark text-white">
                                    <td colspan="3">TOTALS</td>
                                    <td class="text-right">{{ number_format($data['total_debits'], 2) }}</td>
                                    <td class="text-right">{{ number_format($data['total_credits'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Total Accounts</h5>
                                    <h3 class="text-white">{{ count($data['accounts']) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Total Debits</h5>
                                    <h3 class="text-white">{{ number_format($data['total_debits'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Total Credits</h5>
                                    <h3 class="text-white">{{ number_format($data['total_credits'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card {{ $data['is_balanced'] ? 'bg-primary' : 'bg-danger' }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Balance Status</h5>
                                    <h3 class="text-white">{{ $data['is_balanced'] ? 'Balanced' : 'Not Balanced' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
