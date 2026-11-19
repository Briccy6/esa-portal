{{-- filepath: resources/views/teacher/courses/details.blade.php --}}
@extends('teacher.layout')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <h1 class="display-6 fw-bold text-dark mb-0">My Courses & Units</h1>
                    <p class="text-muted mb-0">Manage and track your teaching progress</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-book me-1"></i>
                        {{ $courses->count() }} Course{{ $courses->count() !== 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($courses->count())
        <!-- Courses Grid -->
        <div class="row g-4">
            @foreach($courses as $course)
                @php
                    $totalLessons = $course->branch_lessons->sum(fn($b) => $b['lessons']->count());
                    $totalUnits = $course->branch_lessons->sum(fn($b) => $b['lessons']->sum(fn($l) => $l['units']->count()));
                    $courseBranches = $course->branch_lessons->pluck('branch_id', 'branch_name');
                @endphp
                <div class="col-12">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden course-card">
                        <!-- Course Header -->
                        <div class="card-header bg-gradient-primary text-white border-0 p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="card-title mb-2 fw-bold">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        {{ $course->course_name }}
                                    </h3>
                                    <p class="mb-0 opacity-90">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $course->students->count() }} enrolled student{{ $course->students->count() !== 1 ? 's' : '' }}
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="d-flex gap-2 align-items-center justify-content-md-end flex-wrap">
                                        @if($course->students->count() > 0)
                                            @if($courseBranches->count() > 1)
                                                <!-- Multiple branches: show branch selector -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-file-csv me-1"></i>Export CSV
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><h6 class="dropdown-header">Select Branch</h6></li>
                                                        @foreach($course->branch_lessons as $branchData)
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('teacher.courses.export-students', ['course' => $course->id, 'branch' => $branchData['branch_id']]) }}">
                                                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $branchData['branch_name'] }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <a href="{{ route('teacher.courses.export-students', ['course' => $course->id, 'branch' => $course->branch_lessons->first()['branch_id'] ?? null]) }}"
                                                   class="btn btn-sm btn-light rounded-pill">
                                                    <i class="fas fa-file-csv me-1"></i>Export CSV
                                                </a>
                                            @endif
                                        @endif
                                        <div class="badge bg-white text-primary px-3 py-2 rounded-pill fs-6">
                                            <i class="fas fa-chart-line me-1"></i>
                                            {{ $totalLessons }} Lesson{{ $totalLessons !== 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Content -->
                        <div class="card-body p-0">
                            @if($course->branch_lessons->count())
                                @foreach($course->branch_lessons as $branchData)
                                    <!-- Branch Section -->
                                    <div class="branch-section border-bottom">
                                        <div class="branch-header bg-light px-4 py-3 d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <span class="fw-semibold text-dark">{{ $branchData['branch_name'] }}</span>
                                            <span class="badge bg-primary-subtle text-primary ms-2">
                                                {{ $branchData['lessons']->count() }} lesson{{ $branchData['lessons']->count() !== 1 ? 's' : '' }}
                                            </span>
                                        </div>

                                        @foreach($branchData['lessons'] as $lessonIndex => $lesson)
                                            <div class="lesson-container {{ $lessonIndex > 0 ? 'border-top' : '' }}">
                                                <!-- Lesson Header -->
                                                <div class="lesson-header bg-light p-4">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div class="lesson-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="fas fa-book-open"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1 fw-semibold text-dark">{{ $lesson['title'] }}</h5>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-puzzle-piece me-1"></i>
                                                                    {{ $lesson['units']->count() }} unit{{ $lesson['units']->count() !== 1 ? 's' : '' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        @if($lesson['units']->count())
                                                            <button class="btn btn-outline-primary btn-sm rounded-pill toggle-units"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#lesson-{{ $lesson['id'] }}-units-{{ $branchData['branch_id'] }}"
                                                                    aria-expanded="false">
                                                                <i class="fas fa-chevron-down transition-icon"></i>
                                                                <span class="ms-1">View Units</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Units Section -->
                                                @if($lesson['units']->count())
                                                    <div class="collapse show" id="lesson-{{ $lesson['id'] }}-units-{{ $branchData['branch_id'] }}">
                                                        <div class="units-grid p-4">
                                                            <div class="row g-3">
                                                                @foreach($lesson['units'] as $unit)
                                                                    @php
                                                                        $completedHours = $unitDailyHours[$unit['id']] ?? 0;
                                                                        $requiredHours = $unit['hours'] ?? 0;
                                                                        $isDone = $completedHours >= $requiredHours && $requiredHours > 0;
                                                                        $progressPercentage = $requiredHours > 0 ? min(($completedHours / $requiredHours) * 100, 100) : 0;
                                                                    @endphp
                                                                    <div class="col-lg-6 col-xl-4">
                                                                        <div class="unit-card card border h-100 {{ $isDone ? 'border-success' : 'border-warning' }} position-relative">
                                                                            <!-- Completion Status Badge -->
                                                                            <div class="position-absolute top-0 end-0 m-2">
                                                                                @if($isDone)
                                                                                    <span class="badge bg-success rounded-pill">
                                                                                        <i class="fas fa-check me-1"></i>Complete
                                                                                    </span>
                                                                                @else
                                                                                    <span class="badge bg-warning text-dark rounded-pill">
                                                                                        <i class="fas fa-clock me-1"></i>In Progress
                                                                                    </span>
                                                                                @endif
                                                                            </div>

                                                                            <div class="card-body p-3">
                                                                                <!-- Unit Title -->
                                                                                <div class="d-flex align-items-start mb-3">
                                                                                    <div class="form-check me-2 mt-1">
                                                                                        <input type="checkbox"
                                                                                               class="form-check-input"
                                                                                               id="unit_done_{{ $unit['id'] }}"
                                                                                               {{ $isDone ? 'checked' : '' }}
                                                                                               disabled>
                                                                                    </div>
                                                                                    <div class="flex-grow-1">
                                                                                        <h6 class="card-title mb-2 {{ $isDone ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                                                                            {{ $unit['unit_name'] }}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Progress Section -->
                                                                                <div class="progress-section">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                        <small class="text-muted fw-medium">Progress</small>
                                                                                        <span class="badge {{ $isDone ? 'bg-success' : 'bg-primary' }} rounded-pill">
                                                                                            {{ $completedHours }}/{{ $requiredHours }} hrs
                                                                                        </span>
                                                                                    </div>

                                                                                    <!-- Progress Bar -->
                                                                                    <div class="progress rounded-pill" style="height: 8px;">
                                                                                        <div class="progress-bar {{ $isDone ? 'bg-success' : 'bg-primary' }} progress-bar-striped progress-bar-animated"
                                                                                             role="progressbar"
                                                                                             style="width: {{ $progressPercentage }}%"
                                                                                             aria-valuenow="{{ $progressPercentage }}"
                                                                                             aria-valuemin="0"
                                                                                             aria-valuemax="100">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="text-center mt-2">
                                                                                        <small class="text-muted">{{ number_format($progressPercentage, 1) }}% Complete</small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="p-4 text-center">
                                                        <div class="text-muted">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            No units assigned to this lesson yet.
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div class="card-body text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-book-open fa-3x mb-3 opacity-50"></i>
                                        <h5>No lessons assigned</h5>
                                        <p class="mb-0">This course doesn't have any lessons assigned to you yet.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="fas fa-graduation-cap fa-4x text-primary opacity-50"></i>
                        </div>
                        <h3 class="card-title text-dark mb-3">No Courses Assigned</h3>
                        <p class="text-muted mb-4">You haven't been assigned any courses yet. Contact your administrator for course assignments.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Custom Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.course-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.08);
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.15) !important;
}

.branch-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.lesson-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.lesson-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.unit-card {
    transition: all 0.2s ease;
    border-radius: 12px;
}

.unit-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.toggle-units .transition-icon {
    transition: transform 0.2s ease;
}

.toggle-units[aria-expanded="true"] .transition-icon {
    transform: rotate(180deg);
}

.progress {
    background-color: rgba(0,0,0,0.08);
}

.badge {
    font-weight: 500;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.units-grid {
    background: linear-gradient(135deg, #fafbff 0%, #f8f9fa 100%);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-6 {
        font-size: 1.5rem;
    }

    .card-header .row {
        text-align: center;
    }

    .card-header .col-md-4 {
        margin-top: 1rem;
    }

    .lesson-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .toggle-units {
        margin-top: 1rem;
        align-self: stretch;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .lesson-header {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    }

    .units-grid {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    }
}

/* Animation for progress bars */
@keyframes progress-animation {
    0% { width: 0; }
    100% { width: var(--progress-width); }
}

.progress-bar {
    animation: progress-animation 1s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    document.querySelectorAll('.toggle-units').forEach(function(button) {
        button.addEventListener('click', function() {
            setTimeout(function() {
                button.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 300);
        });
    });
});
</script>
@endsection
