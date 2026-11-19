@extends('staff.partials.sidebar_dos')

@section('title', 'Live Classes Management')

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

/* ── Stats ── */
.lc-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding: 24px;
}
.lc-stat {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e8ecf0;
    text-align: center;
}
.lc-stat-icon {
    width: 48px; height: 48px;
    margin: 0 auto 12px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.lc-stat-value { font-size: 1.75rem; font-weight: 700; margin: 0; }
.lc-stat-label { font-size: 0.81rem; color: #6b7280; margin: 4px 0 0; }

/* ── Filters ── */
.lc-filters {
    padding: 0 24px 24px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.lc-filter {
    flex: 1;
    min-width: 160px;
}
.lc-filter select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.87rem;
}

/* ── Table ── */
.lc-table-wrap { padding: 0 24px 24px; }
.lc-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.lc-table th {
    background: #f8fafc;
    padding: 12px 16px;
    text-align: left;
    font-size: 0.81rem;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}
.lc-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.87rem;
    vertical-align: middle;
}
.lc-table tr:hover { background: #f9fafb; }
.lc-table tr:last-child td { border-bottom: none; }

/* ── Status Badges ── */
.lc-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1.3;
}
.lc-badge.scheduled { background: #dbeafe; color: #1e40af; }
.lc-badge.live { background: #fee2e2; color: #dc2626; animation: pulse 2s infinite; }
.lc-badge.completed { background: #d1fae5; color: #059669; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* ── Actions ── */
.lc-actions {
    display: flex;
    gap: 6px;
}
.lc-btn {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    font-size: 0.79rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
}
.lc-btn:hover { transform: translateY(-1px); }
.lc-btn-view { background: #f3f4f6; color: #374151; }
.lc-btn-view:hover { background: #e5e7eb; }
.lc-btn-start { background: #dcfce7; color: #15803d; }
.lc-btn-start:hover { background: #bbf7d0; }
.lc-btn-end { background: #fef2f2; color: #dc2626; }
.lc-btn-end:hover { background: #fee2e2; }
.lc-btn-edit { background: #fef3c7; color: #d97706; }
.lc-btn-edit:hover { background: #fde68a; }
.lc-btn-delete { background: #fee2e2; color: #dc2626; }
.lc-btn-delete:hover { background: #fecaca; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .lc-wrap { padding: 16px; }
    .lc-stats { grid-template-columns: 1fr 1fr; gap: 12px; padding: 16px; }
    .lc-filters { padding: 0 16px 16px; }
    .lc-table-wrap { padding: 0 16px 16px; }
    .lc-table { font-size: 0.8rem; }
    .lc-table th, .lc-table td { padding: 10px 12px; }
    .lc-actions { flex-direction: column; gap: 4px; }
}
</style>

<div class="lc-wrap">
    <!-- Header -->
    <div class="lc-card">
        <div class="lc-head">
            <div class="lc-head-left">
                <div class="lc-head-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <h1 class="lc-head-title">Live Classes</h1>
                    <p class="lc-head-sub">Manage and monitor live class sessions</p>
                </div>
            </div>
            <a href="{{ route('dos.live-classes.create') }}" class="lc-btn-new">
                <i class="fas fa-plus"></i> New Live Class
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="lc-stats">
        <div class="lc-stat">
            <div class="lc-stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-video"></i>
            </div>
            <div class="lc-stat-value">{{ $liveClasses->count() ?? 0 }}</div>
            <div class="lc-stat-label">Total Classes</div>
        </div>
        <div class="lc-stat">
            <div class="lc-stat-icon" style="background: #dcfce7; color: #059669;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="lc-stat-value">{{ $liveClasses->where('status', 'scheduled')->count() ?? 0 }}</div>
            <div class="lc-stat-label">Scheduled</div>
        </div>
        <div class="lc-stat">
            <div class="lc-stat-icon" style="background: #fee2e2; color: #dc2626;">
                <i class="fas fa-broadcast"></i>
            </div>
            <div class="lc-stat-value">{{ $liveClasses->where('status', 'live')->count() ?? 0 }}</div>
            <div class="lc-stat-label">Live Now</div>
        </div>
        <div class="lc-stat">
            <div class="lc-stat-icon" style="background: #e0e7ff; color: #4f46e5;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="lc-stat-value">{{ $liveClasses->where('status', 'completed')->count() ?? 0 }}</div>
            <div class="lc-stat-label">Completed</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="lc-card">
        <div class="lc-filters">
            <div class="lc-filter">
                <select name="branch" onchange="filterTable(this.value, 'branch')">
                    <option value="">All Branches</option>
                    @if(isset($branches))
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="lc-filter">
                <select name="status" onchange="filterTable(this.value, 'status')">
                    <option value="">All Status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="live">Live Now</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="lc-filter">
                <select name="course" onchange="filterTable(this.value, 'course')">
                    <option value="">All Courses</option>
                    @if(isset($courses))
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="lc-filter">
                <input type="date" name="date" onchange="filterTable(this.value, 'date')" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
        </div>
    </div>

    <!-- Live Classes Table -->
    <div class="lc-card">
        <div class="lc-table-wrap">
            <table class="lc-table" id="liveClassesTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Teacher</th>
                        <th>Course</th>
                        <th>Scheduled</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($liveClasses) && $liveClasses->count() > 0)
                        @foreach($liveClasses as $liveClass)
                        <tr data-branch="{{ $liveClass->branch_id ?? '' }}" data-status="{{ $liveClass->status }}" data-course="{{ $liveClass->course_id ?? '' }}" data-date="{{ \Carbon\Carbon::parse($liveClass->start_time)->format('Y-m-d') }}">
                            <td>
                                <div>
                                    <strong>{{ $liveClass->title }}</strong>
                                    @if($liveClass->description)
                                        <br><small class="text-muted">{{ Str::limit($liveClass->description, 60) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $liveClass->teacher->teacher_name ?? 'N/A' }}</td>
                            <td>{{ $liveClass->course->course_name ?? 'N/A' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($liveClass->start_time)->format('M d, Y H:i') }}
                                @if($liveClass->timezone)
                                    <br><small class="text-muted">{{ $liveClass->timezone }}</small>
                                @endif
                            </td>
                            <td>{{ $liveClass->duration ?? 'N/A' }} min</td>
                            <td>
                                <span class="lc-badge {{ $liveClass->status }}">
                                    {{ ucfirst($liveClass->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $liveClass->participants->count() ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="lc-actions">
                                    <a href="{{ route('dos.live-classes.show', $liveClass->id) }}" class="lc-btn lc-btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($liveClass->status === 'scheduled')
                                        <button onclick="try { startLiveClass({{ $liveClass->id }}); } catch(e) { console.error('Start live class error:', e); alert('Unable to start live class. Please try again.'); }" class="lc-btn lc-btn-start">
                                            <i class="fas fa-play"></i> Start
                                        </button>
                                    @endif
                                    @if($liveClass->status === 'live')
                                        <button onclick="try { endLiveClass({{ $liveClass->id }}); } catch(e) { console.error('End live class error:', e); alert('Unable to end live class. Please try again.'); }" class="lc-btn lc-btn-end">
                                            <i class="fas fa-stop"></i> End
                                        </button>
                                    @endif
                                    <a href="{{ route('dos.live-classes.edit', $liveClass->id) }}" class="lc-btn lc-btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button onclick="try { deleteLiveClass({{ $liveClass->id }}); } catch(e) { console.error('Delete live class error:', e); alert('Unable to delete live class. Please try again.'); }" class="lc-btn lc-btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px;">
                                <div style="color: #6b7280;">
                                    <i class="fas fa-video-slash" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                                    <p style="margin: 0 0 16px;">No live classes found</p>
                                    <a href="{{ route('dos.live-classes.create') }}" class="lc-btn-new">
                                        <i class="fas fa-plus"></i> Schedule Your First Live Class
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable(value, column) {
    const table = document.getElementById('liveClassesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        if (row.cells[0].colSpan === 8) continue; // Skip "no results" row
        
        let show = true;
        if (column === 'branch' && value) {
            show = row.getAttribute('data-branch') === value;
        } else if (column === 'status' && value) {
            show = row.getAttribute('data-status') === value;
        } else if (column === 'course' && value) {
            show = row.getAttribute('data-course') === value;
        } else if (column === 'date' && value) {
            show = row.getAttribute('data-date') === value;
        }
        
        row.style.display = show ? '' : 'none';
    }
}

function startLiveClass(id) {
    if(confirm('Start this live class now?')) {
        fetch(`/dos/live-classes/${id}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Live class started successfully!');
                location.reload();
            } else {
                alert('Error starting live class: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error starting live class');
        });
    }
}

function endLiveClass(id) {
    if(confirm('End this live class now?')) {
        fetch(`/dos/live-classes/${id}/end`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Live class ended successfully!');
                location.reload();
            } else {
                alert('Error ending live class: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error ending live class');
        });
    }
}

function deleteLiveClass(id) {
    if(confirm('Are you sure you want to delete this live class? This action cannot be undone.')) {
        fetch(`/dos/live-classes/${id}/delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Live class deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting live class: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting live class');
        });
    }
}
</script>

@endsection
