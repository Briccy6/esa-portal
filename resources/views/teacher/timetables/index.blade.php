@extends('teacher.layout')

@section('content')
@php
    $sessionTotal = $timetables->sum(fn ($timetable) => $timetable->sessions->count());
@endphp

<style>
    .teacher-timetable-page {
        --tt-ink: #17243b;
        --tt-muted: #748094;
        --tt-accent: #d26c4f;
        max-width: 1480px;
        margin: 0 auto;
        color: var(--tt-ink);
    }
    .teacher-timetable-hero {
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 2rem;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.6rem);
        margin-bottom: 1.6rem;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 22px;
        background:
            radial-gradient(circle at 82% 18%, rgba(210,108,79,.23), transparent 24%),
            radial-gradient(circle at 96% 95%, rgba(72,137,148,.22), transparent 35%),
            linear-gradient(125deg, #14233a 0%, #1b3851 68%, #22545b 120%);
        color: #fff;
        box-shadow: 0 18px 38px rgba(23,36,59,.16);
    }
    .teacher-timetable-hero::after {
        position: absolute;
        top: -90px;
        right: 16%;
        width: 250px;
        height: 250px;
        border: 1px solid rgba(255,255,255,.09);
        border-radius: 50%;
        box-shadow: 0 0 0 28px rgba(255,255,255,.025), 0 0 0 58px rgba(255,255,255,.02);
        content: "";
        pointer-events: none;
    }
    .teacher-timetable-hero__copy, .teacher-timetable-hero__stats { position: relative; z-index: 1; }
    .teacher-timetable-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        margin-bottom: .85rem;
        color: #efb69f;
        font-size: .69rem;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
    }
    .teacher-timetable-hero__eyebrow::before {
        width: 22px;
        height: 1px;
        background: currentColor;
        content: "";
    }
    .teacher-timetable-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(1.7rem, 4vw, 2.55rem);
        font-weight: 750;
        letter-spacing: -.045em;
        line-height: 1.08;
    }
    .teacher-timetable-hero p {
        max-width: 560px;
        margin: .75rem 0 0;
        color: rgba(242,246,250,.76);
        font-size: .94rem;
        line-height: 1.65;
    }
    .teacher-timetable-hero__stats { display: flex; gap: .7rem; }
    .teacher-timetable-stat {
        min-width: 128px;
        padding: .85rem 1rem;
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 13px;
        background: rgba(255,255,255,.075);
        backdrop-filter: blur(8px);
    }
    .teacher-timetable-stat strong {
        display: block;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        line-height: 1.1;
    }
    .teacher-timetable-stat span {
        display: block;
        margin-top: .35rem;
        color: rgba(242,246,250,.69);
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .04em;
    }
    .teacher-timetable-board {
        overflow: hidden;
        margin-bottom: 1.35rem;
        border: 1px solid #e6e9ee;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 26px rgba(23,36,59,.055);
    }
    .teacher-timetable-board__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        border-bottom: 1px solid #edf0f3;
        background: linear-gradient(100deg, #fff 0%, #fbfaf8 100%);
    }
    .teacher-timetable-board__eyebrow {
        display: block;
        margin-bottom: .3rem;
        color: var(--tt-accent);
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }
    .teacher-timetable-board__title {
        margin: 0;
        color: var(--tt-ink);
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }
    .teacher-timetable-download {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        padding: .65rem .95rem;
        border: 1px solid #bc5b41;
        border-radius: 10px;
        background: #c96348;
        color: #fff;
        font-size: .78rem;
        font-weight: 800;
        transition: background .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .teacher-timetable-download:hover {
        transform: translateY(-1px);
        border-color: #a94e37;
        background: #a94e37;
        color: #fff;
        box-shadow: 0 7px 16px rgba(169,78,55,.2);
    }
    .teacher-timetable-board__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem .7rem;
        padding: .9rem 1.35rem;
        border-bottom: 1px solid #edf0f3;
        background: #fbfcfd;
    }
    .teacher-timetable-meta {
        display: inline-flex;
        align-items: center;
        gap: .42rem;
        padding: .36rem .62rem;
        border: 1px solid #e8ebef;
        border-radius: 7px;
        background: #fff;
        color: #667287;
        font-size: .7rem;
        font-weight: 650;
    }
    .teacher-timetable-meta i { color: #a45b48; }
    .teacher-timetable-empty {
        display: grid;
        min-height: 260px;
        place-items: center;
        padding: 2rem 1rem;
        border: 1px solid #e6e9ee;
        border-radius: 20px;
        background: #fff;
        text-align: center;
        box-shadow: 0 8px 26px rgba(23,36,59,.045);
    }
    .teacher-timetable-empty__icon {
        display: grid;
        width: 56px;
        height: 56px;
        place-items: center;
        margin: 0 auto .9rem;
        border: 1px solid #f0dfd8;
        border-radius: 17px;
        background: #fff6f1;
        color: var(--tt-accent);
        font-size: 1.2rem;
    }
    .teacher-timetable-empty h2 { margin: 0; color: var(--tt-ink); font-size: 1.12rem; font-weight: 800; }
    .teacher-timetable-empty p { max-width: 420px; margin: .45rem auto 0; color: var(--tt-muted); font-size: .84rem; }
    @media (max-width: 767.98px) {
        .teacher-timetable-hero { align-items: flex-start; flex-direction: column; gap: 1.25rem; }
        .teacher-timetable-hero__stats { width: 100%; }
        .teacher-timetable-stat { flex: 1; min-width: 0; }
        .teacher-timetable-board__top { align-items: flex-start; }
    }
    @media (max-width: 520px) {
        .teacher-timetable-page { padding-right: .25rem; padding-left: .25rem; }
        .teacher-timetable-hero { padding: 1.3rem; border-radius: 17px; }
        .teacher-timetable-hero__stats { gap: .5rem; }
        .teacher-timetable-stat { padding: .7rem; }
        .teacher-timetable-stat strong { font-size: 1.2rem; }
        .teacher-timetable-board { border-radius: 15px; }
        .teacher-timetable-board__top { flex-direction: column; padding: 1rem; }
        .teacher-timetable-download { width: 100%; }
        .teacher-timetable-board__meta { padding: .75rem 1rem; }
    }
</style>

<main class="container-fluid py-4 teacher-timetable-page">
    <header class="teacher-timetable-hero">
        <div class="teacher-timetable-hero__copy">
            <span class="teacher-timetable-hero__eyebrow">Teacher workspace · Weekly planner</span>
            <h1>My teaching timetable</h1>
            <p>Your assigned lessons, organized into a clear weekly agenda. Review class times, courses, rooms, and session notes at a glance.</p>
        </div>
        <div class="teacher-timetable-hero__stats" aria-label="Timetable summary">
            <div class="teacher-timetable-stat">
                <strong>{{ $timetables->count() }}</strong>
                <span>Active schedules</span>
            </div>
            <div class="teacher-timetable-stat">
                <strong>{{ $sessionTotal }}</strong>
                <span>Assigned sessions</span>
            </div>
        </div>
    </header>

    @if($timetables->isEmpty())
        <section class="teacher-timetable-empty">
            <div>
                <span class="teacher-timetable-empty__icon"><i class="fa-regular fa-calendar-xmark" aria-hidden="true"></i></span>
                <h2>No active timetable yet</h2>
                <p>Published schedules for your assigned lessons will appear here once they are active.</p>
            </div>
        </section>
    @else
        @foreach($timetables as $timetable)
            <section class="teacher-timetable-board" aria-labelledby="timetable-title-{{ $timetable->id }}">
                <header class="teacher-timetable-board__top">
                    <div>
                        <span class="teacher-timetable-board__eyebrow">Your weekly schedule</span>
                        <h2 id="timetable-title-{{ $timetable->id }}" class="teacher-timetable-board__title">{{ $timetable->title }}</h2>
                    </div>
                    <button
                        type="button"
                        class="teacher-timetable-download"
                        onclick="downloadTimetable({{ $timetable->id }})"
                        aria-label="Download {{ $timetable->title }} timetable"
                    >
                        <i class="fa-solid fa-arrow-down-to-line" aria-hidden="true"></i>
                        Download timetable
                    </button>
                </header>
                <div class="teacher-timetable-board__meta">
                    <span class="teacher-timetable-meta"><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $timetable->branch->name ?? 'Branch not specified' }}</span>
                    <span class="teacher-timetable-meta"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>{{ $timetable->academicYear->year ?? 'Academic year not specified' }}</span>
                    <span class="teacher-timetable-meta"><i class="fa-solid fa-book-open" aria-hidden="true"></i>{{ $timetable->term->name ?? 'Term not specified' }}</span>
                    <span class="teacher-timetable-meta"><i class="fa-regular fa-calendar" aria-hidden="true"></i>
                        {{ $timetable->valid_from?->format('d M Y') ?? 'N/A' }} – {{ $timetable->valid_until?->format('d M Y') ?? 'N/A' }}
                    </span>
                </div>
                @include('teacher.timetables._weekly-agenda', ['sessions' => $timetable->sessions])
            </section>
        @endforeach
    @endif
</main>
@endsection
