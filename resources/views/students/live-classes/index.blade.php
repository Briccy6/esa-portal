@extends('layouts.app')

@section('title', 'My Live Classes')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Live Classes</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    @if($liveClasses->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-video fa-3x text-muted mb-3"></i>
                            <h4>No Live Classes Scheduled</h4>
                            <p class="text-muted">There are no live classes scheduled for your course at the moment.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($liveClasses as $liveClass)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">{{ $liveClass->title }}</h5>
                                            @switch($liveClass->status)
                                                @case('scheduled')
                                                    <span class="badge badge-warning">Scheduled</span>
                                                    @break
                                                @case('active')
                                                    <span class="badge badge-success">Live Now</span>
                                                    @break
                                                @case('ended')
                                                    <span class="badge badge-secondary">Ended</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $liveClass->status }}</span>
                                            @endswitch
                                        </div>
                                        <div class="card-body">
                                            @if($liveClass->description)
                                                <p class="card-text">{{ Str::limit($liveClass->description, 100) }}</p>
                                            @endif

                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-book"></i> {{ $liveClass->course?->course_name ?? 'N/A' }}
                                                </small>
                                            </div>

                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $liveClass->teacher?->name ?? 'No teacher assigned' }}
                                                </small>
                                            </div>

                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $liveClass->start_time->format('M j, Y H:i') }} - 
                                                    {{ $liveClass->end_time->format('H:i') }}
                                                </small>
                                            </div>

                                            @if($liveClass->required_payment_percentage > 0)
                                                <div class="mb-2">
                                                    <small class="text-info">
                                                        <i class="fas fa-percentage"></i> 
                                                        {{ $liveClass->required_payment_percentage }}% payment required
                                                    </small>
                                                </div>
                                            @endif

                                            @if($liveClass->max_participants)
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-users"></i> 
                                                        {{ $liveClass->getActiveParticipantsCount() }}/{{ $liveClass->max_participants }} participants
                                                    </small>
                                                </div>
                                            @endif

                                            @if($liveClass->status === 'active')
                                                <div class="alert alert-success py-2">
                                                    <i class="fas fa-broadcast-tower"></i> This class is currently live!
                                                </div>
                                            @elseif($liveClass->start_time->isFuture())
                                                <div class="alert alert-info py-2">
                                                    <i class="fas fa-clock"></i> Starts in {{ $liveClass->start_time->diffForHumans() }}
                                                </div>
                                            @endif

                                            @if($liveClass->isFull())
                                                <div class="alert alert-warning py-2">
                                                    <i class="fas fa-users"></i> Class is full
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer">
                                            @php
                                                $student = auth('student')->user();
                                                $canJoin = $liveClass->canStudentJoin($student);
                                            @endphp

                                            @if($liveClass->status === 'active' && $canJoin['allowed'])
                                                <form action="{{ route('student.live-classes.join', $liveClass) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-block">
                                                        <i class="fas fa-video"></i> Join Live Class
                                                    </button>
                                                </form>
                                            @elseif($liveClass->status === 'active' && isset($canJoin['requires_approval']))
                                                <a href="{{ route('live-classes.request', $liveClass) }}" class="btn btn-warning btn-block">
                                                    <i class="fas fa-hand-paper"></i> Request Access
                                                </a>
                                            @elseif($liveClass->status === 'active' && !$canJoin['allowed'])
                                                <button class="btn btn-outline-secondary btn-block" disabled>
                                                    <i class="fas fa-ban"></i> {{ $canJoin['reason'] }}
                                                </button>
                                            @elseif($liveClass->start_time->isFuture())
                                                <button class="btn btn-outline-secondary btn-block" disabled>
                                                    <i class="fas fa-clock"></i> Not Started Yet
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary btn-block" disabled>
                                                    <i class="fas fa-archive"></i> Class Ended
                                                </button>
                                            @endif

                                            @if(isset($canJoin['current_percentage']))
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        Your payment: {{ $canJoin['current_percentage'] }}% 
                                                        (Required: {{ $canJoin['required_percentage'] }}%)
                                                    </small>
                                                </div>
                                            @endif

                                            <a href="{{ route('student.live-classes.show', $liveClass) }}" class="btn btn-outline-info btn-block mt-2">
                                                <i class="fas fa-info-circle"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $liveClasses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
