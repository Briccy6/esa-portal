@extends('staff.partials.sidebar_dos')

@section('content')
<style>
    .edit-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .edit-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .edit-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .edit-body { padding: 40px; }
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
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
    }
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 13px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
    .current-info {
        background: #e8f4fd;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 0 8px 8px 0;
    }
</style>

<div class="edit-container">
    <div class="edit-card">
        <div class="edit-header">
            <h2>Edit Lesson</h2>
            <p class="mb-0">Modify lesson information for: {{ $lesson->title }}</p>
        </div>
        
        <div class="edit-body">
            <!-- Current Lesson Information -->
            <div class="current-info">
                <h5 class="mb-2">Current Lesson Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Course:</strong> {{ $lesson->course->course_name }}</p>
                        <p class="mb-1"><strong>Teacher:</strong> {{ $lesson->teacher ? $lesson->teacher->teacher_name : 'Not assigned' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Test Marks:</strong> {{ $lesson->max_test_marks }}</p>
                        <p class="mb-0"><strong>Exam Marks:</strong> {{ $lesson->max_exam_marks }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('staff.lessons.update', $lesson->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="lesson_title" class="form-label required">Lesson Title</label>
                            <input type="text" class="form-control" id="lesson_title" name="lesson_title" 
                                   value="{{ $lesson->title }}" required>
                            @error('lesson_title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="lesson_weight" class="form-label required">Lesson Weight</label>
                            <input type="number" class="form-control" id="lesson_weight" name="lesson_weight" 
                                   value="{{ $lesson->lesson_weight }}" min="1" max="100" required>
                            @error('lesson_weight')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Course Selection -->
                <div class="mb-3">
                    <label for="course_id" class="form-label required">Course</label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $lesson->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Teacher Selection -->
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Teacher (Optional)</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">Select Teacher</option>
                        @if($lesson->teacher)
                            <option value="{{ $lesson->teacher_id }}" selected>
                                {{ $lesson->teacher->teacher_name }}
                            </option>
                        @endif
                    </select>
                    @error('teacher_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div class="mb-3">
                    <label for="academic_year_id" class="form-label required">Academic Year</label>
                    <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $lesson->academic_year_id == $year->id ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Marks Configuration -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_test_marks" class="form-label required">Max Test Marks</label>
                            <input type="number" class="form-control" id="max_test_marks" name="max_test_marks" 
                                   value="{{ $lesson->max_test_marks }}" min="1" required>
                            @error('max_test_marks')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_exam_marks" class="form-label required">Max Exam Marks</label>
                            <input type="number" class="form-control" id="max_exam_marks" name="max_exam_marks" 
                                   value="{{ $lesson->max_exam_marks }}" min="1" required>
                            @error('max_exam_marks')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms Selection -->
                <div class="mb-4">
                    <h6 class="section-title">Terms</h6>
                    <div class="row">
                        @foreach($terms as $term)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms[]" 
                                           value="{{ $term->id }}" id="term_{{ $term->id }}"
                                           @if($lesson->terms->contains($term->id)) checked @endif>
                                    <label class="form-check-label" for="term_{{ $term->id }}">
                                        {{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->year . ')' : '' }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('terms')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lesson Units Management -->
                <div class="mb-4">
                    <h6 class="section-title">Lesson Units</h6>
                    <div id="units-container">
                        @if($lesson->units && $lesson->units->count() > 0)
                            @foreach($lesson->units as $index => $unit)
                                <div class="unit-item" data-unit-id="{{ $unit->id }}">
                                    <button type="button" class="btn-remove-unit" onclick="removeUnit(this)">×</button>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-label required">Unit Name</label>
                                            <input type="text" class="form-control" name="units[{{ $index }}][name]" 
                                                   value="{{ $unit->unit_name }}" placeholder="Enter unit name" required>
                                            <input type="hidden" name="units[{{ $index }}][id]" value="{{ $unit->id }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required">Hours</label>
                                            <input type="number" class="form-control" name="units[{{ $index }}][hours]" 
                                                   value="{{ $unit->hours }}" placeholder="Hours" min="1" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Term</label>
                                            <select class="form-select" name="units[{{ $index }}][term_id]">
                                                @foreach($terms as $term)
                                                    <option value="{{ $term->id }}" {{ $unit->term_id == $term->id ? 'selected' : '' }}>
                                                        {{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->year . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Course Level</label>
                                            <select class="form-select" name="units[{{ $index }}][course_level]">
                                                <option value="beginner" {{ $unit->course_level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="intermediate" {{ $unit->course_level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                <option value="advanced" {{ $unit->course_level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-3">No units have been added to this lesson yet.</p>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary mt-3" onclick="addUnit()">
                        <i class="fas fa-plus"></i> Add New Unit
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('staff.lessons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Lessons
                        </a>
                        <a href="{{ route('staff.lesson-units.create', $lesson->id) }}" class="btn btn-outline-success ms-2">
                            <i class="bi bi-diagram-3"></i> Manage Units
                        </a>
                    </div>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Update Lesson
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let unitCount = {{ $lesson->units ? $lesson->units->count() : 0 }};

function addUnit() {
    const container = document.getElementById('units-container');
    const unitHtml = `
        <div class="unit-item">
            <button type="button" class="btn-remove-unit" onclick="removeUnit(this)">×</button>
            <div class="row">
                <div class="col-md-8">
                    <label class="form-label required">Unit Name</label>
                    <input type="text" class="form-control" name="units[${unitCount}][name]" placeholder="Enter unit name" required>
                    <input type="hidden" name="units[${unitCount}][id]" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Hours</label>
                    <input type="number" class="form-control" name="units[${unitCount}][hours]" placeholder="Hours" min="1" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Term</label>
                    <select class="form-select" name="units[${unitCount}][term_id]">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->year . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Course Level</label>
                    <select class="form-select" name="units[${unitCount}][course_level]">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', unitHtml);
    unitCount++;
}

function removeUnit(button) {
    const unitItem = button.closest('.unit-item');
    const unitId = unitItem.dataset.unitId;
    
    if (unitId) {
        // This is an existing unit, mark for deletion
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = `delete_units[]`;
        deleteInput.value = unitId;
        unitItem.appendChild(deleteInput);
        unitItem.style.display = 'none';
    } else {
        // This is a new unit, just remove it
        unitItem.remove();
    }
}

// Load teachers based on selected course
document.getElementById('course_id').addEventListener('change', function() {
    const courseId = this.value;
    const teacherSelect = document.getElementById('teacher_id');
    
    // Clear current options except the first one
    teacherSelect.innerHTML = '<option value="">Select Teacher</option>';
    
    if (courseId) {
        // Fetch teachers for this course via AJAX
        fetch(`/staff/lessons/get-teachers/${courseId}`)
            .then(response => response.json())
            .then(teachers => {
                teachers.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = teacher.teacher_name;
                    teacherSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading teachers:', error));
    }
});

// Trigger change event on page load to populate teachers
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('course_id').dispatchEvent(new Event('change'));
});
</script>
@endsection
