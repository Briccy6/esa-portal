@extends('layouts.app')

@section('title', $exam->title . ' - Skill Practice Test - Excellent Servant Academy')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-blue-600">
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('public.exams.index') }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">
                        Practice Tests
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11 10.586 14.293 14.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500">{{ Str::limit($exam->title, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Exam Header -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ $exam->title }}</h1>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ $exam->questions->count() }} Questions
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $exam->questions->count() * 2 }} minutes
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $exam->status }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Description -->
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">About This Practice Test</h2>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $exam->description ?? 'Practice your skills and prepare for your exams with this comprehensive assessment.' }}
                        </p>
                    </div>

                    <!-- Exam Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Practice Test Information</h3>
                            <dl class="space-y-2">
                                @if($exam->academic_year)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Academic Year:</dt>
                                    <dd class="font-medium">{{ $exam->academic_year->name }}</dd>
                                </div>
                                @endif
                                @if($exam->term)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Term:</dt>
                                    <dd class="font-medium">{{ $exam->term->name }}</dd>
                                </div>
                                @endif
                                @if($exam->courses->count() > 0)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Courses:</dt>
                                    <dd class="font-medium">{{ $exam->courses->pluck('name')->implode(', ') }}</dd>
                                </div>
                                @endif
                                @if($exam->lessons->count() > 0)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Lessons:</dt>
                                    <dd class="font-medium">{{ $exam->lessons->pluck('title')->implode(', ') }}</dd>
                                </div>
                                @endif
                                @if($exam->teacher)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Created By:</dt>
                                    <dd class="font-medium">{{ $exam->teacher->full_name }}</dd>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Total Marks:</dt>
                                    <dd class="font-medium">{{ $exam->total_marks ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Timing</h3>
                            <dl class="space-y-2">
                                @if($exam->start_time)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Start Time:</dt>
                                    <dd class="font-medium">{{ $exam->start_time->format('M j, Y g:i A') }}</dd>
                                </div>
                                @endif
                                @if($exam->end_time)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">End Time:</dt>
                                    <dd class="font-medium">{{ $exam->end_time->format('M j, Y g:i A') }}</dd>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Duration:</dt>
                                    <dd class="font-medium">{{ $exam->questions->count() * 2 }} minutes</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($exam->notes)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">Important Notes</h3>
                        <p class="text-blue-800">{{ $exam->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- What to Expect -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">What to Expect in This Practice Test</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Multiple Choice Questions</h3>
                            <p class="text-gray-600 text-sm">All questions are multiple choice format for easy practice</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Self-Paced Practice</h3>
                            <p class="text-gray-600 text-sm">{{ $exam->questions->count() * 2 }} minutes to complete at your own pace</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Instant Feedback</h3>
                            <p class="text-gray-600 text-sm">Get your score immediately after submission</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Skill Building</h3>
                            <p class="text-gray-600 text-sm">Identify areas for improvement before real exams</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Pricing Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 sticky top-6">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 text-white">
                    <h3 class="text-lg font-bold">Practice Access Options</h3>
                </div>

                <div class="p-6">
                    @if($exam->requiresPayment())
                        <!-- Single Payment Option -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Practice Test Access</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-center mb-3">
                                    <p class="text-3xl font-bold text-green-600">{{ $exam->formatted_public_price }}</p>
                                    <p class="text-sm text-gray-600">One-time practice test</p>
                                </div>
                                <a href="{{ route('public.exams.payment', $exam) }}" 
                                   class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block font-medium">
                                    Pay & Start Practice
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Free Access -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Free Access</h4>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="text-center mb-3">
                                    <p class="text-2xl font-bold text-green-600">FREE</p>
                                    <p class="text-sm text-gray-600">No payment required</p>
                                </div>
                                <a href="{{ route('public.exams.start', $exam) }}" 
                                   class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block font-medium">
                                    Start Practice Now
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($exam->requiresTokens())
                        <!-- Token Option -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Use Tokens</h4>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="text-center mb-3">
                                    <p class="text-xl font-bold text-yellow-700">{{ $exam->formatted_token_cost }}</p>
                                    <p class="text-sm text-gray-600">From your token balance</p>
                                </div>
                                @if(auth()->guard('student')->check())
                                    @if($userAccess && $userAccess['allowed'])
                                        <a href="{{ route('public.exams.start', $exam) }}" 
                                           class="w-full bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 transition-colors text-center block font-medium">
                                            Use Tokens & Practice
                                        </a>
                                    @else
                                        <button disabled class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg cursor-not-allowed text-center block font-medium">
                                            Insufficient Tokens
                                        </button>
                                        <p class="text-xs text-gray-600 mt-2 text-center">
                                            You need {{ $userAccess['required'] ?? $exam->getCalculatedTokenCost() }} tokens
                                        </p>
                                    @endif
                                @else
                                    <a href="{{ route('student.login') }}" 
                                       class="w-full bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 transition-colors text-center block font-medium">
                                        Login to Use Tokens
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Subscription Promotion -->
                    @if($subscriptions->count() > 0)
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Save with Subscription</h4>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700 mb-3">
                                Get unlimited exam access with tokens from just {{ $subscriptions->first()->formatted_price }}!
                            </p>
                            <a href="{{ route('subscriptions.index') }}" 
                               class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center block font-medium text-sm">
                                View Subscriptions
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">What's Next?</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-bold">1</div>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-blue-900">Start Your Practice Test</p>
                            <p class="text-blue-800 text-sm">Begin practicing with {{ $exam->questions->count() }} questions at your own pace.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-bold">2</div>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-blue-900">Complete All Questions</p>
                            <p class="text-blue-800 text-sm">Answer all {{ $exam->questions->count() }} questions. You can review and change answers before submitting.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-bold">3</div>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-blue-900">Get Instant Feedback</p>
                            <p class="text-blue-800 text-sm">Receive your score immediately with detailed feedback to improve.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Requirements</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Stable internet connection
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Modern web browser
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $exam->questions->count() * 2 }} minutes uninterrupted time
                    </li>
                    @if($exam->requiresPayment())
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Payment method or sufficient tokens
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
