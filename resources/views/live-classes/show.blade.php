@extends('admin.layout')

@section('title', 'Live Class Details - {{ $liveClass->title }}')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-base: #f0f2f7;
        --bg-card: #ffffff;
        --bg-input: #f7f8fc;
        --bg-input-focus: #ffffff;
        --border-default: #e2e6f0;
        --border-focus: #63b3ed;
        --border-focus-glow: rgba(99,179,237,0.25);
        --text-primary: #1a1f2e;
        --text-secondary: #5a6478;
        --text-muted: #9aa3b5;
        --text-label: #374151;
        --accent-blue: #3b82f6;
        --accent-blue-dark: #2563eb;
        --accent-blue-light: #eff6ff;
        --accent-red: #ef4444;
        --accent-green: #10b981;
        --accent-amber: #f59e0b;
        --shadow-card: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
        --shadow-card-hover: 0 4px 24px rgba(0,0,0,0.08);
        --radius-card: 14px;
        --radius-input: 9px;
    }

    body, .content-wrapper { background: var(--bg-base) !important; }

    .elc-wrap {
        font-family: 'Sora', sans-serif;
        padding: 32px 28px;
        max-width: 1060px;
        margin: 0 auto;
    }

    /* ── Page header ── */
    .elc-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
        gap: 16px;
    }

    .elc-page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .elc-page-subtitle {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 400;
    }

    .elc-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        background: var(--bg-card);
        border: 1px solid var(--border-default);
        border-radius: var(--radius-input);
        color: var(--text-secondary);
        font-family: 'Sora', sans-serif;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.15s;
        white-space: nowrap;
    }

    .elc-back-btn:hover {
        border-color: var(--border-focus);
        color: var(--accent-blue);
        box-shadow: 0 2px 8px rgba(59,130,246,0.1);
        text-decoration: none;
    }

    /* ── Card ── */
    .elc-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: 1px solid var(--border-default);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }

    /* ── Section ── */
    .elc-section {
        padding: 28px 28px 0;
    }

    .elc-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 22px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border-default);
    }

    .elc-section-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--accent-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-blue);
        font-size: 13px;
        flex-shrink: 0;
    }

    .elc-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    /* ── Grid ── */
    .elc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 32px;
    }

    @media (max-width: 720px) {
        .elc-grid { grid-template-columns: 1fr; }
    }

    /* ── Info Row ── */
    .elc-info-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-default);
    }

    .elc-info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .elc-info-label {
        flex: 0 0 140px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-label);
        letter-spacing: 0.03em;
        text-transform: uppercase;
        padding-top: 2px;
    }

    .elc-info-value {
        flex: 1;
        font-size: 13px;
        color: var(--text-primary);
        line-height: 1.5;
    }

    /* ── Status Badge ── */
    .elc-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 100px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .elc-status-badge.scheduled { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .elc-status-badge.active    { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .elc-status-badge.ended     { background: #f9fafb; color: #374151; border: 1px solid #e5e7eb; }
    .elc-status-badge.cancelled { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* ── Feature Cards ── */
    .elc-features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .elc-feature-card {
        background: var(--bg-input);
        border: 1px solid var(--border-default);
        border-radius: var(--radius-input);
        padding: 16px;
        text-align: center;
        transition: all 0.15s;
    }

    .elc-feature-card:hover {
        border-color: var(--border-focus);
        box-shadow: 0 2px 8px rgba(99,179,237,0.1);
    }

    .elc-feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 16px;
    }

    .elc-feature-icon.enabled {
        background: var(--accent-green);
        color: white;
    }

    .elc-feature-icon.disabled {
        background: var(--text-muted);
        color: white;
    }

    .elc-feature-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .elc-feature-status {
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .elc-feature-status.enabled { color: var(--accent-green); }
    .elc-feature-status.disabled { color: var(--text-muted); }

    /* ── Actions ── */
    .elc-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .elc-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        border-radius: var(--radius-input);
        font-family: 'Sora', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s;
        text-decoration: none;
        white-space: nowrap;
    }

    .elc-btn-primary {
        background: var(--accent-blue);
        color: #fff;
        box-shadow: 0 1px 3px rgba(59,130,246,0.3), 0 4px 12px rgba(59,130,246,0.2);
    }

    .elc-btn-primary:hover {
        background: var(--accent-blue-dark);
        box-shadow: 0 2px 8px rgba(59,130,246,0.4), 0 6px 20px rgba(59,130,246,0.25);
        transform: translateY(-1px);
        color: #fff;
        text-decoration: none;
    }

    .elc-btn-success {
        background: var(--accent-green);
        color: #fff;
        box-shadow: 0 1px 3px rgba(16,185,129,0.3), 0 4px 12px rgba(16,185,129,0.2);
    }

    .elc-btn-success:hover {
        background: #059669;
        box-shadow: 0 2px 8px rgba(16,185,129,0.4), 0 6px 20px rgba(16,185,129,0.25);
        transform: translateY(-1px);
        color: #fff;
        text-decoration: none;
    }

    .elc-btn-danger {
        background: var(--accent-red);
        color: #fff;
        box-shadow: 0 1px 3px rgba(239,68,68,0.3), 0 4px 12px rgba(239,68,68,0.2);
    }

    .elc-btn-danger:hover {
        background: #dc2626;
        box-shadow: 0 2px 8px rgba(239,68,68,0.4), 0 6px 20px rgba(239,68,68,0.25);
        transform: translateY(-1px);
        color: #fff;
        text-decoration: none;
    }

    .elc-btn-warning {
        background: var(--accent-amber);
        color: #fff;
        box-shadow: 0 1px 3px rgba(245,158,11,0.3), 0 4px 12px rgba(245,158,11,0.2);
    }

    .elc-btn-warning:hover {
        background: #d97706;
        box-shadow: 0 2px 8px rgba(245,158,11,0.4), 0 6px 20px rgba(245,158,11,0.25);
        transform: translateY(-1px);
        color: #fff;
        text-decoration: none;
    }

    .elc-btn-ghost {
        background: transparent;
        color: var(--text-secondary);
        border-color: var(--border-default);
    }

    .elc-btn-ghost:hover {
        background: var(--bg-base);
        border-color: #c9d0de;
        color: var(--text-primary);
        text-decoration: none;
    }

    /* ── Divider ── */
    .elc-divider {
        height: 1px;
        background: var(--border-default);
        margin: 8px 0 28px;
    }

    /* ── Fee Display ── */
    .elc-fee-display {
        background: linear-gradient(135deg, var(--accent-blue-light), #f0f7ff);
        border: 1px solid var(--accent-blue);
        border-radius: var(--radius-input);
        padding: 16px;
        margin: 16px 0;
    }

    .elc-fee-amount {
        font-size: 24px;
        font-weight: 700;
        color: var(--accent-blue);
        font-family: 'JetBrains Mono', monospace;
    }

    .elc-fee-label {
        font-size: 12px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 4px;
    }

    /* ── Multi-select Tags ── */
    .elc-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .elc-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: var(--bg-input);
        border: 1px solid var(--border-default);
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        color: var(--text-primary);
    }

    .elc-tag-icon {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        background: var(--accent-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
    }
</style>
@endsection

@section('content')
<div class="elc-wrap">

    <!-- ── Page Header ── -->
    <div class="elc-page-header">
        <div>
            <div class="elc-page-title">{{ $liveClass->title }}</div>
            <div class="elc-page-subtitle">{{ $liveClass->description ?: 'No description provided' }}</div>
        </div>
        <div class="elc-actions">
            <a href="{{ route('admin.live-classes.index') }}" class="elc-btn elc-btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
            <a href="{{ route('admin.live-classes.edit', $liveClass) }}" class="elc-btn elc-btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <!-- ── Main Card ── -->
    <div class="elc-card">

        <!-- Section: Class Details -->
        <div class="elc-section">
            <div class="elc-section-header">
                <div class="elc-section-icon"><i class="fas fa-chalkboard-teacher"></i>
                <span class="elc-section-title">Class Details</span>
            </div>

            <div class="elc-grid">
                <!-- Left Column -->
                <div>
                    <!-- Title & Description -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Title</div>
                        <div class="elc-info-value">{{ $liveClass->title }}</div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">Description</div>
                        <div class="elc-info-value">{{ $liveClass->description ?: 'No description provided' }}</div>
                    </div>

                    <!-- Branches -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Branches</div>
                        <div class="elc-info-value">
                            @if($liveClass->branches->count() > 0)
                                <div class="elc-tags">
                                    @foreach($liveClass->branches as $branch)
                                        <div class="elc-tag">
                                            <div class="elc-tag-icon"><i class="fas fa-building"></i></div>
                                            {{ $branch->name }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: var(--text-muted);">No branches assigned</span>
                            @endif
                        </div>
                    </div>

                    <!-- Courses -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Courses</div>
                        <div class="elc-info-value">
                            @if($liveClass->courses->count() > 0)
                                <div class="elc-tags">
                                    @foreach($liveClass->courses as $course)
                                        <div class="elc-tag">
                                            <div class="elc-tag-icon"><i class="fas fa-book"></i></div>
                                            {{ $course->course_name }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: var(--text-muted);">No courses assigned</span>
                            @endif
                        </div>
                    </div>

                    <!-- Lessons -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Lessons</div>
                        <div class="elc-info-value">
                            @if($liveClass->lessons->count() > 0)
                                <div class="elc-tags">
                                    @foreach($liveClass->lessons as $lesson)
                                        <div class="elc-tag">
                                            <div class="elc-tag-icon"><i class="fas fa-file-alt"></i></div>
                                            {{ $lesson->title }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: var(--text-muted);">No lessons assigned</span>
                            @endif
                        </div>
                    </div>

                    <!-- Teacher -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Teacher</div>
                        <div class="elc-info-value">
                            @if($liveClass->teacher)
                                <div class="elc-tag">
                                    <div class="elc-tag-icon"><i class="fas fa-user-tie"></i></div>
                                    {{ $liveClass->teacher->teacher_name }}
                                </div>
                            @else
                                <span style="color: var(--text-muted);">No teacher assigned</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Status -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Status</div>
                        <div class="elc-info-value">
                            @switch($liveClass->status)
                                @case('scheduled')
                                    <span class="elc-status-badge scheduled">
                                        <i class="fas fa-clock" style="font-size:9px;"></i> Scheduled
                                    </span>
                                    @break
                                @case('active')
                                    <span class="elc-status-badge active">
                                        <i class="fas fa-circle" style="font-size:9px;"></i> Active
                                    </span>
                                    @break
                                @case('ended')
                                    <span class="elc-status-badge ended">
                                        <i class="fas fa-check-circle" style="font-size:9px;"></i> Ended
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="elc-status-badge cancelled">
                                        <i class="fas fa-ban" style="font-size:9px;"></i> Cancelled
                                    </span>
                                    @break
                                @default
                                    <span class="elc-status-badge ended">{{ $liveClass->status }}</span>
                            @endswitch
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Start Time</div>
                        <div class="elc-info-value">
                            <i class="fas fa-calendar-alt" style="color: var(--text-muted); margin-right: 8px;"></i>
                            {{ $liveClass->start_time->format('M j, Y H:i') }}
                        </div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">End Time</div>
                        <div class="elc-info-value">
                            <i class="fas fa-calendar-alt" style="color: var(--text-muted); margin-right: 8px;"></i>
                            {{ $liveClass->end_time->format('M j, Y H:i') }}
                        </div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">Duration</div>
                        <div class="elc-info-value">
                            <i class="fas fa-clock" style="color: var(--text-muted); margin-right: 8px;"></i>
                            {{ $liveClass->start_time->diffInMinutes($liveClass->end_time) }} minutes
                        </div>
                    </div>

                    <!-- Meeting Details -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Meeting ID</div>
                        <div class="elc-info-value">
                            <code style="background: var(--bg-input); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $liveClass->meeting_id }}
                            </code>
                        </div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">Password</div>
                        <div class="elc-info-value">
                            <code style="background: var(--bg-input); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $liveClass->password }}
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="elc-divider" style="margin: 0 28px;"></div>

        <!-- Section: Access Control -->
        <div class="elc-section" style="padding-bottom: 28px;">
            <div class="elc-section-header">
                <div class="elc-section-icon"><i class="fas fa-shield-alt"></i>
                <span class="elc-section-title">Access Control</span>
            </div>

            <div class="elc-grid">
                <!-- Access Type & Fees -->
                <div>
                    <div class="elc-info-row">
                        <div class="elc-info-label">Access Type</div>
                        <div class="elc-info-value">
                            @switch($liveClass->access_type)
                                @case('students')
                                    <span class="elc-tag">
                                        <div class="elc-tag-icon"><i class="fas fa-graduation-cap"></i></div>
                                        Students Only
                                    </span>
                                    @break
                                @case('public')
                                    <span class="elc-tag">
                                        <div class="elc-tag-icon"><i class="fas fa-globe"></i></div>
                                        Public
                                    </span>
                                    @break
                                @case('both')
                                    <span class="elc-tag">
                                        <div class="elc-tag-icon"><i class="fas fa-users"></i></div>
                                        Both
                                    </span>
                                    @break
                                @default
                                    <span class="elc-tag">{{ $liveClass->access_type }}</span>
                            @endswitch
                        </div>
                    </div>

                    <!-- Public Fee Display -->
                    <div class="elc-info-row">
                        <div class="elc-info-label">Public Fee</div>
                        <div class="elc-info-value">
                            @if($liveClass->public_fee > 0)
                                <div class="elc-fee-display">
                                    <div class="elc-fee-amount">{{ $liveClass->currency }} {{ number_format($liveClass->public_fee, 2) }}</div>
                                    <div class="elc-fee-label">Public Access Fee</div>
                                </div>
                            @else
                                <span class="elc-tag">
                                    <div class="elc-tag-icon"><i class="fas fa-gift"></i></div>
                                    Free Access
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">Required Payment %</div>
                        <div class="elc-info-value">
                            <div class="elc-tag">
                                <div class="elc-tag-icon"><i class="fas fa-percentage"></i></div>
                                {{ $liveClass->required_payment_percentage }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participant Settings -->
                <div>
                    <div class="elc-info-row">
                        <div class="elc-info-label">Max Participants</div>
                        <div class="elc-info-value">
                            @if($liveClass->max_participants)
                                <div class="elc-tag">
                                    <div class="elc-tag-icon"><i class="fas fa-users"></i></div>
                                    {{ $liveClass->max_participants }} people
                                </div>
                            @else
                                <span class="elc-tag">
                                    <div class="elc-tag-icon"><i class="fas fa-infinity"></i></div>
                                    Unlimited
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">Auto Approve Online</div>
                        <div class="elc-info-value">
                            @if($liveClass->auto_approve_online_students)
                                <span class="elc-tag">
                                    <div class="elc-tag-icon" style="background: var(--accent-green);"><i class="fas fa-check"></i></div>
                                    Enabled
                                </span>
                            @else
                                <span class="elc-tag">
                                    <div class="elc-tag-icon" style="background: var(--text-muted);"><i class="fas fa-times"></i></div>
                                    Disabled
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="elc-info-row">
                        <div class="elc-info-label">Require Approval</div>
                        <div class="elc-info-value">
                            @if($liveClass->require_student_approval)
                                <span class="elc-tag">
                                    <div class="elc-tag-icon" style="background: var(--accent-amber);"><i class="fas fa-user-check"></i></div>
                                    Required
                                </span>
                            @else
                                <span class="elc-tag">
                                    <div class="elc-tag-icon" style="background: var(--accent-green);"><i class="fas fa-user-check"></i></div>
                                    Not Required
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="elc-divider" style="margin: 0 28px;"></div>

        <!-- Section: Features -->
        <div class="elc-section" style="padding-bottom: 28px;">
            <div class="elc-section-header">
                <div class="elc-section-icon"><i class="fas fa-cogs"></i>
                <span class="elc-section-title">Features</span>
            </div>

            <div class="elc-features-grid">
                <!-- Screen Share -->
                <div class="elc-feature-card">
                    <div class="elc-feature-icon {{ $liveClass->screen_share_enabled ? 'enabled' : 'disabled' }}">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="elc-feature-title">Screen Share</div>
                    <div class="elc-feature-status {{ $liveClass->screen_share_enabled ? 'enabled' : 'disabled' }}">
                        {{ $liveClass->screen_share_enabled ? 'Enabled' : 'Disabled' }}
                    </div>
                </div>

                <!-- Recording -->
                <div class="elc-feature-card">
                    <div class="elc-feature-icon {{ $liveClass->recording_enabled ? 'enabled' : 'disabled' }}">
                        <i class="fas fa-record-vinyl"></i>
                    </div>
                    <div class="elc-feature-title">Recording</div>
                    <div class="elc-feature-status {{ $liveClass->recording_enabled ? 'enabled' : 'disabled' }}">
                        {{ $liveClass->recording_enabled ? 'Enabled' : 'Disabled' }}
                    </div>
                </div>

                <!-- Chat -->
                <div class="elc-feature-card">
                    <div class="elc-feature-icon {{ $liveClass->chat_enabled ? 'enabled' : 'disabled' }}">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="elc-feature-title">Chat</div>
                    <div class="elc-feature-status {{ $liveClass->chat_enabled ? 'enabled' : 'disabled' }}">
                        {{ $liveClass->chat_enabled ? 'Enabled' : 'Disabled' }}
                    </div>
                </div>

                <!-- Device Restriction -->
                <div class="elc-feature-card">
                    <div class="elc-feature-icon {{ $liveClass->device_restriction ? 'enabled' : 'disabled' }}">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="elc-feature-title">Device Restriction</div>
                    <div class="elc-feature-status {{ $liveClass->device_restriction ? 'enabled' : 'disabled' }}">
                        {{ $liveClass->device_restriction ? 'Enabled' : 'Disabled' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Actions Footer ── -->
        <div style="padding: 20px 28px; background: #fafbfd; border-top: 1px solid var(--border-default);">
            <div class="elc-actions">
                @if($liveClass->status === 'scheduled')
                    <form action="{{ route('admin.live-classes.start', $liveClass) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="elc-btn elc-btn-success" onclick="return confirm('Start this live class now?')">
                            <i class="fas fa-play"></i> Start Class
                        </button>
                    </form>
                @endif

                @if($liveClass->status === 'active')
                    <form action="{{ route('admin.live-classes.end', $liveClass) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="elc-btn elc-btn-danger" onclick="return confirm('End this live class?')">
                            <i class="fas fa-stop"></i> End Class
                        </button>
                    </form>
                    
                    <a href="{{ route('live-classes.room', $liveClass) }}" class="elc-btn elc-btn-primary" target="_blank">
                        <i class="fas fa-video"></i> Join Room
                    </a>
                @endif

                <a href="{{ route('admin.live-classes.edit', $liveClass) }}" class="elc-btn elc-btn-warning">
                    <i class="fas fa-edit"></i> Edit Class
                </a>

                <form action="{{ route('admin.live-classes.destroy', $liveClass) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="elc-btn elc-btn-danger" onclick="return confirm('Delete this live class? This action cannot be undone.')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
