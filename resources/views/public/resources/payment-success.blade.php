@extends('layouts.public')

@section('title', 'Payment Successful - ESA Portal')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Success Header -->
    <div class="success-bg text-white rounded-lg shadow-lg p-8 mb-8 text-center">
        <div class="success-animation inline-block">
            <i class="fas fa-check-circle text-6xl mb-4"></i>
        </div>
        <h1 class="text-3xl font-bold mb-2">Payment Successful!</h1>
        <p class="text-lg opacity-90">Thank you for your purchase. You now have access to this resource.</p>
    </div>

    <!-- Payment Details -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Summary</h2>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-600">Resource</span>
                <span class="font-medium">{{ $resource->document_name ?: $resource->video_name ?: 'Resource' }}</span>
            </div>
            
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-600">Reference</span>
                <span class="font-medium font-mono text-sm">{{ $reference }}</span>
            </div>
            
            <div class="flex justify-between items-center py-3 border-b">
                <span class="text-gray-600">Amount Paid</span>
                <span class="font-bold text-green-600">{{ number_format($resource->price) }} RWF</span>
            </div>
            
            <div class="flex justify-between items-center py-3">
                <span class="text-gray-600">Status</span>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-check mr-1"></i>Completed
                </span>
            </div>
        </div>
    </div>

    <!-- Resource Access -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Resource Access</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($resource->document_blob)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-file-pdf text-2xl text-red-600 mr-3"></i>
                        <div>
                            <h3 class="font-semibold">Document</h3>
                            <p class="text-sm text-gray-600">{{ $resource->document_name ?: $resource->video_name ?: 'Resource' }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('public.resources.preview', $resource) }}" 
                           class="flex-1 bg-blue-600 text-white text-center py-2 rounded text-sm hover:bg-blue-700">
                            <i class="fas fa-eye mr-1"></i>Preview
                        </a>
                        <a href="{{ route('public.resources.download', $resource) }}" 
                           class="flex-1 btn-primary text-center py-2 rounded text-sm">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                    </div>
                </div>
            @endif
            
            @if($resource->video_blob)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-video text-2xl text-blue-600 mr-3"></i>
                        <div>
                            <h3 class="font-semibold">Video</h3>
                            <p class="text-sm text-gray-600">{{ $resource->document_name ?: $resource->video_name ?: 'Resource' }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('public.resources.preview', $resource) }}" 
                           class="flex-1 bg-blue-600 text-white text-center py-2 rounded text-sm hover:bg-blue-700">
                            <i class="fas fa-play mr-1"></i>Stream
                        </a>
                        <a href="{{ route('public.resources.download', $resource) }}" 
                           class="flex-1 btn-primary text-center py-2 rounded text-sm">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">What's Next?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-book-open text-white text-xl"></i>
                </div>
                <h3 class="font-semibold mb-2">Access Your Resource</h3>
                <p class="text-sm text-blue-800">Click the preview or download button to access your content immediately.</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <h3 class="font-semibold mb-2">Check Your Email</h3>
                <p class="text-sm text-blue-800">We've sent a receipt and access details to your email address.</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-white text-xl"></i>
                </div>
                <h3 class="font-semibold mb-2">Need Help?</h3>
                <p class="text-sm text-blue-800">Contact our support team if you have any questions or issues.</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('public.resources.show', $resource) }}" 
           class="btn-primary px-8 py-3 rounded-lg font-semibold text-center">
            <i class="fas fa-book mr-2"></i>View Resource Details
        </a>
        <a href="{{ route('public.resources.index') }}" 
           class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold text-center hover:bg-blue-700">
            <i class="fas fa-th-large mr-2"></i>Browse More Resources
        </a>
        <a href="{{ url('/') }}" 
           class="bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold text-center hover:bg-gray-700 transition">
            <i class="fas fa-home mr-2"></i>Back to Home
        </a>
    </div>
</div>

@push('styles')
<style>
    .success-animation {
        animation: success-pulse 2s ease-in-out infinite;
    }

    @keyframes success-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .success-bg {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    }
</style>
@endpush
@endsection
