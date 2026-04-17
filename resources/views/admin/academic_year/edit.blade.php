@extends('admin.layout')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
/* DEBUG: Temporary background to verify CSS is loading */
body { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    --surface:     #ffffff;
    --line:        #e4e7ec;
    --line-strong: #c9cfd9;
    --blue:        #2754d6;
    --blue-bg:     #eef2fd;
    --blue-mid:    #b8c9f7;
    --green:       #1a7f4b;
    --green-bg:    #eaf7f0;
    --red:         #c5281c;
    --red-bg:      #fdf1f0;
    --r-sm: 6px;
    --r-md: 10px;
    --r-lg: 14px;
    --r-xl: 20px;
    --ease: 140ms cubic-bezier(.4,0,.2,1);
    --sans: 'Instrument Sans', system-ui, sans-serif;
    --mono: 'JetBrains Mono', monospace;
    --serif: 'Instrument Serif', Georgia, serif;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
svg { display: block; fill: none; overflow: visible; }

.aye-page {
    min-height: 100vh;
    background: var(--canvas);
    font-family: var(--sans);
    color: var(--ink);
    padding: 48px 24px 100px;
}

.aye-wrap {
    max-width: 820px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ── Header ── */
.aye-hero {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 32px 36px;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-xl);
    position: relative;
    overflow: hidden;
}

.aye-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
        -45deg,
        transparent,
        transparent 18px,
        rgba(39,84,214,.025) 18px,
        rgba(39,84,214,.025) 19px
    );
    pointer-events: none;
}

.aye-hero-badge {
    flex-shrink: 0;
    width: 52px;
    height: 52px;
    border-radius: var(--r-md);
    background: var(--blue-bg);
    border: 1px solid var(--blue-mid);
    display: flex;
    align-items: center;
    justify-content: center;
}

.aye-hero-badge svg {
    width: 22px;
    height: 22px;
    stroke: var(--blue);
    fill: none;
    display: block;
}

.aye-hero-title {
    font-size: 1.45rem;
    font-weight: 600;
    letter-spacing: -0.025em;
    line-height: 1.15;
    margin-bottom: 4px;
}

.aye-hero-sub {
    font-size: 0.85rem;
    color: var(--ink-2);
    line-height: 1.55;
    max-width: 520px;
}

/* ── Error alert ── */
.aye-alert {
    background: var(--red-bg);
    border: 1px solid #f5c2be;
    border-radius: var(--r-lg);
    padding: 18px 22px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.aye-alert svg {
    width: 18px;
    height: 18px;
    stroke: var(--red);
    fill: none;
    flex-shrink: 0;
    margin-top: 1px;
}

.aye-alert-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--red);
    margin-bottom: 6px;
}

.aye-alert ul {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.aye-alert li {
    font-size: 0.82rem;
    color: #a0201a;
    padding-left: 14px;
    position: relative;
}

.aye-alert li::before {
    content: '–';
    position: absolute;
    left: 0;
    color: #e06b64;
}

/* ── Main card ── */
.aye-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-xl);
    overflow: hidden;
}

/* ── Stats row ── */
.aye-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-bottom: 1px solid var(--line);
}

.aye-stat {
    padding: 22px 28px;
    border-right: 1px solid var(--line);
    display: flex;
    align-items: center;
    gap: 14px;
}

.aye-stat:last-child { border-right: none; }

.aye-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: var(--r-sm);
    background: var(--canvas);
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.aye-stat-icon svg {
    width: 15px;
    height: 15px;
    stroke: var(--ink-2);
    fill: none;
}

.aye-stat-val {
    font-family: var(--mono);
    font-size: 1.35rem;
    font-weight: 500;
    color: var(--ink);
    line-height: 1;
}

.aye-stat-lbl {
    font-size: 0.72rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--ink-3);
    margin-top: 3px;
}

/* ── Form body ── */
.aye-body {
    padding: 36px;
}

/* ── Section heading ── */
.aye-sec {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
}

.aye-sec-line {
    flex: 1;
    height: 1px;
    background: var(--line);
}

.aye-sec-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--ink-3);
    white-space: nowrap;
}

/* ── Field grid ── */
.aye-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 32px;
}

/* ── Field ── */
.aye-field {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.aye-label {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--ink-2);
    display: flex;
    align-items: center;
    gap: 4px;
}

.aye-req {
    color: var(--red);
    font-size: 0.75rem;
    line-height: 1;
}

.aye-input {
    width: 100%;
    height: 40px;
    padding: 0 13px;
    font-family: var(--sans);
    font-size: 0.875rem;
    color: var(--ink);
    background: var(--canvas);
    border: 1px solid var(--line);
    border-radius: var(--r-sm);
    outline: none;
    transition: border-color var(--ease), background var(--ease), box-shadow var(--ease);
    -webkit-appearance: none;
}

.aye-input:hover:not(:focus) {
    border-color: var(--line-strong);
    background: #f0f1f4;
}

.aye-input:focus {
    background: var(--surface);
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(39,84,214,.12);
}

