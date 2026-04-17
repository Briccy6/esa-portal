@extends('staff.partials.sidebar_sec')

@section('title', 'Accountant Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Accountant Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <i class="fas fa-calendar me-1"></i>
            This Month
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Branches
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($branchStats) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Income
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format(array_sum(array_column($branchStats, 'totalIncome')), 2) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ array_sum(array_column($branchStats, 'studentsCount')) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Expenses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format(array_sum(array_column($branchStats, 'totalExpenses')), 2) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Branch Details -->
<div class="row">
    @foreach($branchStats as $index => $branchData)
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building me-2"></i>{{ $branchData['branch']->name }}
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <a class="dropdown-item" href="#">View Details</a>
                        <a class="dropdown-item" href="#">Export Report</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Settings</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Branch Stats -->
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="small text-gray-500">Income</div>
                        <div class="h5 text-success">${{ number_format($branchData['totalIncome'], 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-gray-500">Expenses</div>
                        <div class="h5 text-warning">${{ number_format($branchData['totalExpenses'], 2) }}</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="small text-gray-500">Students</div>
                        <div class="h5 text-info">{{ $branchData['studentsCount'] }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-gray-500">Expense Records</div>
                        <div class="h5 text-secondary">{{ $branchData['expensesCount'] }}</div>
                    </div>
                </div>

                <!-- Course Distribution Chart -->
                @if(!empty($branchData['courseData']))
                <div class="mt-3">
                    <h6 class="text-sm font-weight-bold text-gray-700 mb-2">Course Distribution</h6>
                    <div class="progress" style="height: 25px;">
                        @foreach($branchData['courseData'] as $course)
                            <?php 
                            $percentage = $branchData['studentsCount'] > 0 
                                ? round(($course['count'] / $branchData['studentsCount']) * 100) 
                                : 0;
                            ?>
                            <div class="progress-bar" 
                                 style="width: {{ $percentage }}%; background-color: {{ ['#3498db', '#e74c3c', '#f39c12', '#27ae60', '#9b59b6'][$index % 5] }};"
                                 title="{{ $course['name'] }}: {{ $course['count'] }} students ({{ $percentage }}%)">
                                {{ $course['abbreviation'] ?? substr($course['name'], 0, 3) }}
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        @foreach($branchData['courseData'] as $course)
                            <small class="text-muted me-3">
                                <span class="badge bg-secondary">{{ $course['count'] }}</span>
                                {{ $course['name'] }}
                            </small>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-primary" onclick="viewBranchDetails({{ $branchData['branch']->id }})">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    <button class="btn btn-sm btn-success" onclick="viewStudents({{ $branchData['branch']->id }})">
                        <i class="fas fa-users me-1"></i>Students
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="viewExpenses({{ $branchData['branch']->id }})">
                        <i class="fas fa-receipt me-1"></i>Expenses
                    </button>
                    <button class="btn btn-sm btn-info" onclick="manageNesaRegistrations({{ $branchData['branch']->id }})">
                        <i class="fas fa-award me-1"></i>NESA Registration
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts Section -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Income vs Expenses Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Student Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="studentDistributionChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for charts
    const branchNames = @json(array_column(array_column($branchStats, 'branch'), 'name'));
    const incomeData = @json(array_column($branchStats, 'totalIncome'));
    const expenseData = @json(array_column($branchStats, 'totalExpenses'));
    const studentData = @json(array_column($branchStats, 'studentsCount'));

    // Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'bar',
        data: {
            labels: branchNames,
            datasets: [
                {
                    label: 'Income',
                    data: incomeData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Expenses',
                    data: expenseData,
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Student Distribution Chart
    const studentCtx = document.getElementById('studentDistributionChart').getContext('2d');
    new Chart(studentCtx, {
        type: 'doughnut',
        data: {
            labels: branchNames,
            datasets: [{
                data: studentData,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Action functions
    function viewBranchDetails(branchId) {
        window.location.href = `/staff/branches/${branchId}`;
    }

    function viewStudents(branchId) {
        window.location.href = `/staff/students?branch=${branchId}`;
    }

    function viewExpenses(branchId) {
        window.location.href = `/staff/expenses?branch=${branchId}`;
    }

    function manageNesaRegistrations(branchId) {
        window.location.href = `/staff/students?branch=${branchId}&nesa=true`;
    }

    function generateReport() {
        alert('Generating financial report... This would download a comprehensive report in PDF format.');
    }
</script>

<style>
    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    }

    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Section Titles */
    .section-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .info-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-card-header h4 {
        margin: 0;
        color: #1e293b;
        font-weight: 600;
        font-size: 1.125rem;
    }

    .info-card-body {
        padding: 1.5rem;
    }

    /* Metric Display */
    .metric-display {
        text-align: center;
        padding: 1rem 0;
    }

    .metric-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .metric-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Stats */
    .stat-item {
        text-align: center;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }

    /* Action Cards */
    .action-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
    }

    .action-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
    }

    .action-content h6 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.125rem;
    }

    .action-content p {
        color: #64748b;
        margin: 0;
        font-size: 0.875rem;
    }

    /* Progress Bars */
    .progress {
        height: 25px;
        background: #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
    }

    .progress-bar {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: width 0.5s ease;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.5rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
        }

        .metric-number {
            font-size: 2rem;
        }

        .info-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .action-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endsection
