@extends('staff.partials.sidebar_sec')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create New Babyeyi Document</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('babyeyi.store') }}" method="POST" id="babyeyiForm">
                        @csrf

                        <!-- Academic Year -->
                        <div class="form-group mb-3">
                            <label for="academic_year_id" class="form-label"><strong>Academic Year *</strong></label>
                            <select class="form-control @error('academic_year_id') is-invalid @enderror" 
                                    id="academic_year_id" name="academic_year_id" required>
                                <option value="">-- Select Academic Year --</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Term -->
                        <div class="form-group mb-3">
                            <label for="term_id" class="form-label"><strong>Term *</strong></label>
                            <select class="form-control @error('term_id') is-invalid @enderror" 
                                    id="term_id" name="term_id" required>
                                <option value="">-- Select Term --</option>
                            </select>
                            @error('term_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Selection Type -->
                        <div class="form-group mb-3">
                            <label for="selection_type" class="form-label"><strong>Select Type *</strong></label>
                            <div class="form-check">
                                <input class="form-check-input selection-type-radio" type="radio" 
                                       name="selection_type" id="single" value="single" required>
                                <label class="form-check-label" for="single">
                                    Single Course
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input selection-type-radio" type="radio" 
                                       name="selection_type" id="by_type" value="by_type" required>
                                <label class="form-check-label" for="by_type">
                                    By Course Type
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input selection-type-radio" type="radio" 
                                       name="selection_type" id="multiple" value="multiple" required>
                                <label class="form-check-label" for="multiple">
                                    Multiple Courses
                                </label>
                            </div>
                            @error('selection_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Single Course Selection -->
                        <div class="form-group mb-3" id="course_group" style="display: none;">
                            <label for="course_id" class="form-label"><strong>Course *</strong></label>
                            <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id">
                                <option value="">-- Select Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                        {{ $course->name }} ({{ $course->course_type ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Type Selection -->
                        <div class="form-group mb-3" id="course_type_group" style="display: none;">
                            <label for="course_type" class="form-label"><strong>Course Type *</strong></label>
                            <select class="form-control @error('course_type') is-invalid @enderror" 
                                    id="course_type" name="course_type">
                                <option value="">-- Select Course Type --</option>
                                @foreach ($courseTypes as $type)
                                    <option value="{{ $type }}" @selected(old('course_type') == $type)>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info" id="info_alert">
                            <i class="fas fa-info-circle"></i>
                            <span id="info_text">Please select a type above.</span>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Create Babyeyi Template
                            </button>
                            <a href="{{ route('babyeyi.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
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
    const oldTermId = @json(old('term_id'));
    const termsUrlTemplate = @json(route('babyeyi.get-terms', ['academicYearId' => '__ACADEMIC_YEAR_ID__']));

    function loadTermsForYear(yearId) {
        if (!yearId) {
            termSelect.innerHTML = '<option value="">-- Select Term --</option>';
            termSelect.disabled = true;
            return;
        }

        termSelect.disabled = true;
        termSelect.innerHTML = '<option value="">Loading terms...</option>';

        const termsUrl = termsUrlTemplate.replace('__ACADEMIC_YEAR_ID__', encodeURIComponent(yearId));
        fetch(termsUrl)
            .then(response => response.json())
            .then(data => {
                termSelect.innerHTML = '<option value="">-- Select Term --</option>';
                data.forEach(term => {
                    termSelect.innerHTML += `<option value="${term.id}">${term.name}</option>`;
                });
                if (oldTermId) termSelect.value = String(oldTermId);
                termSelect.disabled = false;
            })
            .catch(() => {
                termSelect.innerHTML = '<option value="">Error loading terms</option>';
                termSelect.disabled = true;
            });
    }

    // Load terms when academic year changes
    academicYearSelect.addEventListener('change', function() {
        loadTermsForYear(this.value);
    });

    // Auto-load terms on initial page load (e.g., after validation errors)
    if (academicYearSelect.value) {
        loadTermsForYear(academicYearSelect.value);
    } else {
        termSelect.disabled = true;
    }

    // Handle selection type changes
    selectionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            courseGroup.style.display = 'none';
            courseTypeGroup.style.display = 'none';

            if (this.value === 'single') {
                courseGroup.style.display = 'block';
                infoText.textContent = 'Selected fee table will be included for this course.';
            } else if (this.value === 'by_type') {
                courseTypeGroup.style.display = 'block';
                infoText.textContent = 'All courses of this type will be included. Fee table will be excluded.';
            } else {
                infoText.textContent = 'Multiple courses will be included. Fee table will be excluded.';
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
@endsection
