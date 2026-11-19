@extends('dos.layout')

@section('title', 'Live Class Details')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-broadcast me-2"></i>
            Live Class Details
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('dos.live-classes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <div class="btn-group">
                <a href="{{ route('dos.live-classes.edit', $liveClass->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                @if($liveClass->status === 'scheduled')
                    <button onclick="startLiveClass({{ $liveClass->id }})" class="btn btn-success">
                        <i class="bi bi-play-circle me-1"></i> Start
                    </button>
                @endif
                @if($liveClass->status === 'live')
                    <a href="{{ route('dos.live-classes.room', $liveClass->id) }}" class="btn btn-primary">
                        <i class="bi bi-camera-video me-1"></i> Join Room
                    </a>
                    <button onclick="endLiveClass({{ $liveClass->id }})" class="btn btn-danger">
                        <i class="bi bi-stop-circle me-1"></i> End
                    </button>
                @endif
                <button onclick="deleteLiveClass({{ $liveClass->id }})" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Main Details Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        {{ $liveClass->title }}
                        <span class="badge badge-{{ $liveClass->status === 'scheduled' ? 'primary' : ($liveClass->status === 'live' ? 'danger' : 'success') }} ms-2">
                            {{ ucfirst($liveClass->status) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($liveClass->description)
                        <p class="text-muted">{{ $liveClass->description }}</p>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Class Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Teacher:</strong></td>
                                    <td>{{ $liveClass->teacher->teacher_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $liveClass->course->course_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td>{{ $liveClass->branches->first()->name ?? 'N/A' }}</td>
                                </tr>
                                @if($liveClass->lesson)
                                    <tr>
                                        <td><strong>Lesson:</strong></td>
                                        <td>{{ $liveClass->lesson->title }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Schedule Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Start Time:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($liveClass->start_time)->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $liveClass->duration }} minutes</td>
                                </tr>
                                <tr>
                                    <td><strong>Timezone:</strong></td>
                                    <td>{{ $liveClass->timezone ?? 'Africa/Kigali' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Max Participants:</strong></td>
                                    <td>{{ $liveClass->max_participants ?? 'Unlimited' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($liveClass->meeting_link)
                        <div class="alert alert-info mt-3">
                            <h6><i class="bi bi-link-45deg me-2"></i>Meeting Link</h6>
                            <a href="{{ $liveClass->meeting_link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Join Meeting
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Participants Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Participants ({{ $liveClass->participants->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($liveClass->participants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Joined At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($liveClass->participants as $participant)
                                        <tr>
                                            <td>{{ $participant->name ?? 'N/A' }}</td>
                                            <td>{{ $participant->email ?? 'N/A' }}</td>
                                            <td>{{ $participant->pivot->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No participants yet</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Status Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="status-icon mb-3">
                            @switch($liveClass->status)
                                @case('scheduled')
                                    <i class="bi bi-calendar-check text-primary" style="font-size: 3rem;"></i>
                                    <p class="text-primary mt-2">Scheduled</p>
                                    @break
                                @case('live')
                                    <i class="bi bi-broadcast text-danger" style="font-size: 3rem;"></i>
                                    <p class="text-danger mt-2 animate-pulse">Live Now</p>
                                    @break
                                @case('completed')
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                    <p class="text-success mt-2">Completed</p>
                                    @break
                            @endswitch
                        </div>
                        
                        @if($liveClass->status === 'scheduled')
                            <p class="text-muted small">
                                Starts in: {{ \Carbon\Carbon::parse($liveClass->start_time)->diffForHumans() }}
                            </p>
                        @endif
                        
                        @if($liveClass->started_at)
                            <p class="text-muted small">
                                Started: {{ \Carbon\Carbon::parse($liveClass->started_at)->format('M d, Y H:i') }}
                            </p>
                        @endif
                        
                        @if($liveClass->ended_at)
                            <p class="text-muted small">
                                Ended: {{ \Carbon\Carbon::parse($liveClass->ended_at)->format('M d, Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($liveClass->status === 'scheduled')
                            <button onclick="startLiveClass({{ $liveClass->id }})" class="btn btn-success">
                                <i class="bi bi-play-circle me-2"></i>Start Class
                            </button>
                        @endif
                        
                        @if($liveClass->status === 'live')
                            <a href="{{ route('dos.live-classes.room', $liveClass->id) }}" class="btn btn-primary">
                                <i class="bi bi-camera-video me-2"></i>Join Room
                            </a>
                            <button onclick="endLiveClass({{ $liveClass->id }})" class="btn btn-danger">
                                <i class="bi bi-stop-circle me-2"></i>End Class
                            </button>
                        @endif
                        
                        <a href="{{ route('dos.live-classes.requests', $liveClass->id) }}" class="btn btn-info">
                            <i class="bi bi-person-check me-2"></i>Manage Requests
                        </a>
                        
                        <a href="{{ route('dos.live-classes.edit', $liveClass->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
.animate-pulse {
    animation: pulse 2s infinite;
}
</style>

<script>
function startLiveClass(id) {
    if(confirm('Start this live class now?')) {
        fetch(`/dos/live-classes/${id}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
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

function deleteLiveClass(id) {
    if(confirm('Are you sure you want to delete this live class? This action cannot be undone.')) {
        fetch(`/dos/live-classes/${id}/delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Live class deleted successfully!');
                window.location.href = '/dos/live-classes';
            } else {
                alert('Error deleting live class: ' + data.message);
            }
        });
    }
}
</script>
@endsection
