@extends('layouts.public')

@section('title', 'Educational Resources - ESA Portal')

@section('content')
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
                            @if($resource->is_free)
                                <span class="badge-free text-xs px-2 py-1 rounded-full">FREE</span>
                            @else
                                <span class="badge-paid text-xs px-2 py-1 rounded-full">
                                    RWF {{ number_format($resource->price, 0) }}
                                </span>
                            @endif
                        </div>
                        <div class="text-sm opacity-90">
                            <i class="fas fa-graduation-cap mr-1"></i> {{ $resource->course->course_name ?? 'N/A' }}
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
                            
                            @if($resource->document_blob && ($resource->is_free || $resource->price == 0))
                                <a href="{{ route('public.resources.download', $resource->id) }}" 
                                   class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                            @elseif(!$resource->is_free && $resource->price > 0)
                                <a href="{{ route('public.resources.payment', $resource->id) }}" 
                                   class="flex-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium text-center">
                                    <i class="fas fa-shopping-cart mr-1"></i>Purchase RWF {{ number_format($resource->price, 0) }}
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
@endsection