input[type="date"].aye-input {
    cursor: pointer;
    color-scheme: light;
}

/* ── Status tiles ── */
.aye-status-row {
    display: flex;
    gap: 12px;
    margin-bottom: 8px;
}

.aye-status-opt {
    flex: 1;
    cursor: pointer;
}

.aye-status-opt input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.aye-tile {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 15px 18px;
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    background: var(--canvas);
    transition: all var(--ease);
    user-select: none;
}

.aye-tile:hover {
    border-color: var(--line-strong);
    background: #edf0f4;
}

.aye-status-opt input:checked + .aye-tile {
    background: var(--blue-bg);
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(39,84,214,.1);
}

.aye-status-opt.inactive input:checked + .aye-tile {
    background: var(--canvas);
    border-color: #7c8599;
    box-shadow: 0 0 0 3px rgba(107,114,128,.1);
}

.aye-pip {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
}

.aye-pip.on  { background: var(--green); }
.aye-pip.off { background: var(--ink-3); }

.aye-tile-name {
    font-size: 0.865rem;
    font-weight: 500;
    color: var(--ink);
    line-height: 1.2;
}

.aye-tile-sub {
    font-size: 0.775rem;
    color: var(--ink-3);
    margin-top: 2px;
}

/* ── Footer ── */
.aye-footer {
    padding: 22px 36px;
    border-top: 1px solid var(--line);
    background: var(--canvas);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.aye-spacer { flex: 1; }

/* ── Buttons ── */
.aye-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-family: var(--sans);
    font-size: 0.85rem;
    font-weight: 500;
    padding: 9px 18px;
    border-radius: var(--r-sm);
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all var(--ease);
    white-space: nowrap;
    line-height: 1;
}

.aye-btn svg {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.aye-btn-primary {
    background: var(--blue);
    color: #fff;
    border-color: var(--blue);
}

.aye-btn-primary:hover {
    background: #1e45b8;
    border-color: #1e45b8;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(39,84,214,.3);
}

.aye-btn-primary:active {
    transform: none;
    box-shadow: none;
}

.aye-btn-ghost {
    background: transparent;
    color: var(--ink-2);
    border-color: var(--line);
}

.aye-btn-ghost:hover {
    background: var(--surface);
    color: var(--ink);
    border-color: var(--line-strong);
}

.aye-btn-soft {
    background: var(--blue-bg);
    color: var(--blue);
    border-color: var(--blue-mid);
}

.aye-btn-soft:hover {
    background: #dbe6fb;
    border-color: var(--blue);
}

/* ── Tip panel ── */
.aye-tips-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-xl);
    padding: 26px 32px;
}

.aye-tips-head {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 20px;
}

.aye-tips-head svg {
    width: 15px;
    height: 15px;
    stroke: var(--blue);
    fill: none;
    flex-shrink: 0;
}

.aye-tips-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--ink);
}

.aye-tips-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.aye-tip {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 14px 16px;
    background: var(--canvas);
    border: 1px solid var(--line);
    border-radius: var(--r-md);
}

.aye-tip-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    margin-top: 4px;
    flex-shrink: 0;
}

.aye-tip-strong {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 3px;
}

.aye-tip-text {
    font-size: 0.79rem;
    color: var(--ink-2);
    line-height: 1.5;
}

/* ── Responsive ── */
@media (max-width: 680px) {
    .aye-page      { padding: 20px 14px 70px; }
    .aye-hero      { padding: 24px 22px; flex-direction: column; align-items: flex-start; gap: 14px; }
    .aye-body      { padding: 24px 22px; }
    .aye-footer    { padding: 18px 22px; flex-direction: column; align-items: stretch; }
    .aye-btn       { justify-content: center; }
    .aye-spacer    { display: none; }
    .aye-grid      { grid-template-columns: 1fr; }
    .aye-stats     { grid-template-columns: 1fr 1fr; }
    .aye-stat:last-child { grid-column: 1 / -1; border-right: none; border-top: 1px solid var(--line); }
    .aye-status-row { flex-direction: column; }
    .aye-tips-grid { grid-template-columns: 1fr; }
    .aye-tips-card { padding: 22px; }
}
</style>
@endpush

@section('content')

