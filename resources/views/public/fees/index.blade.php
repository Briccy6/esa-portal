@extends('layouts.public')

@section('title', 'Fees Schedule - Excellent Servant Academy')

@push('styles')
<style>
    .fees-editorial,
    .fees-hero,
    .fees-guide,
    .fees-workbench,
    .fees-result,
    .fees-extra {
        display: block;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .fees-guide {
        display: grid;
    }

    .fees-workbench {
        display: grid;
    }

    .fees-extra {
        display: grid;
    }

    .fees-hero h1.fees-page__heading {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff;
        text-shadow: 0 3px 18px rgba(0, 0, 0, .24);
    }

    #fees-display.hidden,
    #initial-state.hidden,
    #error-state.hidden,
    #loading-state.hidden,
    #courses-section.hidden {
        display: none !important;
        visibility: hidden !important;
    }

    .fees-editorial {
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(23,59,50,.04), transparent 32%),
            #f5f4ee;
    }

    .fees-hero {
        position: relative;
        margin-bottom: 2.25rem;
        overflow: hidden;
        border: 1px solid #e1e6df;
        border-radius: 24px;
        background:
            linear-gradient(110deg, rgba(20,47,41,.96), rgba(46,102,82,.78)),
            url('{{ asset("images/2.jpg") }}') center 42% / cover;
        color: #fff;
        box-shadow: 0 20px 44px rgba(23,59,50,.12);
    }

    .fees-hero::after {
        position: absolute;
        right: -5rem;
        bottom: -8rem;
        width: 19rem;
        height: 19rem;
        border: 1px solid rgba(238,208,140,.34);
        border-radius: 50%;
        content: "";
        box-shadow: 0 0 0 3rem rgba(238,208,140,.035), 0 0 0 6rem rgba(238,208,140,.025);
    }

    .fees-hero__inner {
        position: relative;
        z-index: 1;
        max-width: 760px;
        padding: clamp(2rem, 5vw, 4rem);
    }

    .fees-hero__kicker {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        margin-bottom: 1rem;
        color: #eed08c;
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .fees-hero__kicker::before {
        width: 2rem;
        height: 1px;
        background: #eed08c;
        content: "";
    }

    .fees-hero h1 {
        max-width: 650px;
        margin: 0 0 .8rem;
        color: #fff;
        font-size: clamp(2.35rem, 5vw, 4.5rem);
        line-height: 1.02;
    }

    .fees-hero p {
        max-width: 620px;
        margin: 0;
        color: rgba(255,255,255,.82);
        font-size: 1.02rem;
        line-height: 1.7;
    }

    .fees-guide {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1px;
        margin: 0 0 2rem;
        overflow: hidden;
        border: 1px solid #dde4dc;
        border-radius: 18px;
        background: #dde4dc;
    }

    .fees-guide__item {
        min-height: 128px;
        padding: 1.35rem 1.5rem;
        background: #fff;
    }

    .fees-guide__item:first-child {
        border-top: 3px solid #c79b50;
    }

    .fees-guide__item:last-child {
        border-top: 3px solid #2e6652;
    }

    .fees-guide__item h3 {
        display: flex;
        align-items: center;
        gap: .7rem;
        margin: 0 0 .55rem;
        color: #173b32;
        font-size: 1rem;
    }

    .fees-guide__item h3 i {
        color: #2e6652;
    }

    .fees-guide__item p {
        margin: 0;
        color: #65726c;
        font-size: .91rem;
        line-height: 1.65;
    }

    .fees-workbench {
        display: grid;
        grid-template-columns: minmax(220px, 300px) minmax(0, 1fr);
        align-items: start;
        gap: 1.5rem;
    }

    .fees-selector {
        position: sticky;
        top: 108px;
        overflow: hidden;
        border: 1px solid #dfe6df;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 36px rgba(23,59,50,.07);
    }

    .fees-selector__step {
        padding: 1.35rem;
    }

    .fees-selector__step + .fees-selector__step {
        border-top: 1px solid #edf0ec;
    }

    .fees-selector__label {
        display: flex;
        align-items: center;
        gap: .7rem;
        margin: 0 0 .85rem;
        color: #173b32;
        font-family: 'Lora', Georgia, serif;
        font-size: 1.04rem;
        font-weight: 700;
    }

    .fees-selector__number {
        width: 32px;
        height: 32px;
        display: inline-grid;
        flex: 0 0 32px;
        place-items: center;
        border-radius: 50%;
        background: #173b32;
        color: #fff;
        font-family: 'Source Sans 3', sans-serif;
        font-size: .8rem;
    }

    .fees-selector select {
        width: 100%;
        min-height: 48px;
        padding: .72rem .9rem;
        border: 1px solid #d4ddd5;
        border-radius: 10px;
        background: #fbfcfa;
        color: #26352f;
        font-weight: 600;
        outline: none;
    }

    .fees-selector select:focus {
        border-color: #2e6652;
        box-shadow: 0 0 0 3px rgba(46,102,82,.12);
    }

    .fees-result {
        min-width: 0;
    }

    .fees-result__initial,
    .fees-result__error {
        min-height: 310px;
        display: grid;
        place-items: center;
        border: 1px dashed #ccd7ce;
        border-radius: 20px;
        background: rgba(255,255,255,.7);
        text-align: center;
    }

    .fees-result__initial-inner,
    .fees-result__error-inner {
        max-width: 430px;
        padding: 2rem;
    }

    .fees-result__initial-icon,
    .fees-result__error-icon {
        width: 62px;
        height: 62px;
        display: inline-grid;
        place-items: center;
        margin-bottom: 1rem;
        border-radius: 50%;
        background: #edf3ee;
        color: #2e6652;
        font-size: 1.35rem;
    }

    .fees-result__initial p,
    .fees-result__error p {
        margin: 0;
        color: #65726c;
    }

    .fees-result__error {
        border-style: solid;
        border-color: #eccaca;
        background: #fff7f5;
    }

    .fees-result__error-icon {
        background: #f7dfda;
        color: #a44c3d;
    }

    .fees-course-header {
        position: relative;
        overflow: hidden;
        padding: 1.5rem 1.75rem;
        border-radius: 18px 18px 4px 4px;
        background: linear-gradient(120deg, #173b32, #2e6652);
        color: #fff;
    }

    .fees-course-header::after {
        position: absolute;
        top: -4.5rem;
        right: -2.5rem;
        width: 11rem;
        height: 11rem;
        border: 1px solid rgba(238,208,140,.28);
        border-radius: 50%;
        content: "";
    }

    #course-title {
        position: relative;
        z-index: 1;
        margin: 0;
        color: #fff;
        font-size: clamp(1.35rem, 3vw, 2rem);
    }

    #course-code {
        position: relative;
        z-index: 1;
        margin: .35rem 0 0;
        color: #d6e2da;
        font-size: .88rem;
    }

    #fees-container > div {
        overflow: hidden;
        border: 1px solid #dfe6df;
        border-top: 0;
        border-radius: 4px 4px 16px 16px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(23,59,50,.07);
    }

    #fees-container table {
        min-width: 650px;
    }

    #fees-container thead {
        background: #eef3ef;
    }

    #fees-container th {
        color: #526159;
        font-size: .76rem;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    #fees-container tbody tr {
        transition: background .16s ease;
    }

    #fees-container tbody tr:hover {
        background: #fbfcfa;
    }

    .fees-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1rem;
    }

    .fees-actions button {
        min-height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        padding: .75rem 1rem;
        border: 1px solid transparent;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .fees-actions__download {
        background: #173b32;
        color: #fff;
    }

    .fees-actions__download:hover {
        background: #2e6652;
    }

    .fees-actions__print {
        border-color: #d9e1da !important;
        background: #fff;
        color: #405148;
    }

    .fees-actions__print:hover {
        border-color: #2e6652 !important;
        color: #173b32;
    }

    .fees-extra {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 2.25rem;
    }

    .fees-extra__item {
        min-height: 150px;
        padding: 1.35rem;
        border: 1px solid #dde4dc;
        border-radius: 16px;
        background: #fff;
    }

    .fees-extra__item i {
        width: 38px;
        height: 38px;
        display: inline-grid;
        place-items: center;
        margin-bottom: .8rem;
        border-radius: 10px;
        background: #edf3ee;
        color: #2e6652;
    }

    .fees-extra__item h3 {
        margin: 0 0 .45rem;
        color: #173b32;
        font-size: 1rem;
    }

    .fees-extra__item p {
        margin: 0;
        color: #65726c;
        font-size: .88rem;
        line-height: 1.6;
    }

    @media (max-width: 900px) {
        .fees-workbench {
            grid-template-columns: 1fr;
        }

        .fees-selector {
            position: relative;
            top: 0;
        }
    }

    @media (max-width: 640px) {
        .fees-hero {
            border-radius: 18px;
            background-position: 60% center;
        }

        .fees-guide,
        .fees-extra {
            grid-template-columns: 1fr;
        }

        .fees-guide__item,
        .fees-extra__item {
            min-height: auto;
        }

        .fees-selector__step {
            padding: 1.1rem;
        }

        .fees-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .fees-actions button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="fees-editorial">
<div class="public-fees container mx-auto px-4 py-10 max-w-6xl">
    <header class="fees-hero">
        <div class="fees-hero__inner">
            <p class="fees-hero__kicker">Official school information</p>
            <h1 class="fees-page__heading">Plan for the term ahead.</h1>
            <p>Choose a branch and course to view the current fee structure by academic year and term.</p>
        </div>
    </header>

    <div class="fees-guide" aria-label="Fee information">
        <article class="fees-guide__item">
            <h3><i class="fas fa-user-plus" aria-hidden="true"></i>New students</h3>
            <p>Registration is a one-time fee that applies when you enroll for the first time.</p>
        </article>
        <article class="fees-guide__item">
            <h3><i class="fas fa-user-check" aria-hidden="true"></i>Existing students</h3>
            <p>The registration fee may not apply again; the term schedule shows tuition and remaining fees.</p>
        </article>
    </div>

    <div class="fees-workbench">
        <aside class="fees-selector" aria-label="Choose a fee schedule">
            <div class="fees-selector__step">
                <h2 class="fees-selector__label">
                    <span class="fees-selector__number">01</span>
                    Select a branch
                </h2>
                <label for="branch-select" class="sr-only">Choose a branch</label>
                <select id="branch-select">
                        <option value="">-- Select a branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }} @if($branch->location) ({{ $branch->location }}) @endif
                            </option>
                        @endforeach
                </select>
            </div>

            <div id="courses-section" class="fees-selector__step hidden">
                <h2 class="fees-selector__label">
                    <span class="fees-selector__number">02</span>
                    Select a course
                </h2>
                <label for="course-select" class="sr-only">Choose a course</label>
                <select id="course-select">
                            <option value="">-- Select a course --</option>
                </select>
            </div>
        </aside>

        <section class="fees-result" aria-live="polite">
            <div id="fees-display" class="hidden">
                <div id="loading-state" class="fees-result__initial hidden">
                    <div class="fees-result__initial-inner">
                        <div class="inline-block w-12 h-12 border-4 border-emerald-100 border-t-emerald-800 rounded-full animate-spin"></div>
                        <p class="mt-4">Loading the current fee schedule...</p>
                    </div>
                </div>

                <div id="course-header" class="fees-course-header">
                    <h2 id="course-title"></h2>
                    <p id="course-code"></p>
                </div>

                <div id="fees-container" class="space-y-6">
                </div>

                <div class="fees-actions">
                    <button id="download-btn" class="fees-actions__download" type="button">
                        <i class="fas fa-download" aria-hidden="true"></i>
                        Download PDF
                    </button>
                    <button type="button" class="fees-actions__print" onclick="window.print()">
                        <i class="fas fa-print" aria-hidden="true"></i>
                        Print
                    </button>
                </div>
            </div>

            <div id="initial-state" class="fees-result__initial">
                <div class="fees-result__initial-inner">
                    <span class="fees-result__initial-icon"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
                    <p>Select a branch and course to view the official fees schedule.</p>
                </div>
            </div>

            <div id="error-state" class="fees-result__error hidden">
                <div class="fees-result__error-inner">
                    <span class="fees-result__error-icon"><i class="fas fa-exclamation-circle" aria-hidden="true"></i></span>
                    <h3 class="text-lg font-bold text-red-900 mb-2">Unable to load the schedule</h3>
                    <p id="error-message" class="text-red-700"></p>
                </div>
            </div>
        </section>
    </div>

    <section class="fees-extra" aria-label="Fees information">
        <article class="fees-extra__item">
            <i class="fas fa-wallet" aria-hidden="true"></i>
            <h3>Fee components</h3>
            <p>Registration, tuition, practical work, trips, and student card charges are itemized separately.</p>
        </article>
        <article class="fees-extra__item">
            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
            <h3>Organized by term</h3>
            <p>The schedule is grouped by academic year so you can plan payments for each term.</p>
        </article>
        <article class="fees-extra__item">
            <i class="fas fa-download" aria-hidden="true"></i>
            <h3>Keep or print</h3>
            <p>Download the selected course schedule as PDF or print a clean copy for reference.</p>
        </article>
    </section>
