@extends('layouts.client')

@section('title', 'My Profile - ESA Portal')

@section('content')
<div class="page-content">
    <!-- Page Header -->
    <div class="topbar">
        <div class="topbar-left">
            <h1>My Profile</h1>
            <p>Manage your personal information</p>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="panel">
        <div class="panel-title">Personal Information</div>
        
        <form action="{{ route('client.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $client->name) }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $client->email) }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ old('phone', $client->phone) }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" 
                           name="current_password" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Required for security verification</p>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Status</label>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span class="font-medium text-green-600">Active</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Your account is in good standing</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="font-medium text-gray-800">{{ $client->created_at->format('F j, Y') }}</div>
                            <p class="text-sm text-gray-500 mt-1">{{ $client->created_at->diffInDays(now()) }} days as member</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-graduation-cap text-blue-600 text-2xl mb-2"></i>
                        <div class="text-xl font-bold text-gray-800">
                            {{ $client->examsTaken() ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-600">Exams Taken</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-coins text-green-600 text-2xl mb-2"></i>
                        <div class="text-xl font-bold text-gray-800">
                            {{ $client->getCurrentTokens() }}
                        </div>
                        <div class="text-sm text-gray-600">Current Tokens</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <i class="fas fa-trophy text-purple-600 text-2xl mb-2"></i>
                        <div class="text-xl font-bold text-gray-800">
                            {{ $client->averageScore() ?? 0 }}%
                        </div>
                        <div class="text-sm text-gray-600">Average Score</div>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="border-t pt-6">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('client.dashboard') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
