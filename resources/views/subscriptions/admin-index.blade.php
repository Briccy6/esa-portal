@extends('admin.layout')

@section('title', 'Subscription Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Subscription Management</h1>
        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Subscription
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crown fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Student Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\StudentSubscription::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($subscriptions->sum('price'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Subscriptions</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Tier</th>
                            <th>Tokens</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Recommended</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                        <tr>
                            <td>
                                <strong>{{ $subscription->name }}</strong>
                                @if($subscription->description)
                                <br><small class="text-muted">{{ Str::limit($subscription->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $subscription->tier === 'premium' ? 'warning' : ($subscription->tier === 'standard' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($subscription->tier) }}
                                </span>
                            </td>
                            <td>{{ $subscription->tokens_included }}</td>
                            <td>${{ number_format($subscription->price, 2) }}</td>
                            <td>{{ $subscription->duration_days }} days</td>
                            <td>
                                @if($subscription->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($subscription->is_recommended)
                                    <span class="badge badge-warning">Recommended</span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.subscriptions.toggle', $subscription->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $subscription->is_active ? 'warning' : 'success' }}">
                                            <i class="fas fa-{{ $subscription->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.subscriptions.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus"></i> Create New Subscription
                        </a>
                        <a href="{{ route('admin.subscriptions.student-subscriptions') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users"></i> View Student Subscriptions
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="exportSubscriptions()">
                            <i class="fas fa-download"></i> Export Subscriptions Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @php
                        $recentStudentSubs = \App\Models\StudentSubscription::with(['student', 'subscription'])
                            ->latest()->take(5)->get();
                    @endphp
                    @if($recentStudentSubs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentStudentSubs as $studentSub)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $studentSub->student->full_name }}</strong>
                                        <br><small class="text-muted">{{ $studentSub->subscription->name }}</small>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-{{ $studentSub->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($studentSub->payment_status) }}
                                        </span>
                                        <br><small class="text-muted">{{ $studentSub->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No recent subscription activity</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportSubscriptions() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
}
</script>
@endsection
