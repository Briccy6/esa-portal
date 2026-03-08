@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Mobile Money Payment</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Complete your token purchase using Mobile Money (MTN or Airtel)
                    </p>
                </div>

                <!-- Subscription Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Subscription Summary</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Plan:</span>
                            <span class="font-semibold text-blue-900">{{ $studentSubscription->subscription->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tokens:</span>
                            <span class="font-semibold text-blue-900">{{ $studentSubscription->tokens_included }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-semibold text-blue-900">RWF {{ number_format($studentSubscription->amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-semibold text-blue-900">{{ $studentSubscription->subscription->duration_days }} days</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('tokens.mobile-money.process', $studentSubscription->id) }}" method="POST">
                    @csrf
                    
                    <!-- Provider Selection -->
                    <div class="mb-6">
                        <label for="provider" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Mobile Money Provider
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative">
                                <input type="radio" name="provider" value="mtn" class="peer sr-only" required>
                                <div class="p-4 border-2 rounded-lg cursor-pointer transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">MTN Mobile Money</div>
                                            <div class="text-sm text-gray-600">Dial *182#</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio" name="provider" value="airtel" class="peer sr-only" required>
                                <div class="p-4 border-2 rounded-lg cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold">Airtel Money</div>
                                            <div class="text-sm text-gray-600">Dial *182#</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-6">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Money Phone Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">+250</span>
                            </div>
                            <input type="tel" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   placeholder="788123456" 
                                   pattern="[0-9]{9}" 
                                   required>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">
                            Enter your 9-digit phone number (without +250)
                        </p>
                    </div>

                    <!-- Payment Instructions -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">Payment Instructions:</h4>
                        <ol class="text-sm text-yellow-700 space-y-1 list-decimal list-inside">
                            <li>Click "Complete Payment" to generate your payment request</li>
                            <li>You will receive a USSD prompt on your phone</li>
                            <li>Enter your Mobile Money PIN to confirm the payment</li>
                            <li>Wait for payment confirmation (usually takes 1-2 minutes)</li>
                            <li>Your tokens will be automatically added to your account</li>
                        </ol>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <a href="{{ route('dashboard') }}" class="mr-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Complete Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
