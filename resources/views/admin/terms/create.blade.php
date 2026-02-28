@extends('admin.layout')

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

                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-0 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.terms.store') }}" method="POST" class="needs-validation" novalidate>
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
                            </div>
                            <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                            <input type="hidden" name="term" value="{{ $nextTerm }}">
                        </div>

                        <!-- Term Field -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-tag me-2 text-primary"></i>Term
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-bookmark text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light" value="{{ $nextTerm }}" readonly>
                            </div>
                        </div>

                        <!-- Date Fields Row -->
                        <div class="row g-3">
                            <!-- Starting Date -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="starting_date" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-play-circle me-2 text-success"></i>Starting Date
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0">
                                            <i class="fas fa-calendar-day text-success"></i>
                                        </span>
                                        <input
                                            type="date"
                                            name="starting_date"
                                            id="starting_date"
                                            class="form-control border-start-0 date-input"
                                            required
                                            min="{{ getMinStartDate($academicYear, $existingTerms, $nextTerm) }}"
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
                                    <label for="ending_date" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-stop-circle me-2 text-danger"></i>Ending Date
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0">
                                            <i class="fas fa-calendar-times text-danger"></i>
                                        </span>
                                        <input
                                            type="date"
                                            name="ending_date"
                                            id="ending_date"
                                            class="form-control border-start-0 date-input"
                                            required
                                            min="{{ getMinStartDate($academicYear, $existingTerms, $nextTerm) }}"
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
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-semibold shadow-sm">
                                <i class="fas fa-plus-circle me-2"></i>Create {{ $nextTerm }}
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for the form */
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

.alert-danger {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    border: none;
    font-weight: 500;
}

.form-group {
    position: relative;
}

.invalid-feedback {
    font-size: 0.875rem;
    font-weight: 500;
}

/* Loading state */
.btn-loading .spinner-border {
    display: inline-block !important;
}

.btn-loading {
    pointer-events: none;
    opacity: 0.7;
}

/* Responsive adjustments */
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

/* Focus management for better accessibility */
.form-control:focus,
.btn:focus {
    outline: none;
}

/* Animation for form appearance */
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

@php
// Helper function inside blade to calculate min starting date for term
function getMinStartDate($academicYear, $existingTerms, $nextTerm) {
    if ($nextTerm == 'Term 1') {
        return $academicYear->starting_date->format('Y-m-d');
    } elseif ($nextTerm == 'Term 2') {
        $term1 = $existingTerms->firstWhere('term', 'Term 1');
        return $term1 ? $term1->ending_date->addDay()->format('Y-m-d') : $academicYear->starting_date->format('Y-m-d');
    } elseif ($nextTerm == 'Term 3') {
        $term2 = $existingTerms->firstWhere('term', 'Term 2');
        return $term2 ? $term2->ending_date->addDay()->format('Y-m-d') : $academicYear->starting_date->format('Y-m-d');
    }
    return $academicYear->starting_date->format('Y-m-d');
}
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startingDateInput = document.querySelector('input[name="starting_date"]');
    const endingDateInput = document.querySelector('input[name="ending_date"]');
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');

    // Get values passed from Laravel as data attributes or hidden inputs (you can add these if needed)
    const academicYearStart = new Date('{{ $academicYear->starting_date->format("Y-m-d") }}');
    const academicYearEnd = new Date('{{ $academicYear->expiry_date->format("Y-m-d") }}');

    // Existing terms info from Laravel (JSON encoded)
    const existingTerms = @json($existingTerms);

    // Get the nextTerm from Laravel (string: "Term 1", "Term 2", "Term 3")
    const nextTerm = '{{ $nextTerm }}';

    // Function to parse date string "YYYY-MM-DD" into Date object
    function parseDate(str) {
        const [year, month, day] = str.split('-');
        return new Date(year, month - 1, day);
    }

    // Calculate minStartDate based on existing terms and nextTerm
    function getMinStartDate() {
        if (nextTerm === 'Term 1') {
            return academicYearStart;
        }
        if (nextTerm === 'Term 2') {
            let term1 = existingTerms.find(t => t.term === 'Term 1');
            if (term1) {
                let d = parseDate(term1.ending_date);
                d.setDate(d.getDate() + 1); // day after term 1 ends
                return d;
            }
        }
        if (nextTerm === 'Term 3') {
            let term2 = existingTerms.find(t => t.term === 'Term 2');
            if (term2) {
                let d = parseDate(term2.ending_date);
                d.setDate(d.getDate() + 1); // day after term 2 ends
                return d;
            }
        }
        return academicYearStart; // fallback
    }

    // Set the min and max attributes for the date inputs accordingly
    function setDateConstraints() {
        const minStartDate = getMinStartDate();
        startingDateInput.min = minStartDate.toISOString().split('T')[0];
        startingDateInput.max = academicYearEnd.toISOString().split('T')[0];

        // Initially set ending date min as starting date min, max as academic year end
        endingDateInput.min = startingDateInput.min;
        endingDateInput.max = academicYearEnd.toISOString().split('T')[0];
    }

    setDateConstraints();

    // Update ending_date.min to starting_date's selected value dynamically
    startingDateInput.addEventListener('change', () => {
        if (startingDateInput.value) {
            endingDateInput.min = startingDateInput.value;
            // If ending_date < starting_date, reset it
            if (endingDateInput.value && endingDateInput.value < startingDateInput.value) {
                endingDateInput.value = startingDateInput.value;
            }
        } else {
            endingDateInput.min = startingDateInput.min;
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

    // Add smooth focus transitions
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>