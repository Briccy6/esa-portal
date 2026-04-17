@extends('admin.layout')

@push('styles')
    @include('admin.report-cards.partials.layout-fixes')
@endpush

@section('content')
<div class="content">
    <div class="report-container">
        <div style="padding: 1.5rem; max-width: 100%; width: 100%; margin: 0 auto; box-sizing: border-box;">

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
            <a href="{{ route('admin.recalculate-positions.index') }}" class="rc-btn rc-btn--warning" title="Recalculate student rankings for all report cards">
                <i class="fas fa-recycle"></i> Recalculate Positions
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
                                <i class="fas fa-bolt me-1"></i> Generate / Update Class Reports
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

                            {{-- Custom panel --}}
                            <div class="ms-panel" id="termPanel" hidden>
                                <div class="ms-search-wrap">
                                    <input type="text" id="termSearchInput" class="ms-search" placeholder="Search terms...">
                                    <span class="ms-search-icon"><i class="fas fa-search"></i></span>
                                </div>
                                <ul class="ms-list" id="termList">
                                    @forelse($terms->groupBy('academic_year_id') as $yearId => $yearTerms)
                                        <li class="ms-group-label" data-group="{{ $yearId }}">
                                            <strong>
                                                @php
                                                    $year = \App\Models\AcademicYear::find($yearId);
                                                @endphp
                                                {{ $year?->year ?? 'Year '.$yearId }}
                                            </strong>
                                        </li>
                                        @foreach($yearTerms as $term)
                                            <li class="ms-option" data-value="{{ $term->id }}" data-group="{{ $yearId }}" data-search="{{ strtolower($term->name . ' ' . ($year?->year ?? '')) }}">
                                                <span class="ms-option__check"><i class="fas fa-check"></i></span>
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
                        <span class="rc-hint d-block mt-1"><strong>Tip:</strong> You can run <em>Generate / Update Class Reports</em> any time after a student pays. It safely creates only missing reports and keeps existing ones.</span>
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
            <div class="p-3 border-bottom bg-light d-flex gap-2 flex-wrap align-items-center">
                <input type="text" id="reportSearchInput" class="form-control" style="max-width: 360px;" placeholder="Live search student, course, branch, year, term...">
                <select id="reportSortSelect" class="form-select" style="max-width: 280px;">
                    <option value="newest">Newest generated first</option>
                    <option value="oldest">Oldest generated first</option>
                    <option value="student_asc">Student name (A-Z)</option>
                    <option value="student_desc">Student name (Z-A)</option>
                    <option value="class_asc">Class / Course (A-Z)</option>
                    <option value="class_desc">Class / Course (Z-A)</option>
                    <option value="branch_asc">Branch (A-Z)</option>
                    <option value="branch_desc">Branch (Z-A)</option>
                    <option value="outstanding_desc">Outstanding fees (High to Low)</option>
                    <option value="outstanding_asc">Outstanding fees (Low to High)</option>
                </select>
            </div>
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
                                <th class="text-center"><i class="fas fa-trophy me-1 text-info"></i> Position</th>
                                <th class="text-center"><i class="fas fa-circle me-1 text-secondary"></i> Status</th>
                                <th class="text-center"><i class="fas fa-wallet me-1 text-danger"></i> Fees Status</th>
                                <th class="text-center"><i class="fas fa-cogs me-1 text-dark"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rankCounter = 1; @endphp
                            @foreach($reportCardGroups as $group)
                            @php
                                $first = $group->first();
                                $overallAvg = (float) $group->avg('average_marks');
                            @endphp
                            @php
                                $groupGeneratedAt = $group->max('created_at')?->timestamp ?? 0;
                                $termsText = $group->pluck('term.name')->filter()->implode(' ');
                                $statusesText = $group->pluck('status')->implode(' ');
                                $branchText = $first->student?->branch?->name ?? '';
                            @endphp
                            <tr class="rc-table__row rc-table__row--grouped"
                                data-student="{{ strtolower($first->student->full_name ?? '') }}"
                                data-course="{{ strtolower($first->course->course_name ?? '') }}"
                                data-branch="{{ strtolower($branchText) }}"
                                data-year="{{ strtolower($first->academicYear->year ?? '') }}"
                                data-terms="{{ strtolower($termsText) }}"
                                data-status="{{ strtolower($statusesText) }}"
                                data-generated-at="{{ $groupGeneratedAt }}">
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
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: linear-gradient(135deg, #007bff, #0056b3); color: white; border-radius: 50%; font-weight: bold; font-size: 0.85rem; flex-shrink: 0;">
                                                #{{ $rankCounter }}
                                            </span>
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

                                {{-- Fees status per term --}}
                                <td class="text-center rc-cell-stack">
                                    @foreach($group as $reportCard)
                                        @php
                                            $paymentStatus = \App\Services\FeePaymentService::getPaymentStatus($reportCard->student, [(int) $reportCard->term_id]);
                                            $outstanding = (float) ($paymentStatus['outstanding'] ?? 0);
                                        @endphp
                                        <div class="rc-term-metric rc-term-metric--center">
                                            @if($outstanding > 0)
                                                <span class="rc-badge rc-badge--tiny rc-badge--red">
                                                    Outstanding: {{ number_format($outstanding, 2) }}
                                                </span>
                                            @else
                                                <span class="rc-badge rc-badge--tiny rc-badge--green">Paid</span>
                                            @endif
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
                                    $hasOutstandingInGroup = $group->contains(function ($rc) {
                                        $status = \App\Services\FeePaymentService::getPaymentStatus($rc->student, [(int) $rc->term_id]);
                                        return ((float) ($status['outstanding'] ?? 0)) > 0;
                                    });
                                    @endphp
                                    <div class="dropdown rc-student-actions">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" data-bs-auto-close="true"
                                                aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            {{-- View/Print Section --}}
                                            @if($multipleTermsInGroup && !$hasOutstandingInGroup)
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
                                            @elseif(!$hasOutstandingInGroup)
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
                                            @else
                                                <li>
                                                    <span class="dropdown-item text-muted">
                                                        <i class="fas fa-lock me-2 text-danger"></i> Print/Download blocked (Outstanding fees)
                                                    </span>
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
                            @php $rankCounter++; @endphp
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
    </div>
</div>
@endsection

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('reportSearchInput');
    const sortSelect = document.getElementById('reportSortSelect');
    
    if (!searchInput || !sortSelect) return;

    const tbody = document.querySelector('.rc-table tbody');
    if (!tbody) return;

    // Debounce function to avoid excessive AJAX calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Fetch and render results
    async function fetchAndRender(page = 1) {
        const search = searchInput.value.trim();
        const sort = sortSelect.value;
        
        try {
            const response = await fetch(`{{ route('admin.report-cards.search-sort') }}?search=${encodeURIComponent(search)}&sort=${sort}&page=${page}`);
            const data = await response.json();
            
            if (!data.success) {
                console.error('Error:', data.message);
                return;
            }

            // Render table rows
            renderTableRows(data.results);
        } catch (error) {
            console.error('Error fetching results:', error);
        }
    }

    // Render table rows from JSON data - FIXED TO MATCH TABLE STRUCTURE
    function renderTableRows(results) {
        tbody.innerHTML = '';
        
        if (results.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="9" style="text-align: center; padding: 3rem 0; color: #6c757d;">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    <div style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">No report cards found</div>
                    <div style="font-size: 0.875rem;">Try adjusting your search or sort filters</div>
                </td>
            `;
            tbody.appendChild(emptyRow);
            return;
        }

        let rankCounter = 1;
        results.forEach(group => {
            const row = document.createElement('tr');
            row.className = 'rc-table__row rc-table__row--grouped';
            row.dataset.student = group.group_data.student;
            row.dataset.course = group.group_data.course;
            row.dataset.branch = group.group_data.branch;
            row.dataset.year = group.group_data.year;
            row.dataset.terms = group.group_data.terms;
            row.dataset.generatedAt = group.group_data.generated_at;

            const termsHtml = group.terms.map(t => `<span class="rc-term-pill">${t}</span>`).join('');
            const statusBadges = group.statuses.map(s => {
                const statusClass = s === 'approved' ? 'rc-badge--green' : (s === 'printed' ? 'rc-badge--blue' : 'rc-badge--yellow');
                return `<div class="rc-term-metric rc-term-metric--center"><span class="rc-badge rc-badge--tiny ${statusClass}">${s.charAt(0).toUpperCase() + s.slice(1)}</span></div>`;
            }).join('');

            const feesBadges = group.fees_statuses.map(status => {
                const feeStatus = status.outstanding > 0 
                    ? `<span class="rc-badge rc-badge--tiny rc-badge--red">Outstanding: ${status.outstanding_formatted}</span>`
                    : `<span class="rc-badge rc-badge--tiny rc-badge--green">Paid</span>`;
                return `<div class="rc-term-metric rc-term-metric--center">${feeStatus}</div>`;
            }).join('');

            const avgColorClass = group.overall_avg >= 70 ? 'rc-avg--high' : (group.overall_avg >= 50 ? 'rc-avg--mid' : 'rc-avg--low');

            // Build action menu
            let actionsHtml = '';
            if (group.multiple_terms && !group.has_outstanding) {
                actionsHtml = `
                    <li>
                        <a href="/admin/report-cards/consolidated?student_id=${group.student_id}&course_id=${group.course_id}&academic_year_id=${group.academic_year_id}&term_ids[]=${group.term_ids.join('&term_ids[]=')}" class="dropdown-item" target="_blank">
                            <i class="fas fa-eye me-2 text-info"></i> View all (${group.term_ids.length} terms)
                        </a>
                    </li>
                    <li>
                        <a href="/admin/report-cards/consolidated/print?student_id=${group.student_id}&course_id=${group.course_id}&academic_year_id=${group.academic_year_id}&term_ids[]=${group.term_ids.join('&term_ids[]=')}" class="dropdown-item" target="_blank">
                            <i class="fas fa-print me-2 text-success"></i> Print all (${group.term_ids.length} terms)
                        </a>
                    </li>
                `;
            } else if (!group.has_outstanding) {
                actionsHtml = `
                    <li>
                        <a href="/admin/report-cards/${group.first_report_card_id}" class="dropdown-item" target="_blank">
                            <i class="fas fa-eye me-2 text-info"></i> View
                        </a>
                    </li>
                    <li>
                        <a href="/admin/report-cards/${group.first_report_card_id}/print" class="dropdown-item" target="_blank">
                            <i class="fas fa-print me-2 text-success"></i> Print
                        </a>
                    </li>
                `;
            } else {
                actionsHtml = `
                    <li>
                        <span class="dropdown-item text-muted" style="cursor: default;">
                            <i class="fas fa-lock me-2 text-danger"></i> Print/Download blocked (Outstanding fees)
                        </span>
                    </li>
                `;
            }

            let deleteHtml = '';
            if (group.multiple_terms) {
                deleteHtml = `
                    <button class="dropdown-item text-danger" onclick="deleteReportCardsGroup('${group.student_id}', '${group.course_id}', '${group.academic_year_id}', '${group.term_ids.join(',')}', '${group.student_name.replace(/'/g, "\\'")}')">
                        <i class="fas fa-trash me-2"></i> Delete all (${group.term_ids.length} terms)
                    </button>
                `;
            } else {
                deleteHtml = `
                    <button class="dropdown-item text-danger" onclick="deleteReportCard(${group.first_report_card_id}, '${group.student_name.replace(/'/g, "\\'")}')">
                        <i class="fas fa-trash me-2"></i> Delete report
                    </button>
                `;
            }

            row.innerHTML = `
                <td class="text-center rc-cell-stack">
                    <div class="rc-term-metric rc-term-metric--center">
                        <label class="mb-0 d-flex align-items-center gap-1">
                            <input type="checkbox" class="report-card-checkbox" name="report_card_ids[]" value="${group.first_report_card_id}" disabled>
                            <span class="rc-term-pill">${group.terms[0] || '—'}</span>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="rc-student-cell">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: linear-gradient(135deg, #007bff, #0056b3); color: white; border-radius: 50%; font-weight: bold; font-size: 0.85rem; flex-shrink: 0;">
                                #${rankCounter}
                            </span>
                            <div class="rc-avatar rc-avatar--blue">
                                <div style="color: #fff; font-size: 0.8rem; font-weight: 700;">
                                    ${group.student_name.charAt(0).toUpperCase()}
                                </div>
                            </div>
                        </div>
                        <div class="rc-student-cell__info">
                            <span class="rc-student-cell__name">${group.student_name}</span>
                            <span class="rc-student-cell__id">ID: ${group.student_id}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="rc-course-cell">
                        <i class="fas fa-graduation-cap text-info me-1"></i>
                        <span title="${group.course_name}">
                            ${group.course_name.substring(0, 22)}
                        </span>
                    </div>
                </td>
                <td class="text-center rc-cell-stack">
                    <div class="rc-meta rc-meta--block">
                        <span class="rc-meta__year">${group.year}</span>
                    </div>
                    ${termsHtml}
                </td>
                <td class="text-center rc-cell-stack">
                    <span class="rc-avg rc-avg--overall ${avgColorClass}">
                        ${group.overall_avg}%
                    </span>
                    <span class="rc-stack-label">overall</span>
                </td>
                <td class="text-center rc-cell-stack">
                    <span class="rc-dash">—</span>
                </td>
                <td class="text-center rc-cell-stack">
                    ${statusBadges}
                </td>
                <td class="text-center rc-cell-stack">
                    ${feesBadges}
                </td>
                <td class="text-center rc-table__actions">
                    <div class="dropdown rc-student-actions">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" data-bs-auto-close="true"
                                aria-expanded="false">
                            Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            ${actionsHtml}
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                ${deleteHtml}
                            </li>
                        </ul>
                    </div>
                </td>
            `;
            
            tbody.appendChild(row);
            rankCounter++;
        });
    }

    // Event listeners
    const debouncedFetchAndRender = debounce(fetchAndRender, 300);
    
    searchInput.addEventListener('input', () => {
        debouncedFetchAndRender();
    });

    sortSelect.addEventListener('change', () => {
        fetchAndRender();
    });
});
</script>

<style>
/* ════════════════════════════════════════════
   REPORT CARDS — scoped component styles
════════════════════════════════════════════ */

/* FIX: prevent container-fluid from overflowing behind sidebar */
.report-container .container-fluid {
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
.rc-card__header--green {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
}
.rc-card__header--blue {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}
.rc-card__header-count {
    margin-left: auto;
    font-size: 0.8rem;
    opacity: 0.9;
    padding: 0.25rem 0.5rem;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
}
.rc-card__body {
    background: #fff;
}

/* TABLE STYLES */
.rc-table-wrap {
    overflow-x: auto;
}

.rc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}

.rc-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.rc-table th {
    padding: 0.75rem 0.5rem;
    text-align: center;
    font-weight: 600;
    color: #495057;
    white-space: nowrap;
}

.rc-table tbody tr {
    border-bottom: 1px solid #dee2e6;
}

.rc-table tbody tr:hover {
    background-color: #f8f9fa;
}

.rc-table td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.rc-cell-stack {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.rc-term-metric {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    font-size: 0.75rem;
}

.rc-term-metric--center {
    justify-content: center;
}

.rc-term-pill {
    display: inline-block;
    background: #e7f1ff;
    color: #0066cc;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
}

.rc-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
}

.rc-badge--tiny {
    padding: 0.2rem 0.4rem;
    font-size: 0.65rem;
}

.rc-badge--green {
    background: #d4edda;
    color: #155724;
}

.rc-badge--yellow {
    background: #fff3cd;
    color: #856404;
}

.rc-badge--blue {
    background: #d1ecf1;
    color: #0c5460;
}

.rc-badge--red {
    background: #f8d7da;
    color: #721c24;
}

.rc-badge--gray {
    background: #e2e3e5;
    color: #383d41;
}

.rc-avg {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 700;
    white-space: nowrap;
}

.rc-avg--small {
    font-size: 0.7rem;
}

.rc-avg--overall {
    font-size: 0.8rem;
}

.rc-avg--high {
    background: #d4edda;
    color: #155724;
}

.rc-avg--mid {
    background: #fff3cd;
    color: #856404;
}

.rc-avg--low {
    background: #f8d7da;
    color: #721c24;
}

.rc-stack-label {
    display: block;
    font-size: 0.65rem;
    color: #6c757d;
}

.rc-position {
    display: inline-block;
    background: #cfe2ff;
    color: #084298;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}

.rc-position--small {
    font-size: 0.7rem;
    padding: 0.15rem 0.4rem;
}

.rc-dash {
    color: #adb5bd;
}

.rc-student-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rc-student-cell__info {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}

.rc-student-cell__name {
    font-weight: 600;
    color: #212529;
}

.rc-student-cell__id {
    font-size: 0.75rem;
    color: #6c757d;
}

.rc-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: #fff;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.rc-avatar--blue {
    background: #0d6efd;
}

.rc-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.rc-course-cell {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.rc-meta {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.rc-meta--block {
    display: block;
}

.rc-meta__year {
    font-weight: 600;
    color: #212529;
}

.rc-table__actions {
    text-align: center;
}

.rc-student-actions .btn {
    padding: 0.35rem 0.6rem;
    font-size: 0.8rem;
}

/* Empty state */
.rc-empty {
    text-align: center;
    padding: 3rem 2rem;
}

.rc-empty__icon {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.rc-empty__title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.rc-empty__sub {
    font-size: 0.9rem;
    color: #adb5bd;
}

/* Pagination */
.rc-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1.5rem;
    gap: 0.5rem;
}

.rc-pagination a,
.rc-pagination span {
    padding: 0.5rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #0d6efd;
    text-decoration: none;
    font-size: 0.85rem;
}

.rc-pagination a:hover {
    background: #f8f9fa;
}

.rc-pagination .active span {
    background: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}

/* Generation Grid */
.rc-gen-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.rc-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.rc-field--action {
    align-self: flex-end;
}

.rc-label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #212529;
}

.rc-select {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.9rem;
}

.rc-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1rem;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}

.rc-btn--primary {
    background: #0d6efd;
    color: #fff;
}

.rc-btn--primary:hover {
    background: #0a58ca;
}

.rc-btn--teal {
    background: #20c997;
    color: #fff;
}

.rc-btn--teal:hover {
    background: #1aa179;
}

.rc-btn--warning {
    background: #ffc107;
    color: #000;
}

.rc-btn--warning:hover {
    background: #e0a800;
}

.rc-hint {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.5rem;
}

/* Multi-select dropdown */
.ms-dropdown {
    position: relative;
    display: inline-block;
    width: 100%;
}

.ms-trigger {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: #fff;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ms-trigger:hover {
    border-color: #adb5bd;
}

.ms-trigger.ms-open {
    border-color: #0d6efd;
}

.ms-panel {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
}

.ms-search-wrap {
    padding: 0.5rem;
    border-bottom: 1px solid #dee2e6;
    position: relative;
}

.ms-search {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.9rem;
}

.ms-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ms-group-label {
    padding: 0.5rem;
    font-weight: 600;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.ms-option {
    padding: 0.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ms-option:hover {
    background: #f8f9fa;
}

.ms-option.ms-selected {
    background: #e7f1ff;
}

.ms-option__check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    color: transparent;
    font-size: 0.75rem;
}

.ms-option.ms-selected .ms-option__check {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.ms-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.ms-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #e7f1ff;
    color: #0066cc;
    padding: 0.3rem 0.6rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.ms-pill__remove {
    cursor: pointer;
    color: #0066cc;
}

.ms-pill__remove:hover {
    color: #cc0000;
}

.ms-empty {
    padding: 1rem;
    text-align: center;
    color: #6c757d;
}

.ms-no-results {
    padding: 1rem;
    text-align: center;
    color: #6c757d;
}
</style>