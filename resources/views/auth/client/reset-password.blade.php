@extends('layouts.app')

@section('title', 'Reset Client Password - ESA Portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="ESA Portal" class="mx-auto h-12 w-auto">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Enter your reset code</h2>
            <p class="mt-2 text-sm text-gray-600">
                We have sent a 6-digit code to your email. Enter it below with your new password.
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow-xl rounded-xl">
            @if (session('status'))
                <div class="mb-4 p-3 rounded-md bg-green-100 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('client.auth.password.reset.code') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('email', $email) }}">
                    @error('email')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">6-digit code</label>
                    <input id="code" name="code" type="text" required maxlength="6"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 tracking-widest"
                           value="{{ old('code') }}">
                    @error('code')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('password')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Reset password and continue
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('client.auth.password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                    Resend code
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

