@extends('layouts.public')

@section('title', $resource->document_name ?: $resource->video_name ?: 'Resource' . ' - ESA Portal')

@section('content')
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
@endsection
