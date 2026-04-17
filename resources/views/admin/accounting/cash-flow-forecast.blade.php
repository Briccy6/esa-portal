@extends('admin.layout')

@section('title', 'Cash Flow Forecast')

@push('styles')
<style>
    .cashflow-grid th, .cashflow-grid td {
        white-space: nowrap;
        font-size: 0.875rem;
    }
    .cashflow-grid .sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #fff;
        box-shadow: 2px 0 4px rgba(0,0,0,.06);
    }
    .cashflow-grid thead .sticky-col {
        z-index: 3;
        background: #343a40;
    }
    .cashflow-grid .section-row td {
        background: #f8f9fc;
        font-weight: 600;
    }
    .cashflow-grid .sub-row td {
        color: #5a5c69;
        font-size: 0.82rem;
    }
    .cashflow-grid .num {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    @media print {
        .filter-form, .card-tools { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-area mr-2"></i>Cash flow forecast
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                        <a href="{{ route('admin.accounting.export.cash-flow-forecast', request()->query()) }}" class="btn btn-success btn-sm ml-2">
                            <i class="fas fa-download mr-1"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($school)
                        <p class="text-muted mb-3 mb-md-4">{{ $school->school_name ?? 'School' }} — monthly view ({{ $data['currency'] }}). Receipts use <code>student_transactions</code> (<code>paid_at</code>, <code>amount</code>, <code>type</code>, <code>status</code>); outflows use <code>expenses</code> (<code>expense_date</code>, <code>amount</code>, <code>expense_type</code>).</p>
                    @else
                        <p class="text-muted mb-3 mb-md-4">Monthly view ({{ $data['currency'] }}). Same sources as the cash book.</p>
                    @endif

                    <form method="GET" action="{{ route('admin.accounting.cash-flow-forecast') }}" class="mb-4 filter-form">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" id="year" name="year" class="form-control" min="2000" max="2100" value="{{ $year }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync-alt mr-1"></i> Update
                                </button>
                                <a href="{{ route('admin.accounting.cash-flow-forecast') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm cashflow-grid mb-0">
                            <thead class="thead-dark text-white">
                                <tr>
                                    <th class="sticky-col">Line</th>
                                    @foreach($data['months'] as $m)
                                        <th class="text-right num">{{ $m['label'] }}</th>
                                    @endforeach
                                    <th class="text-right num bg-secondary">Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="sticky-col">Opening balance</td>
                                    @foreach($data['months'] as $m)
                                        <td class="num">{{ number_format($m['opening'], 2) }}</td>
                                    @endforeach
                                    <td class="num bg-light text-muted">—</td>
                                </tr>
                                <tr class="section-row">
                                    <td class="sticky-col">Cash in — student receipts</td>
                                    @foreach($data['months'] as $m)
                                        <td class="num">{{ number_format($m['cash_in'], 2) }}</td>
                                    @endforeach
                                    <td class="num bg-light">{{ number_format($data['totals']['cash_in'], 2) }}</td>
                                </tr>
                                @foreach($data['in_types'] as $type)
                                    <tr class="sub-row">
                                        <td class="sticky-col pl-4"><span class="text-muted">└</span> {{ $type }}</td>
                                        @foreach($data['months'] as $m)
                                            <td class="num">{{ number_format($m['cash_in_by_type'][$type] ?? 0, 2) }}</td>
                                        @endforeach
                                        <td class="num bg-light">
                                            @php
                                                $st = collect($data['months'])->sum(fn ($row) => $row['cash_in_by_type'][$type] ?? 0);
                                            @endphp
                                            {{ number_format($st, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="section-row">
                                    <td class="sticky-col">Cash out — expenses</td>
                                    @foreach($data['months'] as $m)
                                        <td class="num">{{ number_format($m['cash_out'], 2) }}</td>
                                    @endforeach
                                    <td class="num bg-light">{{ number_format($data['totals']['cash_out'], 2) }}</td>
                                </tr>
                                @foreach($data['expense_types'] as $expType)
                                    <tr class="sub-row">
                                        <td class="sticky-col pl-4"><span class="text-muted">└</span> {{ \Illuminate\Support\Str::limit($expType, 48) }}</td>
                                        @foreach($data['months'] as $m)
                                            <td class="num">{{ number_format($m['cash_out_by_expense_type'][$expType] ?? 0, 2) }}</td>
                                        @endforeach
                                        <td class="num bg-light">
                                            @php
                                                $sx = collect($data['months'])->sum(fn ($row) => $row['cash_out_by_expense_type'][$expType] ?? 0);
                                            @endphp
                                            {{ number_format($sx, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info">
                                    <td class="sticky-col"><strong>Net cash movement</strong></td>
                                    @foreach($data['months'] as $m)
                                        <td class="num"><strong>{{ number_format($m['net'], 2) }}</strong></td>
                                    @endforeach
                                    <td class="num"><strong>{{ number_format($data['totals']['net'], 2) }}</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td class="sticky-col"><strong>Closing balance</strong></td>
                                    @foreach($data['months'] as $m)
                                        <td class="num"><strong>{{ number_format($m['closing'], 2) }}</strong></td>
                                    @endforeach
                                    <td class="num"><strong>{{ number_format($data['year_end_closing'], 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-light border">
                        <strong>Notes.</strong> Opening balance for January is cash accumulated before {{ $data['year'] }} (same basis as the cash book). Each month’s opening equals the previous month’s closing. “Year” is the sum of monthly amounts where additive; closing year column is the balance at 31 December.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
