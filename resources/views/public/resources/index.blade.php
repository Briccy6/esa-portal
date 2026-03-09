@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA Portal');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Resources - {{ $systemName }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
        }

        .resource-card {
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .resource-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
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

        .search-container {
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
                    <a href="{{ route('public.resources.index') }}" class="text-blue-600 font-semibold">
                        <i class="fas fa-book mr-2"></i>Resources
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Search -->
    <div class="search-container text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Educational Resources</h1>
                <p class="text-xl mb-8 opacity-90">Access quality learning materials from our expert teachers</p>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('public.resources.index') }}" class="max-w-3xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Search resources by name, course, teacher..." 
                                class="w-full px-4 py-3 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white"
                            >
                        </div>
                        <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-semibold">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap gap-4 mt-6 justify-center">
                        <select name="course_id" class="px-4 py-2 rounded-lg text-gray-800">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <select name="price_type" class="px-4 py-2 rounded-lg text-gray-800">
                            <option value="">All Resources</option>
                            <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Free Only</option>
                            <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Paid Only</option>
                        </select>
                        
                        @if(request()->hasAny(['search', 'course_id', 'price_type']))
                            <a href="{{ route('public.resources.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resources Grid -->
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

        @if($resources->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($resources as $resource)
                    <div class="resource-card bg-white rounded-lg overflow-hidden shadow-md">
                        <!-- Resource Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg line-clamp-2">
                                    {{ $resource->document_name ?: $resource->video_name ?: 'Resource' }}
                                </h3>
                                <button onclick="shareResource({{ $resource->id }}, '{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 30) }}')" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-lg transition-all duration-200"
                                        title="Share this resource">
                                    <i class="fas fa-share-alt text-white"></i>
                                </button>
                            </div>
                            <div class="text-sm opacity-90">
                                <i class="fas fa-graduation-cap mr-1"></i> {{ $resource->course->course_name ?? 'N/A' }}
                                @if($resource->is_free)
                                    <span class="badge-free text-xs px-2 py-1 rounded-full ml-2">FREE</span>
                                @else
                                    <span class="badge-paid text-xs px-2 py-1 rounded-full ml-2">
                                        RWF {{ number_format($resource->price, 0) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Resource Body -->
                        <div class="p-4">
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div>
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <span>{{ $resource->teacher->teacher_name ?? 'N/A' }}</span>
                                </div>
                                @if($resource->lesson)
                                    <div>
                                        <i class="fas fa-book-open mr-2"></i>
                                        <span>{{ $resource->lesson->title }}</span>
                                    </div>
                                @endif
                                <div>
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>{{ $resource->branch->name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Resource Type Icons -->
                            <div class="flex space-x-3 mb-4">
                                @if($resource->document_blob)
                                    <span class="text-blue-600" title="Document Available">
                                        <i class="fas fa-file-alt text-lg"></i>
                                    </span>
                                @endif
                                @if($resource->video_blob)
                                    <span class="text-red-600" title="Video Available">
                                        <i class="fas fa-video text-lg"></i>
                                    </span>
                                @endif
                                @if($resource->youtube_link)
                                    <span class="text-red-600" title="YouTube Video">
                                        <i class="fab fa-youtube text-lg"></i>
                                    </span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('public.resources.show', $resource->id) }}" 
                                   class="flex-1 btn-primary text-center py-2 rounded-lg text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                                
                                @if($resource->document_blob)
                                    <a href="{{ $resource->is_free ? route('public.resources.download', $resource->id) : route('public.resources.payment', $resource->id) }}" 
                                       class="flex-1 btn-primary text-center py-2 rounded-lg text-sm font-medium">
                                        <i class="fas fa-{{ $resource->is_free ? 'download' : 'shopping-cart' }} mr-1"></i>{{ $resource->is_free ? 'Download' : 'Purchase' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $resources->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No resources found</h3>
                <p class="text-gray-500">Try adjusting your search filters or browse all available resources.</p>
                <a href="{{ route('public.resources.index') }}" class="btn-primary inline-block mt-4 px-6 py-2 rounded-lg">
                    <i class="fas fa-redo mr-2"></i>View All Resources
                </a>
            </div>
        @endif
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
    
    <script>
        // Share Function
        function shareResource(resourceId, resourceTitle) {
            const url = window.location.origin + '/public/resources/' + resourceId;
            const text = `Check out this educational resource: ${resourceTitle}`;
            
            if (navigator.share) {
                navigator.share({
                    title: resourceTitle,
                    text: text,
                    url: url
                }).catch(err => console.log('Error sharing:', err));
            } else {
                // Fallback - copy to clipboard
                const shareText = `${text} - ${url}`;
                navigator.clipboard.writeText(shareText).then(() => {
                    showNotification('Link copied to clipboard!');
                }).catch(() => {
                    // Last resort - open in new window
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                });
            }
        }

        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>
