@extends('layouts.app')

@section('title', 'Student Registration - ' . config('app.name'))

@push('styles')
<style>
    /* Enhanced CSS for Student Registration Page */
    
    .registration-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-attachment: fixed;
        position: relative;
        overflow: hidden;
    }
    
    .registration-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }
    
    .registration-header {
        text-align: center;
        color: white;
        padding: 3rem 0 2rem;
        position: relative;
        z-index: 1;
    }
    
    .registration-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        background: linear-gradient(45deg, #ffffff 0%, #f8f9fa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .registration-subtitle {
        font-size: 1.3rem;
        opacity: 0.95;
        margin-bottom: 2rem;
        font-weight: 300;
    }
    
    .back-link {
        color: rgba(255,255,255,0.9);
        text-decoration: none;
        font-weight: 500;
        padding: 12px 24px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        backdrop-filter: blur(10px);
    }
    
    .back-link:hover {
        color: white;
        border-color: rgba(255,255,255,0.6);
        background: rgba(255,255,255,0.1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .registration-form-card {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    
    .registration-form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .form-section {
        padding: 3rem;
        position: relative;
    }
    
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, #dee2e6 50%, transparent 100%);
        margin: 2rem 0;
        position: relative;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }
    
    .section-title::before {
        content: '';
        width: 40px;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
    
    .form-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
    
    .form-group {
        position: relative;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-label .required {
        color: #dc3545;
        font-weight: 700;
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 14px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
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
    
    .form-control.is-valid, .form-select.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    .error-alert {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: none;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.15);
    }
    
    .error-list {
        margin: 0;
        padding-left: 1.5rem;
    }
    
    .error-list li {
        margin-bottom: 0.5rem;
        color: #721c24;
        font-weight: 500;
    }
    
    .submit-section {
        text-align: center;
        padding: 2rem 3rem 3rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 16px 48px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
        transition: left 0.5s ease;
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }
    
    .submit-btn:hover::before {
        left: 100%;
    }
    
    .submit-btn:active {
        transform: translateY(-1px);
    }
    
    .fee-display {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: none;
    }
    
    .fee-display.show {
        display: block;
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
    }
    
    .file-input-wrapper input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    
    .file-input-label {
        display: block;
        padding: 14px 16px;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        text-align: center;
        color: #6c757d;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #f8f9fa;
    }
    
    .file-input-wrapper:hover .file-input-label {
        border-color: #667eea;
        color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }
    
    .file-input-wrapper.has-file .file-input-label {
        border-color: #28a745;
        color: #28a745;
        background: rgba(40, 167, 69, 0.05);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .registration-title {
            font-size: 2.5rem;
        }
        
        .registration-header {
            padding: 2rem 0 1rem;
        }
        
        .form-section {
            padding: 2rem 1.5rem;
        }
        
        .submit-section {
            padding: 1.5rem;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px 24px;
        }
        
        .back-link {
            font-size: 0.9rem;
            padding: 10px 20px;
        }
    }
    
    /* Loading states */
    .submit-btn.loading {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .submit-btn.loading::after {
        content: '';
        width: 20px;
        height: 20px;
        border: 3px solid transparent;
        border-top: 3px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-left: 10px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Form validation styling */
    .invalid-feedback {
        font-weight: 500;
        margin-top: 0.5rem;
        color: #dc3545;
        font-size: 0.875rem;
    }
    
    .form-text {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-style: italic;
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
    
    .form-section > * {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .form-section > *:nth-child(2) { animation-delay: 0.1s; }
    .form-section > *:nth-child(3) { animation-delay: 0.2s; }
    .form-section > *:nth-child(4) { animation-delay: 0.3s; }
    .form-section > *:nth-child(5) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="registration-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Header -->
                <div class="registration-header">
                    <h1 class="registration-title">Student Registration</h1>
                    <p class="registration-subtitle">Join our academic community and start your learning journey</p>
                    <div>
                        <a href="{{ route('register.choice') }}" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                            Choose Different Registration Type
                        </a>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="registration-form-card">
                    <div class="form-section">
                        @if ($errors->any())
                            <div class="error-alert">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="mb-2">Please fix the following errors:</h5>
                                        <ul class="error-list">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('students.flutterwave.initialize') }}" enctype="multipart/form-data" id="registrationForm">
                            @csrf
                            <input type="hidden" name="fee_type" value="registration_fee">
                            <input type="hidden" name="amount" id="payment_amount" value="0">
                            <input type="hidden" name="total_fee" id="total_fee" value="0">

                            <div id="paymentErrorBanner" class="alert alert-danger d-none" role="alert" style="display: none;">
                                <strong>Payment gateway unavailable.</strong>
                                <span id="paymentErrorBannerText">Please try again or contact support if the issue persists.</span>
                            </div>

                            <!-- Academic Information -->
                            <div class="mb-5">
                                <h3 class="section-title">
                                    <i class="fas fa-graduation-cap text-primary"></i>
                                    Academic Information
                                </h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="academic_year_id" class="form-label">
                                            Academic Year <span class="required">*</span>
                                        </label>
                                        <select id="academic_year_id" name="academic_year_id" class="form-select" required>
                                            <option value="">Select Academic Year</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year->id }}" {{ old('academic_year_id', $currentAcademicYear->id ?? null) == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="branch_id" class="form-label">
                                            Branch <span class="required">*</span>
                                        </label>
                                        <select id="branch_id" name="branch_id" class="form-select" required disabled></select>
                                    </div>

                                    <div class="form-group">
                                        <label for="course_id" class="form-label">
                                            Course <span class="required">*</span>
                                        </label>
                                        <select id="course_id" name="course_id" class="form-select" required disabled></select>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <!-- Personal Information -->
                            <div class="mb-5">
                                <h3 class="section-title">
                                    <i class="fas fa-user text-success"></i>
                                    Personal Information
                                </h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="full_name" class="form-label">
                                            Full Name <span class="required">*</span>
                                        </label>
                                        <input type="text" id="full_name" name="full_name" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            Email Address <span class="required">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="form-label">
                                            Password <span class="required">*</span>
                                        </label>
                                        <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="Choose a password">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone" class="form-label">
                                            Phone Number <span class="required">*</span>
                                        </label>
                                        <input type="text" id="phone" name="phone" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="sex" class="form-label">
                                            Sex <span class="required">*</span>
                                        </label>
                                        <select name="sex" id="sex" class="form-select" required>
                                            <option value="">Select Sex</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="identification_type" class="form-label">
                                            Identification Type <span class="required">*</span>
                                        </label>
                                        <select name="identification_type" id="identification_type" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="ID">National ID</option>
                                            <option value="Passport">Passport</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="identification_number" class="form-label">
                                            Identification Number <span class="required">*</span>
                                        </label>
                                        <input type="text" id="identification_number" name="identification_number" class="form-control" required>
                                    </div>

                                    <div class="form-group" id="id_photo_group" style="display:none;">
                                        <label for="id_photo" class="form-label">
                                            ID Document (Max: 2MB)
                                        </label>
                                        <div class="file-input-wrapper">
                                            <input type="file" id="id_photo" name="id_photo" accept="application/pdf,image/*">
                                            <label for="id_photo" class="file-input-label">
                                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                                Click to upload or drag and drop
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <!-- Passport Photo -->
                            <div class="mb-5">
                                <h3 class="section-title">
                                    <i class="fas fa-camera text-info"></i>
                                    Passport Photo
                                </h3>
                                <div class="form-group">
                                    <label for="passport_photo" class="form-label">
                                        Passport Photo <span class="required">*</span> (Max: 2MB)
                                    </label>
                                    <div class="file-input-wrapper">
                                        <input type="file" id="passport_photo" name="passport_photo" accept="image/*" required>
                                        <label for="passport_photo" class="file-input-label">
                                            <i class="fas fa-camera me-2"></i>
                                            Click to upload or drag and drop
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <!-- Education Level -->
                            <div class="mb-5">
                                <h3 class="section-title">
                                    <i class="fas fa-book text-warning"></i>
                                    Education Level
                                </h3>
                                <div class="form-group">
                                    <label class="form-label">
                                        Education Level <span class="required">*</span>
                                    </label>
                                    <select name="education_level" id="education_level" class="form-select" required>
                                        <option value="">Select Level</option>
                                        <option value="s6">Senior 6 (S6)</option>
                                        <option value="s3">Senior 3 (S3)</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <!-- Education Documents -->
                            <div class="mb-5">
                                <h3 class="section-title">
                                    <i class="fas fa-file-alt text-secondary"></i>
                                    Education Documents
                                </h3>
                                <div class="space-y-4">
                                    <div class="d-none" id="s6_field">
                                        <div class="form-group">
                                            <label for="senior6_document" class="form-label">
                                                S6 Result Slip (Max: 2MB)
                                            </label>
                                            <div class="file-input-wrapper">
                                                <input type="file" name="senior6_document" id="senior6_document" accept="application/pdf,image/*">
                                                <label for="senior6_document" class="file-input-label">
                                                    <i class="fas fa-file-pdf me-2"></i>
                                                    Click to upload or drag and drop
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-none" id="s3_field">
                                        <div class="form-group">
                                            <label for="senior3_document" class="form-label">
                                                S3 Result Slip (Max: 2MB)
                                            </label>
                                            <div class="file-input-wrapper">
                                                <input type="file" name="senior3_document" id="senior3_document" accept="application/pdf,image/*">
                                                <label for="senior3_document" class="file-input-label">
                                                    <i class="fas fa-file-pdf me-2"></i>
                                                    Click to upload or drag and drop
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-none" id="other_field">
                                        <div class="form-group">
                                            <label for="other_supporting_document" class="form-label">
                                                Other Supporting Document (Max: 2MB)
                                            </label>
                                            <div class="file-input-wrapper">
                                                <input type="file" name="other_supporting_document" id="other_supporting_document" accept="application/pdf,image/*">
                                                <label for="other_supporting_document" class="file-input-label">
                                                    <i class="fas fa-file me-2"></i>
                                                    Click to upload or drag and drop
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Fee Display -->
                            <div class="fee-display" id="registration_fee_box">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave me-3" style="font-size: 1.5rem; color: #0c5460;"></i>
                                    <div>
                                        <h5 class="mb-1">Registration Fee</h5>
                                        <p class="mb-0"><strong id="registration_fee_amount">0</strong> RWF</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Submit Section -->
                    <div class="submit-section">
                        <button type="submit" form="registrationForm" class="submit-btn">
                            <i class="fas fa-user-plus me-2"></i>
                            Proceed to Registration
                        </button>
                        <p class="mt-3 text-muted small">
                            By clicking "Proceed to Registration", you agree to our terms and conditions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
// Enhanced JavaScript for Student Registration

document.addEventListener('DOMContentLoaded', function() {
    // Academic year change handler
    document.getElementById('academic_year_id').addEventListener('change', function () {
        const yearId = this.value;
        const branchSelect = document.getElementById('branch_id');
        const courseSelect = document.getElementById('course_id');
        
        // Reset dependent fields
        branchSelect.innerHTML = '<option value="">Select Branch</option>';
        branchSelect.disabled = true;
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        
        if (yearId) {
            showLoading(branchSelect);
            fetch(`/api/branches?academic_year_id=${yearId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load branches');
                    return res.json();
                })
                .then(data => {
                    hideLoading(branchSelect);
                    branchSelect.innerHTML = '<option value="">Select Branch</option>';
                    data.forEach(branch => {
                        branchSelect.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                    });
                    branchSelect.disabled = false;

                    const selectedBranch = branchSelect.getAttribute('data-selected-branch');
                    if (selectedBranch) {
                        branchSelect.value = selectedBranch;
                        branchSelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(error => {
                    hideLoading(branchSelect);
                    showNotification('Error loading branches. Please try again.', 'danger');
                    console.error('Branch loading error:', error);
                });
        }
    });

    // Branch change handler
    document.getElementById('branch_id').addEventListener('change', function () {
        const branchId = this.value;
        const yearId = document.getElementById('academic_year_id').value;
        const courseSelect = document.getElementById('course_id');
        
        // Reset course field
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        
        if (branchId && yearId) {
            showLoading(courseSelect);
            fetch(`/api/courses?academic_year_id=${yearId}&branch_id=${branchId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load courses');
                    return res.json();
                })
                .then(data => {
                    hideLoading(courseSelect);
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    data.forEach(course => {
                        courseSelect.innerHTML += `<option value="${course.id}" data-fee="${course.registration_fee}">${course.course_name}</option>`;
                    });
                    courseSelect.disabled = false;

                    const selectedCourse = courseSelect.getAttribute('data-selected-course');
                    if (selectedCourse) {
                        courseSelect.value = selectedCourse;
                        courseSelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(error => {
                    hideLoading(courseSelect);
                    showNotification('Error loading courses. Please try again.', 'danger');
                    console.error('Course loading error:', error);
                });
        }
    });

    // Course change handler for fee display
    const paymentAmountInput = document.getElementById('payment_amount');
    const totalFeeInput = document.getElementById('total_fee');
    const submitBtn = document.querySelector('button[form="registrationForm"]');

    document.getElementById('course_id').addEventListener('change', function () {
        const courseId = this.value;
        const branchId = document.getElementById('branch_id')?.value || '';
        const yearId = document.getElementById('academic_year_id')?.value || '';
        const fee = this.options[this.selectedIndex]?.getAttribute('data-fee');
        const feeBox = document.getElementById('registration_fee_box');
        const feeAmount = document.getElementById('registration_fee_amount');
        
        function updateButton(amount) {
            if (!submitBtn) return;
            const formatted = parseFloat(amount).toLocaleString();
            submitBtn.innerHTML = amount > 0
                ? `<i class="fas fa-credit-card me-2"></i>Pay ${formatted} RWF`
                : `<i class="fas fa-user-plus me-2"></i>Proceed to Registration`;
        }

        // Fetch branch/term specific fee from server for accuracy
        if (courseId && yearId) {
            fetch(`{{ route('courses.registration_fee') }}?course_id=${courseId}&branch_id=${branchId}&academic_year_id=${yearId}`)
                .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch fee'))
                .then(data => {
                    const reg = parseFloat(data.registration_fee) || 0;
                    feeAmount.textContent = reg.toLocaleString();
                    paymentAmountInput.value = reg;
                    totalFeeInput.value = reg;
                    if (this.selectedIndex >= 0) {
                        this.options[this.selectedIndex].setAttribute('data-fee', reg);
                    }
                    feeBox.classList.add('show');
                    updateButton(reg);
                }).catch(err => {
                    console.error('Fee fetch error:', err);
                    const fallback = fee && fee !== '0' ? parseInt(fee) : 0;
                    feeAmount.textContent = fallback.toLocaleString();
                    paymentAmountInput.value = fallback;
                    totalFeeInput.value = fallback;
                    if (!fallback) {
                        feeBox.classList.remove('show');
                    } else {
                        feeBox.classList.add('show');
                    }
                    updateButton(fallback);
                });
        } else {
            feeAmount.textContent = '0';
            paymentAmountInput.value = 0;
            totalFeeInput.value = 0;
            feeBox.classList.remove('show');
            updateButton(0);
        }
    });

    // Education level change handler
    document.getElementById('education_level').addEventListener('change', function () {
        const level = this.value;
        const fields = ['s6_field', 's3_field', 'other_field'];
        
        // Hide all fields first
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) field.classList.add('d-none');
        });
        
        // Show relevant field
        if (level) {
            const targetField = document.getElementById(`${level}_field`);
            if (targetField) {
                targetField.classList.remove('d-none');
                // Add animation
                targetField.style.animation = 'slideDown 0.3s ease-out';
            }
        }
    });

    // Identification type change handler
    document.getElementById('identification_type').addEventListener('change', function () {
        const idPhotoGroup = document.getElementById('id_photo_group');
        
        if (this.value) {
            idPhotoGroup.style.display = 'block';
            idPhotoGroup.style.animation = 'slideDown 0.3s ease-out';
        } else {
            idPhotoGroup.style.display = 'none';
        }
    });

    // File input handlers
    setupFileInputs();
    
    // Form validation
    setupFormValidation();
    
    // Form submission
    setupFormSubmission();
    
    // Auto-save functionality
    setupAutoSave();

    // Load branches/courses immediately for the selected academic year.
    const yearSelect = document.getElementById('academic_year_id');
    if (yearSelect && yearSelect.value) {
        yearSelect.dispatchEvent(new Event('change'));
    }
});

// File input setup
function setupFileInputs() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        const wrapper = input.closest('.file-input-wrapper');
        if (!wrapper) return;
        
        const label = wrapper.querySelector('.file-input-label');
        
        input.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (file) {
                if (file.size > maxSize) {
                    showNotification(`File "${file.name}" is too large. Maximum size is 2MB.`, 'danger');
                    this.value = '';
                    wrapper.classList.remove('has-file');
                    label.innerHTML = `<i class="fas fa-cloud-upload-alt me-2"></i>Click to upload or drag and drop`;
                } else {
                    wrapper.classList.add('has-file');
                    const fileSize = (file.size / 1024).toFixed(1);
                    label.innerHTML = `<i class="fas fa-check-circle me-2"></i>${file.name} (${fileSize}KB)`;
                    showNotification(`File "${file.name}" uploaded successfully`, 'success');
                }
            } else {
                wrapper.classList.remove('has-file');
                label.innerHTML = `<i class="fas fa-cloud-upload-alt me-2"></i>Click to upload or drag and drop`;
            }
        });
        
        // Drag and drop support
        wrapper.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#667eea';
            this.style.background = 'rgba(102, 126, 234, 0.05)';
        });
        
        wrapper.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#dee2e6';
            this.style.background = '#f8f9fa';
        });
        
        wrapper.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#dee2e6';
            this.style.background = '#f8f9fa';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
}

