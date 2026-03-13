@extends('admin.layout')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="bi bi-pencil-square text-primary me-3"></i>
                            Edit Student
                        </h1>
                        <p class="page-subtitle">Update student information and details</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary btn-lg me-2">
                            <i class="bi bi-arrow-left me-2"></i>Back to Details
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-list me-2"></i>All Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <h4 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Student Information - {{ $student->full_name }}
                    </h4>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('admin.students.update', $student) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row g-4">
                            <!-- Personal Information -->
                            <div class="col-lg-6">
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="bi bi-person text-primary me-2"></i>
                                        Personal Information
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label fw-bold">Full Name *</label>
                                        <input type="text" 
                                               class="form-control @error('full_name') is-invalid @enderror" 
                                               id="full_name" 
                                               name="full_name" 
                                               value="{{ old('full_name', $student->full_name) }}" 
                                               required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Email Address *</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $student->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label fw-bold">Phone Number *</label>
                                        <input type="text" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $student->phone) }}" 
                                               required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="identification_type" class="form-label fw-bold">Identification Type *</label>
                                        <select class="form-select @error('identification_type') is-invalid @enderror" 
                                                id="identification_type" 
                                                name="identification_type" 
                                                required>
                                            <option value="">Select Identification Type</option>
                                            <option value="ID" {{ old('identification_type', $student->identification_type) == 'ID' ? 'selected' : '' }}>National ID</option>
                                            <option value="Passport" {{ old('identification_type', $student->identification_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                        </select>
                                        @error('identification_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="id_number" class="form-label fw-bold">Identification Number *</label>
                                        <input type="text" 
                                               class="form-control @error('id_number') is-invalid @enderror" 
                                               id="id_number" 
                                               name="id_number" 
                                               value="{{ old('id_number', $student->id_number) }}" 
                                               required>
                                        @error('id_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="discount" class="form-label fw-bold">Discount (%)</label>
                                        <input type="number" 
                                               class="form-control @error('discount') is-invalid @enderror" 
                                               id="discount" 
                                               name="discount" 
                                               value="{{ old('discount', $student->discount ?? 0) }}" 
                                               min="0" 
                                               max="100" 
                                               step="0.01">
                                        <div class="form-text">Enter discount percentage (0-100)</div>
                                        @error('discount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="col-lg-6">
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="bi bi-mortarboard text-success me-2"></i>
                                        Academic Information
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="academic_year_id" class="form-label fw-bold">Academic Year *</label>
                                        <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                                id="academic_year_id" 
                                                name="academic_year_id" 
                                                required>
                                            <option value="">Select Academic Year</option>
                                            @foreach($academicYears as $academicYear)
                                                <option value="{{ $academicYear->id }}" 
                                                        {{ old('academic_year_id', $student->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                                    {{ $academicYear->year }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_year_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="branch_id" class="form-label fw-bold">Branch *</label>
                                        <select class="form-select @error('branch_id') is-invalid @enderror" 
                                                id="branch_id" 
                                                name="branch_id" 
                                                required>
                                            <option value="">Select Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" 
                                                        {{ old('branch_id', $student->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="course_id" class="form-label fw-bold">Course *</label>
                                        <select class="form-select @error('course_id') is-invalid @enderror" 
                                                id="course_id" 
                                                name="course_id" 
                                                required>
                                            <option value="">Select Course</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" 
                                                        {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                                    {{ $course->course_name }} ({{ $course->abbreviation }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Registration Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ $student->reg_number }}" 
                                               readonly>
                                        <div class="form-text">Registration number cannot be changed</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="col-12">
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="bi bi-key text-warning me-2"></i>
                                        Password Management
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label fw-bold">New Password</label>
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password" 
                                                       minlength="8">
                                                <div class="form-text">Leave blank to keep current password</div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="password_confirmation" 
                                                       name="password_confirmation" 
                                                       minlength="8">
                                                <div class="form-text">Confirm the new password</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Uploads -->
                            <div class="col-12">
                                <div class="form-section">
                                    <h5 class="section-title">
                                        <i class="bi bi-file-earmark-text text-info me-2"></i>
                                        Documents & Photos
                                    </h5>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="id_photo" class="form-label fw-bold">ID Photo</label>
                                                <input type="file" 
                                                       class="form-control @error('id_photo') is-invalid @enderror" 
                                                       id="id_photo" 
                                                       name="id_photo" 
                                                       accept="image/*">
                                                @if($student->id_photo)
                                                    <div class="mt-2">
                                                        <small class="text-muted">Current: </small>
                                                        <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank" class="text-decoration-none">
                                                            View Current Photo
                                                        </a>
                                                    </div>
                                                @endif
                                                @error('id_photo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="passport_photo" class="form-label fw-bold">Passport Photo</label>
                                                <input type="file" 
                                                       class="form-control @error('passport_photo') is-invalid @enderror" 
                                                       id="passport_photo" 
                                                       name="passport_photo" 
                                                       accept="image/*">
                                                @if($student->passport_photo)
                                                    <div class="mt-2">
                                                        <small class="text-muted">Current: </small>
                                                        <a href="{{ asset('storage/' . $student->passport_photo) }}" target="_blank" class="text-decoration-none">
                                                            View Current Photo
                                                        </a>
                                                    </div>
                                                @endif
                                                @error('passport_photo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="senior6_document" class="form-label fw-bold">Senior 6 Certificate</label>
                                                <input type="file" 
                                                       class="form-control @error('senior6_document') is-invalid @enderror" 
                                                       id="senior6_document" 
                                                       name="senior6_document" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->senior6_document)
                                                    <div class="mt-2">
                                                        <small class="text-muted">Current: </small>
                                                        <a href="{{ asset('storage/' . $student->senior6_document) }}" target="_blank" class="text-decoration-none">
                                                            View Document
                                                        </a>
                                                    </div>
                                                @endif
                                                @error('senior6_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="senior3_document" class="form-label fw-bold">Senior 3 Certificate</label>
                                                <input type="file" 
                                                       class="form-control @error('senior3_document') is-invalid @enderror" 
                                                       id="senior3_document" 
                                                       name="senior3_document" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->senior3_document)
                                                    <div class="mt-2">
                                                        <small class="text-muted">Current: </small>
                                                        <a href="{{ asset('storage/' . $student->senior3_document) }}" target="_blank" class="text-decoration-none">
                                                            View Document
                                                        </a>
                                                    </div>
                                                @endif
                                                @error('senior3_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="other_supporting_document" class="form-label fw-bold">Other Documents</label>
                                                <input type="file" 
                                                       class="form-control @error('other_supporting_document') is-invalid @enderror" 
                                                       id="other_supporting_document" 
                                                       name="other_supporting_document" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->other_supporting_document)
                                                    <div class="mt-2">
                                                        <small class="text-muted">Current: </small>
                                                        <a href="{{ asset('storage/' . $student->other_supporting_document) }}" target="_blank" class="text-decoration-none">
                                                            View Document
                                                        </a>
                                                    </div>
                                                @endif
                                                @error('other_supporting_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-arrow-left me-2"></i>Cancel
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>Update Student
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
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    .header-actions .btn {
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .header-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 2rem;
        height: 100%;
        border: 1px solid #e9ecef;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
    }

    .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 1.8rem;
        }

        .form-section {
            padding: 1.5rem;
        }

        .header-actions {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<script>
    // Password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        
        if (password && confirmation && password !== confirmation) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    document.getElementById('password').addEventListener('input', function() {
        const confirmation = document.getElementById('password_confirmation');
        if (confirmation.value) {
            confirmation.dispatchEvent(new Event('input'));
        }
    });

    // File size validation
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (file && file.size > maxSize) {
                alert('File size must be less than 2MB');
                this.value = '';
            }
        });
    });

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>
@endsection 