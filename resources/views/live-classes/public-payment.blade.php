@extends('admin.layout')

@section('title', 'Payment for Live Class')

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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select payment method</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
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
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        Proceed to Payment
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
