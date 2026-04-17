@extends('layouts.app')

@section('title', 'Payment Verification - ' . $liveClass->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-6">
                <div class="flex items-center">
                    <i class="fas fa-clock text-3xl mr-4"></i>
                    <div>
                        <h1 class="text-2xl font-bold">Payment Verification</h1>
                        <p class="mt-1 opacity-90">Your payment is being processed</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-green-800">Payment Submitted Successfully!</h3>
                                <p class="text-green-700 mt-1">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Payment Details -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-lg mb-4">Payment Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Live Class</p>
                            <p class="font-semibold">{{ $liveClass->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Method</p>
                            <p class="font-semibold">
                                @switch($payment->payment_method)
                                    @case('ussd')
                                        USSD Payment
                                    @case('agent_payment')
                                        Pay at ESA Center
                                    @case('voucher')
                                        Voucher Code
                                    @default
                                        Online Payment
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Transaction ID</p>
                            <p class="font-semibold">{{ $payment->transaction_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @switch($payment->status)
                                    @case('pending_verification')
                                        bg-yellow-100 text-yellow-800
                                    @case('completed')
                                        bg-green-100 text-green-800
                                    @case('failed')
                                        bg-red-100 text-red-800
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                @switch($payment->status)
                                    @case('pending_verification')
                                        <i class="fas fa-clock mr-2"></i> Pending Verification
                                    @case('completed')
                                        <i class="fas fa-check mr-2"></i> Completed
                                    @case('failed')
                                        <i class="fas fa-times mr-2"></i> Failed
                                    @default
                                        <i class="fas fa-question mr-2"></i> Unknown
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h3 class="font-semibold text-green-800 mb-3">
                        <i class="fas fa-check-circle mr-2"></i> Payment Successful!
                    </h3>
                    
                    <div class="text-sm text-green-700 space-y-2">
                        <p><strong>Great news!</strong> Your payment has been processed successfully.</p>
                        <p><strong>Payment Method:</strong> 
                            @switch($payment->payment_method)
                                @case('ussd')
                                    USSD Payment
                                @case('agent_payment')
                                    Pay at ESA Center
                                @case('voucher')
                                    Voucher Code
                                @case('card')
                                    Online Payment
                                @case('mobile_money')
                                    Mobile Money
                                @default
                                    Payment
                            @endswitch
                        </p>
                        <p><strong>Status:</strong> Payment completed - You now have full access!</p>
                        <p><strong>What to do:</strong> Click the button below to join the live class immediately.</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('live-classes.room', $liveClass) }}" 
                       class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors text-center">
                        <i class="fas fa-video mr-2"></i>
                        Join Live Class Now
                    </a>
                    
                    <a href="{{ route('live-classes.public-join', $liveClass) }}" 
                       class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Join Page
                    </a>
                </div>

                <!-- Support Information -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-headset mr-2"></i> Need Help?
                    </h3>
                    <div class="text-sm text-gray-700 space-y-2">
                        <p><strong>For USSD/Agent Payments:</strong> Call our support team at +250 788 123 456</p>
                        <p><strong>For Voucher Issues:</strong> Visit any ESA Center with your voucher code</p>
                        <p><strong>Business Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM</p>
                        <p><strong>Email:</strong> support@esa.rw</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkPaymentStatus() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';
    
    // Simulate checking payment status
    setTimeout(() => {
        // In a real implementation, this would call an API
        // For demo, we'll just reload the page
        window.location.reload();
    }, 2000);
}

// Auto-refresh for pending payments
document.addEventListener('DOMContentLoaded', function() {
    const status = '{{ $payment->status }}';
    
    if (status === 'pending_verification') {
        // Refresh every 30 seconds to check payment status
        setInterval(() => {
            window.location.reload();
        }, 30000);
    }
});
</script>
@endpush
