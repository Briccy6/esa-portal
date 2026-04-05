@extends('teacher.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Exam</h1>
        <div>
            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Exam
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list me-2"></i>All Exams
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Exam Information</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('teacher.exams.update', $exam) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <h6 class="text-primary mb-3">Basic Information</h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="title" class="form-label">Exam Title *</label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="{{ old('title', $exam->title) }}" required>
                        @error('title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="exam_type" class="form-label">Exam Type *</label>
                        <select name="exam_type" id="exam_type" class="form-select" required>
                            <option value="">Select Exam Type</option>
                            <option value="Quiz" {{ old('exam_type', $exam->exam_type) == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                            <option value="Test" {{ old('exam_type', $exam->exam_type) == 'Test' ? 'selected' : '' }}>Test</option>
                            <option value="Assignment" {{ old('exam_type', $exam->exam_type) == 'Assignment' ? 'selected' : '' }}>Assignment</option>
                            <option value="Final Exam" {{ old('exam_type', $exam->exam_type) == 'Final Exam' ? 'selected' : '' }}>Final Exam</option>
                            <option value="Midterm" {{ old('exam_type', $exam->exam_type) == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                        </select>
                        @error('exam_type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $exam->description) }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Academic Details -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3">Academic Details</h6>
                    </div>

                    <div class="col-md-3">
                        <label for="exam_weight" class="form-label">Exam Weight (%) *</label>
                        <input type="number" name="exam_weight" id="exam_weight" class="form-control" 
                               value="{{ old('exam_weight', $exam->exam_weight) }}" min="1" max="100" required>
                        @error('exam_weight')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="total_marks" class="form-label">Total Marks *</label>
                        <input type="number" name="total_marks" id="total_marks" class="form-control" 
                               value="{{ old('total_marks', $exam->total_marks) }}" min="1" required>
                        @error('total_marks')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="class_date" class="form-label">Exam Date *</label>
                        <input type="date" name="class_date" id="class_date" class="form-control" 
                               value="{{ old('class_date', $exam->class_date->format('Y-m-d')) }}" required>
                        @error('class_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="exam_password" class="form-label">Exam Password</label>
                        <input type="text" name="exam_password" id="exam_password" class="form-control" 
                               value="{{ old('exam_password', $exam->exam_password) }}" placeholder="Leave blank to keep current">
                        <small class="form-text text-muted">Leave blank to keep current password</small>
                        @error('exam_password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Assignments -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3">Assignments</h6>
                    </div>

                    <div class="col-md-4">
                        <label for="branch_id" class="form-label">Branch *</label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $exam->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="academic_year_id" class="form-label">Academic Year *</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $exam->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="term_id" class="form-label">Term *</label>
                        <select name="term_id" id="term_id" class="form-select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('term_id', $exam->term_id) == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('term_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Courses and Lessons -->
                    <div class="col-md-6">
                        <label for="course_ids" class="form-label">Courses *</label>
                        <select name="course_ids[]" id="course_ids" class="form-select" multiple required>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ in_array($course->id, old('course_ids', $exam->courses->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple courses</small>
                        @error('course_ids')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="lesson_ids" class="form-label">Lessons *</label>
                        <select name="lesson_ids[]" id="lesson_ids" class="form-select" multiple required>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ in_array($lesson->id, old('lesson_ids', $exam->lessons->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple lessons</small>
                        @error('lesson_ids')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3">Additional Information</h6>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $exam->notes) }}</textarea>
                        @error('notes')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hidden teacher_id field -->
                    <input type="hidden" name="teacher_id" value="{{ Auth::guard('teacher')->user()->id }}">

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Exam
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch_id');
    const courseSelect = document.getElementById('course_ids');
    const lessonSelect = document.getElementById('lesson_ids');
    const teacherIdInput = document.querySelector('input[name="teacher_id"]');
    
    const loadLessonsByCourse = function(branchId) {
        if (!branchId) {
            courseSelect.innerHTML = '';
            lessonSelect.innerHTML = '';
            return;
        }
        
        // Fetch lessons and courses for the selected branch
        fetch(`{{ route('teacher.exams.api.lessons-courses', '') }}/${branchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store lesson-teacher mapping for auto-population
                    const lessonTeacherMap = data.lessonTeachers || {};
                    
                    // Clear current options
                    courseSelect.innerHTML = '';
                    lessonSelect.innerHTML = '';
                    
                    // Add filtered courses
                    if (data.courses.length > 0) {
                        data.courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.text = course.name;
                            courseSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.text = 'No courses available for this branch';
                        option.disabled = true;
                        courseSelect.appendChild(option);
                    }
                    
                    // Add filtered lessons
                    if (data.lessons.length > 0) {
                        data.lessons.forEach(lesson => {
                            const option = document.createElement('option');
                            option.value = lesson.id;
                            option.text = lesson.title;
                            lessonSelect.appendChild(option);
                        });
                        
                        // Add listener for lesson selection to auto-populate teacher
                        lessonSelect.addEventListener('change', function() {
                            const selectedLessonIds = Array.from(this.selectedOptions).map(opt => opt.value);
                            if (selectedLessonIds.length > 0) {
                                const firstLessonId = selectedLessonIds[0];
                                if (lessonTeacherMap[firstLessonId]) {
                                    teacherIdInput.value = lessonTeacherMap[firstLessonId].teacher_id;
                                    console.log('Teacher auto-populated:', lessonTeacherMap[firstLessonId].teacher_name);
                                }
                            }
                        }, { once: false });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.text = 'No lessons available for this branch';
                        option.disabled = true;
                        lessonSelect.appendChild(option);
                    }
                } else {
                    console.error('Error fetching data:', data.message);
                    alert('Error loading courses and lessons. Please try again.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error loading courses and lessons. Please try again.');
            });
    };
    
    branchSelect.addEventListener('change', function() {
        loadLessonsByCourse(this.value);
    });
});
</script>
@endsection
