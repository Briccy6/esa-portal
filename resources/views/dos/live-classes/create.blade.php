@extends('dos.layout')

@section('title', 'Schedule Live Class')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-broadcast me-2"></i>
            Schedule Live Class
        </h2>
        <a href="{{ route('dos.live-classes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Live Classes
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('dos.live-classes.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label">Class Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="{{ old('title') }}">
                        @error('title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="teacher_id" class="form-label">Teacher *</label>
                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                            <option value="">Select Teacher</option>
                            @if(isset($teachers))
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->teacher_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('teacher_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">Branch *</label>
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value="">Select Branch</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('branch_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">Course *</label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('course_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="lesson_id" class="form-label">Lesson</label>
                        <select class="form-select" id="lesson_id" name="lesson_id">
                            <option value="">Select Lesson (Optional)</option>
                            @if(isset($lessons))
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('lesson_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Start Time *</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required
                               value="{{ old('start_time') }}">
                        @error('start_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="duration" class="form-label">Duration (minutes) *</label>
                        <input type="number" class="form-control" id="duration" name="duration" min="15" max="480" value="60"
                               value="{{ old('duration') ?? 60 }}">
                        @error('duration')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="timezone" class="form-label">Timezone</label>
                        <select class="form-select" id="timezone" name="timezone">
                            <option value="Africa/Kigali" {{ old('timezone') == 'Africa/Kigali' ? 'selected' : '' }}>Africa/Kigali</option>
                            <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                        @error('timezone')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="max_participants" class="form-label">Max Participants</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" min="1" max="1000" value="50"
                               value="{{ old('max_participants') ?? 50 }}">
                        @error('max_participants')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="meeting_link" class="form-label">Meeting Link (Optional)</label>
                        <input type="url" class="form-control" id="meeting_link" name="meeting_link" 
                               placeholder="https://meet.google.com/..." value="{{ old('meeting_link') }}">
                        @error('meeting_link')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1"
                                   {{ old('is_recurring') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_recurring">
                                Recurring Class
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-12" id="recurring_pattern_div" style="display: none;">
                        <label for="recurring_pattern" class="form-label">Recurring Pattern</label>
                        <input type="text" class="form-control" id="recurring_pattern" name="recurring_pattern" 
                               placeholder="e.g., Weekly on Monday" value="{{ old('recurring_pattern') }}">
                        @error('recurring_pattern')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('dos.live-classes.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Schedule Live Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('is_recurring').addEventListener('change', function() {
    const recurringDiv = document.getElementById('recurring_pattern_div');
    recurringDiv.style.display = this.checked ? 'block' : 'none';
});

// Set minimum datetime to current time
const now = new Date();
const offset = now.getTimezoneOffset() * 60000;
const localISOTime = new Date(now - offset).toISOString().slice(0, 16);
document.getElementById('start_time').min = localISOTime;
</script>
@endsection
