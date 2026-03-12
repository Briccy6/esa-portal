@extends('layouts.app')

@section('title', 'Resource Payment - ESA Portal')

@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
@endphp

@push('styles')
<style>
    /* Modern Design System */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    :root {
        --primary-color: #1e293b;
        --secondary-color: #3b82f6;
        --accent-color: #06b6d4;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    /* Enhanced Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
        overflow-x: hidden;
        background: var(--bg-light);
    }

    /* Enhanced Typography */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        line-height: 1.2;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    /* Enhanced Resource Preview */
    .resource-preview {
        background: var(--gradient-primary);
        position: relative;
        overflow: hidden;
    }

    .resource-preview::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
        pointer-events: none;
    }

    .resource-preview > * {
        position: relative;
        z-index: 1;
    }

    /* Enhanced Payment Cards */
    .payment-card {
        background: var(--bg-white);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .payment-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
        border-color: var(--secondary-color);
    }

    /* Enhanced Buttons */
    .btn-primary {
        background: var(--gradient-primary);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: var(--gradient-primary);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: var(--bg-light);
        color: var(--text-dark);
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: 2px solid var(--border-color);
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-secondary:hover {
        background: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Enhanced Forms */
    .form-input {
        background: var(--bg-white);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 10px rgba(30, 64, 175, 0.3);
        background: var(--bg-white);
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    /* Enhanced Alert Styles */
    .alert {
        padding: 1rem;
    .payment-field {
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .payment-field.show {
        opacity: 1;
        transform: translateY(0);
    }

    /* Info Cards */
    .info-card {
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid;
        transition: all 0.3s ease;
    }

    .info-card.blue {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #3b82f6;
        color: #1e40af;
    }

    .info-card.green {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-color: #10b981;
        color: #065f46;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Price Display */
    .price-display {
        font-size: 3rem;
        font-weight: 800;
        background: var(--gradient-success);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    /* Resource Details */
    .resource-detail-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        transition: all 0.3s ease;
    }

    .resource-detail-item:hover {
        padding-left: 0.5rem;
        background: rgba(59, 130, 246, 0.05);
        margin: 0 -0.5rem;
        padding-right: 0.5rem;
        border-radius: 8px;
    }

    .resource-detail-item:last-child {
        border-bottom: none;
    }

    .resource-detail-item i {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient-primary);
        color: white;
        border-radius: 10px;
        margin-right: 1rem;
        font-size: 0.9rem;
    }

    /* Loading States */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .price-display {
            font-size: 2.5rem;
        }
        
        .payment-card {
            margin: 1rem;
            border-radius: 12px;
        }
        
        .btn-primary {
            width: 100%;
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        .info-card {
            padding: 1rem;
        }
    }

    @media (max-width: 640px) {
        .resource-preview {
            padding: 1.5rem !important;
        }
        
        .resource-preview h2 {
            font-size: 1.5rem !important;
        }
        
        .price-display {
            font-size: 2rem;
        }
    }

    /* Touch-friendly enhancements */
    @media (hover: none) and (pointer: coarse) {
        .btn-primary {
            min-height: 48px;
            min-width: 48px;
        }
        
        .form-input {
            min-height: 48px;
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
</style>
@endpush

@section('content')
@include('components.public-navigation')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('error'))
            <div class="info-card red mb-6" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #ef4444; color: #991b1b;">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mr-3 mt-1"></i>
                    <div>
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Resource Summary Card -->
        <div class="payment-card mb-8">
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

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-info-circle mr-3 text-blue-600"></i>
                                Resource Details
                            </h3>
                            <div class="space-y-1">
                                @if($resource->teacher)
                                    <div class="resource-detail-item">
                                        <i class="fas fa-user-tie"></i>
                                        <div>
                                            <p class="font-semibold text-gray-900">Instructor</p>
                                            <p class="text-gray-600">{{ $resource->teacher->teacher_name }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if($resource->course)
                                    <div class="resource-detail-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <div>
                                            <p class="font-semibold text-gray-900">Course</p>
                                            <p class="text-gray-600">{{ $resource->course->course_name }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if($resource->lesson)
                                    <div class="resource-detail-item">
                                        <i class="fas fa-book-open"></i>
                                        <div>
                                            <p class="font-semibold text-gray-900">Lesson</p>
                                            <p class="text-gray-600">{{ $resource->lesson->title }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="resource-detail-item">
                                    <i class="fas fa-crown"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900">Access Type</p>
                                        <p class="text-gray-600">Premium Resource</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center lg:text-right flex flex-col justify-center">
                            <div class="mb-4">
                                <p class="text-lg font-semibold text-gray-700 mb-2">Access Fee</p>
                                <div class="price-display mb-3">{{ number_format($resource->price) }}</div>
                                <p class="text-sm text-gray-500">RWF - One-time payment</p>
                            </div>
                            <div class="inline-flex items-center justify-center lg:justify-end bg-green-50 px-4 py-2 rounded-full">
                                <i class="fas fa-infinity text-green-600 mr-2"></i>
                                <span class="text-green-800 font-medium">Lifetime Access</span>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Payment Form Card -->
        <div class="payment-card" x-data="paymentForm">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-blue-600"></i>
                    Complete Your Payment
                </h3>
                <p class="text-gray-600 mb-8">Choose your preferred payment method to access this resource</p>

            <form id="payment-form" @submit.prevent="processPayment">
                @csrf

                <!-- Customer Information -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Customer Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                            <input type="text"
                                   name="name"
                                   required
                                   class="form-input"
                                   placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                            <input type="email"
                                   name="email"
                                   required
                                   class="form-input"
                                   placeholder="john@example.com">
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel"
                               name="phone_number"
                               required
                               class="form-input"
                               placeholder="+250 788 123 456">
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-wallet mr-2 text-blue-600"></i>
                        Choose Payment Method *
                    </label>
                    <div class="payment-method-select">
                        <select name="payment_method" id="paymentMethodSelect" x-model="selectedMethod" 
                                class="form-input appearance-none"
                                onchange="togglePaymentFields()">
                            <option value="">Select payment method</option>
                        
                        <!-- Online Payment Options -->
                        <optgroup label="Online Payment (Recommended)">
                            <option value="mobile_money">Mobile Money (WhatsApp/Internet Required)</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </optgroup>
                        
                        <!-- Offline Payment Options -->
                        <optgroup label="Offline Payment (No Internet Required)">
                            <option value="ussd">USSD Code (No Internet)</option>
                            <option value="agent_payment">Pay at ESA Center</option>
                            <option value="voucher">Voucher Code</option>
                        </optgroup>
                    </select>
                </div>

                <!-- USSD Payment Instructions -->
                <div id="ussdField" class="payment-field hidden mb-8">
                    <div class="info-card blue">
                        <h4 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            USSD Payment Instructions
                        </h4>
                        <div class="space-y-3">
                            <div class="bg-white bg-opacity-60 rounded-lg p-4">
                                <p class="font-semibold mb-2">Step-by-Step:</p>
                                <ol class="space-y-2 text-sm">
                                    <li><strong>Dial:</strong> <code class="bg-blue-100 px-2 py-1 rounded">*123*456#</code></li>
                                    <li><strong>Select:</strong> Option 3 (Resource Payment)</li>
                                    <li><strong>Enter Amount:</strong> <code class="bg-blue-100 px-2 py-1 rounded">{{ number_format($resource->price) }} RWF</code></li>
                                    <li><strong>Reference:</strong> <code class="bg-blue-100 px-2 py-1 rounded">RESOURCE-{{ $resource->id }}-{{ date('Ymd') }}</code></li>
                                </ol>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Save the transaction ID and enter it below after payment
                                </p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                USSD Transaction ID <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="ussd_transaction_id" 
                                   placeholder="Enter USSD transaction ID"
                                   class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Agent Payment Instructions -->
                <div id="agentField" class="payment-field hidden mb-8">
                    <div class="info-card green">
                        <h4 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-building mr-3"></i>
                            Pay at ESA Center
                        </h4>
                        <div class="space-y-3">
                            <div class="bg-white bg-opacity-60 rounded-lg p-4">
                                <p class="font-semibold mb-2">Visit any ESA Center with these details:</p>
                                <ul class="space-y-2 text-sm">
                                    <li><strong>Resource:</strong> {{ $resourceName }}</li>
                                    <li><strong>Amount:</strong> {{ number_format($resource->price) }} RWF</li>
                                    <li><strong>Reference:</strong> RESOURCE-{{ $resource->id }}-{{ date('Ymd') }}</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    You will receive a receipt with verification code
                                </p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Receipt/Verification Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="agent_receipt_code" 
                                   placeholder="Enter receipt code from ESA center"
                                   class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Voucher Payment -->
                <div id="voucherField" class="payment-field hidden mb-8">
                    <div class="info-card blue" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-color: #9333ea; color: #6b21a8;">
                        <h4 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-ticket-alt mr-3"></i>
                            Voucher Payment
                        </h4>
                        <div class="space-y-3">
                            <div class="bg-white bg-opacity-60 rounded-lg p-4">
                                <p class="font-semibold mb-2">Enter your voucher code to access this resource:</p>
                                <p class="text-sm">Vouchers can be purchased at ESA centers or authorized agents</p>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Make sure your voucher has sufficient balance for this resource
                                </p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Voucher Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="voucher_code" 
                                   placeholder="Enter voucher code"
                                   class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Mobile Money Network (hidden until mobile_money selected) -->
                <div id="networkField" class="payment-field hidden mb-8">
                    <div class="bg-blue-50 rounded-xl p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-mobile-alt mr-2 text-blue-600"></i>
                            Mobile Money Network <span class="text-red-500">*</span>
                        </label>
                        <select name="network" id="networkSelect"
                                class="form-input">
                            <option value="">Select network</option>
                            <option value="MTN">MTN Mobile Money</option>
                            <option value="AIRTEL">Airtel Money</option>
                            <option value="TIGO">Tigo Cash</option>
                        </select>
                    </div>
                </div>

                <!-- Payment Button -->
                <div class="text-center">
                    <button type="submit"
                            :disabled="processing"
                            class="btn-primary text-lg px-12 py-4">
                        <template x-if="!processing">
                            <span>
                                <i class="fas fa-lock mr-3"></i>
                                Pay {{ number_format($resource->price) }} RWF
                            </span>
                        </template>
                        <template x-if="processing">
                            <span>
                                <div class="loading-spinner mr-3"></div>
                                Processing Payment...
                            </span>
                        </template>
                    </button>
                </div>

                <!-- Security Notice -->
                <div class="info-card blue mt-8">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-2xl mr-4 mt-1"></i>
                        <div>
                            <h4 class="font-bold text-lg mb-2">Secure Payment</h4>
                            <p class="text-sm leading-relaxed">Your payment information is encrypted and secure. We use Flutterwave for safe payment processing with industry-standard security measures.</p>
                        </div>
                    </div>
                </div>

                <!-- Access Information -->
                <div class="info-card green mt-6">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-2xl mr-4 mt-1"></i>
                        <div>
                            <h4 class="font-bold text-lg mb-2">What You Get</h4>
                            <ul class="text-sm space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                                    <span>Immediate access to {{ $resourceName }}</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                                    <span>Download or stream the resource</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                                    <span>Lifetime access to this resource</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                                    <span>Support from our team</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Footer -->
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="mb-4">
                    <div class="w-16 h-16 bg-white bg-opacity-10 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-2">{{ $systemName }}</h3>
                <p class="text-gray-400 mb-6">Empowering education through quality resources</p>
                <div class="flex items-center justify-center space-x-4 text-sm">
                    <span class="flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-green-400"></i>
                        Secure Payments
                    </span>
                    <span class="text-gray-600">•</span>
                    <span class="flex items-center">
                        <i class="fas fa-lock mr-2 text-blue-400"></i>
                        Flutterwave Powered
                    </span>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cryptico/0.0.134a/cryptico.min.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
    // Payment field toggling function
    function togglePaymentFields() {
        const method = document.getElementById('paymentMethodSelect').value;
        
        // Hide all fields first
        const fields = ['ussdField', 'agentField', 'voucherField', 'networkField'];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('hidden');
                field.classList.remove('show');
            }
        });
        
        // Show relevant field with animation
        let targetField = null;
        switch(method) {
            case 'mobile_money':
                targetField = document.getElementById('networkField');
                break;
            case 'ussd':
                targetField = document.getElementById('ussdField');
                break;
            case 'agent_payment':
                targetField = document.getElementById('agentField');
                break;
            case 'voucher':
                targetField = document.getElementById('voucherField');
                break;
        }
        
        if (targetField) {
            targetField.classList.remove('hidden');
            // Add animation class after a small delay for smooth transition
            setTimeout(() => {
                targetField.classList.add('show');
            }, 10);
        }
    }

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
            selectedMethod: '',
            processing: false,

            processPayment() {
                this.processing = true;

                const form = document.getElementById('payment-form');
                const formData = new FormData(form);
                const paymentMethod = formData.get('payment_method');

                // Handle offline payments differently
                if (paymentMethod === 'ussd' || paymentMethod === 'agent_payment' || paymentMethod === 'voucher') {
                    this.processOfflinePayment(formData, paymentMethod);
                    return;
                }

                // Handle online payments with Flutterwave
                const txRef = 'RESOURCE_{{ $resource->id }}_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                const paymentData = {
                    tx_ref: txRef,
                    amount: {{ $resource->price }},
                    currency: 'RWF',
                    payment_options: paymentMethod === 'mobile_money' ? 'mobilemoneyrwanda' : paymentMethod,
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
                        network: formData.get('network'),
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
            },

            processOfflinePayment(formData, paymentMethod) {
                // Submit form normally for offline payments
                const form = document.getElementById('payment-form');
                const submitBtn = form.querySelector('button[type="submit"]');
                
                // Update button text
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                
                // Create form data for offline payment
                const offlineFormData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: offlineFormData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to success page
                        window.location.href = data.redirect_url || '{{ route("public.resources.payment.success", $resource) }}';
                    } else {
                        this.processing = false;
                        submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Pay {{ number_format($resource->price) }} RWF';
                        alert('Payment processing failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    this.processing = false;
                    submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Pay {{ number_format($resource->price) }} RWF';
                    console.error('Error:', error);
                    alert('Payment processing failed. Please try again.');
                });
            }
        }));
    });

    // Initialize payment fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePaymentFields();
    });
</script>
@endpush
