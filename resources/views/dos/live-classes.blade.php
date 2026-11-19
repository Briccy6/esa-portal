@extends('dos.layout')

@section('title', 'Live Classes Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-broadcast me-2"></i>
            Live Classes Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportLiveClasses()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLiveClassModal">
                <i class="bi bi-plus-circle me-1"></i> Schedule Live Class
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
                            <h4 class="card-title">{{ $liveClasses->count() ?? 0 }}</h4>
                            <p class="card-text">Total Live Classes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-camera-video fs-1"></i>
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
                            <h4 class="card-title">{{ $liveClasses->where('status', 'scheduled')->count() ?? 0 }}</h4>
                            <p class="card-text">Scheduled</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-check fs-1"></i>
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
                            <h4 class="card-title">{{ $liveClasses->where('status', 'live')->count() ?? 0 }}</h4>
                            <p class="card-text">Live Now</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-broadcast fs-1"></i>
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
                            <h4 class="card-title">{{ $liveClasses->where('status', 'completed')->count() ?? 0 }}</h4>
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
            <form method="GET" action="{{ route('dos.live-classes.index') }}">
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
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>Live Now</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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
                        <a href="{{ route('dos.live-classes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Classes Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Teacher</th>
                            <th>Course</th>
                            <th>Scheduled Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($liveClasses) && $liveClasses->count() > 0)
                            @foreach($liveClasses as $liveClass)
                                <tr>
                                    <td>
                                        <strong>{{ $liveClass->title }}</strong>
                                        @if($liveClass->description)
                                            <br><small class="text-muted">{{ Str::limit($liveClass->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{
                                            ($liveClass->teacher?->teacher_name)
                                            ?: ($liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('teacher.teacher_name') ?? collect())?->filter()?->unique()?->implode(', ') ?? 'N/A')
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            ($liveClass->courses?->pluck('course_name')?->filter()?->implode(', '))
                                            ?: ($liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('course.course_name') ?? collect())?->filter()?->unique()?->implode(', ') ?? 'N/A')
                                        }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($liveClass->start_time)->format('d M Y H:i') }}
                                        @if($liveClass->timezone)
                                            <br><small class="text-muted">{{ $liveClass->timezone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $liveClass->duration ?? 'N/A' }}</td>
                                    <td>
                                        @switch($liveClass->status)
                                            @case('scheduled')
                                                <span class="badge bg-primary">Scheduled</span>
                                                @break
                                            @case('live')
                                                <span class="badge bg-danger animate-pulse">Live Now</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Completed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Unknown</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $liveClass->participants->count() ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="viewLiveClass({{ $liveClass->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($liveClass->status === 'scheduled')
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="startLiveClass({{ $liveClass->id }})">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            @endif
                                            @if($liveClass->status === 'live')
                                                <button type="button" class="btn btn-outline-warning" 
                                                        onclick="endLiveClass({{ $liveClass->id }})">
                                                    <i class="bi bi-stop-circle"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="editLiveClass({{ $liveClass->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteLiveClass({{ $liveClass->id }})">
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
                                        <i class="bi bi-camera-video-off fs-1"></i>
                                        <p class="mt-2">No live classes found</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLiveClassModal">
                                            <i class="bi bi-plus-circle me-1"></i> Schedule First Live Class
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

<!-- Add Live Class Modal -->
<div class="modal fade" id="addLiveClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Live Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLiveClassForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label">Class Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="15" value="60">
                        </div>
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Africa/Kigali">Africa/Kigali</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="max_participants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" min="1" value="50">
                        </div>
                        <div class="col-12">
                            <label for="meeting_link" class="form-label">Meeting Link (Optional)</label>
                            <input type="url" class="form-control" id="meeting_link" name="meeting_link" 
                                   placeholder="https://meet.google.com/...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse {
    animation: pulse 2s infinite;
}
</style>

<script>
function viewLiveClass(id) {
    window.location.href = `/dos/live-classes/${id}`;
}

function editLiveClass(id) {
    window.location.href = `/dos/live-classes/${id}/edit`;
}

function deleteLiveClass(id) {
    if(confirm('Are you sure you want to delete this live class?')) {
        window.location.href = `/dos/live-classes/${id}/delete`;
    }
}

function startLiveClass(id) {
    if(confirm('Start this live class now?')) {
        fetch(`/dos/live-classes/${id}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Live class started successfully!');
                location.reload();
            } else {
                alert('Error starting live class: ' + data.message);
            }
        });
    }
}

function endLiveClass(id) {
    if(confirm('End this live class now?')) {
        fetch(`/dos/live-classes/${id}/end`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Live class ended successfully!');
                location.reload();
            } else {
                alert('Error ending live class: ' + data.message);
            }
        });
    }
}

function exportLiveClasses() {
    window.location.href = `/dos/live-classes/export`;
}

// Form submission
document.getElementById('addLiveClassForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/dos/live-classes/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Live class scheduled successfully!');
            location.reload();
        } else {
            alert('Error scheduling live class: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error scheduling live class');
    });
});
</script>
@endsection
