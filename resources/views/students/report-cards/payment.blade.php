@extends('students.app')

@section('title', 'Report Card Payment')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="max-w-md mx-auto">
        <!-- Alert Box -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-2">Report Card Access Blocked</h3>
                    <p class="text-sm text-red-800 mb-4">
                        You are not allowed to access the report card due to unpaid fees.
                    </p>
                    <p class="text-base font-semibold text-red-900">
                        Outstanding Balance: <span class="text-2xl">RWF {{ number_format($outstanding, 2) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pay Outstanding Fees</h2>
            
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
                <p class="text-sm text-gray-700 mb-2">
                    <strong>Amount to Pay:</strong>
                </p>
                <p class="text-2xl font-bold text-blue-600">
                    RWF {{ number_format($outstanding, 2) }}
                </p>
            </div>

            <div class="space-y-3 mb-6">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-check-circle text-green-600"></i>
                    Secure payment via Flutterwave
                </p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-credit-card text-blue-600"></i>
                    Multiple payment options available
                </p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-mobile-alt text-purple-600"></i>
                    Mobile money, card, and bank transfers accepted
                </p>
            </div>

            <form action="{{ route('flutterwave.payment.initialize-reportcard-fees') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="{{ $outstanding }}">
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-700 hover:to-green-800 transition duration-300 flex items-center justify-center gap-2"
                >
                    <i class="fas fa-lock"></i>
                    Pay Now with Flutterwave
                </button>
            </form>

            <p class="text-xs text-gray-500 text-center mt-4">
                Your payment information is secure and encrypted
            </p>
        </div>

        <!-- Info Section -->
        <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">What happens after payment?</h3>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>✓ Your payment will be processed immediately</li>
                <li>✓ You can access your report card instantly</li>
                <li>✓ A payment receipt will be sent to your email</li>
                <li>✓ Your account will be updated automatically</li>
            </ul>
        </div>
    </div>
</div>
@endsection
