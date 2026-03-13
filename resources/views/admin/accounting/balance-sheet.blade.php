@extends('admin.layout')

@section('title', 'Balance Sheet Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Balance Sheet Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.accounting.export.balance-sheet', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.accounting.balance-sheet') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="as_of_date">As of Date</label>
                                <input type="date" id="as_of_date" name="as_of_date" class="form-control" value="{{ $asOfDate->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('admin.accounting.balance-sheet') }}" class="btn btn-secondary">Reset to Today</a>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <!-- Assets Section -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">ASSETS</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        @foreach($data['assets']['details'] as $asset)
                                        <tr>
                                            <td>{{ $asset['name'] }}</td>
                                            <td class="text-right">{{ number_format($asset['balance'], 2) }} RWF</td>
                                        </tr>
                                        @endforeach
                                        <tr class="font-weight-bold border-top">
                                            <td>Total Assets</td>
                                            <td class="text-right">{{ number_format($data['assets']['total'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Liabilities Section -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">LIABILITIES</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        @foreach($data['liabilities']['details'] as $liability)
                                        <tr>
                                            <td>{{ $liability['name'] }}</td>
                                            <td class="text-right">{{ number_format($liability['balance'], 2) }} RWF</td>
                                        </tr>
                                        @endforeach
                                        <tr class="font-weight-bold border-top">
                                            <td>Total Liabilities</td>
                                            <td class="text-right">{{ number_format($data['liabilities']['total'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Equity Section -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">EQUITY</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        @foreach($data['equity']['details'] as $equity_item)
                                        <tr>
                                            <td>{{ $equity_item['name'] }}</td>
                                            <td class="text-right">{{ number_format($equity_item['balance'], 2) }} RWF</td>
                                        </tr>
                                        @endforeach
                                        <tr class="font-weight-bold border-top">
                                            <td>Total Equity</td>
                                            <td class="text-right">{{ number_format($data['equity']['total'], 2) }} RWF</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">SUMMARY</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Total Liabilities & Equity</td>
                                            <td class="text-right">
                                                {{ number_format($data['liabilities']['total'] + $data['equity']['total'], 2) }} RWF
                                            </td>
                                        </tr>
                                        <tr class="font-weight-bold border-top">
                                            <td>Balance Check</td>
                                            <td class="text-right">
                                                <span class="{{ $data['is_balanced'] ? 'text-success' : 'text-danger' }}">
                                                    {{ $data['is_balanced'] ? '✓ Balanced' : '✗ Not Balanced' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Sheet Visualization -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Balance Sheet Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h4 class="text-primary">{{ number_format($data['assets']['total'], 2) }} RWF</h4>
                                            <p class="text-muted">Total Assets</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-warning">{{ number_format($data['liabilities']['total'], 2) }} RWF</h4>
                                            <p class="text-muted">Total Liabilities</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-success">{{ number_format($data['equity']['total'], 2) }} RWF</h4>
                                            <p class="text-muted">Total Equity</p>
                                        </div>
                                    </div>
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
