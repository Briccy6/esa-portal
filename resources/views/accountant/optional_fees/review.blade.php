@extends($isAdmin ? 'admin.layout' : 'staff.partials.sidebar_sec')

@section('title', 'Review Optional Fee Responses')

@if(!$isAdmin)
    @include('accountant.partials.theme')
@endif

@section('content')
<style>
    .fee-review {
        --review-ink: #17243b;
        --review-muted: #748094;
        --review-border: #e4e9ef;
        max-width: 1540px;
        margin: 0 auto;
        color: var(--review-ink);
    }
    .fee-review__hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.25rem;
        padding: clamp(1.35rem, 3vw, 2rem);
        margin-bottom: 1rem;
        border-radius: 18px;
        background: radial-gradient(circle at 85% 12%, rgba(114, 173, 155, .22), transparent 25%),
            linear-gradient(125deg, #14233a, #1d4655 78%, #28665e);
        color: #fff;
        box-shadow: 0 15px 34px rgba(23, 36, 59, .13);
    }
    .fee-review__eyebrow { color: #a7d6c6; font-size: .66rem; font-weight: 850; letter-spacing: .14em; text-transform: uppercase; }
    .fee-review__hero h1 { margin: .35rem 0 0; color: #fff; font-size: clamp(1.45rem, 3vw, 2rem); font-weight: 850; letter-spacing: -.035em; }
    .fee-review__hero p { margin: .45rem 0 0; color: rgba(255,255,255,.72); font-size: .82rem; }
    .fee-review__hero-link { display: inline-flex; min-height: 40px; align-items: center; gap: .45rem; padding: .55rem .8rem; border: 1px solid rgba(255,255,255,.24); border-radius: 9px; color: #fff; font-size: .72rem; font-weight: 800; text-decoration: none; white-space: nowrap; }
    .fee-review__hero-link:hover { background: rgba(255,255,255,.1); color: #fff; }
    .fee-review__panel, .fee-review__table-wrap { overflow: hidden; border: 1px solid var(--review-border); border-radius: 13px; background: #fff; box-shadow: 0 5px 18px rgba(23,36,59,.04); }
    .fee-review__filters { padding: 1rem; margin-bottom: 1rem; }
    .fee-review .form-label { color: #59677b; font-size: .68rem; font-weight: 800; }
    .fee-review .form-control, .fee-review .form-select { min-height: 40px; border-color: #dce3eb; border-radius: 8px; font-size: .76rem; }
    .fee-review__table { min-width: 1080px; margin: 0; }
    .fee-review__table thead th { padding: .8rem .85rem; border-bottom: 1px solid var(--review-border); background: #f7f9fb; color: #748094; font-size: .62rem; font-weight: 850; letter-spacing: .07em; text-transform: uppercase; white-space: nowrap; }
    .fee-review__table tbody td { padding: .9rem .85rem; border-color: #eef1f4; color: #3e4b60; font-size: .74rem; vertical-align: middle; }
    .fee-review__student { display: block; color: var(--review-ink); font-size: .79rem; font-weight: 850; }
    .fee-review__subline { display: block; margin-top: .18rem; color: var(--review-muted); font-size: .67rem; }
    .fee-review__badge { display: inline-flex; align-items: center; gap: .3rem; padding: .32rem .55rem; border-radius: 999px; font-size: .65rem; font-weight: 850; white-space: nowrap; }
    .fee-review__badge--accepted { background: #e8f6ec; color: #287044; }
    .fee-review__badge--declined { background: #fff0ef; color: #a33e39; }
    .fee-review__badge--pending { background: #fff5df; color: #916319; }
    .fee-review__badge--assigned { background: #eaf1fa; color: #355d8c; }
    .fee-review__decision { display: flex; min-width: 210px; align-items: center; gap: .4rem; }
    .fee-review__decision .form-select { min-height: 36px; font-size: .7rem; }
    .fee-review__decision .btn { min-height: 36px; padding: .4rem .6rem; font-size: .68rem; font-weight: 800; white-space: nowrap; }
    .fee-review__inactive { color: #8893a3; font-size: .68rem; font-weight: 750; }
    .fee-review__pagination { display: flex; justify-content: flex-end; padding: .9rem 1rem; border-top: 1px solid var(--review-border); }
    @media (max-width: 767.98px) {
        .fee-review { padding-top: 1rem !important; }
        .fee-review__hero { align-items: flex-start; flex-direction: column; }
        .fee-review__hero-link { width: 100%; justify-content: center; }
        .fee-review__pagination { justify-content: center; overflow-x: auto; }
    }
</style>

@php
    $reviewRoute = $isAdmin ? 'admin.optional-fees.responses' : 'staff.accountant.optional-fees.responses';
    $updateRoute = $isAdmin ? 'admin.optional-fees.responses.update' : 'staff.accountant.optional-fees.responses.update';
@endphp

<div class="container-fluid py-4 fee-review">
    <header class="fee-review__hero">
        <div>
            <span class="fee-review__eyebrow">Student decisions · Optional fees</span>
            <h1>Review student responses</h1>
            <p>Check each student’s course and fee decision, then update an optional fee to accepted or declined when needed.</p>
        </div>
        <a class="fee-review__hero-link" href="{{ $isAdmin ? route('admin.dashboard') : route('staff.accountant.dashboard') }}">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Back to dashboard
        </a>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Could not update the response.</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="GET" action="{{ route($reviewRoute) }}" class="fee-review__panel fee-review__filters">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-xl-2">
                <label for="filter-year" class="form-label">Academic year</label>
                <select id="filter-year" name="academic_year_id" class="form-select">
                    <option value="">All years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-xl-2">
                <label for="filter-branch" class="form-label">Branch</label>
                <select id="filter-branch" name="branch_id" class="form-select">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <label for="filter-course" class="form-label">Course</label>
                <select id="filter-course" name="course_id" class="form-select">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-xl-2">
                <label for="filter-fee-type" class="form-label">Fee type</label>
                <select id="filter-fee-type" name="fee_type" class="form-select">
                    <option value="">All types</option>
                    <option value="practical" @selected(request('fee_type') === 'practical')>Practical</option>
                    <option value="trip" @selected(request('fee_type') === 'trip')>Trip</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-xl-2">
                <label for="filter-response" class="form-label">Decision</label>
                <select id="filter-response" name="response" class="form-select">
                    <option value="">All decisions</option>
                    <option value="accepted" @selected(request('response') === 'accepted')>Accepted</option>
                    <option value="declined" @selected(request('response') === 'declined')>Declined</option>
                    <option value="pending" @selected(request('response') === 'pending')>Pending</option>
                </select>
            </div>
            <div class="col-12 col-xl-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1" aria-label="Apply filters"><i class="fa-solid fa-filter" aria-hidden="true"></i></button>
                <a href="{{ route($reviewRoute) }}" class="btn btn-outline-secondary" aria-label="Clear filters"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i></a>
            </div>
        </div>
    </form>

    <section class="fee-review__table-wrap" aria-label="Student optional fee responses">
        <div class="table-responsive">
            <table class="table table-hover fee-review__table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Branch & course</th>
                        <th>Fee</th>
                        <th>Decision</th>
                        <th>Responded</th>
                        <th>Review action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($responses as $response)
                        @php
                            $student = $response->student;
                            $fee = $response->courseTermBranchFee;
                            $assigned = str_starts_with((string) $response->notes, 'Assigned by accountant:');
                            $feeAmount = $response->fee_type === 'practical' ? $fee->practical_fee : $fee->trip_fee;
                            $isOptional = $response->fee_type === 'practical' ? !$fee->is_practical_mandatory : !$fee->is_trip_mandatory;
                        @endphp
                        <tr>
                            <td>
                                <span class="fee-review__student">{{ $student->full_name }}</span>
                                <span class="fee-review__subline">{{ $student->reg_number ?: 'No registration number' }} · {{ $student->phone ?: 'No phone' }}</span>
                                <span class="fee-review__subline">{{ $student->email ?: 'No email' }} · {{ $student->shift ?: 'Shift not set' }}</span>
                            </td>
                            <td>
                                <span class="fee-review__student">{{ $student->course->course_name ?? 'Course unavailable' }}</span>
                                <span class="fee-review__subline">{{ $student->branch->name ?? 'Branch unavailable' }} · {{ $student->academicYear->year ?? 'Year unavailable' }}</span>
                            </td>
                            <td>
                                <span class="fee-review__student">{{ ucfirst($response->fee_type) }} · {{ number_format((float) $feeAmount, 2) }} RWF</span>
                                <span class="fee-review__subline">{{ $fee->term->name ?? 'Term unavailable' }}</span>
                            </td>
                            <td>
                                <span class="fee-review__badge fee-review__badge--{{ $assigned ? 'assigned' : $response->response }}">
                                    <i class="fa-solid {{ $assigned ? 'fa-user-check' : ($response->response === 'accepted' ? 'fa-circle-check' : ($response->response === 'declined' ? 'fa-circle-xmark' : 'fa-clock')) }}" aria-hidden="true"></i>
                                    {{ $assigned ? 'Assigned by accountant' : ucfirst($response->response) }}
                                </span>
                                @if($response->notes && !$assigned)
                                    <span class="fee-review__subline">{{ $response->notes }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $response->responded_at?->format('d M Y, H:i') ?? 'Not recorded' }}</td>
                            <td>
                                @if($fee->is_active && $isOptional)
                                    <form method="POST" action="{{ route($updateRoute, $response->id) }}" class="fee-review__decision">
                                        @csrf
                                        @method('PATCH')
                                        <label class="visually-hidden" for="response-{{ $response->id }}">Update {{ $student->full_name }}'s decision</label>
                                        <select id="response-{{ $response->id }}" name="response" class="form-select" required>
                                            @if(!in_array($response->response, ['accepted', 'declined'], true))
                                                <option value="" selected disabled>Select decision</option>
                                            @endif
                                            <option value="accepted" @selected($response->response === 'accepted')>Approve</option>
                                            <option value="declined" @selected($response->response === 'declined')>Reject</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary">Save</button>
                                    </form>
                                @else
                                    <span class="fee-review__inactive">{{ $fee->is_active ? 'Mandatory fee · no response override' : 'Inactive fee · history only' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox d-block mb-2 fs-3" aria-hidden="true"></i>
                            No optional fee responses match these filters.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="fee-review__pagination">{{ $responses->links() }}</div>
    </section>
</div>
@endsection
