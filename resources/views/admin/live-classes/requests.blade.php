@extends('admin.layout')

@section('title', 'Live Class Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Requests - {{ $liveClass->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.live-classes.show', $liveClass) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Class
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="btn-group">
                            <a href="{{ route('admin.live-classes.requests', $liveClass) }}?status=pending" class="btn btn-outline-primary {{ request('status') == 'pending' ? 'active' : '' }}">
                                Pending ({{ $liveClass->requests()->pending()->count() }})
                            </a>
                            <a href="{{ route('admin.live-classes.requests', $liveClass) }}?status=approved" class="btn btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">
                                Approved ({{ $liveClass->requests()->approved()->count() }})
                            </a>
                            <a href="{{ route('admin.live-classes.requests', $liveClass) }}?status=rejected" class="btn btn-outline-danger {{ request('status') == 'rejected' ? 'active' : '' }}">
                                Rejected ({{ $liveClass->requests()->rejected()->count() }})
                            </a>
                            <a href="{{ route('admin.live-classes.requests', $liveClass) }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                                All ({{ $requests->count() }})
                            </a>
                        </div>
                    </div>

                    @if($requests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4>No requests found</h4>
                            <p class="text-muted">There are no {{ request('status', 'all') }} requests for this live class.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>
                                                @if($request->user_type === 'student' && $request->user)
                                                    {{ $request->user->full_name }}
                                                    <br><small class="text-muted">{{ $request->user->reg_number }}</small>
                                                @else
                                                    {{ $request->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($request->user_type === 'student' && $request->user)
                                                    {{ $request->user->email }}
                                                @else
                                                    {{ $request->email }}
                                                @endif
                                            </td>
                                            <td>
                                                @switch($request->user_type)
                                                    @case('student')
                                                        <span class="badge badge-info">Student</span>
                                                        @break
                                                    @case('public')
                                                        <span class="badge badge-warning">Public</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $request->user_type }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($request->reason, 100) }}</small>
                                                @if(strlen($request->reason) > 100)
                                                    <button class="btn btn-link btn-sm p-0" onclick="toggleReason('{{$request->id}}')">
                                                        <small>Read more</small>
                                                    </button>
                                                    <div id="reason-{{$request->id}}" style="display: none;">
                                                        <small>{{ $request->reason }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($request->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge badge-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge badge-danger">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $request->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $request->created_at->format('M j, Y H:i') }}</td>
                                            <td>
                                                @if($request->status === 'pending')
                                                    <div class="btn-group">
                                                        <form action="{{ route('admin.live-classes.requests.approve', [$liveClass, $request->id]) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this request?')">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-danger btn-sm" onclick="showRejectForm({{ $request->id }})">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Hidden reject form -->
                                                    <div id="reject-form-{{ $request->id }}" style="display: none;" class="mt-2">
                                                        <form action="{{ route('admin.live-classes.requests.reject', [$liveClass, $request->id]) }}" method="POST">
                                                            @csrf
                                                            <div class="form-group">
                                                                <textarea name="reason" class="form-control" rows="2" placeholder="Reason for rejection..." required></textarea>
                                                            </div>
                                                            <div class="btn-group">
                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                                <button type="button" class="btn btn-secondary btn-sm" onclick="hideRejectForm({{ $request->id }})">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @else
                                                    @if($request->status === 'approved')
                                                        <small class="text-success">
                                                            Approved by {{ $request->approvedByUser?->name }}<br>
                                                            {{ $request->approved_at->format('M j, Y H:i') }}
                                                        </small>
                                                    @else
                                                        <small class="text-danger">
                                                            Rejected by {{ $request->approvedByUser?->name }}<br>
                                                            {{ $request->processed_at->format('M j, Y H:i') }}
                                                            @if($request->rejected_reason)
                                                                <br>Reason: {{ $request->rejected_reason }}
                                                            @endif
                                                        </small>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleReason(id) {
    const element = document.getElementById('reason-' + id);
    element.style.display = element.style.display === 'none' ? 'block' : 'none';
}

function showRejectForm(id) {
    document.getElementById('reject-form-' + id).style.display = 'block';
}

function hideRejectForm(id) {
    document.getElementById('reject-form-' + id).style.display = 'none';
}
</script>
@endsection