// Form validation setup
function setupFormValidation() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
}

// Field validation
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Required validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Phone validation (basic)
    if (field.name === 'phone' && value) {
        const phoneRegex = /^[+]?[\d\s\-\(\)]+$/;
        if (!phoneRegex.test(value) || value.length < 8) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // Update field styling
    field.classList.remove('is-valid', 'is-invalid');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.invalid-feedback');
    if (existingError) existingError.remove();
    
    if (isValid) {
        if (value) field.classList.add('is-valid');
    } else {
        field.classList.add('is-invalid');
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = errorMessage;
        field.parentNode.appendChild(errorDiv);
    }
    
    return isValid;
}

// Form submission setup
function setupFormSubmission() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    const submitBtn = document.querySelector('button[form="registrationForm"]') || form.querySelector('button[type="submit"]');
    if (!submitBtn) {
        console.error('Registration submit button not found.');
        return;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all fields
        const inputs = form.querySelectorAll('input[required], select[required]');
        let isFormValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isFormValid = false;
            }
        });
        
        if (!isFormValid) {
            showNotification('Please fix all errors before submitting', 'danger');
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            return;
        }

        const courseSelect = document.getElementById('course_id');
        const selectedFee = courseSelect.options[courseSelect.selectedIndex]?.getAttribute('data-fee');
        if (!selectedFee || selectedFee === '0') {
            showNotification('Please select a course with a registration fee before proceeding.', 'danger');
            courseSelect.focus();
            return;
        }

        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-hourglass-split me-2"></i>Redirecting to payment...';

        showNotification('Redirecting to payment gateway...', 'info');

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            let data = null;

            if (contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                throw new Error(text || 'Payment gateway initialization returned an unexpected response.');
            }

            if (!response.ok) {
                if (response.status === 422 && data?.errors) {
                    // Show per-field validation errors returned from server
                    Object.keys(data.errors).forEach(fieldName => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        const messages = data.errors[fieldName];
                        const message = Array.isArray(messages) ? messages.join(' ') : messages;
                        if (field) {
                            field.classList.remove('is-valid');
                            field.classList.add('is-invalid');
                            const existing = field.parentNode.querySelector('.invalid-feedback');
                            if (existing) existing.remove();
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = message;
                            field.parentNode.appendChild(errorDiv);
                        }
                    });

                    const firstErrorField = form.querySelector('.is-invalid');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorField.focus();
                    }

                    const combined = Object.values(data.errors).flat().join(' ');
                    showPaymentErrorBanner(combined);
                    throw new Error(combined);
                }

                const message = data?.message || 'Payment initialization failed. Please try again.';
                showPaymentErrorBanner(message);
                throw new Error(message);
            }

            if (data.status === 'success' && data.data?.payment_link) {
                hidePaymentErrorBanner();
                window.location.href = data.data.payment_link;
                return;
            }

            const message = data.message || 'Unable to initialize payment.';
            showPaymentErrorBanner(message);
            throw new Error(message);
        })
        .catch(error => {
            console.error('Payment init error:', error);
            showNotification(error.message || 'Payment initialization failed. Please try again.', 'danger');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });
}

