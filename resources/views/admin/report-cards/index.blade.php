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
        <div class="d-flex gap-2">
            <a href="{{ route('admin.report-cards.outstanding-fees') }}" class="rc-btn rc-btn--warning">
                <i class="fas fa-exclamation-triangle"></i> Outstanding Fees
            </a>
            <a href="{{ route('admin.report-cards.create') }}" class="rc-btn rc-btn--primary">
                <i class="fas fa-plus"></i> Generate Report Card
            </a>
        </div>
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
                    <div class="rc-field rc-field--action">
                        <label class="rc-label">&nbsp;</label>
                        <div class="d-flex flex-column gap-2 w-100">
                            <button type="submit" class="rc-btn rc-btn--teal w-100">
                                <i class="fas fa-bolt me-1"></i> Generate for Class
                            </button>
                            <button type="button" class="rc-btn rc-btn--primary w-100" id="btnDownloadClassReportCards" title="PDF of all approved report cards for the selected class, year, and term(s)">
                                <i class="fas fa-download me-1"></i> Download class report cards (PDF)
                            </button>
                        </div>
                    </div>

                    {{-- ── CUSTOM MULTI-SELECT TERMS ── --}}
                    <div class="rc-field">
                        <label class="rc-label">Term(s) — one report card per student per term</label>

                        {{-- Hidden native select that actually submits --}}
                        <select name="term_ids[]" id="termNativeSelect" multiple hidden required>
                            @forelse($terms->groupBy('academic_year_id') as $yearId => $yearTerms)
                                @foreach($yearTerms as $term)
                                    <option value="{{ $term->id }}">{{ $term->name ?: 'Term #'.$term->id }}</option>
                                @endforeach
                            @empty
                            @endforelse
                        </select>

                        {{-- Custom styled dropdown --}}
                        <div class="ms-dropdown" id="termDropdown">
                            <button type="button" class="ms-trigger" id="termTrigger" aria-haspopup="listbox" aria-expanded="false">
                                <span class="ms-trigger__label" id="termTriggerLabel">
                                    <span class="ms-placeholder">Select terms…</span>
                                </span>
                                <span class="ms-trigger__icons">
                                    <span class="ms-clear-all" id="termClearAll" title="Clear all" style="display:none">
                                        <i class="fas fa-times-circle"></i>
                                    </span>
                                    <i class="fas fa-chevron-down ms-chevron" id="termChevron"></i>
                                </span>
                            </button>

                            <div class="ms-panel" id="termPanel" role="listbox" aria-multiselectable="true" style="display:none">
                                {{-- Search --}}
                                <div class="ms-search-wrap">
                                    <i class="fas fa-search ms-search-icon"></i>
                                    <input type="text" class="ms-search" id="termSearch" placeholder="Search terms…" autocomplete="off">
                                </div>

                                {{-- Select All / Clear All row --}}
                                <div class="ms-bulk-row">
                                    <button type="button" class="ms-bulk-btn" id="termSelectAll">
                                        <i class="fas fa-check-double me-1"></i> Select All
                                    </button>
                                    <button type="button" class="ms-bulk-btn ms-bulk-btn--danger" id="termDeselectAll">
                                        <i class="fas fa-ban me-1"></i> Clear All
                                    </button>
                                    <span class="ms-count-badge" id="termCountBadge">0 selected</span>
                                </div>

                                {{-- Option list --}}
                                <ul class="ms-list" id="termList" role="listbox">
                                    @forelse($terms->groupBy('academic_year_id') as $yearId => $yearTerms)
                                        @php
                                            $ogLabel = optional($yearTerms->first()->academicYear)->year ?? ('Year #'.$yearId);
                                        @endphp
                                        <li class="ms-group-label" data-group="{{ $yearId }}">
                                            <i class="fas fa-layer-group me-1"></i> {{ $ogLabel }}
                                        </li>
                                        @foreach($yearTerms as $term)
                                            <li class="ms-option"
                                                role="option"
                                                aria-selected="false"
                                                data-value="{{ $term->id }}"
                                                data-label="{{ $term->name ?: 'Term #'.$term->id }}"
                                                data-group="{{ $yearId }}"
                                                data-search="{{ strtolower($term->name ?: 'Term #'.$term->id) }} {{ strtolower($ogLabel) }}">
                                                <span class="ms-checkbox">
                                                    <i class="fas fa-check ms-check-icon"></i>
                                                </span>
                                                <span class="ms-option__text">{{ $term->name ?: 'Term #'.$term->id }}</span>
                                            </li>
                                        @endforeach
                                    @empty
                                        <li class="ms-empty">No terms in database — add terms first</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        {{-- Selected pills (visible below the dropdown) --}}
                        <div class="ms-pills" id="termPills"></div>

                        <span class="rc-hint">💡 Click items to select/deselect. Use <strong>Select All</strong> or search to filter.</span>
                        <span class="rc-hint d-block mt-1"><strong>Download class PDF</strong> builds one file with every <em>approved</em> student report for the course, year, and term(s) you select.</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="rc-card">
        <div class="rc-card__header rc-card__header--green">
            <i class="fas fa-table me-2"></i> Generated Report Cards
            <span class="rc-card__header-count">{{ $reportCardGroups->total() }} {{ Str::plural('student', $reportCardGroups->total()) }}</span>
        </div>
        <div class="rc-card__body p-0">
            @if($reportCardGroups->isEmpty())
                <div class="rc-empty">
                    <div class="rc-empty__icon"><i class="fas fa-id-card"></i></div>
                    <div class="rc-empty__title">No Report Cards Found</div>
                    <div class="rc-empty__sub">Generate report cards for students to see them here</div>
                </div>
            @else
                {{-- ── DELETE OPTIONS SECTION ── --}}
                <div class="p-3 border-bottom bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center">
                                <strong><i class="fas fa-trash me-2 text-danger"></i>Delete Options:</strong>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteByClass()">
                                    <i class="fas fa-trash me-1"></i> Delete Class
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteAll()" title="Delete all generated report cards">
                                    <i class="fas fa-trash-alt me-1"></i> Delete All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.report-cards.bulk-approve') }}" id="bulkApproveForm">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <label class="mb-0 d-flex align-items-center gap-2">
                                <input type="checkbox" id="selectAllReportCards">
                                <span>Select all on this page</span>
                            </label>
                            <span class="text-muted"><strong id="selectedReportsCount">0</strong> selected</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="rc-btn rc-btn--teal" onclick="return confirm('Approve selected report cards?')">
                                <i class="fas fa-check-circle"></i> Approve Selected
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedReports()" style="display:none;" id="deleteSelectedBtn">
                                <i class="fas fa-trash me-1"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                <div class="rc-table-wrap">
                    <table class="rc-table">
                        <thead>
                            <tr>
                                <th class="text-center"><i class="fas fa-check-square me-1 text-success"></i> Select</th>
                                <th><i class="fas fa-user me-1 text-primary"></i> Student</th>
                                <th><i class="fas fa-book me-1 text-info"></i> Course</th>
                                <th class="text-center"><i class="fas fa-calendar me-1 text-secondary"></i> Year / terms</th>
                                <th class="text-center"><i class="fas fa-percentage me-1 text-primary"></i> Average</th>
                                <th class="text-center"><i class="fas fa-medal me-1 text-warning"></i> Grade</th>
                                <th class="text-center"><i class="fas fa-trophy me-1 text-info"></i> Position</th>
                                <th class="text-center"><i class="fas fa-circle me-1 text-secondary"></i> Status</th>
                                <th class="text-center"><i class="fas fa-cogs me-1 text-dark"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportCardGroups as $group)
                            @php
                                $first = $group->first();
                                $overallAvg = (float) $group->avg('average_marks');
                                $overallGrade = match (true) {
                                    $overallAvg >= 90 => 'A+',
                                    $overallAvg >= 80 => 'A',
                                    $overallAvg >= 70 => 'B+',
                                    $overallAvg >= 60 => 'B',
                                    $overallAvg >= 50 => 'C',
                                    $overallAvg >= 40 => 'D',
                                    default => 'F',
                                };
                                $ogc = match (true) {
                                    in_array($overallGrade, ['A+','A'], true) => 'rc-badge--green',
                                    in_array($overallGrade, ['B+','B'], true) => 'rc-badge--blue',
                                    $overallGrade === 'C' => 'rc-badge--yellow',
                                    $overallGrade === 'D' => 'rc-badge--gray',
                                    default => 'rc-badge--red',
                                };
                            @endphp
                            <tr class="rc-table__row rc-table__row--grouped">
                                <td class="text-center rc-cell-stack">
                                    @foreach($group as $reportCard)
                                        <div class="rc-term-metric rc-term-metric--center">
                                            <label class="mb-0 d-flex align-items-center gap-1">
                                                <input type="checkbox" class="report-card-checkbox" name="report_card_ids[]" value="{{ $reportCard->id }}">
                                                <span class="rc-term-pill">{{ $reportCard->term?->name ?? '—' }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </td>
                                {{-- Student (once per group) --}}
                                <td>
                                    <div class="rc-student-cell">
                                        <div class="rc-avatar @if(!$first->student->passport_photo) rc-avatar--blue @endif">
                                            @if($first->student->passport_photo)
                                                @if(str_contains($first->student->passport_photo, 'http'))
                                                    <img src="{{ $first->student->passport_photo }}" alt="{{ $first->student->full_name }}">
                                                @else
                                                    <img src="{{ asset($first->student->passport_photo) }}" alt="{{ $first->student->full_name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div style="display: none; width: 34px; height: 34px; background: #0d6efd; border-radius: 50%; align-items: center; justify-content: center;">
                                                        <div style="color: #fff; font-size: 0.8rem; font-weight: 700;">{{ strtoupper(substr($first->student->full_name, 0, 1)) }}</div>
                                                    </div>
                                                @endif
                                            @else
                                                {{ strtoupper(substr($first->student->full_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="rc-student-cell__info">
                                            <span class="rc-student-cell__name">{{ $first->student->full_name }}</span>
                                            <span class="rc-student-cell__id">ID: {{ $first->student->id }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Course --}}
                                <td>
                                    <div class="rc-course-cell">
                                        <i class="fas fa-graduation-cap text-info me-1"></i>
                                        <span title="{{ $first->course->course_name }}">
                                            {{ Str::limit($first->course->course_name, 22) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Year + all terms --}}
                                <td class="text-center rc-cell-stack">
                                    <div class="rc-meta rc-meta--block">
                                        <span class="rc-meta__year">{{ $first->academicYear->year ?? '—' }}</span>
                                    </div>
                                    @foreach($group as $reportCard)
                                        <div class="rc-term-pill">{{ $reportCard->term?->name ?? '—' }}</div>
                                    @endforeach
                                </td>

                                {{-- Average: overall + per term --}}
                                <td class="text-center rc-cell-stack">
                                    <span class="rc-avg rc-avg--overall {{ $overallAvg >= 70 ? 'rc-avg--high' : ($overallAvg >= 50 ? 'rc-avg--mid' : 'rc-avg--low') }}">
                                        {{ number_format($overallAvg, 1) }}%
                                    </span>
                                    <span class="rc-stack-label">overall</span>
                                    @foreach($group as $reportCard)
                                        @php $avg = $reportCard->average_marks; @endphp
                                        <div class="rc-term-metric">
                                            <span class="rc-term-metric__t">{{ Str::limit($reportCard->term?->name ?? '—', 8) }}</span>
                                            <span class="rc-avg rc-avg--small {{ $avg >= 70 ? 'rc-avg--high' : ($avg >= 50 ? 'rc-avg--mid' : 'rc-avg--low') }}">{{ number_format($avg, 1) }}%</span>
                                        </div>
                                    @endforeach
                                </td>

                                {{-- Grade: overall + per term --}}
                                <td class="text-center rc-cell-stack">
                                    <span class="rc-badge {{ $ogc }} rc-badge--overall">{{ $overallGrade }}</span>
                                    @foreach($group as $reportCard)
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
                                        <div class="rc-term-metric rc-term-metric--center">
                                            <span class="rc-badge rc-badge--tiny {{ $gc }}">{{ $g }}</span>
                                        </div>
                                    @endforeach
                                </td>

                                {{-- Position per term --}}
                                <td class="text-center rc-cell-stack">
                                    @foreach($group as $reportCard)
                                        <div class="rc-term-metric rc-term-metric--center">
                                            @if($reportCard->position_display !== 'N/A')
                                                <span class="rc-position rc-position--small">{{ $reportCard->position_display }}</span>
                                            @else
                                                <span class="rc-dash">—</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>

                                {{-- Status per term --}}
                                <td class="text-center rc-cell-stack">
                                    @foreach($group as $reportCard)
                                        @php
                                            $sc = match($reportCard->status) {
                                                'approved' => 'rc-badge--green',
                                                'printed'  => 'rc-badge--blue',
                                                default    => 'rc-badge--yellow',
                                            };
                                        @endphp
                                        <div class="rc-term-metric rc-term-metric--center">
                                            <span class="rc-badge rc-badge--tiny {{ $sc }}">{{ ucfirst($reportCard->status) }}</span>
                                        </div>
                                    @endforeach
                                </td>

                                {{-- One Actions control per student (all terms); menu: view + print --}}
                                <td class="text-center rc-table__actions">
                                    @php
                                        $termIdsInGroup = $group->pluck('term_id')->unique()->sort()->values()->all();
                                        $consolidatedQuery = [
                                            'student_id' => $first->student_id,
                                            'course_id' => $first->course_id,
                                            'academic_year_id' => $first->academic_year_id,
                                            'term_ids' => $termIdsInGroup,
                                        ];
                                        $multipleTermsInGroup = count($termIdsInGroup) > 1;
                                    @endphp
                                    <div class="dropdown rc-student-actions">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" data-bs-auto-close="true"
                                                aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            {{-- View/Print Section --}}
                                            @if($multipleTermsInGroup)
                                                <li>
                                                    <a href="{{ route('admin.report-cards.consolidated', $consolidatedQuery) }}" class="dropdown-item" target="_blank">
                                                        <i class="fas fa-eye me-2 text-info"></i> View all ({{ count($termIdsInGroup) }} terms)
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.report-cards.consolidated-print', $consolidatedQuery) }}" class="dropdown-item" target="_blank">
                                                        <i class="fas fa-print me-2 text-success"></i> Print all ({{ count($termIdsInGroup) }} terms)
                                                    </a>
                                                </li>
                                            @else
                                                @php $rc = $group->first(); @endphp
                                                <li>
                                                    <a href="{{ route('admin.report-cards.show', $rc->id) }}" class="dropdown-item" target="_blank">
                                                        <i class="fas fa-eye me-2 text-info"></i> View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.report-cards.print', $rc->id) }}" class="dropdown-item" target="_blank">
                                                        <i class="fas fa-print me-2 text-success"></i> Print
                                                    </a>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            {{-- Delete Section --}}
                                            <li>
                                                @if($multipleTermsInGroup)
                                                    <button class="dropdown-item text-danger" onclick="deleteReportCardsGroup('{{ $first->student_id }}', '{{ $first->course_id }}', '{{ $first->academic_year_id }}', '{{ implode(',', $termIdsInGroup) }}', '{{ $first->student->full_name }}')">
                                                        <i class="fas fa-trash me-2"></i> Delete all ({{ count($termIdsInGroup) }} terms)
                                                    </button>
                                                @else
                                                    <button class="dropdown-item text-danger" onclick="deleteReportCard({{ $first->id }}, '{{ $first->student->full_name }}')">
                                                        <i class="fas fa-trash me-2"></i> Delete report
                                                    </button>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </form>

                {{-- Pagination --}}
                @if($reportCardGroups->hasPages())
                    <div class="rc-pagination">
                        {{ $reportCardGroups->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function deleteReportCard(reportCardId, studentName) {
    if (confirm('Are you sure you want to delete the report card for "' + studentName + '"? This action cannot be undone and will permanently remove all associated data including subjects, test marks, and position calculations.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.report-cards.destroy', ':id') }}'.replace(':id', reportCardId);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteReportCardsGroup(studentId, courseId, academicYearId, termIds, studentName) {
    const termCount = termIds.split(',').length;
    if (confirm('Are you sure you want to delete ALL ' + termCount + ' report cards for "' + studentName + '"? This action cannot be undone and will permanently remove all associated data including subjects, test marks, and position calculations for all terms.')) {
        // Create a form to submit bulk delete via AJAX
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'DELETE');
        formData.append('student_id', studentId);
        formData.append('course_id', courseId);
        formData.append('academic_year_id', academicYearId);
        formData.append('term_ids', termIds);
        formData.append('bulk_delete', 'true');
        
        fetch('{{ route('admin.report-cards.destroy.bulk') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Report cards deleted successfully!');
                window.location.reload();
            } else {
                alert('Error deleting report cards: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting report cards. Please try again.');
        });
    }
}
</script>

<style>
/* ════════════════════════════════════════════
   REPORT CARDS — scoped component styles
════════════════════════════════════════════ */

/* FIX: prevent container-fluid from overflowing behind sidebar */
.main-content .container-fluid {
    width: 100% !important;
    margin-left: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

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
    border-radius: 8px;
    background: #198754;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.25rem;
    flex-shrink: 0;
}
.rc-page-header__title {
    font-size: 1.5rem; font-weight: 700;
    color: #212529; margin: 0;
}
.rc-page-header__sub {
    font-size: 0.82rem; color: #6c757d; margin: 0;
}

/* CARD */
.rc-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    overflow: visible;
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
    border-radius: 14px 14px 0 0;
}
.rc-card__header--blue  { background: #0d6efd; }
.rc-card__header--green { background: #198754; }
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
    grid-template-columns: 1fr 1fr 1fr minmax(140px, 180px);
    gap: 1rem;
    align-items: end;
}
.rc-hint {
    display: block;
    font-size: 0.72rem;
    color: #6c757d;
    margin-top: 6px;
}
.rc-hint kbd {
    font-size: 0.68rem;
    padding: 1px 4px;
    border-radius: 3px;
    background: #f1f3f5;
    border: 1px solid #dee2e6;
}
@media (max-width: 1400px) {
    .rc-gen-grid { grid-template-columns: 1fr 1fr 1fr; }
}
@media (max-width: 1100px) {
    .rc-gen-grid { grid-template-columns: 1fr 1fr; }
    .rc-page-header__left { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 768px) {
    .rc-gen-grid { grid-template-columns: 1fr; }
    .rc-page-header { flex-direction: column; align-items: flex-start; }
    .rc-page-header > .d-flex { width: 100%; flex-direction: column; }
    .rc-page-header > .d-flex .rc-btn { width: 100%; }
    .container-fluid { padding-left: 0.5rem; padding-right: 0.5rem; }
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
    border-radius: 6px;
    font-size: 0.875rem; font-weight: 600;
    border: none; cursor: pointer;
    text-decoration: none;
    transition: opacity .15s;
    white-space: nowrap;
}
.rc-btn--primary {
    background: #0d6efd;
    color: #fff;
}
.rc-btn--primary:hover { opacity: .88; color:#fff; }
.rc-btn--teal {
    background: #0dcaf0;
    color: #000;
}
.rc-btn--teal:hover { opacity: .88; color:#000; }
.rc-btn--warning {
    background: #ffc107;
    color: #000;
}
.rc-btn--warning:hover { opacity: .88; color:#000; }

/* ════════════════════════════════════════════
   CUSTOM MULTI-SELECT DROPDOWN  (ms- prefix)
════════════════════════════════════════════ */
.ms-dropdown {
    position: relative;
    width: 100%;
    font-size: 0.875rem;
    user-select: none;
}

/* Trigger button */
.ms-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 42px;
    padding: 0.45rem 0.75rem;
    border: 1.5px solid #dee2e6;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    color: #212529;
    font-size: 0.875rem;
    text-align: left;
    transition: border-color .2s, box-shadow .2s, background .15s;
    gap: 8px;
}
.ms-trigger:hover {
    border-color: #86b7fe;
    background: #fafcff;
}
.ms-trigger.ms-open {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13,110,253,.12);
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    background: #fafcff;
}
.ms-trigger__label {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
}
.ms-placeholder { color: #adb5bd; font-style: italic; }
.ms-trigger__icons {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.ms-chevron {
    color: #6c757d;
    font-size: 0.72rem;
    transition: transform .25s cubic-bezier(.4,0,.2,1);
}
.ms-open .ms-chevron { transform: rotate(180deg); }
.ms-clear-all {
    color: #adb5bd;
    font-size: 0.82rem;
    cursor: pointer;
    line-height: 1;
    transition: color .15s;
}
.ms-clear-all:hover { color: #dc3545; }

/* Trigger pills (selected items shown inside the button) */
.ms-trigger-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #e7f1ff;
    color: #084298;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ms-trigger-overflow {
    background: #f1f3f5;
    color: #495057;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Panel */
.ms-panel {
    position: absolute;
    top: calc(100% - 1px);
    left: 0; right: 0;
    z-index: 9999;
    background: #fff;
    border: 1.5px solid #0d6efd;
    border-top: none;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    box-shadow: 0 8px 24px rgba(13,110,253,.12), 0 2px 8px rgba(0,0,0,.08);
    overflow: hidden;
    animation: msSlideDown .18s cubic-bezier(.4,0,.2,1);
}
@keyframes msSlideDown {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Search */
.ms-search-wrap {
    position: relative;
    padding: 10px 10px 6px;
    border-bottom: 1px solid #f1f3f5;
}
.ms-search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%) translateY(2px);
    color: #adb5bd;
    font-size: 0.72rem;
    pointer-events: none;
}
.ms-search {
    width: 100%;
    border: 1.5px solid #dee2e6;
    border-radius: 7px;
    padding: 6px 10px 6px 28px;
    font-size: 0.815rem;
    color: #212529;
    background: #f8f9fa;
    outline: none;
    transition: border-color .2s, background .2s;
}
.ms-search:focus {
    border-color: #0d6efd;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(13,110,253,.1);
}
.ms-search::placeholder { color: #adb5bd; }

/* Bulk row */
.ms-bulk-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-bottom: 1px solid #f1f3f5;
    background: #fafafa;
}
.ms-bulk-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: 1.5px solid #dee2e6;
    border-radius: 6px;
    padding: 3px 10px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.ms-bulk-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #212529;
}
.ms-bulk-btn--danger { color: #842029; border-color: #f5c2c7; }
.ms-bulk-btn--danger:hover { background: #f8d7da; border-color: #f1aeb5; color: #58151c; }
.ms-count-badge {
    margin-left: auto;
    background: #0d6efd;
    color: #fff;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 0.68rem;
    font-weight: 700;
    white-space: nowrap;
    transition: background .2s;
}
.ms-count-badge.ms-count-zero { background: #dee2e6; color: #6c757d; }

/* Option list */
.ms-list {
    list-style: none;
    margin: 0;
    padding: 6px 6px;
    max-height: 240px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 transparent;
}
.ms-list::-webkit-scrollbar { width: 5px; }
.ms-list::-webkit-scrollbar-track { background: transparent; }
.ms-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 99px; }

/* Group label */
.ms-group-label {
    padding: 6px 10px 3px;
    font-size: 0.68rem;
    font-weight: 700;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .5px;
    pointer-events: none;
    margin-top: 4px;
}
.ms-group-label:first-child { margin-top: 0; }

/* Option item */
.ms-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 10px;
    border-radius: 7px;
    cursor: pointer;
    transition: background .12s;
    color: #212529;
}
.ms-option:hover {
    background: #f0f7ff;
}
.ms-option.ms-selected {
    background: #e7f1ff;
    color: #084298;
}
.ms-option.ms-selected:hover { background: #dbeafe; }
.ms-option.ms-hidden { display: none; }

/* Checkbox */
.ms-checkbox {
    width: 18px; height: 18px;
    border-radius: 5px;
    border: 2px solid #dee2e6;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    background: #fff;
    transition: border-color .15s, background .15s;
}
.ms-option:hover .ms-checkbox { border-color: #86b7fe; }
.ms-option.ms-selected .ms-checkbox {
    background: #0d6efd;
    border-color: #0d6efd;
}
.ms-check-icon {
    font-size: 0.62rem;
    color: #fff;
    opacity: 0;
    transform: scale(0.6);
    transition: opacity .12s, transform .12s;
}
.ms-option.ms-selected .ms-check-icon {
    opacity: 1;
    transform: scale(1);
}

.ms-option__text {
    font-size: 0.845rem;
    line-height: 1.3;
    min-width: 0;
}

/* Empty */
.ms-empty {
    padding: 16px 10px;
    text-align: center;
    color: #adb5bd;
    font-size: 0.82rem;
    font-style: italic;
    pointer-events: none;
}

/* No-results */
.ms-no-results {
    padding: 12px 10px;
    text-align: center;
    color: #adb5bd;
    font-size: 0.815rem;
    font-style: italic;
}

/* Pills row below dropdown */
.ms-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 8px;
    min-height: 0;
}
.ms-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e7f1ff;
    color: #084298;
    border: 1px solid #bfdbfe;
    border-radius: 20px;
    padding: 3px 10px 3px 10px;
    font-size: 0.72rem;
    font-weight: 600;
    animation: pillPop .18s cubic-bezier(.34,1.56,.64,1);
}
@keyframes pillPop {
    from { opacity: 0; transform: scale(0.7); }
    to   { opacity: 1; transform: scale(1); }
}
.ms-pill__remove {
    cursor: pointer;
    color: #6ea8fe;
    font-size: 0.65rem;
    line-height: 1;
    transition: color .12s;
    display: flex;
    align-items: center;
}
.ms-pill__remove:hover { color: #dc3545; }

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
    background: #0d6efd;
}
.rc-avatar img {
    width: 100%; height: 100%; object-fit: cover;
    border-radius: 50%;
}
.rc-avatar--blue { background: #0d6efd; }
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
.rc-table__actions {
    overflow: visible !important;
    vertical-align: middle;
}
.rc-student-actions .dropdown-menu {
    min-width: 15rem;
    z-index: 1055;
}
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

/* GROUPED ROW */
.rc-table__row--grouped td {
    vertical-align: top;
    padding-top: 0.85rem;
    padding-bottom: 0.85rem;
}
.rc-cell-stack { min-width: 88px; }
.rc-meta--block { margin-bottom: 6px; }
.rc-term-pill {
    display: inline-block;
    font-size: 0.68rem;
    font-weight: 600;
    color: #495057;
    background: #f1f3f5;
    border-radius: 6px;
    padding: 2px 8px;
    margin: 2px 2px 0 0;
}
.rc-avg--overall { font-size: 0.95rem; }
.rc-stack-label {
    display: block;
    font-size: 0.62rem;
    color: #868e96;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 0 0 6px 0;
    line-height: 1.2;
}
.rc-avg--small {
    font-size: 0.72rem;
    padding: 2px 6px;
    font-weight: 600;
}
.rc-term-metric {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 4px;
    font-size: 0.72rem;
}
.rc-term-metric--center {
    justify-content: center;
    flex-direction: column;
    gap: 2px;
}
.rc-term-metric__t {
    color: #868e96;
    max-width: 52px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.rc-badge--overall {
    font-size: 0.78rem;
    padding: 4px 12px;
    margin-bottom: 6px;
}
.rc-badge--tiny {
    font-size: 0.62rem;
    padding: 2px 7px;
    border-radius: 10px;
    font-weight: 700;
}
.rc-position--small {
    font-size: 0.72rem;
    padding: 2px 8px;
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── State ──────────────────────────────────────────────
    const selected = new Set(); // set of string values

    // ── Element refs ──────────────────────────────────────
    const dropdown      = document.getElementById('termDropdown');
    const trigger       = document.getElementById('termTrigger');
    const triggerLabel  = document.getElementById('termTriggerLabel');
    const panel         = document.getElementById('termPanel');
    const chevron       = document.getElementById('termChevron');
    const searchInput   = document.getElementById('termSearch');
    const list          = document.getElementById('termList');
    const pillsWrap     = document.getElementById('termPills');
    const nativeSelect  = document.getElementById('termNativeSelect');
    const clearAllBtn   = document.getElementById('termClearAll');
    const selectAllBtn  = document.getElementById('termSelectAll');
    const deselectAllBtn= document.getElementById('termDeselectAll');
    const countBadge    = document.getElementById('termCountBadge');
    const options       = list.querySelectorAll('.ms-option');

    // ── Helpers ────────────────────────────────────────────
    function getLabel(val) {
        const el = list.querySelector(`.ms-option[data-value="${val}"]`);
        return el ? el.dataset.label : val;
    }

    function syncNative() {
        Array.from(nativeSelect.options).forEach(o => {
            o.selected = selected.has(o.value);
        });
    }

    function renderTriggerLabel() {
        triggerLabel.innerHTML = '';
        if (selected.size === 0) {
            triggerLabel.innerHTML = '<span class="ms-placeholder">Select terms…</span>';
            clearAllBtn.style.display = 'none';
            return;
        }
        clearAllBtn.style.display = 'flex';
        const vals = Array.from(selected);
        const maxShow = 3;
        const shown = vals.slice(0, maxShow);
        shown.forEach(v => {
            const pill = document.createElement('span');
            pill.className = 'ms-trigger-pill';
            pill.textContent = getLabel(v);
            triggerLabel.appendChild(pill);
        });
        if (vals.length > maxShow) {
            const overflow = document.createElement('span');
            overflow.className = 'ms-trigger-overflow';
            overflow.textContent = `+${vals.length - maxShow} more`;
            triggerLabel.appendChild(overflow);
        }
    }

    function renderPills() {
        pillsWrap.innerHTML = '';
        selected.forEach(v => {
            const pill = document.createElement('span');
            pill.className = 'ms-pill';
            pill.innerHTML = `<span>${getLabel(v)}</span><span class="ms-pill__remove" data-value="${v}" title="Remove"><i class="fas fa-times"></i></span>`;
            pillsWrap.appendChild(pill);
        });
    }

    function renderCount() {
        countBadge.textContent = `${selected.size} selected`;
        if (selected.size === 0) {
            countBadge.classList.add('ms-count-zero');
        } else {
            countBadge.classList.remove('ms-count-zero');
        }
    }

    function renderAll() {
        options.forEach(opt => {
            const val = opt.dataset.value;
            if (selected.has(val)) {
                opt.classList.add('ms-selected');
                opt.setAttribute('aria-selected', 'true');
            } else {
                opt.classList.remove('ms-selected');
                opt.setAttribute('aria-selected', 'false');
            }
        });
        renderTriggerLabel();
        renderPills();
        renderCount();
        syncNative();
    }

    function toggleValue(val) {
        if (selected.has(val)) {
            selected.delete(val);
        } else {
            selected.add(val);
        }
        renderAll();
    }

    function openPanel() {
        panel.style.display = 'block';
        trigger.classList.add('ms-open');
        trigger.setAttribute('aria-expanded', 'true');
        searchInput.focus();
    }

    function closePanel() {
        panel.style.display = 'none';
        trigger.classList.remove('ms-open');
        trigger.setAttribute('aria-expanded', 'false');
        searchInput.value = '';
        filterOptions('');
    }

    function filterOptions(q) {
        const query = q.toLowerCase().trim();
        let hasVisible = false;
        const groupLabels = list.querySelectorAll('.ms-group-label');

        // Track which groups have visible children
        const groupVisibility = {};

        options.forEach(opt => {
            const searchText = opt.dataset.search || '';
            const match = !query || searchText.includes(query);
            opt.classList.toggle('ms-hidden', !match);
            if (match) {
                hasVisible = true;
                const gid = opt.dataset.group;
                groupVisibility[gid] = true;
            }
        });

        // Show/hide group labels based on whether they have visible children
        groupLabels.forEach(gl => {
            const gid = gl.dataset.group;
            gl.style.display = groupVisibility[gid] ? '' : 'none';
        });

        // No-results message
        let noRes = list.querySelector('.ms-no-results');
        if (!hasVisible && query) {
            if (!noRes) {
                noRes = document.createElement('li');
                noRes.className = 'ms-no-results';
                noRes.textContent = 'No terms match your search';
                list.appendChild(noRes);
            }
            noRes.style.display = '';
        } else if (noRes) {
            noRes.style.display = 'none';
        }
    }

    // ── Events ─────────────────────────────────────────────

    // Toggle panel
    trigger.addEventListener('click', function (e) {
        if (clearAllBtn.contains(e.target)) return; // handled separately
        panel.style.display === 'none' ? openPanel() : closePanel();
    });

    // Click outside to close
    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target)) closePanel();
    });

    // Select option
    list.addEventListener('click', function (e) {
        const opt = e.target.closest('.ms-option');
        if (!opt) return;
        toggleValue(opt.dataset.value);
    });

    // Remove via pill (outside panel)
    pillsWrap.addEventListener('click', function (e) {
        const btn = e.target.closest('.ms-pill__remove');
        if (!btn) return;
        selected.delete(btn.dataset.value);
        renderAll();
    });

    // Clear all (trigger icon)
    clearAllBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        selected.clear();
        renderAll();
    });

    // Select all (visible)
    selectAllBtn.addEventListener('click', function () {
        options.forEach(opt => {
            if (!opt.classList.contains('ms-hidden')) {
                selected.add(opt.dataset.value);
            }
        });
        renderAll();
    });

    // Deselect all
    deselectAllBtn.addEventListener('click', function () {
        selected.clear();
        renderAll();
    });

    // Search
    searchInput.addEventListener('input', function () {
        filterOptions(this.value);
    });
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePanel();
    });

    // Init
    renderAll();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllReportCards');
    const checkboxes = Array.from(document.querySelectorAll('.report-card-checkbox'));
    const selectedCount = document.getElementById('selectedReportsCount');
    const bulkForm = document.getElementById('bulkApproveForm');

    function refreshCount() {
        const checked = checkboxes.filter(cb => cb.checked).length;
        if (selectedCount) {
            selectedCount.textContent = String(checked);
        }
        if (selectAll) {
            selectAll.checked = checked > 0 && checked === checkboxes.length;
            selectAll.indeterminate = checked > 0 && checked < checkboxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
            refreshCount();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', refreshCount));

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const checked = checkboxes.some(cb => cb.checked);
            if (!checked) {
                e.preventDefault();
                alert('Select at least one report card to approve.');
            }
        });
    }

    // Show/hide delete selected button
    function updateDeleteBtn() {
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        if (deleteBtn) {
            const checked = checkboxes.filter(cb => cb.checked).length;
            deleteBtn.style.display = checked > 0 ? 'inline-block' : 'none';
        }
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateDeleteBtn));

    refreshCount();
});
</script>
<script>
// Delete functions
function deleteByClass() {
    const form = document.querySelector('form[action*="generate-class"]');
    if (!form) return alert('Form not found');

    const courseId = form.querySelector('[name="course_id"]')?.value;
    const yearId = form.querySelector('[name="academic_year_id"]')?.value;
    const termSelect = document.getElementById('termNativeSelect');
    const terms = termSelect ? Array.from(termSelect.selectedOptions).map(o => o.value) : [];

    if (!courseId || !yearId || terms.length === 0) {
        return alert('Select course, academic year, and at least one term.');
    }

    const course = form.querySelector('[name="course_id"] option:checked')?.text;
    const year = form.querySelector('[name="academic_year_id"] option:checked')?.text;

    if (!confirm(`Delete ALL report cards for ${course} (${year})?\n\nThis action cannot be undone.`)) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const formData = new FormData();
    formData.append('course_id', courseId);
    formData.append('academic_year_id', yearId);
    terms.forEach(t => formData.append('term_ids[]', t));

    fetch('{{ route("admin.report-cards.delete-by-class") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(err => showAlert('danger', 'Error: ' + err.message));
}

function deleteSelectedReports() {
    const checkboxes = document.querySelectorAll('.report-card-checkbox:checked');
    if (checkboxes.length === 0) {
        return alert('Select at least one report card to delete.');
    }

    if (!confirm(`Delete ${checkboxes.length} selected report card(s)?\n\nThis action cannot be undone.`)) {
        return;
    }

    const ids = Array.from(checkboxes).map(cb => cb.value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('{{ route("admin.report-cards.delete-selected") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ report_card_ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(err => showAlert('danger', 'Error: ' + err.message));
}

function deleteAll() {
    if (!confirm('Delete ALL report cards in the system?\n\nThis will delete all generated report cards. This action CANNOT be undone.')) {
        return;
    }

    if (!confirm('Are you absolutely sure? Click OK again to confirm deletion of ALL report cards.')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('{{ route("admin.report-cards.delete-all") }}?confirm=DELETE_ALL', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(err => showAlert('danger', 'Error: ' + err.message));
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = Array.from(document.querySelectorAll('.report-card-checkbox'));
    const selectedCount = document.getElementById('selectedReportsCount');
    const bulkForm = document.getElementById('bulkApproveForm');

    function refreshCount() {
        const checked = checkboxes.filter(cb => cb.checked).length;
        if (selectedCount) {
            selectedCount.textContent = String(checked);
        }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', refreshCount));

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const checked = checkboxes.some(cb => cb.checked);
            if (!checked) {
                e.preventDefault();
                alert('Select at least one report card to approve.');
            }
        });
    }

    // Show/hide delete selected button
    function updateDeleteBtn() {
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        if (deleteBtn) {
            const checked = checkboxes.filter(cb => cb.checked).length;
            deleteBtn.style.display = checked > 0 ? 'inline-block' : 'none';
        }
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateDeleteBtn));

    refreshCount();
});
</script>
<script>
// Delete functions
function deleteByClass() {
    const form = document.querySelector('form[action*="generate-class"]');
    if (!form) return alert('Form not found');

    const courseId = form.querySelector('[name="course_id"]')?.value;
    const yearId = form.querySelector('[name="academic_year_id"]')?.value;
    const termSelect = document.getElementById('termNativeSelect');
    const terms = termSelect ? Array.from(termSelect.selectedOptions).map(o => o.value) : [];

    if (!courseId || !yearId || terms.length === 0) {
        return alert('Select course, academic year, and at least one term.');
    }

    const course = form.querySelector('[name="course_id"] option:checked')?.text;
    const year = form.querySelector('[name="academic_year_id"] option:checked')?.text;

    if (!confirm(`Delete ALL report cards for ${course} (${year})?\n\nThis action cannot be undone.`)) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const formData = new FormData();
    formData.append('course_id', courseId);
    formData.append('academic_year_id', yearId);
    terms.forEach(t => formData.append('term_ids[]', t));

    fetch('{{ route("admin.report-cards.delete-by-class") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(err => showAlert('danger', 'Error: ' + err.message));
}

function deleteSelectedReports() {
    const checkboxes = document.querySelectorAll('.report-card-checkbox:checked');
    if (checkboxes.length === 0) {
        return alert('Select at least one report card to delete.');
    }

    if (!confirm(`Delete ${checkboxes.length} selected report card(s)?\n\nThis action cannot be undone.`)) {
        return;
    }

    const ids = Array.from(checkboxes).map(cb => cb.value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('{{ route("admin.report-cards.delete-selected") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ report_card_ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(err => showAlert('danger', 'Error: ' + err.message));
}

function deleteAll() {
    if (!confirm('Delete ALL report cards in the system?\n\nThis will delete all generated report cards. This action CANNOT be undone.')) {
        return;
    }

    if (!confirm('Are you absolutely sure? Click OK again to confirm deletion of ALL report cards.')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('{{ route("admin.report-cards.delete-all") }}?confirm=DELETE_ALL', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(err => showAlert('danger', 'Error: ' + err.message));
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}
</script>
<script>
// Helper function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}

// Helper to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Global state for pending generation
window.pendingGenerationData = null;

// Send individual fee email
window.sendIndividualFeeEmail = function(studentId) {
    const button = event.target.closest('button');
    if (!button) return;
    
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

    const emailRoute = @json(route('admin.report-cards.send-fees-email-modal', ['student' => 'ID']));
    const url = emailRoute.replace('ID', studentId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Email sent to ' + data.student_name);
            button.innerHTML = '<i class="fas fa-check me-1"></i> Sent';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-primary');
        } else {
            showAlert('danger', data.message || 'Error sending email');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-envelope me-1"></i> Email';
        }
    })
    .catch(err => {
        console.error('Email send error:', err);
        showAlert('danger', 'Error sending email: ' + err.message);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-envelope me-1"></i> Email';
    });
};

// Send bulk fee emails
window.sendBulkFeesEmails = function(students) {
    if (!window.confirm(`Send fee reminder emails to ${students.length} student(s)?`)) {
        return;
    }

    const button = document.getElementById('sendAllFeesEmailsBtn');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending to all...';

    let completed = 0;
    let failed = 0;

    const emailRoute = @json(route('admin.report-cards.send-fees-email-modal', ['student' => 'ID']));
    
    const emailPromises = students.map(student => {
        const url = emailRoute.replace('ID', student.id);
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                completed++;
            } else {
                failed++;
            }
        })
        .catch(err => {
            failed++;
        });
    });

    Promise.all(emailPromises).then(() => {
        showAlert('success', `Sent ${completed}/${students.length} fee reminder email(s)`);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-envelope me-1"></i> Send Emails to All';
        if (failed === 0) {
            setTimeout(() => {
                const modalElement = document.getElementById('outstandingFeesModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
            }, 1500);
        }
    });
};

// Main form submission handler
document.addEventListener('DOMContentLoaded', function () {
    // Find all forms that might be the generation form
    const forms = document.querySelectorAll('form');
    let generateForm = null;
    
    for (let form of forms) {
        const action = form.getAttribute('action');
        if (action && action.includes('report-cards/generate-class')) {
            generateForm = form;
            break;
        }
    }

    if (!generateForm) {
        console.warn('Generate form not found');
        return;
    }

    generateForm.addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Form submitted, checking fees...');

        // Get form data
        const courseId = generateForm.querySelector('[name="course_id"]')?.value;
        const yearId = generateForm.querySelector('[name="academic_year_id"]')?.value;
        const termSelect = document.getElementById('termNativeSelect');
        const termIds = termSelect ? Array.from(termSelect.selectedOptions).map(o => o.value) : [];

        if (!courseId || !yearId || termIds.length === 0) {
            alert('Please select course, academic year, and at least one term.');
            return;
        }

        console.log('Checking fees for:', { courseId, yearId, termIds });

        // Check for outstanding fees via AJAX
        const checkUrl = @json(route('admin.report-cards.check-fees-for-generation'));
        fetch(checkUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                course_id: courseId,
                academic_year_id: yearId,
                term_ids: termIds
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Server error response:', text);
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Fee check response:', data);
            if (data.students && data.students.length > 0) {
                // Students with outstanding fees found - show modal
                console.log('Found students with fees:', data.students);
                showOutstandingFeesModal(data.students, courseId, yearId, termIds, generateForm);
            } else {
                // No fees - proceed with generation
                console.log('No students with fees, proceeding with generation');
                submitGenerateForm(generateForm, courseId, yearId, termIds);
            }
        })
        .catch(err => {
            console.error('Error checking fees:', err);
            showAlert('danger', 'Error checking outstanding fees: ' + err.message);
        });
    });

    function showOutstandingFeesModal(students, courseId, yearId, termIds, form) {
        console.log('Showing outstanding fees modal for', students.length, 'students');
        
        const modalElement = document.getElementById('outstandingFeesModal');
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }

        // Update warning message
        document.getElementById('feesWarningMessage').innerText = 
            `The following ${students.length} student(s) have outstanding fees and cannot have report cards generated:`;

        // Build students list
        const studentsList = document.getElementById('studentsList');
        studentsList.innerHTML = students.map(student => `
            <div class="fees-student-card">
                <div class="fees-student-info">
                    <div class="fees-student-name">${escapeHtml(student.full_name)}</div>
                    <div class="fees-student-amount">
                        Outstanding: <span class="fees-amount">RWF ${new Intl.NumberFormat('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(parseFloat(student.outstanding_amount))}</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary fees-email-btn" onclick="window.sendIndividualFeeEmail(${student.id})">
                    <i class="fas fa-envelope me-1"></i> Email
                </button>
            </div>
        `).join('');

        // Store generation data
        window.pendingGenerationData = {
            courseId: courseId,
            yearId: yearId,
            termIds: termIds,
            form: form
        };

        // Setup send all emails button
        document.getElementById('sendAllFeesEmailsBtn').onclick = function() {
            window.sendBulkFeesEmails(students);
        };

        // Setup generate anyway button
        document.getElementById('generateAnywayBtn').onclick = function() {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
            if (window.pendingGenerationData) {
                submitGenerateForm(
                    window.pendingGenerationData.form,
                    window.pendingGenerationData.courseId,
                    window.pendingGenerationData.yearId,
                    window.pendingGenerationData.termIds
                );
            }
        };

        // Show modal
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    function submitGenerateForm(form, courseId, yearId, termIds) {
        console.log('Submitting form for generation...');
        // Create a hidden form submission to avoid re-triggering the submit event
        const submitForm = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: submitForm
        })
        .then(response => response.text())
        .then(html => {
            showAlert('success', 'Report cards generated successfully!');
            setTimeout(() => location.reload(), 1500);
        })
        .catch(err => {
            console.error('Generation error:', err);
            showAlert('danger', 'Error generating report cards: ' + err.message);
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnDownloadClassReportCards');
    if (!btn) return;
    const printClassUrl = @json(route('admin.report-cards.print-class'));
    btn.addEventListener('click', function () {
        const form = btn.closest('form');
        if (!form) return;
        const courseId = form.querySelector('[name="course_id"]')?.value;
        const yearId = form.querySelector('[name="academic_year_id"]')?.value;
        const termSelect = document.getElementById('termNativeSelect');
        const terms = termSelect ? Array.from(termSelect.selectedOptions).map(function (o) { return o.value; }) : [];
        if (!courseId || !yearId || terms.length === 0) {
            alert('Select course, academic year, and at least one term.');
            return;
        }
        const params = new URLSearchParams();
        params.set('course_id', courseId);
        params.set('academic_year_id', yearId);
        params.set('download', '1');
        terms.forEach(function (t) { params.append('term_ids[]', t); });
        window.location.href = printClassUrl + '?' + params.toString();
    });
});
</script>

