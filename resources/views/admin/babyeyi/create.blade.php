@extends('admin.layout')

@section('title', 'Create Babyeyi Document')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-2xl backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V7a1 1 0 100 2h1V7a4 4 0 00-4-4h-1a1 1 0 000 2h1v2H8V5a1 1 0 100 2h8v1H4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Create Term Registration Document</h1>
                    <p class="text-blue-100 text-sm mt-1">Create a new babyeyi document for students</p>
                </div>
            </div>
            <a href="{{ route('babyeyi.index') }}"
               class="inline-flex items-center px-6 py-3 text-blue-600 bg-white font-semibold rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-sm hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Documents
            </a>
        </div>
    </div>

    <!-- Validation Errors Summary -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 flex items-start">
            <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-red-800 font-semibold">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('babyeyi.store') }}" method="POST" id="babyeyiForm">
                    @csrf

                    <div class="p-8 space-y-8">

                        <!-- Academic Year & Term Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Academic Year -->
                            <div>
                                <label for="academic_year_id" class="block text-sm font-semibold text-gray-900 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                        </svg>
                                        Academic Year
                                    </span>
                                    <span class="text-red-500 font-bold">*</span>
                                </label>
                                <select id="academic_year_id"
                                        name="academic_year_id"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('academic_year_id') ? 'border-red-500 ring-2 ring-red-500' : '' }} bg-white">
                                    <option value="">-- Select Academic Year --</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>
                                            {{ $year->year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Term -->
                            <div>
                                <label for="term_id" class="block text-sm font-semibold text-gray-900 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                                        </svg>
                                        Term
                                    </span>
                                    <span class="text-red-500 font-bold">*</span>
                                </label>
                                <select id="term_id"
                                        name="term_id"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('term_id') ? 'border-red-500 ring-2 ring-red-500' : '' }} bg-white">
                                    <option value="">-- Select Term --</option>
                                </select>
                                @error('term_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Document Type Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3V3a1 1 0 011-1h4a1 1 0 011 1v1h3a2 2 0 012 2v2h1a1 1 0 110 2h-1v6h1a1 1 0 110 2h-1v3a2 2 0 01-2 2h-3a1 1 0 001-2v-1H9v1a1 1 0 01-1 2H5a2 2 0 01-2-2v-3H2a1 1 0 110-2h1V8H2a1 1 0 010-2h1V3a2 2 0 012-2h4V2a1 1 0 011-1zm-1 5a1 1 0 100 2h12a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    Document Type
                                </span>
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="typeSelection">
                                <!-- Single Course -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="selection_type" value="single" class="selection-type-radio sr-only" required>
                                    <div class="p-4 border-2 border-gray-300 rounded-xl hover:border-blue-500 group-hover:shadow-md transition-all duration-200 bg-white" id="single-option">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full group-hover:border-blue-500 transition-colors">
                                                <div class="w-2.5 h-2.5 bg-blue-600 rounded-full hidden"></div>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm">Single Course</p>
                                                <p class="text-xs text-gray-600 mt-0.5">For a specific course with fee table</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- By Type -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="selection_type" value="by_type" class="selection-type-radio sr-only" required>
                                    <div class="p-4 border-2 border-gray-300 rounded-xl hover:border-blue-500 group-hover:shadow-md transition-all duration-200 bg-white" id="by-type-option">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full group-hover:border-blue-500 transition-colors">
                                                <div class="w-2.5 h-2.5 bg-blue-600 rounded-full hidden"></div>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm">By Course Type</p>
                                                <p class="text-xs text-gray-600 mt-0.5">All courses of selected type</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Multiple -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="selection_type" value="multiple" class="selection-type-radio sr-only" required>
                                    <div class="p-4 border-2 border-gray-300 rounded-xl hover:border-blue-500 group-hover:shadow-md transition-all duration-200 bg-white" id="multiple-option">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full group-hover:border-blue-500 transition-colors">
                                                <div class="w-2.5 h-2.5 bg-blue-600 rounded-full hidden"></div>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm">Multiple Courses</p>
                                                <p class="text-xs text-gray-600 mt-0.5">Multiple courses combined</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('selection_type')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Single Course Selection -->
                        <div id="course_group" style="display: none;">
                            <label for="course_id" class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 000-2H7zM4 7a1 1 0 011-1h10a1 1 0 011 1v3a1 1 0 11-2 0V8H6v2a1 1 0 11-2 0V7zM2 13a3 3 0 013-3h10a3 3 0 013 3v3a3 3 0 01-3 3H5a3 3 0 01-3-3v-3z"/>
                                    </svg>
                                    Select Course
                                </span>
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <select id="course_id"
                                    name="course_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('course_id') ? 'border-red-500 ring-2 ring-red-500' : '' }} bg-white">
                                <option value="">-- Select Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                        {{ $course->name }}
                                        @if($course->course_type) ({{ $course->course_type }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-blue-700 bg-blue-50 px-3 py-2 rounded-lg flex items-start">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Selecting a course will include the fee table for this specific course.</span>
                            </p>
                        </div>

                        <!-- Course Type Selection -->
                        <div id="course_type_group" style="display: none;">
                            <label for="course_type" class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 000-2H7zM4 7a1 1 0 011-1h10a1 1 0 011 1v3a1 1 0 11-2 0V8H6v2a1 1 0 11-2 0V7zM2 13a3 3 0 013-3h10a3 3 0 013 3v3a3 3 0 01-3 3H5a3 3 0 01-3-3v-3z"/>
                                    </svg>
                                    Select Course Type
                                </span>
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <select id="course_type"
                                    name="course_type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('course_type') ? 'border-red-500 ring-2 ring-red-500' : '' }} bg-white">
                                <option value="">-- Select Course Type --</option>
                                @foreach ($courseTypes as $type)
                                    <option value="{{ $type }}" @selected(old('course_type') == $type)>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-blue-700 bg-blue-50 px-3 py-2 rounded-lg flex items-start">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>All courses of this type will be included. Fee table will be excluded.</span>
                            </p>
                        </div>

                    </div>

                    <!-- Form Actions -->
                    <div class="border-t border-gray-100 px-8 py-6 bg-gradient-to-r from-gray-50 to-white flex justify-end items-center gap-3 rounded-b-2xl">
                        <a href="{{ route('babyeyi.index') }}"
                           class="inline-flex items-center px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-sm hover:shadow-md hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Document
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Information Panel -->
        <div class="lg:col-span-1">
            <!-- Help Panel -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-200 rounded-2xl p-6 sticky top-20">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-600">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-gray-900">How to Create</h3>
                        <p class="text-sm text-gray-600 mt-1">Follow these simple steps</p>
                    </div>
                </div>

                <ol class="space-y-4 ml-1">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-semibold">1</span>
                        <p class="ml-3 text-sm text-gray-700 mt-0.5"><span class="font-medium">Select Year & Term</span><br><span class="text-gray-600">Choose the academic year and term for this document</span></p>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-semibold">2</span>
                        <p class="ml-3 text-sm text-gray-700 mt-0.5"><span class="font-medium">Choose Document Type</span><br><span class="text-gray-600">Select how to organize your document</span></p>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-semibold">3</span>
                        <p class="ml-3 text-sm text-gray-700 mt-0.5"><span class="font-medium">Select Course(s)</span><br><span class="text-gray-600">Pick the specific course or course type</span></p>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-sm font-semibold">4</span>
                        <p class="ml-3 text-sm text-gray-700 mt-0.5"><span class="font-medium">Create Document</span><br><span class="text-gray-600">Click the create button to generate</span></p>
                    </li>
                </ol>

                <div class="mt-6 pt-6 border-t border-blue-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Document Types</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span><span class="font-medium text-gray-900">Single:</span> One course with deep details</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span><span class="font-medium text-gray-900">By Type:</span> All courses in a category</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span><span class="font-medium text-gray-900">Multiple:</span> Custom course selection</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const academicYearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term_id');
    const courseSelect = document.getElementById('course_id');
    const courseTypeSelect = document.getElementById('course_type');
    const selectionTypeRadios = document.querySelectorAll('.selection-type-radio');
    const courseGroup = document.getElementById('course_group');
    const courseTypeGroup = document.getElementById('course_type_group');
    const infoText = document.getElementById('info_text');

    // Update radio button visual when selected
    selectionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            selectionTypeRadios.forEach(r => {
                const option = r.parentElement.querySelector('div');
                const circle = r.parentElement.querySelector('.w-6');
                const dot = circle.querySelector('.hidden');
                
                circle.classList.remove('border-blue-500');
                option.classList.remove('border-blue-500', 'bg-blue-50');
                if (dot) dot.classList.add('hidden');
            });
            
            if (this.checked) {
                const circle = this.parentElement.querySelector('.w-6');
                const dot = circle.querySelector('.hidden');
                const option = this.parentElement.querySelector('div');
                
                circle.classList.add('border-blue-500');
                option.classList.add('border-blue-500', 'bg-blue-50');
                if (dot) dot.classList.remove('hidden');
            }
        });
    });

    // Handle selection type changes
    selectionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            courseGroup.style.display = 'none';
            courseTypeGroup.style.display = 'none';

            if (this.value === 'single') {
                courseGroup.style.display = 'block';
                infoText.innerHTML = '<i class="fas fa-book mr-1"></i> Selected fee table will be included for this course.';
            } else if (this.value === 'by_type') {
                courseTypeGroup.style.display = 'block';
                infoText.innerHTML = '<i class="fas fa-layer-group mr-1"></i> All courses of this type will be included. Fee table will be excluded.';
            } else {
                infoText.innerHTML = '<i class="fas fa-books mr-1"></i> Multiple courses will be included. Fee table will be excluded.';
            }
        });
    });

    // Load terms when academic year changes
    academicYearSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`/babyeyi/get-terms/${this.value}`)
                .then(response => response.json())
                .then(data => {
                    termSelect.innerHTML = '<option value="">-- Select Term --</option>';
                    data.forEach(term => {
                        termSelect.innerHTML += `<option value="${term.id}">${term.name}</option>`;
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        }
    });

    // Load courses by type when course type changes
    courseTypeSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`/babyeyi/get-courses?course_type=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="">-- Available Courses --</option>';
                    data.forEach(course => {
                        courseSelect.innerHTML += `<option value="${course.id}">${course.name}</option>`;
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Restore selection type if form has errors
    const selectedType = document.querySelector('input[name="selection_type"]:checked');
    if (selectedType) {
        selectedType.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
