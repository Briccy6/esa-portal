@extends('admin.layout')

@section('title', 'Request Access to Live Class')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Request Access to Live Class</h4>
                </div>
                <div class="card-body">
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

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Why do you need to request access?</h6>
                        <p class="mb-0">This class requires approval for non-online branch students. Please provide a reason why you need to join this live class.</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('live-classes.request.submit', $liveClass) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="reason">Reason for Access Request <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required placeholder="Please explain why you need to join this live class...">{{ old('reason') }}</textarea>
                            <small class="form-text text-muted">Provide details about why you need access to this class and any relevant circumstances.</small>
                        </div>

                        <div class="form-group">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Your Information</h6>
                                    <p><strong>Name:</strong> {{ $student->full_name }}</p>
                                    <p><strong>Registration Number:</strong> {{ $student->reg_number }}</p>
                                    <p><strong>Course:</strong> {{ $student->course?->course_name ?? 'N/A' }}</p>
                                    <p><strong>Branch:</strong> {{ $student->branch?->branch_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                            <a href="{{ route('student.live-classes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
