@extends('teacher.layout')

@section('title', 'Live Class Details')

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
    margin-bottom: 24px;
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

/* ── Content ── */
.lc-content { padding: 24px; }
.lc-section {
    margin-bottom: 32px;
}
.lc-section:last-child { margin-bottom: 0; }
.lc-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e5e7eb;
}
.lc-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.lc-info-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.lc-info-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.lc-info-value {
    font-size: 0.95rem;
    color: #111827;
    font-weight: 500;
}

/* ── Status ── */
.lc-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
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
    gap: 12px;
    flex-wrap: wrap;
}
.lc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 9px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.lc-btn-start {
    background: #10b981;
    color: white;
    box-shadow: 0 2px 8px rgba(16,185,129,0.3);
}
.lc-btn-start:hover {
    background: #059669;
}
.lc-btn-end {
    background: #ef4444;
    color: white;
    box-shadow: 0 2px 8px rgba(239,68,68,0.3);
}
.lc-btn-end:hover {
    background: #dc2626;
}
.lc-btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}
.lc-btn-secondary:hover {
    background: #e5e7eb;
    color: #111827;
}

/* ── Tags ── */
.lc-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.lc-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* ── Participants ── */
.lc-participants {
    max-height: 300px;
    overflow-y: auto;
}
.lc-participant-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
}
.lc-participant-item:last-child { border-bottom: none; }
.lc-participant-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.lc-participant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    color: #6b7280;
}
.lc-participant-name {
    font-weight: 500;
    color: #111827;
}
.lc-participant-type {
    font-size: 0.8rem;
    color: #6b7280;
}
.lc-participant-time {
    font-size: 0.75rem;
    color: #9ca3af;
}
</style>

