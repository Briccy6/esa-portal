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
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
    }
    .unit-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
    }
    .btn-remove-unit {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
    }
    .lesson-info {
        background: #e8f4fd;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 30px;
        border-radius: 0 8px 8px 0;
    }
</style>

<div class="create-container">
    <div class="create-card">
        <div class="create-header">
            <h2>Create Lesson Details</h2>
            <p class="mb-0">Add units and details for: {{ $lesson->title }}</p>
        </div>
        
        <div class="create-body">
            <!-- Lesson Information -->
            <div class="lesson-info">
                <h5 class="mb-2">Lesson Information</h5>
                <p class="mb-1"><strong>Course:</strong> {{ $lesson->course->name }}</p>
                <p class="mb-1"><strong>Lesson:</strong> {{ $lesson->title }}</p>
                <p class="mb-0"><strong>Max Test Marks:</strong> {{ $lesson->max_test_marks }} | <strong>Max Exam Marks:</strong> {{ $lesson->max_exam_marks }}</p>
            </div>

            <form action="{{ route('staff.lesson.details.store', $lesson->id) }}" method="POST">
                @csrf
                
                <!-- Terms Selection -->
                <div class="mb-4">
                    <label class="form-label required">Select Terms</label>
                    <div class="row">
                        @foreach($terms as $term)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms[]" 
                                           value="{{ $term->id }}" id="term_{{ $term->id }}"
                                           @if(request()->has('terms') && in_array($term->id, request()->input('terms'))) checked @endif>
                                    <label class="form-check-label" for="term_{{ $term->id }}">
                                        {{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->name . ')' : '' }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('terms')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Course Level -->
                <div class="mb-4">
                    <label for="course_level" class="form-label required">Course Level</label>
                    <select class="form-select" id="course_level" name="course_level" required>
                        <option value="">Select Level</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                    @error('course_level')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Units Section -->
                <div class="mb-4">
                    <h6 class="section-title">Lesson Units</h6>
                    <div id="units-container">
                        <div class="unit-item">
                            <button type="button" class="btn-remove-unit" onclick="removeUnit(this)">×</button>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label required">Unit Name</label>
                                    <input type="text" class="form-control" name="units[0][name]" placeholder="Enter unit name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Hours</label>
                                    <input type="number" class="form-control" name="units[0][hours]" placeholder="Hours" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary mt-3" onclick="addUnit()">
                        <i class="fas fa-plus"></i> Add Another Unit
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('staff.lessons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Lessons
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Create Lesson Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let unitCount = 1;

function addUnit() {
    const container = document.getElementById('units-container');
    const unitHtml = `
        <div class="unit-item">
            <button type="button" class="btn-remove-unit" onclick="removeUnit(this)">×</button>
            <div class="row">
                <div class="col-md-8">
                    <label class="form-label required">Unit Name</label>
                    <input type="text" class="form-control" name="units[${unitCount}][name]" placeholder="Enter unit name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Hours</label>
                    <input type="number" class="form-control" name="units[${unitCount}][hours]" placeholder="Hours" min="1" required>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', unitHtml);
    unitCount++;
}

function removeUnit(button) {
    const units = document.querySelectorAll('.unit-item');
    if (units.length > 1) {
        button.closest('.unit-item').remove();
    } else {
        alert('At least one unit is required');
    }
}

// Auto-check terms from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const terms = urlParams.getAll('terms[]');
    terms.forEach(termId => {
        const checkbox = document.getElementById(`term_${termId}`);
        if (checkbox) checkbox.checked = true;
    });
});
</script>
@endsection
