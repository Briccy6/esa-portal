@extends('dos.layout')

@section('content')
<div class="container mt-4">
    <h2>Edit Weekly Timetable</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dos.timetable.update', $timetable) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row gy-3">
            <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $timetable->title) }}" required>
            </div>
            <div class="col-md-6">
                <label for="branch_id" class="form-label">Branch</label>
                <select name="branch_id" id="branch_id" class="form-select" required>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $timetable->branch_id) == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(old('academic_year_id', $selectedAcademicYearId) == $year->id)>{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="term_id" class="form-label">Term</label>
                <select name="term_id" id="term_id" class="form-select" @if(!$activeTerm) required @endif>
                    <option value="" @selected(old('term_id', $timetable->term_id) === null)>-- Select term --</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" @selected(old('term_id', $timetable->term_id ?: $activeTerm?->id) == $term->id)>{{ $term->name }} ({{ $term->start_date->format('M j') }} - {{ $term->end_date->format('M j') }})</option>
                    @endforeach
                </select>
                @if($activeTerm)
                    <small class="text-muted">Using active term: {{ $activeTerm->name }} ({{ $activeTerm->start_date->format('M j') }} - {{ $activeTerm->end_date->format('M j') }}).</small>
                @else
                    <small class="text-muted">No active term found for the selected academic year. Please choose a term manually.</small>
                @endif
            </div>
            <div class="col-md-3">
                <label for="valid_from" class="form-label">Valid From</label>
                <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from', $timetable->valid_from?->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label for="valid_until" class="form-label">Valid Until</label>
                <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until', $timetable->valid_until?->format('Y-m-d')) }}" required>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Sessions</h4>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-session-row">Add Session</button>
        </div>

        <div id="sessions-container">
            @php
                $oldSessions = old('sessions', []);
                $sessionRows = count($oldSessions) > 0 ? $oldSessions : $timetable->sessions->map(function($session) {
                    $data = $session->toArray();
                    // Add course_ids from the relationship
                    $data['course_ids'] = $session->courses->pluck('id')->toArray();
                    return $data;
                })->toArray();
            @endphp

            @foreach($sessionRows as $index => $sessionData)
                <div class="card mb-3 session-row" data-index="{{ $index }}">
                    <div class="card-body">
                        <div class="row gy-3 align-items-end">
                            <input type="hidden" name="sessions[{{ $index }}][session_id]" value="{{ $sessionData['id'] ?? '' }}">
                            <div class="col-md-4">
                                <label class="form-label">Lesson</label>
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
                                <label class="form-label">Day</label>
                                <select name="sessions[{{ $index }}][day_of_week]" class="form-select" required>
                                    <option value="">-- Select Day --</option>
                                    @foreach([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old("sessions.{$index}.day_of_week", $sessionData['day_of_week'] ?? '') == (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="sessions[{{ $index }}][start_time]" class="form-control" value="{{ old("sessions.{$index}.start_time", $sessionData['start_time'] ?? '') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">End Time</label>
                                <input type="time" name="sessions[{{ $index }}][end_time]" class="form-control" value="{{ old("sessions.{$index}.end_time", $sessionData['end_time'] ?? '') }}" required>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-session-row">Remove</button>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Classroom</label>
                                <input type="text" name="sessions[{{ $index }}][classroom]" class="form-control" value="{{ old("sessions.{$index}.classroom", $sessionData['classroom'] ?? '') }}" placeholder="Room or hall">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Course(s)</label>
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
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sessions[{{ $index }}][mark_as_test]" value="1" id="mark_as_test_{{ $index }}" @checked(old("sessions.{$index}.mark_as_test", $sessionData['mark_as_test'] ?? false))>
                                    <label class="form-check-label" for="mark_as_test_{{ $index }}">Test / Exam Session</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea name="sessions[{{ $index }}][notes]" class="form-control" rows="2">{{ old("sessions.{$index}.notes", $sessionData['notes'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Update Timetable</button>
        <form action="{{ route('dos.timetable.publish', $timetable) }}" method="POST" class="d-inline ms-2">
            @csrf
            <button type="submit" class="btn btn-success" onclick="try { return confirm('Are you sure you want to publish this timetable?'); } catch(e) { console.error('Publish error:', e); return false; }">Publish Timetable</button>
        </form>
        @if($timetable->status === 'draft')
            <form action="{{ route('dos.timetable.destroy', $timetable) }}" method="POST" class="d-inline ms-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="try { return confirm('Delete this timetable? This action cannot be undone.'); } catch(e) { console.error('Delete error:', e); return false; }">Delete Timetable</button>
            </form>
        @else
            <div class="d-inline ms-2 align-middle text-muted">
                Published timetables cannot be deleted from this page.
            </div>
        @endif
    </form>
</div>

<template id="session-row-template">
    <div class="card mb-3 session-row" data-index="__INDEX__">
        <div class="card-body">
            <div class="row gy-3 align-items-end">
                <input type="hidden" name="sessions[__INDEX__][session_id]" value="">
                <div class="col-md-4">
                    <label class="form-label">Lesson</label>
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
                    <label class="form-label">Day</label>
                    <select name="sessions[__INDEX__][day_of_week]" class="form-select" required>
                        @foreach([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="sessions[__INDEX__][start_time]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Time</label>
                    <input type="time" name="sessions[__INDEX__][end_time]" class="form-control" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-session-row">Remove</button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Classroom</label>
                    <input type="text" name="sessions[__INDEX__][classroom]" class="form-control" placeholder="Room or hall">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course(s)</label>
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
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="sessions[__INDEX__][mark_as_test]" value="1" id="mark_as_test___INDEX__">
                        <label class="form-check-label" for="mark_as_test___INDEX__">Test / Exam Session</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="sessions[__INDEX__][notes]" class="form-control" rows="2"></textarea>
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

        function updateIndexes() {
            container.querySelectorAll('.session-row').forEach((row, index) => {
                row.dataset.index = index;
                row.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace(/sessions\[\d+\]/, `sessions[${index}]`);
                });
                row.querySelectorAll('[id]').forEach(element => {
                    const id = element.id.replace(/_\d+$/, `_${index}`);
                    element.id = id;
                });
            });
        }

        function addSessionRow() {
            const nextIndex = container.querySelectorAll('.session-row').length;
            const rowMarkup = template.replace(/__INDEX__/g, nextIndex);
            container.insertAdjacentHTML('beforeend', rowMarkup);
            bindRemoveButtons();
            initCourseSelects();
            bindLessonChangeHandlers();
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

        addButton.addEventListener('click', addSessionRow);
        bindRemoveButtons();
        initCourseSelects();
        bindLessonChangeHandlers();
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

    // dynamic branch -> courses behavior (reuse same endpoint)
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
                const prev = Array.from(sel.options).filter(o => o.selected).map(o => o.value);
                // rebuild options
                sel.innerHTML = '';
                courses.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.abbreviation || c.course_name;
                    if (c.teacher_name) {
                        opt.textContent += ' (' + c.teacher_name + ')';
                        opt.setAttribute('data-teacher-name', c.teacher_name);
                    }
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
                // Restore selections after Select2 initialization
                selects.forEach(sel => {
                    const prev = Array.from(sel.options).filter(o => o.selected).map(o => o.value);
                    if (prev.length > 0) {
                        jQuery(sel).val(prev).trigger('change');
                    }
                });
            }
        }

        if (branchSelect) {
            branchSelect.addEventListener('change', function () {
                refreshCourseSelects(this.value);
            });
            refreshCourseSelects(branchSelect.value);
        }

        initCourseSelects();
        
        // Initialize course selections for existing sessions
        document.querySelectorAll('.lesson-select').forEach(lessonSelect => {
            const row = lessonSelect.closest('.session-row');
            const courseSelect = row.querySelector('.course-select');
            if (courseSelect && courseSelect.selectedOptions.length > 0) {
                const selectedIds = Array.from(courseSelect.selectedOptions).map(opt => opt.value);
                if (window.jQuery && jQuery().select2) {
                    jQuery(courseSelect).val(selectedIds).trigger('change');
                }
            }
        });
    });
</script>
@endsection