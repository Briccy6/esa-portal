@extends('students.app')

@section('title', 'Live Class Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="section-title">
                    <i class="bi bi-broadcast me-2"></i>
                    {{ $liveClass->title }}
                </h3>
                <a href="{{ route('student.live-classes.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Classes
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            {{ session('info') }}
        </div>
    @endif

    <!-- Live Class Details Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Class Information</h5>
                    @switch($liveClass->status)
                        @case('scheduled')
                            <span class="badge bg-warning">Scheduled</span>
                            @break
                        @case('active')
                            <span class="badge bg-success">Live Now</span>
                            @break
                        @case('ended')
                            <span class="badge bg-secondary">Ended</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ $liveClass->status }}</span>
                    @endswitch
                </div>
                <div class="card-body">
                    @if($liveClass->description)
                        <div class="row mb-4">
                            <div class="col-12">
                                <p class="lead">{{ $liveClass->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Class Details</h6>
                            <div class="mb-3">
                                <small class="text-muted d-block">Course</small>
                                <strong>
                                    {{
                                        ($liveClass->courses?->pluck('course_name')?->filter()?->implode(', '))
                                        ?: ($liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('course.course_name') ?? collect())?->filter()?->unique()?->implode(', ') ?? 'N/A')
                                    }}
                                </strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Teacher</small>
                                <strong>
                                    {{
                                        ($liveClass->teacher?->teacher_name)
                                        ?: ($liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('teacher.teacher_name') ?? collect())?->filter()?->unique()?->implode(', ') ?? 'No teacher assigned')
                                    }}
                                </strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Lesson</small>
                                <strong>
                                    {{
                                        ($liveClass->lessons?->pluck('title')?->filter()?->implode(', '))
                                        ?: 'N/A'
                                    }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Schedule</h6>
                            <div class="mb-3">
                                <small class="text-muted d-block">Start Time</small>
                                <strong><i class="bi bi-clock me-1"></i>{{ $liveClass->start_time->format('M j, Y H:i') }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">End Time</small>
                                <strong><i class="bi bi-clock me-1"></i>{{ $liveClass->end_time->format('M j, Y H:i') }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Duration</small>
                                <strong><i class="bi bi-hourglass me-1"></i>{{ $liveClass->start_time->diffInHours($liveClass->end_time) }} hours</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Live Class Actions -->
                    @if($liveClass->status === 'active')
                        @php
                            $studentRequest = \App\Models\LiveClassRequest::where('live_class_id', $liveClass->id)
                                ->where('user_id', Auth::guard('student')->user()->id)
                                ->where('user_type', 'student')
                                ->first();
                        @endphp
                        
                        @if($studentRequest)
                            @switch($studentRequest->status)
                                @case('pending')
                                    <div class="alert alert-warning">
                                        <h5><i class="bi bi-clock me-2"></i> Request Pending</h5>
                                        <p class="mb-0">Your request to join this class is pending approval.</p>
                                        <small class="text-muted">Submitted: {{ $studentRequest->created_at->format('M j, Y H:i') }}</small>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-outline-warning btn-lg" disabled>
                                            <i class="bi bi-clock me-2"></i> Request Pending
                                        </button>
                                    </div>
                                    @break
                                @case('approved')
                                    <div class="alert alert-success">
                                        <h5><i class="bi bi-check-circle me-2"></i> Request Approved</h5>
                                        <p class="mb-0">Your request has been approved! You can now join the live class.</p>
                                    </div>
                                    <div class="text-center">
                                        <a href="{{ route('student.live-classes.room', $liveClass) }}" class="btn btn-success btn-lg">
                                            <i class="bi bi-camera-video me-2"></i> Join Live Class Now
                                        </a>
                                    </div>
                                    @break
                                @case('rejected')
                                    <div class="alert alert-danger">
                                        <h5><i class="bi bi-x-circle me-2"></i> Request Rejected</h5>
                                        <p class="mb-0">Your request was rejected.</p>
                                        @if($studentRequest->rejected_reason)
                                            <p><strong>Reason:</strong> {{ $studentRequest->rejected_reason }}</p>
                                        @endif
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-outline-danger btn-lg" disabled>
                                            <i class="bi bi-x-circle me-2"></i> Request Rejected
                                        </button>
                                    </div>
                                    @break
                            @endswitch
                        @else
                            <div class="alert alert-success">
                                <h5><i class="bi bi-broadcast me-2"></i> This class is currently live!</h5>
                                <p class="mb-0">Join now to participate in the live session.</p>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('student.live-classes.room', $liveClass) }}" class="btn btn-success btn-lg">
                                    <i class="bi bi-camera-video me-2"></i> Join Live Class Now
                                </a>
                            </div>
                        @endif
                    @elseif($liveClass->start_time->isFuture())
                        <div class="alert alert-info">
                            <h5><i class="bi bi-clock me-2"></i> Class hasn't started yet</h5>
                            <p class="mb-0">This class will start at: <strong>{{ $liveClass->start_time->format('M j, Y H:i') }}</strong></p>
                            <small class="text-muted">Time until start: <strong>{{ $liveClass->start_time->diffForHumans() }}</strong></small>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-outline-secondary btn-lg" disabled>
                                <i class="bi bi-clock me-2"></i> Not Started Yet
                            </button>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <h5><i class="bi bi-archive me-2"></i> Class has ended</h5>
                            <p class="mb-0">This class ended at: <strong>{{ $liveClass->end_time->format('M j, Y H:i') }}</strong></p>
                            <small class="text-muted">Recording may be available soon.</small>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-outline-secondary btn-lg" disabled>
                                <i class="bi bi-archive me-2"></i> Class Ended
                            </button>
                        </div>
                    @endif

                    <hr>

                    <!-- Technical Requirements -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-gear me-2"></i>Technical Requirements
                            </h6>
                            <div class="alert alert-light">
                                <h6 class="mb-3">Before joining the live class, make sure you have:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li><i class="bi bi-browser-chrome text-primary me-2"></i>A modern web browser (Chrome, Firefox, Safari, or Edge)</li>
                                            <li><i class="bi bi-wifi text-primary me-2"></i>Stable internet connection</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li><i class="bi bi-camera-video text-primary me-2"></i>Webcam and microphone (if you want to participate)</li>
                                            <li><i class="bi bi-headphones text-primary me-2"></i>Headphones recommended for better audio quality</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
