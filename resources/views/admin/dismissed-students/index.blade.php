@extends('admin.layout')

@section('title', 'Dismissed Students Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-gradient">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title-modern">
                            <i class="bi bi-person-x-fill text-danger me-3"></i>
                            Dismissed Students Management
                        </h1>
                        <p class="page-subtitle-modern">View and manage dismissed student records with complete audit trail</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-modern btn-primary-modern">
                            <i class="bi bi-arrow-left me-2"></i>Back to Active Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-modern alert-success-modern alert-dismissible fade show shadow-lg" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-3 alert-icon"></i>
                        <div class="alert-content">
                            <strong>Success!</strong> {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="stats-card-modern stats-danger-modern">
                <div class="stats-icon-modern">
                    <i class="bi bi-person-x-fill"></i>
                </div>
                <div class="stats-content-modern">
                    <h3 class="stats-number-modern">{{ $totalDismissed }}</h3>
                    <p class="stats-label-modern">Total Dismissed</p>
                    <div class="stats-trend">
                        <small class="text-muted">All time records</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="stats-card-modern stats-warning-modern">
                <div class="stats-icon-modern">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stats-content-modern">
                    <h3 class="stats-number-modern">{{ $monthlyDismissed }}</h3>
                    <p class="stats-label-modern">This Month</p>
                    <div class="stats-trend">
                        <small class="text-muted">{{ now()->format('F Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="stats-card-modern stats-info-modern">
                <div class="stats-icon-modern">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stats-content-modern">
                    <h3 class="stats-number-modern">{{ $yearlyDismissed }}</h3>
                    <p class="stats-label-modern">This Year</p>
                    <div class="stats-trend">
                        <small class="text-muted">{{ now()->format('Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card-modern">
                <div class="card-header-modern">
                    <h5 class="filter-title">
                        <i class="bi bi-funnel me-2"></i>Filter & Search
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Clear All
                    </button>
                </div>
                <div class="card-body-modern">
                    <form method="GET" action="{{ route('admin.dismissed-students.index') }}" class="filter-form-modern">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-modern">Search Student</label>
                                <div class="input-group-modern">
                                    <span class="input-group-text-modern">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control-modern" 
                                           placeholder="Name, Reg Number, Email..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-modern">Branch</label>
                                <select name="branch_id" class="form-select-modern">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-modern">Course</label>
                                <select name="course_id" class="form-select-modern">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name ?? $course->abbreviation }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-modern">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-modern btn-primary-modern">
                                        <i class="bi bi-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card-modern">
                <div class="table-header-modern">
                    <h5 class="table-title">
                        <i class="bi bi-people me-2"></i>
                        Dismissed Students ({{ $dismissedStudents->total() }})
                    </h5>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportData()">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="table-responsive-modern">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Student Info</th>
                                <th>Academic Details</th>
                                <th>Dismissal Info</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dismissedStudents as $dismissedStudent)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="student-info-modern">
                                            <div class="student-avatar">
                                                <i class="bi bi-person-circle"></i>
                                            </div>
                                            <div class="student-details">
                                                <h6 class="student-name">{{ $dismissedStudent->full_name }}</h6>
                                                <p class="student-meta">
                                                    <i class="bi bi-hash me-1"></i>{{ $dismissedStudent->reg_number }}
                                                </p>
                                                <p class="student-meta">
                                                    <i class="bi bi-envelope me-1"></i>{{ $dismissedStudent->email }}
                                                </p>
                                                <p class="student-meta">
                                                    <i class="bi bi-phone me-1"></i>{{ $dismissedStudent->phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="academic-info-modern">
                                            <span class="badge-modern badge-branch">{{ $dismissedStudent->branch->name ?? 'N/A' }}</span>
                                            <span class="badge-modern badge-course">{{ $dismissedStudent->course->course_name ?? $dismissedStudent->course->abbreviation ?? 'N/A' }}</span>
                                            <span class="badge-modern badge-year">{{ $dismissedStudent->academicYear->year ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dismissal-info-modern">
                                            <div class="dismissal-date">
                                                <i class="bi bi-calendar-x me-1"></i>
                                                {{ $dismissedStudent->formattedDismissalDate }}
                                            </div>
                                            <div class="dismissal-reason">
                                                <strong>Reason:</strong>
                                                <p class="reason-text">{{ Str::limit($dismissedStudent->dismissal_reason, 50) }}</p>
                                            </div>
                                            @if($dismissedStudent->dismissal_notes)
                                                <div class="dismissal-notes">
                                                    <small class="text-muted">
                                                        <i class="bi bi-chat-left-text me-1"></i>
                                                        {{ Str::limit($dismissedStudent->dismissal_notes, 30) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons-modern">
                                            <a href="{{ route('admin.dismissed-students.show', $dismissedStudent) }}" 
                                               class="btn btn-sm btn-modern btn-info-modern me-1"
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.dismissed-students.restore', $dismissedStudent) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirmRestore('{{ $dismissedStudent->full_name }}')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-modern btn-success-modern"
                                                        title="Restore Student">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <i class="bi bi-inbox empty-icon"></i>
                                            <h5 class="empty-title">No Dismissed Students Found</h5>
                                            <p class="empty-text">There are no dismissed students matching your criteria.</p>
                                            <button class="btn btn-modern btn-primary-modern" onclick="clearFilters()">
                                                <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($dismissedStudents->hasPages())
                    <div class="pagination-modern">
                        {{ $dismissedStudents->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Modern Page Styles */
.page-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.page-title-modern {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle-modern {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.btn-modern {
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Stats Cards */
.stats-card-modern {
    border-radius: 15px;
    padding: 1.5rem;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.stats-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
}

.stats-danger-modern {
    --gradient-start: #f5365c;
    --gradient-end: #f56036;
}

.stats-warning-modern {
    --gradient-start: #fb6340;
    --gradient-end: #fbb140;
}

.stats-info-modern {
    --gradient-start: #11cdef;
    --gradient-end: #1171ef;
}

.stats-icon-modern {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: white;
}

.stats-danger-modern .stats-icon-modern {
    background: linear-gradient(135deg, #f5365c, #f56036);
}

.stats-warning-modern .stats-icon-modern {
    background: linear-gradient(135deg, #fb6340, #fbb140);
}

.stats-info-modern .stats-icon-modern {
    background: linear-gradient(135deg, #11cdef, #1171ef);
}

.stats-number-modern {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.stats-label-modern {
    font-size: 1rem;
    font-weight: 600;
    color: #718096;
    margin-bottom: 0.5rem;
}

/* Filter Card */
.filter-card-modern {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    background: white;
}

.card-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px 15px 0 0;
    padding: 1.25rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: between;
    align-items: center;
}

.filter-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.card-body-modern {
    padding: 1.5rem;
}

.form-label-modern {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.input-group-modern {
    position: relative;
}

.input-group-text-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    border-radius: 10px 0 0 10px;
}

.form-control-modern, .form-select-modern {
    border-radius: 0 10px 10px 0;
    border: 1px solid #e2e8f0;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control-modern:focus, .form-select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Table Styles */
.table-card-modern {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    background: white;
    overflow: hidden;
}

.table-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.25rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.table-responsive-modern {
    border-radius: 0;
}

.table-modern {
    margin-bottom: 0;
}

.table-modern th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    border: none;
    padding: 1rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
}

.table-row-modern:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s ease;
}

/* Student Info Styles */
.student-info-modern {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.student-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.student-details {
    flex: 1;
}

.student-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
    font-size: 1rem;
}

.student-meta {
    color: #718096;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
}

/* Academic Info Styles */
.academic-info-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.badge-modern {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-branch {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge-course {
    background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
    color: white;
}

.badge-year {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
    color: white;
}

/* Dismissal Info Styles */
.dismissal-info-modern {
    font-size: 0.9rem;
}

.dismissal-date {
    color: #f5365c;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.dismissal-reason {
    margin-bottom: 0.5rem;
}

.reason-text {
    color: #4a5568;
    margin-bottom: 0;
    line-height: 1.4;
}

.dismissal-notes {
    margin-bottom: 0;
}

/* Action Buttons */
.action-buttons-modern {
    display: flex;
    gap: 0.5rem;
}

.btn-info-modern {
    background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
    color: white;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    border: none;
    transition: all 0.3s ease;
}

.btn-info-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(17, 205, 239, 0.3);
}

.btn-success-modern {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
    color: white;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    border: none;
    transition: all 0.3s ease;
}

.btn-success-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(45, 206, 137, 0.3);
}

/* Alert Styles */
.alert-modern {
    border-radius: 10px;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.alert-success-modern {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.alert-icon {
    font-size: 1.5rem;
}

.alert-content {
    flex: 1;
}

/* Empty State */
.empty-state-modern {
    text-align: center;
    padding: 3rem;
}

.empty-icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
}

.empty-title {
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.empty-text {
    color: #718096;
    margin-bottom: 1.5rem;
}

/* Pagination */
.pagination-modern {
    padding: 1.25rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header-gradient {
        padding: 1.5rem;
    }
    
    .page-title-modern {
        font-size: 1.5rem;
    }
    
    .student-info-modern {
        flex-direction: column;
        text-align: center;
    }
    
    .academic-info-modern {
        justify-content: center;
    }
    
    .action-buttons-modern {
        justify-content: center;
    }
    
    .table-modern th,
    .table-modern td {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .header-actions {
        margin-top: 1rem;
    }
    
    .page-header-gradient .d-flex {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<!-- JavaScript -->
<script>
function clearFilters() {
    window.location.href = "{{ route('admin.dismissed-students.index') }}";
}

function confirmRestore(studentName) {
    return confirm(`Are you sure you want to restore "${studentName}" back to active students?`);
}

function exportData() {
    // Add export functionality here
    alert('Export functionality coming soon!');
}
</script>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stats-card stats-danger">
                <div class="stats-icon">
                    <i class="bi bi-person-x-fill"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $totalDismissed }}</h3>
                    <p class="stats-label">Total Dismissed</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stats-card stats-warning">
                <div class="stats-icon">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $monthlyDismissed }}</h3>
                    <p class="stats-label">This Month</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stats-card stats-info">
                <div class="stats-icon">
                    <i class="bi bi-calendar-year"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $yearlyDismissed }}</h3>
                    <p class="stats-label">This Year</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dismissed-students.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Name, Reg #, Email, Phone...">
                    </div>
                    <div class="col-md-2">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="course_id" class="form-label">Course</label>
                        <select class="form-select" id="course_id" name="course_id">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->abbreviation ?? $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="dismissal_date_from" class="form-label">Dismissal Date From</label>
                        <input type="date" class="form-control" id="dismissal_date_from" name="dismissal_date_from" 
                               value="{{ request('dismissal_date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="dismissal_date_to" class="form-label">Dismissal Date To</label>
                        <input type="date" class="form-control" id="dismissal_date_to" name="dismissal_date_to" 
                               value="{{ request('dismissal_date_to') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <a href="{{ route('admin.dismissed-students.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Dismissed Students Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>Dismissed Students
                <span class="badge bg-danger ms-2">{{ $dismissedStudents->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Info</th>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Dismissal Date</th>
                            <th>Reason</th>
                            <th>Dismissed By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dismissedStudents as $dismissedStudent)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ strtoupper(substr($dismissedStudent->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $dismissedStudent->full_name }}</div>
                                            <small class="text-muted">
                                                {{ $dismissedStudent->reg_number }}<br>
                                                {{ $dismissedStudent->email }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $dismissedStudent->branch->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $dismissedStudent->course->abbreviation ?? $dismissedStudent->course->course_name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $dismissedStudent->formatted_dismissal_date }}</div>
                                    <small class="text-muted">{{ $dismissedStudent->dismissal_date->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $dismissedStudent->dismissal_reason }}">
                                        {{ $dismissedStudent->dismissal_reason_summary }}
                                    </div>
                                </td>
                                <td>
                                    <small>{{ $dismissedStudent->dismissedBy->name ?? 'System' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.dismissed-students.show', $dismissedStudent) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.dismissed-students.restore', $dismissedStudent) }}" 
                                              onsubmit="return confirm('Are you sure you want to restore this student to active records?')" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Student">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-1 d-block mb-3"></i>
                                        <h5>No dismissed students found</h5>
                                        <p>There are no dismissed students in the system.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($dismissedStudents->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $dismissedStudents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-card.stats-danger {
        border-left: 5px solid #dc3545;
    }
    
    .stats-card.stats-warning {
        border-left: 5px solid #ffc107;
    }
    
    .stats-card.stats-info {
        border-left: 5px solid #17a2b8;
    }
    
    .stats-danger .stats-icon {
        color: #dc3545;
    }
    
    .stats-warning .stats-icon {
        color: #ffc107;
    }
    
    .stats-info .stats-icon {
        color: #17a2b8;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 14px;
        font-weight: bold;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush
@endsection
