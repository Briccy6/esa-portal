<x-guest-layout> 
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-indigo-600 to-blue-600 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-xl transform transition-all duration-500 hover:scale-105">
                    <x-application-logo class="w-10 h-10" />
                </div>
                <h1 class="mt-6 text-3xl font-bold text-white">
                    {{ $systemName }}
                </h1>
                <p class="mt-2 text-lg text-blue-100">
                    {{ __('Welcome to your learning portal') }}
                </p>
            </div>

            <!-- Login Form -->
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ __('Sign in to your account') }}
                    </h2>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="reg_number" :value="__('Registration Number')" />
                        <x-text-input id="reg_number" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-4 py-3 text-gray-900 placeholder-gray-400 transition duration-300" type="text" name="reg_number" :value="old('reg_number')" required autofocus />
                        <x-input-error :messages="$errors->get('reg_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative">
                            <x-text-input id="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-4 py-3 text-gray-900 placeholder-gray-400 transition duration-300" type="password" name="password" required autocomplete="current-password" />
                            <button type="button" class="absolute inset-y-0 right-0 top-0 h-full px-4 py-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset-2 focus:ring-indigo-500" @click="togglePasswordVisibility">
                                <x-icon name="eye" class="h-5 w-5" />
                                <x-icon name="eye-off" class="h-5 w-5 hidden" />
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                        
                        <button type="submit" class="w-full flex justify-center items-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300">
                            <x-icon name="login" class="w-5 h-5 mr-2" />
                            {{ __('Sign in') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-auto text-center text-gray-500 text-sm">
        <p>&copy; {{ date('Y') }} {{ $systemName }}. {{ __('All rights reserved.') }}</p>
    </div>
</x-guest-layout>