</div>
</div>

<!-- Fees Template (will be cloned for each academic year) -->
<template id="fees-template">
    <div>
        <div class="px-6 py-4 border-b border-gray-200" style="background:#f7f9f6">
            <h3 class="text-lg font-semibold text-gray-900">Academic year: <span class="academic-year"></span></h3>
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
        fetch(`/fees/branches/${branchId}/courses`)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw {
                            status: response.status,
                            statusText: response.statusText,
                            data: data
                        };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.courses && data.courses.length > 0) {
                        data.courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.textContent = `${course.name} (${course.code})`;
                            courseSelect.appendChild(option);
                        });
                        console.log(`✅ Loaded ${data.courses.length} courses for branch "${data.branch.name}"`);
                    } else {
                        showError(`❌ No courses available for this branch. (Branch: ${data.branch.name})`);
                        console.warn('No courses in branch_course pivot table for branch:', data.branch);
                    }
                } else {
                    showError(data.message || 'Failed to load courses');
                    if (data.debug) {
                        console.error('Debug Info:', data.debug);
                        console.log('📊 Database Stats:', {
                            totalBranches: data.debug.totalBranches,
                            totalCourses: data.debug.totalCourses,
                            linkedPairs: data.debug.totalPivotRecords
                        });
                    }
                }
            })
            .catch(error => {
                console.error('❌ API Error Details:', error);
                
                if (error.status === 500) {
                    console.error('🔴 Server Error 500');
                    console.error('Error Type:', error.data?.debug?.errorType);
                    console.error('Error Message:', error.data?.debug?.errorMessage);
                    console.error('Error Location:', `${error.data?.debug?.errorFile}:${error.data?.debug?.errorLine}`);
                    console.log('📊 Database Stats:', {
                        totalBranches: error.data?.debug?.totalBranches,
                        totalCourses: error.data?.debug?.totalCourses,
                        linkedPairs: error.data?.debug?.totalPivotRecords
                    });
                    console.error('Stack Trace:', error.data?.debug?.trace);
                    showError(`Server Error: ${error.data?.message || 'Unable to load courses. Check browser console for details.'}`);
                } else if (error.status === 404) {
                    showError(`Branch not found. Database may be empty or branch ID ${branchId} doesn't exist.`);
                    console.error('Branch not found:', error.data?.debug);
                } else {
                    showError(`Error: ${error.message || 'Unknown error occurred'}`);
                }
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

        fetch(`/fees/courses/${courseId}`)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw {
                            status: response.status,
                            statusText: response.statusText,
                            data: data
                        };
                    });
                }
                return response.json();
            })
            .then(data => {
                loadingState.classList.add('hidden');
                if (data.success) {
                    console.log(`✅ Loaded fees for course: ${data.course.name}`);
                    displayCourse(data.course);
                    displayFees(data.fees);
                } else {
                    showError(data.message || 'Failed to load fees');
                    if (data.debug) {
                        console.warn('Fee Loading Info:', data.debug);
                    }
                }
            })
            .catch(error => {
                loadingState.classList.add('hidden');
                console.error('❌ Fee Loading Error:', error);
                
                if (error.status === 404) {
                    showError(`No fees configured for this course yet. Please contact administration.`);
                    console.warn('Course fee records:', error.data?.debug);
                } else if (error.status === 500) {
                    showError(`Server error loading fees: ${error.data?.message || 'Unknown error'}`);
                    console.error('Server Error Details:', error.data?.debug);
                } else {
                    showError(`Error: ${error.message || 'Unable to load fees'}`);
                }
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
        // Format number with proper thousand separators
        const formatted = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
            useGrouping: true
        }).format(amount);
        return `RWF ${formatted}`;
    }

    // Download PDF
    downloadBtn.addEventListener('click', function() {
        if (currentCourseId) {
            window.location.href = `/fees/courses/${currentCourseId}/download`;
        }
    });
});
</script>

