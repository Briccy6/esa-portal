@extends('admin.layout')

@section('title', 'Student Subscriptions - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Student Subscriptions</h1>
            <p class="text-muted mb-0">Manage all student subscription records</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
        <div class="card-body">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subscription</th>
                                <th>Tokens</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Started</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @if($subscription->student && $subscription->student->profile_image)
                                                    <img src="{{ asset('storage/' . $subscription->student->profile_image) }}" 
                                                         alt="{{ $subscription->student->full_name }}" 
                                                         class="rounded-circle" 
                                                         style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ strtoupper(substr($subscription->student->full_name ?? 'Unknown', 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $subscription->student->full_name ?? 'Unknown' }}</div>
                                                <small class="text-muted">{{ $subscription->student->email ?? 'No email' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white">
                                            {{ $subscription->subscription->name ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            {{ number_format($subscription->tokens_included ?? 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            RWF {{ number_format($subscription->amount ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($subscription->payment_status) {
                                                'completed' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'failed' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $statusText = match($subscription->payment_status) {
                                                'completed' => 'Active',
                                                'pending' => 'Pending',
                                                'failed' => 'Failed',
                                                default => 'Unknown'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} text-white">
                                            {{ $statusText }}
                                        </span>
                                        @if($subscription->is_active && $subscription->ends_at > now())
                                            <i class="fas fa-check-circle text-success ms-1" title="Active"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $subscription->subscription->duration_days ?? 0 }} days
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $subscription->started_at ? $subscription->started_at->format('M j, Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="{{ $subscription->ends_at && $subscription->ends_at->isPast() ? 'text-danger' : 'text-success' }}">
                                            {{ $subscription->ends_at ? $subscription->ends_at->format('M j, Y H:i') : 'N/A' }}
                                        </small>
                                        @if($subscription->ends_at && $subscription->ends_at->isPast())
                                            <span class="badge bg-danger text-white ms-1">Expired</span>
                                        @elseif($subscription->ends_at && $subscription->ends_at->diffInDays(now()) <= 7)
                                            <span class="badge bg-warning text-white ms-1">Expires Soon</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" class="dropdown-item" 
                                                       onclick="extendSubscription({{ $subscription->id }}, '{{ $subscription->student->full_name ?? 'Student' }}')">
                                                        <i class="fas fa-calendar-plus me-2"></i>
                                                        Extend
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item" 
                                                       onclick="viewDetails({{ $subscription->id }})">
                                                        <i class="fas fa-eye me-2"></i>
                                                        View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item text-danger" 
                                                       onclick="cancelSubscription({{ $subscription->id }}, '{{ $subscription->student->full_name ?? 'Student' }}')">
                                                        <i class="fas fa-times me-2"></i>
                                                        Cancel
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item text-danger" 
                                                       onclick="deleteSubscription({{ $subscription->id }}, '{{ $subscription->student->full_name ?? 'Student' }}', '{{ $subscription->subscription->name ?? 'Subscription' }}')">
                                                        <i class="fas fa-trash me-2"></i>
                                                        Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $subscriptions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Student Subscriptions Found</h5>
                    <p class="text-muted">There are no student subscription records in the system yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h4 class="card-title">{{ $subscriptions->total() }}</h4>
                    <p class="card-text mb-0">Total Subscriptions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h4 class="card-title">{{ $subscriptions->where('payment_status', 'completed')->count() }}</h4>
                    <p class="card-text mb-0">Active Subscriptions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h4 class="card-title">{{ $subscriptions->where('payment_status', 'pending')->count() }}</h4>
                    <p class="card-text mb-0">Pending Subscriptions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h4 class="card-title">{{ $subscriptions->where('payment_status', 'failed')->count() }}</h4>
                    <p class="card-text mb-0">Failed Subscriptions</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Subscription Modal -->
<div class="modal fade" id="extendSubscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extendSubscriptionForm" method="POST" action="{{ route('subscriptions.extend', ':id') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="subscription_id" id="extendSubscriptionId">
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" id="extendStudentName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Days</label>
                        <input type="number" name="days" class="form-control" min="1" max="365" required>
                        <small class="form-text text-muted">Enter number of days to extend this subscription</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Extend Subscription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function extendSubscription(subscriptionId, studentName) {
    document.getElementById('extendSubscriptionId').value = subscriptionId;
    document.getElementById('extendStudentName').value = studentName;
    
    // Update form action
    const form = document.getElementById('extendSubscriptionForm');
    form.action = form.action.replace(':id', subscriptionId);
    
    // Show modal
    new bootstrap.Modal(document.getElementById('extendSubscriptionModal')).show();
}

function viewDetails(subscriptionId) {
    // You can implement a details modal or redirect to details page
    window.location.href = `/admin/subscriptions/${subscriptionId}/details`;
}

function cancelSubscription(subscriptionId, studentName) {
    if (confirm(`Are you sure you want to cancel the subscription for ${studentName}? This action cannot be undone.`)) {
        // You can implement cancel functionality
        window.location.href = `/subscriptions/${subscriptionId}/cancel`;
    }
}

function deleteSubscription(subscriptionId, studentName, subscriptionName) {
    if (confirm(`Are you sure you want to delete the subscription for ${studentName} (${subscriptionName})? This action cannot be undone.`)) {
        // Submit delete form using the correct route
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/subscriptions/destroy/${subscriptionId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
