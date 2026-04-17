@extends('admin.layout')

@section('title', 'Add Manual Tokens - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add Manual Tokens</h1>
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
                    <h5 class="card-title mb-4">Add Tokens to Client</h5>
                    
                    <!-- Info Alert -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Subscription-Based Tokens:</strong> When you select a subscription package, the system will automatically:
                        <ul class="mb-0 mt-2">
                            <li>Add the included tokens to the client's balance</li>
                            <li>Create a subscription record with the proper duration ({{ config('app.default_subscription_duration', 30) }} days)</li>
                            <li>Set the subscription end date based on the subscription's duration_days</li>
                            <li>Track the subscription separately from manual tokens</li>
                        </ul>
                        <hr class="my-2">
                        <small class="text-muted">For manual token addition without subscription, leave the subscription field empty.</small>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.clients.store-tokens', $client) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Subscription Package <small class="text-muted">(auto-creates subscription with duration)</small></label>
                                    <select name="subscription_id" id="subscriptionSelect" class="form-select">
                                        <option value="">Select a subscription package (optional)</option>
                                        @foreach($subscriptions as $subscription)
                                            <option value="{{ $subscription->id }}" 
                                                    data-tokens="{{ $subscription->tokens_included }}"
                                                    data-price="{{ $subscription->price }}"
                                                    data-name="{{ $subscription->name }}"
                                                    data-duration="{{ $subscription->duration_days }}">
                                                {{ $subscription->name }} - {{ $subscription->tokens_included }} tokens ({{ $subscription->duration_days }} days)
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Selecting a package will automatically create a subscription that expires in the specified duration.</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Tokens to Add *</label>
                                        <input type="number" name="amount" id="tokenAmount" class="form-control" 
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
                                        <label class="form-label">Payment Amount (RWF)</label>
                                        <input type="number" name="payment_amount" id="paymentAmount" class="form-control" 
                                               step="0.01" min="0" 
                                               placeholder="Amount paid">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" 
                                                  placeholder="Reason for adding tokens">{{ $client->name }} - {{ $client->email }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Add Tokens to Client
                                        </button>
                                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subscriptionSelect = document.getElementById('subscriptionSelect');
    const tokenAmountInput = document.getElementById('tokenAmount');
    const paymentAmountInput = document.getElementById('paymentAmount');
    const descriptionInput = document.getElementById('description');
    
    subscriptionSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value && selectedOption) {
            // Auto-populate token amount
            const tokens = selectedOption.getAttribute('data-tokens');
            const price = selectedOption.getAttribute('data-price');
            const name = selectedOption.getAttribute('data-name');
            const duration = selectedOption.getAttribute('data-duration');
            
            tokenAmountInput.value = tokens;
            paymentAmountInput.value = price;
            descriptionInput.value = `Purchased ${name} subscription (${duration} days)`;
            
            // Show a subtle indication that fields were auto-populated
            tokenAmountInput.style.backgroundColor = '#e8f5e8';
            paymentAmountInput.style.backgroundColor = '#e8f5e8';
            descriptionInput.style.backgroundColor = '#e8f5e8';
            
            // Remove highlight after 2 seconds
            setTimeout(() => {
                tokenAmountInput.style.backgroundColor = '';
                paymentAmountInput.style.backgroundColor = '';
                descriptionInput.style.backgroundColor = '';
            }, 2000);
            
            console.log(`Selected subscription: ${name} - ${tokens} tokens for RWF ${price} (${duration} days)`);
        } else {
            // Clear fields when no subscription is selected
            tokenAmountInput.value = '';
            paymentAmountInput.value = '';
            // Reset description to default client info
            descriptionInput.value = '{{ $client->name }} - {{ $client->email }}';
        }
    });
});
</script>
@endsection
