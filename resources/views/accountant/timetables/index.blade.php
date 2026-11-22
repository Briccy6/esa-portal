@extends('staff.partials.sidebar_sec')

@section('title', 'Timetables')

@include('accountant.partials.theme')

@push('styles')
<style>
    .accountant-timetable-page {
        --tt-primary: #0f766e;
        --tt-primary-dark: #115e59;
        --tt-line: #e2e8f0;
        --tt-surface: #ffffff;
        --tt-muted: #64748b;
        font-family: 'Poppins', sans-serif;
        background: #f8fafc;
    }
    .timetable-header {
        background: linear-gradient(135deg, var(--tt-primary) 0%, var(--tt-primary-dark) 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 16px rgba(15, 118, 110, .12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .timetable-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }
    .timetable-header p {
        margin: 0;
        opacity: .9;
        font-size: .95rem;
    }
    .week-count {
        background: rgba(255, 255, 255, .2);
        padding: .5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: .9rem;
    }
    .filter-panel {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        margin-bottom: 2rem;
    }
    .timetable-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .timetable-card-header {
        background: linear-gradient(135deg, var(--tt-primary) 0%, var(--tt-primary-dark) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .timetable-card-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    .timetable-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: .85rem;
        opacity: .9;
    }
    .timetable-meta span {
        display: flex;
        align-items: center;
        gap: .25rem;
    }
    .download-btn {
        background: rgba(255, 255, 255, .2);
        border: 1px solid rgba(255, 255, 255, .3);
        color: white;
        padding: .5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: .875rem;
        transition: all .2s ease;
    }
    .download-btn:hover {
        background: rgba(255, 255, 255, .3);
        transform: translateY(-1px);
    }
    .timetable-card-body {
        padding: 0;
    }
    .timetable-table-wrap {
        overflow-x: auto;
    }
    .timetable-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }
    .timetable-table thead th {
        background: var(--tt-primary);
        color: white;
        font-weight: 600;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid var(--tt-primary-dark);
        font-size: .875rem;
    }
    .timetable-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--tt-line);
        vertical-align: middle;
        font-size: .9rem;
    }
    .timetable-table tbody tr:hover {
        background: #f8fafc;
    }
    .time-cell {
        font-weight: 700;
        background: #f0fdf4;
        min-width: 120px;
    }
    .shift-line {
        display: block;
        margin-bottom: .25rem;
    }
    .shift-line:last-child {
        margin-bottom: 0;
    }
    .course-badges {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
    }
    .notes-cell {
        max-width: 200px;
        font-size: .85rem;
        color: var(--tt-muted);
    }
    @media (max-width: 768px) {
        .timetable-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .timetable-table {
            font-size: .8rem;
        }
        .timetable-table thead th,
        .timetable-table tbody td {
            padding: .75rem .5rem;
        }
    }
</style>
@endpush

@section('content')
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
                                @php
                                    $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
                                @endphp
                                @foreach($days as $dayNum => $dayName)
                                    @php
                                        $daySessions = $timetable->sessions->where('day_of_week', $dayNum)->sortBy('start_time');
                                    @endphp
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
            <title>Timetable</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #667eea; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h2>Timetable</h2>
            ${tableClone.outerHTML}
        </body>
        </html>
    `;

    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'timetable.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection
