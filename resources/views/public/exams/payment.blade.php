@extends('layouts.app')

@section('title', 'Exam Payment - Excellent Servant Academy')

@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
@endphp

@section('content')
@include('components.public-navigation')

@push('styles')
<link href="https://checkout.flutterwave.com/v3.js" rel="preload">
<style>
    :root {
        --primary-color: #6f42c1;
        --secondary-color: #36b9cc;
        --accent-color: #e74a3b;
        --text-dark: #263238;
        --text-light: #455a64;
        --bg-light: #ecf0f1;
        --bg-white: #ffffff;
        --border-color: #cfd8dc;
    }

    .payment-method-card {
        transition: all 0.3s ease;
        border: 2px solid var(--border-color);
    }

    .payment-method-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }

    .payment-method-card.selected {
        border-color: var(--primary-color);
        background-color: #f3f4f6;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #5a32a3;
        transform: translateY(-2px);
    }

    .exam-preview {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Exam Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h2>
                    <div class="text-gray-600 space-y-1">
                        <p><i class="fas fa-question-circle mr-2"></i>{{ $exam->questions->count() }} Questions</p>
                        <p><i class="fas fa-clock mr-2"></i>{{ $exam->duration ?? '60' }} Minutes</p>
                        @if($exam->course)
                            <p><i class="fas fa-graduation-cap mr-2"></i>{{ $exam->course->course_name }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-sm text-gray-600 mb-2">Amount to Pay</p>
                    <p class="text-4xl font-bold text-green-600">{{ number_format($exam->public_price) }} RWF</p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Complete Your Payment</h3>
            
            <form id="payment-form" @submit.prevent="processPayment">
                @csrf
                
                <!-- Customer Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" 
                               name="name" 
                               required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" 
                               name="email" 
                               required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="john@example.com">
                    </div>
                </div>

                <!-- Phone Number (for Mobile Money) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" 
                           name="phone_number" 
                           required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="+250 788 123 456">
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Choose Payment Method *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="payment-method-card cursor-pointer rounded-lg p-4" @click="selectedMethod = 'mobile_money'">
                            <input type="radio" name="payment_method" value="mobile_money" x-model="selectedMethod" class="hidden" required>
                            <div class="flex items-center">
                                <i class="fas fa-mobile-alt text-2xl text-green-600 mr-3"></i>
                                <div>
                                    <p class="font-medium">Mobile Money</p>
                                    <p class="text-sm text-gray-600">MTN, Airtel, Tigo Money</p>
                                </div>
                            </div>
                        </label>

                        <label class="payment-method-card cursor-pointer rounded-lg p-4" @click="selectedMethod = 'card'">
                            <input type="radio" name="payment_method" value="card" x-model="selectedMethod" class="hidden">
                            <div class="flex items-center">
                                <i class="fas fa-credit-card text-2xl text-blue-600 mr-3"></i>
                                <div>
                                    <p class="font-medium">Credit/Debit Card</p>
                                    <p class="text-sm text-gray-600">Visa, Mastercard, etc.</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Button -->
                <div class="text-center">
                    <button type="submit" 
                            :disabled="processing"
                            class="btn-primary px-8 py-3 rounded-lg font-semibold text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!processing">
                            <i class="fas fa-lock mr-2"></i>
                            Pay {{ number_format($exam->public_price) }} RWF
                        </template>
                        <template x-if="processing">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Processing Payment...
                        </template>
                    </button>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-1">Secure Payment</h4>
                            <p class="text-sm text-blue-800">Your payment information is encrypted and secure. We use Flutterwave for safe payment processing.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.</p>
                <p class="mt-2 text-gray-400">Secure payments powered by Flutterwave</p>
            </div>
        </div>
    </footer>

    <script>
        function makeFlutterwavePayment(paymentData) {
            return FlutterwaveCheckout({
                public_key: '{{ $flutterwavePublicKey }}',
                tx_ref: paymentData.tx_ref,
                amount: paymentData.amount,
                currency: paymentData.currency,
                payment_options: paymentData.payment_options,
                customer: paymentData.customer,
                customizations: paymentData.customizations,
                callback: function(response) {
                    // Redirect to success page
                    window.location.href = response.data.redirect_url || '{{ route("public.exams.payment.success", $exam) }}?reference=' + paymentData.tx_ref;
                },
                onclose: function() {
                    // Payment modal closed
                    console.log('Payment modal closed');
                },
                callbackContext: {
                    examId: {{ $exam->id }}
                }
            });
        }

        document.addEventListener('alpine:init', () => {
            return {
                selectedMethod: 'mobile_money',
                processing: false,
                
                processPayment() {
                    this.processing = true;
                    
                    const form = document.getElementById('payment-form');
                    const formData = new FormData(form);
                    
                    // Generate unique transaction reference
                    const txRef = 'EXAM_{{ $exam->id }}_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    
                    const paymentData = {
                        tx_ref: txRef,
                        amount: {{ $exam->public_price }},
                        currency: 'RWF',
                        payment_options: this.selectedMethod === 'mobile_money' ? 'mobilemoneyrwanda' : this.selectedMethod,
                        customer: {
                            email: formData.get('email'),
                            name: formData.get('name'),
                            phone_number: formData.get('phone_number'),
                        },
                        customizations: {
                            title: 'Exam Payment: {{ $exam->title }}',
                            description: 'Payment for exam: {{ $exam->title }}',
                        },
                        meta: {
                            exam_id: {{ $exam->id }},
                            exam_title: '{{ $exam->title }}',
                            payment_type: 'exam_fee',
                        },
                    };

                    // Submit form data to server first to create pending payment record
                    fetch('{{ route("public.exams.payment.process", $exam) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: formData.get('name'),
                            email: formData.get('email'),
                            phone_number: formData.get('phone_number'),
                            payment_method: this.selectedMethod,
                            tx_ref: txRef
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            // Open Flutterwave payment modal
                            makeFlutterwavePayment(paymentData);
                        } else {
                            this.processing = false;
                            alert('Payment initialization failed: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        this.processing = false;
                        console.error('Error:', error);
                        alert('Payment initialization failed. Please try again.');
                    });
                }
            }
        });
    </script>
</body>
</html>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <p class="font-medium">Credit/Debit Card</p>
                            <p class="text-sm text-gray-600">Visa, Mastercard</p>
                        </div>
                    </label>

                    <label class="payment-method-card cursor-pointer">
                        <input type="radio" name="payment_method" value="bank_transfer" class="hidden">
                        <div class="border-2 border-gray-200 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                            <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="font-medium">Bank Transfer</p>
                            <p class="text-sm text-gray-600">Direct deposit</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Mobile Money Details -->
            <div id="mobile-money-details" class="hidden mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-900 mb-3">Mobile Money Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone_number" placeholder="+250 7XX XXX XXX" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                            <select name="provider" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Select Provider</option>
                                <option value="mtn">MTN Mobile Money</option>
                                <option value="airtel">Airtel Money</option>
                                <option value="tigo">Tigo Cash</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Details -->
            <div id="card-details" class="hidden mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-3">Card Details</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                <input type="text" name="expiry" placeholder="MM/YY" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                <input type="text" name="cvv" placeholder="123" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                            <input type="text" name="cardholder_name" placeholder="John Doe" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Details -->
            <div id="bank-transfer-details" class="hidden mb-6">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h4 class="font-semibold text-purple-900 mb-3">Bank Transfer Instructions</h4>
                    <div class="bg-white rounded-lg p-4 border border-purple-200">
                        <p class="text-sm text-gray-700 mb-3">Please make a direct deposit to our bank account:</p>
                        <div class="space-y-2 font-mono text-sm">
                            <p><strong>Bank:</strong> Bank of Kigali</p>
                            <p><strong>Account Name:</strong> Excellent Servant Academy</p>
                            <p><strong>Account Number:</strong> 440123456789</p>
                            <p><strong>Amount:</strong> {{ $exam->formatted_public_price }}</p>
                            <p><strong>Reference:</strong> EXAM_{{ $exam->id }}_{{ uniqid() }}</p>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">After making the payment, please send the deposit slip to payments@esa.ac.rw</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" required placeholder="your@email.com" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="full_name" required placeholder="John Doe" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" name="terms" required class="mt-1 mr-2">
                    <span class="text-sm text-gray-600">
                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> 
                        and understand that this payment is for a single attempt at the exam. 
                        No refunds will be provided after payment is processed.
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <a href="{{ route('public.exams.show', $exam) }}" 
                   class="text-gray-600 hover:text-gray-800 font-medium">
                    ← Back to Exam Details
                </a>
                <button type="submit" id="submit-payment" 
                        class="bg-green-600 text-white py-3 px-8 rounded-lg hover:bg-green-700 transition-colors font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Complete Payment
                </button>
            </div>
        </form>
    </div>

    <!-- Security Notice -->
    <div class="mt-6 text-center">
        <div class="inline-flex items-center text-sm text-gray-600">
            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            Secure payment powered by industry-standard encryption
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const mobileMoneyDetails = document.getElementById('mobile-money-details');
    const cardDetails = document.getElementById('card-details');
    const bankTransferDetails = document.getElementById('bank-transfer-details');
    const submitButton = document.getElementById('submit-payment');

    function hideAllPaymentDetails() {
        mobileMoneyDetails.classList.add('hidden');
        cardDetails.classList.add('hidden');
        bankTransferDetails.classList.add('hidden');
    }

    function updatePaymentMethodCards() {
        document.querySelectorAll('.payment-method-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            const div = card.querySelector('div');
            if (radio.checked) {
                div.classList.remove('border-gray-200');
                div.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                div.classList.remove('border-blue-500', 'bg-blue-50');
                div.classList.add('border-gray-200');
            }
        });
    }

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            hideAllPaymentDetails();
            updatePaymentMethodCards();

            switch(this.value) {
                case 'mobile_money':
                    mobileMoneyDetails.classList.remove('hidden');
                    break;
                case 'card':
                    cardDetails.classList.remove('hidden');
                    break;
                case 'bank_transfer':
                    bankTransferDetails.classList.remove('hidden');
                    submitButton.textContent = 'Continue to Bank Transfer';
                    submitButton.disabled = false;
                    return;
            }
            
            submitButton.textContent = 'Complete Payment';
            submitButton.disabled = false;
        });
    });

    // Form validation
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) {
            e.preventDefault();
            alert('Please select a payment method');
            return;
        }

        if (selectedMethod.value === 'mobile_money') {
            const phoneNumber = document.querySelector('input[name="phone_number"]').value;
            const provider = document.querySelector('select[name="provider"]').value;
            
            if (!phoneNumber || !provider) {
                e.preventDefault();
                alert('Please fill in all mobile money details');
                return;
            }
        }

        if (selectedMethod.value === 'card') {
            const cardNumber = document.querySelector('input[name="card_number"]').value;
            const expiry = document.querySelector('input[name="expiry"]').value;
            const cvv = document.querySelector('input[name="cvv"]').value;
            const cardholderName = document.querySelector('input[name="cardholder_name"]').value;
            
            if (!cardNumber || !expiry || !cvv || !cardholderName) {
                e.preventDefault();
                alert('Please fill in all card details');
                return;
            }
        }

        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
    });
});
</script>
@endsection
