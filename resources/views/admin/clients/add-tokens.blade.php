@extends('admin.layout')

@section('title', 'Add Tokens - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add Tokens</h1>
            <p class="text-muted mb-0">{{ $client->name }} - {{ $client->email }}</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left"></i>
                Back to Client
            </a>
        </div>
    </div>

    <!-- Add Tokens Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Add Tokens Manually</h5>
                    
                    <form method="POST" action="{{ route('admin.clients.store-tokens', $client) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Token Package</label>
                                    <select name="package_id" class="form-select">
                                        <option value="">Select a package (optional)</option>
                                        @foreach($tokenPackages as $package)
                                            <option value="{{ $package->id }}">
                                                {{ $package->name }} - {{ $package->tokens }} tokens ({{ $package->bonus_tokens }} bonus)
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Or leave empty to add custom amount</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Tokens to Add *</label>
                                        <input type="number" name="amount" class="form-control" 
                                               min="1" max="10000" required
                                               placeholder="Enter number of tokens">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Bonus Tokens</label>
                                        <input type="number" name="bonus_tokens" class="form-control" 
                                               min="0" max="1000" 
                                               placeholder="Optional bonus tokens">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Transaction Type *</label>
                                        <select name="type" class="form-select" required>
                                            <option value="purchase">Purchase</option>
                                            <option value="bonus">Bonus</option>
                                            <option value="manual">Manual Addition</option>
                                            <option value="subscription">Subscription</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Status *</label>
                                        <select name="payment_status" class="form-select" required>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <input type="text" name="payment_method" class="form-control" 
                                               placeholder="e.g., Cash, Bank Transfer, Mobile Money">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Reference</label>
                                        <input type="text" name="payment_reference" class="form-control" 
                                               placeholder="Transaction reference number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Amount (RWF)</label>
                                        <input type="number" name="payment_amount" class="form-control" 
                                               step="0.01" min="0" 
                                               placeholder="Amount paid">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3" 
                                                  placeholder="Reason for adding tokens">{{ $client->name }} - {{ $client->email }}</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Current Balance Display -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>Current Balance: {{ number_format($client->getCurrentTokens()) }} tokens</h4>
                    <p class="mb-0">This balance will be updated after adding tokens</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.clients.subscription', $client) }}" class="btn btn-outline-info">
                            <i class="fas fa-crown"></i>
                            Record Subscription
                        </a>
                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i>
                            View Client Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
