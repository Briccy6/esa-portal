@extends('admin.layout')

@section('title', 'Add Lesson Assignment')

@section('content')
<div class="content">
    <div class="container-fluid px-4 py-4">

        {{-- ── PAGE HEADER ── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Add Lesson Assignment
                </h1>
                <p class="text-muted mb-0">Link a lesson to a course, branch, and teacher</p>
            </div>
            <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Lessons
            </a>
        </div>

        {{-- ── INFORMATION BOX ── --}}
        <div class="alert alert-info mb-4" role="alert">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="alert-heading mb-2"><i class="fas fa-lightbulb me-2"></i>About Lesson Assignments</h6>
                    <ul class="mb-0 small">
                        <li><strong>Same lesson, multiple courses:</strong> E.g., Mathematics can be taught in Science and Commerce courses</li>
                        <li><strong>Same lesson, multiple branches:</strong> E.g., Mathematics taught at Kigali and Goma branches</li>
                        <li><strong>Different teachers:</strong> The same lesson can have different teachers per branch/course combination</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="alert-heading mb-2"><i class="fas fa-check-circle me-2"></i>Example</h6>
                    <ul class="mb-0 small">
                        <li>Mathematics → Science Course → Kigali Branch → Teacher A</li>
                        <li>Mathematics → Science Course → Goma Branch → Teacher B</li>
                        <li>Mathematics → Commerce Course → Kigali Branch → Teacher C</li>
                    </ul>
                </div>
            </div>
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
                <form action="{{ route('admin.lessons.store') }}" method="POST">
                    @csrf

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
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
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
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
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
                                @foreach ($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->title }} - {{ $lesson->course->course_name ?? 'N/A' }}
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
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
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
                    <div class="alert alert-info mt-3 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How it works:</strong> Select a lesson, and optionally assign it to specific course(s) and branch(es) with a designated teacher. 
                        The same lesson can be assigned multiple times with different teachers for different courses or branches.
                    </div>

                    {{-- Form Actions --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Assignment
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
            // You can add AJAX call here to filter lessons by course
            fetch(`/admin/lessons/course/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    lessonSelect.innerHTML = '<option value="">-- Select a Lesson --</option>';
                    data.forEach(lesson => {
                        const option = document.createElement('option');
                        option.value = lesson.id;
                        option.textContent = lesson.title;
                        lessonSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>
@endsection
