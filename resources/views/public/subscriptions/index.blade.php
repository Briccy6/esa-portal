@extends('layouts.app')

@section('title', 'Subscription Plans - ESA Portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Choose Your Learning Plan</h1>
                <p class="text-xl opacity-90">Unlock premium features and accelerate your learning journey</p>
            </div>
        </div>
    </div>

    <!-- Subscription Plans -->
    <div class="container mx-auto px-4 py-16">
        @if(session('success'))
            <div class="mb-8 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($subscriptions as $tier => $plans)
                @foreach($plans as $subscription)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 {{ $subscription->is_recommended ? 'ring-4 ring-blue-500' : '' }}">
                        @if($subscription->is_recommended)
                            <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                                MOST POPULAR
                            </div>
                        @endif
                        
                        <div class="p-8">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $subscription->name }}</h3>
                                <div class="text-4xl font-bold text-blue-600 mb-2">
                                    {{ $subscription->formatted_price }}
                                </div>
                                <div class="text-gray-600">
                                    {{ $subscription->duration_days }} days
                                    <span class="text-sm">({{ $subscription->monthly_equivalent }})</span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-center justify-center mb-4">
                                    <i class="fas fa-coins text-yellow-500 mr-2"></i>
                                    <span class="text-lg font-semibold">{{ $subscription->formatted_tokens }}</span>
                                </div>
                                
                                @if($subscription->features)
                                    <ul class="space-y-3">
                                        @foreach($subscription->features as $feature)
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                                <span class="text-gray-700">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="space-y-3">
                                @if(Auth::guard('client')->check())
                                    <a href="{{ route('public.subscriptions.purchase', $subscription) }}" 
                                       class="w-full block text-center bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                        Choose Plan
                                    </a>
                                @else
                                    <a href="{{ route('public.subscriptions.login') }}" 
                                       class="w-full block text-center bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                                        Login to Purchase
                                    </a>
                                @endif
                                
                                <a href="{{ route('public.subscriptions.register') }}" 
                                   class="w-full block text-center border-2 border-blue-600 text-blue-600 py-3 px-6 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                                    Create Account
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        <!-- Features Section -->
        <div class="mt-20 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-12">Why Choose ESA Portal?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Premium Content</h3>
                    <p class="text-gray-600">Access exclusive courses and learning materials</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Expert Instructors</h3>
                    <p class="text-gray-600">Learn from qualified and experienced teachers</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-certificate text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Certification</h3>
                    <p class="text-gray-600">Earn certificates upon course completion</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
