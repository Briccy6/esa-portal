@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue) }} Rwf</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalExpenses) }} Rwf</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Net Profit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($netProfit) }} Rwf</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit Margin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profitMargin }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards Quick Access -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-3">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Report Cards Management</h6>
                    <a href="{{ route('admin.report-cards.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-right"></i> View All Report Cards
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ App\Models\ReportCard::count() }}</h4>
                                <p class="text-muted">Total Report Cards</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ App\Models\ReportCard::where('status', 'approved')->count() }}</h4>
                                <p class="text-muted">Approved</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ App\Models\ReportCard::where('status', 'pending')->count() }}</h4>
                                <p class="text-muted">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ App\Models\SchoolInformation::count() }}</h4>
                                <p class="text-muted">School Configured</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.report-cards.create') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Generate New Report Card
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admin.school-information.edit') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-cog"></i> Configure School Info
                                </a>
                            </div>
                        </div>
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Expenses by Branch</h6>
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="card-body">
                    <canvas id="expensesByBranchChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Expenses by Type</h6>
                    <i class="fas fa-tags"></i>
                </div>
                <div class="card-body">
                    <canvas id="expensesByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Branch Financial Health -->
    <div class="row mb-4">
        <div class="col-xl-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Branch Financial Health (Revenue vs Expenses)</h6>
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> <strong>What this shows:</strong> Revenue (money from students) vs Expenses (money spent by staff). Helps identify profitable branches and those needing financial attention.
                    </div>
                    <canvas id="branchFinancialHealthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Students by Course and Gender -->
    <div class="row mb-4">
        <div class="col-xl-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-purple">Students by Course and Gender</h6>
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="card-body">
                    <canvas id="courseGenderChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Expense Trends & Tuition -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">6-Month Expense Trend</h6>
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="card-body">
                    <canvas id="expenseTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Tuition Fee by Branch</h6>
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="card-body">
                    <canvas id="branchTuitionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 5: Recent Students with NESA Status -->
    <div class="row mb-4">
        <div class="col-xl-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Students & NESA Registration Status</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Student Name</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>NESA Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents ?? [] as $student)
                                    <tr>
                                        <td><strong>{{ $student->reg_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->nesa_registered)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Registered
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$student->nesa_registered)
                                                <button class="btn btn-sm btn-primary" onclick="openNesaModal({{ $student->id }}, '{{ $student->full_name }}', '{{ $student->reg_number }}')">
                                                    <i class="fas fa-graduation-cap mr-1"></i>Register NESA
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-info" onclick="viewNesaDetails({{ $student->id }})">
                                                    <i class="fas fa-eye mr-1"></i>View Details
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Recent Expenses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExpenses->take(5) as $expense)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d') }}</td>
                                    <td>{{ ucfirst($expense->expense_type) }}</td>
                                    <td>{{ number_format($expense->amount) }}</td>
                                    <td>{{ $expense->paid_by_staff->branch->name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart defaults for professional look
    Chart.defaults.font.family = "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.color = '#858796';

    // Color schemes
    const primaryColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    const gradientColors = {
        blue: ['#4e73df', '#224abe'],
        green: ['#1cc88a', '#17a673'],
        orange: ['#f6c23e', '#dda20a'],
        red: ['#e74a3b', '#be2617'],
        purple: ['#6f42c1', '#59359a'],
        teal: ['#36b9cc', '#2c9faf']
    };

    // Helper function to create gradient
    function createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    // 1. Members Pie Chart
    const membersCtx = document.getElementById('membersPieChart').getContext('2d');
    new Chart(membersCtx, {
        type: 'doughnut',
        data: {
            labels: ['Students', 'Teachers', 'Staff'],
            datasets: [{
                data: [
                    {{ $totalStudents ?? 0 }},
                    {{ $totalTeachers ?? 0 }},
                    {{ $totalStaff ?? 0 }}
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // 2. Students by Gender Chart
const genderCtx = document.getElementById('genderPieChart').getContext('2d');
const genderData = {!! json_encode($studentsByGender) !!};
const genderLabels = genderData.map(g => 
    g.sex === 'male' ? 'Male' : g.sex === 'female' ? 'Female' : 'Other'
);
const genderCounts = genderData.map(g => g.total);

    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#4e73df', '#e74a3b', '#f6c23e'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // 2. Fee Payments Bar Chart
    const feesCtx = document.getElementById('feesBarChart').getContext('2d');
    new Chart(feesCtx, {
        type: 'bar',
        data: {
            labels: ['Registration Paid', 'Tuition Paid'],
            datasets: [{
                label: 'Percentage (%)',
                data: [
                    {{ $registrationPaidPercent ?? 0 }},
                    {{ $tuitionPaidPercent ?? 0 }}
                ],
                backgroundColor: [
                    createGradient(feesCtx, '#f6c23e', '#dda20a'),
                    createGradient(feesCtx, '#1cc88a', '#17a673')
                ],
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { callback: function(value) { return value + '%'; } }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Revenue vs Expenses Doughnut
    const revExpCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    new Chart(revExpCtx, {
        type: 'doughnut',
        data: {
            labels: ['Revenue', 'Expenses', 'Profit'],
            datasets: [{
                data: [
                    {{ $totalRevenue }},
                    {{ $totalExpenses }},
                    {{ $netProfit }}
                ],
                backgroundColor: ['#1cc88a', '#e74a3b', '#4e73df'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString() + ' Rwf';
                        }
                    }
                }
            }
        }
    });

    // 4. Expenses by Branch
    const expBranchCtx = document.getElementById('expensesByBranchChart').getContext('2d');
    new Chart(expBranchCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($expensesByBranch as $branch)
                    "{{ $branch->branch }}"{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                label: 'Total Expenses (Rwf)',
                data: [
                    @foreach($expensesByBranch as $branch)
                        {{ $branch->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: createGradient(expBranchCtx, '#e74a3b', '#be2617'),
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                y: { grid: { display: false } }
            }
        }
    });

    // 5. Expenses by Type
    const expTypeCtx = document.getElementById('expensesByTypeChart').getContext('2d');
    new Chart(expTypeCtx, {
        type: 'pie',
        data: {
            labels: [
                @foreach($expensesByType as $type)
                    "{{ ucfirst($type->expense_type) }}"{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($expensesByType as $type)
                        {{ $type->total }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                backgroundColor: primaryColors,
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, font: { size: 10 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString() + ' Rwf';
                        }
                    }
                }
            }
        }
    });

    // 6. Branch Financial Health
    const branchHealthCtx = document.getElementById('branchFinancialHealthChart').getContext('2d');
    new Chart(branchHealthCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($branchFinancialHealth as $branch)
                    "{{ $branch->name }}"{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [
                {
                    label: 'Revenue (Rwf)',
                    data: [
                        @foreach($branchFinancialHealth as $branch)
                            {{ $branch->revenue }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    backgroundColor: createGradient(branchHealthCtx, '#1cc88a', '#17a673'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Expenses (Rwf)',
                    data: [
                        @foreach($branchFinancialHealth as $branch)
                            {{ $branch->expenses }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    backgroundColor: createGradient(branchHealthCtx, '#e74a3b', '#be2617'),
                    borderWidth: 0,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 20, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 7. Expense Trend
    const trendCtx = document.getElementById('expenseTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($expenseTrend as $trend)
                    "{{ $trend['month'] }}"{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [{
                label: 'Monthly Expenses (Rwf)',
                data: [
                    @foreach($expenseTrend as $trend)
                        {{ $trend['amount'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                borderColor: '#f6c23e',
                backgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, 'rgba(246, 194, 62, 0.3)');
                    gradient.addColorStop(1, 'rgba(246, 194, 62, 0.01)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: '#f6c23e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 8. Branch Tuition Chart
    const tuitionCtx = document.getElementById('branchTuitionChart').getContext('2d');
    new Chart(tuitionCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($tuitionByBranch as $branch)
                    "{{ $branch->branch }}"{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            datasets: [
                {
                    label: 'Paid (Rwf)',
                    data: [
                        @foreach($tuitionByBranch as $branch)
                            {{ $tuitionPaidByBranch->firstWhere('branch', $branch->branch)->paid ?? 0 }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    backgroundColor: createGradient(tuitionCtx, '#36b9cc', '#2c9faf'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Expected (Rwf)',
                    data: [
                        @foreach($tuitionByBranch as $branch)
                            {{ $branch->expected ?? 0 }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    backgroundColor: createGradient(tuitionCtx, '#f6c23e', '#dda20a'),
                    borderWidth: 0,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 20, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 9. Students by Course and Gender
    const courseGenderCtx = document.getElementById('courseGenderChart').getContext('2d');
    const courseGenderData = {!! json_encode($studentsByCourseGender) !!};
    
    // Get unique courses
    const courses = [...new Set(courseGenderData.map(item => item.course?.name || 'Unknown'))];
    
    // Prepare data for males and females
    const maleData = courses.map(course => {
        const item = courseGenderData.find(d => (d.course?.name || 'Unknown') === course && d.sex === 'M');
        return item ? item.total : 0;
    });
    
    const femaleData = courses.map(course => {
        const item = courseGenderData.find(d => (d.course?.name || 'Unknown') === course && d.sex === 'F');
        return item ? item.total : 0;
    });
    
    new Chart(courseGenderCtx, {
        type: 'bar',
        data: {
            labels: courses,
            datasets: [
                {
                    label: 'Male Students',
                    data: maleData,
                    backgroundColor: createGradient(courseGenderCtx, '#4e73df', '#224abe'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Female Students',
                    data: femaleData,
                    backgroundColor: createGradient(courseGenderCtx, '#e74a3b', '#be2617'),
                    borderWidth: 0,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { padding: 20, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    stacked: false
                },
                x: { 
                    grid: { display: false },
                    stacked: false
                }
            }
        }
    });
});

// NESA Registration Functions
function openNesaModal(studentId, studentName, regNumber) {
    Swal.fire({
        title: 'NESA Registration',
        html: `
            <div style="text-align: left;">
                <p><strong>Student:</strong> ${studentName}</p>
                <p><strong>Reg Number:</strong> ${regNumber}</p>
                <div class="form-group">
                    <label>SDMS Code:</label>
                    <input type="text" id="sdmsCode" class="swal2-input" placeholder="Enter NESA SDMS code">
                </div>
                <div class="form-group">
                    <label>NESA Registration Number:</label>
                    <input type="text" id="nesaRegNumber" class="swal2-input" placeholder="Enter NESA registration number">
                </div>
                <div class="form-group">
                    <label>Remarks (optional):</label>
                    <textarea id="remarks" class="swal2-input" placeholder="Additional remarks"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Register for NESA',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const sdmsCode = document.getElementById('sdmsCode').value;
            const nesaRegNumber = document.getElementById('nesaRegNumber').value;
            const remarks = document.getElementById('remarks').value;
            
            if (!sdmsCode || !nesaRegNumber) {
                Swal.showValidationMessage('Please enter both SDMS code and NESA registration number');
                return false;
            }
            
            return { sdmsCode, nesaRegNumber, remarks };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit NESA registration
            fetch(`/admin/nesa/${studentId}/mark-registered`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'Student registered for NESA successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Error!', data.message || 'Registration failed', 'error');
                    console.error('NESA Registration Error:', data);
                }
            })
            .catch(error => {
                console.error('NESA Registration Fetch Error:', error);
                Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
            });
        }
    });
}

function viewNesaDetails(studentId) {
    window.open(`/admin/nesa/${studentId}/edit`, '_blank');
}
</script>

@endsection