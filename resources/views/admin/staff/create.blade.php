@extends('admin.layout')
@section('title', 'Add Staff Member')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-5">
                    <!-- Header Section -->
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-plus text-white fs-1"></i>
                        </div>
                        <h2 class="card-title fw-bold text-dark mb-2">Create New Staff Member</h2>
                        <p class="text-muted">Add a new team member to your organization</p>
                    </div>

                    <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Name Field -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold text-dark">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control form-control-lg border-2 rounded-3" 
                                   placeholder="Enter full name" 
                                   required
                                   style="border-color: #dee2e6; transition: all 0.3s ease;">
                        </div>

                        <!-- Email Field -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-dark">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control form-control-lg border-2 rounded-3" 
                                   placeholder="Enter email address" 
                                   required
                                   style="border-color: #dee2e6; transition: all 0.3s ease;">
                            <div class="invalid-feedback">
                                Please provide a valid email address.
                            </div>
                        </div>

                        <!-- Phone Field -->
                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold text-dark">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="te                                              l" 
                                   name="phone" 
                                   id="phone" 
                                   class="form-control form-control-lg border-2 rounded-3" 
                                   placeholder="Enter phone number" 
                                   required
                                   pattern="[0-9+\-\s\(\)]{10,15}"
                                   style="border-color: #dee2e6; transition: all 0.3s ease;">
                            <div class="invalid-feedback">
                                Please provide a valid phone number (10-15 digits).
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Format: +250123456789 
                            </div>
                        </div>

                        <!-- Position Field (renamed from Role) -->
                        <div class="mb-4">
                            <label for="position" class="form-label fw-semibold text-dark">
                                <i class="fas fa-briefcase me-2 text-primary"></i>
                                Position <span class="text-danger">*</span>
                            </label>
                            <select name="role" 
                                    id="position" 
                                    class="form-select form-select-lg border-2 rounded-3" 
                                    required
                                    style="border-color: #dee2e6; transition: all 0.3s ease;">
                                <option value="">-- Select a position --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                        {{ $role === 'DOS' ? 'Director of Studies (DOS)' : ($role === 'Accountant' ? 'Accountant' : ($role === 'IT' ? 'IT Support' : $role)) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a position.
                            </div>
                        </div>

                        <!-- Branch Assignment Field -->
                        <div class="mb-4">
                            <label for="branches" class="form-label fw-semibold text-dark">
                                <i class="fas fa-building me-2 text-primary"></i>
                                Assign to Branches <span class="text-danger">*</span>
                            </label>
                            <div class="border border-2 rounded-3 p-3" style="border-color: #dee2e6; max-height: 200px; overflow-y: auto;">
                                @foreach($branches as $branch)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="branches[]" 
                                           value="{{ $branch->id }}" 
                                           id="branch_{{ $branch->id }}"
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="branch_{{ $branch->id }}" style="cursor: pointer;">
                                        <strong>{{ $branch->name }}</strong>
                                        <small class="text-muted d-block">{{ $branch->location }}</small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Select one or more branches where this staff member will work
                            </div>
                            <div class="invalid-feedback">
                                Please select at least one branch.
                            </div>
                        </div>

                        <!-- Photo Upload Field -->
                        <div class="mb-4">
                            <label for="photo" class="form-label fw-semibold text-dark">
                                <i class="fas fa-camera me-2 text-primary"></i>
                                Staff Photo <span class="text-muted small">(Optional)</span>
                            </label>
                            <div class="border border-2 border-dashed rounded-3 p-4 text-center position-relative" 
                                 id="upload-area" 
                                 style="border-color: #dee2e6; cursor: pointer; transition: all 0.3s ease; background-color: #f8f9fa;">
                                <input type="file" name="photo" id="photo" accept="image/*" class="d-none">
                                <div id="upload-content">
                                    <i class="fas fa-cloud-upload-alt text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-dark fw-semibold mb-2">Click to upload photo</p>
                                    <p class="text-muted small">PNG, JPG, JPEG up to 1MB</p>
                                </div>
                                <div id="image-preview" class="d-none">
                                    <img id="preview-img" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;">
                                    <div class="mt-3">
                                        <button type="button" id="remove-image" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Remove Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="file-info" class="mt-2 text-muted small d-none"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                               <button type="button" 
    onclick="window.location.href='{{ route('admin.staff.index') }}'" 
    class="btn btn-outline-secondary btn-lg w-100 rounded-3">
    <i class="fas fa-arrow-left me-2"></i>
    Cancel
</button>


                            </div>
                            <div class="col-md-6">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg w-100 rounded-3">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Create Staff Member
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Error Messages -->
                    @if ($errors->any())
                    <div class="alert alert-danger mt-4 rounded-3">
                        <div class="d-flex">
                            <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                            <div>
                                <h6 class="alert-heading fw-bold">Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Success Message -->
                    @if(session('success'))
                    <div class="alert alert-success mt-4 rounded-3">
                        <div class="d-flex">
                            <i class="fas fa-check-circle me-3 mt-1"></i>
                            <div>
                                <p class="mb-0 fw-semibold">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('photo');
    const uploadArea = document.getElementById('upload-area');
    const uploadContent = document.getElementById('upload-content');
    const fileInfo = document.getElementById('file-info');
    
    // File upload functionality
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file (PNG, JPG, JPEG)');
                this.value = '';
                return;
            }
            
            // Validate file size (1MB = 1024 * 1024 bytes)
            if (file.size > 1024 * 1024) {
                alert('File size must be less than 1MB');
                this.value = '';
                return;
            }
            
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('upload-content').classList.add('d-none');
                document.getElementById('image-preview').classList.remove('d-none');
            };
            reader.readAsDataURL(file);
            
            fileInfo.innerHTML = `<strong>Selected:</strong> ${fileName} (${fileSize})`;
            fileInfo.classList.remove('d-none');
            
            uploadArea.style.borderColor = '#198754';
            uploadArea.style.backgroundColor = '#d1e7dd';
        }
    });
    
    // Remove image functionality
    document.getElementById('remove-image').addEventListener('click', function() {
        fileInput.value = '';
        document.getElementById('upload-content').classList.remove('d-none');
        document.getElementById('image-preview').classList.add('d-none');
        fileInfo.classList.add('d-none');
        uploadArea.style.borderColor = '#dee2e6';
        uploadArea.style.backgroundColor = '#f8f9fa';
    });

    // Hover effects for upload area
    uploadArea.addEventListener('mouseenter', function() {
        if (!fileInput.files[0]) {
            this.style.borderColor = '#0d6efd';
            this.style.backgroundColor = '#e7f1ff';
        }
    });
    
    uploadArea.addEventListener('mouseleave', function() {
        if (!fileInput.files[0]) {
            this.style.borderColor = '#dee2e6';
            this.style.backgroundColor = '#f8f9fa';
        }
    });

    // Enhanced form validation
    const form = document.querySelector('form');
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    const branchCheckboxes = form.querySelectorAll('input[name="branches[]"]');
    
    // Email validation
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    
    emailInput.addEventListener('input', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value && !emailRegex.test(this.value)) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Phone validation
    phoneInput.addEventListener('input', function() {
        // Remove non-numeric characters for validation
        const phoneRegex = /^[\+]?[0-9\-\s\(\)]{10,15}$/;
        if (this.value && !phoneRegex.test(this.value)) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Format phone number as user types
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        
        // Rwanda phone number formatting
        if (value.startsWith('250')) {
            value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{3})$/, '+$1 $2 $3 $4');
        } else if (value.startsWith('0')) {
            value = value.replace(/^(\d{1})(\d{3})(\d{3})(\d{3})$/, '$1$2 $3 $4');
        }
        
        e.target.value = value;
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate required inputs
        requiredInputs.forEach(input => {
            if (input.value.trim() === '') {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                // Additional validation for email
                if (input.type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value)) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        return;
                    }
                }
                
                // Additional validation for phone
                if (input.type === 'tel') {
                    const phoneRegex = /^[\+]?[0-9\-\s\(\)]{10,15}$/;
                    if (!phoneRegex.test(input.value)) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        return;
                    }
                }
                
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });
        
        // Validate branch selection
        const selectedBranches = Array.from(branchCheckboxes).filter(cb => cb.checked);
        if (selectedBranches.length === 0) {
            // Show error for branch selection
            const branchContainer = document.querySelector('.form-check-input[name="branches[]"]').closest('.mb-4');
            const invalidFeedback = branchContainer.querySelector('.invalid-feedback');
            if (invalidFeedback) {
                invalidFeedback.style.display = 'block';
            }
            isValid = false;
        } else {
            // Clear branch validation error
            const branchContainer = document.querySelector('.form-check-input[name="branches[]"]').closest('.mb-4');
            const invalidFeedback = branchContainer.querySelector('.invalid-feedback');
            if (invalidFeedback) {
                invalidFeedback.style.display = 'none';
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            // Show Bootstrap toast or alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please fill in all required fields correctly.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.appendChild(alertDiv);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
            
            // Scroll to first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });
    
    // Remove validation classes on input
    requiredInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        // Focus effects
        input.addEventListener('focus', function() {
            this.style.borderColor = '#0d6efd';
            this.style.boxShadow = '0 0 0 0.25rem rgba(13, 110, 253, 0.25)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '#dee2e6';
            this.style.boxShadow = 'none';
        });
    });
    
    // Branch selection feedback
    branchCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedBranches = Array.from(branchCheckboxes).filter(cb => cb.checked);
            const branchContainer = this.closest('.mb-4');
            const invalidFeedback = branchContainer.querySelector('.invalid-feedback');
            
            if (selectedBranches.length > 0) {
                if (invalidFeedback) {
                    invalidFeedback.style.display = 'none';
                }
                // Add visual feedback for selected branches
                branchCheckboxes.forEach(cb => {
                    const label = cb.nextElementSibling;
                    if (cb.checked) {
                        label.style.color = '#198754';
                        label.style.fontWeight = 'bold';
                    } else {
                        label.style.color = '';
                        label.style.fontWeight = '';
                    }
                });
            } else {
                if (invalidFeedback) {
                    invalidFeedback.style.display = 'block';
                }
            }
        });
    });
});
</script>

<style>
/* Custom styles for better visual appeal */
.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
}

#upload-area:hover {
    transform: translateY(-2px);
}

.alert {
    border: none;
}
</style>
@endsection