@extends('layouts.app')

@section('title', 'Live Class Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $liveClass->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.live-classes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Classes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            @if($liveClass->description)
                                <p>{{ $liveClass->description }}</p>
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
                                    <td><strong>Teacher:</strong></td>
                                    <td>{{ $liveClass->teacher?->name ?? 'No teacher assigned' }}</td>
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
                            </table>
                        </div>
                    </div>

                    <hr>

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
                                        <h5><i class="fas fa-clock"></i> Request Pending</h5>
                                        <p>Your request to join this class is pending approval.</p>
                                        <p>Submitted: {{ $studentRequest->created_at->format('M j, Y H:i') }}</p>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-outline-warning btn-lg" disabled>
                                            <i class="fas fa-clock"></i> Request Pending
                                        </button>
                                    </div>
                                    @break
                                @case('approved')
                                    <div class="alert alert-success">
                                        <h5><i class="fas fa-check-circle"></i> Request Approved</h5>
                                        <p>Your request has been approved! You can now join the live class.</p>
                                    </div>
                                    <div class="text-center">
                                        <form action="{{ route('student.live-classes.join', $liveClass) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-video"></i> Join Live Class Now
                                            </button>
                                        </form>
                                    </div>
                                    @break
                                @case('rejected')
                                    <div class="alert alert-danger">
                                        <h5><i class="fas fa-times-circle"></i> Request Rejected</h5>
                                        <p>Your request was rejected.</p>
                                        @if($studentRequest->rejected_reason)
                                            <p><strong>Reason:</strong> {{ $studentRequest->rejected_reason }}</p>
                                        @endif
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-outline-danger btn-lg" disabled>
                                            <i class="fas fa-times-circle"></i> Request Rejected
                                        </button>
                                    </div>
                                    @break
                            @endswitch
                        @else
                            <div class="alert alert-success">
                                <h5><i class="fas fa-broadcast-tower"></i> This class is currently live!</h5>
                                <p>Join now to participate in the live session.</p>
                            </div>

                            <div class="text-center">
                                <form action="{{ route('student.live-classes.join', $liveClass) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-video"></i> Join Live Class Now
                                    </button>
                                </form>
                            </div>
                        @endif
                    @elseif($liveClass->start_time->isFuture())
                        <div class="alert alert-info">
                            <h5><i class="fas fa-clock"></i> Class hasn't started yet</h5>
                            <p>This class will start at: <strong>{{ $liveClass->start_time->format('M j, Y H:i') }}</strong></p>
                            <p>Time until start: <strong>{{ $liveClass->start_time->diffForHumans() }}</strong></p>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-outline-secondary btn-lg" disabled>
                                <i class="fas fa-clock"></i> Not Started Yet
                            </button>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <h5><i class="fas fa-archive"></i> Class has ended</h5>
                            <p>This class ended at: <strong>{{ $liveClass->end_time->format('M j, Y H:i') }}</strong></p>
                            <p>Recording may be available soon.</p>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-outline-secondary btn-lg" disabled>
                                <i class="fas fa-archive"></i> Class Ended
                            </button>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Technical Requirements</h5>
                            <div class="alert alert-light">
                                <h6>Before joining the live class, make sure you have:</h6>
                                <ul class="mb-0">
                                    <li>A modern web browser (Chrome, Firefox, Safari, or Edge)</li>
                                    <li>Stable internet connection</li>
                                    <li>Webcam and microphone (if you want to participate)</li>
                                    <li>Headphones recommended for better audio quality</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
