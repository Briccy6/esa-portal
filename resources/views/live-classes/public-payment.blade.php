@extends('layouts.app')

@section('title', 'Payment for Live Class - ' . $liveClass->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-500 text-white p-6">
                <h1 class="text-2xl font-bold">Complete Payment</h1>
                <p class="mt-2 opacity-90">Pay to access this live class</p>
            </div>

            <div class="p-6">

                {{-- Success / Error flash messages --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

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

                <!-- Amount -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold">Amount Due:</span>
                        <span class="text-2xl font-bold text-yellow-600">
                            {{ strtoupper($liveClass->currency) }} {{ number_format($liveClass->public_fee, 2) }}
                        </span>
                    </div>
                </div>

                {{-- =====================================================
                     IMPORTANT: id="paymentForm" is required by the JS
                     below. Do NOT remove it.
                     ===================================================== --}}
                <form id="paymentForm"
                      action="{{ route('live-classes.payment.process', $liveClass) }}"
                      method="POST"
                      class="space-y-4">
                    @csrf

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required
                                   value="{{ old('email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                   placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" required
                               value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="+250 788 123 456">
                        <p class="text-xs text-gray-500 mt-1">Required for mobile money payments</p>
                    </div>

                    @if($liveClass->public_fee > 0)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" id="paymentMethodSelect"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    onchange="togglePaymentFields()">
                                <option value="">Select payment method</option>
                                
                                {{-- Online Payment Options --}}
                                <optgroup label="Online Payment (Recommended)">
                                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>
                                        Credit / Debit Card
                                    </option>
                                    <option value="mobile_money" {{ old('payment_method') === 'mobile_money' ? 'selected' : '' }}>
                                        Mobile Money (WhatsApp/Internet Required)
                                    </option>
                                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                                        Bank Transfer
                                    </option>
                                </optgroup>
                                
                                {{-- Offline Payment Options --}}
                                <optgroup label="Offline Payment (No Internet Required)">
                                    <option value="ussd" {{ old('payment_method') === 'ussd' ? 'selected' : '' }}>
                                        USSD Code (No Internet)
                                    </option>
                                    <option value="agent_payment" {{ old('payment_method') === 'agent_payment' ? 'selected' : '' }}>
                                        Pay at ESA Center
                                    </option>
                                    <option value="voucher" {{ old('payment_method') === 'voucher' ? 'selected' : '' }}>
                                        Voucher Code
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        {{-- USSD Payment Instructions --}}
                        <div id="ussdField" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-semibold text-blue-800 mb-2">
                                <i class="fas fa-phone"></i> USSD Payment Instructions
                            </h4>
                            <div class="text-sm text-blue-700 space-y-2">
                                <p><strong>Dial:</strong> *123*456#</p>
                                <p><strong>Option:</strong> 2 (Live Class Payment)</p>
                                <p><strong>Enter Amount:</strong> {{ $liveClass->public_fee }} {{ $liveClass->currency }}</p>
                                <p><strong>Reference:</strong> {{ session('payment_reference') ?? 'LIVE-' . $liveClass->id . '-' . time() }}</p>
                                <p class="mt-2 p-2 bg-yellow-100 rounded text-yellow-800">
                                    <i class="fas fa-info-circle"></i>
                                    Save the transaction ID and enter it below after payment
                                </p>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    USSD Transaction ID <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="ussd_transaction_id" 
                                       placeholder="Enter USSD transaction ID"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>
                        </div>

                        {{-- Agent Payment Instructions --}}
                        <div id="agentField" class="hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h4 class="font-semibold text-green-800 mb-2">
                                <i class="fas fa-building"></i> Pay at ESA Center
                            </h4>
                            <div class="text-sm text-green-700 space-y-2">
                                <p><strong>Visit any ESA Center</strong> with the following details:</p>
                                <ul class="list-disc list-inside ml-4 space-y-1">
                                    <li>Class: {{ $liveClass->title }}</li>
                                    <li>Amount: {{ $liveClass->public_fee }} {{ $liveClass->currency }}</li>
                                    <li>Reference: LIVE-{{ $liveClass->id }}-{{ date('Ymd') }}</li>
                                </ul>
                                <p class="mt-2 p-2 bg-yellow-100 rounded text-yellow-800">
                                    <i class="fas fa-info-circle"></i>
                                    You will receive a receipt with verification code
                                </p>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Receipt/Verification Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="agent_receipt_code" 
                                       placeholder="Enter receipt code from ESA center"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>
                        </div>

                        {{-- Voucher Payment --}}
                        <div id="voucherField" class="hidden mb-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <h4 class="font-semibold text-purple-800 mb-2">
                                <i class="fas fa-ticket-alt"></i> Voucher Payment
                            </h4>
                            <div class="text-sm text-purple-700 space-y-2">
                                <p>Enter your voucher code to access this live class:</p>
                                <p class="mt-2 p-2 bg-yellow-100 rounded text-yellow-800">
                                    <i class="fas fa-info-circle"></i>
                                    Vouchers can be purchased at ESA centers or authorized agents
                                </p>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Voucher Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="voucher_code" 
                                       placeholder="Enter voucher code"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>
                        </div>

                    @endif

                    <!-- Mobile Money Network (hidden until mobile_money selected) -->
                    <div id="networkField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mobile Money Network <span class="text-red-500">*</span>
                        </label>
                        <select name="network" id="networkSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select network</option>
                            @php
                                $currency = strtoupper($liveClass->currency);
                                $networks = [
                                    'RWF' => ['MTN' => 'MTN Mobile Money', 'AIRTEL' => 'Airtel Money', 'TIGO' => 'Tigo Cash'],
                                    'GHS' => ['MTN' => 'MTN Mobile Money', 'VODOFONE' => 'Vodafone Cash', 'TIGO' => 'Tigo Cash'],
                                    'UGX' => ['MTN' => 'MTN Mobile Money', 'AIRTEL' => 'Airtel Money'],
                                    'XAF' => ['MTN' => 'MTN Mobile Money', 'ORANGE' => 'Orange Money'],
                                    'ZMW' => ['MTN' => 'MTN Mobile Money', 'ZAMTEL' => 'Zamtel Kwacha'],
                                    'TZS' => ['AIRTEL' => 'Airtel Money', 'TIGO' => 'Tigo Pesa', 'HALOPESA' => 'Halopesa', 'VODOFONE' => 'Vodacom M-Pesa'],
                                ];
                                $currencyNetworks = $networks[$currency] ?? ['MTN' => 'MTN Mobile Money'];
                            @endphp
                            @foreach($currencyNetworks as $value => $label)
                                <option value="{{ $value }}" {{ old('network') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-2">
                        <input type="checkbox" id="terms" name="terms" required class="mt-1">
                        <label for="terms" class="text-sm text-gray-600">
                            I agree to the terms and conditions for accessing this live class
                        </label>
                    </div>

                    <!-- Error message area -->
                    <div id="paymentError" class="hidden p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm"></div>

                    <!-- Submit Button -->
                    <button type="submit" id="paymentSubmitBtn"
                            class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
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
                            <p>Your payment is processed securely by Flutterwave. You will be redirected to complete payment.</p>
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
/**
 * Payment form handler.
 *
 * The form action returns JSON with { success, redirect_url }.
 * We must intercept the submit with JS, call the endpoint via fetch,
 * then do window.location.href = redirect_url to send the user to
 * Flutterwave's hosted checkout page.
 *
 * WITHOUT this intercept the browser does a native POST and just
 * displays the raw JSON on screen — which is the bug we are fixing.
 */
document.addEventListener('DOMContentLoaded', function () {

    const form         = document.getElementById('paymentForm');
    const submitBtn    = document.getElementById('paymentSubmitBtn');
    const btnText      = document.getElementById('paymentBtnText');
    const spinner      = document.getElementById('paymentSpinner');
    const methodSelect = document.getElementById('paymentMethodSelect');
    const networkField = document.getElementById('networkField');
    const networkSelect = document.getElementById('networkSelect');
    const ussdField    = document.getElementById('ussdField');
    const agentField   = document.getElementById('agentField');
    const voucherField  = document.getElementById('voucherField');
    const errorBox     = document.getElementById('paymentError');

    // ── Show / hide payment method fields ────────────────────────
    function togglePaymentFields() {
        const method = methodSelect.value;
        
        // Hide all fields first
        networkField.classList.add('hidden');
        ussdField.classList.add('hidden');
        agentField.classList.add('hidden');
        voucherField.classList.add('hidden');
        
        // Show relevant field
        switch(method) {
            case 'mobile_money':
                networkField.classList.remove('hidden');
                networkSelect.required = true;
                break;
            case 'ussd':
                ussdField.classList.remove('hidden');
                break;
            case 'agent_payment':
                agentField.classList.remove('hidden');
                break;
            case 'voucher':
                voucherField.classList.remove('hidden');
                break;
        }
    }
    
    methodSelect.addEventListener('change', togglePaymentFields);
    togglePaymentFields(); // run on load in case old() repopulated field

    // ── Helpers ──────────────────────────────────────────────────────
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('hidden');
        window.scrollTo({ top: errorBox.offsetTop - 20, behavior: 'smooth' });
    }

    function resetBtn() {
        submitBtn.disabled = false;
        btnText.classList.remove('hidden');
        spinner.classList.add('hidden');
    }

    function setBusy() {
        errorBox.classList.add('hidden');
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        spinner.classList.remove('hidden');
    }

    // ── Form submit intercept ────────────────────────────────────────
    form.addEventListener('submit', async function (e) {
        e.preventDefault(); // stop the browser doing a native POST

        setBusy();

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                // NOTE: do NOT manually set Content-Type here.
                // The browser sets it automatically with the correct
                // multipart boundary when using FormData.
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            });

            let data;
            try {
                data = await response.json();
            } catch (parseErr) {
                showError('Unexpected server response. Please try again.');
                resetBtn();
                return;
            }

            if (data.success && data.redirect_url) {
                // ✅ Redirect the user to Flutterwave's hosted checkout page
                // This is where they enter their mobile money PIN / card details
                window.location.href = data.redirect_url;
                // Keep button disabled during redirect
            } else {
                showError(data.message || 'Payment initialization failed. Please try again.');
                resetBtn();
            }

        } catch (networkErr) {
            console.error('Payment fetch error:', networkErr);
            showError('Network error. Please check your connection and try again.');
            resetBtn();
        }
    });

});
</script>
@endpush