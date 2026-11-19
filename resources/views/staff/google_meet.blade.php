@extends('dos.layout')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-gradient rounded-3 p-3 me-3">
                    <i class="fas fa-video text-white fs-4"></i>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Create Google Meet</h2>
                    <p class="text-muted mb-0">Schedule a new online class session</p>
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('meet_link'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 fs-5"></i>
                        <div class="flex-grow-1">
                            <strong>Meeting Created Successfully!</strong><br>
                            <small class="text-success-emphasis">
                                Google Meet Link: 
                                <a href="{{ session('meet_link') }}" target="_blank" class="text-decoration-none fw-semibold">
                                    {{ session('meet_link') }}
                                </a>
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('google.create-meet') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Branch Selection -->
                        <div class="mb-4">
                            <label for="branch_id" class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-building me-2 text-primary"></i>Branch
                            </label>
                            <select name="branch_id" id="branch_id" class="form-select border-2 py-3" required>
                                <option value="">Choose a branch...</option>
                                @foreach(\App\Models\Branch::all() as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a branch.</div>
                        </div>

                        <!-- Course Selection -->
                        <div class="mb-4">
                            <label for="course_id" class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-book me-2 text-primary"></i>Course
                            </label>
                            <select name="course_id" id="course_id" class="form-select border-2 py-3" required disabled>
                                <option value="">Select branch first...</option>
                            </select>
                            <div class="invalid-feedback">Please select a course.</div>
                        </div>

                        <!-- Lesson Selection -->
                        <div class="mb-4">
                            <label for="lesson_id" class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Lesson
                            </label>
                            <select name="lesson_id" id="lesson_id" class="form-select border-2 py-3" required disabled>
                                <option value="">Select course first...</option>
                            </select>
                            <div class="invalid-feedback">Please select a lesson.</div>
                        </div>

                        <!-- Class Title -->
                        <div class="mb-4">
                            <label for="summary" class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-heading me-2 text-primary"></i>Class Title
                            </label>
                            <input type="text" name="summary" id="summary" class="form-control border-2 py-3" 
                                   placeholder="Enter class title..." required>
                            <div class="invalid-feedback">Please enter a class title.</div>
                        </div>

                        <!-- Date & Time Row -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start" class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-calendar-plus me-2 text-success"></i>Start Time
                                </label>
                                <input type="datetime-local" name="start" id="start" class="form-control border-2 py-3" required>
                                <div class="invalid-feedback">Please select start time.</div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="end" class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-calendar-minus me-2 text-danger"></i>End Time
                                </label>
                                <input type="datetime-local" name="end" id="end" class="form-control border-2 py-3" required>
                                <div class="invalid-feedback">Please select end time.</div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-semibold" id="submitBtn">
                                <i class="fas fa-video me-2"></i>
                                <span class="btn-text">Create Google Meet</span>
                                <div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 bg-light mt-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>The Google Meet link will be generated automatically and shared with participants.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Styles */
.form-select:focus,
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgb(13 110 253 / 15%);
}

.form-select:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary:active {
    transform: translateY(0);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left: 4px solid #28a745;
}

.form-label i {
    width: 20px;
}

/* Loading state */
.btn-loading .btn-text {
    opacity: 0.6;
}

.btn-loading .spinner-border {
    display: inline-block !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>

<script>
const branchCourses = @json($branchCourses);
const courses = @json(\App\Models\Course::all(['id','course_name']));
const lessons = @json(\App\Models\Lesson::all(['id','title','course_id']));
// Enhanced JavaScript with better UX
document.addEventListener('DOMContentLoaded', function() {
    // Get all courses and lessons with their branch/course IDs
    
    const branchSelect = document.getElementById('branch_id');
    const courseSelect = document.getElementById('course_id');
    const lessonSelect = document.getElementById('lesson_id');
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

   branchSelect.addEventListener('change', function() {
    const branchId = this.value;
    courseSelect.innerHTML = '<option value="">Choose a course...</option>';
    courseSelect.disabled = !branchId;
    lessonSelect.innerHTML = '<option value="">Select course first...</option>';
    lessonSelect.disabled = true;

    if (branchId) {
        // Get course IDs for this branch from the pivot table
        const courseIds = branchCourses.filter(bc => bc.branch_id == branchId).map(bc => bc.course_id);
        const branchCoursesList = courses.filter(c => courseIds.includes(c.id));
        branchCoursesList.forEach(course => {
            courseSelect.innerHTML += `<option value="${course.id}">${course.course_name}</option>`;
        });
        if (branchCoursesList.length === 0) {
            courseSelect.innerHTML = '<option value="">No courses available</option>';
        }
    }
});
    // Course change handler
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        
        // Reset and enable lesson select
        lessonSelect.innerHTML = '<option value="">Choose a lesson...</option>';
        lessonSelect.disabled = !courseId;
        
        if (courseId) {
            const courseLessons = lessons.filter(l => l.course_id == courseId);
            courseLessons.forEach(lesson => {
                lessonSelect.innerHTML += `<option value="${lesson.id}">${lesson.title}</option>`;
            });
            
            if (courseLessons.length === 0) {
                lessonSelect.innerHTML = '<option value="">No lessons available</option>';
            }
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            // Show loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            submitBtn.querySelector('.spinner-border').classList.remove('d-none');
        }
        
        form.classList.add('was-validated');
    });

    // Date validation - ensure end time is after start time
    const startInput = document.getElementById('start');
    const endInput = document.getElementById('end');
    
    function validateDates() {
        const startTime = new Date(startInput.value);
        const endTime = new Date(endInput.value);
        
        if (startTime && endTime && endTime <= startTime) {
            endInput.setCustomValidity('End time must be after start time');
        } else {
            endInput.setCustomValidity('');
        }
    }
    
    startInput.addEventListener('change', validateDates);
    endInput.addEventListener('change', validateDates);

    // Set minimum date to current date/time
    const now = new Date();
    const minDateTime = now.toISOString().slice(0, 16);
    startInput.min = minDateTime;
    endInput.min = minDateTime;
});

// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
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