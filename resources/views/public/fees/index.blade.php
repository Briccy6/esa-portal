@extends('layouts.public')

@section('title', 'Fees Schedule - Excellent Servant Academy')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            <i class="fas fa-receipt text-blue-600 mr-3"></i>Fees Schedule
        </h1>
        <p class="text-lg text-gray-600 max-w-3xl mb-6">
            View the complete fees structure for our courses organized by branch and academic term.
        </p>

        <!-- Informational Banner -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg mb-8">
            <div class="flex items-start gap-4">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-1 flex-shrink-0"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">How to Check Fees Structure</h3>
                    <p class="text-gray-700 mb-3">
                        You can easily browse our fees structure by selecting a branch and then choosing a course 
                        from that branch. The system will display all available fees broken down by term.
                    </p>
                    <div class="space-y-2 text-sm text-gray-700">
                        <p><strong>📝 Important Notes:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><span class="text-red-600 font-semibold">If you are NOT a student yet:</span> Registration fee is required as a one-time payment when you first enroll.</li>
                            <li><span class="text-green-600 font-semibold">If you are already a student:</span> You do not need to pay the registration fee again - only the tuition and other term-based fees apply.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Fees Browsing Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Step 1: Branch Selection -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold mr-3">1</span>
                    Select Branch
                </h2>
                
                <div class="space-y-2">
                    <label for="branch-select" class="block text-sm font-medium text-gray-700">
                        Choose a branch:
                    </label>
                    <select id="branch-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                        <option value="">-- Select a branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }} @if($branch->location) ({{ $branch->location }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="courses-section" class="mt-6 hidden">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold mr-3">2</span>
                        Select Course
                    </h2>
                    
                    <div class="space-y-2">
                        <label for="course-select" class="block text-sm font-medium text-gray-700">
                            Choose a course:
                        </label>
                        <select id="course-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                            <option value="">-- Select a course --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Fees Display -->
        <div class="lg:col-span-2">
            <div id="fees-display" class="hidden">
                <!-- Loading State -->
                <div id="loading-state" class="text-center py-12 hidden">
                    <div class="inline-block">
                        <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>
                    <p class="text-gray-600 mt-4">Loading fees schedule...</p>
                </div>

                <!-- Course Header -->
                <div id="course-header" class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white mb-6">
                    <h2 id="course-title" class="text-2xl font-bold"></h2>
                    <p id="course-code" class="text-blue-100 mt-1"></p>
                </div>

                <!-- Fees By Academic Year -->
                <div id="fees-container" class="space-y-6">
                    <!-- Will be populated by JavaScript -->
                </div>

                <!-- Download Button -->
                <div class="mt-8 flex gap-3">
                    <button id="download-btn" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-md">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </button>
                    <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                </div>
            </div>

            <!-- Initial State -->
            <div id="initial-state" class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                <i class="fas fa-graduation-cap text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 text-lg">
                    Select a branch and course to view the fees schedule
                </p>
            </div>

            <!-- Error State -->
            <div id="error-state" class="hidden bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl mt-1 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-red-900 mb-1">Error</h3>
                        <p id="error-message" class="text-red-700"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information Section -->
    <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-600">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-wallet text-blue-600 mr-2"></i>Fee Components
            </h3>
            <p class="text-gray-700 text-sm">
                Fees include registration, tuition, practical work, trips, and student card fees. Each component is clearly itemized in the schedule.
            </p>
        </div>

        <div class="bg-green-50 rounded-lg p-6 border-l-4 border-green-600">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-calendar-alt text-green-600 mr-2"></i>By Term
            </h3>
            <p class="text-gray-700 text-sm">
                Fees are organized by academic term, making it easy to plan your budget throughout the year.
            </p>
        </div>

        <div class="bg-purple-50 rounded-lg p-6 border-l-4 border-purple-600">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-download text-purple-600 mr-2"></i>Export Options
            </h3>
            <p class="text-gray-700 text-sm">
                Download the fees schedule as PDF or print it directly from your browser for offline reference.
            </p>
        </div>
    </div>
