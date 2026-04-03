@extends('layouts.public')

@section('title', 'Payment Successful - Excellent Servant Academy')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Success Message -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
        <p class="text-xl text-gray-600">You now have access to the exam</p>
    </div>

    <!-- Payment Confirmation -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $exam->title }}</h2>
                <p class="text-gray-600">{{ $exam->questions->count() }} Questions • {{ $exam->questions->count() * 2 }} minutes</p>
            </div>
            <div class="text-right mt-4 md:mt-0">
                <p class="text-sm text-gray-600">Payment Reference</p>
                <p class="text-lg font-mono font-bold text-green-600">{{ $reference }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Amount Paid</p>
                    <p class="text-lg font-semibold">{{ $exam->formatted_public_price }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Payment Status</p>
                    <p class="text-lg font-semibold text-green-600">Completed</p>
                </div>
            </div>
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
                    <p class="font-medium text-blue-900">Start Your Exam</p>
                    <p class="text-blue-800 text-sm">You can begin the exam immediately. Make sure you have {{ $exam->questions->count() * 2 }} minutes of uninterrupted time.</p>
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
                    <p class="font-medium text-blue-900">Get Instant Results</p>
                    <p class="text-blue-800 text-sm">Receive your score immediately after submission with detailed feedback.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('public.exams.start', $exam) }}" 
           class="bg-green-600 text-white py-3 px-8 rounded-lg hover:bg-green-700 transition-colors font-medium text-center">
            Start Exam Now
        </a>
        
        <a href="{{ route('public.exams.index') }}" 
           class="bg-gray-200 text-gray-800 py-3 px-8 rounded-lg hover:bg-gray-300 transition-colors font-medium text-center">
            Browse More Exams
        </a>
    </div>

    <!-- Important Notes -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h4 class="font-semibold text-yellow-900 mb-2">Important Notes</h4>
        <ul class="text-sm text-yellow-800 space-y-1">
            <li>• This payment is for a single attempt at the exam</li>
            <li>• Your exam access will expire after one attempt or 30 days, whichever comes first</li>
            <li>• Make sure you have a stable internet connection before starting</li>
            <li>• Keep your payment reference ({{ $reference }}) for future reference</li>
        </ul>
    </div>

    <!-- Need Help -->
    <div class="mt-6 text-center">
        <p class="text-gray-600 mb-2">Need help? Contact our support team</p>
        <div class="flex justify-center gap-4 text-sm">
            <a href="mailto:support@esa.ac.rw" class="text-blue-600 hover:underline">
                support@esa.ac.rw
            </a>
            <span class="text-gray-400">|</span>
            <a href="tel:+250788123456" class="text-blue-600 hover:underline">
                +250 788 123 456
            </a>
        </div>
    </div>
</div>
@endsection
