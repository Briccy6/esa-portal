@extends('dos.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('dos.courses.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to Courses
                </a>
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        Edit Course
                    </h2>
                    <p class="text-muted mb-0">Update course information and details</p>
                </div>
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

            <!-- Edit Form Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-form me-2"></i>
                        Course Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('dos.courses.update', $course) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Course Name -->
                            <div class="col-md-6 mb-3">
                                <label for="course_name" class="form-label fw-semibold">
                                    Course Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="course_name" 
                                       name="course_name" 
                                       class="form-control form-control-lg @error('course_name') is-invalid @enderror" 
                                       value="{{ old('course_name', $course->course_name) }}"
                                       placeholder="Enter course name"
                                       required>
                                @error('course_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Course Code -->
                            <div class="col-md-6 mb-3">
                                <label for="course_code" class="form-label fw-semibold">
                                    Course Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="course_code" 
                                       name="course_code" 
                                       class="form-control form-control-lg @error('course_code') is-invalid @enderror" 
                                       value="{{ old('course_code', $course->course_code) }}"
                                       placeholder="Enter course code"
                                       required>
                                @error('course_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Abbreviation -->
                            <div class="col-md-6 mb-3">
                                <label for="abbreviation" class="form-label fw-semibold">
                                    Abbreviation <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="abbreviation" 
                                       name="abbreviation" 
                                       class="form-control form-control-lg @error('abbreviation') is-invalid @enderror" 
                                       value="{{ old('abbreviation', $course->abbreviation) }}"
                                       placeholder="e.g., MCB"
                                       required>
                                @error('abbreviation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Course Type -->
                            <div class="col-md-6 mb-3">
                                <label for="course_type" class="form-label fw-semibold">
                                    Course Type <span class="text-danger">*</span>
                                </label>
                                <select id="course_type" 
                                        name="course_type" 
                                        class="form-select form-select-lg @error('course_type') is-invalid @enderror"
                                        required>
                                    <option value="">-- Select Course Type --</option>
                                    <option value="single" {{ old('course_type', $course->course_type) === 'single' ? 'selected' : '' }}>Single Subject</option>
                                    <option value="combination" {{ old('course_type', $course->course_type) === 'combination' ? 'selected' : '' }}>Combination</option>
                                    <option value="reb combination" {{ old('course_type', $course->course_type) === 'reb combination' ? 'selected' : '' }}>REB Combination</option>
                                </select>
                                @error('course_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Enter course description (optional)">{{ old('description', $course->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fee Information -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-header bg-info bg-opacity-10 border-info">
                                <h6 class="mb-0">
                                    <i class="fas fa-money-bill-wave me-2 text-info"></i>
                                    Fee Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Registration Fee -->
                                    <div class="col-md-6 mb-3">
                                        <label for="registration_fee" class="form-label fw-semibold">
                                            Registration Fee
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" 
                                                   id="registration_fee" 
                                                   name="registration_fee" 
                                                   step="0.01"
                                                   class="form-control bg-light @error('registration_fee') is-invalid @enderror" 
                                                   value="{{ old('registration_fee', $course->registration_fee ?? 0) }}"
                                                   placeholder="0.00"
                                                   readonly>
                                            @error('registration_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Tuition Fee -->
                                    <div class="col-md-6 mb-3">
                                        <label for="tuition_fee" class="form-label fw-semibold">
                                            Tuition Fee
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" 
                                                   id="tuition_fee" 
                                                   name="tuition_fee" 
                                                   step="0.01"
                                                   class="form-control bg-light @error('tuition_fee') is-invalid @enderror" 
                                                   value="{{ old('tuition_fee', $course->tuition_fee ?? 0) }}"
                                                   placeholder="0.00"
                                                   readonly>
                                            @error('tuition_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Practical Fee -->
                                    <div class="col-md-6 mb-3">
                                        <label for="practical_fee" class="form-label fw-semibold">
                                            Practical Fee
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" 
                                                   id="practical_fee" 
                                                   name="practical_fee" 
                                                   step="0.01"
                                                   class="form-control bg-light @error('practical_fee') is-invalid @enderror" 
                                                   value="{{ old('practical_fee', $course->practical_fee ?? 0) }}"
                                                   placeholder="0.00"
                                                   readonly>
                                            @error('practical_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Trip Fee -->
                                    <div class="col-md-6 mb-3">
                                        <label for="trip_fee" class="form-label fw-semibold">
                                            Trip Fee
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" 
                                                   id="trip_fee" 
                                                   name="trip_fee" 
                                                   step="0.01"
                                                   class="form-control bg-light @error('trip_fee') is-invalid @enderror" 
                                                   value="{{ old('trip_fee', $course->trip_fee ?? 0) }}"
                                                   placeholder="0.00"
                                                   readonly>
                                            @error('trip_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Discount Fee -->
                                    <div class="col-md-6 mb-3">
                                        <label for="discount_fee" class="form-label fw-semibold">
                                            Discount Fee
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" 
                                                   id="discount_fee" 
                                                   name="discount_fee" 
                                                   step="0.01"
                                                   class="form-control bg-light @error('discount_fee') is-invalid @enderror" 
                                                   value="{{ old('discount_fee', $course->discount_fee ?? 0) }}"
                                                   placeholder="0.00"
                                                   readonly>
                                            @error('discount_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Total Cost (Read-only) -->
                                    <div class="col-md-6 mb-3">
                                        <label for="total_cost" class="form-label fw-semibold">
                                            Total Cost
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">RWF</span>
                                            <input type="number" 
                                                   id="total_cost" 
                                                   class="form-control bg-light" 
                                                   value="{{ old('total_cost', $course->total_cost ?? 0) }}"
                                                   readonly>
                                        </div>
                                        <small class="text-muted">Calculated automatically</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('dos.courses.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <a href="{{ route('dos.courses.show', $course) }}" class="btn btn-info btn-lg">
                                <i class="fas fa-eye me-2"></i>View
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Note About Teachers -->
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Teachers are assigned to <strong>lessons</strong>, not courses. View the course to manage lesson-teacher assignments through the lesson branch course pivot relationships.
            </div>
        </div>
    </div>
</div>

<script>
    // Calculate total cost automatically
    document.addEventListener('DOMContentLoaded', function() {
        const fees = ['registration_fee', 'tuition_fee', 'practical_fee', 'trip_fee'];
        const discountInput = document.getElementById('discount_fee');
        const totalInput = document.getElementById('total_cost');

        function calculateTotal() {
            let total = 0;
            fees.forEach(fee => {
                const value = parseFloat(document.getElementById(fee).value) || 0;
                total += value;
            });
            const discount = parseFloat(discountInput.value) || 0;
            total -= discount;
            totalInput.value = total.toFixed(2);
        }

        fees.forEach(fee => {
            document.getElementById(fee).addEventListener('input', calculateTotal);
        });
        discountInput.addEventListener('input', calculateTotal);

        // Initial calculation
        calculateTotal();
    });
</script>
@endsection
