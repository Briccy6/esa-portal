@extends('admin.layout')

@section('title', 'General Ledger')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">General Ledger</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.accounting.general-ledger') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="account_id">Account</label>
                                <select name="account_id" id="account_id" class="form-control" required>
                                    <option value="">-- Select Account --</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ (string) $account->id === (string) $selectedAccountId ? 'selected' : '' }}>
                                            {{ $account->account_code }} - {{ $account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                       value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                       value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('admin.accounting.general-ledger') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    @if($ledgerData)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-info">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-white">Opening Balance</h5>
                                        <h3 class="text-white">{{ number_format($ledgerData['opening_balance'], 2) }} RWF</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-white">Closing Balance</h5>
                                        <h3 class="text-white">{{ number_format($ledgerData['closing_balance'], 2) }} RWF</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-dark">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-white">Period</h5>
                                        <p class="text-white mb-0">
                                            {{ $ledgerData['period_start']->format('M d, Y') }}
                                            -
                                            {{ $ledgerData['period_end']->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="12%">Date</th>
                                        <th width="18%">Reference</th>
                                        <th>Description</th>
                                        <th class="text-right" width="12%">Debit</th>
                                        <th class="text-right" width="12%">Credit</th>
                                        <th class="text-right" width="16%">Running Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-active font-weight-bold">
                                        <td colspan="5">Opening Balance</td>
                                        <td class="text-right">{{ number_format($ledgerData['opening_balance'], 2) }}</td>
                                    </tr>
                                    @forelse($ledgerData['transactions'] as $transaction)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($transaction->entry_date)->format('M d, Y') }}</td>
                                            <td><code>{{ $transaction->reference_number }}</code></td>
                                            <td>{{ $transaction->entry_description }}</td>
                                            <td class="text-right">{{ number_format($transaction->debit_amount, 2) }}</td>
                                            <td class="text-right">{{ number_format($transaction->credit_amount, 2) }}</td>
                                            <td class="text-right font-weight-bold">
                                                {{ number_format($transaction->running_balance, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No ledger movements for the selected period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Please select an account and period, then click <strong>Generate</strong> to view the general ledger.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

