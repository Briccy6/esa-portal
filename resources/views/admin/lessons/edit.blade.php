@extends('admin.layout')

@section('title', 'Edit Lesson Assignment')

@section('content')
<div class="content">
    <div class="container-fluid px-4 py-4">

        {{-- ── PAGE HEADER ── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Lesson Assignment
                </h1>
                <p class="text-muted mb-0">Update lesson assignment details</p>
            </div>
            <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Lessons
            </a>
        </div>

        {{-- ── ALERTS ── --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ── FORM CARD ── --}}
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Lesson Assignment Details</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.lessons.update', $lesson->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Branch Selection --}}
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Branch
                                <span class="text-danger">*</span>
                            </label>
                            <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">-- Select a Branch --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $lesson->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Course Selection --}}
                        <div class="col-md-6 mb-3">
                            <label for="course_id" class="form-label">
                                <i class="fas fa-book me-1"></i>Course
                                <span class="text-danger">*</span>
                            </label>
                            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">-- Select a Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $lesson->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Lesson Selection --}}
                        <div class="col-md-6 mb-3">
                            <label for="lesson_id" class="form-label">
                                <i class="fas fa-graduation-cap me-1"></i>Lesson
                                <span class="text-danger">*</span>
                            </label>
                            <select name="lesson_id" id="lesson_id" class="form-select @error('lesson_id') is-invalid @enderror" required>
                                <option value="">-- Select a Lesson --</option>
                                @foreach ($lessons as $lesson_item)
                                    <option value="{{ $lesson_item->id }}" {{ old('lesson_id', $lesson->lesson_id) == $lesson_item->id ? 'selected' : '' }}>
                                        {{ $lesson_item->title }} - {{ $lesson_item->course->course_name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lesson_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Teacher Selection --}}
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>Teacher
                                <span class="text-danger">*</span>
                            </label>
                            <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">-- Select a Teacher --</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $lesson->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Information Box --}}
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Update the lesson assignment details below. Each combination of lesson, course, and branch must be unique.
                    </div>

                    {{-- Current Assignment Info --}}
                    <div class="card bg-light mt-3 mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Current Assignment</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Lesson</small>
                                    <p class="mb-0 fw-bold">{{ $lesson->lesson->title ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Course</small>
                                    <p class="mb-0 fw-bold">{{ $lesson->course->course_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Branch</small>
                                    <p class="mb-0 fw-bold">{{ $lesson->branch->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Teacher</small>
                                    <p class="mb-0 fw-bold">{{ $lesson->teacher->first_name ?? '' }} {{ $lesson->teacher->last_name ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Assignment
                        </button>
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Optional: Filter lessons based on selected course
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_id');
        
        if (courseId) {
            fetch(`/admin/lessons/course/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    const currentLessonId = "{{ old('lesson_id', $lesson->lesson_id) }}";
                    lessonSelect.innerHTML = '<option value="">-- Select a Lesson --</option>';
                    data.forEach(lesson => {
                        const option = document.createElement('option');
                        option.value = lesson.id;
                        option.textContent = lesson.title;
                        if (lesson.id == currentLessonId) {
                            option.selected = true;
                        }
                        lessonSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>
@endsection
