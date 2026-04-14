@extends('teacher.layout')

@section('styles')
<style>
.exams-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    margin-top: 1rem;
    border-radius: 15px;
}

.exams-header h1 { font-weight: 700; margin-bottom: 0.5rem; }
.exams-header p  { opacity: 0.9; margin-bottom: 0; }

.exams-card, .filters-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,.07);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.exams-card .card-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white; border: none; padding: 1.25rem 1.5rem;
}

.filters-card .card-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white; border: none; padding: 1.25rem 1.5rem;
}

.form-label { font-weight: 600; color: #374151; margin-bottom: .5rem; }

.form-control, .form-select {
    border: 2px solid #e5e7eb; border-radius: 8px; padding: .65rem .75rem;
    transition: border-color .2s, box-shadow .2s;
}
.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

/* ── Table ── */
.exams-table { overflow-x: auto; -webkit-overflow-scrolling: touch; }

.exams-table table {
    width: 100%; min-width: 720px; border-collapse: collapse; margin: 0;
}

.exams-table thead tr {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}
.exams-table thead th {
    color: #fff; padding: 1rem .9rem; font-size: .72rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    white-space: nowrap; border: none;
}

.exams-table tbody td {
    padding: .9rem; border-bottom: 1px solid #e5e7eb;
    vertical-align: middle; font-size: .9rem; color: #374151;
}
.exams-table tbody tr:last-child td { border-bottom: none; }
.exams-table tbody tr:hover { background: #f0f9ff; }

/* ── Badges ── */
.badge-pill {
    display: inline-block; padding: .3rem .65rem; border-radius: 20px;
    font-size: .72rem; font-weight: 700; line-height: 1;
    white-space: nowrap; margin: .15rem .15rem 0 0;
}
.bg-course   { background:#6366f1; color:#fff; }
.bg-type     { background:#06b6d4; color:#fff; }
.bg-public   { background:#10b981; color:#fff; }
.bg-private  { background:#f59e0b; color:#fff; }

/* ── Action buttons ── */
.btn-act {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .4rem .75rem; border-radius: 6px; border: none;
    font-size: .78rem; font-weight: 600; text-decoration: none;
    transition: transform .15s, box-shadow .15s;
    white-space: nowrap;
}
.btn-act:hover { transform: translateY(-1px); text-decoration: none; }
.btn-view      { background:#3b82f6; color:#fff; }
.btn-view:hover{ background:#1d4ed8; color:#fff; box-shadow:0 4px 10px rgba(59,130,246,.35); }
.btn-addq      { background:#06b6d4; color:#fff; }
.btn-addq:hover{ background:#0891b2; color:#fff; box-shadow:0 4px 10px rgba(6,182,212,.35); }

/* ── Pagination ── */
.page-link { color:#3b82f6; border-radius:6px !important; margin:0 2px; }
.page-item.active .page-link { background:#3b82f6; border-color:#3b82f6; }
.page-link:focus { box-shadow: none; }

/* ── Empty state ── */
.empty-state {
    text-align: center; padding: 3.5rem 2rem;
    border-radius: 12px; border: 2px dashed #e5e7eb;
    margin: 1.5rem;
}
.empty-state i { color:#d1d5db; }

.pagination-bar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .5rem;
    padding: .9rem 1.25rem; border-top: 1px solid #e5e7eb;
    font-size: .875rem; color: #6b7280;
}

/* ── Responsive ── */
@media (max-width: 576px) {
    .exams-header { padding: 1.25rem; }
    .exams-header h1 { font-size: 1.35rem; }
    .exams-table thead th,
    .exams-table tbody td { padding: .65rem .5rem; font-size: .8rem; }
    .btn-act { padding: .35rem .55rem; font-size: .72rem; }
}
</style>
@endsection

@section('content')
<div class="container-fluid" style="padding: 1.5rem 1rem 2rem;">

    {{-- ── Header ── --}}
    <div class="exams-header px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-clipboard-data"></i> My Exams</h1>
                <p class="mb-0">View and manage question banks for examinations</p>
            </div>
        </div>
    </div>

    {{-- ── Search / Filter ── --}}
    <div class="filters-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Search Exams</h5>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search by Exam Title, Course</label>
                    <div class="input-group">
                        <span class="input-group-text border-2 border-end-0 bg-white" style="border-color:#e5e7eb;">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="liveSearch" class="form-control border-2 border-start-0 ps-0"
                               placeholder="Type to search…" autocomplete="off"
                               style="border-color:#e5e7eb;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter by Course</label>
                    <select id="courseFilter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Exams Table ── --}}
    <div class="exams-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Exams</h5>
            <span id="headerCount" class="badge bg-white text-primary fw-bold">
                {{ $allExams->count() }} total
            </span>
        </div>

        <div id="tableWrapper">
            @if($allExams->count() > 0)
                <div class="exams-table">
                    <table id="examsTable">
                        <thead>
                            <tr>
                                <th style="width:26%">Title</th>
                                <th style="width:20%">Course</th>
                                <th style="width:12%">Date</th>
                                <th style="width:10%">Type</th>
                                <th style="width:8%; text-align:center">Marks</th>
                                <th style="width:9%; text-align:center">Status</th>
                                <th style="width:15%; text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="examsTbody">
                            {{-- Rows injected by JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- Pagination bar --}}
                <div class="pagination-bar">
                    <span>
                        Showing <strong id="rangeStart">1</strong>–<strong id="rangeEnd">10</strong>
                        of <strong id="totalCount">0</strong> exams
                    </span>
                    <nav aria-label="Exam pagination">
                        <ul class="pagination pagination-sm mb-0" id="paginationUl"></ul>
                    </nav>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-clipboard-x display-1"></i>
                    <h4 class="mt-4">No Exams Found</h4>
                    <p class="text-muted">You don't have any exams available yet.</p>
                </div>
            @endif
        </div>

        {{-- No-results placeholder (hidden by default) --}}
        <div id="noResults" class="empty-state" style="display:none;">
            <i class="bi bi-search display-4"></i>
            <h5 class="mt-3">No exams match your search</h5>
            <p class="text-muted">Try different keywords or clear the filters.</p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function () {
    // ── All exams passed from server (full collection, no pagination limit) ──
    const ALL_EXAMS = @json($allExams);
    const PER_PAGE  = 10;

    let filtered    = [...ALL_EXAMS];
    let currentPage = 1;

    const searchInput   = document.getElementById('liveSearch');
    const courseFilter  = document.getElementById('courseFilter');
    const clearBtn      = document.getElementById('clearFilters');
    const tbody         = document.getElementById('examsTbody');
    const paginationUl  = document.getElementById('paginationUl');
    const noResults     = document.getElementById('noResults');
    const tableWrapper  = document.getElementById('tableWrapper');
    const headerCount   = document.getElementById('headerCount');

    if (!tbody) return; // No exams at all

    // ── Helpers ──────────────────────────────────────────────────────────────

    function debounce(fn, ms) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function courseBadges(courses) {
        if (!courses || courses.length === 0)
            return '<span class="text-muted" style="font-size:.82rem;">No course</span>';
        let html = courses.slice(0, 2)
            .map(c => `<span class="badge-pill bg-course">${escHtml(c.course_name)}</span>`)
            .join('');
        if (courses.length > 2)
            html += `<span class="badge-pill bg-course">+${courses.length - 2}</span>`;
        return html;
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Filter ───────────────────────────────────────────────────────────────

    function applyFilters() {
        const q  = searchInput.value.toLowerCase().trim();
        const cId = courseFilter.value;

        filtered = ALL_EXAMS.filter(exam => {
            const matchSearch = !q ||
                (exam.title  && exam.title.toLowerCase().includes(q)) ||
                (exam.exam_type && exam.exam_type.toLowerCase().includes(q)) ||
                (exam.description && exam.description.toLowerCase().includes(q)) ||
                (exam.courses && exam.courses.some(c =>
                    c.course_name && c.course_name.toLowerCase().includes(q)
                ));

            const matchCourse = !cId ||
                (exam.courses && exam.courses.some(c => String(c.id) === cId));

            return matchSearch && matchCourse;
        });

        currentPage = 1;
        render();
    }

    // ── Render ───────────────────────────────────────────────────────────────

    function render() {
        const total      = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        currentPage      = Math.min(currentPage, totalPages);

        const start = (currentPage - 1) * PER_PAGE;
        const end   = Math.min(start + PER_PAGE, total);
        const page  = filtered.slice(start, end);

        // Show/hide table vs no-results
        const hasRows = total > 0;
        tableWrapper.style.display  = hasRows ? '' : 'none';
        noResults.style.display     = hasRows ? 'none' : '';

        headerCount.textContent = `${total} exam${total !== 1 ? 's' : ''}`;

        if (!hasRows) { renderPagination(0, 0); return; }

        // Fill tbody
        tbody.innerHTML = page.map(exam => {
            const statusBadge = exam.is_public
                ? '<span class="badge-pill bg-public">Public</span>'
                : '<span class="badge-pill bg-private">Private</span>';

            return `
            <tr>
                <td>
                    <strong style="color:#111827;">${escHtml(exam.title)}</strong>
                    ${exam.description
                        ? `<div style="font-size:.8rem;color:#6b7280;margin-top:.2rem;">
                               ${escHtml(exam.description.substring(0,60))}${exam.description.length>60?'…':''}
                           </div>`
                        : ''}
                </td>
                <td>${courseBadges(exam.courses)}</td>
                <td style="white-space:nowrap;">${formatDate(exam.class_date)}</td>
                <td><span class="badge-pill bg-type">${escHtml((exam.exam_type||'').toUpperCase())}</span></td>
                <td style="text-align:center;font-weight:700;font-size:1rem;">${exam.total_marks ?? '—'}</td>
                <td style="text-align:center;">${statusBadge}</td>
                <td style="text-align:right;">
                    <a href="/teacher/exams/${exam.id}" class="btn-act btn-view">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <a href="/teacher/exams/${exam.id}/questions" class="btn-act btn-addq">
                        <i class="bi bi-plus-circle"></i> Add Q
                    </a>
                </td>
            </tr>`;
        }).join('');

        // Update range label
        document.getElementById('rangeStart').textContent = start + 1;
        document.getElementById('rangeEnd').textContent   = end;
        document.getElementById('totalCount').textContent = total;

        renderPagination(totalPages, total);
    }

    // ── Pagination ────────────────────────────────────────────────────────────

    function renderPagination(totalPages, total) {
        if (!paginationUl) return;
        paginationUl.innerHTML = '';
        if (total === 0 || totalPages <= 1) return;

        const mkItem = (label, page, disabled, active) => {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = label;
            if (!disabled && !active) {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = page;
                    render();
                    // Scroll to table top
                    document.querySelector('.exams-card').scrollIntoView({behavior:'smooth', block:'start'});
                });
            }
            li.appendChild(a);
            return li;
        };

        paginationUl.appendChild(mkItem('&laquo;', currentPage - 1, currentPage === 1, false));

        // Show at most 5 page numbers around current
        let startP = Math.max(1, currentPage - 2);
        let endP   = Math.min(totalPages, startP + 4);
        startP     = Math.max(1, endP - 4);

        if (startP > 1) {
            paginationUl.appendChild(mkItem('1', 1, false, false));
            if (startP > 2)
                paginationUl.appendChild(mkItem('…', null, true, false));
        }

        for (let p = startP; p <= endP; p++)
            paginationUl.appendChild(mkItem(p, p, false, p === currentPage));

        if (endP < totalPages) {
            if (endP < totalPages - 1)
                paginationUl.appendChild(mkItem('…', null, true, false));
            paginationUl.appendChild(mkItem(totalPages, totalPages, false, false));
        }

        paginationUl.appendChild(mkItem('&raquo;', currentPage + 1, currentPage === totalPages, false));
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    searchInput.addEventListener('input', debounce(applyFilters, 250));
    courseFilter.addEventListener('change', applyFilters);
    clearBtn.addEventListener('click', () => {
        searchInput.value  = '';
        courseFilter.value = '';
        applyFilters();
    });

    // ── Initial render ────────────────────────────────────────────────────────
    applyFilters();
})();
</script>
@endsection