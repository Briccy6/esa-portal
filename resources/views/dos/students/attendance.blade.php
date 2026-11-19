@extends('dos.layout')

@section('title', 'Student Attendance')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-calendar-check me-2"></i>
            Student Attendance Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                <i class="bi bi-pencil-square me-1"></i> Mark Attendance
            </button>
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
                                        @if($attendance->marked_at)
                                            {{ \Carbon\Carbon::parse($attendance->marked_at)->format('H:i:s') }}
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
                                            <button onclick="try { editAttendance({{ $attendance->id }}); } catch(e) { console.error('Edit attendance error:', e); alert('Unable to edit attendance. Please try again.'); }" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button onclick="try { viewStudent({{ $attendance->student->id }}); } catch(e) { console.error('View student error:', e); alert('Unable to view student. Please try again.'); }" class="btn btn-outline-info">
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
                                        <p class="small">Click "Mark Attendance" to start recording attendance</p>
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
</div>

<!-- Mark Attendance Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="markAttendanceModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Mark Student Attendance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dos.students.mark-attendance') }}" id="attendanceForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="branch_select" class="form-label">Branch *</label>
                            <select name="branch_id" id="branch_select" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="course_select" class="form-label">Course *</label>
                            <select name="course_id" id="course_select" class="form-select" required disabled>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="lesson_select" class="form-label">Lesson</label>
                            <select name="lesson_id" id="lesson_select" class="form-select">
                                <option value="">Select Lesson (Optional)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="attendance_date" class="form-label">Date *</label>
                            <input type="date" name="date" id="attendance_date" class="form-control" 
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quick Actions</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-success" onclick="try { markAll('present'); } catch(e) { console.error('Mark all present error:', e); alert('Unable to mark all present. Please try again.'); }">
                                    <i class="bi bi-check-circle"></i> All Present
                                </button>
                                <button type="button" class="btn btn-warning" onclick="try { markAll('late'); } catch(e) { console.error('Mark all late error:', e); alert('Unable to mark all late. Please try again.'); }">
                                    <i class="bi bi-clock"></i> All Late
                                </button>
                                <button type="button" class="btn btn-danger" onclick="try { markAll('absent'); } catch(e) { console.error('Mark all absent error:', e); alert('Unable to mark all absent. Please try again.'); }">
                                    <i class="bi bi-x-circle"></i> All Absent
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="studentsContainer">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1"></i>
                            <p class="mt-2">Please select branch and course to load students</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="bi bi-check-circle me-1"></i> Save Attendance
                    </button>
                </div>
            </form>
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
let currentStudents = [];

