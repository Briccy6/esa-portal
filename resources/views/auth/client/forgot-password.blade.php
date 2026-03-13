@extends('layouts.app')

@section('title', 'Client Forgot Password - ESA Portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="mx-auto h-12 w-auto">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Forgot your password?</h2>
            <p class="mt-2 text-sm text-gray-600">
                Enter your email and we will send you a 6-digit code to reset your password.
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow-xl rounded-xl">
            @if (session('status'))
                <div class="mb-4 p-3 rounded-md bg-green-100 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('client.auth.password.email') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('email') }}">
                    @error('email')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Send reset code
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('client.auth.login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                    Back to login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

