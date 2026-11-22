@extends('accountant.partials.theme')

@section('title', 'Optional Fee Responses Report')

@section('content')
<style>
    .accepted-badge { background: #dcfce7; color: #166534; }
    .declined-badge { background: #fee2e2; color: #991b1b; }
    .pending-badge { background: #fef3c7; color: #92400e; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold mb-1">Optional Fee Responses Report</h2>
            <p class="text-muted mb-0">View student responses for practice and trip fees</p>
        </div>
        <a href="{{ route('staff.accountant.optional-fees.export', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.accountant.optional-fees.report') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fee Type</label>
                    <select name="fee_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="practical" {{ request('fee_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                        <option value="trip" {{ request('fee_type') == 'trip' ? 'selected' : '' }}>Trip</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Response</label>
                    <select name="response" class="form-select">
                        <option value="">All Responses</option>
                        <option value="accepted" {{ request('response') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="declined" {{ request('response') == 'declined' ? 'selected' : '' }}>Declined</option>
                        <option value="pending" {{ request('response') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('staff.accountant.optional-fees.report') }}" class="btn btn-outline-secondary ms-2">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="h4">{{ $responses->total() }}</h3>
                    <small class="text-muted">Total Responses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="h4 text-success">{{ $responses->where('response', 'accepted')->count() }}</h3>
                    <small class="text-muted">Accepted</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="h4 text-danger">{{ $responses->where('response', 'declined')->count() }}</h3>
                    <small class="text-muted">Declined</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="h4 text-warning">{{ $responses->where('response', 'pending')->count() }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Reg Number</th>
                            <th>Branch</th>
                            <th>Course</th>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Response</th>
                            <th>Responded At</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                            <tr>
                                <td>{{ $response->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $response->student->reg_number ?? 'N/A' }}</td>
                                <td>{{ $response->student->branch->name ?? 'N/A' }}</td>
                                <td>{{ $response->student->course->course_name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($response->fee_type) }}</td>
                                <td>
                                    @if($response->fee_type === 'practical')
                                        {{ number_format($response->courseTermBranchFee->practical_fee, 2) }} RWF
                                    @else
                                        {{ number_format($response->courseTermBranchFee->trip_fee, 2) }} RWF
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $response->response === 'accepted' ? 'accepted-badge' : ($response->response === 'declined' ? 'declined-badge' : 'pending-badge') }}">
                                        {{ ucfirst($response->response) }}
                                    </span>
                                </td>
                                <td>{{ $response->responded_at ? $response->responded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $response->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $responses->links() }}
        </div>
    </div>
</div>
@endsection
