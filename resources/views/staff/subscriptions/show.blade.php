@extends('layouts.staff')

@section('title', 'Subscription Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Subscription Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $studentSubscription->student->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Number:</strong></td>
                                    <td>{{ $studentSubscription->student->reg_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $studentSubscription->student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $studentSubscription->student->phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Subscription Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Plan:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $studentSubscription->subscription->tier }}</span>
                                        {{ $studentSubscription->subscription->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tokens Included:</strong></td>
                                    <td>
                                        <span class="badge bg-success">{{ number_format($studentSubscription->subscription->tokens_included) }} tokens</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $studentSubscription->subscription->duration_days }} days</td>
                                </tr>
                                <tr>
                                    <td><strong>Price:</strong></td>
                                    <td>{{ number_format($studentSubscription->subscription->price, 0) }} RWF</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Amount Paid:</strong></td>
                                    <td>{{ number_format($studentSubscription->paid_amount, 0) }} RWF</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td>
                                        @if($studentSubscription->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Reference:</strong></td>
                                    <td>{{ $studentSubscription->payment_reference ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Subscription Status:</strong></td>
                                    <td>
                                        @if($studentSubscription->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Timeline</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $studentSubscription->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $studentSubscription->starts_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $studentSubscription->ends_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Days Remaining:</strong></td>
                                    <td>
                                        <span class="badge @if($studentSubscription->days_remaining > 7) bg-success @elseif($studentSubscription->days_remaining > 0) bg-warning @else bg-danger @endif">
                                            {{ $studentSubscription->days_remaining }} days
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('staff.subscriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($studentSubscription->student->token)
                            <div class="text-muted">
                                <small>Current Token Balance: {{ $studentSubscription->student->token->formatted_balance }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
