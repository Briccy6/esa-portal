@extends('admin.layout')

@section('title', 'Create Term')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <!-- Header Section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-calendar-plus fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-1 fw-bold">Create {{ $nextTerm }}</h2>
                            <p class="mb-0 opacity-90">Set up the next academic term</p>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-0 mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Error:</strong> {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Messages -->
                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-0 mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <strong>Success:</strong> {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="alert alert-warning border-0 rounded-0 mb-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.terms.store') }}" method="POST" class="needs-validation" id="termForm">
                        @csrf

                        <!-- Academic Year Field -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-graduation-cap me-2 text-primary"></i>Academic Year
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light" value="{{ $academicYear->year }}" readonly>
                                <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                                <input type="hidden" name="name" value="{{ $nextTerm }}">
                            </div>
                        </div>

                        <!-- Date Fields Row -->
                        <div class="row g-3">
                            <!-- Starting Date -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="start_date" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-play-circle me-2 text-success"></i>Starting Date
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0">
                                            <i class="fas fa-calendar-day text-success"></i>
                                        </span>
                                        <input 
                                            type="date"
                                            name="start_date"
                                            id="start_date"
                                            class="form-control border-start-0 date-input"
                                            required
                                            min="{{ $academicYear->starting_date->format('Y-m-d') }}"
                                            max="{{ $academicYear->expiry_date->format('Y-m-d') }}"
                                        >
                                        <div class="invalid-feedback">
                                            Please select a valid starting date.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ending Date -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="end_date" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-stop-circle me-2 text-danger"></i>Ending Date
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0">
                                            <i class="fas fa-calendar-times text-danger"></i>
                                        </span>
                                        <input 
                                            type="date"
                                            name="end_date"
                                            id="end_date"
                                            class="form-control border-start-0 date-input"
                                            required
                                            min="{{ $academicYear->starting_date->format('Y-m-d') }}"
                                            max="{{ $academicYear->expiry_date->format('Y-m-d') }}"
                                        >
                                        <div class="invalid-feedback">
                                            Please select a valid ending date.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-semibold shadow-sm" id="submitBtn">
                                <i class="fas fa-plus-circle me-2"></i>Create {{ $nextTerm }}
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" id="submitSpinner"></span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.history.back()">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startingDateInput = document.querySelector('input[name="start_date"]');
    const endingDateInput = document.querySelector('input[name="end_date"]');
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');

    // Set minimum ending date based on starting date selection
    startingDateInput.addEventListener('change', () => {
        if (startingDateInput.value) {
            endingDateInput.min = startingDateInput.value;
            // If ending_date < starting_date, reset it
            if (endingDateInput.value && endingDateInput.value < startingDateInput.value) {
                endingDateInput.value = startingDateInput.value;
            }
        }
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        if (form.checkValidity()) {
            submitBtn.classList.add('btn-loading');
            spinner.classList.remove('d-none');
            submitBtn.disabled = true;
        }
    });

    // Bootstrap form validation
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-bottom: none;
    position: relative;
}

.card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.form-label {
    font-size: 0.95rem;
    letter-spacing: 0.3px;
}

.form-control {
    border: 2px solid #e8ecef;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #fff;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    background-color: #fff;
}

.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.input-group-text {
    border: 2px solid #e8ecef;
    border-radius: 10px;
    background-color: #fff;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
    border-color: #667eea;
    background-color: #f8f9ff;
}

.input-group:focus-within .input-group-text i {
    color: #667eea !important;
}

.date-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(0.5);
    transition: filter 0.3s ease;
}

.date-input::-webkit-calendar-picker-indicator:hover {
    filter: invert(0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-loading {
    pointer-events: none;
    opacity: 0.7;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .card-header {
        padding: 1.5rem !important;
    }
    
    .icon-wrapper {
        width: 50px;
        height: 50px;
    }
    
    .icon-wrapper i {
        font-size: 1.5rem;
    }
    
    .btn-lg {
        padding: 0.75rem 2rem !important;
        font-size: 1rem !important;
    }
}

.form-control:focus,
.btn:focus {
    outline: none;
}

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

.card {
    animation: fadeInUp 0.6s ease-out;
}
</style>
@endsection
