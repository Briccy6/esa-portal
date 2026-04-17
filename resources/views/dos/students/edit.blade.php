@extends('staff.partials.sidebar_dos')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-person me-2"></i>
            Edit Student
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('dos.students.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
            <button onclick="generateCard({{ $student->id }})" class="btn btn-info">
                <i class="bi bi-id-card me-1"></i> Generate Card
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dos.students.update', $student->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="{{ old('full_name', $student->full_name) }}" required>
                                @error('full_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $student->email) }}" required>
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $student->phone) }}">
                                @error('phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" 
                                       value="{{ old('student_id', $student->student_id) }}">
                                @error('student_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="branch_id" class="form-label">Branch *</label>
                                <select class="form-select" id="branch_id" name="branch_id" required>
                                    <option value="">Select Branch</option>
                                    @if(isset($branches))
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $student->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('branch_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="course_id" class="form-label">Course *</label>
                                <select class="form-select" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @if(isset($courses))
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('course_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">Academic Year *</label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Select Academic Year</option>
                                    @if(isset($academicYears))
                                        @foreach($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}" {{ old('academic_year_id', $student->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                                {{ $academicYear->year }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('academic_year_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                                <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                       value="{{ old('parent_name', $student->parent_name) }}">
                                @error('parent_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="parent_phone" class="form-label">Parent/Guardian Phone</label>
                                <input type="tel" class="form-control" id="parent_phone" name="parent_phone" 
                                       value="{{ old('parent_phone', $student->parent_phone) }}">
                                @error('parent_phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('dos.students.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Update Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Student Card Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Student Card Preview</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg bg-primary text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($student->full_name, 0, 1)) }}
                        </div>
                    </div>
                    
                    <h5 class="text-center">{{ $student->full_name }}</h5>
                    
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Student ID:</strong></td>
                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $student->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $student->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Branch:</strong></td>
                            <td>{{ $student->branch->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Course:</strong></td>
                            <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge {{ $student->payed_registration_fees ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $student->payed_registration_fees ? 'Active (Paid Fees)' : 'Inactive (Unpaid Fees)' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button onclick="generateCard({{ $student->id }})" class="btn btn-primary">
                            <i class="bi bi-id-card me-2"></i>Generate ID Card
                        </button>
                        <button onclick="viewAttendance()" class="btn btn-info">
                            <i class="bi bi-calendar-check me-2"></i>View Attendance
                        </button>
                        <button onclick="viewMarks()" class="btn btn-success">
                            <i class="bi bi-clipboard-check me-2"></i>View Marks
                        </button>
                        <button onclick="sendEmail()" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope me-2"></i>Send Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateCard(studentId) {
    fetch(`/dos/students/${studentId}/generate-card`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Student card generated successfully!');
        } else {
            alert('Error generating card: ' + data.message);
        }
    });
}

function viewAttendance() {
    // Implement view attendance functionality
    window.location.href = `/dos/students/attendance?student_id={{ $student->id }}`;
}

function viewMarks() {
    // Implement view marks functionality
    alert('View marks functionality coming soon!');
}

function sendEmail() {
    // Implement send email functionality
    alert('Send email functionality coming soon!');
}
</script>
@endsection
