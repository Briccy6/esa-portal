@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA Portal');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resource->document_name ?: $resource->video_name ?: 'Resource' }} - {{ $systemName }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-dark: #263238;
            --text-light: #455a64;
            --bg-light: #ecf0f1;
            --bg-white: #ffffff;
            --border-color: #cfd8dc;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            position: relative;
        }

        /* ESA Watermark Background */
        body::before {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            background-image: url('/images/esa.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.05;
            z-index: -1;
            pointer-events: none;
        }

        /* Ensure content is above watermark */
        .resource-header,
        .max-w-7xl,
        footer,
        nav {
            position: relative;
            z-index: 1;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .badge-free {
            background-color: #27ae60;
        }

        .badge-paid {
            background-color: #e67e22;
        }

        .resource-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
                            <div class="bg-blue-600 text-white px-3 py-2 rounded-lg font-bold text-lg">ESA</div>
                        @endif
                        <span class="font-bold text-xl text-gray-800">{{ $systemName }}</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="{{ route('public.resources.index') }}" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-book mr-2"></i>Resources
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Resource Header -->
    <div class="resource-header text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">
                        {{ $resource->document_name ?: $resource->video_name ?: 'Resource' }}
                    </h1>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span class="opacity-90">
                            <i class="fas fa-graduation-cap mr-2"></i>{{ $resource->course->course_name ?? 'N/A' }}
                        </span>
                        <span class="opacity-90">
                            <i class="fas fa-user-tie mr-2"></i>{{ $resource->teacher->teacher_name ?? 'N/A' }}
                        </span>
                        <span class="opacity-90">
                            <i class="fas fa-map-marker-alt mr-2"></i>{{ $resource->branch->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    @if($resource->is_free)
                        <span class="badge-free text-lg px-4 py-2 rounded-full">FREE</span>
                    @else
                        <span class="badge-paid text-lg px-4 py-2 rounded-full">
                            ${{ number_format($resource->price, 2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resource Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Resource Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-semibold mb-4">Resource Details</h2>
                    
                    <div class="space-y-4">
                        @if($resource->lesson)
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-book-open w-6 text-blue-600"></i>
                                <span class="font-medium mr-3">Lesson:</span>
                                <span>{{ $resource->lesson->title }}</span>
                            </div>
                        @endif

                        @if($resource->youtube_link)
                            <div class="flex items-center text-gray-700">
                                <i class="fab fa-youtube w-6 text-red-600"></i>
                                <span class="font-medium mr-3">YouTube:</span>
                                <a href="{{ $resource->youtube_link }}" target="_blank" class="text-blue-600 hover:underline">
                                    Watch Video
                                </a>
                            </div>
                        @endif

                        @if($resource->admin_notes)
                            <div class="mt-6">
                                <h3 class="font-semibold text-lg mb-2">Description</h3>
                                <p class="text-gray-600">{{ $resource->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Available Files -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Available Files</h2>
                    
                    @if($resource->document_blob || $resource->video_blob || $resource->youtube_link)
                        <div class="space-y-4">
                            @if($resource->document_blob)
                                <div class="border rounded-lg p-4 hover:border-blue-400 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-file-alt text-3xl text-blue-600"></i>
                                            <div>
                                                <h4 class="font-semibold">{{ $resource->document_name }}</h4>
                                                <p class="text-sm text-gray-500">Document</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($resource->is_free || $resource->price == 0)
                                                <a href="{{ route('public.resources.preview', $resource->id) }}" 
                                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                                    <i class="fas fa-eye mr-1"></i>Preview
                                                </a>
                                                <a href="{{ route('public.resources.download', $resource->id) }}" 
                                                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                                    <i class="fas fa-download mr-1"></i>Download
                                                </a>
                                            @else
                                                <a href="{{ route('public.resources.payment', $resource->id) }}" 
                                                   class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm">
                                                    <i class="fas fa-shopping-cart mr-1"></i>Purchase (${{ number_format($resource->price, 2) }})
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($resource->video_blob)
                                <div class="border rounded-lg p-4 hover:border-red-400 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-video text-3xl text-red-600"></i>
                                            <div>
                                                <h4 class="font-semibold">{{ $resource->video_name }}</h4>
                                                <p class="text-sm text-gray-500">Video</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($resource->is_free || $resource->price == 0)
                                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                                    <i class="fas fa-play mr-1"></i>Play Video
                                                </button>
                                            @else
                                                <a href="{{ route('public.resources.payment', $resource->id) }}" 
                                                   class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm">
                                                    <i class="fas fa-shopping-cart mr-1"></i>Purchase (${{ number_format($resource->price, 2) }})
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($resource->youtube_link)
                                <div class="border rounded-lg p-4 hover:border-red-400 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <i class="fab fa-youtube text-3xl text-red-600"></i>
                                            <div>
                                                <h4 class="font-semibold">YouTube Video</h4>
                                                <p class="text-sm text-gray-500">External Video</p>
                                            </div>
                                        </div>
                                        <a href="{{ $resource->youtube_link }}" target="_blank" 
                                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i>Watch
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-folder-open text-4xl mb-3"></i>
                            <p>No files available for this resource.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Teacher Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-semibold mb-4">Teacher Information</h3>
                    <div class="text-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-tie text-3xl text-blue-600"></i>
                        </div>
                        <h4 class="font-semibold text-lg">{{ $resource->teacher->teacher_name ?? 'N/A' }}</h4>
                        <p class="text-gray-600 text-sm mt-1">Expert Educator</p>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-semibold mb-4">Course Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">Course:</span>
                            <p class="font-medium">{{ $resource->course->course_name ?? 'N/A' }}</p>
                        </div>
                        @if($resource->lesson)
                            <div>
                                <span class="text-gray-600 text-sm">Lesson:</span>
                                <p class="font-medium">{{ $resource->lesson->title }}</p>
                            </div>
                        @endif
                        <div>
                            <span class="text-gray-600 text-sm">Branch:</span>
                            <p class="font-medium">{{ $resource->branch->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('public.resources.index') }}" 
                           class="w-full btn-primary text-center py-2 rounded-lg font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Resources
                        </a>
                        
                        @if(!$resource->is_free && $resource->price > 0)
                            <a href="{{ route('public.resources.payment', $resource->id) }}" 
                               class="w-full bg-orange-600 text-white py-2 rounded-lg font-medium hover:bg-orange-700">
                                <i class="fas fa-shopping-cart mr-2"></i>Purchase (${{ number_format($resource->price, 2) }})
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.</p>
                <p class="mt-2 text-gray-400">Empowering education through quality resources</p>
            </div>
        </div>
    </footer>
</body>
</html>
