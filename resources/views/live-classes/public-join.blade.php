@extends('admin.layout')

@section('title', 'Join Live Class')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Join Live Class - Public Access</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>{{ $liveClass->title }}</h5>
                        <p class="text-muted">{{ $liveClass->description }}</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Course:</strong> {{ $liveClass->course?->course_name ?? 'N/A' }}</p>
                                <p><strong>Teacher:</strong> {{ $liveClass->teacher?->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Start Time:</strong> {{ $liveClass->start_time->format('M j, Y H:i') }}</p>
                                <p><strong>Duration:</strong> {{ $liveClass->start_time->diffInHours($liveClass->end_time) }} hours</p>
                            </div>
                        </div>
                    </div>

                    @if($liveClass->status !== 'active')
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-clock"></i> Class Not Active</h6>
                            <p class="mb-0">This class is not currently active. Please check back when the class is scheduled to start.</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('live-classes.show', $liveClass) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Class Details
                            </a>
                        </div>
                    @else
                        <form action="{{ route('live-classes.public-join', $liveClass) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', session('name')) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', session('email')) }}" required>
                                        <small class="form-text text-muted">This email will be used to verify your access</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Phone Number (Optional)</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', session('phone')) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>Class Information</h6>
                                            <p><strong>Meeting ID:</strong> <code>{{ $liveClass->meeting_id }}</code></p>
                                            <p><strong>Password:</strong> <code>{{ $liveClass->password }}</code></p>
                                            <p><strong>Participants:</strong> {{ $liveClass->getActiveParticipantsCount() }}{{ $liveClass->max_participants ? '/' . $liveClass->max_participants : '' }}</p>
                                            
                                            @if($liveClass->device_restriction)
                                                <div class="alert alert-info py-2">
                                                    <i class="fas fa-mobile-alt"></i> One device per email allowed
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle"></i> Ready to Join!</h6>
                                        <p class="mb-0">Click the button below to join the live class. Make sure you have a stable internet connection and your camera/microphone ready.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-video"></i> Join Live Class Now
                                </button>
                                <br><br>
                                <a href="{{ route('live-classes.show', $liveClass) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
