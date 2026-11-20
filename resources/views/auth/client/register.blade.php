@extends('layouts.public')

@section('title', 'Client Registration - ESA Portal')

@section('content')
<div class="public-registration min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4 max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/esa.jpg') }}" alt="ESA Portal" class="mx-auto h-12 w-auto mb-4">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Create Client Account</h1>
            <p class="text-gray-600">Join ESA Portal and access premium learning resources</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            
            <form action="{{ route('client.auth.register') }}" method="POST" class="space-y-4">
                @csrf
                @method('POST')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your full name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your phone number">
                    @error('phone')
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
                           placeholder="Create a password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Confirm your password">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="terms" 
                           name="terms" 
                           required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                    </label>
                </div>
                @error('terms')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('client.auth.login') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>

        <!-- Benefits -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">What You Get Access To:</h3>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                    <span>Practice exams and assessments</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                    <span>Subscription plans with token benefits</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                    <span>Personalized learning dashboard</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                    <span>Progress tracking and analytics</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                    <span>Secure payment processing</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
