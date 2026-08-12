@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container mt-4">
    <h2>Create Weekly Timetable</h2>

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

    <form action="{{ route('dos.timetable.store') }}" method="POST">
        @csrf

        <div class="row gy-3">
            <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="E.g. Term 1 Weekly Timetable" required>
            </div>
            <div class="col-md-6">
                <label for="branch_id" class="form-label">Branch</label>
                <select name="branch_id" id="branch_id" class="form-select" required>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $branchId) == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="valid_from" class="form-label">Valid From</label>
                <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from') }}" required>
            </div>
            <div class="col-md-3">
                <label for="valid_until" class="form-label">Valid Until</label>
                <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until') }}" required>
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
                $sessionRows = count($oldSessions) > 0 ? $oldSessions : [null];
            @endphp

            @foreach($sessionRows as $index => $sessionData)
                <div class="card mb-3 session-row" data-index="{{ $index }}">
                    <div class="card-body">
                        <div class="row gy-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Lesson</label>
                                <select name="sessions[{{ $index }}][lesson_id]" class="form-select" required>
                                    <option value="">Select lesson</option>
                                    @foreach($lessonAssignments as $assignment)
                                        <option value="{{ $assignment->lesson_id }}" @selected(old("sessions.{$index}.lesson_id", $sessionData['lesson_id'] ?? '') == $assignment->lesson_id)>
                                            {{ $assignment->lesson->title ?? 'Untitled lesson' }}
                                            ({{ $assignment->course->course_name ?? 'Course' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Day</label>
                                <select name="sessions[{{ $index }}][day_of_week]" class="form-select" required>
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
                                <select name="sessions[{{ $index }}][course_ids][]" class="form-select" multiple>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" @selected(in_array($course->id, old("sessions.{$index}.course_ids", $sessionData['course_ids'] ?? [])))>{{ $course->course_name }}</option>
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

        <button type="submit" class="btn btn-primary">Save Draft</button>
    </form>
</div>

<template id="session-row-template">
    <div class="card mb-3 session-row" data-index="__INDEX__">
        <div class="card-body">
            <div class="row gy-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Lesson</label>
                    <select name="sessions[__INDEX__][lesson_id]" class="form-select" required>
                        <option value="">Select lesson</option>
                        @foreach($lessonAssignments as $assignment)
                            <option value="{{ $assignment->lesson_id }}">{{ $assignment->lesson->title ?? 'Untitled lesson' }} ({{ $assignment->course->course_name ?? 'Course' }})</option>
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
                    <select name="sessions[__INDEX__][course_ids][]" class="form-select" multiple>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
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

        addButton.addEventListener('click', addSessionRow);
        bindRemoveButtons();
    });
</script>
@endsection