<div class="aye-page">
    <div class="aye-wrap">

        {{-- ── Hero header ── --}}
        <div class="aye-hero">
            <div class="aye-hero-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="aye-hero-title">Edit Academic Year</div>
                <div class="aye-hero-sub">Update the calendar settings for this academic period. Changes will affect students, courses, and fee structures.</div>
            </div>
        </div>

        {{-- ── Errors ── --}}
        @if ($errors->any())
        <div class="aye-alert">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <div class="aye-alert-title">Please fix the following errors</div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- ── Main card ── --}}
        <div class="aye-card">

            {{-- Stats --}}
            <div class="aye-stats">
                <div class="aye-stat">
                    <div class="aye-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="aye-stat-val">{{ $academicYear->terms()->count() }}</div>
                        <div class="aye-stat-lbl">Terms</div>
                    </div>
                </div>
                <div class="aye-stat">
                    <div class="aye-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <div class="aye-stat-val">{{ $academicYear->students()->count() }}</div>
                        <div class="aye-stat-lbl">Students</div>
                    </div>
                </div>
                <div class="aye-stat">
                    <div class="aye-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="aye-stat-val">{{ $academicYear->courses()->count() }}</div>
                        <div class="aye-stat-lbl">Courses</div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.academic_year.update', $academicYear->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="aye-body">

                    {{-- Section: Year & Dates --}}
                    <div class="aye-sec">
                        <span class="aye-sec-label">Year &amp; Date Range</span>
                        <div class="aye-sec-line"></div>
                    </div>

                    <div class="aye-grid">
                        <div class="aye-field">
                            <label class="aye-label" for="year">
                                Academic Year <span class="aye-req">*</span>
                            </label>
                            <input
                                type="text"
                                id="year"
                                name="year"
                                class="aye-input"
                                value="{{ old('year', $academicYear->year) }}"
                                placeholder="e.g. 2024-2025"
                                required
                            >
                        </div>

                        <div></div>

                        <div class="aye-field">
                            <label class="aye-label" for="starting_date">
                                Starting Date <span class="aye-req">*</span>
                            </label>
                            <input
                                type="date"
                                id="starting_date"
                                name="starting_date"
                                class="aye-input"
                                value="{{ old('starting_date', $academicYear->starting_date->format('Y-m-d')) }}"
                                required
                            >
                        </div>

                        <div class="aye-field">
                            <label class="aye-label" for="expiry_date">
                                Expiry Date <span class="aye-req">*</span>
                            </label>
                            <input
                                type="date"
                                id="expiry_date"
                                name="expiry_date"
                                class="aye-input"
                                value="{{ old('expiry_date', $academicYear->expiry_date->format('Y-m-d')) }}"
                                required
                            >
                        </div>
                    </div>

                    {{-- Section: Status --}}
                    <div class="aye-sec">
                        <span class="aye-sec-label">Status</span>
                        <div class="aye-sec-line"></div>
                    </div>

                    <div class="aye-status-row">
                        <label class="aye-status-opt">
                            <input
                                type="radio"
                                name="is_ongoing"
                                value="1"
                                {{ old('is_ongoing', $academicYear->is_ongoing) ? 'checked' : '' }}
                            >
                            <div class="aye-tile">
                                <div class="aye-pip on"></div>
                                <div>
                                    <div class="aye-tile-name">Active</div>
                                    <div class="aye-tile-sub">Currently ongoing</div>
                                </div>
                            </div>
                        </label>

                        <label class="aye-status-opt inactive">
                            <input
                                type="radio"
                                name="is_ongoing"
                                value="0"
                                {{ !old('is_ongoing', $academicYear->is_ongoing) ? 'checked' : '' }}
                            >
                            <div class="aye-tile">
                                <div class="aye-pip off"></div>
                                <div>
                                    <div class="aye-tile-name">Inactive</div>
                                    <div class="aye-tile-sub">Not the current year</div>
                                </div>
                            </div>
                        </label>
                    </div>

                </div>{{-- /body --}}

                {{-- Footer --}}
                <div class="aye-footer">
                    <button type="submit" class="aye-btn aye-btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>

                    <a href="{{ route('admin.academic_year.index') }}" class="aye-btn aye-btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to List
                    </a>

                    <div class="aye-spacer"></div>

                    @if($academicYear->terms()->count() > 0)
                    <a href="{{ route('admin.terms.index') }}?academic_year={{ $academicYear->id }}" class="aye-btn aye-btn-soft">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        View Terms ({{ $academicYear->terms()->count() }})
                    </a>
                    @endif
                </div>

            </form>
        </div>{{-- /card --}}

        {{-- Tips panel --}}
        <div class="aye-tips-card">
            <div class="aye-tips-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="aye-tips-title">Guidelines</div>
            </div>
            <div class="aye-tips-grid">
                <div class="aye-tip">
                    <div class="aye-tip-dot" style="background:#378add"></div>
                    <div>
                        <div class="aye-tip-strong">Year format</div>
                        <div class="aye-tip-text">Use "YYYY-YYYY" — e.g. 2024-2025</div>
                    </div>
                </div>
                <div class="aye-tip">
                    <div class="aye-tip-dot" style="background:#1a7f4b"></div>
                    <div>
                        <div class="aye-tip-strong">Active status</div>
                        <div class="aye-tip-text">Only one year can be active at any time</div>
                    </div>
                </div>
                <div class="aye-tip">
                    <div class="aye-tip-dot" style="background:#9333ea"></div>
                    <div>
                        <div class="aye-tip-strong">Date ranges</div>
                        <div class="aye-tip-text">Avoid overlapping periods between years</div>
                    </div>
                </div>
                <div class="aye-tip">
                    <div class="aye-tip-dot" style="background:#d97706"></div>
                    <div>
                        <div class="aye-tip-strong">System impact</div>
                        <div class="aye-tip-text">Changes propagate to student records and fees</div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /wrap --}}
</div>{{-- /page --}}

@endsection