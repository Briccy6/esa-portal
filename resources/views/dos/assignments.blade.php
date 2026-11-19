@extends('dos.layout')

@section('title', 'Assignments Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-clipboard-check me-2"></i>
            Assignments Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="try { exportAssignments(); } catch(e) { console.error('Export assignments error:', e); alert('Unable to export assignments. Please try again.'); }">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                <i class="bi bi-plus-circle me-1"></i> Create Assignment
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
                            <h4 class="card-title">{{ $assignments->count() ?? 0 }}</h4>
                            <p class="card-text">Total Assignments</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clipboard-check fs-1"></i>
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
                            <h4 class="card-title">{{ $assignments->where('status', 'active')->count() ?? 0 }}</h4>
                            <p class="card-text">Active</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-play-circle fs-1"></i>
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
                            <h4 class="card-title">{{ $assignments->where('status', 'draft')->count() ?? 0 }}</h4>
                            <p class="card-text">Draft</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-pencil fs-1"></i>
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
                            <h4 class="card-title">{{ $assignments->where('status', 'completed')->count() ?? 0 }}</h4>
                            <p class="card-text">Completed</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.assignments.index') }}">
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
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.assignments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Assignment Title</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Submissions</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($assignments) && $assignments->count() > 0)
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->title }}</strong>
                                        @if($assignment->description)
                                            <br><small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->course->course_name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->teacher->teacher_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($assignment->due_date)
                                            {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y H:i') }}
                                            @if($assignment->due_date->isPast())
                                                <br><span class="badge bg-danger">Overdue</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No due date</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($assignment->status)
                                            @case('active')
                                                <span class="badge bg-success">Active</span>
                                                @break
                                            @case('draft')
                                                <span class="badge bg-warning">Draft</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">Completed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Unknown</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $assignment->submissions->count() ?? 0 }}</span>
                                        @if($assignment->max_submissions)
                                            <br><small class="text-muted">/ {{ $assignment->max_submissions }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary"
                                                    onclick="try { viewAssignment({{ $assignment->id }}); } catch(e) { console.error('View assignment error:', e); alert('Unable to view assignment. Please try again.'); }">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info"
                                                    onclick="try { viewSubmissions({{ $assignment->id }}); } catch(e) { console.error('View submissions error:', e); alert('Unable to view submissions. Please try again.'); }">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning"
                                                    onclick="try { editAssignment({{ $assignment->id }}); } catch(e) { console.error('Edit assignment error:', e); alert('Unable to edit assignment. Please try again.'); }">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($assignment->status === 'active')
                                                <button type="button" class="btn btn-outline-success"
                                                        onclick="try { publishAssignment({{ $assignment->id }}); } catch(e) { console.error('Publish assignment error:', e); alert('Unable to publish assignment. Please try again.'); }">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="try { deleteAssignment({{ $assignment->id }}); } catch(e) { console.error('Delete assignment error:', e); alert('Unable to delete assignment. Please try again.'); }">
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
                                        <i class="bi bi-clipboard-x fs-1"></i>
                                        <p class="mt-2">No assignments found</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                                            <i class="bi bi-plus-circle me-1"></i> Create First Assignment
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

<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAssignmentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label">Assignment Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label">Teacher</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                @if(isset($teachers))
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @if(isset($courses))
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                        </div>
                        <div class="col-md-6">
                            <label for="max_submissions" class="form-label">Max Submissions</label>
                            <input type="number" class="form-control" id="max_submissions" name="max_submissions" min="1" value="1">
                        </div>
                        <div class="col-md-6">
                            <label for="max_score" class="form-label">Max Score</label>
                            <input type="number" class="form-control" id="max_score" name="max_score" min="1" value="100">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="attachment" class="form-label">Attachment (Optional)</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                            <small class="text-muted">PDF, DOC, DOCX, PPT, PPTX files allowed</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allow_late_submission" name="allow_late_submission" value="1">
                                <label class="form-check-label" for="allow_late_submission">
                                    Allow late submissions
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewAssignment(id) {
    window.location.href = `/dos/assignments/${id}`;
}

function viewSubmissions(id) {
    window.location.href = `/dos/assignments/${id}/submissions`;
}

function editAssignment(id) {
    window.location.href = `/dos/assignments/${id}/edit`;
}

function publishAssignment(id) {
    if(confirm('Publish this assignment to students?')) {
        fetch(`/dos/assignments/${id}/publish`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Assignment published successfully!');
                location.reload();
            } else {
                alert('Error publishing assignment: ' + data.message);
            }
        });
    }
}

function deleteAssignment(id) {
    if(confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/dos/assignments/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function exportAssignments() {
    window.location.href = `/dos/assignments/export`;
}

// Form submission
document.getElementById('addAssignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/dos/assignments/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Assignment created successfully!');
            location.reload();
        } else {
            alert('Error creating assignment: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating assignment');
    });
});
</script>
@endsection
