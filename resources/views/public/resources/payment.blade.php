@extends('layouts.app')

@section('title', 'Resource Payment - ESA Portal')

@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
@endphp

@section('content')
@include('components.public-navigation')

@push('styles')
<link href="https://checkout.flutterwave.com/v3.js" rel="preload">
<style>
    :root {

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

    .btn-primary:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .resource-preview {
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

        <!-- Resource Summary -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            @if($resource->document_blob)
                <div class="resource-preview text-white p-6">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-4xl mr-4"></i>
                        <div>
                            <h2 class="text-2xl font-bold mb-1">{{ $resourceName }}</h2>
                            <p class="opacity-90">Document Resource</p>
                        </div>
                    </div>
                </div>
            @elseif($resource->video_blob)
                <div class="resource-preview text-white p-6">
                    <div class="flex items-center">
                        <i class="fas fa-video text-4xl mr-4"></i>
                        <div>
                            <h2 class="text-2xl font-bold mb-1">{{ $resourceName }}</h2>
                            <p class="opacity-90">Video Resource</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="resource-preview text-white p-6">
                    <div class="flex items-center">
                        <i class="fas fa-link text-4xl mr-4"></i>
                        <div>
                            <h2 class="text-2xl font-bold mb-1">{{ $resourceName }}</h2>
                            <p class="opacity-90">External Resource</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resource Details</h3>
                        <div class="space-y-3 text-sm">
                            @if($resource->teacher)
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-user-tie mr-3 text-purple-600"></i>
                                    <span>{{ $resource->teacher->teacher_name }}</span>
                                </div>
                            @endif
                            @if($resource->course)
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-graduation-cap mr-3 text-purple-600"></i>
                                    <span>{{ $resource->course->course_name }}</span>
                                </div>
                            @endif
                            @if($resource->lesson)
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-book-open mr-3 text-purple-600"></i>
                                    <span>{{ $resource->lesson->title }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-tag mr-3 text-purple-600"></i>
                                <span>Premium Resource</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center md:text-right">
                        <p class="text-sm text-gray-600 mb-2">Access Fee</p>
                        <p class="text-4xl font-bold text-green-600">{{ number_format($resource->price) }} RWF</p>
                        <p class="text-sm text-gray-500 mt-2">One-time payment for lifetime access</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-lg p-6" x-data="paymentForm">
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

                <!-- Phone Number -->
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
                            <span><i class="fas fa-lock mr-2"></i>Pay {{ number_format($resource->price) }} RWF</span>
                        </template>
                        <template x-if="processing">
                            <span><i class="fas fa-spinner fa-spin mr-2"></i>Processing Payment...</span>
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

                <!-- Access Information -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-semibold text-green-900 mb-1">What You Get</h4>
                            <ul class="text-sm text-green-800 space-y-1">
                                <li>• Immediate access to {{ $resourceName }}</li>
                                <li>• Download or stream the resource</li>
                                <li>• Lifetime access to this resource</li>
                                <li>• Support from our team</li>
                            </ul>
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

@endsection

@push('scripts')
<script src="https://checkout.flutterwave.com/v3.js"></script>
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
                window.location.href = response.data?.redirect_url
                    || '{{ route("public.resources.payment.success", $resource) }}?reference=' + paymentData.tx_ref;
            },
            onclose: function() {
                console.log('Payment modal closed');
            }
        });
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('paymentForm', () => ({
            selectedMethod: 'mobile_money',
            processing: false,

            processPayment() {
                this.processing = true;

                const form = document.getElementById('payment-form');
                const formData = new FormData(form);

                const txRef = 'RESOURCE_{{ $resource->id }}_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                const paymentData = {
                    tx_ref: txRef,
                    amount: {{ $resource->price }},
                    currency: 'RWF',
                    payment_options: this.selectedMethod === 'mobile_money' ? 'mobilemoneyrwanda' : this.selectedMethod,
                    customer: {
                        email: formData.get('email'),
                        name: formData.get('name'),
                        phone_number: formData.get('phone_number'),
                    },
                    customizations: {
                        title: 'Resource Access: {{ addslashes($resourceName) }}',
                        description: 'Payment for resource: {{ addslashes($resourceName) }}',
                        logo: '{{ \App\Helpers\LogoHelper::getLogoUrl() ?: asset("images/default-logo.png") }}'
                    },
                    meta: {
                        resource_id: {{ $resource->id }},
                        resource_type: '{{ $resource->document_blob ? "document" : "video" }}',
                        payment_type: 'resource_access',
                    },
                };

                fetch('{{ route("public.resources.payment.process", $resource) }}', {
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
        }));
    });
</script>
@endpush
