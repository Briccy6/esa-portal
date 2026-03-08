@extends('layouts.app')

@section('title', 'Client Dashboard - ESA Portal')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="h-8 w-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Client Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, {{ auth()->guard('client')->user()->name }}!</span>
                    <form action="{{ route('client.auth.logout') }}" method="POST" class="inline">
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
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-2">Welcome to Your Learning Portal!</h2>
            <p class="text-blue-100 mb-4">
                You now have access to premium learning resources. Here's what you can do:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white/10 rounded-lg p-4">
                    <i class="fas fa-graduation-cap text-2xl mb-2"></i>
                    <h3 class="font-semibold mb-1">Practice Exams</h3>
                    <p class="text-sm text-blue-100">Access comprehensive practice tests</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <i class="fas fa-crown text-2xl mb-2"></i>
                    <h3 class="font-semibold mb-1">Subscriptions</h3>
                    <p class="text-sm text-blue-100">Get premium plans with benefits</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <i class="fas fa-chart-line text-2xl mb-2"></i>
                    <h3 class="font-semibold mb-1">Track Progress</h3>
                    <p class="text-sm text-blue-100">Monitor your learning journey</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Token Balance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Token Balance</h3>
                    <i class="fas fa-coins text-yellow-500 text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    {{ auth()->guard('client')->user()->getCurrentTokens() }}
                </div>
                <p class="text-gray-600 text-sm">Available tokens for practice tests</p>
                <div class="mt-4">
                    <a href="{{ route('public.tokens.purchase') }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                        <i class="fas fa-plus mr-2"></i>
                        Buy More Tokens
                    </a>
                </div>
            </div>

            <!-- Subscription Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Subscription Status</h3>
                    <i class="fas fa-crown text-purple-500 text-2xl"></i>
                </div>
                @if(auth()->guard('client')->user()->hasActiveSubscription())
                    @php
                        $activeSubscription = auth()->guard('client')->user()->activeSubscription;
                    @endphp
                    <div class="space-y-2">
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

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                    <i class="fas fa-history text-green-500 text-2xl"></i>
                </div>
                @php
                    $recentTransactions = auth()->guard('client')->user()->transactions()->latest()->take(3)->get();
                @endphp
                @if($recentTransactions->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentTransactions as $transaction)
                            <div class="flex justify-between items-center py-2 border-b">
                                <div>
                                    <p class="text-sm font-medium">{{ $transaction->type }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M j, H:i') }}</p>
                                </div>
                                <span class="text-sm font-medium">{{ $transaction->formatted_amount }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600 text-sm">No recent activity</p>
                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('public.tokens.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-500">
                        View All Activity →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('public.exams.index') }}" 
                   class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-graduation-cap text-blue-600 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Browse Exams</span>
                </a>
                <a href="{{ route('public.subscriptions.index') }}" 
                   class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-crown text-purple-600 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Subscriptions</span>
                </a>
                <a href="{{ route('public.tokens.purchase') }}" 
                   class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <i class="fas fa-coins text-yellow-600 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Buy Tokens</span>
                </a>
                <a href="{{ route('public.tokens.index') }}" 
                   class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-chart-line text-green-600 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">View History</span>
                </a>
            </div>
        </div>

        <!-- Available Resources -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Available Resources</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="p-3 bg-blue-100 rounded-full mr-4">
                            <i class="fas fa-book text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Practice Tests</h4>
                            <p class="text-sm text-gray-600 mb-3">Access a wide range of practice exams covering various subjects and difficulty levels.</p>
                            <a href="{{ route('public.exams.index') }}" 
                               class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                Explore Tests →
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="p-3 bg-purple-100 rounded-full mr-4">
                            <i class="fas fa-gem text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Premium Plans</h4>
                            <p class="text-sm text-gray-600 mb-3">Get premium subscriptions with exclusive benefits and token packages.</p>
                            <a href="{{ route('public.subscriptions.index') }}" 
                               class="text-purple-600 hover:text-purple-500 text-sm font-medium">
                                View Plans →
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="p-3 bg-yellow-100 rounded-full mr-4">
                            <i class="fas fa-coins text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Token Packages</h4>
                            <p class="text-sm text-gray-600 mb-3">Purchase token packages with bulk discounts and bonus tokens.</p>
                            <a href="{{ route('public.tokens.purchase') }}" 
                               class="text-yellow-600 hover:text-yellow-500 text-sm font-medium">
                                Buy Tokens →
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="p-3 bg-green-100 rounded-full mr-4">
                            <i class="fas fa-chart-bar text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Progress Tracking</h4>
                            <p class="text-sm text-gray-600 mb-3">Monitor your learning progress and track your achievements.</p>
                            <a href="{{ route('public.tokens.index') }}" 
                               class="text-green-600 hover:text-green-500 text-sm font-medium">
                                View Progress →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
