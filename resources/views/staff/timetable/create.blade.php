@extends('staff.partials.sidebar_dos')

@section('content')
<style>
    .timetable-create-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
    }

    .timetable-create-header {
        color: white;
        margin-bottom: 0;
    }

    .timetable-create-header h2 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .timetable-create-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .form-section-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .form-section-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section-header h5 {
        margin: 0;
        color: #1e293b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-header h5 i {
        color: #667eea;
    }

    .form-section-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .session-row {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .session-row:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .session-row .card-body {
        padding: 1.25rem;
    }

    .session-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-outline-primary {
        border: 2px solid #667eea;
        color: #667eea;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        border: 2px solid #ef4444;
        color: #ef4444;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-outline-danger:hover {
        background: #ef4444;
        color: white;
    }

    .input-group-text {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-right: none;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .stats-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
    }

    .stats-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
    }

    .stats-label {
        font-size: 0.875rem;
        color: #64748b;
    }
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="timetable-create-container">
        <div class="timetable-create-header">
            <h2><i class="fas fa-calendar-plus me-2"></i>Create Weekly Timetable</h2>
            <p>Set up the teaching schedule for your branch and academic year</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('dos.timetable.store') }}" method="POST">
        @csrf

        <!-- Basic Information -->
        <div class="form-section-card">
            <div class="form-section-header">
                <h5><i class="fas fa-info-circle"></i>Basic Information</h5>
            </div>
            <div class="form-section-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1"></i>Timetable Title
                        </label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="E.g. Term 1 Weekly Timetable" required>
                    </div>
                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">
                            <i class="fas fa-building me-1"></i>Branch
                        </label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id', $branchId) == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="academic_year_id" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Academic Year
                        </label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                            <option value="">Select academic year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id', $selectedAcademicYearId) == $year->id)>{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="term_id" class="form-label">
                            <i class="fas fa-calendar-week me-1"></i>Term
                        </label>
                        <select name="term_id" id="term_id" class="form-select" @if(!$activeTerm) required @endif>
                            <option value="" @selected(old('term_id') === null)>-- Select term --</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" @selected(old('term_id', $activeTerm?->id) == $term->id)>{{ $term->name }} ({{ $term->start_date->format('M j') }} - {{ $term->end_date->format('M j') }})</option>
                            @endforeach
                        </select>
                        @if($activeTerm)
                            <small class="text-success"><i class="fas fa-check-circle me-1"></i>Using active term: {{ $activeTerm->name }} ({{ $activeTerm->start_date->format('M j') }} - {{ $activeTerm->end_date->format('M j') }})</small>
                        @else
                            <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>No active term found. Please select a term manually.</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="valid_from" class="form-label">
                            <i class="fas fa-calendar-check me-1"></i>Valid From
                        </label>
                        <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="valid_until" class="form-label">
                            <i class="fas fa-calendar-times me-1"></i>Valid Until
                        </label>
                        <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions -->
        <div class="form-section-card">
            <div class="form-section-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-clock"></i>Schedule Sessions</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" id="add-session-row">
                    <i class="fas fa-plus me-1"></i>Add Session
                </button>
            </div>
            <div class="form-section-body">
                <!-- Session Stats -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="session-count">0</div>
                            <div class="stats-label">Total Sessions</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="test-count">0</div>
                            <div class="stats-label">Test Sessions</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="day-count">0</div>
                            <div class="stats-label">Days Covered</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="hour-count">0</div>
                            <div class="stats-label">Total Hours</div>
                        </div>
                    </div>
                </div>

                <div id="sessions-container">
                    @php
                        $oldSessions = old('sessions', []);
                        $sessionRows = count($oldSessions) > 0 ? $oldSessions : [null];
                    @endphp

                    @foreach($sessionRows as $index => $sessionData)
                        <div class="card session-row" data-index="{{ $index }}">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="session-number me-3">{{ $index + 1 }}</div>
                                    <h6 class="mb-0 text-primary">Session #{{ $index + 1 }}</h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-auto remove-session-row">
                                        <i class="fas fa-trash me-1"></i>Remove
                                    </button>
                                </div>
                                <div class="row gy-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-book me-1"></i>Lesson
                                        </label>
                                        <select name="sessions[{{ $index }}][lesson_id]" class="form-select lesson-select" required>
                                            <option value="">Select lesson</option>
                                            @foreach($groupedLessons as $lesson)
                                                <option value="{{ $lesson->lesson_id }}"
                                                        data-course-ids="{{ implode(',', $lesson->all_course_ids) }}"
                                                        @selected(old("sessions.{$index}.lesson_id", $sessionData['lesson_id'] ?? '') == $lesson->lesson_id)>
                                                    {{ $lesson->lesson->title ?? 'Untitled lesson' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <i class="fas fa-calendar-day me-1"></i>Day
                                        </label>
                                        <select name="sessions[{{ $index }}][day_of_week]" class="form-select" required>
                                            @foreach([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old("sessions.{$index}.day_of_week", $sessionData['day_of_week'] ?? '') == (string) $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <i class="fas fa-clock me-1"></i>Start
                                        </label>
                                        <input type="time" name="sessions[{{ $index }}][start_time]" class="form-control" value="{{ old("sessions.{$index}.start_time", $sessionData['start_time'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <i class="fas fa-clock me-1"></i>End
                                        </label>
                                        <input type="time" name="sessions[{{ $index }}][end_time]" class="form-control" value="{{ old("sessions.{$index}.end_time", $sessionData['end_time'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <i class="fas fa-door-open me-1"></i>Classroom
                                        </label>
                                        <input type="text" name="sessions[{{ $index }}][classroom]" class="form-control" value="{{ old("sessions.{$index}.classroom", $sessionData['classroom'] ?? '') }}" placeholder="Room">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-graduation-cap me-1"></i>Course(s)
                                        </label>
                                        <select name="sessions[{{ $index }}][course_ids][]" class="form-select course-select" multiple data-placeholder="Select course(s)">
                                            @foreach($coursesWithTeachers as $course)
                                                <option value="{{ $course->id }}" data-teacher-name="{{ $course->teacher_name }}" @selected(in_array($course->id, old("sessions.{$index}.course_ids", $sessionData['course_ids'] ?? [])))>
                                                    {{ $course->abbreviation ?? $course->course_name }}
                                                    @if($course->teacher_name)
                                                        ({{ $course->teacher_name }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-sticky-note me-1"></i>Notes
                                        </label>
                                        <textarea name="sessions[{{ $index }}][notes]" class="form-control" rows="2" placeholder="Optional notes...">{{ old("sessions.{$index}.notes", $sessionData['notes'] ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" name="sessions[{{ $index }}][mark_as_test]" value="1" id="mark_as_test_{{ $index }}" @checked(old("sessions.{$index}.mark_as_test", $sessionData['mark_as_test'] ?? false))>
                                            <label class="form-check-label" for="mark_as_test_{{ $index }}">
                                                <i class="fas fa-clipboard-check me-1"></i>Mark as Test/Exam Session
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="form-section-card">
            <div class="form-section-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Save as draft to continue editing later, or publish to make it visible to teachers and students.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('dos.timetable.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Draft
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="session-row-template">
    <div class="card session-row" data-index="__INDEX__">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="session-number me-3">#</div>
                <h6 class="mb-0 text-primary">New Session</h6>
                <button type="button" class="btn btn-outline-danger btn-sm ms-auto remove-session-row">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
            <div class="row gy-3">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-book me-1"></i>Lesson
                    </label>
                    <select name="sessions[__INDEX__][lesson_id]" class="form-select lesson-select" required>
                        <option value="">Select lesson</option>
                        @foreach($groupedLessons as $lesson)
                            <option value="{{ $lesson->lesson_id }}"
                                    data-course-ids="{{ implode(',', $lesson->all_course_ids) }}">
                                {{ $lesson->lesson->title ?? 'Untitled lesson' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar-day me-1"></i>Day
                    </label>
                    <select name="sessions[__INDEX__][day_of_week]" class="form-select" required>
                        @foreach([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-clock me-1"></i>Start
                    </label>
                    <input type="time" name="sessions[__INDEX__][start_time]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-clock me-1"></i>End
                    </label>
                    <input type="time" name="sessions[__INDEX__][end_time]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-door-open me-1"></i>Classroom
                    </label>
                    <input type="text" name="sessions[__INDEX__][classroom]" class="form-control" placeholder="Room">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-graduation-cap me-1"></i>Course(s)
                    </label>
                    <select name="sessions[__INDEX__][course_ids][]" class="form-select course-select" multiple data-placeholder="Select course(s)">
                        @foreach($coursesWithTeachers as $course)
                            <option value="{{ $course->id }}" data-teacher-name="{{ $course->teacher_name }}">
                                {{ $course->abbreviation ?? $course->course_name }}
                                @if($course->teacher_name)
                                    ({{ $course->teacher_name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-sticky-note me-1"></i>Notes
                    </label>
                    <textarea name="sessions[__INDEX__][notes]" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="sessions[__INDEX__][mark_as_test]" value="1" id="mark_as_test___INDEX__">
                        <label class="form-check-label" for="mark_as_test___INDEX__">
                            <i class="fas fa-clipboard-check me-1"></i>Mark as Test/Exam Session
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('sessions-container');
        const template = document.getElementById('session-row-template').innerHTML;
        const addButton = document.getElementById('add-session-row');
        const branchSelect = document.getElementById('branch_id');

        // Load lessons for selected branch
        async function loadLessonsForBranch(branchId) {
            if (!branchId) return [];
            try {
                // Use the existing lessonAssignments data passed from controller as fallback
                const lessonData = @json($groupedLessons ?? []);
                return lessonData.filter(lesson => {
                    // Filter by checking if lesson has courses in this branch
                    // For now, return all since we don't have branch-specific data in the initial load
                    return true;
                });
            } catch (err) {
                console.error('Failed to load branch lessons', err);
                return [];
            }
        }

        // Refresh lesson selects when branch changes
        async function refreshLessonSelects(branchId) {
            const lessons = await loadLessonsForBranch(branchId);
            const selects = document.querySelectorAll('.lesson-select');
            selects.forEach(sel => {
                const prevValue = sel.value;
                sel.innerHTML = '<option value="">Select lesson</option>';
                lessons.forEach(lesson => {
                    const opt = document.createElement('option');
                    opt.value = lesson.lesson_id;
                    opt.setAttribute('data-course-ids', lesson.all_course_ids.join(','));
                    opt.textContent = lesson.lesson_title;
                    if (prevValue === String(lesson.lesson_id)) opt.selected = true;
                    sel.appendChild(opt);
                });
            });
        }

        // Bind branch change event
        if (branchSelect) {
            branchSelect.addEventListener('change', function () {
                refreshLessonSelects(this.value);
            });
        }

        // Bind lesson change event to auto-select courses
        function bindLessonChangeHandlers() {
            container.querySelectorAll('.lesson-select').forEach(select => {
                select.removeEventListener('change', handleLessonChange);
                select.addEventListener('change', handleLessonChange);
            });
        }

        function handleLessonChange(event) {
            const lessonSelect = event.target;
            const selectedOption = lessonSelect.options[lessonSelect.selectedIndex];
            const courseIds = selectedOption.getAttribute('data-course-ids');
            const row = lessonSelect.closest('.session-row');
            const courseSelect = row.querySelector('.course-select');

            if (courseIds && courseSelect) {
                const courseIdArray = courseIds.split(',').map(id => id.trim()).filter(id => id);
                
                // Clear current selections
                if (window.jQuery && jQuery().select2) {
                    jQuery(courseSelect).val(null).trigger('change');
                } else {
                    Array.from(courseSelect.options).forEach(opt => opt.selected = false);
                }
                
                // Filter options to show only courses associated with this lesson
                Array.from(courseSelect.options).forEach(opt => {
                    if (courseIdArray.includes(opt.value)) {
                        opt.disabled = false;
                        opt.style.display = '';
                    } else {
                        opt.disabled = true;
                        opt.style.display = 'none';
                    }
                });
                
                // Select the courses matching the lesson
                if (window.jQuery && jQuery().select2) {
                    jQuery(courseSelect).val(courseIdArray).trigger('change');
                } else {
                    Array.from(courseSelect.options).forEach(opt => {
                        if (courseIdArray.includes(opt.value)) opt.selected = true;
                    });
                }
            }
        }

        function updateIndexes() {
            container.querySelectorAll('.session-row').forEach((row, index) => {
                row.dataset.index = index;
                const sessionNumber = row.querySelector('.session-number');
                if (sessionNumber) {
                    sessionNumber.textContent = index + 1;
                }
                const sessionTitle = row.querySelector('h6');
                if (sessionTitle) {
                    sessionTitle.textContent = `Session #${index + 1}`;
                }
                row.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace(/sessions\[\d+\]/, `sessions[${index}]`);
                });
                row.querySelectorAll('[id]').forEach(element => {
                    const id = element.id.replace(/_\d+$/, `_${index}`);
                    element.id = id;
                });
            });
            updateSessionStats();
        }

        function updateSessionStats() {
            const sessions = container.querySelectorAll('.session-row');
            const sessionCount = sessions.length;
            let testCount = 0;
            const daysCovered = new Set();
            let totalHours = 0;

            sessions.forEach(session => {
                const isTest = session.querySelector('[name$="[mark_as_test]"]')?.checked;
                if (isTest) testCount++;

                const dayValue = session.querySelector('[name$="[day_of_week]"]')?.value;
                if (dayValue) daysCovered.add(dayValue);

                const startTime = session.querySelector('[name$="[start_time]"]')?.value;
                const endTime = session.querySelector('[name$="[end_time]"]')?.value;
                if (startTime && endTime) {
                    const start = new Date(`2000-01-01T${startTime}`);
                    const end = new Date(`2000-01-01T${endTime}`);
                    const diff = (end - start) / (1000 * 60 * 60);
                    totalHours += diff;
                }
            });

            document.getElementById('session-count').textContent = sessionCount;
            document.getElementById('test-count').textContent = testCount;
            document.getElementById('day-count').textContent = daysCovered.size;
            document.getElementById('hour-count').textContent = totalHours.toFixed(1);
        }

        function addSessionRow() {
            const nextIndex = container.querySelectorAll('.session-row').length;
            const rowMarkup = template.replace(/__INDEX__/g, nextIndex);
            container.insertAdjacentHTML('beforeend', rowMarkup);
            bindRemoveButtons();
            initCourseSelects();
            bindLessonChangeHandlers();
            // Reload lessons for the new row based on current branch
            const currentBranchId = branchSelect ? branchSelect.value : null;
            if (currentBranchId) {
                refreshLessonSelects(currentBranchId);
            }
        }

        // Initial bindings
        bindRemoveButtons();
        initCourseSelects();
        bindLessonChangeHandlers();
    }

    function updateIndexes() {
        container.querySelectorAll('.session-row').forEach((row, idx) => {
            row.querySelectorAll('input, select').forEach(input => {
                input.name = input.name.replace(/sessions\[\d+\]/, `sessions[${idx}]`);
            });
        });
    }

        function bindRemoveButtons() {
            container.querySelectorAll('.remove-session-row').forEach(btn => {
                btn.removeEventListener('click', removeRow);
                btn.addEventListener('click', removeRow);
            });
        }

        function removeRow(event) {
            const targetRow = event.currentTarget.closest('.session-row');
            if (targetRow) {
                targetRow.remove();
                updateIndexes();
            }
        }

        function bindLessonChangeHandlers() {
            container.querySelectorAll('.lesson-select').forEach(select => {
                select.removeEventListener('change', handleLessonChange);
                select.addEventListener('change', handleLessonChange);
            });
        }

        function handleLessonChange(event) {
            const lessonSelect = event.target;
            const selectedOption = lessonSelect.options[lessonSelect.selectedIndex];
            const courseId = selectedOption.getAttribute('data-course-id');
            const row = lessonSelect.closest('.session-row');
            const courseSelect = row.querySelector('.course-select');

            if (courseId && courseSelect) {
                Array.from(courseSelect.options).forEach(opt => opt.selected = false);
                const matchingOption = Array.from(courseSelect.options).find(opt => opt.value === courseId);
                if (matchingOption) {
                    matchingOption.selected = true;
                }
                if (window.jQuery && jQuery().select2) {
                    jQuery(courseSelect).trigger('change');
                }
            }
        }

        // Listen for changes to update stats
        container.addEventListener('change', updateSessionStats);

        addButton.addEventListener('click', addSessionRow);
        bindRemoveButtons();
        initCourseSelects();
        bindLessonChangeHandlers();
        updateSessionStats();
    });
</script>
<!-- Select2 assets -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    function initCourseSelects() {
        if (!window.jQuery || !jQuery().select2) return;
        jQuery('.course-select').each(function () {
            const $el = jQuery(this);
            // destroy if already initialized
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            $el.select2({
                placeholder: $el.data('placeholder') || 'Select course(s)',
                width: '100%',
                allowClear: true,
                dropdownParent: $el.parent(),
                closeOnSelect: false
            });
        });
    }
</script>
<script>
    // Dynamically load courses when branch changes and update all course multi-selects
    document.addEventListener('DOMContentLoaded', function () {
        const branchSelect = document.getElementById('branch_id');

        async function loadCoursesForBranch(branchId) {
            try {
                const url = new URL('{{ route('dos.timetable.branch-courses') }}', window.location.origin);
                url.searchParams.set('branch_id', branchId);
                const res = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!res.ok) return [];
                return await res.json();
            } catch (err) {
                console.error('Failed to load branch courses', err);
                return [];
            }
        }

        async function refreshCourseSelects(branchId) {
            const courses = await loadCoursesForBranch(branchId);
            const selects = document.querySelectorAll('.course-select');
            selects.forEach(sel => {
                // remember selected values that still apply
                const prev = Array.from(sel.options).filter(o => o.selected).map(o => o.value);
                sel.innerHTML = '';
                courses.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.course_name;
                    if (prev.includes(String(c.id))) opt.selected = true;
                    sel.appendChild(opt);
                });
            });
            // Destroy and reinitialize Select2 for all course selects
            if (window.jQuery && jQuery().select2) {
                jQuery('.course-select').each(function() {
                    if (jQuery(this).hasClass('select2-hidden-accessible')) {
                        jQuery(this).select2('destroy');
                    }
                });
                initCourseSelects();
            }
        }

        if (branchSelect) {
            branchSelect.addEventListener('change', function () {
                refreshCourseSelects(this.value);
            });

            // initial load to ensure selects match initial branch
            refreshCourseSelects(branchSelect.value);
        }
    });
</script>
@endsection