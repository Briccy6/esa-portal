@extends('dos.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-clipboard-check me-2 text-primary"></i>
                        Review Daily Report
                    </h2>
                    <p class="text-muted mb-0">Review and approve daily teaching activities</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                    @if(!$daily->approved)
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-hourglass-half me-1"></i>Pending Review
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i>Approved
                        </span>
                    @endif
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Daily Information Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fas fa-info-circle me-2"></i>
                                Daily Report Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Date Information -->
                            <div class="info-item mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <label class="fw-semibold text-muted small">DATE</label>
                                        <p class="mb-0 fs-5 fw-medium">{{ \Carbon\Carbon::parse($daily->date)->format('F d, Y') }}</p>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($daily->date)->format('l') }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Teacher Information -->
                            <div class="info-item mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-user-tie text-info"></i>
                                    </div>
                                    <div>
                                        <label class="fw-semibold text-muted small">TEACHER</label>
                                        <p class="mb-0 fs-5 fw-medium">{{ $daily->teacher->teacher_name ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Information -->
                            <div class="info-item mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-book text-success"></i>
                                    </div>
                                    <div>
                                        <label class="fw-semibold text-muted small">COURSE</label>
                                        <p class="mb-0 fs-5 fw-medium">{{ $daily->course->course_name ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Lesson Information -->
                            <div class="info-item mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-bookmark text-warning"></i>
                                    </div>
                                    <div>
                                        <label class="fw-semibold text-muted small">LESSON</label>
                                        <p class="mb-0 fs-5 fw-medium">{{ $daily->lesson->title ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Unit Information -->
                            <div class="info-item mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-layer-group text-secondary"></i>
                                    </div>
                                    <div>
                                        <label class="fw-semibold text-muted small">UNIT</label>
                                        <p class="mb-0 fs-5 fw-medium">{{ $daily->unit->unit_name ?? 'Not Assigned' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activities Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-success text-white py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fas fa-tasks me-2"></i>
                                Teaching Activities
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $activities = is_array($daily->activity) ? $daily->activity : json_decode($daily->activity, true);
                            @endphp
                            
                            @if($activities && count($activities) > 0)
                                <div class="activities-list">
                                    @foreach($activities as $index => $activity)
                                        <div class="activity-item d-flex align-items-start mb-3 p-3 bg-light rounded border-start border-4 border-success">
                                            <div class="activity-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 0.875rem;">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-medium">{{ $activity }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-exclamation-triangle fs-2 mb-3"></i>
                                    <p class="mb-0">No activities recorded for this daily report</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Management Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-users me-2"></i>
                            Student Attendance Management
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" id="sort-attendance-btn" class="btn btn-sm btn-light">
                                <i class="bi bi-sort-alpha-down me-1"></i>Sort: A-Z
                            </button>
                            <div class="attendance-stats">
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-user-check me-1"></i>
                                    {{ $attendances->where('status', 'P')->count() }} Present
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-user-times me-1"></i>
                                    {{ $attendances->where('status', 'A')->count() }} Absent
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Box -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="studentSearch" class="form-control" placeholder="Search students...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select id="statusFilter" class="form-select">
                                <option value="">All Students</option>
                                <option value="P">Present Only</option>
                                <option value="A">Absent Only</option>
                                <option value="AR">Absent with Reason</option>
                                <option value="L">Late Only</option>
                            </select>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('dos.dailies.attendance', $daily->id) }}" id="attendanceForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover" id="attendanceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold py-3">
                                            <i class="fas fa-user text-primary me-2"></i>Student
                                        </th>
                                        <th class="fw-semibold py-3 text-center">
                                            <i class="fas fa-flag text-info me-2"></i>Current Status
                                        </th>
                                        <th class="fw-semibold py-3 text-center">
                                            <i class="fas fa-edit text-warning me-2"></i>Modify Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTableBody">
                                    @if($attendances->count() > 0)
                                        @foreach($attendances as $attendance)
                                            <tr class="attendance-row" data-status="{{ $attendance->status }}">
                                                <td class="align-middle py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="student-avatar bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-medium student-name">{{ $attendance->student->full_name ?? 'Unknown Student' }}</span>
                                                            <br>
                                                            <small class="text-muted">ID: {{ $attendance->student_id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle py-3 text-center">
                                                    @switch($attendance->status)
                                                        @case('P')
                                                            <span class="badge bg-success bg-opacity-15 text-success px-3 py-2">
                                                                <i class="fas fa-check-circle me-1"></i>Present
                                                            </span>
                                                            @break
                                                        @case('A')
                                                            <span class="badge bg-danger bg-opacity-15 text-danger px-3 py-2">
                                                                <i class="fas fa-times-circle me-1"></i>Absent
                                                            </span>
                                                            @break
                                                        @case('AR')
                                                            <span class="badge bg-warning bg-opacity-15 text-warning px-3 py-2">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Absent with Reason
                                                            </span>
                                                            @break
                                                        @case('L')
                                                            <span class="badge bg-info bg-opacity-15 text-info px-3 py-2">
                                                                <i class="fas fa-clock me-1"></i>Late
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary bg-opacity-15 text-secondary px-3 py-2">
                                                                <i class="fas fa-question-circle me-1"></i>Unknown
                                                            </span>
                                                    @endswitch
                                                </td>
                                                <td class="align-middle py-3 text-center">
                                                    <select name="attendance[{{ $attendance->id }}]" class="form-select form-select-sm attendance-select" style="max-width: 200px; margin: 0 auto;">
                                                        <option value="P" {{ $attendance->status == 'P' ? 'selected' : '' }}>
                                                            ✓ Present
                                                        </option>
                                                        <option value="A" {{ $attendance->status == 'A' ? 'selected' : '' }}>
                                                            ✗ Absent
                                                        </option>
                                                        <option value="AR" {{ $attendance->status == 'AR' ? 'selected' : '' }}>
                                                            ⚠ Absent with Reason
                                                        </option>
                                                        <option value="L" {{ $attendance->status == 'L' ? 'selected' : '' }}>
                                                            ⏰ Late
                                                        </option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-users-slash fs-1 mb-3 d-block"></i>
                                                    <h5>No Attendance Records Found</h5>
                                                    <p class="mb-0">No attendance records were found for this daily report.</p>
                                                    <p class="mb-0 small">This might be because no students are assigned to this course or attendance wasn't marked.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- No Results Message -->
                        <div id="noStudentsFound" class="text-center py-5 d-none">
                            <i class="fas fa-search fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No students found</h5>
                            <p class="text-muted mb-0">Try adjusting your search terms or filters</p>
                        </div>

                        <div class="mt-4 text-center">
                            @if($attendances->count() > 0)
                                <button type="submit" class="btn btn-warning btn-lg px-4" id="updateAttendanceBtn">
                                    <i class="fas fa-save me-2"></i>Update Attendance
                                </button>
                            @else
                                <div class="text-center">
                                    <p class="text-muted mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No attendance records available to update
                                    </p>
                                    <button type="button" class="btn btn-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                                        <i class="fas fa-user-check me-2"></i>Mark Attendance Now
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Approval Section -->
            <div class="card shadow-sm border-success">
                <div class="card-header bg-gradient-success text-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-check-double me-2"></i>
                        Daily Report Approval
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-4 fs-5">Review this daily report and approve when ready.</p>
                    <form method="POST" action="{{ route('dos.dailies.approve', $daily->id) }}" id="approvalForm">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-5" id="approveBtn">
                            <i class="fas fa-check-circle me-2"></i>Approve Daily Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .info-item {
        transition: all 0.2s ease;
        padding: 0.5rem;
        border-radius: 0.5rem;
    }
    
    .info-item:hover {
        background-color: #f8f9fa;
    }
    
    .icon-wrapper {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .activity-item {
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .attendance-row {
        transition: all 0.2s ease;
    }
    
    .attendance-row:hover {
        background-color: #f8f9fa;
    }
    
    .attendance-select {
        transition: all 0.2s ease;
        border: 2px solid #dee2e6;
    }
    
    .attendance-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    .attendance-select.changed {
        border-color: #ffc107;
        background-color: #fff3cd;
    }
    
    @media (max-width: 768px) {
        .attendance-stats {
            margin-top: 0.5rem;
        }
        
        .attendance-stats .badge {
            display: block;
            margin: 0.25rem 0;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
    }
</style>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSearch = document.getElementById('studentSearch');
    const statusFilter = document.getElementById('statusFilter');
    const attendanceRows = document.querySelectorAll('.attendance-row');
    const noStudentsFound = document.getElementById('noStudentsFound');
    const attendanceSelects = document.querySelectorAll('.attendance-select');
    const updateAttendanceBtn = document.getElementById('updateAttendanceBtn');
    const approveBtn = document.getElementById('approveBtn');

    // Track original values
    const originalValues = {};
    attendanceSelects.forEach(select => {
        originalValues[select.name] = select.value;
    });

    // Filter functionality
    function filterStudents() {
        const searchTerm = studentSearch.value.toLowerCase().trim();
        const statusValue = statusFilter.value;
        let visibleRows = 0;

        attendanceRows.forEach(row => {
            const studentName = row.querySelector('.student-name').textContent.toLowerCase();
            const studentStatus = row.getAttribute('data-status');
            
            const matchesSearch = searchTerm === '' || studentName.includes(searchTerm);
            const matchesStatus = statusValue === '' || studentStatus === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleRows === 0) {
            noStudentsFound.classList.remove('d-none');
        } else {
            noStudentsFound.classList.add('d-none');
        }
    }

    // Event listeners for filtering
    studentSearch.addEventListener('input', filterStudents);
    statusFilter.addEventListener('change', filterStudents);

    // Sort attendance button functionality
    const sortBtn = document.getElementById('sort-attendance-btn');
    if (sortBtn) {
        let isAscending = true;
        sortBtn.addEventListener('click', function() {
            const rows = Array.from(document.querySelectorAll('.attendance-row'));
            const tbody = document.querySelector('#attendanceTable tbody');

            rows.sort(function(a, b) {
                const nameA = a.querySelector('.student-name').textContent.trim();
                const nameB = b.querySelector('.student-name').textContent.trim();

                if (isAscending) {
                    return nameA.localeCompare(nameB);
                } else {
                    return nameB.localeCompare(nameA);
                }
            });

            // Re-append rows in sorted order
            rows.forEach(function(row) {
                tbody.appendChild(row);
            });

            // Update button state
            isAscending = !isAscending;
            if (isAscending) {
                sortBtn.innerHTML = '<i class="bi bi-sort-alpha-down me-1"></i>Sort: A-Z';
            } else {
                sortBtn.innerHTML = '<i class="bi bi-sort-alpha-up me-1"></i>Sort: Z-A';
            }
        });
    }

    // Track changes in attendance
    attendanceSelects.forEach(select => {
        select.addEventListener('change', function() {
            if (this.value !== originalValues[this.name]) {
                this.classList.add('changed');
                updateAttendanceBtn.classList.add('btn-warning');
                updateAttendanceBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Update Attendance (Changes Detected)';
            } else {
                this.classList.remove('changed');
            }
            
            // Check if any changes exist
            const hasChanges = Array.from(attendanceSelects).some(s => s.classList.contains('changed'));
            if (!hasChanges) {
                updateAttendanceBtn.classList.remove('btn-warning');
                updateAttendanceBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Attendance';
            }

            // Update the row's data-status attribute
            this.closest('.attendance-row').setAttribute('data-status', this.value);
        });
    });

    // Add loading states to buttons
    if (updateAttendanceBtn) {
        document.getElementById('attendanceForm').addEventListener('submit', function() {
            updateAttendanceBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            updateAttendanceBtn.disabled = true;
        });
    }

    if (approveBtn) {
        document.getElementById('approvalForm').addEventListener('submit', function(e) {
            // Show confirmation dialog
            if (!confirm('Are you sure you want to approve this daily report? This action cannot be undone.')) {
                e.preventDefault();
                return;
            }
            
            approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
            approveBtn.disabled = true;
        });
    }

    // Auto-save functionality (optional - commented out)
    /*
    let autoSaveTimeout;
    attendanceSelects.forEach(select => {
        select.addEventListener('change', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // Auto-save logic here
                console.log('Auto-saving changes...');
            }, 2000);
        });
    });
    */
});
</script>

<!-- Mark Attendance Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="markAttendanceModalLabel">
                    <i class="fas fa-user-check me-2"></i>Mark Student Attendance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('dos.dailies.mark-attendance', $daily->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Mark attendance for all students in this course. You can mark individual students as Present, Absent, Absent with Reason, or Late.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Get all students for this course
                                    $courseStudents = \App\Models\Student::where('course_id', $daily->course_id)->get();
                                @endphp
                                
                                @if($courseStudents->count() > 0)
                                    @foreach($courseStudents as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium">{{ $student->full_name ?? 'Unknown Student' }}</span>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $student->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="present_{{ $student->id }}" value="P" checked>
                                                    <label class="btn btn-outline-success" for="present_{{ $student->id }}">
                                                        <i class="fas fa-check"></i> Present
                                                    </label>
                                                    
                                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="absent_{{ $student->id }}" value="A">
                                                    <label class="btn btn-outline-danger" for="absent_{{ $student->id }}">
                                                        <i class="fas fa-times"></i> Absent
                                                    </label>
                                                    
                                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="absent_reason_{{ $student->id }}" value="AR">
                                                    <label class="btn btn-outline-warning" for="absent_reason_{{ $student->id }}">
                                                        <i class="fas fa-exclamation"></i> A. Reason
                                                    </label>
                                                    
                                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="late_{{ $student->id }}" value="L">
                                                    <label class="btn btn-outline-info" for="late_{{ $student->id }}">
                                                        <i class="fas fa-clock"></i> Late
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users-slash fs-2 mb-3 d-block"></i>
                                                <h5>No Students Found</h5>
                                                <p class="mb-0">No students are assigned to this course.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    @if($courseStudents->count() > 0)
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Attendance
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection