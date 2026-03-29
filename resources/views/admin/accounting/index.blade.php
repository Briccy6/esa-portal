@extends('admin.layout')

@section('title', 'Accounting Reports')

@push('styles')
<style>
.chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Student Status Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">NESA Registration Status</h6>
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="nesaRegistrationChart"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            {{ $nesaRegistered ?? 0 }} registered • {{ $nesaNotRegistered ?? 0 }} not registered
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Student Approval Status</h6>
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="studentApprovalChart"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            {{ $approvedStudents ?? 0 }} approved • {{ $notApprovedStudents ?? 0 }} not approved
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }
    
    // Chart defaults
    Chart.defaults.font.family = "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.color = '#858796';

    // NESA Registration Status Chart
    const nesaCtx = document.getElementById('nesaRegistrationChart').getContext('2d');
    new Chart(nesaCtx, {
        type: 'doughnut',
        data: {
            labels: ['Registered for NESA', 'Not Registered'],
            datasets: [{
                data: [
                    {{ $nesaRegistered ?? 0 }},
                    {{ $nesaNotRegistered ?? 0 }}
                ],
                backgroundColor: ['#1cc88a', '#e74a3b'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Student Approval Status Chart  
    const approvalCtx = document.getElementById('studentApprovalChart').getContext('2d');
    new Chart(approvalCtx, {
        type: 'doughnut',
        data: {
            labels: ['Approved (Registration Fees Paid)', 'Not Approved'],
            datasets: [{
                data: [
                    {{ $approvedStudents ?? 0 }},
                    {{ $notApprovedStudents ?? 0 }}
                ],
                backgroundColor: ['#4e73df', '#f6c23e'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
