@extends('layouts.app')

@section('title', 'Token Purchase - ESA Portal')

@section('content')
@include('components.public-navigation')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="mx-auto h-12 w-auto mb-4">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-4">Purchase Tokens</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Get more tokens to practice exams and improve your skills. 
                The more tokens you buy, the more you save!
            </p>
        </div>

        <!-- Token Packages -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($tokenPackages as $package)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 {{ $package['popular'] ? 'ring-4 ring-blue-500' : '' }}">
                    @if($package['popular'])
                        <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                            MOST POPULAR
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="text-4xl font-bold text-blue-600 mb-2">
                                {{ $package['tokens'] }} 
                                <i class="fas fa-coins text-yellow-500 ml-2"></i>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $package['formatted_price'] }}
                            </div>
                            <div class="text-sm text-gray-600">
                                @if($package['discount'] > 0)
                                    <span class="text-green-600 font-semibold">Save {{ $package['discount'] }}%</span>
                                @endif
                                <span class="{{ $package['discount'] > 0 ? 'line-through text-gray-400' : '' }}">
                                    {{ $package['original_price'] }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 mb-6">
                            @if($package['bonus'] > 0)
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-gift mr-2"></i>
                                    <span>{{ $package['bonus'] }} Bonus Tokens</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center text-sm text-blue-600">
                                <i class="fas fa-bolt mr-2"></i>
                                <span>Instant Delivery</span>
                            </div>
                            
                            <div class="flex items-center text-sm text-purple-600">
                                <i class="fas fa-infinity mr-2"></i>
                                <span>Valid Until Used</span>
                            </div>
                        </div>

                        <form id="tokenPaymentForm" data-package="{{ $package['tokens'] }}" data-amount="{{ $package['amount'] }}" data-price="{{ $package['formatted_price'] }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                <span class="button-text">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Buy {{ $package['tokens'] }} Tokens
                                </span>
                                <span class="button-spinner hidden">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Current Balance -->
        @if(auth()->guard('client')->check())
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Current Balance</h3>
                        <p class="text-sm text-gray-600">Your available tokens for practice tests</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">{{ auth()->guard('client')->user()->getCurrentTokens() }}</div>
                        <div class="text-sm text-gray-500">tokens</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Features -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Why Buy Tokens?</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Cost Effective</h4>
                        <p class="text-sm text-gray-600">Buy in bulk and save more on each practice test</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <i class="fas fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Flexible Usage</h4>
                        <p class="text-sm text-gray-600">Use tokens across any available practice exams</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Track Progress</h4>
                        <p class="text-sm text-gray-600">Monitor your token usage and exam history</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Exams -->
        <div class="text-center mt-8">
            <a href="{{ route('public.exams.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Practice Tests
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForms = document.querySelectorAll('#tokenPaymentForm');
    
    paymentForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const packageTokens = this.dataset.package;
            const amount = this.dataset.amount;
            const price = this.dataset.price;
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonSpinner = submitBtn.querySelector('.button-spinner');
            
            submitBtn.disabled = true;
            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');
            
            // Create payment data
            const paymentData = {
                tokens: packageTokens,
                amount: amount,
                price: price,
                payment_method: 'card' // Default payment method
            };
            
            // Send to server
            fetch('{{ route('public.tokens.purchase') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to Flutterwave
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.message || 'Payment initialization failed');
                    submitBtn.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonSpinner.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                alert('Payment initialization failed. Please try again.');
                submitBtn.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            });
        });
    });
});
</script>
@endpush
