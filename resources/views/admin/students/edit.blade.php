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
                                        <label for="sex" class="form-label fw-bold">Sex *</label>
                                        <select class="form-select @error('sex') is-invalid @enderror" 
                                                id="sex" 
                                                name="sex" 
                                                required>
                                            <option value="">Select Sex</option>
                                            <option value="Male" {{ old('sex', $student->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('sex', $student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('sex')
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
    /* Enhanced CSS for Student Edit Page */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .page-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }

    .page-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }

    .header-actions .btn {
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    .header-actions .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }

    .form-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 16px;
        padding: 2.5rem;
        height: 100%;
        border: 1px solid #dee2e6;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
    }

    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }

    .section-title {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #e9ecef;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        font-size: 1.5rem;
    }

    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        padding: 12px 16px;
        font-size: 1rem;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label::after {
        content: '';
        width: 4px;
        height: 4px;
        background: #667eea;
        border-radius: 50%;
        display: inline-block;
    }

    .btn {
        border-radius: 12px;
        transition: all 0.3s ease;
        font-weight: 600;
        padding: 12px 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }

    .card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .card-header {
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .card-body {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    .invalid-feedback {
        font-weight: 500;
        margin-top: 0.5rem;
        color: #dc3545;
    }

    .form-text {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-style: italic;
    }

    .alert-dismissible .btn-close {
        padding: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .form-section {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .header-actions {
            flex-direction: column;
            gap: 1rem;
        }

        .header-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .page-subtitle {
            font-size: 1rem;
        }
    }

    /* Animation for form sections */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-section {
        animation: fadeInUp 0.6s ease-out;
    }

    .form-section:nth-child(2) {
        animation-delay: 0.1s;
    }

    .form-section:nth-child(3) {
        animation-delay: 0.2s;
    }

    .form-section:nth-child(4) {
        animation-delay: 0.3s;
    }

    /* File input styling */
    input[type="file"] {
        background: white;
        cursor: pointer;
    }

    input[type="file"]:hover {
        border-color: #667eea;
    }

    /* Password field styling */
    input[type="password"] {
        font-family: 'Courier New', monospace;
    }

    /* Readonly field styling */
    input[readonly] {
        background: #f8f9fa;
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Focus states for accessibility */
    .btn:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }

    /* Loading state */
    .btn.loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .btn.loading::after {
        content: '';
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-left: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    // Enhanced JavaScript for Student Edit Page
    
    // Password confirmation validation with visual feedback
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirmation = passwordConfirmationInput.value;
        
        if (password && confirmation) {
            if (password === confirmation) {
                passwordConfirmationInput.setCustomValidity('');
                passwordConfirmationInput.classList.remove('is-invalid');
                passwordConfirmationInput.classList.add('is-valid');
            } else {
                passwordConfirmationInput.setCustomValidity('Passwords do not match');
                passwordConfirmationInput.classList.remove('is-valid');
                passwordConfirmationInput.classList.add('is-invalid');
            }
        } else {
            passwordConfirmationInput.setCustomValidity('');
            passwordConfirmationInput.classList.remove('is-valid', 'is-invalid');
        }
    }
    
    if (passwordConfirmationInput) {
        passwordConfirmationInput.addEventListener('input', validatePasswordMatch);
        passwordConfirmationInput.addEventListener('blur', validatePasswordMatch);
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (passwordConfirmationInput.value) {
                validatePasswordMatch();
            }
            
            // Password strength indicator
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            // Add visual feedback for password strength
            this.classList.remove('bg-danger', 'bg-warning', 'bg-success', 'bg-info');
            if (password.length > 0) {
                if (strength < 2) this.classList.add('bg-danger');
                else if (strength < 3) this.classList.add('bg-warning');
                else if (strength < 4) this.classList.add('bg-info');
                else this.classList.add('bg-success');
            }
        });
    }
    
    // Enhanced file size validation with visual feedback
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            // Remove previous validation states
            this.classList.remove('is-valid', 'is-invalid');
            
            if (file) {
                if (file.size > maxSize) {
                    this.classList.add('is-invalid');
                    showNotification(`File "${file.name}" is too large. Maximum size is 2MB.`, 'danger');
                    this.value = '';
                } else {
                    this.classList.add('is-valid');
                    showNotification(`File "${file.name}" is valid (${(file.size / 1024).toFixed(1)}KB)`, 'success');
                }
            }
        });
    });
    
    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Form submission with loading state
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
            }
            
            // Show loading notification
            showNotification('Updating student information...', 'info');
        });
    }
    
    // Auto-save functionality for draft data
    let autoSaveTimer;
    const formInputs = form.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Save to localStorage as draft
                const formData = new FormData(form);
                const draftData = {};
                
                for (let [key, value] of formData.entries()) {
                    if (value) draftData[key] = value;
                }
                
                localStorage.setItem('studentEditDraft', JSON.stringify(draftData));
                console.log('Draft saved automatically');
            }, 2000); // Auto-save after 2 seconds of inactivity
        });
    });
    
    // Restore draft on page load
    window.addEventListener('load', function() {
        const draftData = localStorage.getItem('studentEditDraft');
        if (draftData) {
            const draft = JSON.parse(draftData);
            let hasDraftData = false;
            
            Object.keys(draft).forEach(key => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input && !input.value) {
                    input.value = draft[key];
                    hasDraftData = true;
                }
            });
            
            if (hasDraftData) {
                showNotification('Draft data restored from previous session', 'info');
            }
        }
    });
    
    // Clear draft on successful submission
    window.addEventListener('beforeunload', function() {
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn && submitBtn.classList.contains('loading')) {
            localStorage.removeItem('studentEditDraft');
        }
    });
    
    // Initialize tooltips and popovers
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize Bootstrap popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }
        
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            // Ctrl+S to save (prevent default browser save)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    form.dispatchEvent(new Event('submit'));
                }
            }
        });
        
        // Add form field focus animations
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    });
    
    // Add CSS for focused state animations
    const style = document.createElement('style');
    style.textContent = `
        .focused {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }
        
        .bg-success, .bg-danger, .bg-warning, .bg-info {
            transition: background-color 0.3s ease;
        }
        
        .is-valid {
            border-color: #28a745 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
    `;
    document.head.appendChild(style);
</script>
@endsection