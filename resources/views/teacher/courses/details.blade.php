{{-- filepath: resources/views/teacher/courses/details.blade.php --}}
@extends('teacher.layout')

@section('content')
<div class="dashboard-content px-4 py-5">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h2 fw-bold mb-1" style="color: #1e293b;">My Courses & Units</h1>
                <p class="text-muted mb-0">Track your teaching progress and manage course units</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="stat-card bg-white border rounded-3 px-4 py-2 shadow-sm">
                    <span class="text-muted small me-2">Total Courses</span>
                    <span class="fw-bold" style="color: #1e40af;">{{ $courses->count() }}</span>
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
                    <div class="course-card bg-white border rounded-4 shadow-sm overflow-hidden">
                        <!-- Course Header -->
                        <div class="course-header px-5 py-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                            <div class="d-flex align-items-center gap-4">
                                <div class="course-icon bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                    <i class="fas fa-graduation-cap text-white fs-4"></i>
                                </div>
                                <div class="text-white">
                                    <h3 class="fw-bold mb-1 fs-4">{{ $course->course_name }}</h3>
                                    <div class="d-flex align-items-center gap-3 opacity-90">
                                        <span class="small">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $course->students->count() }} enrolled student{{ $course->students->count() !== 1 ? 's' : '' }}
                                        </span>
                                        <span class="small">
                                            <i class="fas fa-book me-1"></i>
                                            {{ $totalLessons }} lesson{{ $totalLessons !== 1 ? 's' : '' }}
                                        </span>
                                        <span class="small">
                                            <i class="fas fa-puzzle-piece me-1"></i>
                                            {{ $totalUnits }} unit{{ $totalUnits !== 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @if($course->students->count() > 0)
                                    @if($courseBranches->count() > 1)
                                        <div class="dropdown">
                                            <button class="btn btn-white btn-sm rounded-3 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-download me-2"></i>Export CSV
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li><h6 class="dropdown-header small text-muted">Select Branch</h6></li>
                                                @foreach($course->branch_lessons as $branchData)
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('teacher.courses.export-students', ['course' => $course->id, 'branch' => $branchData['branch_id']]) }}">
                                                            <i class="fas fa-map-marker-alt me-2" style="color: #3b82f6;"></i>{{ $branchData['branch_name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <a href="{{ route('teacher.courses.export-students', ['course' => $course->id, 'branch' => $course->branch_lessons->first()['branch_id'] ?? null]) }}"
                                           class="btn btn-white btn-sm rounded-3 px-3">
                                            <i class="fas fa-download me-2"></i>Export CSV
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Course Content -->
                        <div class="course-body">
                            @if($course->branch_lessons->count())
                                @foreach($course->branch_lessons as $branchData)
                                    <!-- Branch Section -->
                                    <div class="branch-section border-bottom">
                                        <div class="branch-header px-5 py-3 d-flex align-items-center" style="background: #f8fafc;">
                                            <div class="branch-icon bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                                <i class="fas fa-map-marker-alt" style="color: #1e40af; font-size: 14px;"></i>
                                            </div>
                                            <span class="fw-semibold" style="color: #1e293b;">{{ $branchData['branch_name'] }}</span>
                                            <span class="badge bg-primary bg-opacity-10 ms-2" style="color: #1e40af;">
                                                {{ $branchData['lessons']->count() }} lesson{{ $branchData['lessons']->count() !== 1 ? 's' : '' }}
                                            </span>
                                        </div>

                                        @foreach($branchData['lessons'] as $lessonIndex => $lesson)
                                            <div class="lesson-container {{ $lessonIndex > 0 ? 'border-top' : '' }}">
                                                <!-- Lesson Header -->
                                                <div class="lesson-header px-5 py-4 d-flex align-items-center justify-content-between" style="background: #ffffff;">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="lesson-number bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; font-size: 14px;">
                                                            {{ $lessonIndex + 1 }}
                                                        </div>
                                                        <div>
                                                            <h5 class="fw-semibold mb-1" style="color: #1e293b;">{{ $lesson['title'] }}</h5>
                                                            <span class="small text-muted">
                                                                <i class="fas fa-puzzle-piece me-1"></i>
                                                                {{ $lesson['units']->count() }} unit{{ $lesson['units']->count() !== 1 ? 's' : '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @if($lesson['units']->count())
                                                        <button class="btn btn-outline-primary btn-sm rounded-3 px-3 toggle-units"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#lesson-{{ $lesson['id'] }}-units-{{ $branchData['branch_id'] }}"
                                                                aria-expanded="false"
                                                                style="border-color: #e2e8f0; color: #64748b;">
                                                            <i class="fas fa-chevron-down transition-icon me-1"></i>
                                                            <span class="fw-medium">View Units</span>
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Units Section -->
                                                @if($lesson['units']->count())
                                                    <div class="collapse show" id="lesson-{{ $lesson['id'] }}-units-{{ $branchData['branch_id'] }}">
                                                        <div class="units-grid px-5 py-4" style="background: #f8fafc;">
                                                            <div class="row g-3">
                                                                @foreach($lesson['units'] as $unit)
                                                                    @php
                                                                        $completedHours = $unitDailyHours[$unit['id']] ?? 0;
                                                                        $requiredHours = $unit['hours'] ?? 0;
                                                                        $isDone = $completedHours >= $requiredHours && $requiredHours > 0;
                                                                        $progressPercentage = $requiredHours > 0 ? min(($completedHours / $requiredHours) * 100, 100) : 0;
                                                                    @endphp
                                                                    <div class="col-lg-6 col-xl-4">
                                                                        <div class="unit-card bg-white border rounded-3 h-100 {{ $isDone ? 'border-success-subtle' : 'border' }} shadow-sm position-relative overflow-hidden">
                                                                            <!-- Status Indicator -->
                                                                            <div class="status-indicator position-absolute top-0 start-0 w-100" style="height: 4px; background: {{ $isDone ? '#10b981' : '#f59e0b' }};"></div>

                                                                            <div class="card-body p-4">
                                                                                <!-- Unit Header -->
                                                                                <div class="d-flex align-items-start justify-content-between mb-3">
                                                                                    <div class="d-flex align-items-start gap-3">
                                                                                        <div class="status-icon {{ $isDone ? 'bg-success' : 'bg-warning' }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mt-1" style="width: 32px; height: 32px;">
                                                                                            <i class="fas {{ $isDone ? 'fa-check' : 'fa-clock' }}" style="color: {{ $isDone ? '#10b981' : '#f59e0b' }}; font-size: 12px;"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                            <h6 class="fw-semibold mb-1" style="color: #1e293b;">{{ $unit['unit_name'] }}</h6>
                                                                                            <span class="badge {{ $isDone ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill small">
                                                                                                {{ $isDone ? 'Complete' : 'In Progress' }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Progress Section -->
                                                                                <div class="progress-section mt-4">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                        <span class="small text-muted">Hours Progress</span>
                                                                                        <span class="fw-semibold small" style="color: #1e293b;">
                                                                                            {{ $completedHours }} / {{ $requiredHours }} hrs
                                                                                        </span>
                                                                                    </div>

                                                                                    <!-- Progress Bar -->
                                                                                    <div class="progress rounded-pill" style="height: 6px; background: #e2e8f0;">
                                                                                        <div class="progress-bar rounded-pill"
                                                                                             role="progressbar"
                                                                                             style="width: {{ $progressPercentage }}%; background: {{ $isDone ? '#10b981' : '#3b82f6' }}; transition: width 0.6s ease;"
                                                                                             aria-valuenow="{{ $progressPercentage }}"
                                                                                             aria-valuemin="0"
                                                                                             aria-valuemax="100">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="text-end mt-2">
                                                                                        <span class="small fw-medium" style="color: {{ $isDone ? '#10b981' : '#3b82f6' }};">
                                                                                            {{ number_format($progressPercentage, 0) }}% Complete
                                                                                        </span>
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
                                                    <div class="px-5 py-4 text-center">
                                                        <div class="text-muted small">
                                                            <i class="fas fa-inbox me-2"></i>
                                                            No units assigned to this lesson yet.
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-book-open fa-3x mb-3 opacity-25"></i>
                                        <h5 class="fw-semibold" style="color: #64748b;">No lessons assigned</h5>
                                        <p class="small text-muted">This course doesn't have any lessons assigned to you yet.</p>
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
                <div class="bg-white border rounded-4 shadow-sm text-center py-5">
                    <div class="empty-state-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-graduation-cap text-primary fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color: #1e293b;">No Courses Assigned</h3>
                    <p class="text-muted mb-0">You haven't been assigned any courses yet. Contact your administrator for course assignments.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Modern Dashboard Styles */
.dashboard-content {
    max-width: 1400px;
    margin: 0 auto;
}

.course-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-color: #e2e8f0;
}

.course-card:hover {
    box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.course-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.course-icon {
    backdrop-filter: blur(10px);
}

.branch-section:last-child {
    border-bottom: none;
}

.branch-header {
    border-bottom: 1px solid #e2e8f0;
}

.lesson-container {
    transition: background-color 0.2s ease;
}

.lesson-container:hover {
    background-color: #fafbfc;
}

.lesson-header {
    border-bottom: 1px solid #f1f5f9;
}

.lesson-number {
    box-shadow: 0 2px 8px rgba(30, 64, 175, 0.2);
}

.unit-card {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-color: #e2e8f0;
}

.unit-card:hover {
    box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    border-color: #cbd5e1;
}

.unit-card.border-success-subtle {
    border-color: #d1fae5;
}

.unit-card.border-success-subtle:hover {
    border-color: #a7f3d0;
}

.status-indicator {
    transition: background-color 0.3s ease;
}

.status-icon {
    backdrop-filter: blur(4px);
}

.toggle-units {
    transition: all 0.2s ease;
}

.toggle-units:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
    color: #1e293b;
}

.toggle-units .transition-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.toggle-units[aria-expanded="true"] .transition-icon {
    transform: rotate(180deg);
}

.toggle-units[aria-expanded="true"] {
    background-color: #eff6ff;
    border-color: #3b82f6;
    color: #1e40af;
}

.stat-card {
    transition: all 0.2s ease;
}

.stat-card:hover {
    box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.1);
}

/* Progress bar animation */
@keyframes progressFill {
    from {
        width: 0;
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.progress-bar {
    animation: progressFill 0.8s ease-out;
}

/* Custom badge styles */
.badge.bg-success-subtle {
    background-color: #d1fae5 !important;
    color: #065f46 !important;
}

.badge.bg-warning-subtle {
    background-color: #fef3c7 !important;
    color: #92400e !important;
}

.badge.bg-primary-subtle {
    background-color: #dbeafe !important;
    color: #1e40af !important;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .course-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
    }

    .course-header .d-flex {
        width: 100%;
    }

    .course-header .d-flex:last-child {
        justify-content: flex-start !important;
    }
}

@media (max-width: 768px) {
    .dashboard-content {
        padding: 1rem !important;
    }

    .course-header {
        padding: 1.25rem !important;
    }

    .branch-header,
    .lesson-header {
        padding: 1rem !important;
    }

    .units-grid {
        padding: 1rem !important;
    }

    .lesson-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.75rem;
    }

    .toggle-units {
        width: 100%;
        text-align: center;
    }

    .unit-card {
        margin-bottom: 0.5rem;
    }
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Focus states for accessibility */
button:focus,
a:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Empty state animation */
.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to button after toggle
    document.querySelectorAll('.toggle-units').forEach(function(button) {
        button.addEventListener('click', function() {
            setTimeout(function() {
                button.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 300);
        });
    });

    // Add subtle entrance animation for cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.course-card, .unit-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        observer.observe(card);
    });
});
</script>
@endsection
