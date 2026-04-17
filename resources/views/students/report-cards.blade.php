@extends('students.app')

@section('title', 'Report Cards')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ─────────────────────────────────────────
   Base & Layout
───────────────────────────────────────── */
:root {
    --bg:          #080b12;
    --surface:     #0e1320;
    --surface-2:   #141926;
    --border:      #1e2638;
    --accent:      #3b82f6;
    --accent-soft: rgba(59,130,246,.12);
    --danger:      #ef4444;
    --danger-soft: rgba(239,68,68,.12);
    --success:     #22c55e;
    --success-soft:rgba(34,197,94,.12);
    --warn:        #f59e0b;
    --warn-soft:   rgba(245,158,11,.12);
    --text:        #e2e8f0;
    --muted:       #64748b;
    --font:        'Sora', sans-serif;
    --mono:        'DM Mono', monospace;
    --radius:      14px;
}

* { box-sizing: border-box; }

body { background: var(--bg); color: var(--text); font-family: var(--font); }

.rc-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.25rem 4rem;
}

/* ─────────────────────────────────────────
   Page Header
───────────────────────────────────────── */
.rc-hero {
    position: relative;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem 2rem 1.6rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.rc-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 60% 80% at 90% 0%, rgba(59,130,246,.12) 0%, transparent 70%);
    pointer-events: none;
}
.rc-hero-label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: .5rem;
}
.rc-hero h1 {
    font-size: 1.65rem;
    font-weight: 800;
    letter-spacing: -.03em;
    color: var(--text);
    margin: 0 0 .4rem;
}
.rc-hero p {
    font-size: .88rem;
    color: var(--muted);
    margin: 0;
}

/* ─────────────────────────────────────────
   Fee Alert Banner
───────────────────────────────────────── */
.rc-fee-alert {
    background: var(--danger-soft);
    border: 1px solid rgba(239,68,68,.3);
    border-radius: var(--radius);
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    animation: slideDown .4s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rc-fee-icon {
    width: 46px; height: 46px;
    background: var(--danger);
    border-radius: 12px;
    display: grid; place-items: center;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 0 20px rgba(239,68,68,.35);
}
.rc-fee-content { flex: 1; }
.rc-fee-content h5 {
    font-size: .95rem;
    font-weight: 700;
    color: #fca5a5;
    margin: 0 0 .3rem;
}
.rc-fee-content p {
    font-size: .83rem;
    color: #fca5a5;
    opacity: .85;
    margin: 0 0 .85rem;
    line-height: 1.5;
}
.rc-fee-amount {
    display: inline-flex;
    align-items: baseline;
    gap: .35rem;
    background: rgba(239,68,68,.2);
    border: 1px solid rgba(239,68,68,.35);
    border-radius: 8px;
    padding: .5rem 1rem;
}
.rc-fee-amount .amount-num {
    font-family: var(--mono);
    font-size: 1.55rem;
    font-weight: 700;
    color: #fca5a5;
    letter-spacing: -.02em;
}
.rc-fee-amount .amount-unit {
    font-size: .78rem;
    font-weight: 600;
    color: #fca5a5;
    opacity: .7;
    letter-spacing: .05em;
}
.rc-fee-note {
    margin-top: .7rem !important;
    font-size: .78rem !important;
    opacity: .7 !important;
}

/* ─────────────────────────────────────────
   Countdown Block
───────────────────────────────────────── */
.rc-countdown-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
    animation: slideDown .5s ease;
}
.rc-countdown-label {
    flex: 1;
    min-width: 180px;
}
.rc-countdown-label .pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: var(--warn-soft);
    border: 1px solid rgba(245,158,11,.3);
    border-radius: 999px;
    padding: .25rem .75rem;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--warn);
    margin-bottom: .6rem;
}
.rc-countdown-label h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 .2rem;
}
.rc-countdown-label p {
    font-size: .78rem;
    color: var(--muted);
    margin: 0;
}

/* The four digit blocks */
.rc-digits {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    align-items: center;
}
.rc-digit-block {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 62px;
}
.rc-digit-num {
    font-family: var(--mono);
    font-size: 2rem;
    font-weight: 700;
    color: var(--text);
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    width: 62px;
    height: 62px;
    display: grid;
    place-items: center;
    letter-spacing: -.02em;
    position: relative;
    overflow: hidden;
    transition: border-color .3s;
}
.rc-digit-num.tick {
    border-color: var(--accent);
}
.rc-digit-num::after {
    content: '';
    position: absolute;
    top: 50%; left: 0; right: 0;
    height: 1px;
    background: var(--border);
}
.rc-digit-label {
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--muted);
    margin-top: .35rem;
}
.rc-digit-sep {
    font-size: 1.5rem;
    color: var(--muted);
    font-weight: 700;
    margin-bottom: 1.1rem;
    align-self: flex-end;
    padding-bottom: .5rem;
}

