@extends('admin.layout')

@section('title', 'Babyeyi Management')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V7a1 1 0 100 2h1V7a4 4 0 00-4-4h-1a1 1 0 000 2h1v2H8V5a1 1 0 100 2h8v1H4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Babyeyi Management</h1>
                    <p class="text-gray-600 text-sm mt-1">Create and manage term registration documents</p>
                </div>
            </div>
            <a href="{{ route('babyeyi.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Document
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Documents</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $babyeyis->total() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Published</p>
                    <p class="text-2xl font-bold text-green-600">{{ $babyeyis->where('status', 'published')->count() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Drafts</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $babyeyis->where('status', 'draft')->count() }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Preview</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $babyeyis->where('status', 'preview')->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Academic Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Term</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($babyeyis as $babyeyi)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm">
                                <code class="bg-gray-100 px-2 py-1 rounded text-gray-800">{{ $babyeyi->reference_number }}</code>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $babyeyi->academicYear->year ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $babyeyi->term->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if ($babyeyi->course)
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        {{ $babyeyi->course->name }}
                                    </span>
                                @elseif ($babyeyi->course_type)
                                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                        {{ $babyeyi->course_type }}
                                    </span>
                                @else
                                    <span class="text-gray-500">Multiple</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($babyeyi->status === 'draft')
                                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-file"></i> Draft
                                    </span>
                                @elseif ($babyeyi->status === 'preview')
                                    <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-eye"></i> Preview
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle"></i> Published
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $babyeyi->creator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $babyeyi->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('babyeyi.show', $babyeyi) }}" 
                                       class="inline-block px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors duration-150" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('babyeyi.edit', $babyeyi) }}" 
                                       class="inline-block px-3 py-1 bg-green-50 text-green-600 rounded hover:bg-green-100 transition-colors duration-150" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('babyeyi.preview', $babyeyi) }}" 
                                       class="inline-block px-3 py-1 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100 transition-colors duration-150" 
                                       title="Preview">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                    <a href="{{ route('babyeyi.download', $babyeyi) }}" 
                                       class="inline-block px-3 py-1 bg-purple-50 text-purple-600 rounded hover:bg-purple-100 transition-colors duration-150" 
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl opacity-50 mb-2 block"></i>
                                <p class="font-medium">No babyeyi documents found</p>
                                <p class="text-xs mt-1">Create your first document to get started</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($babyeyis->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $babyeyis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
