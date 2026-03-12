@extends('layouts.app')

@section('title', 'Public Resources - ESA Portal')

@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
@endphp

@push('styles')
<style>
    /* Modern Design System */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    :root {
        --primary-color: #1e293b;
        --secondary-color: #3b82f6;
        --accent-color: #06b6d4;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
        overflow-x: hidden;
        background: var(--bg-light);
    }

    /* Enhanced Typography */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-dark);
    }

    /* Enhanced Resource Cards */
    .resource-card {
        background: var(--bg-white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .resource-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 4px;
        background: var(--gradient-primary);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .resource-card:hover::before {
        transform: scaleX(1);
    }

    .resource-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        border-color: var(--secondary-color);
    }

    /* Enhanced Search Container */
    .search-container {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        position: relative;
        overflow: hidden;
    }

    .search-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
        pointer-events: none;
    }

    .search-container > * {
        position: relative;
        z-index: 1;
    }

    /* Enhanced Badges */
    .badge-free {
        background: var(--gradient-success);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow-sm);
    }

    .badge-paid {
        background: var(--gradient-secondary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow-sm);
    }

    /* Enhanced Buttons */
    .btn-primary {
        background: var(--gradient-primary);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: var(--gradient-primary);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* Enhanced Form Elements */
    .form-input {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .form-input:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.95);
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .form-select {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        color: var(--text-dark);
    }

    .form-select:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.95);
    }

    /* Enhanced Grid */
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        padding: 2rem;
    }

    /* Enhanced Resource Header */
    .resource-header {
        background: var(--gradient-primary);
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .resource-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
        50% { transform: translate(-30%, -30%) rotate(180deg); }
    }

    /* Mobile Responsive Enhancements */
    @media (max-width: 768px) {
        .search-container {
            padding: 3rem 1rem !important;
        }
        
        .search-container h1 {
            font-size: 2rem !important;
            line-height: 1.2;
        }
        
        .search-container p {
            font-size: 1rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        .resource-card {
            margin-bottom: 1rem;
        }
        
        .resource-card h3 {
            font-size: 1rem !important;
            line-height: 1.3;
        }
        
        .resource-card .text-sm {
            font-size: 0.75rem !important;
        }
        
        /* Better mobile filters */
        .filter-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .filter-container select {
            width: 100%;
            padding: 0.75rem;
        }
        
        .filter-container button {
            width: 100%;
            padding: 0.75rem;
        }
        
        .resources-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1rem;
        }
    }

    @media (max-width: 640px) {
        .resource-card {
            border-radius: 0.75rem;
        }
        
        .btn-primary {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }
        
        .form-input, .form-select {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
    }

    /* Touch-friendly buttons */
    @media (hover: none) and (pointer: coarse) {
        .resource-card button {
            min-height: 44px;
            min-width: 44px;
        }
        
        .btn-primary {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
@include('components.public-navigation')

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
                    <div class="filter-container flex flex-wrap gap-4 mt-6 justify-center md:flex-row md:gap-4">
                        <select name="course_id" class="px-4 py-2 rounded-lg text-gray-800 w-full md:w-auto">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <select name="price_type" class="px-4 py-2 rounded-lg text-gray-800 w-full md:w-auto">
                            <option value="">All Resources</option>
                            <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Free Only</option>
                            <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Paid Only</option>
                        </select>
                        
                        @if(request()->hasAny(['search', 'course_id', 'price_type']))
                            <a href="{{ route('public.resources.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full md:w-auto text-center">
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
@endsection

@push('scripts')
<script>
    function shareResource(resourceId, title) {
        const url = window.location.origin + '/public/resources/' + resourceId;
        
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url,
                text: `Check out this resource: ${title}`
            });
        } else {
            // Fallback to copying link
            navigator.clipboard.writeText(url).then(() => {
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
@endpush
