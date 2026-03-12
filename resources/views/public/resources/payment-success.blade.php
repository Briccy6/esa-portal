@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA Portal');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
    $resourceName = $resource->document_name ?: $resource->video_name ?: 'Resource';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - {{ $systemName }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-color: #6f42c1;
            --secondary-color: #36b9cc;
            --accent-color: #e74a3b;
            --text-dark: #263238;
            --text-light: #455a64;
            --bg-light: #ecf0f1;
            --bg-white: #ffffff;
            --border-color: #cfd8dc;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .success-animation {
            animation: success-pulse 2s ease-in-out infinite;
        }

        @keyframes success-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #5a32a3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #2c8c9e;
            transform: translateY(-2px);
        }

        .success-bg {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-10 w-auto">
                        @else
                            <div class="bg-purple-600 text-white px-3 py-2 rounded-lg font-bold text-lg">ESA</div>
                        @endif
                        <span class="font-bold text-xl text-gray-800">{{ $systemName }}</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-purple-600 transition">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="{{ route('public.resources.index') }}" class="text-gray-600 hover:text-purple-600 transition">
                        <i class="fas fa-book mr-2"></i>Resources
                    </a>
                </div>
            </div>
        </div>
    </nav>

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
                    <span class="font-medium">{{ $resourceName }}</span>
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
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-500 transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-file-pdf text-2xl text-red-600 mr-3"></i>
                            <div>
                                <h3 class="font-semibold">Document</h3>
                                <p class="text-sm text-gray-600">{{ $resourceName }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('public.resources.preview', $resource) }}" 
                               class="flex-1 btn-secondary text-center py-2 rounded text-sm">
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
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-500 transition">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-video text-2xl text-blue-600 mr-3"></i>
                            <div>
                                <h3 class="font-semibold">Video</h3>
                                <p class="text-sm text-gray-600">{{ $resourceName }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('public.resources.preview', $resource) }}" 
                               class="flex-1 btn-secondary text-center py-2 rounded text-sm">
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
        <div class="bg-purple-50 rounded-lg border border-purple-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-purple-900 mb-4">What's Next?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-book-open text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Access Your Resource</h3>
                    <p class="text-sm text-purple-800">Click the preview or download button to access your content immediately.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-envelope text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Check Your Email</h3>
                    <p class="text-sm text-purple-800">We've sent a receipt and access details to your email address.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-headset text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Need Help?</h3>
                    <p class="text-sm text-purple-800">Contact our support team if you have any questions or issues.</p>
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
               class="btn-secondary px-8 py-3 rounded-lg font-semibold text-center">
                <i class="fas fa-th-large mr-2"></i>Browse More Resources
            </a>
            <a href="{{ url('/') }}" 
               class="bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold text-center hover:bg-gray-700 transition">
                <i class="fas fa-home mr-2"></i>Back to Home
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.</p>
                <p class="mt-2 text-gray-400">Thank you for supporting our educational resources</p>
            </div>
        </div>
    </footer>
</body>
</html>
