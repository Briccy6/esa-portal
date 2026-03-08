@extends('layouts.app')

@section('title', 'Choose Registration Type - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Choose Your Registration Type
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Select how you want to join ESA Portal
            </p>
        </div>

        <!-- Registration Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Student Registration -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                    <div class="text-center">
                        <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                        <h3 class="text-2xl font-bold">Student Registration</h3>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Attend classes and courses</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Access course materials and resources</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Take exams and receive certificates</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Track attendance and academic progress</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('students.create') }}" 
                       class="w-full block text-center bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Register as Student
                    </a>
                </div>
            </div>

            <!-- Client Registration -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6">
                    <div class="text-center">
                        <i class="fas fa-user-tie text-4xl mb-4"></i>
                        <h3 class="text-2xl font-bold">Client Registration</h3>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Access premium content and resources</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Purchase subscription plans</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Manage tokens and services</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                            <span class="text-gray-700">Access system without attending classes</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('public.subscriptions.register') }}" 
                       class="w-full block text-center bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                        Register as Client
                    </a>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="{{ route('welcome') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
