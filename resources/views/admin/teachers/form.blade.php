@extends('admin.layout')

@section('title', 'Add Teacher')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-plus text-primary fs-2"></i>
                </div>
                <h1 class="h2 fw-bold text-dark mb-2">Add New Teacher</h1>
                <p class="text-muted mb-0">Fill in the information below to add a new teacher to the system</p>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data" id="teacherForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Teacher Name Field -->
                                <div class="mb-4">
                                    <label for="teacher_name" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-user text-primary me-2"></i>Teacher Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               name="teacher_name" 
                                               id="teacher_name" 
                                               class="form-control border-start-0 ps-0 @error('teacher_name') is-invalid @enderror" 
                                               value="{{ old('teacher_name') }}" 
                                               placeholder="Enter teacher's full name"
                                               required>
                                    </div>
                                    @error('teacher_name')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Teacher ID Number (Auto-generated, Read-only) -->
                                <div class="mb-4">
                                    <label for="teacher_id_number" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-id-badge text-primary me-2"></i>Teacher ID Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-hashtag text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               name="teacher_id_number" 
                                               id="teacher_id_number" 
                                               class="form-control border-start-0 ps-0" 
                                               value="{{ old('teacher_id_number', $generated_teacher_id ?? $teacher->teacher_id_number ?? '') }}" 
                                               placeholder="Auto-generated"
                                               readonly>
                                    </div>
                                    <small class="text-muted">This ID is generated automatically.</small>
                                </div>

                                <!-- National ID Number Field -->
                                <div class="mb-4">
                                    <label for="id_number" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-id-card text-primary me-2"></i>National ID Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-hashtag text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               name="id_number" 
                                               id="id_number" 
                                               class="form-control border-start-0 ps-0 @error('id_number') is-invalid @enderror" 
                                               value="{{ old('id_number') }}" 
                                               placeholder="Enter national ID number"
                                               required>
                                    </div>
                                    @error('id_number')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Telephone Field -->
                                <div class="mb-4">
                                    <label for="telephone" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-phone text-primary me-2"></i>Telephone
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="tel" 
                                               name="telephone" 
                                               id="telephone" 
                                               class="form-control border-start-0 ps-0 @error('telephone') is-invalid @enderror" 
                                               value="{{ old('telephone') }}" 
                                               placeholder="Enter phone number"
                                               required>
                                    </div>
                                    @error('telephone')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email Field -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-envelope text-primary me-2"></i>Email Address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-at text-muted"></i>
                                        </span>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                               value="{{ old('email') }}" 
                                               placeholder="Enter email address"
                                               required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Photo Upload Field -->
                                <div class="mb-4">
                                    <label for="photo" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-camera text-primary me-2"></i>Passport Photo
                                        <span class="text-danger">*</span>
                                        <small class="text-muted">(Max: 10KB)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-image text-muted"></i>
                                        </span>
                                        <input type="file" 
                                               name="photo" 
                                               id="photo" 
                                               class="form-control border-start-0 @error('photo') is-invalid @enderror" 
                                               accept="image/*"
                                               required>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Upload a passport-size photo (JPEG, PNG). Maximum file size: 10KB
                                    </small>
                                    @error('photo')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Branch Field -->
                                <div class="mb-4">
                                    <label for="branch_id" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>Branch
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-location-dot text-muted"></i>
                                        </span>
                                        <select name="branch_ids[]" 
                                                id="branch_id" 
                                                class="form-select border-start-0 @error('branch_ids') is-invalid @enderror" 
                                                multiple
                                                required>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ in_array($branch->id, old('branch_ids', $assignedBranches ?? [])) ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple branches.</small>
                                    @error('branch_ids')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Department Field -->
                                <div class="mb-4">
                                    <label for="department" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-building text-primary me-2"></i>Department
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-sitemap text-muted"></i>
                                        </span>
                                        <select name="department" 
                                                id="department" 
                                                class="form-select border-start-0 @error('department') is-invalid @enderror" 
                                                required>
                                            <option value="">Select department</option>
                                            <option value="All" {{ old('department') == 'All' ? 'selected' : '' }}>All Departments</option>
                                            <option value="REB Combinations" {{ old('department') == 'REB Combinations' ? 'selected' : '' }}>REB Combinations</option>
                                            <option value="RTB Trades" {{ old('department') == 'RTB Trades' ? 'selected' : '' }}>RTB Trades</option>
                                            <option value="Training" {{ old('department') == 'Training' ? 'selected' : '' }}>Training</option>
                                            <option value="Internship" {{ old('department') == 'Internship' ? 'selected' : '' }}>Internship</option>
                                        </select>
                                    </div>
                                    @error('department')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Employment Type Field -->
                                <div class="mb-4">
                                    <label for="employment_type" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-briefcase text-primary me-2"></i>Employment Type
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user-tie text-muted"></i>
                                        </span>
                                        <select name="employment_type" 
                                                id="employment_type" 
                                                class="form-select border-start-0 @error('employment_type') is-invalid @enderror" 
                                                required>
                                            <option value="">Select employment type</option>
                                            <option value="Permanent" {{ old('employment_type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                            <option value="Short Term" {{ old('employment_type') == 'Short Term' ? 'selected' : '' }}>Short Term</option>
                                            <option value="Casual" {{ old('employment_type') == 'Casual' ? 'selected' : '' }}>Casual</option>
                                        </select>
                                    </div>
                                    @error('employment_type')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Level of Education Field -->
                                <div class="mb-4">
                                    <label for="level_of_education" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-graduation-cap text-primary me-2"></i>Level of Education
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-university text-muted"></i>
                                        </span>
                                        <select name="level_of_education" 
                                                id="level_of_education" 
                                                class="form-select border-start-0 @error('level_of_education') is-invalid @enderror" 
                                                required>
                                            <option value="">Select education level</option>
                                            <option value="Bachelor's Degree" {{ old('level_of_education') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                            <option value="Master's Degree" {{ old('level_of_education') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                            <option value="PhD" {{ old('level_of_education') == 'PhD' ? 'selected' : '' }}>PhD</option>
                                            <option value="Diploma" {{ old('level_of_education') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                            <option value="Certificate" {{ old('level_of_education') == 'Certificate' ? 'selected' : '' }}>Certificate</option>
                                            <option value="Other" {{ old('level_of_education') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    @error('level_of_education')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Salary Field -->
                                <div class="mb-5">
                                    <label for="salary" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-dollar-sign text-primary me-2"></i>
                                        <span id="salary-label">Salary</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-money-bill text-muted"></i>
                                        </span>
                                        <input type="number" 
                                               name="salary" 
                                               id="salary" 
                                               class="form-control border-start-0 ps-0 @error('salary') is-invalid @enderror" 
                                               value="{{ old('salary') }}" 
                                               placeholder="Enter amount"
                                               step="0.01"
                                               min="0"
                                               required>
                                        <span class="input-group-text bg-light border-start-0">
                                            RWF
                                        </span>
                                    </div>
                                    <small id="salary-help" class="text-muted mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="salary-help-text">Enter the salary amount</span>
                                    </small>
                                    @error('salary')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i>Save Teacher
                            </button>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-lg px-5 rounded-pill">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Text -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Fields marked with <span class="text-danger">*</span> are required
                </small>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for enhanced form styling */
.form-control, .form-select {
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    transform: translateY(-1px);
}

.input-group-text {
    border-radius: 0.5rem 0 0 0.5rem;
    border: 2px solid #e9ecef;
    border-right: none;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.input-group .form-control {
    border-radius: 0 0.5rem 0.5rem 0;
}

.input-group .form-select {
    border-radius: 0 0.5rem 0.5rem 0;
}

.input-group .form-control:not(:last-child) {
    border-radius: 0;
}

.input-group .input-group-text:last-child {
    border-radius: 0 0.5rem 0.5rem 0;
    border-left: none;
    border-right: 2px solid #e9ecef;
}

.btn {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card {
    transition: all 0.3s ease;
}

.form-label {
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.invalid-feedback {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

/* File upload styling */
.form-control[type="file"] {
    padding: 0.5rem 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 2rem 1.5rem;
    }
    
    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1rem;
    }
    
    .d-grid.gap-3.d-md-flex .btn {
        margin-bottom: 0.5rem;
    }
}

/* Animation for form validation */
.is-invalid {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-5px);
    }
}

/* Loading state for submit button */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 1rem;
    height: 1rem;
    top: 50%;
    left: 50%;
    margin-left: -0.5rem;
    margin-top: -0.5rem;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Photo preview styling */
.photo-preview {
    max-width: 150px;
    max-height: 150px;
    border-radius: 0.5rem;
    border: 2px solid #e9ecef;
    margin-top: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('teacherForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Add loading state to submit button
    form.addEventListener('submit', function() {
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('telephone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.substring(0, 10);
        }
        e.target.value = value;
    });
    
    // Photo file size validation
    const photoInput = document.getElementById('photo');
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileSize = file.size / 1024; // Convert to KB
            if (fileSize > 100) {
                alert('File size must be less than 100KB. Please choose a smaller image.');
                e.target.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.querySelector('.photo-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.classList.add('photo-preview');
                    photoInput.parentNode.parentNode.appendChild(preview);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Salary formatting
    const salaryInput = document.getElementById('salary');
    salaryInput.addEventListener('input', function(e) {
        let value = e.target.value;
        // Remove any non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, '');
        // Ensure only one decimal point
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        e.target.value = value;
    });
    
    // Real-time validation feedback
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Employment type change handler for salary field
    const employmentTypeSelect = document.getElementById('employment_type');
    const salaryLabel = document.getElementById('salary-label');
    const salaryInput = document.getElementById('salary');
    const salaryHelpText = document.getElementById('salary-help-text');
    
    function updateSalaryField() {
        const employmentType = employmentTypeSelect.value;
        
        if (employmentType === 'Permanent') {
            salaryLabel.textContent = 'Monthly Salary';
            salaryInput.placeholder = 'Enter monthly salary amount';
            salaryHelpText.textContent = 'Enter the monthly salary amount';
        } else if (employmentType === 'Casual') {
            salaryLabel.textContent = 'Hourly Rate';
            salaryInput.placeholder = 'Enter hourly rate';
            salaryHelpText.textContent = 'Enter the hourly rate (salary will be calculated based on hours worked per day)';
        } else if (employmentType === 'Short Term') {
            salaryLabel.textContent = 'Salary';
            salaryInput.placeholder = 'Enter salary amount';
            salaryHelpText.textContent = 'Enter the salary amount';
        } else {
            salaryLabel.textContent = 'Salary';
            salaryInput.placeholder = 'Enter amount';
            salaryHelpText.textContent = 'Enter the salary amount';
        }
    }
    
    // Initialize salary field based on current selection
    updateSalaryField();
    
    // Update salary field when employment type changes
    employmentTypeSelect.addEventListener('change', updateSalaryField);
});
</script>
@endsection