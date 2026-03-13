@extends('admin.layout')

@section('title', 'Accounting Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Accounting Reports</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Cash Book</h5>
                                    <p class="card-text text-white">View all income and expense transactions</p>
                                    <a href="{{ route('admin.accounting.cashbook') }}" class="btn btn-light">
                                        <i class="fas fa-book"></i> View Cash Book
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Balance Sheet</h5>
                                    <p class="card-text text-white">View assets, liabilities and equity</p>
                                    <a href="{{ route('admin.accounting.balance-sheet') }}" class="btn btn-light">
                                        <i class="fas fa-balance-scale"></i> View Balance Sheet
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Trial Balance</h5>
                                    <p class="card-text text-white">View all ledger accounts and balances</p>
                                    <a href="{{ route('admin.accounting.trial-balance') }}" class="btn btn-light">
                                        <i class="fas fa-list-alt"></i> View Trial Balance
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card bg-secondary">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Income Statement</h5>
                                    <p class="card-text text-white">View revenues, expenses and net income</p>
                                    <a href="{{ route('admin.accounting.income-statement') }}" class="btn btn-light">
                                        <i class="fas fa-file-invoice-dollar"></i> View Income Statement
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-dark">
                                <div class="card-body">
                                    <h5 class="card-title text-white">General Ledger</h5>
                                    <p class="card-text text-white">View detailed movements per account</p>
                                    <a href="{{ route('admin.accounting.general-ledger') }}" class="btn btn-light">
                                        <i class="fas fa-book-open"></i> View General Ledger
                                    </a>
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