</div>

<!-- Fees Template (will be cloned for each academic year) -->
<template id="fees-template">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Academic Year: <span class="academic-year"></span></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Term</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Registration</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Tuition</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Practical</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Trip</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Card</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch-select');
    const courseSelect = document.getElementById('course-select');
    const coursesSection = document.getElementById('courses-section');
    const feesDisplay = document.getElementById('fees-display');
    const initialState = document.getElementById('initial-state');
    const errorState = document.getElementById('error-state');
    const loadingState = document.getElementById('loading-state');
    const feesContainer = document.getElementById('fees-container');
    const downloadBtn = document.getElementById('download-btn');
    let currentCourseId = null;

    // Handle branch selection
    branchSelect.addEventListener('change', function() {
        const branchId = this.value;
        if (!branchId) {
            coursesSection.classList.add('hidden');
            courseSelect.innerHTML = '<option value="">-- Select a course --</option>';
            feesDisplay.classList.add('hidden');
            initialState.classList.remove('hidden');
            return;
        }

        coursesSection.classList.remove('hidden');
        courseSelect.innerHTML = '<option value="">-- Select a course --</option>';
        feesDisplay.classList.add('hidden');
        initialState.classList.remove('hidden');

        // Fetch courses for this branch
        fetch(`/public/fees/branches/${branchId}/courses`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses.length > 0) {
                    data.courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = `${course.name} (${course.code})`;
                        courseSelect.appendChild(option);
                    });
                } else {
                    showError('No courses available for this branch');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to load courses');
            });
    });

    // Handle course selection
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        if (!courseId) {
            feesDisplay.classList.add('hidden');
            initialState.classList.remove('hidden');
            return;
        }

        currentCourseId = courseId;
        loadCourseFees(courseId);
    });

    // Load fees for selected course
    function loadCourseFees(courseId) {
        initialState.classList.add('hidden');
        errorState.classList.add('hidden');
        feesDisplay.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        feesContainer.innerHTML = '';

        fetch(`/public/fees/courses/${courseId}`)
            .then(response => response.json())
            .then(data => {
                loadingState.classList.add('hidden');
                if (data.success) {
                    displayCourse(data.course);
                    displayFees(data.fees);
                } else {
                    showError(data.message || 'Failed to load fees');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingState.classList.add('hidden');
                showError('An error occurred while loading fees');
            });
    }

    // Display course information
    function displayCourse(course) {
        document.getElementById('course-title').textContent = course.name;
        document.getElementById('course-code').textContent = `Code: ${course.code} | ${course.abbreviation}`;
    }

    // Display fees by academic year
    function displayFees(fees) {
        Object.entries(fees).forEach(([academicYear, yearFees]) => {
            const template = document.getElementById('fees-template');
            const clone = template.content.cloneNode(true);
            
            clone.querySelector('.academic-year').textContent = academicYear;
            const tbody = clone.querySelector('tbody');

            yearFees.forEach(fee => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${fee.term_name}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-700">${formatCurrency(fee.registration_fee)}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-700">${formatCurrency(fee.tuition_fee)}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-700">${formatCurrency(fee.practical_fee)}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-700">${formatCurrency(fee.trip_fee)}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-700">${formatCurrency(fee.student_card_fee)}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-right text-blue-600">${formatCurrency(fee.total_fees)}</td>
                `;
                tbody.appendChild(row);
            });

            feesContainer.appendChild(clone);
        });
    }

    // Show error message
    function showError(message) {
        loadingState.classList.add('hidden');
        feesDisplay.classList.add('hidden');
        initialState.classList.add('hidden');
        errorState.classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
    }

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 0,
        }).format(amount);
    }

    // Download PDF
    downloadBtn.addEventListener('click', function() {
        if (currentCourseId) {
            window.location.href = `/public/fees/courses/${currentCourseId}/download`;
        }
    });
});
</script>

<style>
    @media print {
        .sticky {
            position: static !important;
        }
        #download-btn, #courses-section > div:first-child {
            display: none;
        }
    }
</style>
@endsection
