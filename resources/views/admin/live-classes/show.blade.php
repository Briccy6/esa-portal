@extends('admin.layout')

@section('title', 'Live Class Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Live Class Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.live-classes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('admin.live-classes.edit', $liveClass) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $liveClass->title }}</h4>
                            @if($liveClass->description)
                                <p class="text-muted">{{ $liveClass->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-right">
                            @switch($liveClass->status)
                                @case('scheduled')
                                    <span class="badge badge-warning badge-lg">Scheduled</span>
                                    @break
                                @case('active')
                                    <span class="badge badge-success badge-lg">Live Now</span>
                                    @break
                                @case('ended')
                                    <span class="badge badge-secondary badge-lg">Ended</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger badge-lg">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary badge-lg">{{ $liveClass->status }}</span>
                            @endswitch
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $liveClass->course?->course_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td>{{ $liveClass->branch?->branch_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teacher:</strong></td>
                                    <td>{{ $liveClass->teacher?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lesson:</strong></td>
                                    <td>{{ $liveClass->lesson?->title ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Start Time:</strong></td>
                                    <td>{{ $liveClass->start_time->format('M j, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Time:</strong></td>
                                    <td>{{ $liveClass->end_time->format('M j, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $liveClass->start_time->diffInHours($liveClass->end_time) }} hours</td>
                                </tr>
                                <tr>
                                    <td><strong>Meeting ID:</strong></td>
                                    <td><code>{{ $liveClass->meeting_id }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Actions</h5>
                            <div class="btn-group">
                                @if($liveClass->status === 'scheduled')
                                    <form action="{{ route('admin.live-classes.start', $liveClass) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Start this live class now?')">
                                            <i class="fas fa-play"></i> Start Class
                                        </button>
                                    </form>
                                @endif

                                @if($liveClass->status === 'active')
                                    <a href="{{ route('live-classes.room', $liveClass) }}" class="btn btn-info" target="_blank">
                                        <i class="fas fa-video"></i> Join Room
                                    </a>
                                    <form action="{{ route('admin.live-classes.end', $liveClass) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('End this live class?')">
                                            <i class="fas fa-stop"></i> End Class
                                        </button>
                                    </form>
                                @endif

                                @if($liveClass->status === 'ended')
                                    <a href="#" class="btn btn-secondary" disabled>
                                        <i class="fas fa-archive"></i> View Recording (Coming Soon)
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($liveClass->status === 'scheduled')
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5>Class Information for Students</h5>
                                <div class="alert alert-info">
                                    <h6>How students can join:</h6>
                                    <ol>
                                        <li>Students can view this class in their dashboard</li>
                                        <li>They can join once the class is started</li>
                                        <li>Meeting ID: <code>{{ $liveClass->meeting_id }}</code></li>
                                        <li>Password: <code>{{ $liveClass->password }}</code></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
