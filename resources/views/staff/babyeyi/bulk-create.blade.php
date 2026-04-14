@extends('staff.partials.sidebar_sec')

@section('title', 'Bulk Generate Babyeyi Documents')

@section('content')
<div class="page-content">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-bolt text-primary"></i> Bulk Generate Babyeyi
                    </h1>
                    <p class="text-muted mb-0">Automatically create and publish babyeyis for all courses in a term</p>
                </div>
                <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="row mb-4">
        <div class="col-md-10 offset-md-1">
            <div class="alert alert-info border-0 shadow-sm" role="alert">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading mb-2">How it works</h5>
                        <p class="mb-0">
                            This tool will automatically create a babyeyi document for each course that has fees configured for the selected term. 
                            Each document will include the complete fee schedule table. You can choose to publish them immediately or keep them in draft for review.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog"></i> Bulk Generation Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('babyeyi.bulk.store') }}" method="POST" id="bulkForm">
                        @csrf

                        <!-- Academic Year & Term Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="academic_year_id" class="form-label">
                                    <strong>Academic Year</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                        id="academic_year_id" name="academic_year_id" required onchange="loadTerms()">
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
                                        id="term_id" name="term_id" required @if(!$terms->count()) disabled @endif>
                                    <option value="">-- Select Term --</option>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->id }}" @selected(old('term_id') == $term->id)>
                                            {{ $term->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('term_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Auto-Publish Checkbox -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="auto_publish" id="auto_publish" 
                                           value="1" @checked(old('auto_publish') || true)>
                                    <label class="form-check-label" for="auto_publish">
                                        <strong>Auto-Publish Documents</strong>
                                        <p class="text-muted small mb-0 mt-1">
                                            Check this to publish all generated babyeyis immediately, making them available to students. 
                                            Uncheck to keep them in draft for review.
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Help Text -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-warning border-0 small" role="alert">
                                    <strong>✓ What will happen:</strong>
                                    <ul class="mb-0 mt-2 ps-4">
                                        <li>A babyeyi will be created for each course with active term fees</li>
                                        <li>Each document will include a complete fee schedule table</li>
                                        <li>Documents will automatically include class entry conditions and payment requirements</li>
                                        <li>If auto-publish is enabled, students will see them immediately in their portal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-danger border-0" role="alert">
                                        <strong>Please fix the following:</strong>
                                        <ul class="mb-0 mt-2 ps-4">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Footer -->
                <div class="card-footer bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" form="bulkForm" class="btn btn-success btn-lg">
                            <i class="fas fa-bolt"></i> Generate Babyeyis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadTerms() {
    const academicYearId = document.getElementById('academic_year_id').value;
    const termSelect = document.getElementById('term_id');

    if (!academicYearId) {
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        termSelect.disabled = true;
        return;
    }

    // Fetch terms for selected academic year
    const termsUrlTemplate = @json(route('babyeyi.get-terms', ['academicYearId' => '__ACADEMIC_YEAR_ID__']));
    const termsUrl = termsUrlTemplate.replace('__ACADEMIC_YEAR_ID__', encodeURIComponent(academicYearId));

    fetch(termsUrl)
        .then(response => response.json())
        .then(data => {
            termSelect.innerHTML = '<option value="">-- Select Term --</option>';
            data.forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.name;
                termSelect.appendChild(option);
            });
            termSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            termSelect.innerHTML = '<option value="">Error loading terms</option>';
        });
}
</script>
@endpush

@endsection
