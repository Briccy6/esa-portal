@extends('layouts.public')

@section('title', 'Client Dashboard - ESA Portal')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="h-8 w-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, {{ $client->first_name }}!</span>
                    <form action="{{ route('public.subscriptions.logout') }}" method="POST" class="inline">
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
        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-coins text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Current Tokens</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($currentTokens) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-crown text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Subscription Status</p>
                        @if($client->hasActiveSubscription())
                            <p class="text-lg font-bold text-green-600">Active</p>
                            <p class="text-xs text-gray-500">{{ $client->activeSubscription->subscription->name }}</p>
                        @else
                            <p class="text-lg font-bold text-gray-400">No Active Subscription</p>
                            <p class="text-xs text-gray-500">Choose a plan to continue</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-calendar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="text-lg font-bold text-gray-900">{{ $client->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Subscription -->
            <div class="lg:col-span-2">
                @if($client->hasActiveSubscription())
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Active Subscription</h2>
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $client->activeSubscription->subscription->name }}</h3>
                                    <p class="text-gray-600">{{ $client->activeSubscription->subscription->description }}</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                    Active
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Started</p>
                                    <p class="font-semibold">{{ $client->activeSubscription->starts_at->format('M j, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Expires</p>
                                    <p class="font-semibold">{{ $client->activeSubscription->ends_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Tokens from this subscription</span>
                                    <span class="font-bold text-lg">{{ $client->activeSubscription->subscription->formatted_tokens }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Available Plans -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Available Plans</h2>
                    <div class="space-y-4">
                        @foreach($subscriptions as $subscription)
                            @if(!$client->hasActiveSubscription() || $client->activeSubscription->subscription_id != $subscription->id)
                                <div class="border rounded-lg p-4 hover:border-blue-500 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $subscription->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $subscription->description }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm">
                                                <span class="font-medium">{{ $subscription->formatted_price }}</span>
                                                <span class="text-gray-500">{{ $subscription->duration_days }} days</span>
                                                <span class="text-blue-600">{{ $subscription->formatted_tokens }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('public.subscriptions.purchase', $subscription) }}" 
                                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            {{ $client->hasActiveSubscription() ? 'Upgrade' : 'Choose' }}
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Transactions</h2>
                    @if($client->transactions->count() > 0)
                        <div class="space-y-3">
                            @foreach($client->transactions as $transaction)
                                <div class="border-b pb-3 last:border-b-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium">{{ $transaction->type }}</span>
                                        {!! $transaction->status_badge !!}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <p>{{ $transaction->formatted_amount }}</p>
                                        <p class="text-xs">{{ $transaction->created_at->format('M j, Y H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                View All Transactions
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-3 opacity-50"></i>
                            <p>No transactions yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