<style>
    @media print {
        /* Hide everything by default */
        * {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        body {
            background: white !important;
            margin: 0 !important;
            padding: 10mm !important;
        }
        
        /* Hide navigation, headers, and controls */
        header, nav, .lg\:col-span-1, .p-8.md\:p-12, 
        #initial-state, #error-state, #loading-state,
        .inline-flex.items-center, .space-x-3, button,
        .mt-16, .gap-6, .additional-information {
            display: none !important;
        }
        
        /* Show only the fees display section */
        #fees-display {
            display: block !important;
            visibility: visible !important;
            position: static !important;
            width: 100% !important;
        }
        
        /* Main container */
        .container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .lg\:col-span-2 {
            grid-column: auto !important;
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Course header */
        #course-header {
            background: #f3f4f6 !important;
            color: #1f2937 !important;
            page-break-after: avoid !important;
            border: 2px solid #1f2937 !important;
            padding: 15px !important;
            margin: 20px 0 !important;
        }
        
        #course-title {
            font-size: 24px !important;
            font-weight: bold !important;
            margin: 0 !important;
        }
        
        #course-code {
            font-size: 12px !important;
            color: #4b5563 !important;
            margin: 5px 0 0 0 !important;
        }
        
        /* Fees table styling */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            page-break-inside: avoid !important;
            margin: 15px 0 !important;
            font-size: 11px !important;
        }
        
        thead {
            display: table-header-group !important;
            background-color: #2c3e50 !important;
            color: white !important;
        }
        
        th {
            background-color: #2c3e50 !important;
            color: white !important;
            font-weight: bold !important;
            border: 1px solid #1f2937 !important;
            padding: 8px !important;
            text-align: left !important;
        }
        
        td {
            border: 1px solid #d1d5db !important;
            padding: 8px !important;
            text-align: left !important;
        }
        
        tr {
            page-break-inside: avoid !important;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9 !important;
        }
        
        /* Ensure header repeats on each page */
        .academic-year {
            font-weight: bold !important;
            font-size: 12px !important;
            margin: 10px 0 5px 0 !important;
            display: block !important;
        }
        
        /* Hide action buttons but show in non-print */
        .no-print {
            display: none !important;
        }
        
        /* Document info footer */
        footer, .mt-12.pt-6 {
            display: none !important;
        }
    }
    
    @page {
        margin: 10mm;
        size: A4 portrait;
    }
</style>
@endsection
