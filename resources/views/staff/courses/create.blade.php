@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- Header Section -->
            <div class="mb-4">
                <h2 class="text-dark fw-bold mb-1">
                    <i class="fas fa-plus me-2 text-primary"></i>
                    Create New Course
                </h2>
                <p class="text-muted mb-0">Add a new course to the system</p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Course Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dos.courses.store') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Course Name -->
                        <div class="mb-3">
                            <label for="course_name" class="form-label fw-semibold">
                                <i class="fas fa-book me-2 text-primary"></i>Course Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('course_name') is-invalid @enderror" 
                                   id="course_name" 
                                   name="course_name" 
                                   value="{{ old('course_name') }}"
                                   placeholder="Enter course name"
                                   required>
                            @error('course_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Code -->
                        <div class="mb-3">
                            <label for="course_code" class="form-label fw-semibold">
                                <i class="fas fa-barcode me-2 text-primary"></i>Course Code
                            </label>
                            <input type="text" 
                                   class="form-control @error('course_code') is-invalid @enderror" 
                                   id="course_code" 
                                   name="course_code" 
                                   value="{{ old('course_code') }}"
                                   placeholder="e.g., NIT15330"
                                   required>
                            @error('course_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Abbreviation -->
                        <div class="mb-3">
                            <label for="abbreviation" class="form-label fw-semibold">
                                <i class="fas fa-compress me-2 text-primary"></i>Abbreviation
                            </label>
                            <input type="text" 
                                   class="form-control @error('abbreviation') is-invalid @enderror" 
                                   id="abbreviation" 
                                   name="abbreviation" 
                                   value="{{ old('abbreviation') }}"
                                   placeholder="e.g., NIT"
                                   maxlength="10"
                                   required>
                            @error('abbreviation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Type -->
                        <div class="mb-3">
                            <label for="course_type" class="form-label fw-semibold">
                                <i class="fas fa-tag me-2 text-primary"></i>Course Type
                            </label>
                            <input type="text" 
                                   class="form-control @error('course_type') is-invalid @enderror" 
                                   id="course_type" 
                                   name="course_type" 
                                   value="{{ old('course_type') }}"
                                   placeholder="e.g., RTB Trade, Diploma">
                            @error('course_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Registration Fee -->
                        <div class="mb-3">
                            <label for="registration_fee" class="form-label fw-semibold">
                                <i class="fas fa-money-bill me-2 text-success"></i>Registration Fee
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">RWF</span>
                                <input type="number" 
                                       class="form-control @error('registration_fee') is-invalid @enderror" 
                                       id="registration_fee" 
                                       name="registration_fee" 
                                       value="{{ old('registration_fee', 0) }}"
                                       step="0.01"
                                       min="0">
                                @error('registration_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tuition Fee -->
                        <div class="mb-3">
                            <label for="tuition_fee" class="form-label fw-semibold">
                                <i class="fas fa-money-bill me-2 text-success"></i>Tuition Fee
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">RWF</span>
                                <input type="number" 
                                       class="form-control @error('tuition_fee') is-invalid @enderror" 
                                       id="tuition_fee" 
                                       name="tuition_fee" 
                                       value="{{ old('tuition_fee', 0) }}"
                                       step="0.01"
                                       min="0">
                                @error('tuition_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Practical Fee -->
                        <div class="mb-3">
                            <label for="practical_fee" class="form-label fw-semibold">
                                <i class="fas fa-money-bill me-2 text-success"></i>Practical Fee
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">RWF</span>
                                <input type="number" 
                                       class="form-control @error('practical_fee') is-invalid @enderror" 
                                       id="practical_fee" 
                                       name="practical_fee" 
                                       value="{{ old('practical_fee', 0) }}"
                                       step="0.01"
                                       min="0">
                                @error('practical_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Trip Fee -->
                        <div class="mb-3">
                            <label for="trip_fee" class="form-label fw-semibold">
                                <i class="fas fa-money-bill me-2 text-success"></i>Trip Fee (Optional)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">RWF</span>
                                <input type="number" 
                                       class="form-control @error('trip_fee') is-invalid @enderror" 
                                       id="trip_fee" 
                                       name="trip_fee" 
                                       value="{{ old('trip_fee') }}"
                                       step="0.01"
                                       min="0">
                                @error('trip_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Discount Fee -->
                        <div class="mb-3">
                            <label for="discount_fee" class="form-label fw-semibold">
                                <i class="fas fa-money-bill me-2 text-success"></i>Discount Fee (Optional)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">RWF</span>
                                <input type="number" 
                                       class="form-control @error('discount_fee') is-invalid @enderror" 
                                       id="discount_fee" 
                                       name="discount_fee" 
                                       value="{{ old('discount_fee') }}"
                                       step="0.01"
                                       min="0">
                                @error('discount_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="fas fa-save me-2"></i>Create Course
                            </button>
                            <a href="{{ route('dos.courses.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back Link -->
            <div class="mt-3">
                <a href="{{ route('dos.courses.index') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to Courses
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .form-label {
        margin-bottom: 0.5rem;
        color: #333;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>

<script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endsection
