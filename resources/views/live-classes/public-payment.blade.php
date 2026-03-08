@extends('layouts.app')

@section('title', 'Payment for Live Class - {{ $liveClass->title }}')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-500 text-white p-6">
                <h1 class="text-2xl font-bold">Complete Payment</h1>
                <p class="mt-2 opacity-90">Pay to access this live class</p>
            </div>

            <div class="p-6">
                <!-- Class Details -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-lg mb-2">{{ $liveClass->title }}</h3>
                    <p class="text-gray-600 mb-2">{{ $liveClass->description }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($liveClass->start_time)->format('M j, Y H:i') }} - 
                            {{ \Carbon\Carbon::parse($liveClass->end_time)->format('H:i') }}
                        </span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('live-classes.payment.process', $liveClass) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Amount Display -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Amount Due:</span>
                            <span class="text-2xl font-bold text-yellow-600">
                                {{ $liveClass->currency }} {{ number_format($liveClass->public_fee, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number (Optional)</label>
                        <input type="tel" name="phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="+250 788 123 456">
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                id="paymentMethod">
                            <option value="">Select payment method</option>
                            @if($liveClass->currency === 'RWF')
                                <option value="mobile_money">Mobile Money (MTN, Airtel, Tigo)</option>
                                <option value="ussd">USSD</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            @else
                                <option value="card">Credit/Debit Card</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="ussd">USSD</option>
                            @endif
                        </select>
                        @if($liveClass->currency === 'RWF')
                            <p class="text-xs text-gray-500 mt-1">For RWF payments: Mobile Money, USSD, and Bank Transfer are supported</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">All payment methods available</p>
                        @endif
                    </div>

                    <!-- Mobile Money Network (shown only when mobile money is selected) -->
                    <div id="networkField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Money Network *</label>
                        <select name="network" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @if($liveClass->currency === 'RWF')
                                <option value="">Select network</option>
                                <option value="MTN">MTN Mobile Money</option>
                                <option value="AIRTEL">Airtel Money</option>
                                <option value="TIGO">Tigo Cash</option>
                            @elseif($liveClass->currency === 'GHS')
                                <option value="">Select network</option>
                                <option value="MTN">MTN Mobile Money</option>
                                <option value="VODAFONE">Vodafone Cash</option>
                                <option value="TIGO">Tigo Cash</option>
                            @elseif($liveClass->currency === 'UGX')
                                <option value="">Select network</option>
                                <option value="MTN">MTN Mobile Money</option>
                                <option value="AIRTEL">Airtel Money</option>
                            @elseif($liveClass->currency === 'TZS')
                                <option value="">Select network</option>
                                <option value="AIRTEL">Airtel Money</option>
                                <option value="TIGO">Tigo Pesa</option>
                                <option value="HALOPESA">Halopesa</option>
                                <option value="VODAFONE">Vodacom M-Pesa</option>
                            @else
                                <option value="">Select network</option>
                                <option value="MTN">MTN Mobile Money</option>
                            @endif
                        </select>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" name="terms" required class="mr-2">
                        <label for="terms" class="text-sm text-gray-600">
                            I agree to the terms and conditions for accessing this live class
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="paymentSubmitBtn"
                            class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        <span id="paymentBtnText">Proceed to Payment</span>
                        <span id="paymentSpinner" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                        </span>
                    </button>
                </form>

                <!-- Security Notice -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-blue-600 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Secure Payment</p>
                            <p>Your payment information is encrypted and secure. You will receive a confirmation email after successful payment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.querySelector('form[action*="payment/process"]');
    const submitBtn = document.getElementById('paymentSubmitBtn');
    const btnText = document.getElementById('paymentBtnText');
    const spinner = document.getElementById('paymentSpinner');
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const networkField = document.getElementById('networkField');

    // Show/hide network field based on payment method
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'mobile_money') {
                networkField.classList.remove('hidden');
            } else {
                networkField.classList.add('hidden');
            }
        });
    }

    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');

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
                    submitBtn.disabled = false;
                    btnText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                alert('Payment initialization failed. Please try again.');
                submitBtn.disabled = false;
                btnText.classList.remove('hidden');
                spinner.classList.add('hidden');
            });
        });
    }
});
</script>
@endpush
