@extends('admin.layout')

@section('title', 'Client Details - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Client Details</h1>
            <p class="text-muted mb-0">{{ $client->name }} - {{ $client->email }}</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Client Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Information</h5>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Full Name:</strong> {{ $client->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $client->email }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $client->phone ?? 'Not provided' }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            @if($client->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                        <p class="mb-1"><strong>Joined:</strong> {{ $client->created_at->format('M j, Y') }}</p>
                        <p class="mb-0"><strong>Last Login:</strong> {{ $client->last_login ? $client->last_login->format('M j, Y H:i') : 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Token Statistics -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Token Statistics</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <p class="mb-0">Current Balance</p>
                                <h3 class="text-primary">{{ number_format($tokenStats['current_balance']) }}</h3>
                            </div>
                            <a href="{{ route('admin.clients.add-tokens', $client) }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i>
                                Add Tokens
                            </a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <p class="mb-0">Total Purchased</p>
                                <h4 class="text-success">{{ number_format($tokenStats['total_purchased']) }}</h4>
                            </div>
                            <div>
                                <p class="mb-0">Total Used</p>
                                <h4 class="text-warning">{{ number_format($tokenStats['total_used']) }}</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <p class="mb-0">Total Bonus</p>
                                <h4 class="text-info">{{ number_format($tokenStats['total_bonus']) }}</h4>
                            </div>
                            <div>
                                <p class="mb-0">Available Balance</p>
                                <h3 class="text-primary">{{ number_format($tokenStats['current_balance']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Recent Transactions</h5>
                <a href="{{ route('admin.clients.show', $client) }}#transactions" class="btn btn-outline-primary">
                    <i class="fas fa-history"></i>
                    View All Transactions
                </a>
            </div>
            
            @if($recentTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Tokens</th>
                                <th>Bonus</th>
                                <th>Total</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M j, Y H:i') }}</td>
                                    <td>
                                        @if($transaction->type === 'purchase')
                                            <span class="badge bg-success">
                                                <i class="fas fa-plus"></i>
                                                Purchase
                                            </span>
                                        @elseif($transaction->type === 'usage')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-minus"></i>
                                                Usage
                                            </span>
                                        @elseif($transaction->type === 'subscription')
                                            <span class="badge bg-info">
                                                <i class="fas fa-crown"></i>
                                                Subscription
                                            </span>
                                        @elseif($transaction->type === 'bonus')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-gift"></i>
                                                Bonus
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-question"></i>
                                                {{ $transaction->type }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->tokenPackage)
                                            {{ $transaction->tokenPackage->name }} Package
                                        @else
                                            {{ $transaction->description ?? 'Token transaction' }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $transaction->tokens ?? 0 }}</td>
                                    <td>
                                        @if($transaction->bonus_tokens > 0)
                                            <span class="text-success">+{{ $transaction->bonus_tokens }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $transaction->total_tokens ?? 0 }}</td>
                                    <td class="text-center">{{ number_format($transaction->amount ?? 0) }} RWF</td>
                                    <td>
                                        @if($transaction->status === 'completed')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i>
                                                Completed
                                            </span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i>
                                                Pending
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i>
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No transactions found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
