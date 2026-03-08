@extends('layouts.app')

@section('title', 'Token History - ESA Portal')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="h-8 w-auto">
                    <h1 class="text-xl font-semibold text-gray-900">My Tokens</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, {{ auth()->guard('client')->user()->first_name }}!</span>
                    <form action="{{ route('public.tokens.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-500">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Balance Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Token Balance</h2>
                    <div class="text-center">
                        <div class="text-5xl font-bold text-blue-600 mb-2">
                            {{ auth()->guard('client')->user()->getCurrentTokens() }}
                        </div>
                        <div class="text-gray-600">Available Tokens</div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Quick Actions</span>
                            <div class="space-x-2">
                                <a href="{{ route('public.tokens.purchase') }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buy Tokens
                                </a>
                                <a href="{{ route('public.exams.index') }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-green-600 bg-green-100 hover:bg-green-200">
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    Practice Tests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Subscription Status</h2>
                @if(auth()->guard('client')->user()->hasActiveSubscription())
                    @php
                        $activeSubscription = auth()->guard('client')->user()->activeSubscription;
                    @endphp
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Plan</span>
                            <span class="font-medium">{{ $activeSubscription->subscription->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                Active
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tokens Included</span>
                            <span class="font-medium">{{ $activeSubscription->subscription->formatted_tokens }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Started</span>
                            <span class="font-medium">{{ $activeSubscription->starts_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Expires</span>
                            <span class="font-medium">{{ $activeSubscription->ends_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-crown text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600 mb-4">No Active Subscription</p>
                        <a href="{{ route('public.subscriptions.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-crown mr-2"></i>
                            Get Subscription
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Transaction History</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ $transactions->count() }} transactions</span>
                        @if($transactions->count() > 0)
                            <a href="{{ route('public.tokens.download') }}" 
                               class="text-sm text-blue-600 hover:text-blue-500">
                                <i class="fas fa-download mr-1"></i>
                                Download
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->created_at->format('M j, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {!! $transaction->transaction_type_badge !!}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium">{{ $transaction->formatted_amount }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {!! $transaction->status_badge !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600 mb-4">No transactions yet</p>
                        <p class="text-sm text-gray-500">Buy tokens to get started with practice tests</p>
                        <a href="{{ route('public.tokens.purchase') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mt-4">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Buy Tokens
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