/* ─────────────────────────────────────────
   Report Card Cards
───────────────────────────────────────── */
.rc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.rc-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: transform .2s, border-color .2s, box-shadow .2s;
    animation: fadeUp .5s ease both;
}
.rc-card:hover {
    transform: translateY(-3px);
    border-color: rgba(59,130,246,.35);
    box-shadow: 0 8px 32px rgba(0,0,0,.35);
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rc-card:nth-child(1) { animation-delay: .05s; }
.rc-card:nth-child(2) { animation-delay: .10s; }
.rc-card:nth-child(3) { animation-delay: .15s; }
.rc-card:nth-child(4) { animation-delay: .20s; }
.rc-card:nth-child(5) { animation-delay: .25s; }
.rc-card:nth-child(6) { animation-delay: .30s; }

.rc-card-top {
    padding: 1rem 1.25rem .85rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.rc-card-top h6 {
    font-size: .88rem;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 .2rem;
}
.rc-card-top p {
    font-size: .75rem;
    color: var(--muted);
    margin: 0;
}

.rc-card-body { padding: 1.25rem; }

.rc-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .6rem;
    margin-bottom: 1rem;
}
.rc-stat {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: .7rem .5rem;
    text-align: center;
}
.rc-stat-val {
    font-family: var(--mono);
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--accent);
    display: block;
}
.rc-stat-lbl {
    font-size: .65rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--muted);
    display: block;
    margin-top: .2rem;
}

.rc-approved {
    font-size: .72rem;
    color: var(--muted);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .4rem;
}

/* Download button */
.rc-dl-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    width: 100%;
    padding: .65rem 1rem;
    border-radius: 9px;
    font-size: .84rem;
    font-weight: 700;
    font-family: var(--font);
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}
.rc-dl-btn.available {
    background: var(--success);
    color: #fff;
    box-shadow: 0 0 20px rgba(34,197,94,.2);
}
.rc-dl-btn.available:hover {
    box-shadow: 0 0 28px rgba(34,197,94,.4);
    transform: translateY(-1px);
}
.rc-dl-btn.locked {
    background: var(--surface-2);
    border: 1px solid var(--border);
    color: var(--muted);
    cursor: not-allowed;
}
.rc-dl-btn.locked:hover { transform: none; box-shadow: none; }

/* ─────────────────────────────────────────
   Empty State
───────────────────────────────────────── */
.rc-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--muted);
}
.rc-empty-icon {
    font-size: 3.5rem;
    opacity: .25;
    margin-bottom: 1rem;
    display: block;
}
.rc-empty h4 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: .5rem;
}
.rc-empty p { font-size: .85rem; max-width: 360px; margin: 0 auto; }
</style>
@endsection

