@extends('students.app')

@section('content')
<div class="container-fluid">
    <!-- Token Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0">
                        <i class="bi bi-coin text-warning me-2"></i>
                        Token Dashboard
                    </h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchaseModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Purchase Tokens
                    </button>
                </div>

                <!-- Token Balance Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Current Balance</h6>
                                        <h3 class="mb-0">{{ number_format($stats['total_tokens']) }}</h3>
                                        <small class="opacity-75">tokens available</small>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bi bi-wallet2" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Total Earned</h6>
                                        <h3 class="mb-0">{{ number_format($stats['total_earned']) }}</h3>
                                        <small class="opacity-75">tokens earned</small>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Total Spent</h6>
                                        <h3 class="mb-0">{{ number_format($stats['total_spent']) }}</h3>
                                        <small class="opacity-75">tokens spent</small>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bi bi-graph-down" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Need More Tokens?</h6>
                                        <div class="mt-2">
                                            <button class="btn btn-light btn-sm w-100" data-bs-toggle="modal" data-bs-target="#purchaseModal">
                                                <i class="bi bi-cart-plus me-1"></i>
                                                Buy Tokens Now
                                            </button>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bi bi-shop" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Recent Transactions
                    </h3>
                    <a href="{{ route('tokens.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list-ul me-1"></i>
                        View All Transactions
                    </a>
                </div>

                @if($stats['recent_transactions'] && $stats['recent_transactions']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_transactions']->take(10) as $transaction)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $transaction->created_at->format('M j, Y H:i') }}
                                    </span>
                                </td>
                                <td>{{ $transaction->description }}</td>
                                <td>
                                    @if($transaction->type === 'credit')
                                        <span class="badge bg-success text-white">Credit</span>
                                    @else
                                        <span class="badge bg-danger text-white">Debit</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->type === 'credit')
                                        <span class="text-success">+{{ number_format($transaction->amount) }}</span>
                                    @else
                                        <span class="text-danger">-{{ number_format($transaction->amount) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($transaction->balance_after) }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    <h5 class="mt-3 text-muted">No transactions yet</h5>
                    <p class="text-muted">Purchase tokens to access premium features and services</p>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#purchaseModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Purchase Your First Tokens
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Purchase Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseModalLabel">
                        <i class="bi bi-cart-plus me-2"></i>
                        Purchase Tokens
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Subscription Plans -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Subscription Plans</h6>
                            <div class="row g-2">
                                @foreach($subscriptions as $subscription)
                                <div class="col-12 mb-2">
                                    <div class="card h-100 @if($subscription->is_recommended) border-primary @else border-secondary">
                                        @if($subscription->is_recommended)
                                        <div class="card-header bg-primary text-white text-center py-1">
                                            <small class="fw-bold">RECOMMENDED</small>
                                        </div>
                                        @endif
                                        <div class="card-body text-center">
                                            <h6 class="card-title">{{ $subscription->name }}</h6>
                                            <div class="h4 mb-2">{{ $subscription->formatted_price }}</div>
                                            <small class="text-muted">{{ $subscription->formatted_tokens }}</small>
                                            <button onclick="purchaseSubscription({{ $subscription->id }})" 
                                                    class="btn @if($subscription->is_recommended) btn-primary @else btn-outline-primary @endif btn-sm w-100 mt-2">
                                                Select Plan
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom Purchase -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Custom Amount</h6>
                            <div class="card">
                                <div class="card-body">
                                    <form id="customTokenForm">
                                        <div class="mb-3">
                                            <label class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Tokens</span>
                                                <input type="number" 
                                                       id="tokenAmount" 
                                                       class="form-control" 
                                                       min="1" 
                                                       max="1000" 
                                                       value="50"
                                                       oninput="updateTotal()">
                                            </div>
                                            <div class="form-text">1-1000 tokens • 1 token = RWF 500</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Payment Reference (Optional)</label>
                                            <input type="text" 
                                                   id="paymentReference" 
                                                   class="form-control" 
                                                   placeholder="Transaction ID or reference">
                                        </div>

                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold">Total Amount:</span>
                                                        <span id="totalAmount" class="h4 mb-0 text-primary">RWF 25,000</span>
                                                    </div>
                                                    <div id="discountInfo" class="text-success small mt-2 hidden">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        You saved RWF <span id="discountAmount">0</span> with bulk discount!
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="button" onclick="purchaseCustomTokens()" class="btn btn-success">
                                                <i class="bi bi-credit-card me-2"></i>
                                                Purchase Now
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
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
// Update total amount with bulk discounts
function updateTotal() {
    const amount = parseInt(document.getElementById('tokenAmount').value) || 0;
    const basePrice = 500;
    let discount = 0;
    let bulkDiscount = false;
    
    if (amount >= 100) {
        discount = 0.10;
        bulkDiscount = true;
    } else if (amount >= 50) {
        discount = 0.05;
        bulkDiscount = true;
    }
    
    const totalPrice = amount * basePrice;
    const discountAmount = totalPrice * discount;
    const finalPrice = totalPrice - discountAmount;
    
    document.getElementById('totalAmount').textContent = `RWF ${finalPrice.toLocaleString()}`;
    
    const discountInfo = document.getElementById('discountInfo');
    const discountAmountEl = document.getElementById('discountAmount');
    
    if (bulkDiscount) {
        discountInfo.classList.remove('hidden');
        discountAmountEl.textContent = discountAmount.toLocaleString();
    } else {
        discountInfo.classList.add('hidden');
    }
}

// Purchase custom tokens
function purchaseCustomTokens() {
    const amount = parseInt(document.getElementById('tokenAmount').value);
    const reference = document.getElementById('paymentReference').value;
    
    if (amount < 1 || amount > 1000) {
        alert('Please enter a valid amount between 1 and 1000 tokens');
        return;
    }
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
    
    fetch('/flutterwave/payment/initialize-tokens', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount: amount,
            payment_reference: reference
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert('Token purchase initiated successfully!');
                location.reload();
            }
        } else {
            alert('Purchase failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Purchase Now';
    });
}

// Purchase subscription
function purchaseSubscription(subscriptionId) {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
    
    fetch('/flutterwave/payment/initialize-subscription', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            subscription_id: subscriptionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert('Subscription purchase initiated successfully!');
                location.reload();
            }
        } else {
            alert('Purchase failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Select Plan';
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});
</script>
@endsection
