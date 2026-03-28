@extends('admin.layout')

@section('title', 'Cash Flow Statement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cash Flow Statement</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.accounting.export.cash-flow-statement', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.accounting.cash-flow-statement') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('admin.accounting.cash-flow-statement') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Cash Flow Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Operating Activities</h5>
                                    <h3 class="text-white">{{ number_format($data['cash_flows']['operating_activities'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Investing Activities</h5>
                                    <h3 class="text-white">{{ number_format($data['cash_flows']['investing_activities'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Financing Activities</h5>
                                    <h3 class="text-white">{{ number_format($data['cash_flows']['financing_activities'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-white">Net Cash Flow</h5>
                                    <h3 class="text-white">{{ number_format($data['cash_flows']['net_cash_flow'], 2) }} RWF</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Flow Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">CASH FLOWS FROM ACTIVITIES</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Cash flows from operating activities</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_flows']['operating_activities'], 2) }} RWF</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cash flows from investing activities</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_flows']['investing_activities'], 2) }} RWF</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cash flows from financing activities</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_flows']['financing_activities'], 2) }} RWF</td>
                                        </tr>
                                        <tr class="font-weight-bold border-top">
                                            <td><strong>Net cash flow</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_flows']['net_cash_flow'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">CASH BALANCES</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Opening balance</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_balances']['opening_balance'], 2) }} RWF</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Closing balance</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_balances']['closing_balance'], 2) }} RWF</td>
                                        </tr>
                                        <tr class="font-weight-bold border-top">
                                            <td><strong>Net change in cash</strong></td>
                                            <td class="text-right">{{ number_format($data['cash_balances']['net_change'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Period Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle mr-2"></i>Period Information</h5>
                                <p class="mb-0">
                                    This cash flow statement covers the period from 
                                    <strong>{{ $data['period_start']->format('M d, Y') }}</strong> to 
                                    <strong>{{ $data['period_end']->format('M d, Y') }}</strong>.
                                    All amounts are in RWF (Rwandan Francs).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