// Auto-save functionality
function setupAutoSave() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input, select');
    let autoSaveTimer;
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveFormData();
            }, 2000);
        });
    });
    
    // Restore saved data on page load
    restoreFormData();
}

// Save form data to localStorage
function saveFormData() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        if (value) data[key] = value;
    }
    
    localStorage.setItem('studentRegistrationDraft', JSON.stringify(data));
    console.log('Form data auto-saved');
}

// Restore form data from localStorage
function restoreFormData() {
    const savedData = localStorage.getItem('studentRegistrationDraft');
    if (!savedData) return;
    
    try {
        const data = JSON.parse(savedData);
        const form = document.getElementById('registrationForm');
        if (!form) return;

        let hasRestoredData = false;
        
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (!input || input.type === 'file' || input.type === 'password') {
                return;
            }

            if (!input.value) {
                input.value = data[key];
                
                // Trigger change events for dependent fields
                if (key === 'academic_year_id') {
                    input.dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        if (data.branch_id) {
                            const branchInput = form.querySelector('[name="branch_id"]');
                            if (branchInput) {
                                branchInput.value = data.branch_id;
                                branchInput.dispatchEvent(new Event('change'));
                                setTimeout(() => {
                                    if (data.course_id) {
                                        const courseInput = form.querySelector('[name="course_id"]');
                                        if (courseInput) {
                                            courseInput.value = data.course_id;
                                            courseInput.dispatchEvent(new Event('change'));
                                        }
                                    }
                                }, 500);
                            }
                        }
                    }, 500);
                }
                
                hasRestoredData = true;
            }
        });
        
        if (hasRestoredData) {
            showNotification('Previous form data has been restored', 'info');
        }
    } catch (error) {
        console.error('Error restoring form data:', error);
    }
}

