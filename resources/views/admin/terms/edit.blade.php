@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <!-- Header Section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-warning text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-1 fw-bold">Edit {{ $term->term }}</h2>
                            <p class="mb-0 opacity-90">Update term information and dates</p>
                        </div>
                    </div>
                    <!-- Back Button -->
                    <div class="position-absolute top-0 end-0 m-3">
                        <a href="{{ route('admin.terms.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-0 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-0 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-body p-4 p-md-5">
                    <!-- Current Term Info -->
                    <div class="info-section mb-4 p-3 bg-light rounded-3">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Current Term Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Current Status</small>
                                @php
                                    $now = now();
                                    $status = 'upcoming';
                                    $badgeClass = 'bg-info';
                                    $icon = 'fas fa-clock';
                                    
                                    if($term->starting_date && $term->ending_date) {
                                        if($now->lt($term->starting_date)) {
                                            $status = 'upcoming';
                                            $badgeClass = 'bg-info';
                                            $icon = 'fas fa-clock';
                                        } elseif($now->between($term->starting_date, $term->ending_date)) {
                                            $status = 'active';
                                            $badgeClass = 'bg-success';
                                            $icon = 'fas fa-play-circle';
                                        } else {
                                            $status = 'completed';
                                            $badgeClass = 'bg-secondary';  
                                            $icon = 'fas fa-check-circle';
                                        }
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2">
                                    <i class="{{ $icon }} me-1"></i>{{ ucfirst($status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Last Updated</small>
                                <span class="fw-semibold">{{ $term->updated_at ? $term->updated_at->format('M d, Y - H:i') : 'Never' }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.terms.update', $term->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Academic Year Field -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-graduation-cap me-2 text-warning"></i>Academic Year
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light" value="{{ $term->academicYear->year ?? 'N/A' }}" readonly>
                            </div>
                            <input type="hidden" name="academic_year_id" value="{{ $term->academic_year_id }}">
                        </div>

                        <!-- Term Field -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-tag me-2 text-warning"></i>Term
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-bookmark text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light" value="{{ $term->term }}" readonly>
                            </div>
                            <input type="hidden" name="term" value="{{ $term->term }}">
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
                                            class="form-control border-start-0 date-input @error('starting_date') is-invalid @enderror"
                                            value="{{ old('starting_date', $term->starting_date ? $term->starting_date->format('Y-m-d') : '') }}"
                                            required
                                            min="{{ getMinStartDate($term->academicYear, $existingTerms ?? collect(), $term->term) }}"
                                            max="{{ $term->academicYear->expiry_date->format('Y-m-d') }}"
                                        >
                                        @error('starting_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">
                                                Please select a valid starting date.
                                            </div>
                                        @enderror
                                    </div>
                                    @if($term->starting_date)
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Current: {{ $term->starting_date->format('M d, Y (l)') }}
                                        </small>
                                    @endif
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
                                            class="form-control border-start-0 date-input @error('ending_date') is-invalid @enderror"
                                            value="{{ old('ending_date', $term->ending_date ? $term->ending_date->format('Y-m-d') : '') }}"
                                            required
                                            min="{{ old('starting_date', $term->starting_date ? $term->starting_date->format('Y-m-d') : '') }}"
                                            max="{{ $term->academicYear->expiry_date->format('Y-m-d') }}"
                                        >
                                        @error('ending_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">
                                                Please select a valid ending date.
                                            </div>
                                        @enderror
                                    </div>
                                    @if($term->ending_date)
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Current: {{ $term->ending_date->format('M d, Y (l)') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Duration Display -->
                        <div class="duration-info mb-4 p-3 bg-info bg-opacity-10 rounded-3">
                            <h6 class="fw-bold text-info mb-2">
                                <i class="fas fa-clock me-2"></i>Duration Information
                            </h6>
                            <div id="durationDisplay" class="text-muted">
                                @if($term->starting_date && $term->ending_date)
                                    @php
                                        $duration = $term->starting_date->diffInDays($term->ending_date);
                                        $weeks = floor($duration / 7);
                                        $days = $duration % 7;
                                    @endphp
                                    <span class="fw-bold text-info">{{ $duration }} days</span> 
                                    ({{ $weeks }} weeks {{ $days }} days)
                                @else
                                    Select both dates to see duration
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-5">
                            <a href="{{ route('admin.terms.index') }}" class="btn btn-outline-secondary btn-lg px-4 py-3">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning btn-lg px-4 py-3" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-warning btn-lg px-5 py-3 fw-semibold shadow-sm">
                                    <i class="fas fa-save me-2"></i>Update {{ $term->term }}
                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Information Card -->
            <div class="card shadow border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Update Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="guideline-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Date Validation:</strong> Dates must fall within the academic year period
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="guideline-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Sequential Order:</strong> Term dates should not overlap with other terms
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="guideline-item">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                <strong>Active Terms:</strong> Be careful when modifying dates of active terms
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="guideline-item">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                <strong>Duration:</strong> Recommended minimum duration is 8-12 weeks
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for the edit form */
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

.info-section {
    border-left: 4px solid #f093fb;
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
    border-color: #f093fb;
    box-shadow: 0 0 0 0.2rem rgba(240, 147, 251, 0.15);
    background-color: #fff;
}

.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

.input-group-text {
    border: 2px solid #e8ecef;
    border-radius: 10px;
    background-color: #fff;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
    border-color: #f093fb;
    background-color: #fdf2ff;
}

.input-group:focus-within .input-group-text i {
    color: #f093fb !important;
}

.date-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(0.5);
    transition: filter 0.3s ease;
}

.date-input::-webkit-calendar-picker-indicator:hover {
    filter: invert(0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    border-radius: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
    background: linear-gradient(135deg, #e084fc 0%, #e04e5e 100%);
    color: white;
}

.btn-warning:active {
    transform: translateY(0);
}

.btn-warning::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-warning:hover::before {
    left: 100%;
}

.btn-outline-warning {
    border: 2px solid #f093fb;
    color: #f093fb;
    background: transparent;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-warning:hover {
    background: #f093fb;
    color: white;
    transform: translateY(-2px);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
}

.duration-info {
    border-left: 4px solid #17a2b8;
}

.guideline-item {
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    line-height: 1.4;
}

.alert-danger {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    border: none;
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
    
    .position-absolute.top-0.end-0 {
        position: relative !important;
        margin: 1rem 0 0 0 !important;
    }
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
function getMinStartDate($academicYear, $existingTerms, $currentTerm) {
    if ($currentTerm == 'Term 1') {
        return $academicYear->starting_date->format('Y-m-d');
    } elseif ($currentTerm == 'Term 2') {
        $term1 = $existingTerms->firstWhere('term', 'Term 1');
        return $term1 ? $term1->ending_date->addDay()->format('Y-m-d') : $academicYear->starting_date->format('Y-m-d');
    } elseif ($currentTerm == 'Term 3') {
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
    const durationDisplay = document.getElementById('durationDisplay');

    // Store original values for reset functionality
    const originalStartDate = startingDateInput.value;
    const originalEndDate = endingDateInput.value;

    // Get values from Laravel
    const academicYearStart = new Date('{{ $term->academicYear->starting_date->format("Y-m-d") }}');
    const academicYearEnd = new Date('{{ $term->academicYear->expiry_date->format("Y-m-d") }}');

    // Function to calculate and display duration
    function updateDuration() {
        if (startingDateInput.value && endingDateInput.value) {
            const startDate = new Date(startingDateInput.value);
            const endDate = new Date(endingDateInput.value);
            
            if (endDate >= startDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const weeks = Math.floor(diffDays / 7);
                const days = diffDays % 7;
                
                durationDisplay.innerHTML = `
                    <span class="fw-bold text-info">${diffDays} days</span> 
                    (${weeks} weeks ${days} days)
                `;
            } else {
                durationDisplay.innerHTML = `
                    <span class="text-danger">End date must be after start date</span>
                `;
            }
        } else {
            durationDisplay.innerHTML = 'Select both dates to see duration';
        }
    }

    // Update ending_date.min to starting_date's selected value dynamically
    startingDateInput.addEventListener('change', () => {
        if (startingDateInput.value) {
            endingDateInput.min = startingDateInput.value;
            // If ending_date < starting_date, reset it
            if (endingDateInput.value && endingDateInput.value < startingDateInput.value) {
                endingDateInput.value = startingDateInput.value;
            }
        }
        updateDuration();
    });

    // Update duration when ending date changes
    endingDateInput.addEventListener('change', updateDuration);

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

    // Reset form functionality
    window.resetForm = function() {
        startingDateInput.value = originalStartDate;
        endingDateInput.value = originalEndDate;
        form.classList.remove('was-validated');
        updateDuration();
        
        // Remove any validation classes
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
        });
    };

    // Initial duration calculation
    updateDuration();
});
</script>