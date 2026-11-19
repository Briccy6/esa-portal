@extends('dos.layout')

@section('title', 'Teacher Daily Activities')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-calendar-check me-2"></i>Teacher Daily Activities Approval
        </h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="bulkApprove()">
                <i class="bi bi-check-circle me-1"></i> Bulk Approve Selected
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Activities</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dos.teacher-dailies.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Course</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">All Teachers</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->teacher_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('dos.teacher-dailies.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <form id="bulkForm" method="POST" action="{{ route('dos.teacher-dailies.bulk-approve') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input" onchange="toggleAll()">
                                </th>
                                <th>Date</th>
                                <th>Teacher</th>
                                <th>Branch</th>
                                <th>Course</th>
                                <th>Lesson</th>
                                <th>Unit</th>
                                <th class="text-center">Students</th>
                                <th class="text-center">Absences</th>
                                <th class="text-center">Hours</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailies as $daily)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $daily->id }}" class="daily-checkbox form-check-input">
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($daily->date)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ substr($daily->teacher->teacher_name ?? 'N/A', 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $daily->teacher->teacher_name ?? 'N/A' }}</div>
                                            <small class="text-muted">ID: {{ $daily->teacher_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $daily->branch->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($daily->course_ids && is_array($daily->course_ids) && count($daily->course_ids) > 1)
                                            @foreach(array_unique($daily->course_ids) as $courseId)
                                                <span class="badge bg-primary me-1">{{ $courseNamesById->get($courseId, 'N/A') }}</span>
                                            @endforeach
                                        @else
                                            {{ $daily->course->course_name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $daily->lesson->title ?? 'N/A' }}</td>
                                    <td>{{ $daily->unit->unit_name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $daily->total_students }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">{{ $daily->absences }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $daily->hours_studied }}</span>
                                    </td>
                                    <td>
                                        @if($daily->approved)
                                            <span class="badge bg-success">Approved</span>
                                            @if($daily->approved_at)
                                                <small class="text-muted d-block">{{ $daily->approved_at->diffForHumans() }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('dos.teacher-dailies.show', $daily->id) }}" class="btn btn-outline-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$daily->approved)
                                                <button type="button" class="btn btn-outline-success" onclick="approveDaily({{ $daily->id }})" title="Approve">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteDaily({{ $daily->id }})" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-3">No daily activities found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        @if($dailies->hasPages())
        <div class="card-footer">
            {{ $dailies->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<script>
function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.daily-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

function bulkApprove() {
    const form = document.getElementById('bulkForm');
    const checkboxes = document.querySelectorAll('.daily-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one item to approve.');
        return;
    }
    
    if (confirm('Are you sure you want to approve ' + checkboxes.length + ' daily activities?')) {
        form.submit();
    }
}

function approveDaily(id) {
    if (confirm('Are you sure you want to approve this daily activity?')) {
        fetch('{{ route('dos.teacher-dailies.approve', ':id') }}'.replace(':id', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

function deleteDaily(id) {
    if (confirm('Are you sure you want to delete this daily activity? This will also delete all related attendance records. This action cannot be undone.')) {
        fetch('{{ route('dos.teacher-dailies.destroy', ':id') }}'.replace(':id', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
</script>
@endsection