// Clear saved data on successful submission
window.addEventListener('beforeunload', function() {
    const submitBtn = document.querySelector('button[form="registrationForm"]') || document.querySelector('button[type="submit"]');
    if (submitBtn && submitBtn.classList.contains('loading')) {
        localStorage.removeItem('studentRegistrationDraft');
    }
});

// Loading states
function showLoading(element) {
    element.disabled = true;
    element.style.opacity = '0.6';
    element.style.position = 'relative';
    
    if (!element.querySelector('.loading-spinner')) {
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);';
        spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        element.appendChild(spinner);
    }
}

function hideLoading(element) {
    element.disabled = false;
    element.style.opacity = '1';
    
    const spinner = element.querySelector('.loading-spinner');
    if (spinner) spinner.remove();
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
    
    const icon = type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle';
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${icon} me-2" style="font-size: 1.2rem;"></i>
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

function showPaymentErrorBanner(message) {
    const banner = document.getElementById('paymentErrorBanner');
    const bannerText = document.getElementById('paymentErrorBannerText');
    if (!banner || !bannerText) return;
    bannerText.textContent = message || 'Please try again or contact support if the issue persists.';
    banner.classList.remove('d-none');
    banner.style.display = 'block';
}

function hidePaymentErrorBanner() {
    const banner = document.getElementById('paymentErrorBanner');
    if (!banner) return;
    banner.classList.add('d-none');
    banner.style.display = 'none';
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+Enter to submit form
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            submitBtn.click();
        }
    }
});
</script>
