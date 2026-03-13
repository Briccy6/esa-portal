@extends('admin.layout')

@section('content')
<!-- Custom Styles -->


<div class="gradient-bg">
    <!-- Floating Shapes -->
    <div class="floating-shapes">
        <div class="shape w-20 h-20 bg-white rounded-full"></div>
        <div class="shape w-16 h-16 bg-white rounded-lg"></div>
        <div class="shape w-12 h-12 bg-white transform rotate-45"></div>
    </div>
    
    <div class="container mx-auto px-4 py-12 relative z-10">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-12 form-container">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4">Edit Academic Year</h1>
                <p class="text-xl text-white text-opacity-80 max-w-2xl mx-auto">
                    Configure the academic year settings and manage the institutional calendar
                </p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-8 form-container error-animation">
                    <div class="glass-effect rounded-2xl p-6 border-l-4 border-red-400">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Validation Errors</h3>
                                <ul class="space-y-2">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex items-center text-red-700">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Form -->
            <div class="glass-effect rounded-3xl shadow-2xl form-container">
                <form action="{{ route('admin.academic_year.update', $academicYear->id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-8">
                            <!-- Academic Year Field -->
                            <div class="label-float">
                                <input 
                                    type="text" 
                                    name="year" 
                                    id="year" 
                                    value="{{ old('year', $academicYear->year) }}" 
                                    required
                                    placeholder=" "
                                    class="form-input w-full px-4 pt-6 pb-2 text-lg rounded-xl focus:outline-none"
                                >
                                <label for="year" class="text-gray-600">
                                    Academic Year <span class="text-red-500">*</span>
                                </label>
                                <div class="absolute right-4 top-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Starting Date -->
                            <div class="label-float">
                                <input 
                                    type="date" 
                                    name="starting_date" 
                                    id="starting_date" 
                                    value="{{ old('starting_date', $academicYear->starting_date->format('Y-m-d')) }}" 
                                    required
                                    placeholder=" "
                                    class="form-input w-full px-4 pt-6 pb-2 text-lg rounded-xl focus:outline-none"
                                >
                                <label for="starting_date" class="text-gray-600">
                                    Starting Date <span class="text-red-500">*</span>
                                </label>
                                <div class="absolute right-4 top-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-8">
                            <!-- Expiry Date -->
                            <div class="label-float">
                                <input 
                                    type="date" 
                                    name="expiry_date" 
                                    id="expiry_date" 
                                    value="{{ old('expiry_date', $academicYear->expiry_date->format('Y-m-d')) }}" 
                                    required
                                    placeholder=" "
                                    class="form-input w-full px-4 pt-6 pb-2 text-lg rounded-xl focus:outline-none"
                                >
                                <label for="expiry_date" class="text-gray-600">
                                    Expiry Date <span class="text-red-500">*</span>
                                </label>
                                <div class="absolute right-4 top-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Status Field -->
                            <div class="space-y-4">
                                <label class="block text-gray-700 font-semibold text-lg">
                                    Academic Year Status <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 gap-4">
                                    <label class="cursor-pointer">
                                        <input 
                                            type="radio" 
                                            name="is_ongoing" 
                                            value="1" 
                                            {{ old('is_ongoing', $academicYear->is_ongoing) ? 'checked' : '' }}
                                            class="sr-only peer"
                                        >
                                        <div class="status-indicator status-active peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:ring-offset-2 transition-all">
                                            <div class="pulse-dot active"></div>
                                            <span>Currently Active</span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input 
                                            type="radio" 
                                            name="is_ongoing" 
                                            value="0" 
                                            {{ !old('is_ongoing', $academicYear->is_ongoing) ? 'checked' : '' }}
                                            class="sr-only peer"
                                        >
                                        <div class="status-indicator status-inactive peer-checked:ring-2 peer-checked:ring-gray-400 peer-checked:ring-offset-2 transition-all">
                                            <div class="pulse-dot inactive"></div>
                                            <span>Inactive</span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-12 pt-8 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="btn-primary flex-1 text-white font-semibold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 relative overflow-hidden"
                        >
                            <span class="flex items-center justify-center relative z-10">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Academic Year
                            </span>
                        </button>
                        
                        <a 
                            href="{{ route('admin.academic_year.index') }}" 
                            class="flex-1 sm:flex-initial bg-white bg-opacity-90 backdrop-blur-sm text-gray-700 font-semibold py-4 px-8 rounded-xl border-2 border-gray-200 hover:border-gray-300 transition-all duration-300 text-center hover:bg-opacity-100 hover:shadow-lg"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Pro Tips Section -->
            <div class="mt-8 glass-effect rounded-2xl p-6 form-container">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Pro Tips</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                                Use format "YYYY-YYYY" for academic year
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                Only one academic year can be active
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-purple-400 rounded-full mr-3"></div>
                                Avoid overlapping date ranges
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection