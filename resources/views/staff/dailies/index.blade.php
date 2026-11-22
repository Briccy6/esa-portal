@extends('dos.layout')

@section('page_title', 'Teacher daily reports')

@section('content')
@php
    $currentStatus = request('status', '');
@endphp

<div class="daily-page">
    <header class="daily-heading">
        <div>
            <p class="daily-eyebrow"><i class="bi bi-journal-check"></i> Academic operations</p>
            <h1>Teacher daily reports</h1>
            <p class="daily-subtitle">Review what was taught, who taught it, and keep each report’s status up to date.</p>
        </div>
        <div class="daily-heading-count">
            <span class="daily-heading-count-number">{{ number_format($stats->total ?? 0) }}</span>
            <span>reports submitted</span>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success daily-alert" role="status">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger daily-alert" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger daily-alert" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <section class="daily-stats" aria-label="Report status summary">
        <article class="daily-stat">
            <span class="daily-stat-icon stat-all"><i class="bi bi-files"></i></span>
            <div><span class="daily-stat-label">All reports</span><strong>{{ number_format($stats->total ?? 0) }}</strong></div>
        </article>
        <article class="daily-stat">
            <span class="daily-stat-icon stat-pending"><i class="bi bi-hourglass-split"></i></span>
            <div><span class="daily-stat-label">Pending review</span><strong>{{ number_format($stats->pending ?? 0) }}</strong></div>
        </article>
        <article class="daily-stat">
            <span class="daily-stat-icon stat-approved"><i class="bi bi-check2-circle"></i></span>
            <div><span class="daily-stat-label">Approved</span><strong>{{ number_format($stats->approved ?? 0) }}</strong></div>
        </article>
    </section>

    <section class="daily-list-panel">
        <div class="daily-list-header">
            <div>
                <h2>Daily report queue</h2>
                <p>{{ $dailies->total() ? 'Showing ' . $dailies->firstItem() . '–' . $dailies->lastItem() . ' of ' . number_format($dailies->total()) . ' reports' : 'No reports to show' }}</p>
            </div>
            <div class="daily-filters">
                <label class="daily-search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search" id="searchInput" placeholder="Search teacher, course, lesson…" autocomplete="off" aria-label="Search daily reports">
                    <kbd>/</kbd>
                </label>
                <form method="GET" action="{{ route('dos.dailies.index') }}">
                    <label class="visually-hidden" for="statusFilter">Filter by report status</label>
                    <select id="statusFilter" name="status" class="daily-filter-select" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        <option value="pending" @selected($currentStatus === 'pending')>Pending</option>
                        <option value="approved" @selected($currentStatus === 'approved')>Approved</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="table-responsive daily-table-wrap">
            <table class="table daily-table align-middle mb-0" id="dailiesTable">
                <thead>
                    <tr>
                        <th scope="col">Report date</th>
                        <th scope="col">Teacher</th>
                        <th scope="col">Course(s) studied</th>
                        <th scope="col">Lesson</th>
                        <th scope="col">Status &amp; update</th>
                        <th scope="col"><span class="visually-hidden">Report details</span></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($dailies as $daily)
                        @php
                            $reportStatus = $daily->approved ? 'approved' : 'pending';
                            $dailyCourseIds = collect($daily->course_ids ?? [])
                                ->push($daily->course_id)
                                ->filter()
                                ->unique()
                                ->values();
                        @endphp
                        <tr class="daily-row" data-status="{{ $reportStatus }}">
                            <td data-label="Report date">
                                <div class="daily-date">
                                    <span class="daily-date-day">{{ $daily->date ? $daily->date->format('d') : '—' }}</span>
                                    <span class="daily-date-month">{{ $daily->date ? $daily->date->format('M Y') : 'Date unavailable' }}</span>
                                </div>
                                @if($daily->date)
                                    <span class="daily-date-weekday">{{ $daily->date->format('l') }}</span>
                                @endif
                            </td>
                            <td data-label="Teacher">
                                <div class="daily-teacher">
                                    <span class="daily-avatar">{{ strtoupper(substr($daily->teacher->teacher_name ?? 'NA', 0, 2)) }}</span>
                                    <span>
                                        <strong>{{ $daily->teacher->teacher_name ?? 'Teacher unavailable' }}</strong>
                                        <small>Teacher ID {{ $daily->teacher_id ?? '—' }}</small>
                                    </span>
                                </div>
                            </td>
                            <td data-label="Course(s) studied">
                                <div class="daily-courses">
                                    @forelse($dailyCourseIds as $courseId)
                                        <span class="course-chip">{{ $courseNamesById->get($courseId, 'Course #' . $courseId) }}</span>
                                    @empty
                                        <span class="daily-muted">No course recorded</span>
                                    @endforelse
                                </div>
                                @if($dailyCourseIds->count() > 1)
                                    <small class="daily-secondary">{{ $dailyCourseIds->count() }} courses in this session</small>
                                @endif
                            </td>
                            <td data-label="Lesson">
                                <strong class="daily-lesson">{{ $daily->lesson->title ?? 'Lesson unavailable' }}</strong>
                                @if($daily->unit)
                                    <small class="daily-secondary">{{ $daily->unit->unit_name }}</small>
                                @elseif($daily->hours_studied)
                                    <small class="daily-secondary">{{ $daily->hours_studied }} {{ (float) $daily->hours_studied === 1.0 ? 'hour' : 'hours' }} studied</small>
                                @endif
                            </td>
                            <td data-label="Status & update">
                                <div class="daily-status-cell">
                                    <span class="status-pill status-{{ $reportStatus }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($reportStatus) }}
                                    </span>
                                    <form method="POST" action="{{ route('dos.dailies.status', $daily->id) }}" class="daily-status-form">
                                        @csrf
                                        <label class="visually-hidden" for="status-{{ $daily->id }}">Change report status</label>
                                        <select id="status-{{ $daily->id }}" name="status" class="daily-status-select">
                                            <option value="pending" @selected($reportStatus === 'pending')>Pending</option>
                                            <option value="approved" @selected($reportStatus === 'approved')>Approved</option>
                                        </select>
                                        <button type="submit" class="daily-save-button" aria-label="Save status for report {{ $daily->id }}" title="Save status">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td data-label="Report details" class="daily-action-cell">
                                <div class="daily-action-actions">
                                    <a href="{{ route('dos.dailies.show', $daily->id) }}" class="daily-view-link">
                                        Review <i class="bi bi-arrow-up-right"></i>
                                    </a>
                                    <form method="POST" action="{{ route('dos.dailies.destroy', $daily->id) }}"
                                        onsubmit="return confirm('Permanently delete this daily report, its teacher payment record, and attendance entries for its lesson and courses at this branch? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="daily-delete-button" aria-label="Delete daily report {{ $daily->id }}" title="Delete report and related attendance/payment">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="daily-empty">
                                <span class="daily-empty-icon"><i class="bi bi-journal-x"></i></span>
                                <strong>No daily reports found</strong>
                                <span>Try another status filter or check back after teachers submit their reports.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="noResults" class="daily-empty d-none">
                <span class="daily-empty-icon"><i class="bi bi-search"></i></span>
                <strong>No matching reports</strong>
                <span>Try a different teacher, course, or lesson search.</span>
            </div>
        </div>

        @if($dailies->hasPages())
            <footer class="daily-pagination">{{ $dailies->links() }}</footer>
        @endif
    </section>
