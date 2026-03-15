@extends('admin.layout')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── PAGE HEADER ── --}}
    <div class="rc-page-header mb-4">
        <div class="rc-page-header__left">
            <div class="rc-page-header__icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <h1 class="rc-page-header__title">Report Cards</h1>
                <p class="rc-page-header__sub">Generate and manage student academic report cards</p>
            </div>
        </div>
        <a href="{{ route('admin.report-cards.create') }}" class="rc-btn rc-btn--primary">
            <i class="fas fa-plus"></i> Generate Report Card
        </a>
    </div>

    {{-- ── QUICK GENERATION ── --}}
    <div class="rc-card mb-4">
        <div class="rc-card__header rc-card__header--blue">
            <i class="fas fa-users me-2"></i> Quick Class Generation
        </div>
        <div class="rc-card__body">
            <form method="POST" action="{{ route('admin.report-cards.generate-class') }}">
                @csrf
                <div class="rc-gen-grid">
                    <div class="rc-field">
                        <label class="rc-label">Course</label>
                        <select name="course_id" class="rc-select" required>
                            <option value="">Select Course</option>
                            @foreach(\App\Models\Course::all() as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rc-field">
                        <label class="rc-label">Academic Year</label>
                        <select name="academic_year_id" class="rc-select" required>
                            <option value="">Select Year</option>
                            @foreach(\App\Models\AcademicYear::all() as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rc-field">
                        <label class="rc-label">Term</label>
                        <select name="term_id" class="rc-select" required>
                            <option value="">Select Term</option>
                            @foreach(\App\Models\Term::all() as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rc-field rc-field--action">
                        <label class="rc-label">&nbsp;</label>
                        <button type="submit" class="rc-btn rc-btn--teal w-100">
                            <i class="fas fa-bolt me-1"></i> Generate for Class
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="rc-card">
        <div class="rc-card__header rc-card__header--green">
            <i class="fas fa-table me-2"></i> Generated Report Cards
            <span class="rc-card__header-count">{{ $reportCards->total() }} records</span>
        </div>
        <div class="rc-card__body p-0">
            @if($reportCards->isEmpty())
                <div class="rc-empty">
                    <div class="rc-empty__icon"><i class="fas fa-id-card"></i></div>
                    <div class="rc-empty__title">No Report Cards Found</div>
                    <div class="rc-empty__sub">Generate report cards for students to see them here</div>
                </div>
            @else
                <div class="rc-table-wrap">
                    <table class="rc-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-1 text-primary"></i> Student</th>
                                <th><i class="fas fa-book me-1 text-info"></i> Course</th>
                                <th class="text-center"><i class="fas fa-calendar me-1 text-secondary"></i> Year / Term</th>
                                <th class="text-center"><i class="fas fa-percentage me-1 text-primary"></i> Average</th>
                                <th class="text-center"><i class="fas fa-medal me-1 text-warning"></i> Grade</th>
                                <th class="text-center"><i class="fas fa-trophy me-1 text-info"></i> Position</th>
                                <th class="text-center"><i class="fas fa-circle me-1 text-secondary"></i> Status</th>
                                <th class="text-center"><i class="fas fa-cogs me-1 text-dark"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportCards as $reportCard)
                            <tr class="rc-table__row">
                                {{-- Student --}}
                                <td>
                                    <div class="rc-student-cell">
                                        <div class="rc-avatar @if(!$reportCard->student->passport_photo) rc-avatar--blue @endif">
                                            @if($reportCard->student->passport_photo)
                                                @if(str_contains($reportCard->student->passport_photo, 'http'))
                                                    <img src="{{ $reportCard->student->passport_photo }}" alt="{{ $reportCard->student->full_name }}">
                                                @else
                                                    <img src="{{ asset($reportCard->student->passport_photo) }}" alt="{{ $reportCard->student->full_name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div style="display: none; width: 34px; height: 34px; background: linear-gradient(135deg,#0d6efd,#0a58ca); border-radius: 50%; align-items: center; justify-content: center;">
                                                        <div style="color: #fff; font-size: 0.8rem; font-weight: 700;">{{ strtoupper(substr($reportCard->student->full_name, 0, 1)) }}</div>
                                                    </div>
                                                @endif
                                            @else
                                                {{ strtoupper(substr($reportCard->student->full_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="rc-student-cell__info">
                                            <span class="rc-student-cell__name">{{ $reportCard->student->full_name }}</span>
                                            <span class="rc-student-cell__id">ID: {{ $reportCard->student->id }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Course --}}
                                <td>
                                    <div class="rc-course-cell">
                                        <i class="fas fa-graduation-cap text-info me-1"></i>
                                        <span title="{{ $reportCard->course->course_name }}">
                                            {{ Str::limit($reportCard->course->course_name, 22) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Year / Term --}}
                                <td class="text-center">
                                    <div class="rc-meta">
                                        <span class="rc-meta__year">{{ $reportCard->academicYear->year ?? '—' }}</span>
                                        <span class="rc-meta__term">Term {{ $reportCard->term->term ?? '—' }}</span>
                                    </div>
                                </td>

                                {{-- Average --}}
                                <td class="text-center">
                                    @php $avg = $reportCard->average_marks; @endphp
                                    <span class="rc-avg {{ $avg >= 70 ? 'rc-avg--high' : ($avg >= 50 ? 'rc-avg--mid' : 'rc-avg--low') }}">
                                        {{ number_format($avg, 1) }}%
                                    </span>
                                </td>

                                {{-- Grade --}}
                                <td class="text-center">
                                    @php
                                        $g = $reportCard->grade;
                                        $gc = match(true) {
                                            in_array($g, ['A+','A']) => 'rc-badge--green',
                                            in_array($g, ['B+','B']) => 'rc-badge--blue',
                                            $g === 'C'              => 'rc-badge--yellow',
                                            $g === 'D'              => 'rc-badge--gray',
                                            default                 => 'rc-badge--red',
                                        };
                                    @endphp
                                    <span class="rc-badge {{ $gc }}">{{ $g }}</span>
                                </td>

                                {{-- Position --}}
                                <td class="text-center">
                                    @if($reportCard->position_display !== 'N/A')
                                        <span class="rc-position">{{ $reportCard->position_display }}</span>
                                    @else
                                        <span class="rc-dash">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center">
                                    @php
                                        $sc = match($reportCard->status) {
                                            'approved' => 'rc-badge--green',
                                            'printed'  => 'rc-badge--blue',
                                            default    => 'rc-badge--yellow',
                                        };
                                    @endphp
                                    <span class="rc-badge {{ $sc }}">{{ ucfirst($reportCard->status) }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    <div class="rc-actions">
                                        <a href="{{ route('admin.report-cards.show', $reportCard) }}"
                                           class="rc-action-btn rc-action-btn--view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.report-cards.print', $reportCard) }}"
                                           class="rc-action-btn rc-action-btn--print" target="_blank" title="Print PDF">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if($reportCard->status !== 'approved')
                                            <a href="{{ route('admin.report-cards.edit', $reportCard) }}"
                                               class="rc-action-btn rc-action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.report-cards.destroy', $reportCard) }}" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rc-action-btn rc-action-btn--delete" title="Delete"
                                                    onclick="return confirm('Delete this report card?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($reportCards->hasPages())
                    <div class="rc-pagination">
                        {{ $reportCards->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
/* ════════════════════════════════════════════
   REPORT CARDS — scoped component styles
   (all classes prefixed with rc- to avoid
    collisions with the admin layout)
════════════════════════════════════════════ */

/* PAGE HEADER */
.rc-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.rc-page-header__left {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.rc-page-header__icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #198754 0%, #0f5132 100%);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(25,135,84,.3);
}
.rc-page-header__title {
    font-size: 1.5rem; font-weight: 700;
    color: #1a1a2e; margin: 0;
}
.rc-page-header__sub {
    font-size: 0.82rem; color: #6c757d; margin: 0;
}

/* CARD */
.rc-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    overflow: hidden;
    border: 1px solid #e9ecef;
}
.rc-card__header {
    display: flex;
    align-items: center;
    padding: 0.85rem 1.25rem;
    font-weight: 600;
    font-size: 0.92rem;
    color: #fff;
    gap: 0.4rem;
}
.rc-card__header--blue  { background: linear-gradient(135deg,#0d6efd,#0a58ca); }
.rc-card__header--green { background: linear-gradient(135deg,#198754,#0f5132); }
.rc-card__header-count {
    margin-left: auto;
    background: rgba(255,255,255,.2);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 0.75rem;
    font-weight: 500;
}
.rc-card__body { padding: 1.25rem; }
.rc-card__body.p-0 { padding: 0; }

/* GENERATION FORM */
.rc-gen-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}
@media (max-width: 992px) {
    .rc-gen-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 576px) {
    .rc-gen-grid { grid-template-columns: 1fr; }
}
.rc-field { display: flex; flex-direction: column; gap: 4px; }
.rc-label {
    font-size: 0.78rem; font-weight: 600;
    color: #495057; letter-spacing: .3px;
}
.rc-select {
    border: 1.5px solid #dee2e6;
    border-radius: 8px;
    padding: 0.45rem 0.75rem;
    font-size: 0.875rem;
    color: #212529;
    background: #fff;
    outline: none;
    transition: border-color .2s;
    appearance: auto;
}
.rc-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.12); }

/* BUTTONS */
.rc-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 0.5rem 1.1rem;
    border-radius: 8px;
    font-size: 0.875rem; font-weight: 600;
    border: none; cursor: pointer;
    text-decoration: none;
    transition: all .2s;
    white-space: nowrap;
}
.rc-btn--primary {
    background: linear-gradient(135deg,#0d6efd,#0a58ca);
    color: #fff;
    box-shadow: 0 3px 10px rgba(13,110,253,.3);
}
.rc-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(13,110,253,.4); color:#fff; }
.rc-btn--teal {
    background: linear-gradient(135deg,#0dcaf0,#098fac);
    color: #fff;
    box-shadow: 0 3px 10px rgba(13,202,240,.3);
}
.rc-btn--teal:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(13,202,240,.4); color:#fff; }

/* TABLE */
.rc-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.rc-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.845rem;
    color: #212529;
}
.rc-table thead tr {
    background: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
}
.rc-table thead th {
    padding: 0.7rem 0.9rem;
    font-weight: 700;
    font-size: 0.775rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #495057;
    white-space: nowrap;
    border-bottom: 2px solid #dee2e6;
}
.rc-table tbody td {
    padding: 0.65rem 0.9rem;
    border-bottom: 1px solid #f1f3f5;
    vertical-align: middle;
}
.rc-table__row {
    transition: background .15s, box-shadow .15s;
}
.rc-table__row:hover {
    background: #f0f7ff;
}
.rc-table__row:hover td { border-color: #d0e4ff; }
.rc-table__row:last-child td { border-bottom: none; }

/* STUDENT CELL */
.rc-student-cell {
    display: flex; align-items: center; gap: 10px;
    min-width: 0;
}
.rc-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.8rem; color: #fff;
    flex-shrink: 0;
    overflow: hidden;
    background: linear-gradient(135deg,#0d6efd,#0a58ca);
}
.rc-avatar img {
    width: 100%; height: 100%; object-fit: cover;
    border-radius: 50%;
}
.rc-avatar--blue { background: linear-gradient(135deg,#0d6efd,#0a58ca); }
.rc-student-cell__info {
    display: flex; flex-direction: column;
    min-width: 0;
}
.rc-student-cell__name {
    font-weight: 600; font-size: 0.855rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 180px;
}
.rc-student-cell__id { font-size: 0.72rem; color: #6c757d; }

/* COURSE CELL */
.rc-course-cell {
    display: flex; align-items: center; gap: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 200px; font-size: 0.845rem;
}

/* YEAR/TERM META */
.rc-meta {
    display: flex; flex-direction: column; align-items: center; gap: 1px;
}
.rc-meta__year { font-weight: 600; font-size: 0.82rem; }
.rc-meta__term { font-size: 0.7rem; color: #6c757d; }

/* AVERAGE */
.rc-avg {
    font-weight: 700; font-size: 0.9rem;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-block;
}
.rc-avg--high  { color: #0f5132; background: #d1e7dd; }
.rc-avg--mid   { color: #664d03; background: #fff3cd; }
.rc-avg--low   { color: #842029; background: #f8d7da; }

/* BADGES */
.rc-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: .3px;
    white-space: nowrap;
}
.rc-badge--green  { background: #d1e7dd; color: #0f5132; }
.rc-badge--blue   { background: #cfe2ff; color: #084298; }
.rc-badge--yellow { background: #fff3cd; color: #664d03; }
.rc-badge--gray   { background: #e2e3e5; color: #41464b; }
.rc-badge--red    { background: #f8d7da; color: #842029; }

/* POSITION */
.rc-position {
    display: inline-block;
    font-weight: 700; font-size: 0.82rem;
    background: #e9ecef; color: #495057;
    padding: 3px 10px; border-radius: 20px;
}
.rc-dash { color: #adb5bd; font-size: 1rem; }

/* ACTION BUTTONS */
.rc-actions { display: flex; align-items: center; justify-content: center; gap: 5px; flex-wrap: nowrap; }
.rc-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px;
    border-radius: 7px;
    border: 1.5px solid transparent;
    font-size: 0.75rem;
    cursor: pointer;
    text-decoration: none;
    transition: all .18s;
    background: transparent;
}
.rc-action-btn--view   { border-color: #0d6efd; color: #0d6efd; }
.rc-action-btn--view:hover  { background: #0d6efd; color: #fff; }
.rc-action-btn--print  { border-color: #198754; color: #198754; }
.rc-action-btn--print:hover { background: #198754; color: #fff; }
.rc-action-btn--edit   { border-color: #ffc107; color: #856404; }
.rc-action-btn--edit:hover  { background: #ffc107; color: #fff; }
.rc-action-btn--delete { border-color: #dc3545; color: #dc3545; }
.rc-action-btn--delete:hover { background: #dc3545; color: #fff; }

/* EMPTY STATE */
.rc-empty {
    padding: 4rem 2rem;
    text-align: center;
    color: #6c757d;
}
.rc-empty__icon { font-size: 3rem; margin-bottom: 1rem; opacity: .35; }
.rc-empty__title { font-size: 1.1rem; font-weight: 600; margin-bottom: .4rem; }
.rc-empty__sub { font-size: 0.875rem; }

/* PAGINATION */
.rc-pagination {
    padding: 1rem 1.25rem;
    display: flex; justify-content: center;
    border-top: 1px solid #f1f3f5;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
    .rc-student-cell__name { max-width: 140px; }
    .rc-course-cell { max-width: 160px; }
}
@media (max-width: 768px) {
    .rc-table { font-size: 0.78rem; }
    .rc-table thead th { font-size: 0.7rem; padding: 0.55rem 0.6rem; }
    .rc-table tbody td { padding: 0.55rem 0.6rem; }
    .rc-avatar { width: 28px; height: 28px; font-size: 0.72rem; }
    .rc-action-btn { width: 26px; height: 26px; font-size: 0.68rem; }
    .rc-student-cell__name { max-width: 110px; }
}
</style>
@endsection