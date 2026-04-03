@extends('admin.layout')

@section('title', 'Bulk Generate Babyeyi Documents')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-2xl backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M12 6a1 1 0 110-2h.01a1 1 0 110 2H12z"/>
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12s4.477 10 10 10 10-4.484 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm3.5-9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Bulk Generate Babyeyi Documents</h1>
                    <p class="text-indigo-100 text-sm mt-1">Automatically create and publish babyeyis for all courses in a term</p>
                </div>
            </div>
            <a href="{{ route('babyeyi.index') }}"
               class="inline-flex items-center px-6 py-3 text-indigo-600 bg-white font-semibold rounded-lg hover:bg-indigo-50 transition-all duration-200 shadow-sm hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Documents
            </a>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-8">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1h2v2H7V4zm2 4H7v2h2V8zm2-4h2v2h-2V4zm2 4h-2v2h2V8z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-blue-900 font-semibold">How it works</h3>
                <p class="text-blue-800 text-sm mt-1">
                    This tool will automatically create a babyeyi document for each course that has fees configured for the selected term. 
                    Each document will include the complete fee schedule table. You can choose to publish them immediately or keep them in draft for review.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Form Section -->
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('babyeyi.bulk.store') }}" method="POST" id="bulkForm">
                @csrf

                <div class="p-8 space-y-8">

                    <!-- Academic Year & Term Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Academic Year -->
                        <div>
                            <label for="academic_year_id" class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                                    </svg>
                                    Academic Year
                                </span>
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <select id="academic_year_id"
                                    name="academic_year_id"
                                    required
                                    onchange="loadTerms()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 {{ $errors->has('academic_year_id') ? 'border-red-500 ring-2 ring-red-500' : '' }} bg-white">
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
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V7a1 1 0 100 2h1V7a4 4 0 00-4-4h-1a1 1 0 000 2h1v2H8V5a1 1 0 100 2h8v1H4z" clip-rule="evenodd"/>
                                    </svg>
                                    Term
                                </span>
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <select id="term_id"
                                    name="term_id"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 {{ $errors->has('term_id') ? 'border-red-500 ring-2 ring-red-500' : '' }} bg-white"
                                    @if(!$terms->count()) disabled @endif>
                                <option value="">-- Select Term --</option>
                                @foreach ($terms as $term)
                                    <option value="{{ $term->id }}" @selected(old('term_id') == $term->id)>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Auto-Publish Checkbox -->
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="auto_publish"
                                   value="1"
                                   @checked(old('auto_publish') || true)
                                   class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                            <span class="ml-3 text-gray-700">
                                <span class="font-semibold">Auto-Publish Documents</span>
                                <p class="text-sm text-gray-600 mt-1">
                                    Check this to publish all generated babyeyis immediately, making them available to students. 
                                    Uncheck to keep them in draft for review.
                                </p>
                            </span>
                        </label>
                    </div>

                    <!-- Help Text -->
                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded p-4">
                        <p class="text-sm text-amber-900">
                            <strong>✓ What will happen:</strong>
                        </p>
                        <ul class="text-sm text-amber-800 mt-2 space-y-1 ml-4 list-disc">
                            <li>A babyeyi will be created for each course with active term fees</li>
                            <li>Each document will include a complete fee schedule table</li>
                            <li>Documents will automatically include class entry conditions and payment requirements</li>
                            <li>If auto-publish is enabled, students will see them immediately in their portal</li>
                        </ul>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 rounded p-4">
                            <p class="text-sm text-red-900 font-semibold">Please fix the following:</p>
                            <ul class="text-sm text-red-800 mt-2 space-y-1 ml-4 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('babyeyi.index') }}" class="px-6 py-2 text-gray-700 font-semibold hover:text-gray-900 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Generate Babyeyis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadTerms() {
    const academicYearId = document.getElementById('academic_year_id').value;
    const termSelect = document.getElementById('term_id');

    if (!academicYearId) {
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        termSelect.disabled = true;
        return;
    }

    // Fetch terms for selected academic year
    fetch(`{{ route('babyeyi.get-terms', '') }}/${academicYearId}`)
        .then(response => response.json())
        .then(data => {
            termSelect.innerHTML = '<option value="">-- Select Term --</option>';
            data.forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.name;
                termSelect.appendChild(option);
            });
            termSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            termSelect.innerHTML = '<option value="">Error loading terms</option>';
        });
}
</script>
@endpush

@endsection
