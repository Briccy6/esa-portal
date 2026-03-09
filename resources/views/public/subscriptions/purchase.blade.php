@extends('layouts.app')

@section('title', 'Subscription Purchase - ESA Portal')

@section('content')
@include('components.public-navigation')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('public.subscriptions.index') }}" class="text-blue-600 hover:text-blue-500">Subscription Plans</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">{{ $subscription->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Subscription Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $subscription->name }}</h1>
                            <p class="text-gray-600 mt-2">{{ $subscription->description }}</p>
                        </div>
                        @if($subscription->is_recommended)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                Popular
                            </span>
                        @endif
                    </div>

                    <!-- Price Display -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-8">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">
                                {{ $subscription->formatted_price }}
                            </div>
                            <div class="text-gray-600">
                                Valid for {{ $subscription->duration_days }} days
                                <span class="text-sm">({{ $subscription->monthly_equivalent }})</span>
                            </div>
                            <div class="flex items-center justify-center mt-4">
                                <i class="fas fa-coins text-yellow-500 mr-2"></i>
                                <span class="text-lg font-semibold">{{ $subscription->formatted_tokens }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    @if($subscription->features)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">What's Included</h3>
                            <ul class="space-y-3">
                                @foreach($subscription->features as $feature)
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                        <span class="text-gray-700">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Payment Form -->
                    <div class="border-t pt-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Complete Your Purchase</h3>
                        
                        <form id="paymentForm" action="{{ route('public.subscriptions.payment.initialize', $subscription) }}" method="POST">
                            @csrf
                            
                            <!-- Payment Method Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <label class="relative">
                                        <input type="radio" name="payment_method" value="card" class="peer" checked>
                                        <div class="cursor-pointer rounded-lg border-2 border-gray-200 p-4 text-center hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                            <i class="fas fa-credit-card text-2xl text-gray-600 peer-checked:text-blue-600"></i>
                                            <p class="text-xs mt-2 font-medium">Card</p>
                                        </div>
                                    </label>
                                    
                                    <label class="relative">
                                        <input type="radio" name="payment_method" value="mobile_money" class="peer">
                                        <div class="cursor-pointer rounded-lg border-2 border-gray-200 p-4 text-center hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                            <i class="fas fa-mobile-alt text-2xl text-gray-600 peer-checked:text-blue-600"></i>
                                            <p class="text-xs mt-2 font-medium">Mobile Money</p>
                                        </div>
                                    </label>
                                    
                                    <label class="relative">
                                        <input type="radio" name="payment_method" value="bank_transfer" class="peer">
                                        <div class="cursor-pointer rounded-lg border-2 border-gray-200 p-4 text-center hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                            <i class="fas fa-university text-2xl text-gray-600 peer-checked:text-blue-600"></i>
                                            <p class="text-xs mt-2 font-medium">Bank Transfer</p>
                                        </div>
                                    </label>
                                    
                                    <label class="relative">
                                        <input type="radio" name="payment_method" value="ussd" class="peer">
                                        <div class="cursor-pointer rounded-lg border-2 border-gray-200 p-4 text-center hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                            <i class="fas fa-phone text-2xl text-gray-600 peer-checked:text-blue-600"></i>
                                            <p class="text-xs mt-2 font-medium">USSD</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="mb-6">
                                <label class="flex items-start">
                                    <input type="checkbox" name="terms" required class="mt-1 mr-3">
                                    <span class="text-sm text-gray-600">
                                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a> 
                                        and understand that this subscription will auto-renew unless cancelled.
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="payButton"
                                    class="w-full bg-blue-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <span id="buttonText">
                                    <i class="fas fa-lock mr-2"></i>
                                    Pay {{ $subscription->formatted_price }}
                                </span>
                                <span id="buttonSpinner" class="hidden">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subscription Plan</span>
                            <span class="font-medium">{{ $subscription->name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration</span>
                            <span class="font-medium">{{ $subscription->duration_days }} days</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tokens Included</span>
                            <span class="font-medium">{{ $subscription->formatted_tokens }}</span>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total</span>
                                <span class="text-2xl font-bold text-blue-600">{{ $subscription->formatted_price }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Badge -->
                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-semibold text-green-800">Secure Payment</p>
                                <p class="text-xs text-green-600">Your payment information is encrypted and secure</p>
                            </div>
                        </div>
                    </div>

                    <!-- Support -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 mb-2">Need help?</p>
                        <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');

    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            payButton.disabled = true;
            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');

            const formData = new FormData(paymentForm);
            
            fetch(paymentForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to Flutterwave payment page
                    window.location.href = data.redirect_url;
                } else {
                    // Show error and reset form
                    alert(data.message || 'Payment initialization failed');
                    payButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonSpinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                alert('Payment initialization failed. Please try again.');
                payButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            });
        });
    }
});
</script>
@endpush
