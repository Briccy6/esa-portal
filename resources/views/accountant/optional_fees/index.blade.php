@extends('staff.partials.sidebar_sec')

@section('title', 'Optional Fees')

@include('accountant.partials.theme')

@section('content')
@php
    $feeTotal = $fees->sum(fn ($fee) => (float) $fee->practical_fee + (float) $fee->trip_fee);
    $notificationsEnabled = $fees->where('notify_students', true)->count();
    $assignedStudents = $fees->sum(fn ($fee) => $fee->assignable_students->count());
@endphp

<style>
    .optional-fees-page {
        --optional-ink: #17243b;
        --optional-muted: #748094;
        --optional-accent: #d26c4f;
        max-width: 1480px;
        margin: 0 auto;
        color: var(--optional-ink);
    }
    .optional-fees-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        padding: clamp(1.4rem, 3.5vw, 2.25rem);
        margin-bottom: 1rem;
        border-radius: 20px;
        background:
            radial-gradient(circle at 84% 12%, rgba(210,108,79,.2), transparent 25%),
            linear-gradient(125deg, #14233a, #1b3851 72%, #22545b 120%);
        color: #fff;
        box-shadow: 0 16px 34px rgba(23,36,59,.14);
    }
    .optional-fees-hero__eyebrow { display: block; margin-bottom: .5rem; color: #efb69f; font-size: .66rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .optional-fees-hero h1 { margin: 0; color: #fff; font-size: clamp(1.5rem, 3vw, 2.1rem); font-weight: 800; letter-spacing: -.035em; }
    .optional-fees-hero p { margin: .45rem 0 0; color: rgba(242,246,250,.74); font-size: .83rem; }
    .optional-fees-hero__actions { display: flex; flex-wrap: wrap; gap: .55rem; }
    .optional-fees-hero__button {
        display: inline-flex;
        min-height: 41px;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .6rem .85rem;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 9px;
        background: rgba(255,255,255,.08);
        color: #fff;
        font-size: .74rem;
        font-weight: 800;
        text-decoration: none;
    }
    .optional-fees-hero__button:hover { background: rgba(255,255,255,.16); color: #fff; }
    .optional-fees-hero__button--primary { border-color: #d47659; background: #c96348; }
    .optional-fees-hero__button--primary:hover { border-color: #a94e37; background: #a94e37; }
    .optional-fees-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .65rem; margin-bottom: 1rem; }
    .optional-fees-stat {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: .7rem;
        padding: .85rem 1rem;
        border: 1px solid #e6e9ee;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(23,36,59,.035);
    }
    .optional-fees-stat__icon { display: grid; width: 37px; height: 37px; flex: 0 0 37px; place-items: center; border: 1px solid #f0dfd8; border-radius: 10px; background: #fff6f1; color: var(--optional-accent); }
    .optional-fees-stat__value { display: block; color: var(--optional-ink); font-size: 1.02rem; font-weight: 850; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
    .optional-fees-stat__label { display: block; margin-top: .12rem; color: var(--optional-muted); font-size: .67rem; font-weight: 700; }
    .optional-fees-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .85rem 1rem;
        margin-bottom: .8rem;
        border: 1px solid #e6e9ee;
        border-radius: 12px;
        background: #fff;
    }
    .optional-fees-toolbar__title { margin: 0; color: var(--optional-ink); font-size: .85rem; font-weight: 850; }
    .optional-fees-toolbar__count { color: var(--optional-muted); font-size: .7rem; }
    .optional-fees-search { position: relative; width: min(100%, 360px); }
    .optional-fees-search i { position: absolute; top: 50%; left: .8rem; color: #98a2b3; transform: translateY(-50%); }
    .optional-fees-search input { min-height: 39px; padding-left: 2.25rem; border: 1px solid #e0e5eb; border-radius: 8px; font-size: .76rem; }
    .optional-fees-search input:focus { border-color: #9db9ba; box-shadow: 0 0 0 .2rem rgba(45,111,110,.11); }
    .optional-fee-card {
        overflow: hidden;
        margin-bottom: .75rem;
        border: 1px solid #e5e9ef;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(23,36,59,.04);
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .optional-fee-card[open] { border-color: #cbd9d9; box-shadow: 0 9px 24px rgba(23,36,59,.07); }
    .optional-fee-card__summary {
        display: grid;
        grid-template-columns: minmax(200px, 1.3fr) repeat(3, minmax(120px, .75fr)) auto;
        align-items: center;
        gap: .8rem;
        padding: 1rem 1.1rem;
        list-style: none;
        cursor: pointer;
    }
    .optional-fee-card__summary::-webkit-details-marker { display: none; }
    .optional-fee-card__identity { min-width: 0; }
    .optional-fee-card__course { display: block; overflow: hidden; color: var(--optional-ink); font-size: .86rem; font-weight: 850; text-overflow: ellipsis; white-space: nowrap; }
    .optional-fee-card__context { display: flex; flex-wrap: wrap; gap: .28rem .55rem; margin-top: .3rem; color: var(--optional-muted); font-size: .68rem; }
    .optional-fee-card__context span { display: inline-flex; align-items: center; gap: .28rem; }
    .optional-fee-card__context i { color: #a45b48; font-size: .63rem; }
    .optional-fee-card__metric-label { display: block; color: #8993a3; font-size: .59rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .optional-fee-card__metric-value { display: block; margin-top: .17rem; color: #304058; font-size: .77rem; font-weight: 800; font-variant-numeric: tabular-nums; }
    .optional-fee-card__chips { display: flex; flex-wrap: wrap; gap: .3rem; }
    .optional-fee-chip { display: inline-flex; align-items: center; gap: .3rem; padding: .28rem .48rem; border-radius: 999px; font-size: .61rem; font-weight: 800; white-space: nowrap; }
    .optional-fee-chip--optional { background: #fff4dc; color: #92610d; }
    .optional-fee-chip--mandatory { background: #e8f4ec; color: #2b7049; }
    .optional-fee-chip--notification { background: #e9f1fb; color: #355f91; }
    .optional-fee-chip--silent { background: #f0f2f5; color: #6d7788; }
    .optional-fee-card__toggle { display: inline-flex; align-items: center; gap: .35rem; color: #286663; font-size: .68rem; font-weight: 800; white-space: nowrap; }
    .optional-fee-card__toggle::after { content: "View details"; }
    .optional-fee-card__toggle i { transition: transform .18s ease; }
    .optional-fee-card[open] .optional-fee-card__toggle::after { content: "Hide details"; }
    .optional-fee-card[open] .optional-fee-card__toggle i { transform: rotate(180deg); }
    .optional-fee-card__body { padding: .2rem 1.1rem 1.1rem; border-top: 1px solid #edf0f3; background: #fcfdfd; }
    .optional-fee-detail-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .55rem; padding: .9rem 0; }
    .optional-fee-detail { min-width: 0; padding: .7rem .75rem; border: 1px solid #e9edf1; border-radius: 9px; background: #fff; }
    .optional-fee-detail__label { display: block; color: #8993a3; font-size: .59rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .optional-fee-detail__value { display: block; margin-top: .22rem; color: #344158; font-size: .73rem; font-weight: 750; overflow-wrap: anywhere; }
    .optional-fee-section { padding: .95rem; margin-top: .65rem; border: 1px solid #e7ebf0; border-radius: 11px; background: #fff; }
    .optional-fee-section__heading { display: flex; align-items: center; gap: .45rem; margin: 0 0 .8rem; color: var(--optional-ink); font-size: .78rem; font-weight: 850; }
    .optional-fee-section__heading i { color: var(--optional-accent); }
    .optional-fee-option {
        display: grid;
        grid-template-columns: minmax(120px, 1fr) repeat(3, minmax(135px, 1fr));
        align-items: end;
        gap: .75rem;
        padding: .75rem 0;
        border-top: 1px solid #f0f2f5;
    }
    .optional-fee-option:first-of-type { border-top: 0; }
    .optional-fee-option__name { align-self: center; color: #344158; font-size: .75rem; font-weight: 800; }
    .optional-fee-card .form-label { margin-bottom: .32rem; color: #647084; font-size: .65rem; font-weight: 750; }
    .optional-fee-card .form-control, .optional-fee-card .form-select { min-height: 39px; border-color: #dfe4eb; border-radius: 8px; font-size: .74rem; }
    .optional-fee-card .form-control:focus, .optional-fee-card .form-select:focus { border-color: #9db9ba; box-shadow: 0 0 0 .18rem rgba(45,111,110,.11); }
    .optional-fee-notify {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        padding: .75rem;
        border: 1px solid #e8ebef;
        border-radius: 9px;
        background: #f8fafb;
    }
    .optional-fee-notify .form-check-input { margin-top: .18rem; }
    .optional-fee-notify strong { display: block; color: #344158; font-size: .72rem; }
    .optional-fee-notify small { display: block; margin-top: .12rem; color: var(--optional-muted); font-size: .66rem; }
    .optional-fee-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .65rem; padding-top: .8rem; }
    .optional-fee-actions__left, .optional-fee-actions__right { display: flex; flex-wrap: wrap; gap: .5rem; }
    .optional-fee-action {
        display: inline-flex;
        min-height: 39px;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .55rem .75rem;
        border: 1px solid #dfe4eb;
        border-radius: 8px;
        background: #fff;
        color: #47556b;
        font-size: .69rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }
    .optional-fee-action:hover { background: #f6f8fa; color: var(--optional-ink); }
    .optional-fee-action--save { border-color: #205f62; background: #205f62; color: #fff; }
    .optional-fee-action--save:hover { border-color: #174b4d; background: #174b4d; color: #fff; }
    .optional-fee-action--delete { border-color: #f1d8d5; background: #fff8f7; color: #a3433d; }
    .optional-fee-action--delete:hover { border-color: #c64d46; background: #c64d46; color: #fff; }
    .optional-fee-action:disabled { cursor: not-allowed; opacity: .48; }
    .optional-fee-assign-note { color: var(--optional-muted); font-size: .65rem; }
    .optional-fees-empty { padding: 3.2rem 1rem; border: 1px solid #e6e9ee; border-radius: 15px; background: #fff; text-align: center; }
    .optional-fees-empty__icon { display: grid; width: 52px; height: 52px; place-items: center; margin: 0 auto .8rem; border: 1px solid #f0dfd8; border-radius: 15px; background: #fff6f1; color: var(--optional-accent); }
    .optional-fees-empty h2 { margin: 0; color: var(--optional-ink); font-size: .98rem; font-weight: 850; }
    .optional-fees-empty p { margin: .4rem 0 0; color: var(--optional-muted); font-size: .76rem; }
    @media (max-width: 1050px) {
        .optional-fee-card__summary { grid-template-columns: minmax(180px, 1.5fr) repeat(2, minmax(115px, 1fr)) auto; }
        .optional-fee-card__summary > :nth-child(4) { display: none; }
        .optional-fee-detail-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .optional-fee-option { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767.98px) {
        .optional-fees-hero { align-items: flex-start; flex-direction: column; }
        .optional-fees-hero__actions { width: 100%; }
        .optional-fees-hero__button { flex: 1; }
        .optional-fees-stats { grid-template-columns: 1fr; }
        .optional-fees-stat { padding: .7rem .85rem; }
        .optional-fees-toolbar { align-items: stretch; flex-direction: column; }
        .optional-fees-search { width: 100%; }
        .optional-fee-card__summary { grid-template-columns: 1fr auto; gap: .65rem; }
        .optional-fee-card__summary > :nth-child(4) { display: block; }
        .optional-fee-card__summary > :nth-child(2), .optional-fee-card__summary > :nth-child(3), .optional-fee-card__summary > :nth-child(4) { grid-column: 1; }
        .optional-fee-card__summary > :nth-child(5) { grid-column: 2; grid-row: 1 / span 4; align-self: center; }
    }
    @media (max-width: 520px) {
        .optional-fees-page { padding-right: .25rem; padding-left: .25rem; }
        .optional-fees-hero { padding: 1.2rem; border-radius: 16px; }
        .optional-fees-hero__actions { flex-direction: column; }
        .optional-fees-hero__button { width: 100%; }
        .optional-fee-card__summary { padding: .85rem; }
        .optional-fee-card__body { padding: .2rem .75rem .85rem; }
        .optional-fee-detail-grid { gap: .4rem; }
        .optional-fee-detail { padding: .6rem; }
        .optional-fee-option { grid-template-columns: 1fr; gap: .5rem; }
        .optional-fee-actions, .optional-fee-actions__left, .optional-fee-actions__right { width: 100%; }
        .optional-fee-actions { flex-direction: column-reverse; align-items: stretch; }
        .optional-fee-actions__left, .optional-fee-actions__right { display: grid; grid-template-columns: 1fr; }
        .optional-fee-action { width: 100%; }
    }
</style>

<main class="container-fluid py-4 optional-fees-page">
    <header class="optional-fees-hero">
        <div>
            <span class="optional-fees-hero__eyebrow">Accountant workspace · Student charges</span>
            <h1>Optional fee management</h1>
            <p>Review practical and trip fees by branch, course, and term. Open a fee to update settings or assign it to eligible students.</p>
        </div>
        <div class="optional-fees-hero__actions">
            <a href="{{ route('staff.accountant.optional-fees.responses') }}" class="optional-fees-hero__button optional-fees-hero__button--primary">
                <i class="fa-solid fa-user-check" aria-hidden="true"></i>Review student decisions
            </a>
            <a href="{{ route('staff.accountant.optional-fees.report') }}" class="optional-fees-hero__button optional-fees-hero__button--primary">
                <i class="fa-solid fa-chart-column" aria-hidden="true"></i>View student responses
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="status">
            <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>We could not complete that action.</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="optional-fees-stats" aria-label="Optional fee summary">
        <article class="optional-fees-stat">
            <span class="optional-fees-stat__icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
            <span><strong class="optional-fees-stat__value">{{ number_format($fees->count()) }}</strong><span class="optional-fees-stat__label">Active fee configurations</span></span>
        </article>
        <article class="optional-fees-stat">
            <span class="optional-fees-stat__icon"><i class="fa-solid fa-coins" aria-hidden="true"></i></span>
            <span><strong class="optional-fees-stat__value">{{ number_format($feeTotal, 0) }} RWF</strong><span class="optional-fees-stat__label">Configured practical and trip fees</span></span>
        </article>
        <article class="optional-fees-stat">
            <span class="optional-fees-stat__icon"><i class="fa-solid fa-bell" aria-hidden="true"></i></span>
            <span><strong class="optional-fees-stat__value">{{ number_format($notificationsEnabled) }} notifying</strong><span class="optional-fees-stat__label">{{ number_format($assignedStudents) }} eligible student places</span></span>
        </article>
    </section>

    <div class="optional-fees-toolbar">
        <div>
            <h2 class="optional-fees-toolbar__title">Fee configurations</h2>
            <span class="optional-fees-toolbar__count"><span id="visibleFeeCount">{{ $fees->count() }}</span> of {{ $fees->count() }} configurations</span>
        </div>
        <label class="optional-fees-search">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input id="feeSearch" type="search" class="form-control" placeholder="Search course, branch, or term" aria-label="Search optional fees">
        </label>
    </div>

    @if($fees->isEmpty())
        <section class="optional-fees-empty">
            <span class="optional-fees-empty__icon"><i class="fa-regular fa-folder-open" aria-hidden="true"></i></span>
            <h2>No active fee configurations</h2>
            <p>Optional fee configurations for your assigned branches will appear here.</p>
        </section>
    @else
        <div id="optionalFeeList">
            @foreach($fees as $fee)
                @php
                    $courseName = $fee->course->course_name ?? 'Course not specified';
                    $branchName = $fee->branch->name ?? 'Branch not specified';
                    $termName = $fee->term->name ?? 'Term not specified';
                    $academicYear = $fee->term->academicYear->year ?? 'Academic year not specified';
                    $hasAcceptedResponses = $fee->accepted_responses_count > 0;
                    $hasOptionalFees = ((float) $fee->practical_fee > 0 && !$fee->is_practical_mandatory)
                        || ((float) $fee->trip_fee > 0 && !$fee->is_trip_mandatory);
                @endphp
                <details
                    class="optional-fee-card"
                    data-search="{{ \Illuminate\Support\Str::lower($courseName . ' ' . $branchName . ' ' . $termName . ' ' . $academicYear) }}"
                >
                    <summary class="optional-fee-card__summary">
                        <span class="optional-fee-card__identity">
                            <strong class="optional-fee-card__course">{{ $courseName }}</strong>
                            <span class="optional-fee-card__context">
                                <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $branchName }}</span>
                                <span><i class="fa-solid fa-calendar" aria-hidden="true"></i>{{ $termName }} · {{ $academicYear }}</span>
                            </span>
                        </span>
                        <span>
                            <span class="optional-fee-card__metric-label">Practical</span>
                            <strong class="optional-fee-card__metric-value">{{ number_format((float) $fee->practical_fee, 0) }} RWF</strong>
                        </span>
                        <span>
                            <span class="optional-fee-card__metric-label">Trip</span>
                            <strong class="optional-fee-card__metric-value">{{ number_format((float) $fee->trip_fee, 0) }} RWF</strong>
                        </span>
                        <span class="optional-fee-card__chips">
                            @if($fee->is_practical_mandatory || $fee->is_trip_mandatory)
                                <span class="optional-fee-chip optional-fee-chip--mandatory"><i class="fa-solid fa-lock" aria-hidden="true"></i>Mandatory</span>
                            @else
                                <span class="optional-fee-chip optional-fee-chip--optional"><i class="fa-regular fa-circle-check" aria-hidden="true"></i>Optional</span>
                            @endif
                            <span class="optional-fee-chip {{ $fee->notify_students ? 'optional-fee-chip--notification' : 'optional-fee-chip--silent' }}">
                                <i class="fa-solid {{ $fee->notify_students ? 'fa-bell' : 'fa-bell-slash' }}" aria-hidden="true"></i>{{ $fee->notify_students ? 'Notifying' : 'Hidden' }}
                            </span>
                        </span>
                        <span class="optional-fee-card__toggle"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
                    </summary>

                    <div class="optional-fee-card__body">
                        <div class="optional-fee-detail-grid">
                            <div class="optional-fee-detail">
                                <span class="optional-fee-detail__label">Practical fee due date</span>
                                <span class="optional-fee-detail__value">{{ $fee->practical_due_date?->format('d M Y') ?? 'Not set' }}</span>
                            </div>
                            <div class="optional-fee-detail">
                                <span class="optional-fee-detail__label">Trip fee due date</span>
                                <span class="optional-fee-detail__value">{{ $fee->trip_due_date?->format('d M Y') ?? 'Not set' }}</span>
                            </div>
                            <div class="optional-fee-detail">
                                <span class="optional-fee-detail__label">Notification period</span>
                                <span class="optional-fee-detail__value">
                                    {{ $fee->valid_from?->format('d M Y') ?? 'Any date' }} – {{ $fee->valid_until?->format('d M Y') ?? 'No end date' }}
                                </span>
                            </div>
                            <div class="optional-fee-detail">
                                <span class="optional-fee-detail__label">Accepted or assigned</span>
                                <span class="optional-fee-detail__value">{{ number_format($fee->accepted_responses_count) }} student responses</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('staff.accountant.optional-fees.update', $fee->id) }}">
                            @csrf
                            @method('PUT')
                            <section class="optional-fee-section" aria-label="Update fee settings">
                                <h3 class="optional-fee-section__heading"><i class="fa-solid fa-sliders" aria-hidden="true"></i>Update fee settings</h3>
                                <div class="optional-fee-option">
                                    <strong class="optional-fee-option__name">Practical fee</strong>
                                    <div>
                                        <label class="form-label" for="practical_fee_{{ $fee->id }}">Amount (RWF)</label>
                                        <input id="practical_fee_{{ $fee->id }}" type="number" step="0.01" min="0" class="form-control" name="practical_fee" value="{{ $fee->practical_fee ?? 0 }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="practical_due_date_{{ $fee->id }}">Due date</label>
                                        <input id="practical_due_date_{{ $fee->id }}" type="date" class="form-control" name="practical_due_date" value="{{ $fee->practical_due_date?->format('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="is_practical_mandatory_{{ $fee->id }}">Requirement</label>
                                        <select id="is_practical_mandatory_{{ $fee->id }}" class="form-select" name="is_practical_mandatory">
                                            <option value="0" {{ !$fee->is_practical_mandatory ? 'selected' : '' }}>Optional</option>
                                            <option value="1" {{ $fee->is_practical_mandatory ? 'selected' : '' }}>Mandatory</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="optional-fee-option">
                                    <strong class="optional-fee-option__name">Trip fee</strong>
                                    <div>
                                        <label class="form-label" for="trip_fee_{{ $fee->id }}">Amount (RWF)</label>
                                        <input id="trip_fee_{{ $fee->id }}" type="number" step="0.01" min="0" class="form-control" name="trip_fee" value="{{ $fee->trip_fee ?? 0 }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="trip_due_date_{{ $fee->id }}">Due date</label>
                                        <input id="trip_due_date_{{ $fee->id }}" type="date" class="form-control" name="trip_due_date" value="{{ $fee->trip_due_date?->format('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="is_trip_mandatory_{{ $fee->id }}">Requirement</label>
                                        <select id="is_trip_mandatory_{{ $fee->id }}" class="form-select" name="is_trip_mandatory">
                                            <option value="0" {{ !$fee->is_trip_mandatory ? 'selected' : '' }}>Optional</option>
                                            <option value="1" {{ $fee->is_trip_mandatory ? 'selected' : '' }}>Mandatory</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="valid_from_{{ $fee->id }}">Notify students from</label>
                                        <input id="valid_from_{{ $fee->id }}" type="date" class="form-control" name="valid_from" value="{{ $fee->valid_from?->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="valid_until_{{ $fee->id }}">Stop notifications after</label>
                                        <input id="valid_until_{{ $fee->id }}" type="date" class="form-control" name="valid_until" value="{{ $fee->valid_until?->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="optional-fee-notify" for="notify_students_{{ $fee->id }}">
                                            <input class="form-check-input flex-shrink-0" type="checkbox" id="notify_students_{{ $fee->id }}" name="notify_students" value="1" {{ $fee->notify_students ? 'checked' : '' }}>
                                            <span>
                                                <strong>Show fee notifications to students</strong>
                                                <small>When off, existing assignments remain payable but no new student popup is shown.</small>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="optional-fee-actions justify-content-end">
                                    <button type="submit" class="optional-fee-action optional-fee-action--save">
                                        <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>Save changes
                                    </button>
                                </div>
                            </section>
                        </form>

                        <div class="optional-fee-actions">
                            <div class="optional-fee-actions__left">
                                @if($hasAcceptedResponses)
                                    <button type="button" class="optional-fee-action optional-fee-action--delete" disabled title="Accepted or assigned fees cannot be deleted while they are part of student balances.">
                                        <i class="fa-solid fa-lock" aria-hidden="true"></i>Delete unavailable · {{ $fee->accepted_responses_count }} student balances
                                    </button>
                                    <span class="optional-fee-assign-note">To protect student balances, turn off notifications or update the fee instead.</span>
                                @else
                                    <form method="POST" action="{{ route('staff.accountant.optional-fees.index') }}/{{ $fee->id }}/delete" onsubmit="return confirm('Delete this fee configuration? Student response history will be preserved.');">
                                        @csrf
                                        <button type="submit" class="optional-fee-action optional-fee-action--delete">
                                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i>Delete configuration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if($hasOptionalFees && $fee->assignable_students->isNotEmpty())
                            <form method="POST" action="{{ route('staff.accountant.optional-fees.assign', $fee->id) }}">
                                @csrf
                                <section class="optional-fee-section" aria-label="Assign optional fee">
                                    <h3 class="optional-fee-section__heading"><i class="fa-solid fa-user-plus" aria-hidden="true"></i>Assign an optional fee to a student</h3>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label" for="student_{{ $fee->id }}">Eligible student</label>
                                            <select class="form-select" id="student_{{ $fee->id }}" name="student_id" required>
                                                <option value="">Select a student</option>
                                                @foreach($fee->assignable_students as $student)
                                                    <option value="{{ $student->id }}">{{ $student->full_name }} · {{ $student->reg_number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <span class="form-label d-block">Fee to assign</span>
                                            <div class="d-flex flex-wrap gap-3">
                                                @if((float) $fee->practical_fee > 0 && !$fee->is_practical_mandatory)
                                                    <label class="form-check mb-0">
                                                        <input class="form-check-input" type="checkbox" name="fee_types[]" value="practical">
                                                        <span class="form-check-label">Practical · {{ number_format($fee->practical_fee, 0) }} RWF</span>
                                                    </label>
                                                @endif
                                                @if((float) $fee->trip_fee > 0 && !$fee->is_trip_mandatory)
                                                    <label class="form-check mb-0">
                                                        <input class="form-check-input" type="checkbox" name="fee_types[]" value="trip">
                                                        <span class="form-check-label">Trip · {{ number_format($fee->trip_fee, 0) }} RWF</span>
                                                    </label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-2">
                                            <button type="submit" class="optional-fee-action optional-fee-action--save w-100">
                                                <i class="fa-solid fa-user-check" aria-hidden="true"></i>Assign fee
                                            </button>
                                        </div>
                                    </div>
                                    <p class="optional-fee-assign-note mt-2 mb-0">Assigned fees are added to the student’s payable balance and cannot be deleted while accepted or assigned.</p>
                                </section>
                            </form>
                        @elseif($hasOptionalFees)
                            <p class="optional-fee-assign-note mt-3 mb-0">No students currently match this branch, course, and academic year.</p>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
        <div class="optional-fees-empty d-none" id="noFeeSearchResults">
            <span class="optional-fees-empty__icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
            <h2>No matching fee</h2>
            <p>Try searching with a different course, branch, or term name.</p>
        </div>
    @endif
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('feeSearch');
    const cards = Array.from(document.querySelectorAll('.optional-fee-card'));
    const visibleCount = document.getElementById('visibleFeeCount');
    const noResults = document.getElementById('noFeeSearchResults');

    if (!search) return;

    search.addEventListener('input', function () {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = 0;

        cards.forEach(function (card) {
            const matches = !query || (card.dataset.search || '').includes(query);
            card.hidden = !matches;
            if (matches) visible += 1;
        });

        if (visibleCount) visibleCount.textContent = String(visible);
        if (noResults) noResults.classList.toggle('d-none', visible !== 0 || cards.length === 0);
    });
});
</script>
@endsection
