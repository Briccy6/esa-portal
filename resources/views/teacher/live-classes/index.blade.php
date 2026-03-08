@extends('teacher.layout')

@section('title', 'My Live Classes')

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

/* ── Table ── */
.lc-table { width: 100%; border-collapse: collapse; }
.lc-table th {
    background: #f8fafc;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 0.81rem;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}
.lc-table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
    font-size: 0.88rem;
}
.lc-table tr:hover { background: #fafbfc; }

/* ── Status ── */
.lc-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.77rem;
    font-weight: 600;
    text-transform: uppercase;
}
.lc-status.scheduled {
    background: #fef3c7;
    color: #92400e;
}
.lc-status.active {
    background: #d1fae5;
    color: #065f46;
}
.lc-status.ended {
    background: #f3f4f6;
    color: #6b7280;
}

/* ── Actions ── */
.lc-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.lc-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 7px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.2s;
}
.lc-btn-start {
    background: #10b981;
    color: white;
    border-color: #10b981;
}
.lc-btn-start:hover {
    background: #059669;
    border-color: #059669;
    color: white;
}
.lc-btn-end {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}
.lc-btn-end:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: white;
}
.lc-btn-view {
    background: #f3f4f6;
    color: #374151;
    border-color: #d1d5db;
}
.lc-btn-view:hover {
    background: #e5e7eb;
    color: #111827;
}

/* ── Empty State ── */
.lc-empty {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}
.lc-empty-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    opacity: 0.3;
}
.lc-empty-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
}
.lc-empty-text {
    font-size: 0.9rem;
    line-height: 1.5;
}
</style>

<div class="lc-wrap">
    <div class="lc-card">
        <div class="lc-head">
            <div class="lc-head-left">
                <div class="lc-head-icon">🎥</div>
                <div>
                    <h1 class="lc-head-title">My Live Classes</h1>
                    <p class="lc-head-sub">Manage and start your assigned live classes</p>
                </div>
            </div>
        </div>

        @if($liveClasses->count() > 0)
            <div style="padding: 0 24px 24px;">
                <table class="lc-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Schedule</th>
                            <th>Courses</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($liveClasses as $liveClass)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #111827;">{{ $liveClass->title }}</div>
                                    @if($liveClass->description)
                                        <div style="font-size: 0.8rem; color: #6b7280; margin-top: 4px;">
                                            {{ Str::limit($liveClass->description, 80) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;">
                                        <div>{{ $liveClass->start_time->format('M j, Y') }}</div>
                                        <div style="color: #6b7280;">
                                            {{ $liveClass->start_time->format('H:i') }} - {{ $liveClass->end_time->format('H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach($liveClass->courses->take(2) as $course)
                                            <span style="background: #f3f4f6; color: #374151; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">
                                                {{ $course->name }}
                                            </span>
                                        @endforeach
                                        @if($liveClass->courses->count() > 2)
                                            <span style="background: #f3f4f6; color: #6b7280; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">
                                                +{{ $liveClass->courses->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="lc-status {{ $liveClass->status }}">
                                        {{ $liveClass->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="lc-actions">
                                        <a href="{{ route('teacher.live-classes.show', $liveClass) }}" class="lc-btn lc-btn-view">
                                            👁️ View
                                        </a>
                                        
                                        @if($liveClass->status === 'scheduled')
                                            <form method="POST" action="{{ route('teacher.live-classes.start', $liveClass) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="lc-btn lc-btn-start" onclick="return confirm('Start this live class now?')">
                                                    ▶️ Start
                                                </button>
                                            </form>
                                        @elseif($liveClass->status === 'active')
                                            <form method="POST" action="{{ route('teacher.live-classes.end', $liveClass) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="lc-btn lc-btn-end" onclick="return confirm('End this live class?')">
                                                    ⏹️ End
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($liveClass->status === 'active' || $liveClass->status === 'scheduled')
                                            <a href="{{ route('teacher.live-classes.requests', $liveClass) }}" class="lc-btn lc-btn-view">
                                                📋 Requests
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 24px; display: flex; justify-content: center;">
                    {{ $liveClasses->links() }}
                </div>
            </div>
        @else
            <div class="lc-empty">
                <div class="lc-empty-icon">📚</div>
                <div class="lc-empty-title">No Live Classes Assigned</div>
                <div class="lc-empty-text">
                    You haven't been assigned any live classes yet.<br>
                    Contact your administrator to get live class assignments.
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
