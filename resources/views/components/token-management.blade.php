<div x-data="{ 
    showTokenDetails: false,
    showPurchaseModal: false
}" class="bg-white rounded-lg shadow-md p-4 mb-6">
    <!-- Token Balance Header -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Token Balance</h3>
                <p class="text-sm text-gray-600">Available tokens</p>
            </div>
        </div>
        <button @click="showTokenDetails = !showTokenDetails" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </button>
    </div>

    <!-- Current Balance Display -->
    <div class="text-center mb-4">
        <div class="text-3xl font-bold text-gray-800">
            {{ number_format($studentToken->balance ?? 0) }}
        </div>
        <div class="text-sm text-gray-600">tokens</div>
    </div>

    <!-- Token Details (Collapsible) -->
    <div x-show="showTokenDetails" x-transition class="mb-4 p-3 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-600">Total Earned:</span>
                <span class="font-semibold text-green-600">{{ number_format($studentToken->total_earned ?? 0) }}</span>
            </div>
            <div>
                <span class="text-gray-600">Total Spent:</span>
                <span class="font-semibold text-red-600">{{ number_format($studentToken->total_spent ?? 0) }}</span>
            </div>
            @if($studentToken->last_earned_at)
            <div class="col-span-2">
                <span class="text-gray-600">Last Earned:</span>
                <span class="font-semibold">{{ $studentToken->last_earned_at->diffForHumans() }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Purchase Button -->
    <button @click="showPurchaseModal = true" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Purchase More Tokens
    </button>

    <!-- Purchase Modal -->
    <div x-show="showPurchaseModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="showPurchaseModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Purchase Tokens</h2>
                    <button @click="showPurchaseModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Subscription Plans -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Choose a Subscription Plan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    @foreach($subscriptions as $subscription)
                    <div class="border rounded-lg p-4 @if($subscription->is_recommended) border-blue-500 shadow-lg @else border-gray-200 @endif">
                        @if($subscription->is_recommended)
                        <div class="bg-blue-500 text-white text-xs font-semibold px-2 py-1 rounded-full inline-block mb-2">
                            RECOMMENDED
                        </div>
                        @endif
                        <h4 class="font-semibold text-lg text-gray-800">{{ $subscription->name }}</h4>
                        <div class="text-2xl font-bold text-gray-900 my-2">RWF {{ number_format($subscription->tokens_included * 500) }}</div>
                        <div class="text-sm text-gray-600 mb-3">{{ $subscription->formatted_tokens }}</div>
                        <div class="text-xs text-gray-500 mb-4">{{ $subscription->monthly_equivalent }}</div>
                        
                        @if($subscription->features)
                        <ul class="text-sm text-gray-600 space-y-1 mb-4">
                            @foreach($subscription->features as $feature)
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        
                        <button onclick="purchaseSubscription({{ $subscription->id }})" 
                                class="w-full @if($subscription->is_recommended) bg-blue-600 hover:bg-blue-700 @else bg-gray-600 hover:bg-gray-700 @endif text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Select Plan
                        </button>
                    </div>
                    @endforeach
                </div>

                <!-- Custom Token Purchase -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchase Custom Tokens</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <form id="custom-token-form" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Number of Tokens
                                    <span class="text-gray-500 font-normal">(1-1000 tokens)</span>
                                </label>
                                <input type="number" 
                                       id="token-amount" 
                                       name="token_amount" 
                                       min="1" 
                                       max="1000" 
                                       value="50"
                                       oninput="updateTotal()"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter number of tokens">
                                <div class="text-xs text-gray-500 mt-1">
                                    <span id="bulk-discount" class="text-green-600 font-semibold hidden">Bulk discount applied!</span>
                                    Token pricing: 1 token = RWF 500
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Payment Reference <span class="text-gray-500 font-normal">(Optional)</span>
                                </label>
                                <input type="text" 
                                       id="payment-reference" 
                                       name="payment_reference" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Transaction ID or payment reference">
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Total Amount:</span>
                                    <span id="total-amount" class="text-xl font-bold text-blue-900">RWF 25,000</span>
                                </div>
                                <div id="discount-info" class="text-xs text-green-600 mt-1 hidden">
                                    You saved RWF <span id="discount-amount">0</span> with bulk discount!
                                </div>
                            </div>

                            <button type="button" 
                                    onclick="purchaseCustomTokens()" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1l4 4 4-4-1m-5 4l4-4m-4 4v-4"/>
                                </svg>
                                Purchase Custom Tokens
                            </button>
                        </form>
                    </div>
                </div>

                    <button onclick="selectPaymentMethod('paypal')" class="payment-method border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 transition duration-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944 2.419c.103-.596.624-1.034 1.227-1.034h7.65c1.803 0 3.466.35 4.754 1.087.642.368 1.18.822 1.604 1.354.425.531.735 1.145.915 1.82.181.676.234 1.405.154 2.14-.079.735-.285 1.442-.603 2.083a6.193 6.193 0 01-1.274 1.748c-.525.5-1.15.917-1.854 1.227-.703.31-1.48.496-2.302.537l-.054.002h-1.866l-.722 4.203a.641.641 0 01-.633.541h-4.65z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium">PayPal</div>
                            <div class="text-xs text-gray-500">Global payments</div>
                        </div>
                    </button>

                    <button onclick="selectPaymentMethod('stripe')" class="payment-method border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 transition duration-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M13.976 8.843c0-1.295-.848-1.943-2.545-1.943H7.965v3.963h3.322c1.697 0 2.689-.648 2.689-2.02zm1.295 6.058c0-1.435-.992-2.158-2.976-2.158H7.965v4.315h4.238c1.984 0 3.068-.723 3.068-2.157zM12.151 2C7.398 2 4.448 4.948 4.448 9.701c0 2.707 1.34 4.79 3.543 5.683L4.448 22h4.238l2.976-5.683h2.545L17.207 22h4.238l-3.543-6.616c2.203-.893 3.543-2.976 3.543-5.683C21.445 4.948 18.497 2 13.744 2h-1.593z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium">Stripe</div>
                            <div class="text-xs text-gray-500">Cards & more</div>
                        </div>
                    </button>

                    <button onclick="selectPaymentMethod('mobile_money')" class="payment-method border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 transition duration-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17 1H7C5.34 1 4 2.34 4 4v16c0 1.66 1.34 3 3 3h10c1.66 0 3-1.34 3-3V4c0-1.66-1.34-3-3-3zm-5 20c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium">Mobile Money</div>
                            <div class="text-xs text-gray-500">MTN, Airtel</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update total amount and calculate bulk discounts
function updateTotal() {
    const tokenAmount = parseInt(document.getElementById('token-amount').value) || 0;
    const basePrice = 500; // RWF per token
    let totalPrice = tokenAmount * basePrice;
    let discount = 0;
    let bulkDiscount = false;
    
    // Calculate bulk discounts
    if (tokenAmount >= 100) {
        discount = 0.10; // 10% discount for 100+ tokens
        bulkDiscount = true;
    } else if (tokenAmount >= 50) {
        discount = 0.05; // 5% discount for 50+ tokens
        bulkDiscount = true;
    }
    
    const discountAmount = totalPrice * discount;
    const finalPrice = totalPrice - discountAmount;
    
    // Update UI
    document.getElementById('total-amount').textContent = `RWF ${finalPrice.toLocaleString()}`;
    
    const bulkDiscountEl = document.getElementById('bulk-discount');
    const discountInfoEl = document.getElementById('discount-info');
    const discountAmountEl = document.getElementById('discount-amount');
    
    if (bulkDiscount) {
        bulkDiscountEl.classList.remove('hidden');
        discountInfoEl.classList.remove('hidden');
        discountAmountEl.textContent = discountAmount.toLocaleString();
    } else {
        bulkDiscountEl.classList.add('hidden');
        discountInfoEl.classList.add('hidden');
    }
}

// Purchase custom tokens
function purchaseCustomTokens() {
    const tokenAmount = parseInt(document.getElementById('token-amount').value);
    const paymentReference = document.getElementById('payment-reference').value;
    
    if (tokenAmount < 1 || tokenAmount > 1000) {
        alert('Please enter a valid token amount between 1 and 1000');
        return;
    }
    
    // Show loading state
    event.target.disabled = true;
    event.target.innerHTML = 'Processing...';
    
    // Process custom token purchase
    fetch(`/tokens/purchase`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount: tokenAmount,
            payment_reference: paymentReference
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment successful! Tokens have been added to your account.');
            window.location.reload();
        } else {
            alert('Payment failed: ' + data.message);
            event.target.disabled = false;
            event.target.innerHTML = 'Purchase Custom Tokens';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        event.target.disabled = false;
        event.target.innerHTML = 'Purchase Custom Tokens';
    });
}

function purchaseSubscription(subscriptionId) {
    // Show loading state
    event.target.disabled = true;
    event.target.innerHTML = 'Processing...';
    
    // Store selected subscription
    window.selectedSubscription = subscriptionId;
    
    // Highlight payment methods section
    document.querySelectorAll('.payment-method').forEach(method => {
        method.classList.add('ring-2', 'ring-blue-500');
    });
}

function selectPaymentMethod(method) {
    if (!window.selectedSubscription) {
        alert('Please select a subscription plan first');
        return;
    }
    
    // Show loading state
    event.target.closest('.payment-method').style.opacity = '0.5';
    
    // Process payment
    fetch(`/tokens/purchase-subscription`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            subscription_id: window.selectedSubscription,
            payment_method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to payment gateway
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                // Show success message and reload
                alert('Payment successful! Tokens have been added to your account.');
                window.location.reload();
            }
        } else {
            alert('Payment failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Initialize total on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});
</script>
