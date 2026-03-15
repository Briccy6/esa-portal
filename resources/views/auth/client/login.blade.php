@extends('layouts.app')

@section('title', 'Client Login - ESA Portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4 max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="mx-auto h-12 w-auto mb-4">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Client Login</h1>
            <p class="text-gray-600">Sign in to access your learning dashboard</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            
            <form action="{{ route('client.auth.login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <a href="{{ route('client.auth.password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Sign In
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('client.auth.register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                        Create one here
                    </a>
                </p>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Access</h3>
            <div class="space-y-2">
                <a href="{{ route('public.exams.index') }}" 
                   class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-graduation-cap mr-2 text-blue-600"></i>
                    Browse Practice Exams
                </a>
                <a href="{{ route('public.subscriptions.index') }}" 
                   class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-crown mr-2 text-purple-600"></i>
                    View Subscription Plans
                </a>
                <a href="{{ route('public.tokens.purchase') }}" 
                   class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-coins mr-2 text-yellow-600"></i>
                    Buy Tokens
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
