@extends('staff.partials.sidebar_dos')

@section('title', 'Student Attendance')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-calendar-check me-2"></i>
            Student Attendance
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportAttendance()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <a href="{{ route('dos.students.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $attendances->count() }}</h4>
                            <p class="card-text">Total Records</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $attendances->where('status', 'present')->count() }}</h4>
                            <p class="card-text">Present</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $attendances->where('status', 'late')->count() }}</h4>
                            <p class="card-text">Late</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $attendances->where('status', 'absent')->count() }}</h4>
                            <p class="card-text">Absent</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.students.attendance') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="branch_filter" class="form-label">Branch</label>
                        <select name="branch" id="branch_filter" class="form-select">
                            <option value="">All Branches</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="course_filter" class="form-label">Course</label>
                        <select name="course" id="course_filter" class="form-select">
                            <option value="">All Courses</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_filter" class="form-label">Date</label>
                        <input type="date" name="date" id="date_filter" class="form-control" 
                               value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.students.attendance') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Lesson</th>
                            <th>Check-in Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($attendances) && $attendances->count() > 0)
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($attendance->student->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $attendance->student->full_name }}</strong>
                                                <br><small class="text-muted">{{ $attendance->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $attendance->student->branch->name ?? 'N/A' }}</td>
                                    <td>{{ $attendance->student->course->course_name ?? 'N/A' }}</td>
                                    <td>{{ $attendance->lesson->title ?? 'N/A' }}</td>
                                    <td>
                                        @if($attendance->check_in_time)
                                            {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($attendance->status)
                                            @case('present')
                                                <span class="badge bg-success">Present</span>
                                                @break
                                            @case('late')
                                                <span class="badge bg-warning">Late</span>
                                                @break
                                            @case('absent')
                                                <span class="badge bg-danger">Absent</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Unknown</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button onclick="editAttendance({{ $attendance->id }})" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button onclick="viewStudent({{ $attendance->student->id }})" class="btn btn-outline-info">
                                                <i class="bi bi-person"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-calendar-x fs-1"></i>
                                        <p class="mt-2">No attendance records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($attendances) && $attendances->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Summary -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="attendance-stat">
                                <div class="stat-number text-success">{{ $attendances->where('status', 'present')->count() }}</div>
                                <div class="stat-label">Present</div>
                                <div class="stat-percentage">{{ $attendances->count() > 0 ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 1) : 0 }}%</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="attendance-stat">
                                <div class="stat-number text-warning">{{ $attendances->where('status', 'late')->count() }}</div>
                                <div class="stat-label">Late</div>
                                <div class="stat-percentage">{{ $attendances->count() > 0 ? round(($attendances->where('status', 'late')->count() / $attendances->count()) * 100, 1) : 0 }}%</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="attendance-stat">
                                <div class="stat-number text-danger">{{ $attendances->where('status', 'absent')->count() }}</div>
                                <div class="stat-label">Absent</div>
                                <div class="stat-percentage">{{ $attendances->count() > 0 ? round(($attendances->where('status', 'absent')->count() / $attendances->count()) * 100, 1) : 0 }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button onclick="markAllPresent()" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Mark All Present
                        </button>
                        <button onclick="generateReport()" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                        </button>
                        <button onclick="importAttendance()" class="btn btn-outline-secondary">
                            <i class="bi bi-upload me-2"></i>Import Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.attendance-stat {
    padding: 1rem;
}
.stat-number {
    font-size: 2rem;
    font-weight: bold;
}
.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
}
.stat-percentage {
    font-size: 0.8rem;
    color: #868e96;
}
</style>

<script>
function editAttendance(attendanceId) {
    // Implement edit attendance functionality
    alert('Edit attendance functionality coming soon!');
}

function viewStudent(studentId) {
    window.location.href = `/dos/students/${studentId}/edit`;
}

function exportAttendance() {
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add header
    csvContent += "Date,Student,Email,Branch,Course,Lesson,Check-in Time,Status\n";
    
    // Add data rows
    @if(isset($attendances) && $attendances->count() > 0)
        @foreach($attendances as $attendance)
            csvContent += "{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }},{{ $attendance->student->full_name }},{{ $attendance->student->email }},{{ $attendance->student->branch->name ?? 'N/A' }},{{ $attendance->student->course->course_name ?? 'N/A' }},{{ $attendance->lesson->title ?? 'N/A' }},{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') : 'N/A' }},{{ ucfirst($attendance->status) }}\n";
        @endforeach
    @endif
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "attendance_report_{{ now()->format('Y-m-d') }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function markAllPresent() {
    if(confirm('Mark all students as present for today?')) {
        alert('Mark all present functionality coming soon!');
    }
}

function generateReport() {
    window.print();
}

function importAttendance() {
    alert('Import attendance functionality coming soon!');
}
</script>

<style media="print">
.btn-group {
    display: none !important;
}
.card {
    break-inside: avoid;
    page-break-inside: avoid;
}
</style>
@endsection