@section('content')
<div class="rc-wrap">

    {{-- ── Hero Header ── --}}
    <div class="rc-hero">
        <div class="rc-hero-label"><i class="bi bi-mortarboard-fill me-1"></i> Student Portal</div>
        <h1><i class="bi bi-file-earmark-text me-2"></i>My Report Cards</h1>
        <p>View and download your approved report cards for each term.</p>
    </div>

    {{-- ── Outstanding Fees Alert ── --}}
    @if($hasOutstandingFees)
    <div class="rc-fee-alert">
        <div class="rc-fee-icon">⚠️</div>
        <div class="rc-fee-content">
            <h5>Outstanding Fees — Access Restricted</h5>
            <p>Your report cards are locked until all outstanding fees have been cleared. Please visit our offices to complete payment.</p>
            <div class="rc-fee-amount">
                <span class="amount-num">{{ number_format($outstanding, 2) }}</span>
                <span class="amount-unit">RWF remaining</span>
            </div>
            <p class="rc-fee-note"><i class="bi bi-info-circle me-1"></i>Once payment is confirmed, you will be able to download your report cards immediately.</p>
        </div>
    </div>
    @endif

    {{-- ── Countdown (only when release date exists and hasn't passed) ── --}}
    @if($reportCardReleaseDate && !$releaseDatePassed)
    <div class="rc-countdown-wrap">
        <div class="rc-countdown-label">
            <div class="pill">
                <span>⏳</span> Releasing soon
            </div>
            <h4>Report Cards Release Countdown</h4>
            <p>Available on {{ \Carbon\Carbon::parse($reportCardReleaseDate)->format('d M Y \a\t H:i') }}</p>
        </div>
        <div class="rc-digits" id="rc-digits">
            <div class="rc-digit-block">
                <div class="rc-digit-num" id="cd-days">--</div>
                <div class="rc-digit-label">Days</div>
            </div>
            <div class="rc-digit-sep">:</div>
            <div class="rc-digit-block">
                <div class="rc-digit-num" id="cd-hours">--</div>
                <div class="rc-digit-label">Hours</div>
            </div>
            <div class="rc-digit-sep">:</div>
            <div class="rc-digit-block">
                <div class="rc-digit-num" id="cd-mins">--</div>
                <div class="rc-digit-label">Mins</div>
            </div>
            <div class="rc-digit-sep">:</div>
            <div class="rc-digit-block">
                <div class="rc-digit-num" id="cd-secs">--</div>
                <div class="rc-digit-label">Secs</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Report Cards Grid ── --}}
    @if($reportCards->count() > 0)
    <div class="rc-grid">
        @foreach($reportCards as $reportCard)
        <div class="rc-card">
            <div class="rc-card-top">
                <h6>{{ $reportCard->academicYear->year ?? 'Academic Year' }}</h6>
                <p>{{ $reportCard->term->name ?? 'Term' }} &mdash; {{ $reportCard->course->course_name ?? 'Course' }}</p>
            </div>
            <div class="rc-card-body">
                <div class="rc-stats">
                    <div class="rc-stat">
                        <span class="rc-stat-val">{{ $reportCard->average_marks ? number_format($reportCard->average_marks, 1) : 'N/A' }}</span>
                        <span class="rc-stat-lbl">Average</span>
                    </div>
                    <div class="rc-stat">
                        <span class="rc-stat-val">{{ $reportCard->grade ?? 'N/A' }}</span>
                        <span class="rc-stat-lbl">Grade</span>
                    </div>
                    <div class="rc-stat">
                        <span class="rc-stat-val">{{ $reportCard->position_display }}</span>
                        <span class="rc-stat-lbl">Position</span>
                    </div>
                </div>

                <div class="rc-approved">
                    <i class="bi bi-patch-check"></i>
                    Approved: {{ $reportCard->approved_at ? $reportCard->approved_at->format('d M Y H:i') : 'N/A' }}
                </div>

                @if($hasOutstandingFees || ($reportCardReleaseDate && !$releaseDatePassed))
                    <button class="rc-dl-btn locked" disabled>
                        <i class="bi bi-lock-fill"></i>
                        {{ $hasOutstandingFees ? 'Pay Fees to Unlock' : 'Not Yet Released' }}
                    </button>
                @else
                    <a href="{{ route('student.report-cards.download', $reportCard->id) }}" class="rc-dl-btn available">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @else
    <div class="rc-empty">
        <span class="rc-empty-icon"><i class="bi bi-file-earmark-text"></i></span>
        <h4>No Report Cards Yet</h4>
        <p>
            @if($hasOutstandingFees)
                Clear your outstanding fees and your report cards will be accessible here.
            @elseif($reportCardReleaseDate && !$releaseDatePassed)
                Report cards will appear here once the release date passes.
            @else
                No approved report cards have been generated for your account yet.
            @endif
        </p>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
@if($reportCardReleaseDate && !$releaseDatePassed)
(function () {
    // Release timestamp from server (UTC ISO string, adjust if your server sends local time)
    const releaseISO = '{{ \Carbon\Carbon::parse($reportCardReleaseDate)->toIso8601String() }}';
    const releaseTime = new Date(releaseISO).getTime();

    const elDays  = document.getElementById('cd-days');
    const elHours = document.getElementById('cd-hours');
    const elMins  = document.getElementById('cd-mins');
    const elSecs  = document.getElementById('cd-secs');

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }

    function tick() {
        const now  = Date.now();
        const diff = releaseTime - now;

        if (diff <= 0) {
            // Time's up — reload so PHP re-evaluates the state
            window.location.reload();
            return;
        }

        const totalSecs = Math.floor(diff / 1000);
        const days  = Math.floor(totalSecs / 86400);
        const hours = Math.floor((totalSecs % 86400) / 3600);
        const mins  = Math.floor((totalSecs % 3600) / 60);
        const secs  = totalSecs % 60;

        // Flash border on seconds change
        elSecs.classList.add('tick');
        setTimeout(() => elSecs.classList.remove('tick'), 300);

        elDays.textContent  = pad(days);
        elHours.textContent = pad(hours);
        elMins.textContent  = pad(mins);
        elSecs.textContent  = pad(secs);
    }

    tick(); // run immediately — no blank state
    setInterval(tick, 1000);
})();
@endif
</script>
@endsection