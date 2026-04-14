@extends('teacher.layout')

@section('content')
<style>
/* ── Layout ── */
.exams-wrap { padding: 1.5rem 1rem 3rem; }

/* ── Header banner ── */
.exams-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff;
    padding: 1.75rem 2rem;
    border-radius: 14px;
    margin-bottom: 1.5rem;
}
.exams-header h1 { font-size: 1.75rem; font-weight: 700; margin: 0 0 .25rem; }
.exams-header p  { opacity: .9; margin: 0; font-size: .95rem; }

/* ── Cards ── */
.ex-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.ex-card-head {
    padding: 1rem 1.25rem;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
}
.ex-card-head.blue {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff; border-bottom: none;
}
.ex-card-head.purple {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff; border-bottom: none;
}
.ex-card-head h5 { margin: 0; font-size: 1rem; font-weight: 700; }

/* ── Filter card body ── */
.filter-body { padding: 1.25rem; }
.filter-body .form-label { font-weight: 600; color: #374151; font-size: .875rem; margin-bottom: .4rem; }
.filter-body .form-control,
.filter-body .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: .6rem .85rem;
    font-size: .9rem;
    transition: border-color .2s, box-shadow .2s;
}
.filter-body .form-control:focus,
.filter-body .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    outline: none;
}
.filter-body .input-group-text {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-right: none;
    border-radius: 8px 0 0 8px;
    color: #9ca3af;
}
.filter-body .input-group .form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
    padding-left: .5rem;
}
.btn-clear {
    border: 2px solid #d1d5db;
    background: #fff;
    color: #6b7280;
    border-radius: 8px;
    padding: .6rem 1rem;
    font-size: .875rem;
    font-weight: 600;
    transition: all .2s;
    cursor: pointer;
    white-space: nowrap;
    width: 100%;
}
.btn-clear:hover { border-color: #ef4444; color: #ef4444; }

/* ── Table ── */
.table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
#examsTable {
    width: 100%; min-width: 700px;
    border-collapse: collapse; margin: 0;
    font-size: .9rem;
}
#examsTable thead tr {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}
#examsTable thead th {
    color: #fff; padding: .9rem 1rem;
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    white-space: nowrap; border: none;
}
#examsTable tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle; color: #374151;
}
#examsTable tbody tr:last-child td { border-bottom: none; }
#examsTable tbody tr { transition: background .15s; }
#examsTable tbody tr:hover { background: #f0f7ff; }