<div class="lc-wrap">
    <!-- Header Card -->
    <div class="lc-card">
        <div class="lc-head">
            <div class="lc-head-left">
                <div class="lc-head-icon">🎥</div>
                <div>
                    <h1 class="lc-head-title">{{ $liveClass->title }}</h1>
                    <p class="lc-head-sub">Live class details and management</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="lc-status {{ $liveClass->status }}">
                    {{ $liveClass->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="lc-card">
        <div class="lc-content">
            <!-- Actions Section -->
            @if($liveClass->status === 'scheduled' || $liveClass->status === 'active')
                <div class="lc-section">
                    <div class="lc-actions">
                        @if($liveClass->status === 'scheduled')
                            <form method="POST" action="{{ route('teacher.live-classes.start', $liveClass) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="lc-btn lc-btn-start" onclick="return confirm('Start this live class now?')">
                                    ▶️ Start Class
                                </button>
                            </form>
                        @elseif($liveClass->status === 'active')
                            <form method="POST" action="{{ route('teacher.live-classes.end', $liveClass) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="lc-btn lc-btn-end" onclick="return confirm('End this live class?')">
                                    ⏹️ End Class
                                </button>
                            </form>
                            <a href="{{ route('teacher.live-classes.room', $liveClass) }}" class="lc-btn lc-btn-secondary">
                                🎥 Enter Room
                            </a>
                        @endif
                        
                        @if($liveClass->status === 'active' || $liveClass->status === 'scheduled')
                            <a href="{{ route('teacher.live-classes.requests', $liveClass) }}" class="lc-btn lc-btn-secondary">
                                📋 Manage Requests
                            </a>
                        @endif
                        
                        <a href="{{ route('teacher.live-classes.index') }}" class="lc-btn lc-btn-secondary">
                            ← Back to Classes
                        </a>
                    </div>
                </div>
            @endif

            <!-- Basic Information -->
            <div class="lc-section">
                <h2 class="lc-section-title">📋 Class Information</h2>
                <div class="lc-info-grid">
                    <div class="lc-info-item">
                        <span class="lc-info-label">Schedule</span>
                        <span class="lc-info-value">
                            {{ $liveClass->start_time->format('M j, Y') }}<br>
                            {{ $liveClass->start_time->format('H:i') }} - {{ $liveClass->end_time->format('H:i') }}
                        </span>
                    </div>
                    <div class="lc-info-item">
                        <span class="lc-info-label">Duration</span>
                        <span class="lc-info-value">
                            {{ $liveClass->start_time->diffInMinutes($liveClass->end_time) }} minutes
                        </span>
                    </div>
                    @if($liveClass->description)
                        <div class="lc-info-item" style="grid-column: 1 / -1;">
                            <span class="lc-info-label">Description</span>
                            <span class="lc-info-value">{{ $liveClass->description }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Courses -->
            @php
                $displayCourseNames = ($liveClass->courses?->pluck('course_name')?->filter()?->unique() ?? collect());
                if ($displayCourseNames->isEmpty()) {
                    $displayCourseNames = $liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('course.course_name') ?? collect())
                        ?->filter()
                        ?->unique()
                        ?? collect();
                }

                $displayBranchNames = ($liveClass->branches?->pluck('name')?->filter()?->unique() ?? collect());
                if ($displayBranchNames->isEmpty()) {
                    $displayBranchNames = $liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('branch.name') ?? collect())
                        ?->filter()
                        ?->unique()
                        ?? collect();
                }

                $displayTeacherNames = collect([$liveClass->teacher?->teacher_name])->filter();
                if ($displayTeacherNames->isEmpty()) {
                    $displayTeacherNames = $liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('teacher.teacher_name') ?? collect())
                        ?->filter()
                        ?->unique()
                        ?? collect();
                }
            @endphp

            @if($displayCourseNames->count() > 0)
                <div class="lc-section">
                    <h2 class="lc-section-title">📚 Courses</h2>
                    <div class="lc-tags">
                        @foreach($displayCourseNames as $courseName)
                            <span class="lc-tag">{{ $courseName }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Branches -->
            @if($displayBranchNames->count() > 0)
                <div class="lc-section">
                    <h2 class="lc-section-title">🏢 Branches</h2>
                    <div class="lc-tags">
                        @foreach($displayBranchNames as $branchName)
                            <span class="lc-tag">{{ $branchName }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Lessons -->
            @if($liveClass->lessons->count() > 0)
                <div class="lc-section">
                    <h2 class="lc-section-title">📖 Lessons</h2>
                    <div class="lc-tags">
                        @foreach($liveClass->lessons as $lesson)
                            <span class="lc-tag">{{ $lesson->title }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Meeting Details -->
            <div class="lc-section">
                <h2 class="lc-section-title">🔗 Meeting Details</h2>
                <div class="lc-info-grid">
                    <div class="lc-info-item">
                        <span class="lc-info-label">Teacher</span>
                        <span class="lc-info-value">{{ $displayTeacherNames->implode(', ') ?: 'Not assigned' }}</span>
                    </div>
                    <div class="lc-info-item">
                        <span class="lc-info-label">Meeting ID</span>
                        <span class="lc-info-value">{{ $liveClass->meeting_id }}</span>
                    </div>
                    <div class="lc-info-item">
                        <span class="lc-info-label">Password</span>
                        <span class="lc-info-value">{{ $liveClass->password }}</span>
                    </div>
                    <div class="lc-info-item">
                        <span class="lc-info-label">Access Type</span>
                        <span class="lc-info-value">{{ ucfirst(str_replace('_', ' ', $liveClass->access_type)) }}</span>
                    </div>
                    @if($liveClass->public_fee > 0)
                        <div class="lc-info-item">
                            <span class="lc-info-label">Public Fee</span>
                            <span class="lc-info-value">{{ $liveClass->currency }} {{ number_format($liveClass->public_fee, 2) }}</span>
                        </div>
                    @endif
                    @if($liveClass->max_participants)
                        <div class="lc-info-item">
                            <span class="lc-info-label">Max Participants</span>
                            <span class="lc-info-value">{{ $liveClass->max_participants }}</span>
                        </div>
                    @endif
                    <div class="lc-info-item">
                        <span class="lc-info-label">Device Restriction</span>
                        <span class="lc-info-value">{{ $liveClass->device_restriction ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="lc-info-item">
                        <span class="lc-info-label">Recording</span>
                        <span class="lc-info-value">{{ $liveClass->recording_enabled ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="lc-info-item">
                        <span class="lc-info-label">Chat</span>
                        <span class="lc-info-value">{{ $liveClass->chat_enabled ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Participants -->
            @if($liveClass->participants && $liveClass->participants->count() > 0)
                <div class="lc-section">
                    <h2 class="lc-section-title">👥 Active Participants ({{ $liveClass->participants->count() }})</h2>
                    <div class="lc-participants">
                        @foreach($liveClass->participants as $participant)
                            <div class="lc-participant-item">
                                <div class="lc-participant-info">
                                    <div class="lc-participant-avatar">
                                        {{ strtoupper(substr($participant->name ?? $participant->email ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="lc-participant-name">{{ $participant->name ?? 'Unknown' }}</div>
                                        <div class="lc-participant-type">{{ ucfirst($participant->user_type) }}</div>
                                    </div>
                                </div>
                                <div class="lc-participant-time">
                                    Joined {{ $participant->joined_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
