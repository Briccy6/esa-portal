@extends('dos.layout')

@section('title', 'Student List')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Student List
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportStudents()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <a href="{{ route('dos.students.StudentCards') }}" class="btn btn-info">
                <i class="bi bi-id-card me-1"></i> Student Cards
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
                            <h4 class="card-title">{{ $studentStats->total ?? 0 }}</h4>
                            <p class="card-text">Total Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
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
                            <h4 class="card-title">{{ $studentStats->active ?? 0 }}</h4>
                            <p class="card-text">Active</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1"></i>
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
                            <h4 class="card-title">{{ $studentStats->inactive ?? 0 }}</h4>
                            <p class="card-text">Inactive</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-x fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $branches->count() ?? 0 }}</h4>
                            <p class="card-text">Branches</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-geo-alt fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.students.index') }}">
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
                        <label for="status_filter" class="form-label">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (Paid Fees)</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (Unpaid Fees)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.students.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Academic Year</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($students) && $students->count() > 0)
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $student->full_name }}</strong>
                                                @if($student->student_id)
                                                    <br><small class="text-muted">ID: {{ $student->student_id }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->phone ?? 'N/A' }}</td>
                                    <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                    <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                    <td>{{ $student->academicYear->year ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $student->payed_registration_fees ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $student->payed_registration_fees ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('dos.students.edit', $student->id) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button onclick="try { generateCard({{ $student->id }}); } catch(e) { console.error('Card generation error:', e); alert('Unable to generate card. Please try again.'); }" class="btn btn-outline-info">
                                                <i class="bi bi-id-card"></i>
                                            </button>
                                            <button onclick="try { viewDetails({{ $student->id }}); } catch(e) { console.error('View details error:', e); alert('Unable to view details. Please try again.'); }" class="btn btn-outline-success">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1"></i>
                                        <p class="mt-2">No students found</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($students) && $students->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $students->links() }}
                </div>
            @endif
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

function viewDetails(studentId) {
    // Implement view details functionality
    window.location.href = `/dos/students/${studentId}/edit`;
}

function exportStudents() {
    window.location.href = `/dos/students/export`;
}
</script>
@endsection
