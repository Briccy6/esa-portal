@extends('staff.layout')

@section('title', 'IT Dashboard')

@section('content')
@include('partials.academic-year-banner')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <div class="text-uppercase small fw-semibold text-primary mb-1">Technology operations</div>
        <h1 class="h2 fw-bold text-gray-800 mb-1">IT Dashboard</h1>
        <p class="text-muted mb-0">A live view of platform usage, account activity, and access health.</p>
    </div>
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

<section class="it-overview mb-4" aria-labelledby="usage-heading">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
        <div>
            <div class="it-eyebrow">Platform pulse</div>
            <h2 class="h4 mb-1" id="usage-heading">Usage &amp; account activity</h2>
            <p class="mb-0 text-muted">Active means a successful portal request in the last 15 minutes. Page analytics show the last 30 days.</p>
        </div>
        <span class="it-live-indicator"><span></span> Activity tracking enabled</span>
    </div>

    <div class="row g-3 mb-4">
        @foreach($activityGroups as $group)
            <div class="col-6 col-xl-3">
                <article class="it-audience-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <span class="it-audience-label">{{ $group['label'] }}</span>
                        <span class="it-audience-icon"><i class="fas fa-{{ $loop->iteration === 1 ? 'user-graduate' : ($loop->iteration === 2 ? 'users' : ($loop->iteration === 3 ? 'chalkboard-teacher' : 'user-shield')) }}"></i></span>
                    </div>
                    <div class="it-audience-count">{{ number_format($group['active']) }}</div>
                    <div class="it-audience-caption">active now <span>of {{ number_format($group['total']) }}</span></div>
                </article>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="it-panel h-100">
                <div class="it-panel-heading">
                    <div>
                        <h3 class="h6 mb-1">Daily active accounts</h3>
                        <p class="text-muted small mb-0">Unique accounts visiting each day · last 7 days</p>
                    </div>
                    <span class="it-panel-icon"><i class="fas fa-chart-line"></i></span>
                </div>
                <div class="it-chart-wrap"><canvas id="activityTrendChart" aria-label="Daily active accounts by user type"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="it-panel h-100">
                <div class="it-panel-heading">
                    <div>
                        <h3 class="h6 mb-1">Most visited pages</h3>
                        <p class="text-muted small mb-0">Page visits · last 30 days</p>
                    </div>
                    <span class="it-panel-icon"><i class="fas fa-chart-bar"></i></span>
                </div>
                <div class="it-chart-wrap"><canvas id="popularPagesChart" aria-label="Most visited pages"></canvas></div>
                @if($popularPages->isEmpty())
                    <div class="it-chart-empty">Page analytics will appear as users browse the portal.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($activityGroups as $group)
            <div class="col-12 col-xl-6">
                <div class="it-panel h-100">
                    <div class="it-panel-heading">
                        <div>
                            <h3 class="h6 mb-1">{{ $group['label'] }} activity</h3>
                            <p class="text-muted small mb-0">Most recently active accounts</p>
                        </div>
                        <span class="it-panel-icon"><i class="fas fa-clock"></i></span>
                    </div>
                    @if($group['people']->isEmpty())
                        <div class="it-chart-empty">No recorded activity yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table it-activity-table align-middle mb-0">
                                <thead>
                                    <tr><th>Account</th><th>Last active</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($group['people'] as $person)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $person['name'] }}</div>
                                                @if($person['email'])
                                                    <small class="text-muted">{{ $person['email'] }}</small>
                                                @endif
                                            </td>
                                            <td class="text-nowrap" title="{{ $person['last_active_at']->format('Y-m-d H:i:s') }}">
                                                {{ $person['last_active_at']->diffForHumans() }}
                                            </td>
                                            <td>
                                                <span class="it-status {{ $person['is_active'] ? 'it-status-active' : 'it-status-idle' }}">
                                                    {{ $person['is_active'] ? 'Active' : 'Away' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>

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
                                            @php($user = $lockStatus->account)
                                            @if($user)
                                                @if($lockStatus->user_type === 'student')
                                                    {{ $user->full_name ?? 'N/A' }}
                                                @elseif($lockStatus->user_type === 'staff')
                                                    {{ $user->name ?? 'N/A' }}
                                                @elseif($lockStatus->user_type === 'teacher')
                                                    {{ $user->teacher_name ?? 'N/A' }}
                                                @else
                                                    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->email ?? 'N/A') }}
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
    .it-overview {
        padding: clamp(1rem, 2.4vw, 1.75rem);
        border: 1px solid #dfe7ef;
        border-radius: 18px;
        background:
            radial-gradient(ellipse at 92% 0%, rgba(35, 122, 162, .09), transparent 34%),
            linear-gradient(145deg, #f8fbfd 0%, #f2f6fa 100%);
    }

    .it-eyebrow {
        color: #26749a;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
        margin-bottom: .35rem;
    }

    .it-live-indicator {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .48rem .75rem;
        border: 1px solid #d8e8df;
        border-radius: 999px;
        background: rgba(255,255,255,.82);
        color: #476254;
        font-size: .78rem;
        font-weight: 600;
    }

    .it-live-indicator span {
        width: .5rem;
        height: .5rem;
        border-radius: 50%;
        background: #20a36b;
        box-shadow: 0 0 0 4px rgba(32,163,107,.12);
    }

    .it-audience-card,
    .it-panel {
        border: 1px solid #e3eaf0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 26px rgba(31, 51, 72, .045);
    }

    .it-audience-card {
        padding: 1rem 1.1rem;
        min-height: 132px;
    }

    .it-audience-label {
        color: #687789;
        font-size: .76rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .it-audience-icon,
    .it-panel-icon {
        display: inline-flex;
        width: 2rem;
        height: 2rem;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #edf5f8;
        color: #26749a;
    }

    .it-audience-count {
        margin-top: .55rem;
        color: #183044;
        font-size: 1.8rem;
        font-weight: 750;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .it-audience-caption {
        margin-top: .3rem;
        color: #27835e;
        font-size: .78rem;
        font-weight: 700;
    }

    .it-audience-caption span {
        color: #8995a2;
        font-weight: 500;
    }

    .it-panel {
        padding: 1.1rem 1.15rem;
    }

    .it-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: .8rem;
    }

    .it-panel-heading h3 {
        color: #21384a;
        font-weight: 700;
    }

    .it-chart-wrap {
        position: relative;
        height: 250px;
        min-width: 0;
    }

    .it-chart-empty {
        padding: .75rem 0 0;
        color: #8190a0;
        font-size: .85rem;
    }

    .it-activity-table {
        font-size: .84rem;
    }

    .it-activity-table thead th {
        color: #8794a2;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .it-activity-table > :not(caption) > * > * {
        padding: .65rem .5rem;
        border-bottom-color: #edf1f4;
    }

    .it-status {
        display: inline-flex;
        align-items: center;
        padding: .24rem .55rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
    }

    .it-status-active {
        background: #e8f6ef;
        color: #17804f;
    }

    .it-status-idle {
        background: #f0f2f4;
        color: #697887;
    }

    @media (max-width: 575.98px) {
        .it-audience-card {
            padding: .85rem;
            min-height: 124px;
        }

        .it-audience-count {
            font-size: 1.55rem;
        }

        .it-chart-wrap {
            height: 220px;
        }
    }

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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activityCanvas = document.getElementById('activityTrendChart');
        if (activityCanvas && window.Chart) {
            const activityDatasets = @json($activityChartDatasets);
            const activityColors = ['#247ba0', '#39a982', '#e3a23b', '#8168a8'];

            new Chart(activityCanvas, {
                type: 'line',
                data: {
                    labels: @json($activityChartLabels),
                    datasets: activityDatasets.map((dataset, index) => ({
                        ...dataset,
                        borderColor: activityColors[index],
                        backgroundColor: activityColors[index] + '20',
                        pointBackgroundColor: activityColors[index],
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                        tension: 0.35,
                        fill: false
                    }))
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 7, padding: 16 } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#edf1f4' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        const pagesCanvas = document.getElementById('popularPagesChart');
        if (pagesCanvas && window.Chart) {
            new Chart(pagesCanvas, {
                type: 'bar',
                data: {
                    labels: @json($popularPages->pluck('label')->values()),
                    datasets: [{
                        label: 'Visits',
                        data: @json($popularPages->pluck('visits')->values()),
                        backgroundColor: '#378baa',
                        borderRadius: 5,
                        maxBarThickness: 22
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#edf1f4' } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

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
