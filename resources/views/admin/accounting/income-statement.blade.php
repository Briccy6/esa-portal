@extends('admin.layout')

@section('title', 'Income Statement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Income Statement</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.accounting.income-statement') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                       value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                       value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('admin.accounting.income-statement') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Total Revenue</h5>
                                    <h3 class="text-white">{{ number_format($data['total_revenue'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Total Expenses</h5>
                                    <h3 class="text-white">{{ number_format($data['total_expenses'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $data['net_income'] >= 0 ? 'bg-success' : 'bg-warning' }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Net Income</h5>
                                    <h3 class="text-white">{{ number_format($data['net_income'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Revenues</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        @forelse($data['revenues'] as $revenue)
                                            <tr>
                                                <td><code>{{ $revenue->account_code }}</code> - {{ $revenue->account_name }}</td>
                                                <td class="text-right">{{ number_format($revenue->amount, 2) }} RWF</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No revenue accounts for this period.</td>
                                            </tr>
                                        @endforelse
                                        <tr class="font-weight-bold border-top">
                                            <td>Total Revenue</td>
                                            <td class="text-right">{{ number_format($data['total_revenue'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">Expenses</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        @forelse($data['expenses'] as $expense)
                                            <tr>
                                                <td><code>{{ $expense->account_code }}</code> - {{ $expense->account_name }}</td>
                                                <td class="text-right">{{ number_format($expense->amount, 2) }} RWF</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No expense accounts for this period.</td>
                                            </tr>
                                        @endforelse
                                        <tr class="font-weight-bold border-top">
                                            <td>Total Expenses</td>
                                            <td class="text-right">{{ number_format($data['total_expenses'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Period:</strong>
                                {{ $data['period_start']->format('M d, Y') }} to
                                {{ $data['period_end']->format('M d, Y') }}.
                                All amounts are in RWF (Rwandan Francs).
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

