@extends('secretary.layout')

@section('page_title', 'Complete registration')
@section('title', 'Complete Student Registration')

@section('content')
<div class="page-shell">
    <div class="page-heading">
        <div>
            <h1>Complete registration</h1>
            <p>{{ $student->full_name }} · {{ $student->reg_number }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to list
            </a>
        </div>
    </div>

    <section class="sec-card mb-3">
        <div class="sec-card-header"><div><h2>Student snapshot</h2></div></div>
        <div class="sec-card-body">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3"><span class="text-muted d-block small">Email</span><strong>{{ $student->email }}</strong></div>
                <div class="col-sm-6 col-lg-3"><span class="text-muted d-block small">Phone</span><strong>{{ $student->phone ?? 'N/A' }}</strong></div>
                <div class="col-sm-6 col-lg-3"><span class="text-muted d-block small">Branch</span><strong>{{ $student->branch->branch_name ?? 'N/A' }}</strong></div>
                <div class="col-sm-6 col-lg-3"><span class="text-muted d-block small">Course</span><strong>{{ $student->course->course_name ?? 'N/A' }}</strong></div>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('secretary.student-registration.update', $student) }}" enctype="multipart/form-data" id="registrationForm">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-lg-6">
                <section class="sec-card h-100">
                    <div class="sec-card-header"><div><h2>Identification</h2></div></div>
                    <div class="sec-card-body">
                        <div class="mb-3">
                            <label for="identification_type" class="form-label">Identification type *</label>
                            <select class="form-select @error('identification_type') is-invalid @enderror"
                                    id="identification_type" name="identification_type" required>
                                <option value="">Select identification type</option>
                                <option value="national_id" {{ old('identification_type', $student->identification_type) == 'national_id' ? 'selected' : '' }}>National ID</option>
                                <option value="passport" {{ old('identification_type', $student->identification_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                            </select>
                            @error('identification_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0">
                            <label for="id_number" class="form-label">ID / passport number *</label>
                            <input type="text" class="form-control @error('id_number') is-invalid @enderror"
                                   id="id_number" name="id_number" value="{{ old('id_number', $student->id_number) }}" required>
                            @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="sec-card h-100">
                    <div class="sec-card-header"><div><h2>Photo uploads</h2><p>JPEG, PNG, or JPG. Max 2MB.</p></div></div>
                    <div class="sec-card-body">
                        <div class="mb-3">
                            <label for="id_photo" class="form-label">ID photo {{ $student->id_photo ? '' : '*' }}</label>
                            <input type="file" class="form-control @error('id_photo') is-invalid @enderror"
                                   id="id_photo" name="id_photo" accept="image/*" {{ $student->id_photo ? '' : 'required' }}>
                            @error('id_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($student->id_photo)
                                <small class="text-muted d-block mt-1">Current: <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank">View ID photo</a></small>
                            @endif
                            <img id="id_photo_preview" class="img-fluid mt-2 rounded" style="max-height: 180px; display: none;" alt="ID Photo Preview">
                        </div>
                        <div class="mb-0">
                            <label for="passport_photo" class="form-label">Passport photo {{ $student->passport_photo ? '' : '*' }}</label>
                            <input type="file" class="form-control @error('passport_photo') is-invalid @enderror"
                                   id="passport_photo" name="passport_photo" accept="image/*" {{ $student->passport_photo ? '' : 'required' }}>
                            @error('passport_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($student->passport_photo)
                                <small class="text-muted d-block mt-1">Current: <a href="{{ asset('storage/' . $student->passport_photo) }}" target="_blank">View passport photo</a></small>
                            @endif
                            <img id="passport_photo_preview" class="img-fluid mt-2 rounded" style="max-height: 180px; display: none;" alt="Passport Photo Preview">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <section class="sec-card mt-3">
            <div class="sec-card-header"><div><h2>Supporting documents</h2><p>Optional. PDF, DOC, DOCX, or image. Max 5MB.</p></div></div>
            <div class="sec-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="senior6_document" class="form-label">Senior 6 document</label>
                        <input type="file" class="form-control @error('senior6_document') is-invalid @enderror"
                               id="senior6_document" name="senior6_document" accept="image/*,.pdf,.doc,.docx">
                        @error('senior6_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($student->senior6_document)
                            <small class="text-muted d-block mt-1">Current: <a href="{{ asset('storage/' . $student->senior6_document) }}" target="_blank">View</a></small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="senior3_document" class="form-label">Senior 3 document</label>
                        <input type="file" class="form-control @error('senior3_document') is-invalid @enderror"
                               id="senior3_document" name="senior3_document" accept="image/*,.pdf,.doc,.docx">
                        @error('senior3_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($student->senior3_document)
                            <small class="text-muted d-block mt-1">Current: <a href="{{ asset('storage/' . $student->senior3_document) }}" target="_blank">View</a></small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="other_supporting_document" class="form-label">Other document</label>
                        <input type="file" class="form-control @error('other_supporting_document') is-invalid @enderror"
                               id="other_supporting_document" name="other_supporting_document" accept="image/*,.pdf,.doc,.docx">
                        @error('other_supporting_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($student->other_supporting_document)
                            <small class="text-muted d-block mt-1">Current: <a href="{{ asset('storage/' . $student->other_supporting_document) }}" target="_blank">View</a></small>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="page-actions mt-3 justify-content-between">
            <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save registration
                </button>
                <button type="submit" class="btn btn-success" form="completeForm" onclick="return confirm('Mark this registration as complete?')">
                    <i class="bi bi-check2"></i> Mark complete
                </button>
            </div>
        </div>
    </form>

    <form id="completeForm" action="{{ route('secretary.student-registration.complete', $student) }}" method="POST" class="d-none">
        @csrf
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function setupFilePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    setupFilePreview('id_photo', 'id_photo_preview');
    setupFilePreview('passport_photo', 'passport_photo_preview');
});
</script>
@endsection
