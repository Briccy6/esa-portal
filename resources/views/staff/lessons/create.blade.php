@extends('staff.partials.sidebar_dos')

@section('content')
<style>
    .create-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .create-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .create-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .create-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .create-body { padding: 40px; }
    .section-title {
        color: #667eea;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 25px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #667eea;
    }
    .form-label { font-weight: 600; color: #333; }
    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 14px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .required::after { content: ' *'; color: #dc3545; }
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 13px 40px;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        color: white;
    }
</style>

<div class="create-container">
    <div class="container">
        <div class="create-card">
            <div class="create-header">
                <h2><i class="fas fa-plus-circle me-2"></i>Create New Lesson</h2>
                <p class="mb-0 mt-1 opacity-75">Add a new lesson to the system</p>
            </div>

            <div class="create-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('staff.lessons.store') }}" method="POST">
                    @csrf

                    {{-- ── Basic Information ── --}}
                    <h5 class="section-title"><i class="fas fa-book me-2"></i>Basic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label required">Lesson Title</label>
                            <input type="text" name="lesson_title" class="form-control"
                                   value="{{ old('lesson_title') }}" required placeholder="Enter lesson title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Course</label>
                            <select name="course_id" class="form-select" id="courseSelect" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }} ({{ $course->abbreviation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select" id="teacherSelect">
                                <option value="">Select Teacher (Optional)</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->teacher_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ── Academic Settings ── --}}
                    <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Academic Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @if($ongoingAcademicYear)
                                    <option value="{{ $ongoingAcademicYear->id }}" selected>
                                        {{ $ongoingAcademicYear->year }} ({{ ucfirst($ongoingAcademicYear->status) }})
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Terms</label>
                            <div class="mt-2">
                                @foreach($terms as $term)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="terms[]" value="{{ $term->id }}" 
                                               id="term_{{ $term->id }}" {{ in_array($term->id, old('terms', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="term_{{ $term->id }}">
                                            {{ $term->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── Grading Settings ── --}}
                    <h5 class="section-title"><i class="fas fa-chart-line me-2"></i>Grading Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Maximum Test Marks</label>
                            <input type="number" name="max_test_marks" class="form-control"
                                   value="{{ old('max_test_marks') }}" min="0" placeholder="e.g. 30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Maximum Exam Marks</label>
                            <input type="number" name="max_exam_marks" class="form-control"
                                   value="{{ old('max_exam_marks') }}" min="0" placeholder="e.g. 70">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Lesson Weight (%)</label>
                            <input type="number" name="lesson_weight" class="form-control"
                                   value="{{ old('lesson_weight', 100) }}" min="1" max="100" required placeholder="e.g. 100">
                        </div>
                    </div>

                    {{-- ── Action Buttons ── --}}
                    <div class="text-center mt-5">
                        <a href="{{ route('staff.lessons.index') }}" class="btn btn-secondary me-3 px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-plus-circle me-2"></i>Create Lesson
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const courseSelect = document.getElementById('courseSelect');
    const teacherSelect = document.getElementById('teacherSelect');

    // Load teachers when course is selected
    courseSelect.addEventListener('change', function () {
        const courseId = this.value;
        if (courseId) {
            fetch(`/staff/lessons/get-teachers/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                    data.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.teacher_name;
                        teacherSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading teachers:', error);
                });
        } else {
            teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
        }
    });

    // Loading state on submit
    document.querySelector('form').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating…';
    });
});
</script>
@endsection
