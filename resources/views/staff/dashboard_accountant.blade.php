@extends('staff.partials.sidebar_sec')

@section('title', 'Academic Dashboard')

@section('content')
@include('partials.academic-year-banner')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">Academic Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-download me-1"></i>Export
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <i class="fas fa-calendar me-1"></i>
            This Month
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-primary h-100">
            <div class="stat-card-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Branches</div>
                <div class="stat-card-value tabular-nums">{{ count($branchStats) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-success h-100">
            <div class="stat-card-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Students</div>
                <div class="stat-card-value tabular-nums">{{ array_sum(array_column($branchStats, 'studentsCount')) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-info h-100">
            <div class="stat-card-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Active Courses</div>
                <div class="stat-card-value tabular-nums">{{ array_sum(array_column($branchStats, 'courseCount') ?? array_fill(0, count($branchStats), 0)) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-warning h-100">
            <div class="stat-card-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Teachers</div>
                <div class="stat-card-value tabular-nums">{{ array_sum(array_column($branchStats, 'teacherCount') ?? array_fill(0, count($branchStats), 0)) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Branch Details -->
<div class="row g-4">
    @foreach($branchStats as $index => $branchData)
    <div class="col-12 col-lg-6">
        <div class="branch-card h-100">
            <div class="branch-card-header">
                <div class="branch-card-title">
                    <i class="fas fa-building me-2"></i>
                    <span>{{ $branchData['branch']->name }}</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-icon btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="#">View Details</a></li>
                        <li><a class="dropdown-item" href="#">Export Report</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div class="branch-card-body">
                <!-- Branch Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-box stat-box-success">
                        <div class="stat-box-label">Students</div>
                        <div class="stat-box-number tabular-nums">{{ $branchData['studentsCount'] ?? 0 }}</div>
                    </div>
                    <div class="stat-box stat-box-info">
                        <div class="stat-box-label">Courses</div>
                        <div class="stat-box-number tabular-nums">{{ $branchData['courseCount'] ?? count($branchData['courseData'] ?? []) }}</div>
                    </div>
                    <div class="stat-box stat-box-warning">
                        <div class="stat-box-label">Teachers</div>
                        <div class="stat-box-number tabular-nums">{{ $branchData['teacherCount'] ?? 0 }}</div>
                    </div>
                    <div class="stat-box stat-box-secondary">
                        <div class="stat-box-label">Classes</div>
                        <div class="stat-box-number tabular-nums">{{ $branchData['classCount'] ?? 0 }}</div>
                    </div>
                </div>

                <!-- Course Distribution -->
                <div class="course-distribution">
                    <h6 class="section-subtitle">Course Distribution</h6>
                    @if(!empty($branchData['courseData']))
                    <div class="progress-container">
                        @foreach($branchData['courseData'] as $course)
                            <?php
                            $percentage = $branchData['studentsCount'] > 0
                                ? round(($course['count'] / $branchData['studentsCount']) * 100)
                                : 0;
                            ?>
                            <div class="progress-segment"
                                 style="width: {{ $percentage }}%; background-color: {{ ['#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#8b5cf6'][$index % 5] }};"
                                 title="{{ $course['name'] }}: {{ $course['count'] }} students ({{ $percentage }}%)">
                                {{ $percentage >= 10 ? ($course['abbreviation'] ?? substr($course['name'], 0, 3)) : '' }}
                            </div>
                        @endforeach
                    </div>
                    <div class="course-legend">
                        @foreach($branchData['courseData'] as $course)
                            <div class="legend-item">
                                <span class="legend-badge tabular-nums">{{ $course['count'] }}</span>
                                <span class="legend-text">{{ $course['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        <span>No courses added yet for this academic year</span>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn btn-primary btn-sm" onclick="viewBranchDetails({{ $branchData['branch']->id }})">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    <button class="btn btn-success btn-sm" onclick="viewStudents({{ $branchData['branch']->id }})">
                        <i class="fas fa-users me-1"></i>Students
                    </button>
                    <button class="btn btn-info btn-sm" onclick="viewCourses({{ $branchData['branch']->id }})">
                        <i class="fas fa-book me-1"></i>Courses
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="viewTeachers({{ $branchData['branch']->id }})">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Teachers
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Academic Flow Chart Section -->
<div class="row g-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-card-header">
                <h6 class="chart-card-title">
                    <i class="fas fa-project-diagram me-2"></i>Academic Workflow Flowchart
                </h6>
            </div>
            <div class="chart-card-body">
                <div class="flowchart-container">
                    <div class="flowchart-row">
                        <div class="flowchart-node flowchart-start">
                            <div class="flowchart-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flowchart-label">Student Enrollment</div>
                        </div>
                        <div class="flowchart-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="flowchart-label">Course Selection</div>
                        </div>
                        <div class="flowchart-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="flowchart-label">Class Scheduling</div>
                        </div>
                    </div>

                    <div class="flowchart-row">
                        <div class="flowchart-arrow-down">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>

                    <div class="flowchart-row">
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="flowchart-label">Teacher Assignment</div>
                        </div>
                        <div class="flowchart-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="flowchart-label">Learning Activities</div>
                        </div>
                        <div class="flowchart-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="flowchart-label">Assessment</div>
                        </div>
                    </div>

                    <div class="flowchart-row">
                        <div class="flowchart-arrow-down">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>

                    <div class="flowchart-row">
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="flowchart-label">NESA Registration</div>
                        </div>
                        <div class="flowchart-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="flowchart-node">
                            <div class="flowchart-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="flowchart-label">Certification</div>
                        </div>
                        <div class="flowchart-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="flowchart-node flowchart-end">
                            <div class="flowchart-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="flowchart-label">Graduation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Action functions
    function viewBranchDetails(branchId) {
        window.location.href = `/staff/branches/${branchId}`;
    }

    function viewStudents(branchId) {
        window.location.href = `/staff/students?branch=${branchId}`;
    }

    function viewCourses(branchId) {
        window.location.href = `/staff/courses?branch=${branchId}`;
    }

    function viewTeachers(branchId) {
        window.location.href = `/staff/teachers?branch=${branchId}`;
    }
</script>

<style>
    /* Tabular Numbers for alignment */
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
    }

    .stat-card-primary::before {
        background: linear-gradient(180deg, #3b82f6, #1d4ed8);
    }

    .stat-card-success::before {
        background: linear-gradient(180deg, #10b981, #059669);
    }

    .stat-card-info::before {
        background: linear-gradient(180deg, #06b6d4, #0891b2);
    }

    .stat-card-warning::before {
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-card-primary .stat-card-icon {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .stat-card-success .stat-card-icon {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .stat-card-info .stat-card-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .stat-card-warning .stat-card-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .stat-card-content {
        flex: 1;
    }

    .stat-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .stat-card-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
    }

    /* Branch Cards */
    .branch-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .branch-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .branch-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .branch-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
    }

    .branch-card-title i {
        color: #3b82f6;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #6b7280;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    .branch-card-body {
        padding: 1.25rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .stat-box {
        padding: 0.875rem;
        border-radius: 8px;
        text-align: center;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }

    .stat-box-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        margin-bottom: 0.375rem;
    }

    .stat-box-number {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
    }

    .stat-box-success .stat-box-number {
        color: #10b981;
    }

    .stat-box-warning .stat-box-number {
        color: #f59e0b;
    }

    .stat-box-info .stat-box-number {
        color: #06b6d4;
    }

    .stat-box-secondary .stat-box-number {
        color: #6b7280;
    }

    /* Course Distribution */
    .course-distribution {
        margin-bottom: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .section-subtitle {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.625rem;
    }

    .progress-container {
        height: 28px;
        background: #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        display: flex;
        margin-bottom: 0.625rem;
    }

    .progress-segment {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: width 0.5s ease;
        border-right: 1px solid rgba(255, 255, 255, 0.2);
    }

    .progress-segment:last-child {
        border-right: none;
    }

    .course-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.625rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8125rem;
        color: #6b7280;
    }

    .legend-badge {
        background: #e5e7eb;
        color: #374151;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        color: #9ca3af;
        text-align: center;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .empty-state span {
        font-size: 0.875rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .action-buttons .btn {
        flex: 1;
        min-width: 100px;
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .chart-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .chart-card-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .chart-card-title i {
        color: #3b82f6;
    }

    .chart-card-body {
        padding: 1.25rem;
    }

    .chart-container {
        position: relative;
        height: 350px;
    }

    .chart-container-doughnut {
        height: 400px;
    }

    /* Flowchart Styles */
    .flowchart-container {
        padding: 2rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
    }

    .flowchart-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .flowchart-row:last-child {
        margin-bottom: 0;
    }

    .flowchart-node {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem;
        min-width: 140px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .flowchart-node:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
    }

    .flowchart-start {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    }

    .flowchart-start .flowchart-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .flowchart-end {
        border-color: #8b5cf6;
        background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    }

    .flowchart-end .flowchart-icon {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .flowchart-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1.25rem;
    }

    .flowchart-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
    }

    .flowchart-arrow {
        color: #9ca3af;
        font-size: 1.5rem;
        padding: 0 0.5rem;
    }

    .flowchart-arrow-down {
        color: #9ca3af;
        font-size: 1.5rem;
        display: flex;
        justify-content: center;
        padding: 0.5rem 0;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stat-card {
            padding: 1rem;
            gap: 0.875rem;
        }

        .stat-card-icon {
            width: 42px;
            height: 42px;
            font-size: 1.125rem;
        }

        .stat-card-value {
            font-size: 1.375rem;
        }

        .branch-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.625rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.625rem;
        }

        .stat-box {
            padding: 0.75rem;
        }

        .stat-box-number {
            font-size: 1rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }

        .chart-container {
            height: 300px;
        }

        .chart-container-doughnut {
            height: 350px;
        }
    }

    @media (max-width: 576px) {
        .stat-card {
            flex-direction: column;
            text-align: center;
            gap: 0.625rem;
            padding: 1rem;
        }

        .stat-card::before {
            top: 0;
            left: 0;
            right: 0;
            bottom: auto;
            width: auto;
            height: 3px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .progress-container {
            height: 24px;
        }

        .course-legend {
            flex-direction: column;
            gap: 0.5rem;
        }

        .chart-card-header,
        .branch-card-header {
            padding: 0.875rem;
        }

        .chart-card-body,
        .branch-card-body {
            padding: 1rem;
        }

        /* Flowchart Responsive */
        .flowchart-container {
            padding: 1rem;
        }

        .flowchart-row {
            flex-direction: column;
            gap: 0.75rem;
        }

        .flowchart-node {
            min-width: 100%;
            padding: 1rem;
        }

        .flowchart-arrow {
            transform: rotate(90deg);
            padding: 0.25rem;
        }

        .flowchart-arrow-down {
            display: none;
        }
    }
</style>
@endsection
