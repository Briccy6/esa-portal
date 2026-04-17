@extends('admin.layout')

@section('title', 'Create Babyeyi Document')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-medical text-primary me-2"></i>
                Create New Babyeyi
            </h2>
            <div class="text-muted">Create a new term registration document (template). You will edit it with the WYSIWYG editor.</div>
        </div>
        <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <strong>Term Registration Document Details</strong>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('babyeyi.store') }}" method="POST" id="babyeyiForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="academic_year_id" class="form-label">
                            <strong>Academic Year</strong> <span class="text-danger">*</span>
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
                            <strong>Term</strong> <span class="text-danger">*</span>
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

                <div class="mb-3">
                    <label class="form-label">
                        <strong>Document Type</strong> <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded bg-light h-100">
                                <input class="form-check-input selection-type-radio" type="radio"
                                       name="selection_type" id="type_single" value="single" required>
                                <label class="form-check-label" for="type_single">
                                    <strong>Single Course</strong><br>
                                    <small class="text-muted">Include fee table for one course</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded bg-light h-100">
                                <input class="form-check-input selection-type-radio" type="radio"
                                       name="selection_type" id="type_by" value="by_type" required>
                                <label class="form-check-label" for="type_by">
                                    <strong>By Course Type</strong><br>
                                    <small class="text-muted">All courses of selected type</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded bg-light h-100">
                                <input class="form-check-input selection-type-radio" type="radio"
                                       name="selection_type" id="type_multiple" value="multiple" required>
                                <label class="form-check-label" for="type_multiple">
                                    <strong>Multiple Courses</strong><br>
                                    <small class="text-muted">Combined courses (no fee table)</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    @error('selection_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row" id="course_group" style="display:none;">
                    <div class="col-md-12 mb-3">
                        <label for="course_id" class="form-label">
                            <strong>Select Course</strong> <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id">
                            <option value="">-- Select Course --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                    {{ $course->name }}@if($course->course_type) ({{ $course->course_type }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row" id="course_type_group" style="display:none;">
                    <div class="col-md-12 mb-3">
                        <label for="course_type" class="form-label">
                            <strong>Select Course Type</strong> <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('course_type') is-invalid @enderror" id="course_type" name="course_type">
                            <option value="">-- Select Course Type --</option>
                            @foreach ($courseTypes as $type)
                                <option value="{{ $type }}" @selected(old('course_type') == $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('course_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('babyeyi.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const academicYearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term_id');
    const selectionTypeRadios = document.querySelectorAll('.selection-type-radio');
    const courseGroup = document.getElementById('course_group');
    const courseTypeGroup = document.getElementById('course_type_group');
    const oldTermId = @json(old('term_id'));
    const termsUrlTemplate = @json(route('babyeyi.get-terms', ['academicYearId' => '__ACADEMIC_YEAR_ID__']));

    function updateType() {
        const selected = document.querySelector('input[name="selection_type"]:checked');
        courseGroup.style.display = 'none';
        courseTypeGroup.style.display = 'none';
        if (!selected) return;
        if (selected.value === 'single') courseGroup.style.display = '';
        if (selected.value === 'by_type') courseTypeGroup.style.display = '';
    }

    selectionTypeRadios.forEach(r => r.addEventListener('change', updateType));
    updateType();

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
            .then(r => r.json())
            .then(data => {
                termSelect.innerHTML = '<option value="">-- Select Term --</option>';
                data.forEach(term => {
                    const opt = document.createElement('option');
                    opt.value = term.id;
                    opt.textContent = term.name;
                    termSelect.appendChild(opt);
                });
                if (oldTermId) termSelect.value = String(oldTermId);
                termSelect.disabled = false;
            })
            .catch(() => {
                termSelect.innerHTML = '<option value="">Error loading terms</option>';
                termSelect.disabled = true;
            });
    }

    academicYearSelect.addEventListener('change', function () {
        loadTermsForYear(this.value);
    });

    // Auto-load terms on initial page load (e.g., after validation errors)
    if (academicYearSelect.value) {
        loadTermsForYear(academicYearSelect.value);
    } else {
        termSelect.disabled = true;
    }
});
</script>
@endsection
