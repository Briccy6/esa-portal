@extends('students.app')

@section('content')
<style>
    .test-skills-page {
        --test-ink: #172b4d;
        --test-muted: #63748b;
        --test-border: #e2e8f0;
        --test-blue: #2457a7;
        --test-blue-dark: #1d4688;
        max-width: 1320px;
        margin: 0 auto;
        padding: clamp(1rem, 3vw, 2rem);
        color: var(--test-ink);
        font-family: "Aptos", "Segoe UI", sans-serif;
    }

    .test-skills-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: clamp(1.25rem, 3vw, 2rem);
        border: 1px solid var(--test-border);
        border-radius: 16px;
        background: #fff;
    }

    .test-skills-heading-icon {
        display: grid;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        place-items: center;
        border: 1px solid #d5e2f5;
        border-radius: 13px;
        background: #edf4ff;
        color: var(--test-blue);
        font-size: 1.35rem;
    }

    .test-skills-eyebrow {
        margin: 0 0 .35rem;
        color: var(--test-blue);
        font-size: .73rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .test-skills-title {
        margin: 0;
        color: var(--test-ink);
        font-size: clamp(1.55rem, 3vw, 2rem);
        font-weight: 700;
        letter-spacing: -.035em;
    }

    .test-skills-intro {
        max-width: 680px;
        margin: .45rem 0 0;
        color: var(--test-muted);
        font-size: .98rem;
        line-height: 1.6;
    }

    .test-skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr));
        gap: 1.1rem;
    }

    .test-skill-card {
        display: flex;
        min-width: 0;
        flex-direction: column;
        padding: 1.35rem;
        border: 1px solid var(--test-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(23, 43, 77, .045);
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .test-skill-card:hover {
        transform: translateY(-2px);
        border-color: #c5d5ec;
        box-shadow: 0 10px 24px rgba(23, 43, 77, .09);
    }

    .test-skill-card-top,
    .test-skill-meta,
    .test-skill-meta-item,
    .test-skill-actions {
        display: flex;
        align-items: center;
    }

    .test-skill-card-top {
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .test-skill-type {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: var(--test-muted);
        font-size: .78rem;
        font-weight: 600;
    }

    .test-skill-type i {
        color: var(--test-blue);
        font-size: 1rem;
    }

    .test-skill-status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .65rem;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .test-skill-status-ready {
        border-color: #b7e4ca;
        background: #effaf3;
        color: #17643a;
    }

    .test-skill-status-completed {
        border-color: #c7d8f1;
        background: #f0f5fc;
        color: #244e86;
    }

    .test-skill-status-missed {
        border-color: #e2e8f0;
        background: #f6f8fa;
        color: #596579;
    }

    .test-skill-name {
        margin: 0;
        color: var(--test-ink);
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .test-skill-description {
        min-height: 3.2em;
        margin: .55rem 0 1.1rem;
        color: var(--test-muted);
        font-size: .92rem;
        line-height: 1.6;
    }

    .test-skill-meta {
        flex-wrap: wrap;
        gap: .65rem 1rem;
        margin-bottom: 1rem;
        color: #52657e;
        font-size: .84rem;
    }

    .test-skill-meta-item {
        gap: .45rem;
    }

    .test-skill-meta-item i {
        color: var(--test-blue);
    }

    .test-skill-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .7rem;
        margin-bottom: 1.2rem;
    }

    .test-skill-detail {
        padding: .8rem .9rem;
        border: 1px solid #e6edf6;
        border-radius: 10px;
        background: #f7f9fc;
    }

    .test-skill-detail-label {
        display: block;
        margin-bottom: .25rem;
        color: var(--test-muted);
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .test-skill-detail-value {
        color: var(--test-ink);
        font-size: 1rem;
        font-weight: 700;
    }

    .test-skill-actions {
        justify-content: flex-start;
        margin-top: auto;
    }

    .test-skill-button {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1rem;
        border: 1px solid var(--test-blue);
        border-radius: 8px;
        background: var(--test-blue);
        color: #fff;
        font-size: .88rem;
        font-weight: 700;
        text-decoration: none;
        transition: background-color .18s ease, border-color .18s ease;
    }

    .test-skill-button:hover,
    .test-skill-button:focus-visible {
        border-color: var(--test-blue-dark);
        background: var(--test-blue-dark);
        color: #fff;
        text-decoration: none;
    }

    .test-skill-empty {
        grid-column: 1 / -1;
        padding: clamp(2rem, 6vw, 4rem) 1.5rem;
        border: 1px dashed #cbd6e4;
        border-radius: 14px;
        background: #fff;
        text-align: center;
    }

    .test-skill-empty-icon {
        display: grid;
        width: 54px;
        height: 54px;
        margin: 0 auto 1rem;
        place-items: center;
        border-radius: 14px;
        background: #edf4ff;
        color: var(--test-blue);
        font-size: 1.35rem;
    }

    .test-skill-empty-title {
        margin: 0 0 .4rem;
        color: var(--test-ink);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .test-skill-empty-copy {
        max-width: 480px;
        margin: 0 auto;
        color: var(--test-muted);
        font-size: .92rem;
        line-height: 1.6;
    }

    @media (max-width: 520px) {
        .test-skills-page {
            padding: .8rem;
        }

        .test-skills-header {
            gap: .75rem;
            padding: 1rem;
        }

        .test-skills-heading-icon {
            width: 44px;
            height: 44px;
            flex-basis: 44px;
            font-size: 1.15rem;
        }

        .test-skill-card {
            padding: 1rem;
        }

        .test-skill-description {
            min-height: 0;
        }

        .test-skill-actions,
        .test-skill-button {
            width: 100%;
        }
    }
</style>

<main class="test-skills-page" aria-labelledby="testSkillsTitle">
    <header class="test-skills-header">
        <div class="test-skills-heading-icon" aria-hidden="true">
            <i class="bi bi-patch-question"></i>
        </div>
        <div>
            <p class="test-skills-eyebrow">Learn · Practice · Improve</p>
            <h1 class="test-skills-title" id="testSkillsTitle">Test Skills</h1>
            <p class="test-skills-intro">
                Use these practice tests to check what you have learned, see where you are confident,
                and decide what to study next.
            </p>
        </div>
    </header>

    <section class="test-skills-grid" aria-label="Available tests">
        @forelse($exams as $exam)
            @php
                $examDate = \Carbon\Carbon::parse($exam->class_date);
                $hasMark = $exam->marks && $exam->marks->count() > 0;
                $isUpcoming = $examDate->isFuture() || $examDate->isToday();

                if ($hasMark) {
                    $statusLabel = 'Completed';
                    $statusClass = 'test-skill-status-completed';
                    $statusIcon = 'bi-check-circle';
                } elseif ($isUpcoming) {
                    $statusLabel = 'Ready to try';
                    $statusClass = 'test-skill-status-ready';
                    $statusIcon = 'bi-play-circle';
                } else {
                    $statusLabel = 'Not attempted';
                    $statusClass = 'test-skill-status-missed';
                    $statusIcon = 'bi-clock';
                }
            @endphp

            <article class="test-skill-card">
                <div class="test-skill-card-top">
                    <span class="test-skill-type"><i class="bi bi-journal-check" aria-hidden="true"></i>Practice test</span>
                    <span class="test-skill-status {{ $statusClass }}">
                        <i class="bi {{ $statusIcon }}" aria-hidden="true"></i>
                        {{ $statusLabel }}
                    </span>
                </div>

                <h2 class="test-skill-name">{{ $exam->title }}</h2>
                <p class="test-skill-description">
                    {{ $exam->description ?: 'Use this test to review your understanding and identify topics to study further.' }}
                </p>

                <div class="test-skill-meta">
                    <span class="test-skill-meta-item">
                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                        {{ $examDate->format('M d, Y') }}
                    </span>
                    <span class="test-skill-meta-item">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        {{ $examDate->format('g:i A') }}
                    </span>
                </div>

                <div class="test-skill-details" aria-label="Test information">
                    <div class="test-skill-detail">
                        <span class="test-skill-detail-label">Total marks</span>
                        <span class="test-skill-detail-value">{{ $exam->total_marks }}</span>
                    </div>
                    <div class="test-skill-detail">
                        <span class="test-skill-detail-label">Weight</span>
                        <span class="test-skill-detail-value">{{ $exam->exam_weight }}%</span>
                    </div>
                </div>

                <div class="test-skill-actions">
                    @if($isUpcoming)
                        @if(!$hasMark)
                            <a href="{{ route('student.exams.take', $exam->id) }}" class="test-skill-button">
                                <i class="bi bi-play-fill" aria-hidden="true"></i>
                                Start test
                            </a>
                        @endif
                    @elseif($hasMark)
                        <a href="{{ route('student.exams.marks', $exam->id) }}" class="test-skill-button">
                            <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
                            Review result
                        </a>
                    @endif
                </div>
            </article>
        @empty
            <div class="test-skill-empty">
                <div class="test-skill-empty-icon" aria-hidden="true">
                    <i class="bi bi-journal-check"></i>
                </div>
                <h2 class="test-skill-empty-title">No tests available yet</h2>
                <p class="test-skill-empty-copy">
                    When a practice test is scheduled for you, it will appear here. Check back later and keep working through your study materials.
                </p>
            </div>
        @endforelse
    </section>
</main>
@endsection
