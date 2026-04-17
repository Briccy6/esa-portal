@extends('admin.layout')

@section('title', 'Record Subscription - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Record Subscription</h1>
            <p class="text-muted mb-0">{{ $client->name }} - {{ $client->email }}</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left"></i>
                Back to Client
            </a>
        </div>
    </div>

    <!-- Record Subscription Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Record Subscription Payment</h5>
                    
                    <form method="POST" action="{{ route('admin.clients.store-subscription', $client) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Subscription Plan *</label>
                                    <select name="subscription_id" class="form-select" required>
                                        <option value="">Select a subscription plan</option>
                                        @foreach($subscriptions as $subscription)
                                            <option value="{{ $subscription->id }}">
                                                {{ $subscription->name }} - {{ number_format($subscription->price) }} RWF/month ({{ $subscription->duration_months }} months)
                                            </option>
                                        @endforeach
                                    </select>
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
                                        <label class="form-label">Payment Amount (RWF) *</label>
                                        <input type="number" name="payment_amount" class="form-control" 
                                               step="0.01" min="0" required
                                               placeholder="Amount paid by client">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Reference</label>
                                        <input type="text" name="payment_reference" class="form-control" 
                                               placeholder="Transaction reference number">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subscription Start Date</label>
                                        <input type="date" name="starts_at" class="form-control" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subscription End Date</label>
                                        <input type="date" name="ends_at" class="form-control" 
                                               placeholder="Leave empty for auto-calculation">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info mb-3">
                                        <h6><i class="fas fa-info-circle"></i> Important Information</h6>
                                        <ul class="mb-0">
                                            <li>If payment status is "Completed", {{ $client->name }} will receive {{ $subscriptions->first()->tokens_included ?? 0 }} tokens immediately</li>
                                            <li>Subscription will be activated and client can access premium features</li>
                                            <li>End date will be automatically calculated as start date + duration if not provided</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Record Subscription
                            </button>
                            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Subscription Status -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Current Subscription Status</h5>
                    @if($client->hasActiveSubscription())
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Active Subscription</h6>
                            <p><strong>Plan:</strong> {{ $client->activeSubscription->subscription->name }}</p>
                            <p><strong>Started:</strong> {{ $client->activeSubscription->starts_at->format('M j, Y') }}</p>
                            <p><strong>Ends:</strong> {{ $client->activeSubscription->ends_at->format('M j, Y') }}</p>
                            <p><strong>Tokens Included:</strong> {{ number_format($client->activeSubscription->subscription->tokens_included) }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> No Active Subscription</h6>
                            <p>{{ $client->name }} does not have an active subscription.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