<!-- MODAL: Outstanding Fees Warning -->
<div class="modal fade" id="outstandingFeesModal" tabindex="-1" aria-labelledby="outstandingFeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="outstandingFeesModalLabel">
                    <i class="fas fa-exclamation-circle me-2"></i> Students with Outstanding Fees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" id="feesWarningMessage"></p>
                <div id="studentsList" style="max-height: 400px; overflow-y: auto;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="sendAllFeesEmailsBtn">
                    <i class="fas fa-envelope me-1"></i> Send Emails to All
                </button>
                <button type="button" class="btn btn-primary" id="generateAnywayBtn">
                    <i class="fas fa-check me-1"></i> Generate Anyway
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-header.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.fees-student-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 10px;
    background: #f8f9fa;
    transition: all 0.2s;
}

.fees-student-card:hover {
    background: #fff3cd;
    border-color: #ffc107;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
}

.fees-student-info {
    flex: 1;
}

.fees-student-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #212529;
    margin-bottom: 4px;
}

.fees-student-amount {
    font-size: 0.85rem;
    color: #6c757d;
}

.fees-amount {
    font-weight: 700;
    color: #dc3545;
    font-size: 1rem;
}

.fees-email-btn {
    padding: 6px 12px;
    font-size: 0.85rem;
    white-space: nowrap;
}
</style>