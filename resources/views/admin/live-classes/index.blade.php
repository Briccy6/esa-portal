@extends('admin.layout')

@section('title', 'Live Classes')

@section('content')

<style>
.lc-wrap { padding: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

/* ── Card ── */
.lc-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.09);
    border: 1px solid #e8ecf0;
    overflow: hidden;
}

/* ── Header ── */
.lc-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap;
    gap: 12px;
}
.lc-head-left { display: flex; align-items: center; gap: 14px; }
.lc-head-icon {
    width: 44px; height: 44px;
    background: #eff6ff;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    color: #2563eb; font-size: 19px; flex-shrink: 0;
}
.lc-head-title { font-size: 1.15rem; font-weight: 700; color: #111827; margin: 0; line-height: 1.3; }
.lc-head-sub   { font-size: 0.78rem; color: #6b7280; margin: 2px 0 0; }

.lc-btn-new {
    display: inline-flex; align-items: center; gap: 7px;
    background: #2563eb; color: #fff !important;
    border: none; border-radius: 9px;
    padding: 9px 18px;
    font-size: 0.84rem; font-weight: 600;
    text-decoration: none !important; cursor: pointer;
    box-shadow: 0 2px 8px rgba(37,99,235,0.28);
}
.lc-btn-new:hover { background: #1d4ed8; }

/* ── Alert ── */
.lc-alert {
    display: flex; align-items: center; gap: 10px;
    margin: 16px 24px 0;
    padding: 11px 15px;
    background: #ecfdf5; border: 1px solid #6ee7b7;
    border-radius: 9px; color: #065f46;
    font-size: 0.85rem; font-weight: 500;
}

/* ── Toolbar ── */
.lc-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 24px;
    background: #f9fafb;
    border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap; gap: 10px;
}
.lc-search-box {
    position: relative; flex: 1; max-width: 320px; min-width: 160px;
}
.lc-search-box i {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    color: #9ca3af; font-size: 13px; pointer-events: none;
}
.lc-search-input {
    width: 100%;
    padding: 8px 14px 8px 36px !important;
    border: 1.5px solid #e5e7eb !important;
    border-radius: 8px !important;
    font-size: 0.85rem !important;
    color: #111827 !important;
    background: #fff !important;
    outline: none !important;
    box-shadow: none !important;
    box-sizing: border-box;
    transition: border-color 0.2s;
}
.lc-search-input:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important;
}
.lc-count { font-size: 0.79rem; color: #6b7280; font-weight: 500; white-space: nowrap; }
.lc-count b { color: #111827; }

/* ── Table ── */
.lc-table-wrap { overflow-x: auto; }
.lc-table { width: 100%; border-collapse: collapse !important; font-size: 0.875rem; }

.lc-table thead tr { background: #f8fafc !important; }
.lc-table th {
    padding: 11px 18px !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #6b7280 !important;
    text-align: left !important;
    border-bottom: 2px solid #e8ecf0 !important;
    border-top: none !important;
    white-space: nowrap;
    background: #f8fafc !important;
}
.lc-table td {
    padding: 13px 18px !important;
    border-bottom: 1px solid #f0f2f5 !important;
    border-top: none !important;
    color: #1f2937 !important;
    vertical-align: middle !important;
    background: transparent;
}
.lc-table tbody tr:last-child td { border-bottom: none !important; }
.lc-table tbody tr:hover td { background: #f5f8ff !important; }

.td-title { font-weight: 600; color: #111827; display: block; }
.td-date  { font-weight: 600; color: #111827; font-size: 0.83rem; white-space: nowrap; }
.td-time  { font-size: 0.75rem; color: #6b7280; margin-top: 2px; }

/* ── Badges ── */
.lc-badge {
    display: inline-flex !important; align-items: center; gap: 6px;
    padding: 4px 11px !important;
    border-radius: 999px !important;
    font-size: 0.73rem !important; font-weight: 700 !important;
    white-space: nowrap;
    border: none !important;
}
.lc-badge .bd { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; display: inline-block; }

.badge-sched { background: #fef9c3 !important; color: #854d0e !important; }
.badge-sched .bd { background: #ca8a04; }
.badge-live  { background: #dcfce7 !important; color: #14532d !important; }
.badge-live .bd  { background: #16a34a; animation: lc-blink 1.4s infinite; }
.badge-end   { background: #f3f4f6 !important; color: #4b5563 !important; }
.badge-end .bd   { background: #6b7280; }
.badge-cancel{ background: #fee2e2 !important; color: #7f1d1d !important; }
.badge-cancel .bd{ background: #dc2626; }

@keyframes lc-blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

/* ── Action buttons ── */
.lc-actions { display: flex; align-items: center; gap: 5px; flex-wrap: nowrap; }
.lc-btn {
    display: inline-flex !important; align-items: center; gap: 5px;
    padding: 5px 10px !important;
    border-radius: 7px !important; border: none !important;
    font-size: 0.76rem !important; font-weight: 600 !important;
    cursor: pointer; text-decoration: none !important;
    white-space: nowrap; line-height: 1.4 !important;
    transition: opacity 0.15s, transform 0.12s;
}
.lc-btn:hover { opacity: 0.82; transform: translateY(-1px); }

.lc-btn-view   { background: #eff6ff !important; color: #1d4ed8 !important; }
.lc-btn-edit   { background: #fefce8 !important; color: #a16207 !important; }
.lc-btn-start  { background: #dcfce7 !important; color: #15803d !important; }
.lc-btn-end    { background: #fee2e2 !important; color: #b91c1c !important; }
.lc-btn-delete { background: #fee2e2 !important; color: #b91c1c !important; }

/* ── Pagination wrapper ── */
.lc-pager {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 24px;
    border-top: 1px solid #f0f2f5;
    background: #f9fafb;
    flex-wrap: wrap; gap: 10px;
}
.lc-pager .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; }
.lc-pager .pagination li > a,
.lc-pager .pagination li > span {
    display: inline-flex !important; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 10px;
    border-radius: 8px !important;
    border: 1.5px solid #e5e7eb !important;
    background: #fff !important;
    color: #374151 !important;
    font-size: 0.82rem; font-weight: 600;
    text-decoration: none !important;
    transition: all 0.15s;
}
.lc-pager .pagination li.active > span,
.lc-pager .pagination li.active > a {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(37,99,235,0.28);
}
.lc-pager .pagination li > a:hover {
    border-color: #2563eb !important;
    color: #2563eb !important;
    background: #eff6ff !important;
}
.lc-pager .pagination li.disabled > span,
.lc-pager .pagination li.disabled > a { opacity: 0.4; pointer-events: none; }
.lc-page-info { font-size: 0.79rem; color: #6b7280; font-weight: 500; }

/* search highlight */
mark { background: #fde68a; border-radius: 3px; padding: 0 2px; color: inherit; font-style: normal; }
</style>

<div class="lc-wrap">
<div class="lc-card">

    {{-- ── Header ── --}}
    <div class="lc-head">
        <div class="lc-head-left">
            <div class="lc-head-icon"><i class="fas fa-broadcast-tower"></i></div>
            <div>
                <p class="lc-head-title">Live Classes</p>
                <p class="lc-head-sub">Manage and monitor all scheduled live sessions</p>
            </div>
        </div>
        <a href="{{ route('admin.live-classes.create') }}" class="lc-btn-new">
            <i class="fas fa-plus"></i> Schedule New Class
        </a>
    </div>

    {{-- ── Success alert ── --}}
    @if(session('success'))
    <div class="lc-alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ── Toolbar ── --}}
    <div class="lc-toolbar">
        <div class="lc-search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="lcSearch" class="lc-search-input" placeholder="Search title, course, teacher…" autocomplete="off">
        </div>
        <span class="lc-count">Showing <b id="lcCount">{{ $liveClasses->count() }}</b> of {{ $liveClasses->total() }} classes</span>
    </div>

    {{-- ── Table ── --}}
    <div class="lc-table-wrap">
        <table class="lc-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Course</th>
                    <th>Branch</th>
                    <th>Teacher</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="lcBody">
                @forelse($liveClasses as $liveClass)
                <tr>
                    <td><span class="td-title">{{ $liveClass->title }}</span></td>
                    <td>{{ $liveClass->courses->pluck('course_name')->implode(', ') ?: '—' }}</td>
                    <td>{{ $liveClass->branches->pluck('name')->implode(', ') ?: '—' }}</td>
                    <td>{{ $liveClass->teacher?->teacher_name ?? '—' }}</td>
                    <td>
                        <div class="td-date">{{ $liveClass->start_time->format('M j, Y') }}</div>
                        <div class="td-time">{{ $liveClass->start_time->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="td-date">{{ $liveClass->end_time->format('M j, Y') }}</div>
                        <div class="td-time">{{ $liveClass->end_time->format('H:i') }}</div>
                    </td>
                    <td>
                        @switch($liveClass->status)
                            @case('scheduled')
                                <span class="lc-badge badge-sched"><span class="bd"></span>Scheduled</span>
                                @break
                            @case('active')
                                <span class="lc-badge badge-live"><span class="bd"></span>Live Now</span>
                                @break
                            @case('ended')
                                <span class="lc-badge badge-end"><span class="bd"></span>Ended</span>
                                @break
                            @case('cancelled')
                                <span class="lc-badge badge-cancel"><span class="bd"></span>Cancelled</span>
                                @break
                            @default
                                <span class="lc-badge badge-end"><span class="bd"></span>{{ $liveClass->status }}</span>
                        @endswitch
                    </td>
                    <td>
                        <div class="lc-actions">
                            <a href="{{ route('admin.live-classes.show', $liveClass) }}" class="lc-btn lc-btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.live-classes.edit', $liveClass) }}" class="lc-btn lc-btn-edit">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            @if($liveClass->status === 'scheduled')
                                <form action="{{ route('admin.live-classes.start', $liveClass) }}" method="POST" style="display:inline;margin:0;padding:0;">
                                    @csrf
                                    <button type="submit" class="lc-btn lc-btn-start" onclick="return confirm('Start this live class now?')">
                                        <i class="fas fa-play"></i> Start
                                    </button>
                                </form>
                            @endif
                            @if($liveClass->status === 'active')
                                <form action="{{ route('admin.live-classes.end', $liveClass) }}" method="POST" style="display:inline;margin:0;padding:0;">
                                    @csrf
                                    <button type="submit" class="lc-btn lc-btn-end" onclick="return confirm('End this live class?')">
                                        <i class="fas fa-stop"></i> End
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.live-classes.destroy', $liveClass) }}" method="POST" style="display:inline;margin:0;padding:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="lc-btn lc-btn-delete" onclick="return confirm('Delete this live class?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:52px 20px;color:#9ca3af;">
                        <i class="fas fa-broadcast-tower" style="font-size:2.4rem;display:block;margin-bottom:12px;opacity:0.22;"></i>
                        No live classes found. Schedule your first class!
                    </td>
                </tr>
                @endforelse

                <tr id="lc-no-results" style="display:none;">
                    <td colspan="8" style="text-align:center;padding:52px 20px;color:#9ca3af;">
                        <i class="fas fa-search" style="font-size:2rem;display:block;margin-bottom:10px;opacity:0.25;"></i>
                        No classes match your search.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    @if($liveClasses->hasPages())
    <div class="lc-pager">
        <span class="lc-page-info">Page {{ $liveClasses->currentPage() }} of {{ $liveClasses->lastPage() }}</span>
        {{ $liveClasses->links() }}
    </div>
    @endif

</div>
</div>

<script>
(function(){
    var input   = document.getElementById('lcSearch');
    var rows    = [].slice.call(document.querySelectorAll('#lcBody tr:not(#lc-no-results)'));
    var noRes   = document.getElementById('lc-no-results');
    var counter = document.getElementById('lcCount');

    // Store originals once on load
    rows.forEach(function(row){
        [].slice.call(row.querySelectorAll('td')).forEach(function(td){
            td.dataset.orig = td.innerHTML;
        });
    });

    function esc(s){ return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }

    input.addEventListener('input', function(){
        var q = this.value.trim();
        var re = q ? new RegExp(esc(q), 'gi') : null;
        var visible = 0;

        rows.forEach(function(row){
            var cells = [].slice.call(row.querySelectorAll('td'));
            var rawText = cells.map(function(c){ return c.dataset.orig || ''; }).join(' ').replace(/<[^>]+>/g,' ');
            var match = !q || rawText.toLowerCase().indexOf(q.toLowerCase()) > -1;

            row.style.display = match ? '' : 'none';
            if(match) visible++;

            cells.forEach(function(td){
                // restore original HTML first
                td.innerHTML = td.dataset.orig;
                // then highlight plain text nodes only
                if(re && match && q){
                    highlightNode(td, re);
                }
            });
        });

        counter.textContent = visible;
        noRes.style.display = (visible === 0 && q) ? 'table-row' : 'none';
    });

    function highlightNode(node, re){
        if(node.nodeType === 3){ // text node
            var val = node.nodeValue;
            if(re.test(val)){
                re.lastIndex = 0;
                var span = document.createElement('span');
                span.innerHTML = val.replace(re, '<mark>$&</mark>');
                node.parentNode.replaceChild(span, node);
            }
        } else if(node.nodeType === 1 && node.childNodes){
            [].slice.call(node.childNodes).forEach(function(child){
                highlightNode(child, re);
            });
        }
    }
})();
</script>

@endsection