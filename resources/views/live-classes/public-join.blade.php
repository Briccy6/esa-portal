@extends('layouts.app')

@section('title', 'Join Live Class - {{ $liveClass->title }}')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-6">
                <h1 class="text-2xl font-bold">Join Live Class - Public Access</h1>
                <p class="mt-2 opacity-90">Enter your details to join this live class</p>
            </div>
            
            <div class="p-6">
                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Class Details -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-lg mb-2">{{ $liveClass->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ $liveClass->description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm"><strong>Course:</strong> {{ $liveClass->courses->pluck('name')->join(', ') ?? 'N/A' }}</p>
                            <p class="text-sm"><strong>Teacher:</strong> {{ $liveClass->teacher?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm"><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($liveClass->start_time)->format('M j, Y H:i') }}</p>
                            <p class="text-sm"><strong>Duration:</strong> {{ $liveClass->start_time->diffInHours($liveClass->end_time) }} hours</p>
                        </div>
                    </div>
                </div>

                    @if($liveClass->status !== 'active')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-3"></i>
                            <div>
                                <h6 class="font-semibold text-yellow-800">Class Not Active</h6>
                                <p class="text-yellow-700">This class is not currently active. Please check back when the class is scheduled to start.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('welcome') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Home
                        </a>
                    </div>
                @else
                    <form action="{{ route('live-classes.public-join', $liveClass) }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Registration Form -->
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name', session('name')) }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email', session('email')) }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">This email will be used to verify your access</p>
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number (Optional)</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', session('phone')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Class Information -->
                            <div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h6 class="font-semibold mb-3">Class Information</h6>
                                    <div class="space-y-2 text-sm">
                                        <p><strong>Meeting ID:</strong> <code class="bg-gray-200 px-2 py-1 rounded">{{ $liveClass->meeting_id }}</code></p>
                                        <p><strong>Password:</strong> <code class="bg-gray-200 px-2 py-1 rounded">{{ $liveClass->password }}</code></p>
                                        <p><strong>Participants:</strong> {{ $liveClass->getActiveParticipantsCount() ?? 0 }}{{ $liveClass->max_participants ? '/' . $liveClass->max_participants : '' }}</p>
                                    </div>
                                    
                                    @if($liveClass->device_restriction)
                                        <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
                                            <i class="fas fa-mobile-alt mr-1"></i> One device per email allowed
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-check-circle text-green-600 mr-3 mt-0.5"></i>
                                        <div>
                                            <h6 class="font-semibold text-green-800">Ready to Join!</h6>
                                            <p class="text-green-700 text-sm">Click the button below to join the live class. Make sure you have a stable internet connection and your camera/microphone ready.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center space-y-3">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                <i class="fas fa-video mr-2"></i> Join Live Class Now
                            </button>
                            <br>
                            <a href="{{ route('welcome') }}" class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Home
                            </a>
                        </div>
                    </form>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
