@extends('staff.partials.sidebar_sec')

@section('title', 'Optional Fees Management')

@include('accountant.partials.theme')

@section('content')
<style>
    .optional-fees-page { --of-border: #e7edf5; --of-ink: #172033; --of-muted: #667085; }
    .optional-fees-page .page-heading { letter-spacing: -.02em; color: var(--of-ink); }
    .optional-fees-page .page-copy { color: var(--of-muted); }
    .optional-fees-page .hero-actions .btn { min-height: 42px; display: inline-flex; align-items: center; border-radius: 9px; }
    .fee-card {
        border: 1px solid var(--of-border);
        border-radius: 14px;
        margin-bottom: 1.25rem;
        background: #fff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .045);
        overflow: hidden;
    }
    .fee-card .fee-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--of-border);
        background: linear-gradient(135deg, #fbfdff 0%, #f7faff 100%);
    }
    .fee-card .fee-title { color: var(--of-ink); font-size: 1rem; font-weight: 750; }
    .fee-card .fee-meta { color: var(--of-muted); font-size: .82rem; }
    .fee-card .fee-form { padding: 1.25rem 1.35rem 1.35rem; }
    .fee-card .setting-section {
        border: 1px solid var(--of-border);
        border-radius: 11px;
        padding: 1rem;
        margin-bottom: .85rem;
        background: #fff;
    }
    .fee-card .section-title { color: var(--of-ink); font-size: .875rem; font-weight: 750; margin-bottom: .9rem; }
    .fee-card .form-label { color: #344054; font-size: .82rem; font-weight: 650; margin-bottom: .42rem; }
    .fee-card .form-control { min-height: 43px; border-color: #d9e1ec; border-radius: 8px; }
    .fee-card .form-control:focus { border-color: #4e83ff; box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .09); }
    .fee-card .notification-box { display: flex; align-items: flex-start; gap: .75rem; background: #f8fafc; }
    .fee-card .form-switch .form-check-input { width: 2.55em; height: 1.35em; margin-top: .05em; cursor: pointer; }
    .fee-card .fee-option {
        display: grid;
        grid-template-columns: minmax(150px, 1fr) minmax(150px, 1fr) minmax(220px, 1fr);
        gap: 1rem;
        align-items: end;
    }
    .fee-card .fee-option + .fee-option { margin-top: .85rem; }
    .fee-card .fee-amount { font-size: 1.05rem; font-weight: 800; color: #0f766e; }
    .fee-card .fee-option-name { color: #344054; font-weight: 700; }
    .fee-card .mandatory-badge, .fee-card .optional-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .28rem .62rem; border-radius: 999px;
        font-size: .72rem; font-weight: 750; white-space: nowrap;
    }
    .fee-card .mandatory-badge { background: #dcfce7; color: #166534; }
    .fee-card .optional-badge { background: #fff3cd; color: #8a5a00; }
    .fee-card .save-bar { display: flex; justify-content: flex-end; padding-top: .25rem; }
    .fee-card .save-bar .btn { min-width: 145px; min-height: 42px; border-radius: 8px; }
    @media (max-width: 991.98px) {
        .fee-card .fee-option { grid-template-columns: 1fr 1fr; }
        .fee-card .fee-option-status { grid-column: 1 / -1; }
    }
    @media (max-width: 575.98px) {
        .optional-fees-page { padding-top: 1rem !important; }
        .optional-fees-page .hero-actions { width: 100%; }
        .optional-fees-page .hero-actions .btn { width: 100%; justify-content: center; }
        .fee-card .fee-header { align-items: flex-start; }
        .fee-card .fee-form { padding: 1rem; }
        .fee-card .fee-option { grid-template-columns: 1fr; }
        .fee-card .fee-option-status { grid-column: auto; }
        .fee-card .save-bar .btn { width: 100%; }
    }
</style>

<div class="container-fluid py-4 optional-fees-page">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold page-heading mb-1">Optional Fees Management</h1>
            <p class="page-copy mb-0">Configure practice and trip fees for each branch, course, and term.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('staff.accountant.optional-fees.report') }}" class="btn btn-primary">
                <i class="fas fa-chart-bar me-2" aria-hidden="true"></i>View Responses
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                        <h2 class="fee-title mb-1">{{ $fee->course->course_name ?? 'N/A' }}</h2>
                        <div class="fee-meta">
                            <span>{{ $fee->branch->name ?? 'N/A' }}</span>
                            <span class="mx-1">&middot;</span>
                            <span>{{ $fee->term->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <form class="fee-form" method="POST" action="{{ route('staff.accountant.optional-fees.update', $fee->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="setting-section">
                        <h3 class="section-title">Fee Amounts</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="practical_fee_{{ $fee->id }}">Practical Fee (RWF)</label>
                                <input id="practical_fee_{{ $fee->id }}" type="number" step="0.01" min="0" class="form-control"
                                       name="practical_fee"
                                       value="{{ $fee->practical_fee ?? 0 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="trip_fee_{{ $fee->id }}">Trip Fee (RWF)</label>
                                <input id="trip_fee_{{ $fee->id }}" type="number" step="0.01" min="0" class="form-control"
                                       name="trip_fee"
                                       value="{{ $fee->trip_fee ?? 0 }}">
                            </div>
                        </div>
                    </div>

                    <div class="setting-section">
                        <h3 class="section-title">Fee Validity Period</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="valid_from_{{ $fee->id }}">Valid From</label>
                                <input id="valid_from_{{ $fee->id }}" type="date" class="form-control"
                                       name="valid_from"
                                       value="{{ $fee->valid_from ? $fee->valid_from->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="valid_until_{{ $fee->id }}">Valid Until</label>
                                <input id="valid_until_{{ $fee->id }}" type="date" class="form-control"
                                       name="valid_until"
                                       value="{{ $fee->valid_until ? $fee->valid_until->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="setting-section notification-box">
                        <div class="form-check form-switch flex-shrink-0">
                            <input class="form-check-input" type="checkbox"
                                   id="notify_students_{{ $fee->id }}"
                                   name="notify_students"
                                   value="1"
                                   {{ $fee->notify_students ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label" for="notify_students_{{ $fee->id }}">
                            <strong class="d-block fee-option-name">Notify students</strong>
                            <span class="small text-muted">Show these optional fees as a popup when eligible students log in.</span>
                        </label>
                    </div>

                    <div class="setting-section">
                        <h3 class="section-title">Optional Fee Classification</h3>
                        <div class="fee-option">
                            <div>
                                <span class="fee-option-name d-block mb-1">Practical fee</span>
                                <span class="fee-amount">{{ number_format($fee->practical_fee ?? 0, 2) }} RWF</span>
                            </div>
                            <div>
                                <label class="form-label" for="practical_due_date_{{ $fee->id }}">Due date</label>
                                <input id="practical_due_date_{{ $fee->id }}" type="date" class="form-control"
                                       name="practical_due_date"
                                       value="{{ $fee->practical_due_date ? $fee->practical_due_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="fee-option-status d-flex justify-content-lg-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           id="is_practical_mandatory_{{ $fee->id }}"
                                           name="is_practical_mandatory"
                                           value="1"
                                           {{ $fee->is_practical_mandatory ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1" for="is_practical_mandatory_{{ $fee->id }}">
                                        <span class="{{ $fee->is_practical_mandatory ? 'mandatory-badge' : 'optional-badge' }}">
                                            {{ $fee->is_practical_mandatory ? 'Mandatory' : 'Optional' }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="fee-option">
                            <div>
                                <span class="fee-option-name d-block mb-1">Trip fee</span>
                                <span class="fee-amount">{{ number_format($fee->trip_fee ?? 0, 2) }} RWF</span>
                            </div>
                            <div>
                                <label class="form-label" for="trip_due_date_{{ $fee->id }}">Due date</label>
                                <input id="trip_due_date_{{ $fee->id }}" type="date" class="form-control"
                                       name="trip_due_date"
                                       value="{{ $fee->trip_due_date ? $fee->trip_due_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="fee-option-status d-flex justify-content-lg-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           id="is_trip_mandatory_{{ $fee->id }}"
                                           name="is_trip_mandatory"
                                           value="1"
                                           {{ $fee->is_trip_mandatory ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1" for="is_trip_mandatory_{{ $fee->id }}">
                                        <span class="{{ $fee->is_trip_mandatory ? 'mandatory-badge' : 'optional-badge' }}">
                                            {{ $fee->is_trip_mandatory ? 'Mandatory' : 'Optional' }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="save-bar">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2" aria-hidden="true"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    @endif
</div>
@endsection