/* ── Badges ── */
.badge-pill {
    display: inline-block;
    padding: .28rem .65rem;
    border-radius: 20px;
    font-size: .7rem; font-weight: 700;
    line-height: 1; white-space: nowrap;
    margin: .1rem .1rem 0 0;
}
.bp-course  { background: #6366f1; color: #fff; }
.bp-type    { background: #06b6d4; color: #fff; }
.bp-public  { background: #10b981; color: #fff; }
.bp-private { background: #f59e0b; color: #fff; }

/* ── Action buttons ── */
.btn-act {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .38rem .7rem; border-radius: 6px; border: none;
    font-size: .76rem; font-weight: 600;
    text-decoration: none; cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    white-space: nowrap; line-height: 1.3;
}
.btn-act:hover { transform: translateY(-1px); text-decoration: none; }
.btn-view      { background: #3b82f6; color: #fff; margin-right: .3rem; }
.btn-view:hover{ background: #1d4ed8; color: #fff; box-shadow: 0 4px 10px rgba(59,130,246,.35); }
.btn-addq      { background: #06b6d4; color: #fff; }
.btn-addq:hover{ background: #0891b2; color: #fff; box-shadow: 0 4px 10px rgba(6,182,212,.35); }

/* ── Pagination bar ── */
.pag-bar {
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap;
    gap: .5rem; padding: .85rem 1.25rem;
    border-top: 1px solid #e5e7eb;
    font-size: .85rem; color: #6b7280;
}
.pag-bar strong { color: #111827; }
#paginationUl { display: flex; list-style: none; margin: 0; padding: 0; gap: 3px; }
#paginationUl .page-item .page-link {
    display: flex; align-items: center; justify-content: center;
    min-width: 32px; height: 32px; padding: 0 8px;
    border-radius: 6px; border: 1.5px solid #e5e7eb;
    color: #374151; font-size: .82rem; font-weight: 600;
    background: #fff; text-decoration: none; transition: all .15s;
}
#paginationUl .page-item .page-link:hover { border-color: #3b82f6; color: #3b82f6; }
#paginationUl .page-item.active .page-link { background: #3b82f6; border-color: #3b82f6; color: #fff; }
#paginationUl .page-item.disabled .page-link { opacity: .45; pointer-events: none; }

/* ── Empty / no-results ── */
.empty-state {
    text-align: center; padding: 3rem 2rem;
    border-radius: 10px; border: 2px dashed #e5e7eb;
    margin: 1.25rem;
}
.empty-state i { color: #d1d5db; }

/* ── Badge counter in header ── */
.count-badge {
    background: rgba(255,255,255,.2);
    color: #fff; border-radius: 20px;
    padding: .2rem .7rem; font-size: .8rem; font-weight: 700;
}

/* ── Responsive ── */
@media (max-width: 576px) {
    .exams-header { padding: 1.1rem 1.25rem; }
    .exams-header h1 { font-size: 1.35rem; }
    #examsTable thead th,
    #examsTable tbody td { padding: .65rem .6rem; font-size: .78rem; }
    .btn-act { padding: .32rem .55rem; font-size: .72rem; }
}
</style>

<div class="exams-wrap">

    {{-- ── Header ── --}}
    <div class="exams-header">
        <h1><i class="bi bi-clipboard-data"></i> My Exams</h1>
        <p>View and manage question banks for examinations</p>
    </div>

    {{-- ── Search / Filter ── --}}
    <div class="ex-card">
        <div class="ex-card-head purple">
            <h5><i class="bi bi-funnel me-2"></i>Search Exams</h5>
        </div>
        <div class="filter-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search by Exam Title, Course</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="liveSearch" class="form-control"
                               placeholder="Type to search…" autocomplete="off">
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
                <div class="col-md-3">
                    <button id="clearFilters" class="btn-clear">
                        <i class="bi bi-x-circle me-1"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Exams Table ── --}}
    <div class="ex-card">
        <div class="ex-card-head blue">
            <h5><i class="bi bi-list-ul me-2"></i>All Exams</h5>
            <span class="count-badge" id="headerCount">{{ $allExams->count() }} exams</span>
        </div>

        @if($allExams->count() > 0)
            <div id="tableSection">
                <div class="table-scroll">
                    <table id="examsTable">
                        <thead>
                            <tr>
                                <th style="width:26%">Title</th>
                                <th style="width:20%">Course</th>
                                <th style="width:12%">Date</th>
                                <th style="width:9%">Type</th>
                                <th style="width:8%;text-align:center">Marks</th>
                                <th style="width:9%;text-align:center">Status</th>
                                <th style="width:16%;text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="examsTbody"></tbody>
                    </table>
                </div>
                <div class="pag-bar">
                    <span>
                        Showing <strong id="rangeStart">—</strong>–<strong id="rangeEnd">—</strong>
                        of <strong id="totalCount">—</strong> exams
                    </span>
                    <nav><ul id="paginationUl"></ul></nav>
                </div>
            </div>

            <div id="noResults" class="empty-state" style="display:none;">
                <i class="bi bi-search display-4"></i>
                <h5 class="mt-3">No exams match your search</h5>
                <p class="text-muted mb-0">Try different keywords or clear the filters.</p>
            </div>

        @else
            <div class="empty-state">
                <i class="bi bi-clipboard-x display-1"></i>
                <h4 class="mt-4">No Exams Found</h4>
                <p class="text-muted">You don't have any exams available yet.</p>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
(function () {
    const ALL_EXAMS = @json($allExams);
    const PER_PAGE  = 10;

    let filtered    = [...ALL_EXAMS];
    let currentPage = 1;

    const searchInput  = document.getElementById('liveSearch');
    const courseFilter = document.getElementById('courseFilter');
    const clearBtn     = document.getElementById('clearFilters');
    const tbody        = document.getElementById('examsTbody');
    const paginationUl = document.getElementById('paginationUl');
    const noResults    = document.getElementById('noResults');
    const tableSection = document.getElementById('tableSection');
    const headerCount  = document.getElementById('headerCount');

    if (!tbody) return;

    function debounce(fn, ms) {
        let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
    }

    function fmtDate(s) {
        if (!s) return '—';
        const d = new Date(s);
        return d.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
    }

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function courseBadges(courses) {
        if (!courses || !courses.length)
            return '<span style="font-size:.8rem;color:#9ca3af;">No course</span>';
        let h = courses.slice(0,2).map(c =>
            `<span class="badge-pill bp-course">${esc(c.course_name)}</span>`).join('');
        if (courses.length > 2)
            h += `<span class="badge-pill bp-course">+${courses.length-2}</span>`;
        return h;
    }

    function applyFilters() {
        const q   = (searchInput.value || '').toLowerCase().trim();
        const cId = courseFilter.value;

        filtered = ALL_EXAMS.filter(e => {
            const ms = !q ||
                (e.title       || '').toLowerCase().includes(q) ||
                (e.exam_type   || '').toLowerCase().includes(q) ||
                (e.description || '').toLowerCase().includes(q) ||
                (e.courses && e.courses.some(c =>
                    (c.course_name || '').toLowerCase().includes(q)));

            const mc = !cId ||
                (e.courses && e.courses.some(c => String(c.id) === cId));

            return ms && mc;
        });

        currentPage = 1;
        render();
    }

    function render() {
        const total      = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        currentPage      = Math.min(currentPage, totalPages);

        const start = (currentPage - 1) * PER_PAGE;
        const end   = Math.min(start + PER_PAGE, total);
        const page  = filtered.slice(start, end);

        const hasRows = total > 0;
        tableSection.style.display = hasRows ? '' : 'none';
        noResults.style.display    = hasRows ? 'none' : '';
        headerCount.textContent    = `${total} exam${total !== 1 ? 's' : ''}`;

        if (!hasRows) { paginationUl.innerHTML = ''; return; }

        tbody.innerHTML = page.map(exam => {
            const status = exam.is_public
                ? '<span class="badge-pill bp-public">Public</span>'
                : '<span class="badge-pill bp-private">Private</span>';

            const desc = exam.description
                ? `<div style="font-size:.78rem;color:#6b7280;margin-top:.15rem;">${esc(exam.description.substring(0,65))}${exam.description.length>65?'…':''}</div>`
                : '';

            return `<tr>
                <td><strong style="color:#111827;">${esc(exam.title)}</strong>${desc}</td>
                <td>${courseBadges(exam.courses)}</td>
                <td style="white-space:nowrap;">${fmtDate(exam.class_date)}</td>
                <td><span class="badge-pill bp-type">${esc((exam.exam_type||'').toUpperCase())}</span></td>
                <td style="text-align:center;font-weight:700;font-size:.95rem;">${exam.total_marks ?? '—'}</td>
                <td style="text-align:center;">${status}</td>
                <td style="text-align:right;">
                    
                    <a href="/teacher/exams/${exam.id}/questions" class="btn-act btn-addq">
                        <i class="bi bi-plus-circle"></i> Add Q
                    </a>
                </td>
            </tr>`;
        }).join('');

        document.getElementById('rangeStart').textContent = start + 1;
        document.getElementById('rangeEnd').textContent   = end;
        document.getElementById('totalCount').textContent = total;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        paginationUl.innerHTML = '';
        if (totalPages <= 1) return;

        function mkLi(label, page, disabled, active) {
            const li = document.createElement('li');
            li.className = 'page-item' + (disabled?' disabled':'') + (active?' active':'');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = label;
            if (!disabled && !active) {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = page;
                    render();
                    document.querySelector('.ex-card').scrollIntoView({behavior:'smooth',block:'start'});
                });
            }
            li.appendChild(a);
            return li;
        }

        paginationUl.appendChild(mkLi('&laquo;', currentPage-1, currentPage===1, false));

        let sp = Math.max(1, currentPage-2);
        let ep = Math.min(totalPages, sp+4);
        sp     = Math.max(1, ep-4);

        if (sp > 1) {
            paginationUl.appendChild(mkLi('1', 1, false, false));
            if (sp > 2) paginationUl.appendChild(mkLi('…', null, true, false));
        }
        for (let p = sp; p <= ep; p++)
            paginationUl.appendChild(mkLi(p, p, false, p === currentPage));
        if (ep < totalPages) {
            if (ep < totalPages-1) paginationUl.appendChild(mkLi('…', null, true, false));
            paginationUl.appendChild(mkLi(totalPages, totalPages, false, false));
        }

        paginationUl.appendChild(mkLi('&raquo;', currentPage+1, currentPage===totalPages, false));
    }

    searchInput.addEventListener('input', debounce(applyFilters, 220));
    courseFilter.addEventListener('change', applyFilters);
    clearBtn.addEventListener('click', () => {
        searchInput.value  = '';
        courseFilter.value = '';
        applyFilters();
    });

    applyFilters();
})();
</script>
@endsection