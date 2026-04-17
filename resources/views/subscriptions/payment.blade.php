@extends('students.app')

@section('title', 'Complete Payment - Subscription')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1">Complete Payment</h2>
                    <p class="text-muted mb-0">Complete your subscription payment to activate your plan</p>
                </div>
                <div>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Subscriptions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Details -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-star me-2"></i>Subscription Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ $studentSubscription->subscription->name }}</h6>
                            <p class="text-muted mb-2">{{ $studentSubscription->subscription->description ?? 'No description available' }}</p>
                            <div class="mb-3">
                                <span class="badge bg-primary">
                                    <i class="bi bi-coin me-1"></i>{{ $studentSubscription->subscription->tokens_included }} Tokens Included
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-end">
                                <h4 class="text-primary">{{ number_format($studentSubscription->amount, 0) }} RWF</h4>
                                <p class="text-muted">{{ $studentSubscription->subscription->duration_days }} days</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($studentSubscription->subscription->features)
                        <hr>
                        <h6 class="mb-3">Features Included:</h6>
                        <ul class="list-unstyled">
                            @foreach($studentSubscription->subscription->features as $feature)
                                <li class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Subscription Price:</span>
                        <strong>{{ number_format($studentSubscription->amount, 0) }} RWF</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Processing Fee:</span>
                        <strong>0 RWF</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Total Amount:</h5>
                        <h5 class="text-primary">{{ number_format($studentSubscription->amount, 0) }} RWF</h5>
                    </div>
                    
                    @if($studentSubscription->payment_status === 'pending')
                        <div class="alert alert-warning">
                            <i class="bi bi-clock me-2"></i>
                            Payment pending - Complete payment to activate subscription
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Options -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>Payment Options
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Mobile Money -->
                        <div class="col-md-6 mb-4">
                            <div class="payment-option">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-phone payment-icon"></i>
                                        <h6 class="mt-3">Mobile Money</h6>
                                        <p class="text-muted">Pay with MTN, Airtel Money or other mobile money services</p>
                                        <button class="btn btn-outline-primary" onclick="showMobileMoneyForm()">
                                            <i class="bi bi-phone me-1"></i>Pay with Mobile Money
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bank Card -->
                        <div class="col-md-6 mb-4">
                            <div class="payment-option">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-credit-card payment-icon"></i>
                                        <h6 class="mt-3">Credit/Debit Card</h6>
                                        <p class="text-muted">Pay with Visa, Mastercard or other bank cards</p>
                                        <button class="btn btn-outline-primary" onclick="showCardForm()">
                                            <i class="bi bi-credit-card me-1"></i>Pay with Card
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Reference Form -->
                    <div id="paymentReferenceForm" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Enter Payment Reference</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('subscriptions.payment.complete', $studentSubscription) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="payment_reference" class="form-label">Payment Reference Number</label>
                                                <input type="text" class="form-control" 
                                                       id="payment_reference" name="payment_reference" 
                                                       value="{{ old('payment_reference') }}" 
                                                       placeholder="Enter transaction reference from your mobile money or bank payment"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">&nbsp;</label><br>
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="bi bi-check-circle me-1"></i>Complete Payment
                                                </button>
                                            </div>
                                        </div>
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

<style>
.payment-icon {
    font-size: 3rem;
    color: #667eea;
}

.payment-option .card {
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.payment-option .card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.payment-option .card-body {
    padding: 2rem;
}

@media (max-width: 768px) {
    .payment-option .card-body {
        padding: 1.5rem;
    }
    
    .payment-icon {
        font-size: 2rem;
    }
}
</style>

@section('scripts')
<script>
function showMobileMoneyForm() {
    document.getElementById('paymentReferenceForm').style.display = 'block';
    document.getElementById('payment_reference').focus();
    document.getElementById('payment_reference').placeholder = 'Enter mobile money transaction ID';
}

function showCardForm() {
    document.getElementById('paymentReferenceForm').style.display = 'block';
    document.getElementById('payment_reference').focus();
    document.getElementById('payment_reference').placeholder = 'Enter bank transaction reference';
}

// Auto-scroll to payment form when shown
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.target.style.display === 'block') {
            mutation.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});

const paymentForm = document.getElementById('paymentReferenceForm');
if (paymentForm) {
    observer.observe(paymentForm, { attributes: true, attributeFilter: ['style'] });
}
</script>
@endsection
