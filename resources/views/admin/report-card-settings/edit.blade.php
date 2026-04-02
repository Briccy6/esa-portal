@extends('admin.layout')

@section('title', 'Edit Report Card Setting')

@section('styles')
<style>
    /* ── Layout Fix: Ensure content fits within sidebar offset ── */
    .main-content,
    .content-wrapper,
    #content,
    .page-content {
        min-width: 0;
        overflow-x: hidden;
    }

    /* ── Design Tokens ── */
    :root {
        --rc-bg:        #0f1117;
        --rc-surface:   #181c27;
        --rc-border:    #252a3a;
        --rc-accent:    #4f8ef7;
        --rc-accent-dim:#2a4d9e;
        --rc-success:   #22c55e;
        --rc-warn:      #f59e0b;
        --rc-danger:    #ef4444;
        --rc-text:      #e2e8f0;
        --rc-muted:     #64748b;
        --rc-radius:    10px;
        --rc-font:      'DM Sans', 'Segoe UI', sans-serif;
    }

    /* ── Page header ── */
    .rcs-page-header { margin-bottom: 1.75rem; }
    .rcs-page-header h4 {
        font-family: var(--rc-font);
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--rc-text);
        margin: 0 0 .3rem;
        letter-spacing: -.02em;
    }
    .rcs-breadcrumb {
        display: flex; gap: .4rem; align-items: center; flex-wrap: wrap;
        list-style: none; padding: 0; margin: 0;
        font-size: .78rem; color: var(--rc-muted);
    }
    .rcs-breadcrumb li + li::before { content: '/'; margin-right: .4rem; opacity: .5; }
    .rcs-breadcrumb a { color: var(--rc-accent); text-decoration: none; }
    .rcs-breadcrumb a:hover { text-decoration: underline; }

    /* ── Card ── */
    .rcs-card {
        background: var(--rc-surface);
        border: 1px solid var(--rc-border);
        border-radius: var(--rc-radius);
        overflow: hidden;
        width: 100%; box-sizing: border-box;
    }
    .rcs-card-header {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid var(--rc-border);
        display: flex; align-items: center; gap: .65rem;
    }
    .rcs-card-header-icon {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, var(--rc-accent), var(--rc-accent-dim));
        border-radius: 8px; display: grid; place-items: center;
        font-size: .85rem; color: #fff; flex-shrink: 0;
    }
    .rcs-card-header h5 {
        font-family: var(--rc-font); font-size: .95rem;
        font-weight: 600; color: var(--rc-text); margin: 0;
    }
    .rcs-card-body { padding: 1.75rem 1.5rem; }

    /* ── Form grid ── */
    .rcs-row { display: grid; gap: 1rem; margin-bottom: 1rem; }
    .rcs-row.cols-2  { grid-template-columns: 1fr 1fr; }
    .rcs-row.cols-84 { grid-template-columns: 2fr 1fr; }
    @media (max-width: 680px) {
        .rcs-row.cols-2,
        .rcs-row.cols-84 { grid-template-columns: 1fr; }
    }

    /* ── Controls ── */
    .rcs-field { display: flex; flex-direction: column; gap: .35rem; }
    .rcs-label {
        font-size: .78rem; font-weight: 600;
        letter-spacing: .04em; text-transform: uppercase; color: var(--rc-muted);
    }
    .rcs-input, .rcs-select, .rcs-textarea {
        width: 100%; box-sizing: border-box;
        background: #111420; border: 1px solid var(--rc-border);
        border-radius: 8px; padding: .6rem .85rem;
        font-size: .88rem; color: var(--rc-text); font-family: var(--rc-font);
        transition: border-color .2s, box-shadow .2s;
        outline: none; -webkit-appearance: none; appearance: none;
    }
    .rcs-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right .75rem center;
        padding-right: 2.2rem;
    }
    .rcs-input:focus, .rcs-select:focus, .rcs-textarea:focus {
        border-color: var(--rc-accent);
        box-shadow: 0 0 0 3px rgba(79,142,247,.15);
    }
    .rcs-input.is-invalid, .rcs-select.is-invalid, .rcs-textarea.is-invalid { border-color: var(--rc-danger); }
    .rcs-error  { font-size: .75rem; color: var(--rc-danger); }
    .rcs-hint   { font-size: .74rem; color: var(--rc-muted); line-height: 1.45; }

    /* ── Checkbox ── */
    .rcs-check-wrap {
        display: flex; align-items: flex-start; gap: .6rem; padding-top: 1.85rem;
    }
    .rcs-check-input {
        width: 18px; height: 18px; flex-shrink: 0;
        accent-color: var(--rc-accent); cursor: pointer; margin-top: 1px;
    }
    .rcs-check-label { font-size: .88rem; color: var(--rc-text); font-weight: 500; }

    /* ── Status alert ── */
    .rcs-status-alert {
        background: rgba(79,142,247,.07);
        border: 1px solid rgba(79,142,247,.25);
        border-radius: 8px;
        padding: 1rem 1.15rem;
        margin-bottom: 1.25rem;
    }
    .rcs-status-alert h6 {
        font-size: .82rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: var(--rc-accent);
        margin: 0 0 .65rem; display: flex; align-items: center; gap: .4rem;
    }
    .rcs-status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; }
    @media (max-width: 540px) { .rcs-status-grid { grid-template-columns: 1fr; } }
    .rcs-status-item { font-size: .83rem; color: var(--rc-muted); }
    .rcs-status-item strong { color: var(--rc-text); }

    /* ── Badges ── */
    .rcs-badge {
        display: inline-block; padding: .18rem .55rem;
        border-radius: 999px; font-size: .72rem; font-weight: 700;
        letter-spacing: .03em; text-transform: uppercase;
    }
    .badge-success  { background: rgba(34,197,94,.15);  color: var(--rc-success); }
    .badge-warning  { background: rgba(245,158,11,.15); color: var(--rc-warn); }
    .badge-neutral  { background: rgba(100,116,139,.15);color: var(--rc-muted); }

    /* ── Footer ── */
    .rcs-footer {
        display: flex; justify-content: space-between; align-items: center;
        gap: 1rem; flex-wrap: wrap;
        margin-top: 1.5rem; padding-top: 1.25rem;
        border-top: 1px solid var(--rc-border);
    }
    .rcs-btn-group { display: flex; gap: .6rem; flex-wrap: wrap; }

    /* ── Buttons ── */
    .rcs-btn {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .55rem 1.15rem; border-radius: 8px;
        font-size: .84rem; font-weight: 600; font-family: var(--rc-font);
        cursor: pointer; border: none; text-decoration: none;
        transition: opacity .15s, transform .1s; white-space: nowrap;
    }
    .rcs-btn:active { transform: scale(.97); }
    .rcs-btn-primary   { background: var(--rc-accent); color: #fff; }
    .rcs-btn-primary:hover  { opacity: .88; }
    .rcs-btn-secondary { background: #252a3a; color: var(--rc-text); }
    .rcs-btn-secondary:hover { background: #2e3449; }
    .rcs-btn-outline   { background: transparent; border: 1px solid var(--rc-border); color: var(--rc-muted); }
    .rcs-btn-outline:hover { border-color: var(--rc-accent); color: var(--rc-accent); }
</style>
@endsection

@section('content')

<div class="rcs-page-header">
    <h4>Edit Report Card Setting</h4>
    <ol class="rcs-breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li><a href="{{ route('admin.report-card-settings.index') }}">Report Card Settings</a></li>
        <li>Edit</li>
    </ol>
</div>

<div class="rcs-card">
    <div class="rcs-card-header">
        <div class="rcs-card-header-icon"><i class="fas fa-calendar-alt"></i></div>
        <h5>Edit Report Card Setting</h5>
    </div>
    <div class="rcs-card-body">

        <form action="{{ route('admin.report-card-settings.update', $reportCardSetting) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Row 1: Academic Year + Term --}}
            <div class="rcs-row cols-2">
                <div class="rcs-field">
                    <label class="rcs-label" for="academic_year">Academic Year <span style="color:#ef4444">*</span></label>
                    <input type="text"
                           class="rcs-input @error('academic_year') is-invalid @enderror"
                           id="academic_year" name="academic_year"
                           value="{{ old('academic_year', $reportCardSetting->academic_year) }}"
                           placeholder="e.g. 2025-2026" required>
                    @error('academic_year')<span class="rcs-error">{{ $message }}</span>@enderror
                </div>

                <div class="rcs-field">
                    <label class="rcs-label" for="term">Term <span style="color:#ef4444">*</span></label>
                    <select class="rcs-select @error('term') is-invalid @enderror"
                            id="term" name="term" required>
                        <option value="">Select Term</option>
                        <option value="Term 1" {{ old('term', $reportCardSetting->term) == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                        <option value="Term 2" {{ old('term', $reportCardSetting->term) == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                        <option value="Term 3" {{ old('term', $reportCardSetting->term) == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                    </select>
                    @error('term')<span class="rcs-error">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Row 2: Available From + Active --}}
            <div class="rcs-row cols-84">
                <div class="rcs-field">
                    <label class="rcs-label" for="report_card_available_at">Report Cards Available From <span style="color:#ef4444">*</span></label>
                    <input type="datetime-local"
                           class="rcs-input @error('report_card_available_at') is-invalid @enderror"
                           id="report_card_available_at" name="report_card_available_at"
                           value="{{ old('report_card_available_at', $reportCardSetting->report_card_available_at->format('Y-m-d\TH:i')) }}"
                           required>
                    @error('report_card_available_at')<span class="rcs-error">{{ $message }}</span>@enderror
                    <span class="rcs-hint">Students can access report cards from this date/time, provided all fees are paid.</span>
                </div>

                <div class="rcs-field">
                    <div class="rcs-check-wrap">
                        <input class="rcs-check-input" type="checkbox"
                               id="is_active" name="is_active" value="1"
                               {{ old('is_active', $reportCardSetting->is_active) ? 'checked' : '' }}>
                        <div>
                            <label class="rcs-check-label" for="is_active">Active</label>
                            <p class="rcs-hint" style="margin:.2rem 0 0">Enable this setting for students</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="rcs-field" style="margin-bottom:1.25rem">
                <label class="rcs-label" for="description">Description</label>
                <textarea class="rcs-textarea @error('description') is-invalid @enderror"
                          id="description" name="description" rows="3"
                          placeholder="Optional description for this report card setting">{{ old('description', $reportCardSetting->description) }}</textarea>
                @error('description')<span class="rcs-error">{{ $message }}</span>@enderror
            </div>

            {{-- Status info block --}}
            <div class="rcs-status-alert">
                <h6><i class="fas fa-info-circle"></i> Current Status</h6>
                <div class="rcs-status-grid">
                    <div class="rcs-status-item">
                        <strong>Setting: </strong>
                        @if($reportCardSetting->is_active)
                            @if($reportCardSetting->isAvailable())
                                <span class="rcs-badge badge-success">Available</span>
                            @else
                                <span class="rcs-badge badge-warning">Scheduled</span>
                            @endif
                        @else
                            <span class="rcs-badge badge-neutral">Inactive</span>
                        @endif
                    </div>
                    <div class="rcs-status-item">
                        <strong>Time until available: </strong>
                        @if($reportCardSetting->isAvailable())
                            <span style="color:var(--rc-success)">Available now</span>
                        @else
                            <span style="color:var(--rc-warn)">{{ $reportCardSetting->getTimeRemaining() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="rcs-footer">
                <a href="{{ route('admin.report-card-settings.index') }}" class="rcs-btn rcs-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div class="rcs-btn-group">
                    <button type="reset" class="rcs-btn rcs-btn-outline">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="rcs-btn rcs-btn-primary">
                        <i class="fas fa-save"></i> Update Setting
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dt = document.getElementById('report_card_available_at');
    const originalVal = dt.value;

    dt.addEventListener('change', function () {
        if (new Date(this.value) < new Date()) {
            if (!confirm('You selected a date in the past — report cards will be immediately available. Continue?')) {
                this.value = originalVal;
            }
        }
    });
});
</script>
@endsection