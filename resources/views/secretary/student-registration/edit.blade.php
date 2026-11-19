@extends('secretary.layout')

@section('title', 'Complete Student Registration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Complete Registration - {{ $student->full_name }}</h5>
                    <div>
                        <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Student Information</h6>
                                <div class="row">
                                    <div class="col-md-3"><strong>Reg Number:</strong> {{ $student->reg_number }}</div>
                                    <div class="col-md-3"><strong>Email:</strong> {{ $student->email }}</div>
                                    <div class="col-md-3"><strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Branch:</strong> {{ $student->branch ? $student->branch->branch_name : 'N/A' }}</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>Course:</strong> {{ $student->course ? $student->course->course_name : 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Academic Year:</strong> {{ $student->academic_year ? $student->academic_year->year : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('secretary.student-registration.update', $student) }}" enctype="multipart/form-data" id="registrationForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Identification Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="identification_type" class="form-label">Identification Type *</label>
                                            <select class="form-select @error('identification_type') is-invalid @enderror" 
                                                    id="identification_type" name="identification_type" required>
                                                <option value="">Select identification type</option>
                                                <option value="national_id" {{ $student->identification_type == 'national_id' ? 'selected' : '' }}>
                                                    National ID
                                                </option>
                                                <option value="passport" {{ $student->identification_type == 'passport' ? 'selected' : '' }}>
                                                    Passport
                                                </option>
                                            </select>
                                            @error('identification_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="id_number" class="form-label">ID/Passport Number *</label>
                                            <input type="text" class="form-control @error('id_number') is-invalid @enderror" 
                                                   id="id_number" name="id_number" value="{{ old('id_number', $student->id_number) }}" required>
                                            @error('id_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Photo Uploads</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="id_photo" class="form-label">ID Photo *</label>
                                            <input type="file" class="form-control @error('id_photo') is-invalid @enderror" 
                                                   id="id_photo" name="id_photo" accept="image/*">
                                            @error('id_photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($student->id_photo)
                                                <small class="text-muted d-block mt-1">
                                                    Current: <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank">View current ID photo</a>
                                                </small>
                                            @endif
                                            <small class="text-muted">Max size: 2MB, Formats: JPEG, PNG, JPG</small>
                                            <img id="id_photo_preview" class="img-fluid mt-2" style="max-height: 200px; display: none;" alt="ID Photo Preview">
                                        </div>

                                        <div class="mb-3">
                                            <label for="passport_photo" class="form-label">Passport Photo *</label>
                                            <input type="file" class="form-control @error('passport_photo') is-invalid @enderror" 
                                                   id="passport_photo" name="passport_photo" accept="image/*">
                                            @error('passport_photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($student->passport_photo)
                                                <small class="text-muted d-block mt-1">
                                                    Current: <a href="{{ asset('storage/' . $student->passport_photo) }}" target="_blank">View current passport photo</a>
                                                </small>
                                            @endif
                                            <small class="text-muted">Max size: 2MB, Formats: JPEG, PNG, JPG</small>
                                            <img id="passport_photo_preview" class="img-fluid mt-2" style="max-height: 200px; display: none;" alt="Passport Photo Preview">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Supporting Documents</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="senior6_document" class="form-label">Senior 6 Document</label>
                                                    <input type="file" class="form-control @error('senior6_document') is-invalid @enderror" 
                                                           id="senior6_document" name="senior6_document" accept="image/*,.pdf,.doc,.docx">
                                                    @error('senior6_document')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @if($student->senior6_document)
                                                        <small class="text-muted d-block mt-1">
                                                            Current: <a href="{{ asset('storage/' . $student->senior6_document) }}" target="_blank">View document</a>
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="senior3_document" class="form-label">Senior 3 Document</label>
                                                    <input type="file" class="form-control @error('senior3_document') is-invalid @enderror" 
                                                           id="senior3_document" name="senior3_document" accept="image/*,.pdf,.doc,.docx">
                                                    @error('senior3_document')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @if($student->senior3_document)
                                                        <small class="text-muted d-block mt-1">
                                                            Current: <a href="{{ asset('storage/' . $student->senior3_document) }}" target="_blank">View document</a>
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="other_supporting_document" class="form-label">Other Supporting Document</label>
                                                    <input type="file" class="form-control @error('other_supporting_document') is-invalid @enderror" 
                                                           id="other_supporting_document" name="other_supporting_document" accept="image/*,.pdf,.doc,.docx">
                                                    @error('other_supporting_document')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @if($student->other_supporting_document)
                                                        <small class="text-muted d-block mt-1">
                                                            Current: <a href="{{ asset('storage/' . $student->other_supporting_document) }}" target="_blank">View document</a>
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Max size: 5MB, Formats: PDF, DOC, DOCX, JPEG, PNG, JPG</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Registration
                                </button>
                                <form action="{{ route('secretary.student-registration.complete', $student) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success ms-2" onclick="try { return confirm('Are you sure this student\'s registration is complete?'); } catch(e) { console.error('Confirmation error:', e); return false; }">
                                        <i class="fas fa-check"></i> Mark Complete
                                    </button>
                                </form>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // File preview functionality
    function setupFilePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }
    
    // Setup file previews for photo uploads
    setupFilePreview('id_photo', 'id_photo_preview');
    setupFilePreview('passport_photo', 'passport_photo_preview');
    
    // Form validation
    const form = document.getElementById('registrationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const idNumber = document.getElementById('id_number').value;
            const identificationType = document.getElementById('identification_type').value;
            const idPhoto = document.getElementById('id_photo').files[0];
            const passportPhoto = document.getElementById('passport_photo').files[0];
            
            if (!idNumber || !identificationType || !idPhoto || !passportPhoto) {
                e.preventDefault();
                alert('Please fill in all required fields and upload both ID photo and passport photo.');
                return false;
            }
        });
    }
});
</script>
@endpush
