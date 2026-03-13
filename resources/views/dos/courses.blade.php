@extends('staff.partials.sidebar_dos')

@section('title', 'Courses Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-book-fill me-2"></i>
            Courses Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportCourses()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="bi bi-plus-circle me-1"></i> Add Course
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $courses->count() ?? 0 }}</h4>
                            <p class="card-text">Total Courses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book fs-1"></i>
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
                            <h4 class="card-title">{{ $courses->where('is_active', 1)->count() ?? 0 }}</h4>
                            <p class="card-text">Active Courses</p>
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
                            <h4 class="card-title">{{ $courses->where('is_active', 0)->count() ?? 0 }}</h4>
                            <p class="card-text">Inactive Courses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-pause-circle fs-1"></i>
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
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.courses.index') }}">
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
                        <label for="status_filter" class="form-label">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Search courses...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.courses.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Branch</th>
                            <th>Duration</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($courses) && $courses->count() > 0)
                            @foreach($courses as $course)
                                <tr>
                                    <td><strong>{{ $course->course_code ?? 'N/A' }}</strong></td>
                                    <td>{{ $course->course_name ?? 'N/A' }}</td>
                                    <td>{{ $course->branch->name ?? 'N/A' }}</td>
                                    <td>{{ $course->duration ?? 'N/A' }}</td>
                                    <td>RWF {{ number_format($course->fee ?? 0, 0) }}</td>
                                    <td>
                                        @if($course->is_active ?? false)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->students->count() ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="viewCourse({{ $course->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="editCourse({{ $course->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteCourse({{ $course->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mt-2">No courses found</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                            <i class="bi bi-plus-circle me-1"></i> Add First Course
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCourseForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="course_code" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" required>
                        </div>
                        <div class="col-md-6">
                            <label for="course_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @if(isset($branches))
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="duration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 2 years">
                        </div>
                        <div class="col-md-6">
                            <label for="fee" class="form-label">Course Fee (RWF)</label>
                            <input type="number" class="form-control" id="fee" name="fee" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewCourse(id) {
    // Implement view course functionality
    window.location.href = `/dos/courses/${id}`;
}

function editCourse(id) {
    // Implement edit course functionality
    window.location.href = `/dos/courses/${id}/edit`;
}

function deleteCourse(id) {
    if(confirm('Are you sure you want to delete this course?')) {
        // Implement delete course functionality
        window.location.href = `/dos/courses/${id}/delete`;
    }
}

function exportCourses() {
    // Implement export functionality
    window.location.href = `/dos/courses/export`;
}

// Form submission
document.getElementById('addCourseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/dos/courses/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Course added successfully!');
            location.reload();
        } else {
            alert('Error adding course: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding course');
    });
});
</script>
@endsection
