@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-plus-circle"></i> Create New Question Bank</h2>
        <a href="{{ route('teacher.question-banks.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Question Banks
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Question Bank Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.question-banks.store') }}">
                        @csrf

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Question Bank Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            <div class="form-text">Give your question bank a descriptive name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <div class="form-text">Optional description of what this question bank contains.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-select" id="course_id" name="course_id">
                                    <option value="">Select Course (Optional)</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associate with a specific course.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select class="form-select" id="branch_id" name="branch_id">
                                    <option value="">Select Branch (Optional)</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associate with a specific branch.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lesson_id" class="form-label">Lesson</label>
                                <select class="form-select" id="lesson_id" name="lesson_id">
                                    <option value="">Select Lesson (Optional)</option>
                                </select>
                                <div class="form-text">Associate with a specific lesson.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="lesson_teachers" class="form-label">Teachers Assigned to This Lesson</label>
                            <div id="lesson_teachers" class="alert alert-info">
                                <small class="text-muted">Select a lesson to see assigned teachers</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="academic_year_id" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id">
                                    <option value="">Select Academic Year (Optional)</option>
                                    @foreach($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}" {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                            {{ $academicYear->year }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associate with an academic year.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="teacher_id" value="{{ auth('teacher')->user()->id }}">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Question Bank
                                </label>
                                <div class="form-text">Uncheck to make this question bank inactive.</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('teacher.question-banks.index') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Question Bank
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load lessons when course is selected
document.getElementById('course_id').addEventListener('change', function() {
    const courseId = this.value;
    const lessonSelect = document.getElementById('lesson_id');
    
    lessonSelect.innerHTML = '<option value="">Select Lesson (Optional)</option>';
    
    if (courseId) {
        fetch(`/api/courses/${courseId}/lessons`)
            .then(response => response.json())
            .then(data => {
                data.forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading lessons:', error);
            });
    }
    
    // Clear teachers display
    document.getElementById('lesson_teachers').innerHTML = '<small class="text-muted">Select a lesson to see assigned teachers</small>';
});

// Load teachers when lesson is selected
document.getElementById('lesson_id').addEventListener('change', function() {
    const lessonId = this.value;
    const teachersDiv = document.getElementById('lesson_teachers');
    
    if (lessonId) {
        fetch(`/api/lessons/${lessonId}/teachers`)
            .then(response => response.json())
            .then(data => {
                if (data.teachers && data.teachers.length > 0) {
                    let html = '<div class="d-flex flex-wrap gap-2">';
                    data.teachers.forEach(teacher => {
                        html += `<span class="badge bg-success">${teacher.teacher_name}</span>`;
                    });
                    html += '</div>';
                    teachersDiv.innerHTML = html;
                } else {
                    teachersDiv.innerHTML = '<small class="text-muted">No teachers assigned to this lesson</small>';
                }
            })
            .catch(error => {
                console.error('Error loading teachers:', error);
                teachersDiv.innerHTML = '<small class="text-muted">Error loading teachers</small>';
            });
    } else {
        teachersDiv.innerHTML = '<small class="text-muted">Select a lesson to see assigned teachers</small>';
    }
});
</script>
@endsection
