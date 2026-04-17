@extends('layouts.public')

@section('title', 'Subscription Plans - ESA Portal')

@section('content')
<div class="min-h-screen bg-slate-100">
    <!-- ALX-style hero -->
    <div class="bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto px-4 py-12 md:py-16">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="inline-flex items-center space-x-2 rounded-full bg-white/10 px-4 py-1 mb-4 backdrop-blur-sm border border-white/10 text-xs font-semibold tracking-wide uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Subscription & tokens</span>
                    </p>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3">
                        Pick a plan that<br class="hidden md:block">
                        fits how you learn.
                    </h1>
                    <p class="text-base md:text-lg text-slate-200 max-w-xl mb-6">
                        Convert your payments into tokens you can use on practice exams and resources.
                        Stay in control of your spending while keeping learning consistent.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-200">
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-coins text-amber-300"></i>
                            <span>Tokens for exams & resources</span>
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-clock text-emerald-300"></i>
                            <span>Plans with clear duration</span>
                        </span>
                    </div>
                </div>

                <div class="bg-white/20 border border-white/30 rounded-2xl p-6 md:p-7 shadow-2xl backdrop-blur-sm">
                    <h2 class="text-lg font-semibold mb-2">How subscriptions work</h2>
                    <p class="text-sm text-slate-200 mb-4">
                        When you subscribe, your account receives tokens you can spend on tests and resources.
                        You can always see your balance from your client dashboard.
                    </p>
                    <ol class="space-y-3 text-sm text-slate-100 list-decimal list-inside">
                        <li>Sign in or create a client account.</li>
                        <li>Choose a plan that matches how often you practice.</li>
                        <li>Use your tokens on any supported public exam or resource.</li>
                    </ol>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('public.subscriptions.login') }}"
                           class="btn-primary px-6 py-2.5 rounded-full text-sm font-semibold shadow-md hover:scale-105">
                            Login to manage plan
                        </a>
                        <a href="{{ route('public.subscriptions.register') }}"
                           class="inline-flex items-center px-5 py-2.5 rounded-full border border-white/20 text-xs font-semibold bg-white/5 hover:bg-white/10">
                            Create a client account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription plans -->
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
