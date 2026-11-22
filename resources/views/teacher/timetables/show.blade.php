@extends('teacher.layout')

@section('content')
<style>
    .teacher-timetable-detail {
        --detail-ink: #17243b;
        --detail-muted: #748094;
        max-width: 1480px;
        margin: 0 auto;
        color: var(--detail-ink);
    }
    .teacher-timetable-detail__hero {
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        overflow: hidden;
        padding: clamp(1.4rem, 3.5vw, 2.3rem);
        margin-bottom: 1.3rem;
        border-radius: 20px;
        background:
            radial-gradient(circle at 85% 15%, rgba(210,108,79,.2), transparent 25%),
            linear-gradient(125deg, #14233a 0%, #1b3851 72%, #22545b 120%);
        color: #fff;
        box-shadow: 0 16px 34px rgba(23,36,59,.14);
    }
    .teacher-timetable-detail__eyebrow {
        display: block;
        margin-bottom: .55rem;
        color: #efb69f;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }
    .teacher-timetable-detail__hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(1.45rem, 3vw, 2.1rem);
        font-weight: 800;
        letter-spacing: -.035em;
    }
    .teacher-timetable-detail__hero p { margin: .55rem 0 0; color: rgba(242,246,250,.75); font-size: .85rem; }
    .teacher-timetable-detail__actions { display: flex; flex-wrap: wrap; gap: .55rem; }
    .teacher-timetable-detail__action {
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
        font-size: .75rem;
        font-weight: 800;
        text-decoration: none;
        transition: background .16s ease, transform .16s ease;
    }
    .teacher-timetable-detail__action:hover { transform: translateY(-1px); background: rgba(255,255,255,.16); color: #fff; }
    .teacher-timetable-detail__action--download { border-color: #bc5b41; background: #c96348; }
    .teacher-timetable-detail__action--download:hover { background: #a94e37; }
    .teacher-timetable-detail__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        padding: .85rem 1rem;
        margin-bottom: 0;
        border: 1px solid #e6e9ee;
        border-bottom: 0;
        border-radius: 16px 16px 0 0;
        background: #fbfcfd;
    }
    .teacher-timetable-detail__meta span {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .34rem .58rem;
        border: 1px solid #e8ebef;
        border-radius: 7px;
        background: #fff;
        color: var(--detail-muted);
        font-size: .69rem;
        font-weight: 650;
    }
    .teacher-timetable-detail__meta i { color: #a45b48; }
    @media (max-width: 720px) {
        .teacher-timetable-detail__hero { align-items: flex-start; flex-direction: column; }
        .teacher-timetable-detail__actions { width: 100%; }
        .teacher-timetable-detail__action { flex: 1; }
    }
    @media (max-width: 480px) {
        .teacher-timetable-detail { padding-right: .25rem; padding-left: .25rem; }
        .teacher-timetable-detail__hero { padding: 1.2rem; border-radius: 16px; }
        .teacher-timetable-detail__actions { flex-direction: column; }
        .teacher-timetable-detail__meta { padding: .7rem; }
    }
</style>

<main class="container-fluid py-4 teacher-timetable-detail">
    <header class="teacher-timetable-detail__hero">
        <div>
            <span class="teacher-timetable-detail__eyebrow">Schedule details · {{ $teacherSessions->count() }} assigned sessions</span>
            <h1>{{ $timetable->title }}</h1>
            <p>A complete week of your assigned teaching sessions and class details.</p>
        </div>
        <div class="teacher-timetable-detail__actions">
            <a href="{{ route('teacher.timetables.index') }}" class="teacher-timetable-detail__action">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>All timetables
            </a>
            <button type="button" class="teacher-timetable-detail__action teacher-timetable-detail__action--download" onclick="downloadTimetable({{ $timetable->id }})">
                <i class="fa-solid fa-arrow-down-to-line" aria-hidden="true"></i>Download
            </button>
        </div>
    </header>

    <div class="teacher-timetable-detail__meta">
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $timetable->branch->name ?? 'Branch not specified' }}</span>
        <span><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>{{ $timetable->academicYear->year ?? 'Academic year not specified' }}</span>
        <span><i class="fa-solid fa-book-open" aria-hidden="true"></i>{{ $timetable->term->name ?? 'Term not specified' }}</span>
        <span><i class="fa-regular fa-calendar" aria-hidden="true"></i>{{ $timetable->valid_from?->format('d M Y') ?? 'N/A' }} – {{ $timetable->valid_until?->format('d M Y') ?? 'N/A' }}</span>
    </div>

    @include('teacher.timetables._weekly-agenda', ['sessions' => $teacherSessions])
</main>
@endsection
