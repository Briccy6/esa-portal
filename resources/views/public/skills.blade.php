@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            <i class="fas fa-brain text-orange-500 mr-3"></i>Test Your Skills
        </h1>
        <p class="text-gray-600">Take assessment tests to evaluate your knowledge and progress.</p>
    </div>

    <div class="mb-8 bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
        <h2 class="text-2xl font-bold text-blue-900 mb-2">
            <i class="fas fa-star mr-2"></i>Why Test Your Skills?
        </h2>
        <ul class="text-blue-800 space-y-2">
            <li><i class="fas fa-check-circle mr-2"></i>Identify strengths and areas for improvement</li>
            <li><i class="fas fa-check-circle mr-2"></i>Track your learning progress</li>
            <li><i class="fas fa-check-circle mr-2"></i>Prepare for final examinations</li>
            <li><i class="fas fa-check-circle mr-2"></i>Get personalized feedback</li>
        </ul>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-pencil-alt text-blue-500 text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-gray-800">Practice Tests</h3>
            </div>
            <p class="text-gray-700 mb-4">Take unlimited practice tests in various subjects to strengthen your knowledge.</p>
            <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                Start Practice <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-chart-line text-green-500 text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-gray-800">Progress Tracker</h3>
            </div>
            <p class="text-gray-700 mb-4">Monitor your improvement with detailed performance analytics and reports.</p>
            <a href="{{ url('/') }}" class="text-green-600 hover:text-green-800 font-semibold">
                View Progress <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-medal text-yellow-500 text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-gray-800">Certification</h3>
            </div>
            <p class="text-gray-700 mb-4">Earn certificates upon completing skill assessments and achieving required scores.</p>
            <a href="{{ url('/') }}" class="text-yellow-600 hover:text-yellow-800 font-semibold">
                Learn More <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-book text-purple-500 text-3xl mr-3"></i>
                <h3 class="text-xl font-semibold text-gray-800">Topical Tests</h3>
            </div>
            <p class="text-gray-700 mb-4">Attempt tests focused on specific topics for targeted learning.</p>
            <a href="{{ url('/') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                Browse Topics <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <div class="mt-12 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-4">
            <i class="fas fa-rocket mr-2"></i>Get Started Today
        </h2>
        <p class="mb-6">Sign up or log in to access all the skill testing features and start your learning journey.</p>
        <a href="/portals" class="bg-white text-blue-600 px-6 py-2 rounded font-semibold hover:bg-gray-100 transition">
            <i class="fas fa-sign-in-alt mr-2"></i>Access Portal
        </a>
    </div>
</div>

