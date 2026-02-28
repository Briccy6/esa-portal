@extends('students.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1 text-primary fw-bold">
                        <i class="fas fa-graduation-cap me-2"></i>My Grades
                    </h2>
                    <p class="text-muted mb-0">Track your academic performance across all courses</p>
                </div>
                <div class="badge bg-light text-dark fs-6 px-3 py-2">
                    Total Records: {{ count($marks) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-chart-line me-2"></i>Academic Results
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 text-uppercase fw-semibold text-muted small ps-4">
                                <i class="fas fa-book me-1"></i>Course
                            </th>
                            <th class="border-0 text-uppercase fw-semibold text-muted small">
                                <i class="fas fa-code-branch me-1"></i>Branch
                            </th>
                            <th class="border-0 text-uppercase fw-semibold text-muted small">
                                <i class="fas fa-file-alt me-1"></i>Exam
                            </th>
                            <th class="border-0 text-uppercase fw-semibold text-muted small text-center">
                                <i class="fas fa-star me-1"></i>Mark
                            </th>
                            <th class="border-0 text-uppercase fw-semibold text-muted small text-center">
                                <i class="fas fa-trophy me-1"></i>Position
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marks as $mark)
                            <tr class="grade-row">
                                <td class="ps-4 py-3">
                                    <div class="fw-semibold text-dark">
                                        {{ $mark->course->course_name ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                                        {{ $mark->branch->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark">
                                        {{ $mark->exam->title ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    @php
                                        $markValue = $mark->value;
                                        $markClass = 'text-success';
                                        $markIcon = 'fas fa-check-circle';
                                        
                                        if ($markValue < 50) {
                                            $markClass = 'text-danger';
                                            $markIcon = 'fas fa-exclamation-circle';
                                        } elseif ($markValue < 70) {
                                            $markClass = 'text-warning';
                                            $markIcon = 'fas fa-minus-circle';
                                        }
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="{{ $markIcon }} me-1 {{ $markClass }}"></i>
                                        <span class="fw-bold {{ $markClass }} fs-5">{{ $markValue }}%</span>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    @if($mark->position)
                                        <div class="position-badge">
                                            @php
                                                $positionClass = 'bg-secondary';
                                                $positionIcon = 'fas fa-medal';
                                                
                                                if ($mark->position == 1) {
                                                    $positionClass = 'bg-warning text-dark';
                                                    $positionIcon = 'fas fa-crown';
                                                } elseif ($mark->position <= 3) {
                                                    $positionClass = 'bg-success';
                                                } elseif ($mark->position <= 10) {
                                                    $positionClass = 'bg-primary';
                                                }
                                            @endphp
                                            <span class="badge {{ $positionClass }} px-3 py-2 rounded-pill">
                                                <i class="{{ $positionIcon }} me-1"></i>
                                                {{ $mark->position }}
                                                @if(isset($mark->total_students))
                                                    <span class="opacity-75">/ {{ $mark->total_students }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-graduation-cap text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <h5 class="text-muted mb-2">No Grades Available</h5>
                                        <p class="text-muted mb-0">Your grades will appear here once they are published by your instructors.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(count($marks) > 0)
        <div class="card-footer bg-light border-0 py-3">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-calculator text-primary mb-1"></i>
                        <div class="fw-semibold">Average</div>
                        <div class="text-muted small">
                            {{ number_format($marks->avg('value'), 1) }}%
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-arrow-up text-success mb-1"></i>
                        <div class="fw-semibold">Highest</div>
                        <div class="text-muted small">
                            {{ $marks->max('value') }}%
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-arrow-down text-danger mb-1"></i>
                        <div class="fw-semibold">Lowest</div>
                        <div class="text-muted small">
                            {{ $marks->min('value') }}%
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-list text-info mb-1"></i>
                        <div class="fw-semibold">Total</div>
                        <div class="text-muted small">
                            {{ count($marks) }} Results
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.grade-row {
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.grade-row:hover {
    background-color: rgba(102, 126, 234, 0.05);
    border-left-color: #667eea;
    transform: translateX(2px);
}

.position-badge .badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.stat-item {
    padding: 0.5rem;
}

.stat-item i {
    font-size: 1.25rem;
    display: block;
}

.empty-state {
    padding: 2rem;
}

.table th {
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    font-weight: 600;
    padding: 1rem 0.75rem;
}

.table td {
    vertical-align: middle;
    border-color: rgba(0,0,0,0.05);
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-radius: 12px 12px 0 0;
}

@media (max-width: 768px) {
    .table-responsive {
        border-radius: 8px;
    }
    
    .stat-item {
        margin-bottom: 1rem;
    }
    
    .grade-row:hover {
        transform: none;
    }
}
</style>
@endsection