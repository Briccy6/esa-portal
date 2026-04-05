@extends('teacher.layout')

@section('styles')
<style>
.create-exam-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.create-exam-header h1 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.create-exam-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.exam-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.exam-card .card-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #3b82f6;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
    position: relative;
}

.section-title::before {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 50px;
    height: 2px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-text {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.error-message {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
    color: white;
}

.btn-outline-custom {
    border: 2px solid #6b7280;
    color: #6b7280;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-outline-custom:hover {
    background: #6b7280;
    color: white;
    border-color: #6b7280;
    transform: translateY(-1px);
}

.alert-custom {
    border-radius: 10px;
    border: none;
    padding: 1rem 1.5rem;
}

.alert-info {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border-left: 4px solid #3b82f6;
}

.question-bank-section {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.question-bank-section h6 {
    color: #3b82f6;
    font-weight: 600;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .create-exam-header {
        padding: 1.5rem 0;
    }
    
    .create-exam-header h1 {
        font-size: 1.5rem;
    }
    
    .btn-primary-custom, .btn-outline-custom {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="create-exam-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-plus-circle"></i> Create New Exam</h1>
                    <p class="mb-0">Design and configure a new examination for your students</p>
                </div>
                <a href="{{ route('teacher.exams.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Back to Exams
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="exam-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard-plus"></i> Exam Information</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('teacher.exams.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="section-title"><i class="bi bi-info-circle"></i> Basic Information</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="title" class="form-label">Exam Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="exam_type" class="form-label">Exam Type *</label>
                            <select name="exam_type" id="exam_type" class="form-select" required>
                                <option value="">Select Exam Type</option>
                                <option value="Quiz" {{ old('exam_type') == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="Test" {{ old('exam_type') == 'Test' ? 'selected' : '' }}>Test</option>
                                <option value="Assignment" {{ old('exam_type') == 'Assignment' ? 'selected' : '' }}>Assignment</option>
                                <option value="Final Exam" {{ old('exam_type') == 'Final Exam' ? 'selected' : '' }}>Final Exam</option>
                                <option value="Midterm" {{ old('exam_type') == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                            </select>
                            @error('exam_type')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Academic Details -->
                        <div class="col-12">
                            <h6 class="section-title"><i class="bi bi-mortarboard"></i> Academic Details</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="exam_weight" class="form-label">Exam Weight (%) *</label>
                            <input type="number" name="exam_weight" id="exam_weight" class="form-control" 
                                   value="{{ old('exam_weight', 10) }}" min="1" max="100" required>
                            @error('exam_weight')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="total_marks" class="form-label">Total Marks *</label>
                            <input type="number" name="total_marks" id="total_marks" class="form-control" 
                                   value="{{ old('total_marks', 100) }}" min="1" required>
                            @error('total_marks')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="class_date" class="form-label">Exam Date *</label>
                            <input type="date" name="class_date" id="class_date" class="form-control" 
                                   value="{{ old('class_date') }}" required>
                            @error('class_date')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="exam_password" class="form-label">Exam Password</label>
                            <input type="text" name="exam_password" id="exam_password" class="form-control" 
                                   value="{{ old('exam_password', $examPassword) }}" placeholder="Auto-generated">
                            <div class="form-text">Leave blank to auto-generate</div>
                            @error('exam_password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assignments -->
                        <div class="col-12">
                            <h6 class="section-title"><i class="bi bi-people"></i> Assignments</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="branch_id" class="form-label">Branch *</label>
                            <select name="branch_id" id="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="academic_year_id" class="form-label">Academic Year *</label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="term_id" class="form-label">Term *</label>
                            <select name="term_id" id="term_id" class="form-select" required>
                                <option value="">Select Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Courses and Lessons -->
                        <div class="col-md-6">
                            <label for="course_ids" class="form-label">Courses *</label>
                            <select name="course_ids[]" id="course_ids" class="form-select" multiple required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ in_array($course->id, old('course_ids', [])) ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple courses</div>
                            @error('course_ids')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lesson_ids" class="form-label">Lessons *</label>
                            <select name="lesson_ids[]" id="lesson_ids" class="form-select" multiple required>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ in_array($lesson->id, old('lesson_ids', [])) ? 'selected' : '' }}>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple lessons</div>
                            @error('lesson_ids')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Question Banks -->
                        @if($questionBanks->count() > 0)
                            <div class="col-12">
                                <h6 class="section-title"><i class="bi bi-database"></i> Question Banks</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="question_bank_ids" class="form-label">Import Questions from Banks</label>
                                <select name="question_bank_ids[]" id="question_bank_ids" class="form-select" multiple>
                                    @foreach($questionBanks as $bank)
                                        <option value="{{ $bank->id }}" {{ in_array($bank->id, old('question_bank_ids', [])) ? 'selected' : '' }}>
                                            {{ $bank->name }} ({{ $bank->questions->count() }} questions)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl/Cmd to select multiple banks</div>
                            </div>

                            <div class="col-md-6">
                                <label for="questions_per_bank" class="form-label">Questions per Bank</label>
                                <input type="number" name="questions_per_bank" id="questions_per_bank" class="form-control" 
                                       value="{{ old('questions_per_bank', 5) }}" min="1" max="50">
                                <div class="form-text">Number of questions to import from each selected bank</div>
                            </div>
                        @else
                            <div class="col-12">
                                <div class="question-bank-section">
                                    <h6><i class="bi bi-info-circle"></i> Question Banks</h6>
                                    <div class="alert alert-custom alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        No question banks available. You can add questions manually after creating the exam.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Additional Information -->
                        <div class="col-12">
                            <h6 class="section-title"><i class="bi bi-journal-text"></i> Additional Information</h6>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden teacher_id field -->
                        <input type="hidden" name="teacher_id" value="{{ Auth::guard('teacher')->user()->id }}">

                        <!-- Submit Buttons -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-custom">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary-custom">
                                        <i class="bi bi-check-circle me-2"></i>Create Exam
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch_id');
    const courseSelect = document.getElementById('course_ids');
    const lessonSelect = document.getElementById('lesson_ids');
    const teacherIdInput = document.querySelector('input[name="teacher_id"]');
    
    // Store API data
    let lessonsByBranchCourse = {}; // courseId => [lessons]
    let lessonTeacherMap = {}; // lessonId => {teacher_id, teacher_name}
    let coursesList = []; // All available courses
    
    branchSelect.addEventListener('change', function() {
        const branchId = this.value;
        
        // Reset course and lesson selects
        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
        lessonSelect.innerHTML = '<option value="">-- Select Lesson --</option>';
        teacherIdInput.value = '';
        
        if (!branchId) {
            return;
        }
        
        // Fetch lessons, courses, and teacher mappings for the selected branch
        fetch(`{{ route('teacher.exams.api.lessons-courses', '') }}/${branchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store the API data for later use
                    lessonsByBranchCourse = data.lessonsByCourse || {};
                    lessonTeacherMap = data.lessonTeachers || {};
                    coursesList = data.courses || [];
                    
                    // Populate courses dropdown
                    if (coursesList.length > 0) {
                        coursesList.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.text = course.name;
                            courseSelect.appendChild(option);
                        });
                    } else {
                        courseSelect.innerHTML = '<option value="">-- No courses available for this branch --</option>';
                    }
                } else {
                    console.error('Error fetching data:', data.message);
                    alert('Error loading courses. Please try again.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error loading courses. Please try again.');
            });
    });
    
    // Populate lessons when course is selected
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        
        // Reset lesson select and teacher
        lessonSelect.innerHTML = '<option value="">-- Select Lesson --</option>';
        teacherIdInput.value = '';
        
        if (!courseId) {
            return;
        }
        
        // Populate lessons for selected course
        const lessonsForCourse = lessonsByBranchCourse[courseId] || [];
        
        if (lessonsForCourse.length > 0) {
            lessonsForCourse.forEach(lesson => {
                const option = document.createElement('option');
                option.value = lesson.id;
                option.text = lesson.title;
                lessonSelect.appendChild(option);
            });
        } else {
            lessonSelect.innerHTML = '<option value="">-- No lessons available for this course --</option>';
        }
    });
    
    // Update teacher when lesson is selected
    lessonSelect.addEventListener('change', function() {
        const selectedLessonId = this.value;
        teacherIdInput.value = '';
        
        if (selectedLessonId && lessonTeacherMap[selectedLessonId]) {
            const teacher = lessonTeacherMap[selectedLessonId];
            
            // Auto-populate the teacher field
            teacherIdInput.value = teacher.teacher_id;
            
            // If there's a select field for teacher, update it
            const teacherSelect = document.getElementById('teacher_id');
            if (teacherSelect) {
                teacherSelect.value = teacher.teacher_id;
            }
            
            console.log('Teacher auto-selected:', teacher.teacher_name);
        }
    });
});
</script>
@endsection
