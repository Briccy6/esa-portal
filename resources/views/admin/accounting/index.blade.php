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
                    <p class="text-muted small mb-4">
                        <i class="fas fa-file-invoice me-1"></i>
                        For an official <strong>student payment transcript</strong> (print / PDF with school letterhead and stamp), open a student profile and use <strong>Print payment transcript</strong>, or go to
                        <a href="{{ route('admin.students.index') }}">Students</a> and choose a student.
                    </p>
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
                        <div class="col-md-4">
                            <div class="card" style="background: linear-gradient(135deg, #1a5f7a 0%, #159895 100%);">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Cash Flow Forecast</h5>
                                    <p class="card-text text-white">Monthly cash in, out and balance from receipts and expenses</p>
                                    <a href="{{ route('admin.accounting.cash-flow-forecast') }}" class="btn btn-light">
                                        <i class="fas fa-chart-area"></i> View Forecast
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }
    
    // Debug: Log the data values
    console.log('NESA Registered: {{ $nesaRegistered ?? 0 }}');
    console.log('NESA Not Registered: {{ $nesaNotRegistered ?? 0 }}');
    console.log('Approved Students: {{ $approvedStudents ?? 0 }}');
    console.log('Not Approved Students: {{ $notApprovedStudents ?? 0 }}');
    
    // Chart defaults
    Chart.defaults.font.family = "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.color = '#858796';

    // NESA Registration Status Chart
    try {
        const nesaCanvas = document.getElementById('nesaRegistrationChart');
        console.log('NESA Canvas element:', nesaCanvas);
        
        if (!nesaCanvas) {
            console.error('NESA Registration Chart canvas not found!');
            return;
        }
        
        const nesaCtx = nesaCanvas.getContext('2d');
        console.log('NESA Canvas context:', nesaCtx);
        
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
        console.log('NESA Registration Chart created successfully');
    } catch (error) {
        console.error('Error creating NESA Registration Chart:', error);
    }

    // Student Approval Status Chart  
    try {
        const approvalCanvas = document.getElementById('studentApprovalChart');
        console.log('Approval Canvas element:', approvalCanvas);
        
        if (!approvalCanvas) {
            console.error('Student Approval Chart canvas not found!');
            return;
        }
        
        const approvalCtx = approvalCanvas.getContext('2d');
        console.log('Approval Canvas context:', approvalCtx);
        
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
        console.log('Student Approval Chart created successfully');
    } catch (error) {
        console.error('Error creating Student Approval Chart:', error);
    }
});
</script>
@endsection
