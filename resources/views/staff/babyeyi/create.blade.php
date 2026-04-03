@extends('staff.partials.sidebar_sec')

@section('content')
<div class="page-content">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-file-plus text-primary"></i> Create New Babyeyi
                    </h1>
                    <p class="text-muted mb-0">Create a new term registration document for students</p>
                </div>
                <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-contract"></i> Term Registration Document Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('babyeyi.store') }}" method="POST" id="babyeyiForm">
                        @csrf

                        <!-- Academic Year & Term Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="academic_year_id" class="form-label">
                                    <strong>Academic Year</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                        id="academic_year_id" name="academic_year_id" required>
                                    <option value="">-- Select Academic Year --</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>
                                            {{ $year->year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="term_id" class="form-label">
                                    <strong>Term</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('term_id') is-invalid @enderror" 
                                        id="term_id" name="term_id" required>
                                    <option value="">-- Select Term --</option>
                                </select>
                                @error('term_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Selection Type -->
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>Document Type</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check p-3 border rounded-2 cursor-pointer" style="background-color: #f8f9fa; transition: all 0.3s;">
                                        <input class="form-check-input selection-type-radio" type="radio" 
                                               name="selection_type" id="single" value="single" required>
                                        <label class="form-check-label w-100" for="single">
                                            <strong>Single Course</strong>
                                            <br>
                                            <small class="text-muted">For a specific course with fee table</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check p-3 border rounded-2 cursor-pointer" style="background-color: #f8f9fa; transition: all 0.3s;">
                                        <input class="form-check-input selection-type-radio" type="radio" 
                                               name="selection_type" id="by_type" value="by_type" required>
                                        <label class="form-check-label w-100" for="by_type">
                                            <strong>By Course Type</strong>
                                            <br>
                                            <small class="text-muted">All courses of selected type</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check p-3 border rounded-2 cursor-pointer" style="background-color: #f8f9fa; transition: all 0.3s;">
                                        <input class="form-check-input selection-type-radio" type="radio" 
                                               name="selection_type" id="multiple" value="multiple" required>
                                        <label class="form-check-label w-100" for="multiple">
                                            <strong>Multiple Courses</strong>
                                            <br>
                                            <small class="text-muted">Multiple courses combined</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('selection_type')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Single Course Selection -->
                        <div class="mb-3" id="course_group" style="display: none;">
                            <label for="course_id" class="form-label">
                                <strong>Select Course</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id">
                                <option value="">-- Select Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                        {{ $course->name }} 
                                        @if($course->course_type)
                                            ({{ $course->course_type }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> Selecting a course will include the fee table for this specific course.
                            </small>
                        </div>

                        <!-- Course Type Selection -->
                        <div class="mb-3" id="course_type_group" style="display: none;">
                            <label for="course_type" class="form-label">
                                <strong>Select Course Type</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('course_type') is-invalid @enderror" 
                                    id="course_type" name="course_type">
                                <option value="">-- Select Course Type --</option>
                                @foreach ($courseTypes as $type)
                                    <option value="{{ $type }}" @selected(old('course_type') == $type)>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> All courses of this type will be included. Fee table will be excluded.
                            </small>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mb-4" id="info_alert" role="alert">
                            <i class="fas fa-lightbulb"></i>
                            <span id="info_text">Please select a document type above to proceed.</span>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Create Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const academicYearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term_id');
    const courseSelect = document.getElementById('course_id');
    const courseTypeSelect = document.getElementById('course_type');
    const selectionTypeRadios = document.querySelectorAll('.selection-type-radio');
    const courseGroup = document.getElementById('course_group');
    const courseTypeGroup = document.getElementById('course_type_group');
    const infoText = document.getElementById('info_text');
    const checkBoxes = document.querySelectorAll('.form-check');

    // Load terms when academic year changes
    academicYearSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`/babyeyi/get-terms/${this.value}`)
                .then(response => response.json())
                .then(data => {
                    termSelect.innerHTML = '<option value="">-- Select Term --</option>';
                    data.forEach(term => {
                        termSelect.innerHTML += `<option value="${term.id}">${term.name}</option>`;
                    });
                });
        } else {
            termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        }
    });

    // Handle selection type changes
    selectionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            courseGroup.style.display = 'none';
            courseTypeGroup.style.display = 'none';

            if (this.value === 'single') {
                courseGroup.style.display = 'block';
                infoText.innerHTML = '<i class="fas fa-book"></i> Selected fee table will be included for this course.';
            } else if (this.value === 'by_type') {
                courseTypeGroup.style.display = 'block';
                infoText.innerHTML = '<i class="fas fa-layer-group"></i> All courses of this type will be included. Fee table will be excluded.';
            } else {
                infoText.innerHTML = '<i class="fas fa-books"></i> Multiple courses will be included. Fee table will be excluded.';
            }
        });
    });

    // Load courses by type when course type changes
    courseTypeSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`/babyeyi/get-courses?course_type=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="">-- Available Courses --</option>';
                    data.forEach(course => {
                        courseSelect.innerHTML += `<option value="${course.id}">${course.name}</option>`;
                    });
                });
        }
    });

    // Restore selection type if form has errors
    const selectedType = document.querySelector('input[name="selection_type"]:checked');
    if (selectedType) {
        selectedType.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
    .page-content h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
    }
    
    .form-check p-3 {
        cursor: pointer;
    }
    
    .form-check:hover {
        background-color: #e8eef7 !important;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 14px;
    }
</style>
@endsection
