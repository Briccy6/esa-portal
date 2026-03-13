@extends('layouts.app')

@section('title', 'Client Login - ESA Portal')

@section('content')
<div class="min-h-screen bg-slate-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto mb-4 inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-800">
                <i class="fas fa-user-circle text-slate-800 text-2xl"></i>
            </div>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-800">Client sign in</h2>
            <p class="mt-2 text-sm text-slate-700">
                Manage your ESA subscriptions, tokens, and public practice tests.
            </p>
        </div>

        <!-- Login Form -->
        <div class="bg-white border border-slate-200 shadow-lg py-8 px-6 rounded-2xl backdrop-blur-sm">
            <!-- Google Sign In -->
            <div class="mb-6">
                <a href="{{ route('client.auth.google') }}"
                   class="w-full flex items-center justify-center px-4 py-3 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </a>
            </div>
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/40 text-rose-200 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/40 text-emerald-200 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('public.subscriptions.login.submit') }}" method="POST">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-800">Email address</label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                           class="mt-1 block w-full px-3 py-2 border border-slate-300 bg-white text-slate-800 rounded-md shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           value="{{ old('email') }}">
                    @error('email')
                        <span class="text-rose-300 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-800">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="mt-1 block w-full px-3 py-2 border border-slate-300 bg-white text-slate-800 rounded-md shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    @error('password')
                        <span class="text-rose-300 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-emerald-500 focus:ring-emerald-500 border-slate-700 rounded bg-slate-900">
                        <label for="remember" class="ml-2 block text-sm text-slate-700">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('client.auth.password.request') }}" class="font-medium text-emerald-400 hover:text-emerald-300">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        Sign In
                    </button>
                </div>
            </form>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('public.subscriptions.register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Create one here
                </a>
            </p>
            <p class="mt-2 text-sm text-gray-600">
                <a href="{{ route('public.subscriptions.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    ← Back to Subscription Plans
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
