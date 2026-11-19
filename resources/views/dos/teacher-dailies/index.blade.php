@extends('dos.layout')

@section('title', 'Teacher Daily Activities')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-calendar-check me-2"></i>Teacher Daily Activities Approval</h2>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.teacher-dailies.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">All Teachers</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->teacher_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('dos.teacher-dailies.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-3">
        <button type="button" class="btn btn-success" onclick="try { bulkApprove(); } catch(e) { console.error('Bulk approve error:', e); alert('Unable to bulk approve. Please try again.'); }">
            <i class="bi bi-check-circle me-2"></i>Bulk Approve Selected
        </button>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <form id="bulkForm" method="POST" action="{{ route('dos.teacher-dailies.bulk-approve') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                </th>
                                <th>Date</th>
                                <th>Teacher</th>
                                <th>Branch</th>
                                <th>Course</th>
                                <th>Lesson</th>
                                <th>Unit</th>
                                <th>Students</th>
                                <th>Absences</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailies as $daily)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $daily->id }}" class="daily-checkbox">
                                    </td>
                                    <td>{{ $daily->date }}</td>
                                    <td>{{ $daily->teacher->teacher_name ?? 'N/A' }}</td>
                                    <td>{{ $daily->branch->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($daily->course_ids && is_array($daily->course_ids) && count($daily->course_ids) > 1)
                                            @foreach(array_unique($daily->course_ids) as $courseId)
                                                <span class="badge bg-primary">{{ $courseNamesById->get($courseId, 'N/A') }}</span>
                                                @if(!$loop->last) <br>@endif
                                            @endforeach
                                        @else
                                            {{ $daily->course->course_name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $daily->lesson->title ?? 'N/A' }}</td>
                                    <td>{{ $daily->unit->unit_name ?? 'N/A' }}</td>
                                    <td>{{ $daily->total_students }}</td>
                                    <td>{{ $daily->absences }}</td>
                                    <td>{{ $daily->hours_studied }}</td>
                                    <td>
                                        @if($daily->approved)
                                            <span class="badge bg-success">Approved</span>
                                            @if($daily->approved_at)
                                                <small class="text-muted d-block">{{ $daily->approved_at->diffForHumans() }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark">Pending Approval</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dos.teacher-dailies.show', $daily->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        @if(!$daily->approved)
                                            <button type="button" class="btn btn-sm btn-success" onclick="try { approveDaily({{ $daily->id }}); } catch(e) { console.error('Approve error:', e); alert('Unable to approve. Please try again.'); }">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" onclick="try { deleteDaily({{ $daily->id }}); } catch(e) { console.error('Delete error:', e); alert('Unable to delete. Please try again.'); }">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No daily activities found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    {{ $dailies->links() }}
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
