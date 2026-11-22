@extends('staff.partials.sidebar_sec')

@section('title', 'Timetables')

@include('accountant.partials.theme')

@section('content')
<style>
    .accountant-timetable-page { --tt-ink: #172033; --tt-muted: #667085; --tt-line: #e5eaf2; }
    .timetable-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.25rem;
        padding: clamp(1.1rem, 3vw, 1.75rem);
        margin-bottom: 1.25rem;
        border-radius: 16px;
        color: #fff;
        background: linear-gradient(115deg, #163c9a, #2563eb 72%, #0e7490);
        box-shadow: 0 12px 30px rgba(30, 64, 175, .18);
    }
    .timetable-header h2 { font-size: clamp(1.25rem, 2.2vw, 1.65rem); font-weight: 800; margin: 0 0 .3rem; }
    .timetable-header p { color: rgba(255,255,255,.82); margin: 0; font-size: .92rem; }
    .filter-panel, .timetable-card {
        border: 1px solid var(--tt-line);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .045);
    }
    .filter-panel { padding: 1rem; margin-bottom: 1.25rem; }
    .filter-panel .form-label { color: #344054; font-size: .78rem; font-weight: 750; margin-bottom: .4rem; }
    .filter-panel .form-select { min-height: 44px; border-color: #d7dfeb; border-radius: 9px; }
    .filter-panel .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 .2rem rgba(59,130,246,.13); }
    .filter-panel .btn { min-height: 44px; border-radius: 9px; font-weight: 700; }
    .timetable-card { margin-bottom: 1.25rem; overflow: hidden; }
    .timetable-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.15rem;
        color: #fff;
        background: #1747b0;
    }
    .timetable-card-header h3 { font-size: 1rem; font-weight: 800; margin: 0 0 .35rem; }
    .timetable-meta { display: flex; flex-wrap: wrap; gap: .35rem 1rem; color: rgba(255,255,255,.84); font-size: .78rem; }
    .download-btn {
        flex: 0 0 auto;
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 9px;
        background: rgba(7, 28, 87, .34);
        color: white;
        padding: .55rem .8rem;
        font-weight: 700;
        font-size: .82rem;
    }
    .download-btn:hover { background: rgba(7, 28, 87, .58); color: #fff; }
    .timetable-card-body { padding: 1rem; }
    .timetable-table-wrap { overflow-x: auto; }
    .timetable-table { width: 100%; min-width: 1050px; margin: 0; vertical-align: middle; }
    .timetable-table thead th {
        padding: .72rem .8rem;
        border-color: var(--tt-line);
        background: #f3f6fb;
        color: #475467;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .045em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .timetable-table td { padding: .78rem .8rem; border-color: var(--tt-line); color: #344054; font-size: .83rem; }
    .timetable-table tbody tr:hover { background: #f8fbff; }
    .timetable-table .day-cell { color: #1d4ed8; font-weight: 800; white-space: nowrap; }
    .timetable-table .time-cell { min-width: 145px; color: #172033; font-weight: 750; }
    .timetable-table .course-badges { display: flex; flex-wrap: wrap; gap: .3rem; min-width: 130px; }
    .timetable-table .course-badges .badge { white-space: normal; text-align: left; }
    .timetable-table .shift-line { display: block; white-space: nowrap; }
    .timetable-table .shift-line + .shift-line { margin-top: .22rem; }
    .timetable-table .notes-cell { min-width: 160px; max-width: 240px; overflow-wrap: anywhere; }
    .timetable-table .empty-day td { padding: .65rem .8rem; background: #fbfcfe; color: #98a2b3; font-style: italic; }
    .week-count {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .38rem .65rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        color: #fff;
        font-size: .75rem;
        font-weight: 750;
    }
    @media (max-width: 767.98px) {
        .timetable-header { align-items: flex-start; flex-direction: column; }
        .timetable-card-header { flex-direction: column; }
        .download-btn { width: 100%; }
        .timetable-card-body { padding: .75rem; }
        .timetable-table-wrap { overflow: visible; }
        .timetable-table { display: block; min-width: 0; }
        .timetable-table thead { display: none; }
        .timetable-table tbody { display: grid; gap: .75rem; }
        .timetable-table tbody tr:not(.empty-day) {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            overflow: hidden;
            border: 1px solid var(--tt-line);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15,23,42,.035);
        }
        .timetable-table tbody tr:not(.empty-day) td {
            display: block;
            min-width: 0;
            padding: .65rem;
            border: 0;
            border-bottom: 1px solid #eef1f6;
            overflow-wrap: anywhere;
        }
        .timetable-table tbody tr:not(.empty-day) td::before {
            display: block;
            margin-bottom: .25rem;
            color: var(--tt-muted);
            content: attr(data-label);
            font-size: .64rem;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .timetable-table tbody tr:not(.empty-day) td:first-child,
        .timetable-table tbody tr:not(.empty-day) td:last-child,
        .timetable-table tbody tr:not(.empty-day) .notes-cell { grid-column: 1 / -1; }
        .timetable-table .time-cell { min-width: 0; background: #f8faff; }
        .timetable-table .notes-cell { max-width: none; }
        .timetable-table .empty-day { display: block; }
        .timetable-table .empty-day td { display: block; border: 1px solid var(--tt-line); border-radius: 10px; }
    }
    @media (max-width: 420px) {
        .filter-panel { padding: .8rem; }
        .timetable-table tbody tr:not(.empty-day) { grid-template-columns: 1fr; }
        .timetable-table tbody tr:not(.empty-day) td:first-child,
        .timetable-table tbody tr:not(.empty-day) td:last-child,
        .timetable-table tbody tr:not(.empty-day) .notes-cell { grid-column: auto; }
    }
</style>

<div class="container-fluid py-4 accountant-timetable-page">
    <div class="timetable-header">
        <div>
            <h2><i class="fas fa-calendar-week me-2" aria-hidden="true"></i>Weekly timetables</h2>
            <p>Choose a branch, course, and shift to review the full published week.</p>
        </div>
        <span class="week-count">
            <i class="fas fa-list-check" aria-hidden="true"></i>
            {{ $timetables->sum(fn ($timetable) => $timetable->sessions->count()) }} sessions shown
        </span>
    </div>

    <form method="GET" action="{{ route('staff.accountant.timetables.index') }}" class="filter-panel">
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
            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-filter me-2" aria-hidden="true"></i>Show timetable
                </button>
                <a href="{{ route('staff.accountant.timetables.index') }}" class="btn btn-outline-secondary px-4">Clear filters</a>
            </div>
        </div>
    </form>

    @if($timetables->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <i class="fas fa-circle-info me-2" aria-hidden="true"></i>
            @if(count(array_filter($filters)))
                No active timetable matches the selected branch, course, and shift. Try another filter or clear the selection.
            @else
                No published timetables are active for your assigned branches this week.
            @endif
        </div>
    @else
        @foreach($timetables as $timetable)
            <section class="timetable-card" data-timetable-id="{{ $timetable->id }}">
                <header class="timetable-card-header">
                    <div>
                        <h3 data-timetable-title>{{ $timetable->title }}</h3>
                        <div class="timetable-meta">
                            <span><i class="fas fa-location-dot me-1" aria-hidden="true"></i>{{ $timetable->branch->name ?? 'N/A' }}</span>
                            <span><i class="fas fa-graduation-cap me-1" aria-hidden="true"></i>{{ $timetable->academicYear->year ?? 'N/A' }}</span>
                            <span><i class="fas fa-book-open me-1" aria-hidden="true"></i>{{ $timetable->term->name ?? 'N/A' }}</span>
                            <span>
                                <i class="fas fa-calendar-days me-1" aria-hidden="true"></i>
                                {{ $timetable->valid_from?->format('d M Y') ?? 'N/A' }} - {{ $timetable->valid_until?->format('d M Y') ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <button onclick="downloadTimetable({{ $timetable->id }})" class="download-btn" type="button">
                        <i class="fas fa-download me-2" aria-hidden="true"></i>Download week
                    </button>
                </header>
                <div class="timetable-card-body">
                    <div class="timetable-table-wrap">
                        <table id="timetable-{{ $timetable->id }}" class="table timetable-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Day</th>
                                    <th>Course</th>
                                    <th>Lesson / activity</th>
                                    <th>Room</th>
                                    <th>Teacher</th>
                                    <th>Shift</th>
                                    <th>Session type</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'])
                                @foreach($days as $dayNum => $dayName)
                                    @php($daySessions = $timetable->sessions->where('day_of_week', $dayNum)->sortBy('start_time'))
                                    @if($daySessions->isEmpty())
                                        <tr class="empty-day">
                                            <td colspan="9"><strong>{{ $dayName }}</strong> - No sessions scheduled</td>
                                        </tr>
                                    @else
                                        @foreach($daySessions as $session)
                                            @php
                                                $selectedShiftValues = $filters['shift'] === 'morning' ? ['morning', 'day'] : [$filters['shift']];
                                                $visibleShifts = $filters['shift']
                                                    ? $session->shifts->filter(fn ($shift) => in_array($shift->shift, $selectedShiftValues, true))
                                                    : $session->shifts;
                                                $activityType = $session->activity_type ?: 'lesson';
                                                $isTest = $session->session_type === 'test' || $session->mark_as_test;
                                                $teacherNames = $session->branch_teacher_names;
                                                if ($teacherNames->isEmpty() && $session->lesson && $session->lesson->teacher) {
                                                    $teacherNames->push($session->lesson->teacher->teacher_name);
                                                }
                                                if ($teacherNames->isEmpty() && $session->lesson) {
                                                    $teacherNames = $session->lesson->teachers->pluck('teacher_name')->filter();
                                                }
                                            @endphp
                                            <tr>
                                                <td class="time-cell" data-label="Time">
                                                    @forelse($visibleShifts as $shift)
                                                        @php
                                                            $shiftLabel = in_array($shift->shift, ['day', 'morning'], true) ? 'Morning' : ucfirst($shift->shift);
                                                            $startTime = $shift->start_time ?: $session->start_time;
                                                            $endTime = $shift->end_time ?: $session->end_time;
                                                        @endphp
                                                        <span class="shift-line">
                                                            <strong>{{ $shiftLabel }}</strong>
                                                            @if($startTime && $endTime)
                                                                {{ \Carbon\Carbon::parse($startTime)->format('H:i') }} - {{ \Carbon\Carbon::parse($endTime)->format('H:i') }}
                                                            @else
                                                                Time not set
                                                            @endif
                                                        </span>
                                                    @empty
                                                        <span class="shift-line">
                                                            @if($session->start_time && $session->end_time)
                                                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                            @else
                                                                Time not set
                                                            @endif
                                                        </span>
                                                    @endforelse
                                                </td>
                                                <td class="day-cell" data-label="Day">{{ $dayName }}</td>
                                                <td data-label="Course">
                                                    <div class="course-badges">
                                                        @forelse($session->courses as $course)
                                                            <span class="badge text-bg-info">{{ $course->course_name }}</span>
                                                        @empty
                                                            <span class="text-muted">No course assigned</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td data-label="Lesson / activity">
                                                    <div class="fw-semibold">
                                                        @if($activityType === 'lesson' && $session->lesson)
                                                            {{ $session->lesson->title }}
                                                        @elseif($activityType === 'group_work' && $session->lesson)
                                                            {{ $session->lesson->title }}
                                                        @elseif($activityType === 'self_study')
                                                            Self study
                                                        @elseif($activityType === 'group_work')
                                                            Group work
                                                        @elseif($activityType === 'reserve')
                                                            {{ $session->reserve_text ?: 'Reserve time' }}
                                                        @else
                                                            {{ $session->lesson->title ?? 'N/A' }}
                                                        @endif
                                                    </div>
                                                    <span class="badge text-bg-light mt-1">{{ ucfirst(str_replace('_', ' ', $activityType)) }}</span>
                                                </td>
                                                <td data-label="Room">{{ $session->classroom ?: 'Not assigned' }}</td>
                                                <td data-label="Teacher">
                                                    @if($teacherNames->isNotEmpty())
                                                        <span class="badge text-bg-success">{{ $teacherNames->join(', ') }}</span>
                                                    @elseif($activityType === 'lesson' || $activityType === 'group_work')
                                                        <span class="badge text-bg-warning">To be assigned</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Shift">
                                                    @forelse($visibleShifts as $shift)
                                                        @php($shiftLabel = in_array($shift->shift, ['day', 'morning'], true) ? 'Morning' : ucfirst($shift->shift))
                                                        <span class="badge {{ in_array($shift->shift, ['day', 'morning'], true) ? 'text-bg-success' : ($shift->shift === 'evening' ? 'text-bg-warning' : ($shift->shift === 'afternoon' ? 'text-bg-info' : 'text-bg-secondary')) }}">
                                                            {{ $shiftLabel }}
                                                        </span>
                                                    @empty
                                                        @if($session->shift)
                                                            <span class="badge text-bg-secondary">{{ $session->shift === 'day' ? 'Morning' : ucfirst($session->shift) }}</span>
                                                        @else
                                                            <span class="text-muted">Not assigned</span>
                                                        @endif
                                                    @endforelse
                                                </td>
                                                <td data-label="Session type">
                                                    <span class="badge {{ $isTest ? 'text-bg-danger' : 'text-bg-primary' }}">
                                                        {{ $isTest ? 'Test' : ucfirst($session->session_type ?: 'Class') }}
                                                    </span>
                                                    @if($isTest && $session->minimum_paid_percentage !== null)
                                                        <small class="d-block mt-1 text-muted">Minimum paid: {{ number_format((float) $session->minimum_paid_percentage, 0) }}%</small>
                                                    @endif
                                                </td>
                                                <td class="notes-cell" data-label="Notes">{{ $session->notes ?: 'No notes' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endforeach
    @endif
</div>

<script>
function downloadTimetable(timetableId) {
    const table = document.getElementById('timetable-' + timetableId);
    if (!table) return;

    const tableClone = table.cloneNode(true);
    const timetableCard = table.closest('.timetable-card');
    const title = timetableCard?.querySelector('[data-timetable-title]')?.textContent.trim() || 'Timetable';
    const metadata = timetableCard?.querySelector('.timetable-meta')?.textContent.trim() || '';
    const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
    const html = `
        <html>
        <head>
            <meta charset="utf-8">
            <title>${escapeHtml(title)}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #1747b0; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h2>${escapeHtml(title)}</h2>
            <p>${escapeHtml(metadata)}</p>
            ${tableClone.outerHTML}
        </body>
        </html>
    `;

    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${title.replace(/[^a-z0-9-_]+/gi, '-').replace(/^-|-$/g, '') || 'timetable'}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection
