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
                    ESA Portal
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
                            <button type="button" class="absolute inset-y-0 right-0 top-0 h-full px-4 py-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset-2 focus:ring-indigo-500" @click="togglePasswordVisibility" id="password-toggle">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825 11.25 15.125 8.625 18.825 10.875 12.75 8.625 18.825 13.875 18.825"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9.75 11.25 15.125 8.625 12.75 12.75 9.75 15.75 9.75"></path></svg>
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
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            {{ __('Sign in') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-auto text-center text-gray-500 text-sm">
        <p>&copy; {{ date('Y') }} ESA Portal. {{ __('All rights reserved.') }}</p>
    </div>
</x-guest-layout>
