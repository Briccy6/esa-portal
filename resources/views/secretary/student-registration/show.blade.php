@extends('secretary.layout')

@section('page_title', 'Registration details')
@section('title', 'Student Registration Details')

@section('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.7rem 0;
        border-bottom: 1px solid #edf1f6;
        font-size: 0.92rem;
    }

    .detail-row span { color: #64748b; }
    .detail-row strong { text-align: right; }

    .photo-frame {
        border: 1px solid #edf1f6;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        background: #f8fafc;
        min-height: 220px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .photo-frame img {
        max-height: 280px;
        width: auto;
        max-width: 100%;
        border-radius: 0.5rem;
    }

    .checklist {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .checklist-item {
        padding: 0.75rem;
        border-radius: 0.65rem;
        background: #f8fafc;
        font-size: 0.85rem;
    }

    @media (max-width: 900px) {
        .detail-grid, .checklist { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
@php
    $isComplete = $student->id_number && $student->id_photo && $student->passport_photo;
@endphp
<div class="page-shell">
    <div class="page-heading">
        <div>
            <h1>{{ $student->full_name }}</h1>
            <p>{{ $student->reg_number }} · {{ $student->course->course_name ?? 'No course' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="{{ route('secretary.student-registration.edit', $student) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit registration
            </a>
            @if($isComplete)
                <form action="{{ route('secretary.student-registration.complete', $student) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark this registration as complete?')">
                        <i class="bi bi-check2"></i> Mark complete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="detail-grid">
        <section class="sec-card">
            <div class="sec-card-header"><div><h2>Personal information</h2></div></div>
            <div class="sec-card-body">
                <div class="detail-row"><span>Registration number</span><strong>{{ $student->reg_number }}</strong></div>
                <div class="detail-row"><span>Full name</span><strong>{{ $student->full_name }}</strong></div>
                <div class="detail-row"><span>Email</span><strong>{{ $student->email }}</strong></div>
                <div class="detail-row"><span>Phone</span><strong>{{ $student->phone ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Sex</span><strong>{{ $student->sex ?? 'N/A' }}</strong></div>
            </div>
        </section>

        <section class="sec-card">
            <div class="sec-card-header"><div><h2>Academic information</h2></div></div>
            <div class="sec-card-body">
                <div class="detail-row"><span>Branch</span><strong>{{ $student->branch->branch_name ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Course</span><strong>{{ $student->course->course_name ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Academic year</span><strong>{{ $student->academic_year->year ?? 'N/A' }}</strong></div>
                <div class="detail-row">
                    <span>Registration fees</span>
                    <strong>
                        @if($student->payed_registration_fees)
                            <span class="badge text-bg-success">Paid</span>
                        @else
                            <span class="badge text-bg-danger">Not paid</span>
                        @endif
                    </strong>
                </div>
                <div class="detail-row"><span>Discount</span><strong>{{ $student->discount ?? 0 }}%</strong></div>
            </div>
        </section>

        <section class="sec-card">
            <div class="sec-card-header"><div><h2>Identification</h2></div></div>
            <div class="sec-card-body">
                <div class="detail-row">
                    <span>Type</span>
                    <strong>
                        @if($student->identification_type)
                            {{ ucfirst(str_replace('_', ' ', $student->identification_type)) }}
                        @else
                            Not set
                        @endif
                    </strong>
                </div>
                <div class="detail-row"><span>ID / passport number</span><strong>{{ $student->id_number ?? 'Missing' }}</strong></div>
            </div>
        </section>

        <section class="sec-card">
            <div class="sec-card-header">
                <div>
                    <h2>Registration status</h2>
                    <p>{{ $isComplete ? 'Ready to mark complete' : 'Still missing required files' }}</p>
                </div>
                @if($isComplete)
                    <span class="badge text-bg-success">Complete</span>
                @else
                    <span class="badge text-bg-warning">Incomplete</span>
                @endif
            </div>
            <div class="sec-card-body">
                <div class="checklist">
                    <div class="checklist-item">ID number: {{ $student->id_number ? 'Complete' : 'Missing' }}</div>
                    <div class="checklist-item">ID photo: {{ $student->id_photo ? 'Uploaded' : 'Missing' }}</div>
                    <div class="checklist-item">Passport photo: {{ $student->passport_photo ? 'Uploaded' : 'Missing' }}</div>
                    <div class="checklist-item">Supporting docs: {{ ($student->senior6_document || $student->senior3_document || $student->other_supporting_document) ? 'Available' : 'None' }}</div>
                </div>
            </div>
        </section>
    </div>

    <section class="sec-card mb-3">
        <div class="sec-card-header"><div><h2>Photos</h2></div></div>
        <div class="sec-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h3 class="h6 mb-2">ID photo</h3>
                    <div class="photo-frame">
                        @if($student->id_photo)
                            <img src="{{ asset('storage/' . $student->id_photo) }}" alt="ID Photo">
                            <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-3">View full size</a>
                        @else
                            <i class="bi bi-image fs-2 text-muted mb-2"></i>
                            <p class="text-muted mb-0">No ID photo uploaded</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="h6 mb-2">Passport photo</h3>
                    <div class="photo-frame">
                        @if($student->passport_photo)
                            <img src="{{ asset('storage/' . $student->passport_photo) }}" alt="Passport Photo">
                            <a href="{{ asset('storage/' . $student->passport_photo) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-3">View full size</a>
                        @else
                            <i class="bi bi-image fs-2 text-muted mb-2"></i>
                            <p class="text-muted mb-0">No passport photo uploaded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sec-card">
        <div class="sec-card-header"><div><h2>Supporting documents</h2></div></div>
        <div class="sec-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="checklist-item">
                        <div class="fw-semibold mb-2">Senior 6</div>
                        @if($student->senior6_document)
                            <a href="{{ asset('storage/' . $student->senior6_document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View document</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checklist-item">
                        <div class="fw-semibold mb-2">Senior 3</div>
                        @if($student->senior3_document)
                            <a href="{{ asset('storage/' . $student->senior3_document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View document</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checklist-item">
                        <div class="fw-semibold mb-2">Other</div>
                        @if($student->other_supporting_document)
                            <a href="{{ asset('storage/' . $student->other_supporting_document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View document</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