document.addEventListener('DOMContentLoaded', function() {
    // Handle branch selection
    document.getElementById('branch_select').addEventListener('change', function() {
        const branchId = this.value;
        const courseSelect = document.getElementById('course_select');
        const lessonSelect = document.getElementById('lesson_select');
        
        // Reset course and lesson selects
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = !branchId;
        lessonSelect.innerHTML = '<option value="">Select Lesson (Optional)</option>';
        lessonSelect.disabled = true;
        
        // Clear students container
        document.getElementById('studentsContainer').innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-people fs-1"></i>
                <p class="mt-2">Please select course to load students</p>
            </div>
        `;
        
        if (branchId) {
            // Load courses for selected branch
            fetch(`/api/branches/${branchId}/courses`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        data.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.textContent = course.course_name;
                            courseSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading courses:', error));
        }
    });
    
    // Handle course selection
    document.getElementById('course_select').addEventListener('change', function() {
        const branchId = document.getElementById('branch_select').value;
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_select');
        
        // Reset lesson select
        lessonSelect.innerHTML = '<option value="">Select Lesson (Optional)</option>';
        lessonSelect.disabled = !courseId;
        
        if (branchId && courseId) {
            // Load students for attendance
            loadStudentsForAttendance(branchId, courseId);
            
            // Load lessons for course
            fetch(`/api/courses/${courseId}/lessons`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.lessons && data.lessons.length > 0) {
                        data.lessons.forEach(lesson => {
                            const option = document.createElement('option');
                            option.value = lesson.id;
                            option.textContent = lesson.title || lesson.name;
                            lessonSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading lessons:', error));
        }
    });
    
    // Handle form submission
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const attendanceData = [];
        
        // Collect attendance data for each student
        currentStudents.forEach(student => {
            const statusSelect = document.querySelector(`input[name="attendance[${student.id}][status]"]:checked`);
            if (statusSelect) {
                attendanceData.push({
                    student_id: student.id,
                    status: statusSelect.value
                });
            }
        });
        
        if (attendanceData.length === 0) {
            alert('Please mark attendance for at least one student');
            return;
        }
        
        // Add attendance data to form data
        attendanceData.forEach((data, index) => {
            formData.append(`attendance[${index}][student_id]`, data.student_id);
            formData.append(`attendance[${index}][status]`, data.status);
        });
        
        // Submit the form
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and refresh page
                const modal = bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal'));
                modal.hide();
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking attendance');
        });
    });
});

function loadStudentsForAttendance(branchId, courseId) {
    fetch(`{{ route('dos.students.get-students-for-attendance') }}?branch_id=${branchId}&course_id=${courseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentStudents = data.students;
                displayStudentsForAttendance(data.students);
                document.getElementById('submitBtn').disabled = false;
            } else {
                console.error('Error loading students:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            document.getElementById('studentsContainer').innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <p class="mt-2">Error loading students</p>
                </div>
            `;
        });
}

function displayStudentsForAttendance(students) {
    const container = document.getElementById('studentsContainer');
    
    if (students.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-people fs-1"></i>
                <p class="mt-2">No students found for this course</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="40%">Student Name</th>
                        <th width="20%">Email</th>
                        <th width="40%" class="text-center">Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    students.forEach(student => {
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.9rem;">
                            ${student.full_name.charAt(0).toUpperCase()}
                        </div>
                        <strong>${student.full_name}</strong>
                    </div>
                </td>
                <td>${student.email}</td>
                <td>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="attendance[${student.id}][status]" id="present_${student.id}" value="present" checked>
                        <label class="btn btn-outline-success" for="present_${student.id}">
                            <i class="bi bi-check-circle"></i> Present
                        </label>
                        
                        <input type="radio" class="btn-check" name="attendance[${student.id}][status]" id="late_${student.id}" value="late">
                        <label class="btn btn-outline-warning" for="late_${student.id}">
                            <i class="bi bi-clock"></i> Late
                        </label>
                        
                        <input type="radio" class="btn-check" name="attendance[${student.id}][status]" id="absent_${student.id}" value="absent">
                        <label class="btn btn-outline-danger" for="absent_${student.id}">
                            <i class="bi bi-x-circle"></i> Absent
                        </label>
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function markAll(status) {
    currentStudents.forEach(student => {
        const radio = document.querySelector(`input[name="attendance[${student.id}][status]"][value="${status}"]`);
        if (radio) {
            radio.checked = true;
        }
    });
}

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
    csvContent += "Date,Student,Email,Branch,Course,Lesson,Marked Time,Status\n";
    
    // Add data rows
    @if(isset($attendances) && $attendances->count() > 0)
        @foreach($attendances as $attendance)
            csvContent += "{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }},{{ $attendance->student->full_name }},{{ $attendance->student->email }},{{ $attendance->student->branch->name ?? 'N/A' }},{{ $attendance->student->course->course_name ?? 'N/A' }},{{ $attendance->lesson->title ?? 'N/A' }},{{ $attendance->marked_at ? \Carbon\Carbon::parse($attendance->marked_at)->format('H:i:s') : 'N/A' }},{{ ucfirst($attendance->status) }}\n";
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
