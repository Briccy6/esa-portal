@extends('staff.partials.sidebar_sec')

@section('title', 'Subscription Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Subscriptions</h5>
                    <a href="{{ route('staff.subscriptions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Record Subscription Payment
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student</th>
                                    <th>Subscription</th>
                                    <th>Amount Paid</th>
                                    <th>Tokens Added</th>
                                    <th>Payment Status</th>
                                    <th>Duration</th>
                                    <th>Recorded By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $subscription)
                                    <tr>
                                        <td>
                                            <strong>{{ $subscription->student->full_name }}</strong><br>
                                            <small class="text-muted">{{ $subscription->student->reg_number }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $subscription->subscription->tier }}</span><br>
                                            {{ $subscription->subscription->name }}
                                        </td>
                                        <td>{{ number_format($subscription->paid_amount, 0) }} RWF</td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ number_format($subscription->subscription->tokens_included) }} tokens
                                            </span>
                                        </td>
                                        <td>
                                            @if($subscription->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $subscription->starts_at->format('M d, Y') }} - 
                                                {{ $subscription->ends_at->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($subscription->payment_reference)
                                                <small class="text-muted">{{ $subscription->payment_reference }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('staff.subscriptions.show', $subscription) }}" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No subscription payments recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} 
                            of {{ $subscriptions->total() }} entries
                        </div>
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
