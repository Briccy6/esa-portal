@extends('staff.partials.sidebar_sec')

@section('title', 'Timetables')

@include('accountant.partials.theme')

@push('styles')
<style>
    .accountant-timetable-page {
        --accountant-ink: #17243b;
        --accountant-muted: #748094;
        --accountant-accent: #d26c4f;
        max-width: 1480px;
        margin: 0 auto;
        color: var(--accountant-ink);
    }
    .accountant-timetable-hero {
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 2rem;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        margin-bottom: 1.25rem;
        border-radius: 22px;
        background:
            radial-gradient(circle at 82% 15%, rgba(210,108,79,.2), transparent 25%),
            radial-gradient(circle at 96% 95%, rgba(72,137,148,.22), transparent 34%),
            linear-gradient(125deg, #14233a 0%, #1b3851 68%, #22545b 120%);
        color: #fff;
        box-shadow: 0 17px 36px rgba(23,36,59,.15);
    }
    .accountant-timetable-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        margin-bottom: .8rem;
        color: #efb69f;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .15em;
        text-transform: uppercase;
    }
    .accountant-timetable-hero__eyebrow::before { width: 22px; height: 1px; background: currentColor; content: ""; }
    .accountant-timetable-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(1.65rem, 3.8vw, 2.4rem);
        font-weight: 800;
        letter-spacing: -.04em;
        line-height: 1.1;
    }
    .accountant-timetable-hero p { max-width: 580px; margin: .65rem 0 0; color: rgba(242,246,250,.76); font-size: .9rem; line-height: 1.6; }
    .accountant-timetable-stats { display: flex; gap: .65rem; }
    .accountant-timetable-stat {
        min-width: 124px;
        padding: .8rem .95rem;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 12px;
        background: rgba(255,255,255,.075);
        backdrop-filter: blur(8px);
    }
    .accountant-timetable-stat strong { display: block; color: #fff; font-size: 1.4rem; font-weight: 800; font-variant-numeric: tabular-nums; line-height: 1.1; }
    .accountant-timetable-stat span { display: block; margin-top: .35rem; color: rgba(242,246,250,.72); font-size: .68rem; font-weight: 700; }
    .accountant-timetable-filters {
        padding: 1.15rem 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e6e9ee;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(23,36,59,.045);
    }
    .accountant-timetable-filters__heading { display: flex; align-items: center; gap: .55rem; margin-bottom: .9rem; color: var(--accountant-ink); font-size: .88rem; font-weight: 800; }
    .accountant-timetable-filters__heading i { color: var(--accountant-accent); }
    .accountant-timetable-filters .form-label { margin-bottom: .38rem; color: #58657a; font-size: .72rem; font-weight: 750; }
    .accountant-timetable-filters .form-select {
        min-height: 44px;
        border-color: #dfe4eb;
        border-radius: 9px;
        color: #26334a;
        font-size: .82rem;
        box-shadow: none;
    }
    .accountant-timetable-filters .form-select:focus { border-color: #9db9ba; box-shadow: 0 0 0 .2rem rgba(45,111,110,.12); }
    .accountant-filter-actions { display: flex; flex-wrap: wrap; gap: .55rem; }
    .accountant-filter-button, .accountant-clear-button {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .6rem .9rem;
        border-radius: 9px;
        font-size: .76rem;
        font-weight: 800;
        text-decoration: none;
        transition: background .16s ease, transform .16s ease;
    }
    .accountant-filter-button { border: 1px solid #205f62; background: #205f62; color: #fff; }
    .accountant-filter-button:hover { transform: translateY(-1px); border-color: #174b4d; background: #174b4d; color: #fff; }
    .accountant-clear-button { border: 1px solid #dfe4eb; background: #fff; color: #536176; }
    .accountant-clear-button:hover { background: #f5f7f9; color: var(--accountant-ink); }
    .accountant-schedule-card {
        overflow: hidden;
        margin-bottom: 1.25rem;
        border: 1px solid #e6e9ee;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 26px rgba(23,36,59,.055);
    }
    .accountant-schedule-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        background: linear-gradient(100deg, #fff 0%, #fbfaf8 100%);
    }
    .accountant-schedule-card__eyebrow { display: block; margin-bottom: .25rem; color: var(--accountant-accent); font-size: .64rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .accountant-schedule-card__title { margin: 0; color: var(--accountant-ink); font-size: 1.08rem; font-weight: 800; letter-spacing: -.02em; }
    .accountant-timetable-download {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .62rem .9rem;
        border: 1px solid #bc5b41;
        border-radius: 9px;
        background: #c96348;
        color: #fff;
        font-size: .76rem;
        font-weight: 800;
        transition: background .16s ease, box-shadow .16s ease, transform .16s ease;
    }
    .accountant-timetable-download:hover { transform: translateY(-1px); border-color: #a94e37; background: #a94e37; color: #fff; box-shadow: 0 7px 16px rgba(169,78,55,.2); }
    .accountant-schedule-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem .65rem;
        padding: .8rem 1.35rem;
        border-top: 1px solid #edf0f3;
        border-bottom: 1px solid #edf0f3;
        background: #fbfcfd;
    }
    .accountant-schedule-card__meta span {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .34rem .58rem;
        border: 1px solid #e8ebef;
        border-radius: 7px;
        background: #fff;
        color: #667287;
        font-size: .69rem;
        font-weight: 650;
    }
    .accountant-schedule-card__meta i { color: #a45b48; }
    .accountant-timetable-empty {
        display: grid;
        min-height: 230px;
        place-items: center;
        padding: 2rem 1rem;
        border: 1px solid #e6e9ee;
        border-radius: 18px;
        background: #fff;
        text-align: center;
        box-shadow: 0 7px 22px rgba(23,36,59,.045);
    }
    .accountant-timetable-empty__icon {
        display: grid;
        width: 52px;
        height: 52px;
        place-items: center;
        margin: 0 auto .8rem;
        border: 1px solid #f0dfd8;
        border-radius: 15px;
        background: #fff6f1;
        color: var(--accountant-accent);
        font-size: 1.1rem;
    }
    .accountant-timetable-empty h2 { margin: 0; color: var(--accountant-ink); font-size: 1.05rem; font-weight: 800; }
    .accountant-timetable-empty p { max-width: 420px; margin: .45rem auto 0; color: var(--accountant-muted); font-size: .8rem; }
    @media (max-width: 767.98px) {
        .accountant-timetable-hero { align-items: flex-start; flex-direction: column; gap: 1.2rem; }
        .accountant-timetable-stats { width: 100%; }
        .accountant-timetable-stat { flex: 1; min-width: 0; }
    }
    @media (max-width: 520px) {
        .accountant-timetable-page { padding-right: .25rem; padding-left: .25rem; }
        .accountant-timetable-hero { padding: 1.25rem; border-radius: 17px; }
        .accountant-timetable-stat { padding: .7rem; }
        .accountant-timetable-stat strong { font-size: 1.2rem; }
        .accountant-timetable-filters { padding: 1rem; }
        .accountant-schedule-card { border-radius: 15px; }
        .accountant-schedule-card__header { align-items: flex-start; flex-direction: column; padding: 1rem; }
        .accountant-schedule-card__meta { padding: .7rem; }
        .accountant-timetable-download { width: 100%; }
        .accountant-filter-actions, .accountant-filter-button, .accountant-clear-button { width: 100%; }
    }
</style>
@endpush

@section('content')
@php
    $sessionTotal = $timetables->sum(fn ($timetable) => $timetable->sessions->count());
@endphp

<main class="container-fluid py-4 accountant-timetable-page">
    <header class="accountant-timetable-hero">
        <div>
            <span class="accountant-timetable-hero__eyebrow">Accountant workspace · Weekly overview</span>
            <h1>Timetables</h1>
            <p>Review each branch’s published week, check lesson assignments, and download a clean copy for your records.</p>
        </div>
        <div class="accountant-timetable-stats" aria-label="Timetable summary">
            <div class="accountant-timetable-stat">
                <strong>{{ $timetables->count() }}</strong>
                <span>Active schedules</span>
            </div>
            <div class="accountant-timetable-stat">
                <strong>{{ $sessionTotal }}</strong>
                <span>Sessions shown</span>
            </div>
        </div>
    </header>

    <form method="GET" action="{{ route('staff.accountant.timetables.index') }}" class="accountant-timetable-filters">
        <div class="accountant-timetable-filters__heading">
            <i class="fa-solid fa-sliders" aria-hidden="true"></i>Filter timetable
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label" for="branchFilter">Branch</label>
                <select class="form-select" id="branchFilter" name="branch_id">
                    <option value="">All assigned branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string) $filters['branch_id'] === (string) $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="courseFilter">Course</label>
                <select class="form-select" id="courseFilter" name="course_id">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) $filters['course_id'] === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="shiftFilter">Shift</label>
                <select class="form-select" id="shiftFilter" name="shift">
                    <option value="">All shifts</option>
                    <option value="morning" {{ $filters['shift'] === 'morning' ? 'selected' : '' }}>Morning</option>
                    <option value="afternoon" {{ $filters['shift'] === 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                    <option value="evening" {{ $filters['shift'] === 'evening' ? 'selected' : '' }}>Evening</option>
                    <option value="weekend" {{ $filters['shift'] === 'weekend' ? 'selected' : '' }}>Weekend</option>
                </select>
            </div>
            <div class="col-12">
                <div class="accountant-filter-actions">
                    <button type="submit" class="accountant-filter-button">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i>Show timetable
                    </button>
                    <a href="{{ route('staff.accountant.timetables.index') }}" class="accountant-clear-button">Clear filters</a>
                </div>
            </div>
        </div>
    </form>

    @if($timetables->isEmpty())
        <section class="accountant-timetable-empty">
            <div>
                <span class="accountant-timetable-empty__icon"><i class="fa-regular fa-calendar-xmark" aria-hidden="true"></i></span>
                <h2>{{ count(array_filter($filters)) ? 'No matching timetable' : 'No active timetable yet' }}</h2>
                <p>
                    @if(count(array_filter($filters)))
                        No active timetable matches the selected branch, course, and shift. Adjust your filters or clear them to see more schedules.
                    @else
                        Published timetables active for your assigned branches will appear here.
                    @endif
                </p>
            </div>
        </section>
    @else
        @foreach($timetables as $timetable)
            <section class="accountant-schedule-card" aria-labelledby="accountant-timetable-{{ $timetable->id }}">
                <header class="accountant-schedule-card__header">
                    <div>
                        <span class="accountant-schedule-card__eyebrow">Published weekly schedule</span>
                        <h2 id="accountant-timetable-{{ $timetable->id }}" class="accountant-schedule-card__title">{{ $timetable->title }}</h2>
                    </div>
                    <button
                        type="button"
                        class="accountant-timetable-download"
                        onclick="downloadTimetable({{ $timetable->id }})"
                        aria-label="Download {{ $timetable->title }} timetable"
                    >
                        <i class="fa-solid fa-arrow-down-to-line" aria-hidden="true"></i>
                        Download week
                    </button>
                </header>
                <div class="accountant-schedule-card__meta">
                    <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $timetable->branch->name ?? 'Branch not specified' }}</span>
                    <span><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>{{ $timetable->academicYear->year ?? 'Academic year not specified' }}</span>
                    <span><i class="fa-solid fa-book-open" aria-hidden="true"></i>{{ $timetable->term->name ?? 'Term not specified' }}</span>
                    <span><i class="fa-regular fa-calendar" aria-hidden="true"></i>{{ $timetable->valid_from?->format('d M Y') ?? 'N/A' }} – {{ $timetable->valid_until?->format('d M Y') ?? 'N/A' }}</span>
                </div>
                @include('teacher.timetables._weekly-agenda', [
                    'sessions' => $timetable->sessions,
                    'showTeachers' => true,
                    'shiftFilter' => $filters['shift'],
                    'exportHeading' => 'Weekly timetable',
                ])
            </section>
        @endforeach
    @endif
</main>
@endsection
