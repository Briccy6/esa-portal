@extends('admin.layout')

@section('title', 'Babyeyi Management')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-2xl backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V7a1 1 0 100 2h1V7a4 4 0 00-4-4h-1a1 1 0 000 2h1v2H8V5a1 1 0 100 2h8v1H4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Term Registration Documents</h1>
                    <p class="text-blue-100 text-sm mt-1">Manage and create babyeyi documents for all courses</p>
                </div>
            </div>
            <a href="{{ route('babyeyi.create') }}"
               class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-sm hover:shadow-lg transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Document
            </a>
        </div>
    </div>

    <!-- Success / Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="text-red-800 font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Documents -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Documents</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $babyeyis->total() }}</p>
                    <p class="text-xs text-gray-500 mt-2">All documents</p>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-blue-50 p-4 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Published -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Published</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $babyeyis->where('status', 'published')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-2">Active documents</p>
                </div>
                <div class="bg-gradient-to-br from-green-100 to-green-50 p-4 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Drafts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Drafts</p>
                    <p class="text-3xl font-bold text-amber-600 mt-2">{{ $babyeyis->where('status', 'draft')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-2">In progress</p>
                </div>
                <div class="bg-gradient-to-br from-amber-100 to-amber-50 p-4 rounded-xl">
                    <svg class="w-8 h-8 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Preview</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $babyeyis->where('status', 'preview')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-2">Under review</p>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-purple-50 p-4 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V7a1 1 0 100 2h1V7a4 4 0 00-4-4h-1a1 1 0 000 2h1v2H8V5a1 1 0 100 2h8v1H4z" clip-rule="evenodd"/>
                </svg>
                Term Registration Documents
            </h2>
        </div>

        @if($babyeyis->count() > 0)
            <div class="overflow-x-auto">
                <div class="px-6 py-4 space-y-3">
                    @foreach ($babyeyis as $babyeyi)
                        <div class="border border-gray-100 rounded-lg p-4 hover:shadow-md hover:border-gray-200 transition-all duration-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Document Info -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-3 mb-2">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <code class="bg-gray-100 px-3 py-1 rounded text-sm font-mono text-gray-700">{{ $babyeyi->reference_number }}</code>
                                                @if ($babyeyi->status === 'draft')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                        Draft
                                    </span>
                                                @elseif ($babyeyi->status === 'preview')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Preview
                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Published
                                    </span>
                                                @endif
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm mt-2">
                                                <div>
                                                    <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">Academic Year</p>
                                                    <p class="font-medium text-gray-900">{{ $babyeyi->academicYear->year ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">Term</p>
                                                    <p class="font-medium text-gray-900">{{ $babyeyi->term->name ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">Created By</p>
                                                    <p class="font-medium text-gray-900">{{ $babyeyi->creator->name ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">Created Date</p>
                                                    <p class="font-medium text-gray-900">{{ $babyeyi->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            @if ($babyeyi->course)
                                                <div class="mt-2">
                                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a1 1 0 001 1h12a1 1 0 001-1V6a2 2 0 00-2-2H4zm12 12H4a2 2 0 01-2-2v-4a1 1 0 00-1-1H.5a1.5 1.5 0 011.5 1.5v4a4 4 0 004 4h12a4 4 0 004-4v-4a1.5 1.5 0 011.5-1.5H19a1 1 0 00-1 1v4a2 2 0 01-2 2z" clip-rule="evenodd"/></svg>
                                                        {{ $babyeyi->course->name }}
                                                    </span>
                                                </div>
                                            @elseif ($babyeyi->course_type)
                                                <div class="mt-2">
                                                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                                        {{ $babyeyi->course_type }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
                                    <a href="{{ route('babyeyi.show', $babyeyi) }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-150" 
                                       title="View">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('babyeyi.edit', $babyeyi) }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150" 
                                       title="Edit">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('babyeyi.preview', $babyeyi) }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors duration-150" 
                                       title="Preview">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        Preview
                                    </a>
                                    <a href="{{ route('babyeyi.download', $babyeyi) }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-150" 
                                       title="Download">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($babyeyis->hasPages())
                <div class="px-6 py-6 border-t border-gray-100 bg-gray-50 flex justify-center">
                    {{ $babyeyis->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-16 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 font-medium text-lg">No babyeyi documents found</p>
                <p class="text-gray-500 text-sm mt-1">Create your first term registration document to get started</p>
                <a href="{{ route('babyeyi.create') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create First Document
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
