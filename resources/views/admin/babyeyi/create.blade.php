@extends('admin.layout')

@section('title', 'Create Babyeyi Document')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V7a1 1 0 100 2h1V7a4 4 0 00-4-4h-1a1 1 0 000 2h1v2H8V5a1 1 0 100 2h8v1H4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Term Registration Document</h1>
                    <p class="text-gray-600 text-sm mt-1">Create a new babyeyi document for students</p>
                </div>
            </div>
            <a href="{{ route('babyeyi.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Documents
            </a>
        </div>
    </div>

    <!-- Validation Errors Summary -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="text-red-800 font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <form action="{{ route('babyeyi.store') }}" method="POST" id="babyeyiForm">
            @csrf

            <div class="p-6 space-y-6">

                <!-- Academic Year & Term Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-900 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select id="academic_year_id"
                                name="academic_year_id"
                                required
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('academic_year_id') ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-300' }}">
                            <option value="">-- Select Academic Year --</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Term -->
                    <div>
                        <label for="term_id" class="block text-sm font-medium text-gray-900 mb-2">
                            Term <span class="text-red-500">*</span>
                        </label>
                        <select id="term_id"
                                name="term_id"
                                required
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('term_id') ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-300' }}">
                            <option value="">-- Select Term --</option>
                        </select>
                        @error('term_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Document Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-3">
                        Document Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="typeSelection">
                        <!-- Single Course -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="selection_type" value="single" class="selection-type-radio sr-only" required>
                            <div class="p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 transition-all duration-200" id="single-option">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full">
                                        <div class="w-2 h-2 bg-blue-600 rounded-full hidden"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Single Course</p>
                                        <p class="text-xs text-gray-600">For a specific course with fee table</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- By Type -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="selection_type" value="by_type" class="selection-type-radio sr-only" required>
                            <div class="p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 transition-all duration-200" id="by-type-option">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full">
                                        <div class="w-2 h-2 bg-blue-600 rounded-full hidden"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">By Course Type</p>
                                        <p class="text-xs text-gray-600">All courses of selected type</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Multiple -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="selection_type" value="multiple" class="selection-type-radio sr-only" required>
                            <div class="p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 transition-all duration-200" id="multiple-option">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full">
                                        <div class="w-2 h-2 bg-blue-600 rounded-full hidden"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Multiple Courses</p>
                                        <p class="text-xs text-gray-600">Multiple courses combined</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('selection_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Single Course Selection -->
                <div id="course_group" style="display: none;">
                    <label for="course_id" class="block text-sm font-medium text-gray-900 mb-2">
                        Select Course <span class="text-red-500">*</span>
                    </label>
                    <select id="course_id"
                            name="course_id"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('course_id') ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-300' }}">
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
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> Selecting a course will include the fee table for this specific course.
                    </p>
                </div>

                <!-- Course Type Selection -->
                <div id="course_type_group" style="display: none;">
                    <label for="course_type" class="block text-sm font-medium text-gray-900 mb-2">
                        Select Course Type <span class="text-red-500">*</span>
                    </label>
                    <select id="course_type"
                            name="course_type"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $errors->has('course_type') ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-300' }}">
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
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> All courses of this type will be included. Fee table will be excluded.
                    </p>
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" id="info_alert">
                    <div class="flex items-center text-blue-800">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span id="info_text">Please select a document type above to proceed.</span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end items-center gap-3 rounded-b-2xl">
                <a href="{{ route('babyeyi.index') }}"
                   class="inline-flex items-center px-4 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Document
                </button>
            </div>
        </form>
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
