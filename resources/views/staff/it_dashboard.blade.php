@extends('staff.layout')

@section('title', 'IT Dashboard')

@section('content')
@include('partials.academic-year-banner')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">IT Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('staff.it.account-locking.settings') }}" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-cog me-1"></i>Account Locking Settings
            </a>
        </div>
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#downloadModal">
                <i class="fas fa-download me-1"></i>Download NESA Documents
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('staff.it.dashboard') }}" class="row g-3">
            <div class="col-md-4">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select">
                    <option value="">All Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedAcademicYear && $selectedAcademicYear->id == $year->id ? 'selected' : '' }}>
                            {{ $year->year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="branch_id" class="form-label">Branch</label>
                <select name="branch_id" id="branch_id" class="form-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-primary h-100">
            <div class="stat-card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Students</div>
                <div class="stat-card-value tabular-nums">{{ number_format($stats['total_students']) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-danger h-100">
            <div class="stat-card-icon">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Blocked Students</div>
                <div class="stat-card-value tabular-nums">{{ number_format($stats['blocked_students']) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-warning h-100">
            <div class="stat-card-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Dismissed Students</div>
                <div class="stat-card-value tabular-nums">{{ number_format($stats['dismissed_students']) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-success h-100">
            <div class="stat-card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Profile Completed</div>
                <div class="stat-card-value tabular-nums">{{ number_format($stats['profile_completed']) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Completion Stats -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Profile Completion
                </h5>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 30px;">
                    @php
                        $completionRate = $stats['total_students'] > 0 
                            ? round(($stats['profile_completed'] / $stats['total_students']) * 100) 
                            : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $completionRate }}%;" 
                         aria-valuenow="{{ $completionRate }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $completionRate }}% Complete
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Completed: {{ number_format($stats['profile_completed']) }}</span>
                    <span class="text-warning"><i class="fas fa-exclamation-circle me-1"></i>Incomplete: {{ number_format($stats['profile_incomplete']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-shield me-2"></i>Student Status
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="display-6 text-primary">{{ number_format($stats['total_students'] - $stats['blocked_students'] - $stats['dismissed_students']) }}</div>
                        <small class="text-muted">Active</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-danger">{{ number_format($stats['blocked_students']) }}</div>
                        <small class="text-muted">Blocked</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-warning">{{ number_format($stats['dismissed_students']) }}</div>
                        <small class="text-muted">Dismissed</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Lock Status Management -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock me-2 text-primary"></i>Account Lock Status
                </h5>
                <a href="{{ route('staff.it.account-locking.settings') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-cog me-1"></i>Manage Settings
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User Type</th>
                                <th>User ID</th>
                                <th>Name/Email</th>
                                <th>Lock Status</th>
                                <th>Lock Reason</th>
                                <th>Locked At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lockedAccounts = \App\Models\AccountLockStatus::with('userable')
                                    ->where('is_locked', true)
                                    ->latest('locked_at')
                                    ->limit(10)
                                    ->get();
                            @endphp
                            @if($lockedAccounts->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <p>No locked accounts found.</p>
                                    </td>
                                </tr>
                            @else
                                @foreach($lockedAccounts as $lockStatus)
                                    <tr>
                                        <td>{{ ucfirst($lockStatus->user_type) }}</td>
                                        <td>{{ $lockStatus->user_id }}</td>
                                        <td>
                                            @php
                                                $user = $lockStatus->getUser();
                                            @endphp
                                            @if($user)
                                                @if($lockStatus->user_type === 'student')
                                                    {{ $user->full_name ?? 'N/A' }}
                                                @elseif($lockStatus->user_type === 'staff')
                                                    {{ $user->name ?? 'N/A' }}
                                                @else
                                                    {{ $user->email ?? 'N/A' }}
                                                @endif
                                            @else
                                                <span class="text-muted">User not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lockStatus->is_locked)
                                                <span class="badge bg-danger">Locked</span>
                                            @else
                                                <span class="badge bg-success">Unlocked</span>
                                            @endif
                                        </td>
                                        <td>{{ $lockStatus->lock_reason ?? 'N/A' }}</td>
                                        <td>{{ $lockStatus->locked_at ? $lockStatus->locked_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                        <td>
                                            @if($lockStatus->is_locked)
                                                <form method="POST" action="{{ route('staff.it.account-locking.unlock-by-id', $lockStatus->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to unlock this account?')">
                                                        <i class="fas fa-unlock me-1"></i>Unlock
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Lists -->
<div class="row g-4">
    <!-- Blocked Students -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-ban me-2 text-danger"></i>Blocked Students
                </h5>
                <span class="badge bg-danger">{{ $blockedStudentsList->count() }}</span>
            </div>
            <div class="card-body">
                @if($blockedStudentsList->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>No blocked students found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Reg Number</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Block Reason</th>
                                    <th>Blocked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blockedStudentsList as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $student->block_reason ?? 'N/A' }}</td>
                                        <td>{{ $student->blocked_at ? $student->blocked_at->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Dismissed Students -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-times me-2 text-warning"></i>Dismissed Students
                </h5>
                <span class="badge bg-warning">{{ $dismissedStudentsList->count() }}</span>
            </div>
            <div class="card-body">
                @if($dismissedStudentsList->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>No dismissed students found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Reg Number</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Dismissal Reason</th>
                                    <th>Dismissal Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dismissedStudentsList as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $student->dismissal_reason ?? 'N/A' }}</td>
                                        <td>{{ $student->dismissal_date ? $student->dismissal_date->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Incomplete Profile Students -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-circle me-2 text-warning"></i>Incomplete Profile Students
                </h5>
                <span class="badge bg-warning">{{ $incompleteProfileStudents->count() }}</span>
            </div>
            <div class="card-body">
                @if($incompleteProfileStudents->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>All students have completed their profiles.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Reg Number</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Missing Fields</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incompleteProfileStudents as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $missing = [];
                                                if(empty($student->email)) $missing[] = 'Email';
                                                if(empty($student->phone)) $missing[] = 'Phone';
                                                if(empty($student->sex)) $missing[] = 'Sex';
                                                if(empty($student->identification_type)) $missing[] = 'ID Type';
                                                if(empty($student->id_number)) $missing[] = 'ID Number';
                                            @endphp
                                            <span class="badge bg-warning">{{ implode(', ', $missing) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Completed Profile Students -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-check-circle me-2 text-success"></i>Completed Profile Students
                </h5>
                <span class="badge bg-success">{{ $completedProfileStudents->count() }}</span>
            </div>
            <div class="card-body">
                @if($completedProfileStudents->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user fa-3x mb-3"></i>
                        <p>No students with completed profiles found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Reg Number</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedProfileStudents as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->phone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Download Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">
                    <i class="fas fa-download me-2"></i>Download NESA Registration Documents
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('staff.it.download-nesa') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_category" class="form-label">Select Students</label>
                        <select class="form-select" id="student_category" name="student_category">
                            <option value="all">All Students</option>
                            <option value="completed">Profile Completed Only</option>
                            <option value="incomplete">Profile Incomplete Only</option>
                            <option value="selected">Select Individual Students</option>
                        </select>
                    </div>

                    <div class="mb-3" id="student_selection" style="display: none;">
                        <label class="form-label">Select Students</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllStudents">
                            <label class="form-check-label" for="selectAllStudents">Select All</label>
                        </div>
                        <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            @foreach($completedProfileStudents as $student)
                                <div class="form-check">
                                    <input class="form-check-input student-checkbox" type="checkbox"
                                           name="student_ids[]" value="{{ $student->id }}" id="student_{{ $student->id }}">
                                    <label class="form-check-label" for="student_{{ $student->id }}">
                                        {{ $student->full_name }} ({{ $student->reg_number }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Documents to Include</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="name" id="doc_name" checked>
                                    <label class="form-check-label" for="doc_name">Student Name</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="email" id="doc_email" checked>
                                    <label class="form-check-label" for="doc_email">Email</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="phone" id="doc_phone" checked>
                                    <label class="form-check-label" for="doc_phone">Telephone</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="id_number" id="doc_id_number" checked>
                                    <label class="form-check-label" for="doc_id_number">ID Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="id_photo" id="doc_id_photo" checked>
                                    <label class="form-check-label" for="doc_id_photo">ID Photo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="passport_photo" id="doc_passport_photo" checked>
                                    <label class="form-check-label" for="doc_passport_photo">Passport Photo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="result_slip" id="doc_result_slip" checked>
                                    <label class="form-check-label" for="doc_result_slip">Result Slip Documents</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documents[]" value="passport" id="doc_passport" checked>
                                    <label class="form-check-label" for="doc_passport">Passport Document</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download ZIP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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

    .stat-card-danger::before {
        background: linear-gradient(180deg, #ef4444, #dc2626);
    }

    .stat-card-warning::before {
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }

    .stat-card-success::before {
        background: linear-gradient(180deg, #10b981, #059669);
    }

    .stat-card-info::before {
        background: linear-gradient(180deg, #06b6d4, #0891b2);
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

    .stat-card-danger .stat-card-icon {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .stat-card-warning .stat-card-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .stat-card-success .stat-card-icon {
        background: linear-gradient(135deg, #10b981, #059669);
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

    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentCategory = document.getElementById('student_category');
        const studentSelection = document.getElementById('student_selection');
        const selectAllStudents = document.getElementById('selectAllStudents');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');

        studentCategory.addEventListener('change', function() {
            if (this.value === 'selected') {
                studentSelection.style.display = 'block';
            } else {
                studentSelection.style.display = 'none';
                // Uncheck all when switching away from selected
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                selectAllStudents.checked = false;
            }
        });

        selectAllStudents.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Form submission validation
        const downloadForm = document.querySelector('#downloadModal form');
        downloadForm.addEventListener('submit', function(e) {
            const category = studentCategory.value;
            if (category === 'selected') {
                const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one student.');
                }
            }
        });
    });
</script>
@endsection
