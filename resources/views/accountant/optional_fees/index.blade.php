@extends('staff.partials.sidebar_sec')

@section('title', 'Optional Fees Management')

@include('accountant.partials.theme')

@section('content')
<style>
    .fee-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .fee-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .fee-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f766e;
    }

    .mandatory-badge {
        background: #dcfce7;
        color: #166534;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .optional-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold mb-1">Optional Fees Management</h2>
            <p class="text-muted mb-0">Configure mandatory/optional status for practice and trip fees</p>
        </div>
        <a href="{{ route('staff.accountant.optional-fees.report') }}" class="btn btn-primary">
            <i class="fas fa-chart-bar me-2"></i>View Responses Report
        </a>
    </div>

    @if($fees->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No fee configurations found for your branches.
        </div>
    @else
        @foreach($fees as $fee)
            <div class="fee-card">
                <div class="fee-header">
                    <div>
                        <h5 class="mb-1">{{ $fee->course->course_name ?? 'N/A' }}</h5>
                        <small class="text-muted">
                            Branch: {{ $fee->branch->name ?? 'N/A' }} |
                            Term: {{ $fee->term->name ?? 'N/A' }}
                        </small>
                    </div>
                </div>

                <form method="POST" action="{{ route('staff.accountant.optional-fees.update', $fee->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Fee Amounts -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="mb-3">Fee Amounts</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Practical Fee (RWF)</label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                           name="practical_fee"
                                           value="{{ $fee->practical_fee ?? 0 }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trip Fee (RWF)</label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                           name="trip_fee"
                                           value="{{ $fee->trip_fee ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Validity -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="mb-3">Fee Validity Period</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Valid From</label>
                                    <input type="date" class="form-control"
                                           name="valid_from"
                                           value="{{ $fee->valid_from ? $fee->valid_from->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Valid Until</label>
                                    <input type="date" class="form-control"
                                           name="valid_until"
                                           value="{{ $fee->valid_until ? $fee->valid_until->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Notification -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="notify_students_{{ $fee->id }}"
                                       name="notify_students"
                                       value="1"
                                       {{ $fee->notify_students ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_students_{{ $fee->id }}">
                                    <strong>Notify Students</strong>
                                    <div class="small text-muted">
                                        Students in this branch and course will see a popup to accept/decline these fees when they log in
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Practical Fee Settings -->
                    @if($fee->practical_fee > 0 || old('practical_fee'))
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">Practical Fee Settings</h6>
                                    <span class="fee-amount">{{ number_format($fee->practical_fee ?? 0, 2) }} RWF</span>
                                </div>
                                @if($fee->is_practical_mandatory)
                                    <span class="mandatory-badge">Mandatory</span>
                                @else
                                    <span class="optional-badge">Optional</span>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="is_practical_mandatory_{{ $fee->id }}"
                                               name="is_practical_mandatory"
                                               value="1"
                                               {{ $fee->is_practical_mandatory ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_practical_mandatory_{{ $fee->id }}">
                                            Make Mandatory
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Due Date</label>
                                    <input type="date" class="form-control form-control-sm"
                                           name="practical_due_date"
                                           value="{{ $fee->practical_due_date ? $fee->practical_due_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Trip Fee Settings -->
                    @if($fee->trip_fee > 0 || old('trip_fee'))
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">Trip Fee Settings</h6>
                                    <span class="fee-amount">{{ number_format($fee->trip_fee ?? 0, 2) }} RWF</span>
                                </div>
                                @if($fee->is_trip_mandatory)
                                    <span class="mandatory-badge">Mandatory</span>
                                @else
                                    <span class="optional-badge">Optional</span>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="is_trip_mandatory_{{ $fee->id }}"
                                               name="is_trip_mandatory"
                                               value="1"
                                               {{ $fee->is_trip_mandatory ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_trip_mandatory_{{ $fee->id }}">
                                            Make Mandatory
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Due Date</label>
                                    <input type="date" class="form-control form-control-sm"
                                           name="trip_due_date"
                                           value="{{ $fee->trip_due_date ? $fee->trip_due_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Save Settings
                    </button>
                </form>
            </div>
        @endforeach
    @endif
</div>
@endsection