</div>

<style>
    .daily-page {
        --daily-ink: #172b3a;
        --daily-muted: #71808e;
        --daily-line: #e7edf0;
        --daily-paper: #fff;
        --daily-accent: #1c776b;
        max-width: 1540px;
        margin: 0 auto;
        padding: 34px clamp(18px, 3vw, 42px) 48px;
        color: var(--daily-ink);
    }

    .daily-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 28px;
    }

    .daily-eyebrow {
        margin: 0 0 9px;
        color: var(--daily-accent);
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .daily-eyebrow i { margin-right: 7px; }

    .daily-heading h1 {
        margin: 0;
        color: var(--daily-ink);
        font-size: clamp(1.7rem, 3vw, 2.35rem);
        font-weight: 750;
        letter-spacing: -.045em;
    }

    .daily-subtitle {
        max-width: 640px;
        margin: 8px 0 0;
        color: var(--daily-muted);
        font-size: .95rem;
    }

    .daily-heading-count {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        color: var(--daily-muted);
        font-size: .78rem;
    }

    .daily-heading-count-number {
        color: var(--daily-ink);
        font-size: 1.5rem;
        font-weight: 750;
        line-height: 1.1;
    }

    .daily-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 0;
        border-radius: 12px;
        box-shadow: 0 5px 16px rgba(23, 43, 58, .05);
    }

    .daily-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .daily-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 94px;
        padding: 18px 20px;
        border: 1px solid var(--daily-line);
        border-radius: 14px;
        background: var(--daily-paper);
        box-shadow: 0 5px 18px rgba(23, 43, 58, .035);
    }

    .daily-stat-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-radius: 12px;
        font-size: 1.15rem;
    }

    .stat-all { color: #536d80; background: #edf2f5; }
    .stat-pending { color: #a56a13; background: #fff4dc; }
    .stat-approved { color: #147361; background: #e5f5ef; }

    .daily-stat-label {
        display: block;
        margin-bottom: 3px;
        color: var(--daily-muted);
        font-size: .76rem;
        font-weight: 600;
    }

    .daily-stat strong {
        color: var(--daily-ink);
        font-size: 1.32rem;
        font-weight: 750;
        line-height: 1.15;
    }

    .daily-list-panel {
        overflow: hidden;
        border: 1px solid var(--daily-line);
        border-radius: 16px;
        background: var(--daily-paper);
        box-shadow: 0 8px 26px rgba(23, 43, 58, .045);
    }

    .daily-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 22px 24px;
        border-bottom: 1px solid var(--daily-line);
    }

    .daily-list-header h2 {
        margin: 0;
        color: var(--daily-ink);
        font-size: 1.08rem;
        font-weight: 750;
        letter-spacing: -.02em;
    }

    .daily-list-header p {
        margin: 4px 0 0;
        color: var(--daily-muted);
        font-size: .8rem;
    }

    .daily-filters {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .daily-search {
        display: flex;
        width: min(320px, 32vw);
        min-width: 210px;
        align-items: center;
        gap: 9px;
        padding: 0 11px;
        border: 1px solid var(--daily-line);
        border-radius: 9px;
        background: #fbfcfc;
        color: #8997a2;
    }

    .daily-search:focus-within {
        border-color: #9dc9bf;
        box-shadow: 0 0 0 3px rgba(28, 119, 107, .09);
    }

    .daily-search input {
        width: 100%;
        height: 39px;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--daily-ink);
        font: inherit;
        font-size: .8rem;
    }

    .daily-search input::placeholder { color: #95a1aa; }

    .daily-search kbd {
        padding: 1px 5px;
        border: 1px solid #e4e9ec;
        border-radius: 4px;
        background: #fff;
        color: #8a969f;
        font-size: .68rem;
    }

    .daily-filter-select,
    .daily-status-select {
        min-height: 39px;
        padding: 0 30px 0 11px;
        border: 1px solid var(--daily-line);
        border-radius: 9px;
        background-color: #fff;
        color: #445563;
        font-size: .78rem;
        font-weight: 600;
    }

    .daily-table { min-width: 1040px; }

    .daily-table thead th {
        padding: 12px 18px;
        border-bottom: 1px solid var(--daily-line);
        background: #f7f9f9;
        color: #71808e;
        font-size: .67rem;
        font-weight: 750;
        letter-spacing: .075em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .daily-table tbody td {
        padding: 17px 18px;
        border-color: #edf1f3;
        color: var(--daily-ink);
        vertical-align: middle;
    }

    .daily-row { transition: background .16s ease; }
    .daily-row:hover { background: #f9fbfa; }

    .daily-date {
        display: flex;
        align-items: baseline;
        gap: 7px;
        white-space: nowrap;
    }

    .daily-date-day {
        color: var(--daily-ink);
        font-size: 1.1rem;
        font-weight: 800;
    }

    .daily-date-month { color: #526574; font-size: .78rem; font-weight: 650; }
    .daily-date-weekday { display: block; margin-top: 2px; color: #95a1aa; font-size: .72rem; }

    .daily-teacher {
        display: flex;
        min-width: 160px;
        align-items: center;
        gap: 10px;
    }

    .daily-avatar {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border: 1px solid #d5e9e3;
        border-radius: 50%;
        background: #edf7f3;
        color: #247766;
        font-size: .68rem;
        font-weight: 800;
    }

    .daily-teacher strong,
    .daily-teacher small,
    .daily-secondary { display: block; }

    .daily-teacher strong { font-size: .82rem; font-weight: 700; }
    .daily-teacher small,
    .daily-secondary { margin-top: 3px; color: #8997a2; font-size: .69rem; }

    .daily-courses {
        display: flex;
        max-width: 310px;
        flex-wrap: wrap;
        gap: 5px;
    }

    .course-chip {
        display: inline-flex;
        padding: 4px 8px;
        border: 1px solid #e0ebe7;
        border-radius: 6px;
        background: #f1f7f4;
        color: #376759;
        font-size: .7rem;
        font-weight: 650;
        line-height: 1.3;
    }

    .daily-muted { color: #8997a2; font-size: .78rem; }
    .daily-lesson { display: block; max-width: 210px; font-size: .81rem; font-weight: 700; }

    .daily-status-cell { display: flex; min-width: 174px; flex-direction: column; align-items: flex-start; gap: 8px; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .status-pending { background: #fff5df; color: #9a671b; }
    .status-approved { background: #e8f6f0; color: #187461; }
    .daily-status-form { display: flex; align-items: center; gap: 5px; }
    .daily-status-select { min-height: 32px; padding-top: 0; padding-bottom: 0; font-size: .7rem; }

    .daily-save-button {
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;
        border: 1px solid #d9e8e3;
        border-radius: 8px;
        background: #f0f7f4;
        color: #247766;
        transition: background .15s ease, color .15s ease, transform .15s ease;
    }

    .daily-save-button:hover { transform: translateY(-1px); background: var(--daily-accent); color: #fff; }
    .daily-action-cell { text-align: right; white-space: nowrap; }
    .daily-action-actions { display: inline-flex; align-items: center; justify-content: flex-end; gap: 12px; }
    .daily-delete-button {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #f0d7d4;
        border-radius: 9px;
        background: #fff7f6;
        color: #b54740;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .daily-delete-button:hover { border-color: #b54740; background: #b54740; color: #fff; }
    .daily-delete-button:focus-visible { outline: 3px solid rgba(181, 71, 64, .22); outline-offset: 2px; }

    .daily-view-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--daily-accent);
        font-size: .76rem;
        font-weight: 750;
        text-decoration: none;
    }

    .daily-view-link:hover { color: #14594f; text-decoration: underline; }

    .daily-empty {
        display: flex;
        min-height: 220px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 30px;
        color: var(--daily-muted);
        text-align: center;
    }

    .daily-empty strong { color: var(--daily-ink); font-size: .95rem; }
    .daily-empty span:last-child { font-size: .78rem; }

    .daily-empty-icon {
        display: grid;
        width: 48px;
        height: 48px;
        margin-bottom: 5px;
        place-items: center;
        border-radius: 14px;
        background: #f1f5f5;
        color: #78908f;
        font-size: 1.25rem;
    }

    .daily-pagination { padding: 17px 22px; border-top: 1px solid var(--daily-line); }
    .daily-pagination nav { display: flex; justify-content: center; }
    .daily-pagination p { margin: 0; color: var(--daily-muted); font-size: .78rem; }
    .daily-pagination .pagination { margin: 0; }

    @media (max-width: 1050px) {
        .daily-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .daily-list-header { align-items: flex-start; flex-direction: column; }
        .daily-filters { width: 100%; }
        .daily-search { width: 100%; flex: 1; }
    }

    @media (max-width: 600px) {
        .daily-page { padding: 24px 12px 36px; }
        .daily-heading { align-items: flex-start; flex-direction: column; margin-bottom: 20px; }
        .daily-heading-count { align-items: flex-start; }
        .daily-stats { gap: 9px; }
        .daily-stat { min-height: 80px; gap: 10px; padding: 12px; }
        .daily-stat-icon { width: 35px; height: 35px; flex-basis: 35px; font-size: 1rem; }
        .daily-stat-label { font-size: .68rem; }
        .daily-stat strong { font-size: 1.12rem; }
        .daily-list-header { padding: 18px 15px; }
        .daily-filters { align-items: stretch; flex-direction: column; }
        .daily-search { min-width: 0; }
        .daily-filter-select { width: 100%; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const noResults = document.getElementById('noResults');
    const rows = tableBody.querySelectorAll('.daily-row');
    const searchShortcut = document.querySelector('.daily-search kbd');

    function filterRows() {
        const query = searchInput.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const matches = row.textContent.toLocaleLowerCase().includes(query);
            row.classList.toggle('d-none', !matches);
            if (matches) visibleCount++;
        });

        noResults.classList.toggle('d-none', visibleCount > 0 || rows.length === 0);
    }

    searchInput.addEventListener('input', filterRows);
    document.addEventListener('keydown', function (event) {
        if (event.key === '/' && !event.target.matches('input, textarea, select')) {
            event.preventDefault();
            searchInput.focus();
        }
        if (event.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            filterRows();
            searchInput.blur();
        }
    });

    if (searchShortcut) searchShortcut.setAttribute('aria-hidden', 'true');
});
</script>
@endsection
