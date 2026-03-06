@extends('admin.layout')

@section('title', 'Edit Live Class')

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

    /* ── Error alert ── */
    .elc-alert {
        display: flex;
        gap: 12px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: var(--radius-input);
        padding: 14px 16px;
        margin-bottom: 24px;
    }

    .elc-alert-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fee2e2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-red);
        font-size: 13px;
        flex-shrink: 0;
    }

    .elc-alert-title {
        font-size: 13px;
        font-weight: 600;
        color: #991b1b;
        margin-bottom: 4px;
    }

    .elc-alert ul {
        margin: 0;
        padding-left: 16px;
    }

    .elc-alert li {
        font-size: 12px;
        color: #b91c1c;
        line-height: 1.6;
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

    /* ── Form grid ── */
    .elc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 32px;
    }

    @media (max-width: 720px) {
        .elc-grid { grid-template-columns: 1fr; }
    }

    /* ── Field ── */
    .elc-field {
        margin-bottom: 20px;
    }

    .elc-label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-label);
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .elc-required {
        color: var(--accent-red);
        font-size: 13px;
        line-height: 1;
    }

    .elc-optional {
        font-size: 10px;
        font-weight: 500;
        color: var(--text-muted);
        background: var(--bg-base);
        border: 1px solid var(--border-default);
        border-radius: 4px;
        padding: 1px 6px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .elc-input,
    .elc-select,
    .elc-textarea {
        width: 100%;
        background: var(--bg-input);
        border: 1.5px solid var(--border-default);
        border-radius: var(--radius-input);
        color: var(--text-primary);
        font-family: 'Sora', sans-serif;
        font-size: 13px;
        font-weight: 400;
        padding: 10px 13px;
        outline: none;
        transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        appearance: none;
        -webkit-appearance: none;
    }

    .elc-input:focus,
    .elc-select:focus,
    .elc-textarea:focus {
        border-color: var(--border-focus);
        background: var(--bg-input-focus);
        box-shadow: 0 0 0 3px var(--border-focus-glow);
    }

    .elc-input::placeholder,
    .elc-textarea::placeholder { color: var(--text-muted); }

    .elc-textarea { resize: vertical; min-height: 90px; line-height: 1.6; }

    /* select arrow */
    .elc-select-wrap { position: relative; }

    .elc-select-wrap::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 10px;
        color: var(--text-muted);
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* datetime icon */
    .elc-input-wrap { position: relative; }

    .elc-input-wrap .elc-input { padding-right: 38px; }

    .elc-input-icon {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 12px;
        pointer-events: none;
    }

    /* datetime native color fix */
    input[type="datetime-local"] { color-scheme: light; }

    /* ── Status select badge colors ── */
    .elc-select option[value="scheduled"] { color: #1d4ed8; }
    .elc-select option[value="active"]    { color: #065f46; }
    .elc-select option[value="ended"]     { color: #374151; }
    .elc-select option[value="cancelled"] { color: #991b1b; }

    /* ── Divider ── */
    .elc-divider {
        height: 1px;
        background: var(--border-default);
        margin: 8px 0 28px;
    }

    /* ── Footer actions ── */
    .elc-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 28px;
        background: #fafbfd;
        border-top: 1px solid var(--border-default);
    }

    .elc-footer-left {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .elc-footer-actions { display: flex; align-items: center; gap: 8px; }

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

    .elc-btn-primary:active { transform: translateY(0); }

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

    /* status pill next to label */
    .elc-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 100px;
        letter-spacing: 0.04em;
    }

    .elc-status-pill.scheduled { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .elc-status-pill.active    { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .elc-status-pill.ended     { background: #f9fafb; color: #374151; border: 1px solid #e5e7eb; }
    .elc-status-pill.cancelled { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
</style>
@endsection

@section('content')
<div class="elc-wrap">

    <!-- ── Page Header ── -->
    <div class="elc-page-header">
        <div>
            <div class="elc-page-title">Edit Live Class</div>
            <div class="elc-page-subtitle">Update the details and scheduling for this session</div>
        </div>
        <a href="{{ route('admin.live-classes.index') }}" class="elc-back-btn">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
    </div>

    <!-- ── Errors ── -->
    @if($errors->any())
    <div class="elc-alert">
        <div class="elc-alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="elc-alert-title">Please fix the following errors</div>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- ── Form Card ── -->
    <form action="{{ route('admin.live-classes.update', $liveClass) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="elc-card">

            <!-- Section: Class Details -->
            <div class="elc-section">
                <div class="elc-section-header">
                    <div class="elc-section-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <span class="elc-section-title">Class Details</span>
                </div>

                <div class="elc-grid">
                    <!-- Title -->
                    <div class="elc-field" style="grid-column: 1 / -1;">
                        <label class="elc-label" for="title">
                            Class Title <span class="elc-required">*</span>
                        </label>
                        <input type="text" class="elc-input" id="title" name="title"
                               value="{{ old('title', $liveClass->title) }}"
                               placeholder="e.g. Introduction to Calculus – Week 4"
                               required>
                    </div>

                    <!-- Description -->
                    <div class="elc-field" style="grid-column: 1 / -1;">
                        <label class="elc-label" for="description">Description</label>
                        <textarea class="elc-textarea" id="description" name="description"
                                  placeholder="Briefly describe what will be covered in this session…">{{ old('description', $liveClass->description) }}</textarea>
                    </div>

                    <!-- Branch -->
                    <div class="elc-field">
                        <label class="elc-label" for="branch_id">
                            Branch <span class="elc-required">*</span>
                        </label>
                        <div class="elc-select-wrap">
                            <select class="elc-select" id="branch_id" name="branch_id" required>
                                <option value="">Select branch…</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $liveClass->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Course -->
                    <div class="elc-field">
                        <label class="elc-label" for="course_id">
                            Course <span class="elc-required">*</span>
                        </label>
                        <div class="elc-select-wrap">
                            <select class="elc-select" id="course_id" name="course_id" required>
                                <option value="">Select course…</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $liveClass->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Lesson -->
                    <div class="elc-field">
                        <label class="elc-label" for="lesson_id">
                            Lesson <span class="elc-optional">Optional</span>
                        </label>
                        <div class="elc-select-wrap">
                            <select class="elc-select" id="lesson_id" name="lesson_id">
                                <option value="">Select lesson…</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('lesson_id', $liveClass->lesson_id) == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Teacher -->
                    <div class="elc-field">
                        <label class="elc-label" for="teacher_id">
                            Teacher <span class="elc-optional">Optional</span>
                        </label>
                        <div class="elc-select-wrap">
                            <select class="elc-select" id="teacher_id" name="teacher_id">
                                <option value="">Select teacher…</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $liveClass->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="elc-divider" style="margin: 0 28px;"></div>

            <!-- Section: Schedule & Status -->
            <div class="elc-section" style="padding-bottom: 28px;">
                <div class="elc-section-header">
                    <div class="elc-section-icon"><i class="fas fa-clock"></i></div>
                    <span class="elc-section-title">Schedule &amp; Status</span>
                </div>

                <div class="elc-grid">
                    <!-- Start Time -->
                    <div class="elc-field">
                        <label class="elc-label" for="start_time">
                            Start Time <span class="elc-required">*</span>
                        </label>
                        <div class="elc-input-wrap">
                            <input type="datetime-local" class="elc-input" id="start_time" name="start_time"
                                   value="{{ old('start_time', $liveClass->start_time->format('Y-m-d\TH:i')) }}"
                                   required>
                            <i class="fas fa-calendar-alt elc-input-icon"></i>
                        </div>
                    </div>

                    <!-- End Time -->
                    <div class="elc-field">
                        <label class="elc-label" for="end_time">
                            End Time <span class="elc-required">*</span>
                        </label>
                        <div class="elc-input-wrap">
                            <input type="datetime-local" class="elc-input" id="end_time" name="end_time"
                                   value="{{ old('end_time', $liveClass->end_time->format('Y-m-d\TH:i')) }}"
                                   required>
                            <i class="fas fa-calendar-alt elc-input-icon"></i>
                        </div>
                        <div id="durationHint" style="font-size:11px;color:var(--text-muted);margin-top:5px;font-family:'JetBrains Mono',monospace;"></div>
                    </div>

                    <!-- Status -->
                    <div class="elc-field">
                        <label class="elc-label" for="status">
                            Status <span class="elc-required">*</span>
                        </label>
                        <div class="elc-select-wrap">
                            <select class="elc-select" id="status" name="status" required>
                                <option value="scheduled" {{ old('status', $liveClass->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="active"    {{ old('status', $liveClass->status) == 'active'    ? 'selected' : '' }}>Active</option>
                                <option value="ended"     {{ old('status', $liveClass->status) == 'ended'     ? 'selected' : '' }}>Ended</option>
                                <option value="cancelled" {{ old('status', $liveClass->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div id="statusPreview" style="margin-top:8px;"></div>
                    </div>
                </div>
            </div>

            <!-- ── Footer ── -->
            <div class="elc-footer">
                <div class="elc-footer-left">
                    <i class="fas fa-shield-alt" style="color:var(--accent-blue);"></i>
                    All changes are saved immediately on submit
                </div>
                <div class="elc-footer-actions">
                    <a href="{{ route('admin.live-classes.index') }}" class="elc-btn elc-btn-ghost">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="elc-btn elc-btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_time');
    const endInput   = document.getElementById('end_time');
    const hint       = document.getElementById('durationHint');
    const statusSel  = document.getElementById('status');
    const preview    = document.getElementById('statusPreview');

    /* ── Duration hint ── */
    function updateDuration() {
        if (!startInput.value || !endInput.value) { hint.textContent = ''; return; }
        const diff = new Date(endInput.value) - new Date(startInput.value);
        if (diff <= 0) { hint.textContent = '⚠ End time must be after start time'; hint.style.color = '#ef4444'; return; }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        hint.textContent = `Duration: ${h > 0 ? h + 'h ' : ''}${m}m`;
        hint.style.color = 'var(--text-muted)';
    }

    startInput.addEventListener('change', function () {
        endInput.min = this.value;
        if (endInput.value && endInput.value <= this.value) {
            const s = new Date(this.value);
            s.setHours(s.getHours() + 1);
            endInput.value = s.toISOString().slice(0, 16);
        }
        updateDuration();
    });

    endInput.addEventListener('change', updateDuration);
    updateDuration();

    /* ── Status badge preview ── */
    const statusMeta = {
        scheduled: { label: 'Scheduled', cls: 'scheduled', icon: 'fa-clock' },
        active:    { label: 'Active',     cls: 'active',    icon: 'fa-circle' },
        ended:     { label: 'Ended',      cls: 'ended',     icon: 'fa-check-circle' },
        cancelled: { label: 'Cancelled',  cls: 'cancelled', icon: 'fa-ban' },
    };

    function updateStatusPreview() {
        const meta = statusMeta[statusSel.value];
        if (!meta) { preview.innerHTML = ''; return; }
        preview.innerHTML = `<span class="elc-status-pill ${meta.cls}">
            <i class="fas ${meta.icon}" style="font-size:9px;"></i> ${meta.label}
        </span>`;
    }

    statusSel.addEventListener('change', updateStatusPreview);
    updateStatusPreview();
});
</script>
@endsection
@endsection