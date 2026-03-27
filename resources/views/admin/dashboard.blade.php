@extends('admin.layout')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
.border-left-purple {
    border-left: 0.25rem solid #6f42c1 !important;
}
.border-left-teal {
    border-left: 0.25rem solid #20c997 !important;
}
.text-purple {
    color: #6f42c1 !important;
}
.text-teal {
    color: #20c997 !important;
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Mobile Responsive Dashboard */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0 1rem !important;
    }
    
    .row {
        margin: 0 !important;
    }
    
    .col-xl-3,
    .col-md-6 {
        padding: 0.5rem !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    .card {
        margin-bottom: 1rem !important;
        border-radius: 0.5rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .h5 {
        font-size: 1.125rem !important;
    }
    
    .text-xs {
        font-size: 0.75rem !important;
    }
    
    .fa-2x {
        font-size: 1.5rem !important;
    }
    
    /* Mobile charts */
    .chart-container {
        margin: 1rem 0 !important;
        padding: 1rem !important;
    }
    
    .chart-container canvas {
        max-height: 300px !important;
    }
    
    /* Mobile tables */
    .table-responsive {
        font-size: 0.875rem !important;
    }
    
    .table th,
    .table td {
        padding: 0.5rem !important;
    }
    
    /* Mobile stats grid */
    .stats-grid {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
    }
}

@media (max-width: 640px) {
    .card-body {
        padding: 0.75rem !important;
    }
    
    .h5 {
        font-size: 1rem !important;
    }
    
    .fa-2x {
        font-size: 1.25rem !important;
    }
    
    .chart-container canvas {
        max-height: 250px !important;
    }
    
    .table-responsive {
        font-size: 0.8125rem !important;
    }
}

/* Touch optimizations */
@media (hover: none) and (pointer: coarse) {
    .card {
        cursor: pointer;
    }
    
    .btn {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.expenses.export') }}" class="text-decoration-none">
                <div class="card border-left-danger shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Expenses</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalExpenses) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Net Profit</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($netProfit) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit Margin</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profitMargin }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Revenue Source Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Token Purchase Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($tokenRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">This month: {{ number_format($monthlyTokenRevenue ?? 0) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-purple shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">Live Class Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($liveClassRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">This month: {{ number_format($monthlyLiveClassRevenue ?? 0) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-video fa-2x text-purple"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-teal shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-teal text-uppercase mb-1">Resource Upload Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($resourceRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">This month: {{ number_format($monthlyResourceRevenue ?? 0) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-upload fa-2x text-teal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Subscription Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($subscriptionRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">This month: {{ number_format($monthlySubscriptionRevenue ?? 0) }} Rwf</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-crown fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Additional Revenue Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Exam Purchase Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($examRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">From public exam sales</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-graduation-cap fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.report') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Registration Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($registrationRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">Student registration fees</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-plus fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Client Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalClientRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">From client token purchases</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-handshake fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.expenses.export') }}" class="text-decoration-none">
                <div class="card border-left-danger shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Expenses</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalExpenses ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">Operational expenses</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Client Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Clients</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalClients ?? 0) }}</div>
                                <div class="text-xs text-muted">{{ number_format($activeClients ?? 0) }} active, {{ number_format($inactiveClients ?? 0) }} inactive</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Client Tokens Sold</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalClientTokens ?? 0) }}</div>
                                <div class="text-xs text-muted">Total tokens purchased</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Client Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalClientRevenue ?? 0) }} Rwf</div>
                                <div class="text-xs text-muted">From token purchases</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Subscriptions</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\ClientSubscription::where('is_active', true)->count() }}</div>
                                <div class="text-xs text-muted">Currently active</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-crown fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Management Section -->
    <div class="row mb-4">
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Blog Management</h6>
                    <div class="btn-group">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list me-2"></i>All Blogs
                        </a>
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-2"></i>Create New Blog
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ \App\Models\Blog::count() }}</h3>
                                <p class="text-muted">Total Blogs</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ \App\Models\Blog::where('status', 'published')->count() }}</h3>
                                <p class="text-muted">Published</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ \App\Models\Blog::where('status', 'draft')->count() }}</h3>
                                <p class="text-muted">Drafts</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ \App\Models\Blog::where('status', 'archived')->count() }}</h3>
                                <p class="text-muted">Archived</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-2"></i>Manage All Blogs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Management Section -->
    <div class="row mb-4">
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">Client Management</h6>
                    <div class="btn-group">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list me-2"></i>All Clients
                        </a>
                        <a href="{{ route('admin.clients.index') }}?status=active" class="btn btn-success btn-sm">
                            <i class="fas fa-user-check me-2"></i>Active Clients
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $totalClients ?? 0 }}</h3>
                                <p class="text-muted">Total Clients</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-success">{{ $activeClients ?? 0 }}</h3>
                                <p class="text-muted">Active</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-danger">{{ $inactiveClients ?? 0 }}</h3>
                                <p class="text-muted">Inactive</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-info">{{ number_format($totalClientTokens ?? 0) }}</h3>
                                <p class="text-muted">Tokens Sold</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-primary">{{ number_format($totalClientRevenue ?? 0) }} Rwf</h3>
                                <p class="text-muted">Revenue</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-secondary">{{ \App\Models\ClientSubscription::where('is_active', true)->count() }}</h3>
                                <p class="text-muted">Active Subscriptions</p>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($recentClients) && $recentClients->count() > 0)
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Recent Clients</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Tokens</th>
                                        <th>Subscription</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentClients as $client)
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td><span class="badge bg-primary">{{ $client->getCurrentTokens() }}</span></td>
                                        <td>
                                            @if($client->hasActiveSubscription())
                                                <span class="badge bg-success">{{ $client->activeSubscription->subscription->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $client->created_at->format('M j, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-warning">
                            <i class="fas fa-users me-2"></i>Manage All Clients
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Management Section -->
    <div class="row mb-4">
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Resources Management</h6>
                    <a href="{{ route('admin.resources.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-right"></i> Manage All Resources
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-info">{{ App\Models\Resource::count() }}</h4>
                                <p class="text-muted">Total Resources</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-success">{{ App\Models\Resource::where('is_public', true)->count() }}</h4>
                                <p class="text-muted">Public</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-warning">{{ App\Models\Resource::where('is_free', true)->count() }}</h4>
                                <p class="text-muted">Free</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-danger">{{ App\Models\Resource::where('is_free', false)->where('price', '>', 0)->count() }}</h4>
                                <p class="text-muted">Paid</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-primary">{{ App\Models\Resource::whereNotNull('document_blob')->count() }}</h4>
                                <p class="text-muted">Documents</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-secondary">{{ App\Models\Resource::whereNotNull('video_blob')->count() }}</h4>
                                <p class="text-muted">Videos</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ route('admin.resources.create') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Upload New Resource
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.resources.index') }}?filter=public" class="btn btn-info btn-block">
                                    <i class="fas fa-eye"></i> View Public Resources
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('public.resources.index') }}" target="_blank" class="btn btn-warning btn-block">
                                    <i class="fas fa-external-link-alt"></i> Public Resource Page
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Members Distribution</h6>
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-body">
                    <canvas id="membersPieChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">Students by Gender</h6>
                    <i class="fas fa-venus-mars"></i>
                </div>
                <div class="card-body">
                    <canvas id="genderPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 6: Fee Payments & Revenue vs Expenses -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">Fee Payment Status</h6>
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="card-body">
                    <canvas id="feesBarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Revenue vs Expenses</h6>
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="card-body">
                    <canvas id="revenueExpenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 7: Recent Students with NESA Status -->
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
                                    <td>{{ $expense->branch->name ?? 'N/A' }}</td>
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
                                    <td>{{ $expense->branch->name ?? 'N/A' }}</td>
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
    // Helper function to capitalize first letter (JavaScript equivalent of PHP ucfirst)
    function ucfirst(str) {
        if (!str) return str;
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }
    
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
    
    // Debug: Check if canvas exists
    if (!feesCtx) {
        console.error('Canvas element feesBarChart not found!');
        return;
    }
    
    const feeData = {!! json_encode($feePaymentData) !!};
    console.log('Fee payment data:', feeData);
    
    // Filter out zero values and prepare chart data
    const feeLabels = [];
    const feeAmounts = [];
    const feeColors = [];
    
    if (feeData.registration_fees > 0) {
        feeLabels.push('Registration Fees');
        feeAmounts.push(feeData.registration_fees);
        feeColors.push('#f6c23e');
    }
    
    if (feeData.school_fees > 0) {
        feeLabels.push('School Fees');
        feeAmounts.push(feeData.school_fees);
        feeColors.push('#1cc88a');
    }
    
    if (feeData.subscription_fees > 0) {
        feeLabels.push('Subscription Fees');
        feeAmounts.push(feeData.subscription_fees);
        feeColors.push('#4e73df');
    }
    
    if (feeData.student_card_fees > 0) {
        feeLabels.push('Student Card Fees');
        feeAmounts.push(feeData.student_card_fees);
        feeColors.push('#36b9cc');
    }
    
    if (feeData.exam_fees > 0) {
        feeLabels.push('Exam Fees');
        feeAmounts.push(feeData.exam_fees);
        feeColors.push('#e74a3b');
    }
    
    if (feeData.token_fees > 0) {
        feeLabels.push('Token Purchases');
        feeAmounts.push(feeData.token_fees);
        feeColors.push('#858796');
    }
    
    if (feeData.live_class_fees > 0) {
        feeLabels.push('Live Class Fees');
        feeAmounts.push(feeData.live_class_fees);
        feeColors.push('#5a5c69');
    }
    
    if (feeData.resource_fees > 0) {
        feeLabels.push('Resource Fees');
        feeAmounts.push(feeData.resource_fees);
        feeColors.push('#2e59d9');
    }
    
    // If no fee data, show "No Data"
    if (feeLabels.length === 0) {
        feeLabels.push('No Fee Data');
        feeAmounts.push(1);
        feeColors.push('#e9ecef');
    }
    
    new Chart(feesCtx, {
        type: 'bar',
        data: {
            labels: feeLabels,
            datasets: [{
                label: 'Amount (Rwf)',
                data: feeAmounts,
                backgroundColor: feeColors,
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
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' Rwf';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' Rwf';
                        }
                    }
                },
                x: { 
                    grid: { display: false }
                }
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
    const expensesByBranchData = {!! json_encode($expensesByBranch) !!};
    
    new Chart(expBranchCtx, {
        type: 'bar',
        data: {
            labels: expensesByBranchData.length > 0 ? expensesByBranchData.map(item => item.branch) : ['No Data'],
            datasets: [{
                label: 'Total Expenses (Rwf)',
                data: expensesByBranchData.length > 0 ? expensesByBranchData.map(item => parseFloat(item.total)) : [0],
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
    const expensesByTypeData = {!! json_encode($expensesByType) !!};
    
    new Chart(expTypeCtx, {
        type: 'doughnut',
        data: {
            labels: expensesByTypeData.length > 0 ? expensesByTypeData.map(item => ucfirst(item.expense_type)) : ['No Data'],
            datasets: [{
                data: expensesByTypeData.length > 0 ? expensesByTypeData.map(item => parseFloat(item.total)) : [1],
                backgroundColor: expensesByTypeData.length > 0 ? primaryColors : ['#e9ecef'],
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
    const branchHealthData = {!! json_encode($branchFinancialHealth) !!};
    
    new Chart(branchHealthCtx, {
        type: 'bar',
        data: {
            labels: branchHealthData.length > 0 ? branchHealthData.map(item => item.name) : ['No Data'],
            datasets: [
                {
                    label: 'Revenue (Rwf)',
                    data: branchHealthData.length > 0 ? branchHealthData.map(item => parseFloat(item.revenue)) : [0],
                    backgroundColor: createGradient(branchHealthCtx, '#1cc88a', '#17a673'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Expenses (Rwf)',
                    data: branchHealthData.length > 0 ? branchHealthData.map(item => parseFloat(item.expenses)) : [0],
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
    
    // Debug: Check if canvas exists
    if (!trendCtx) {
        console.error('Canvas element expenseTrendChart not found!');
        return;
    }
    
    const expenseTrendData = {!! json_encode($expenseTrend) !!};
    console.log('Expense trend data:', expenseTrendData);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: expenseTrendData.length > 0 ? expenseTrendData.map(item => item.month) : ['No Data'],
            datasets: [{
                label: 'Monthly Expenses (Rwf)',
                data: expenseTrendData.length > 0 ? expenseTrendData.map(item => parseFloat(item.amount)) : [0],
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f6c23e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { 
                        padding: 15, 
                        font: { size: 12 },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
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
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' Rwf';
                        }
                    }
                },
                x: { 
                    grid: { display: false }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // 8. Branch Tuition Chart
    const tuitionCtx = document.getElementById('branchTuitionChart').getContext('2d');
    const tuitionData = {!! json_encode($tuitionByBranch) !!};
    const tuitionPaidData = {!! json_encode($tuitionPaidByBranch) !!};
    
    new Chart(tuitionCtx, {
        type: 'bar',
        data: {
            labels: tuitionData.length > 0 ? tuitionData.map(item => item.branch) : ['No Data'],
            datasets: [
                {
                    label: 'Expected (Rwf)',
                    data: tuitionData.length > 0 ? tuitionData.map(item => parseFloat(item.expected)) : [0],
                    backgroundColor: createGradient(tuitionCtx, '#36b9cc', '#2c9faf'),
                    borderWidth: 0,
                    borderRadius: 8
                },
                {
                    label: 'Paid (Rwf)',
                    data: tuitionPaidData.length > 0 ? tuitionPaidData.map(item => parseFloat(item.paid ?? 0)) : [0],
                    backgroundColor: createGradient(tuitionCtx, '#1cc88a', '#17a673'),
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
                    labels: { padding: 15, font: { size: 12 } }
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
    
    // Debug: Check if canvas exists
    if (!courseGenderCtx) {
        console.error('Canvas element courseGenderChart not found!');
        return;
    }
    
    const courseGenderData = {!! json_encode($studentsByCourseGender) !!};
    console.log('Raw course gender data:', courseGenderData);
    
    // Simple and robust data processing
    let chartData = {
        labels: [],
        maleData: [],
        femaleData: []
    };
    
    if (courseGenderData && courseGenderData.length > 0) {
        // Group by course
        const courseGroups = {};
        
        courseGenderData.forEach(item => {
            const courseName = item.course?.name || 'Unknown Course';
            if (!courseGroups[courseName]) {
                courseGroups[courseName] = { male: 0, female: 0 };
            }
            
            if (item.sex === 'M') {
                courseGroups[courseName].male = item.total || 0;
            } else if (item.sex === 'F') {
                courseGroups[courseName].female = item.total || 0;
            }
        });
        
        // Convert to chart arrays
        Object.keys(courseGroups).forEach(courseName => {
            chartData.labels.push(courseName);
            chartData.maleData.push(courseGroups[courseName].male);
            chartData.femaleData.push(courseGroups[courseName].female);
        });
        
        console.log('Processed chart data:', chartData);
    }
    
    // Create chart
    if (chartData.labels.length === 0) {
        // No data chart
        new Chart(courseGenderCtx, {
            type: 'bar',
            data: {
                labels: ['No Data Available'],
                datasets: [{
                    label: 'No Students Found',
                    data: [1],
                    backgroundColor: '#e9ecef',
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    } else {
        // Real data chart
        new Chart(courseGenderCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Male Students',
                        data: chartData.maleData,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    },
                    {
                        label: 'Female Students',
                        data: chartData.femaleData,
                        backgroundColor: 'rgba(231, 74, 59, 0.8)',
                        borderColor: 'rgba(231, 74, 59, 1)',
                        borderWidth: 2,
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
                        labels: { 
                            padding: 20, 
                            font: { size: 12 },
                            usePointStyle: true
                        }
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
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: { 
                        grid: { display: false }
                    }
                }
            }
        });
    }
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