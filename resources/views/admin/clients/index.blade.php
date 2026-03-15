@extends('admin.layout')

@section('title', 'Client Management - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Client Management</h1>
            <p class="text-muted mb-0">Manage students, tokens, and subscriptions</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-users"></i>
                All Clients
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">{{ number_format($stats['total_clients']) }}</h3>
                            <p class="card-text text-muted">Total Clients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-success text-white me-3">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">{{ number_format($stats['active_clients']) }}</h3>
                            <p class="card-text text-muted">Active Clients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-warning text-white me-3">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">{{ number_format($stats['total_tokens_sold']) }}</h3>
                            <p class="card-text text-muted">Tokens Sold</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-info text-white me-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h3 class="card-title mb-1">{{ number_format($stats['total_revenue']) }} RWF</h3>
                            <p class="card-text text-muted">Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.clients.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by name, email, or phone...">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Clients</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-danger w-100">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card">
        <div class="card-body">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Tokens</th>
                                <th>Subscription</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs bg-primary text-white me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $client->name }}</div>
                                                <small class="text-muted">{{ $client->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $client->getCurrentTokens() }}</span>
                                    </td>
                                    <td>
                                        @if($client->hasActiveSubscription())
                                            <span class="badge bg-success">
                                                {{ $client->activeSubscription->subscription->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Subscription</span>
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
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.clients.show', $client) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.clients.subscription', $client) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Record Subscription">
                                                <i class="fas fa-crown"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.clients.toggle-status', $client) }}" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to toggle this client\\'s status?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        title="Toggle Status">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $clients->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>No Clients Found</h4>
                    <p class="text-muted">
                        @if(request('search') || request('status'))
                            No clients found matching your criteria.
                        @else
                            No clients have been registered yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
