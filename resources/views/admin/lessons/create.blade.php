@extends('admin.layout')

@section('title', 'Create New Lesson')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Lesson</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lessons.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lesson_title" class="form-label">Lesson Title *</label>
                                    <input type="text" class="form-control @error('lesson_title') is-invalid @enderror" 
                                           id="lesson_title" name="lesson_title" value="{{ old('lesson_title') }}" required>
                                    @error('lesson_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course *</label>
                                    <select class="form-select @error('course_id') is-invalid @enderror" 
                                            id="course_id" name="course_id" required>
                                        <option value="">Select a course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="teacher_id" class="form-label">Teacher</label>
                                    <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                            id="teacher_id" name="teacher_id">
                                        <option value="">Select a teacher (optional)</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->teacher_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Teachers will be loaded based on selected course</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lesson_weight" class="form-label">Lesson Weight (%) *</label>
                                    <input type="number" class="form-control @error('lesson_weight') is-invalid @enderror" 
                                           id="lesson_weight" name="lesson_weight" value="{{ old('lesson_weight', 100) }}" 
                                           min="1" max="100" required>
                                    @error('lesson_weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_test_marks" class="form-label">Max Test Marks</label>
                                    <input type="number" class="form-control @error('max_test_marks') is-invalid @enderror" 
                                           id="max_test_marks" name="max_test_marks" value="{{ old('max_test_marks', 100) }}" 
                                           min="0">
                                    @error('max_test_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_exam_marks" class="form-label">Max Exam Marks</label>
                                    <input type="number" class="form-control @error('max_exam_marks') is-invalid @enderror" 
                                           id="max_exam_marks" name="max_exam_marks" value="{{ old('max_exam_marks', 100) }}" 
                                           min="0">
                                    @error('max_exam_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year *</label>
                                    <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                            id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select academic year</option>
                                        @if($ongoingAcademicYear)
                                            <option value="{{ $ongoingAcademicYear->id }}" selected>
                                                {{ $ongoingAcademicYear->name }} (Ongoing)
                                            </option>
                                        @endif
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Terms *</label>
                            <div class="row">
                                @foreach($terms as $term)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input @error('terms.' . $loop->index) is-invalid @enderror" 
                                                   type="checkbox" name="terms[]" value="{{ $term->id }}" 
                                                   id="term_{{ $term->id }}" {{ in_array($term->id, old('terms', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="term_{{ $term->id }}">
                                                {{ $term->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select at least 1 term (maximum 2)</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Lessons
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Lesson
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('course_id').addEventListener('change', function() {
    const courseId = this.value;
    const teacherSelect = document.getElementById('teacher_id');
    
    if (courseId) {
        fetch(`/admin/teachers/by-course/${courseId}`)
            .then(response => response.json())
            .then(data => {
                teacherSelect.innerHTML = '<option value="">Select a teacher (optional)</option>';
                data.teachers.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = teacher.teacher_name;
                    teacherSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                teacherSelect.innerHTML = '<option value="">Select a teacher (optional)</option>';
            });
    } else {
        teacherSelect.innerHTML = '<option value="">Select a teacher (optional)</option>';
    }
});
</script>
@endpush
