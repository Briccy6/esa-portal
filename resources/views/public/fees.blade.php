@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            <i class="fas fa-credit-card text-blue-500 mr-3"></i>School Fees
        </h1>
        <p class="text-gray-600">Explore our transparent fee structure and course pricing.</p>
    </div>

    @if($fees && $fees->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($fees as $fee)
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-book text-blue-500 text-2xl mr-3"></i>
                        <h3 class="text-xl font-semibold text-gray-800">
                            {{ $fee->course->name ?? 'General Fee' }}
                        </h3>
                    </div>
                    
                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-600 mb-2">Amount:</p>
                        <p class="text-3xl font-bold text-green-600 mb-4">
                            {{ config('app.currency_symbol', '$') }}{{ number_format($fee->amount ?? 0, 2) }}
                        </p>
                        
                        @if($fee->description)
                            <p class="text-gray-700 text-sm mb-4">{{ $fee->description }}</p>
                        @endif
                        
                        @if($fee->course && $fee->course->description)
                            <p class="text-gray-600 text-xs italic">
                                <i class="fas fa-info-circle mr-1"></i>{{ $fee->course->description }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No public fees are currently available. Please check back later or contact the school office for more information.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-12 bg-blue-50 rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            <i class="fas fa-question-circle text-blue-500 mr-2"></i>Need Help?
        </h2>
        <p class="text-gray-700 mb-2">
            For more information about fees and payment options, please contact our office:
        </p>
        <div class="text-gray-600">
            <p><i class="fas fa-phone mr-2"></i>{{ config('app.phone', '+1 (555) 123-4567') }}</p>
            <p><i class="fas fa-envelope mr-2"></i>{{ config('app.email', 'info@school.com') }}</p>
        </div>
    </div>
</div>
@endsection
