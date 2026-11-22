@extends('students.app')

@section('content')
<div class="course-page">
    @php
        $allLessons = collect();
        foreach ($courses as $course) {
            foreach ($course->lessons as $lesson) {
                $allLessons->push(['course' => $course, 'lesson' => $lesson]);
            }
        }
        $lessonCount = $allLessons->count();
        $completedUnits = $allLessons->sum(fn ($item) =>
            $item['lesson']->units->whereNotNull('studied_record')->count()
        );
        $totalUnits = $allLessons->sum(fn ($item) => $item['lesson']->units->count());
    @endphp

    <header class="course-hero">
        <div>
            <p class="eyebrow">Academic workspace</p>
            <h1>My course lessons</h1>
            <p class="hero-copy">Review your lessons, teaching staff, and progress through every unit.</p>
        </div>
        <div class="hero-stats" aria-label="Course progress summary">
            <div>
                <strong>{{ $lessonCount }}</strong>
                <span>Lessons</span>
            </div>
            <div>
                <strong>{{ $completedUnits }}/{{ $totalUnits }}</strong>
                <span>Units done</span>
            </div>
        </div>
    </header>

    @if($lessonCount)
        <div class="course-toolbar">
            <label class="lesson-search">
                <i class="bi bi-search"></i>
                <input id="lessonSearch" type="search" placeholder="Search lesson or teacher" aria-label="Search lessons">
            </label>
            <div class="toolbar-status">
                <span class="status-dot"></span>
                <span>{{ $lessonCount }} active lesson{{ $lessonCount === 1 ? '' : 's' }}</span>
            </div>
        </div>

        <div class="lesson-grid" id="lessonGrid">
            @foreach($allLessons as $index => $item)
                @php
                    $course = $item['course'];
                    $lesson = $item['lesson'];
                    $teacher = $lesson->branch_teacher ?? $lesson->teacher;
                    $doneUnits = $lesson->units->whereNotNull('studied_record')->count();
                    $unitCount = $lesson->units->count();
                    $progress = $unitCount ? round(($doneUnits / $unitCount) * 100) : 0;
                    $modalId = 'lesson-' . $lesson->id;
                    $initials = $teacher
                        ? collect(preg_split('/\s+/', trim($teacher->teacher_name)))->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->implode('')
                        : 'T';
                    $teacherPhotoUrl = null;
                    if ($teacher) {
                        if ($teacher->photo) {
                            $teacherPhoto = trim((string) $teacher->photo);
                            if (str_starts_with($teacherPhoto, 'data:image')) {
                                $teacherPhotoUrl = $teacherPhoto;
                            } elseif (str_starts_with($teacherPhoto, 'base64,')) {
                                $teacherPhotoUrl = 'data:image/jpeg;base64,' . substr($teacherPhoto, 7);
                            } else {
                                $teacherPhotoUrl = 'data:image/jpeg;base64,' . $teacherPhoto;
                            }
                        } elseif ($teacher->google_avatar) {
                            $teacherPhotoUrl = $teacher->google_avatar;
                        }
                    }
                @endphp
                <article class="lesson-card"
                         data-search="{{ strtolower(($lesson->title ?? '') . ' ' . ($teacher?->teacher_name ?? '') . ' ' . ($course->course_name ?? '')) }}">
                    <div class="lesson-card-top">
                        <div class="lesson-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <span class="course-tag">{{ $course->course_name ?? 'Course' }}</span>
                    </div>

                    <div class="lesson-title-block">
                        <h2>{{ $lesson->title }}</h2>
                        <div class="teacher-row">
                            @if($teacherPhotoUrl)
                                <span class="teacher-avatar-img">
                                    <span class="teacher-avatar-fallback">{{ $initials }}</span>
                                    <img src="{{ $teacherPhotoUrl }}" alt="{{ $teacher->teacher_name }}" onerror="this.style.display='none'">
                                </span>
                            @else
                                <span class="teacher-avatar">{{ $initials }}</span>
                            @endif
                            <div>
                                <span>Instructor</span>
                                <strong>{{ $teacher->teacher_name ?? 'To be assigned' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="progress-block">
                        <div class="progress-label">
                            <span>Unit progress</span>
                            <strong>{{ $doneUnits }}/{{ $unitCount }} done</strong>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <button type="button"
                            class="view-details-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#{{ $modalId }}">
                        <span>View lesson details</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </article>

                <div class="modal fade lesson-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                        <div class="modal-content">
                            <div class="modal-header lesson-modal-header">
                                <div>
                                    <p>{{ $course->course_name ?? 'Course' }}</p>
                                    <h3 class="modal-title">{{ $lesson->title }}</h3>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body lesson-modal-body">
                                <div class="lesson-overview">
                                    <div class="overview-instructor">
                                        @if($teacherPhotoUrl)
                                            <span class="teacher-avatar-img large">
                                                <span class="teacher-avatar-fallback">{{ $initials }}</span>
                                                <img src="{{ $teacherPhotoUrl }}" alt="{{ $teacher->teacher_name }}" onerror="this.style.display='none'">
                                            </span>
                                        @else
                                            <span class="teacher-avatar large">{{ $initials }}</span>
                                        @endif
                                        <div>
                                            <span>Lesson teacher</span>
                                            <strong>{{ $teacher->teacher_name ?? 'To be assigned' }}</strong>
                                        </div>
                                    </div>
                                    <div class="overview-progress">
                                        <span>Completion</span>
                                        <strong>{{ $progress }}%</strong>
                                    </div>
                                </div>

                                <div class="unit-heading">
                                    <div>
                                        <p>Curriculum</p>
                                        <h4>Lesson units</h4>
                                    </div>
                                    <span>{{ $doneUnits }} completed</span>
                                </div>

                                @if($unitCount)
                                    <div class="unit-list">
                                        @foreach($lesson->units as $unitIndex => $unit)
                                            @php
                                                $studied = $unit->studied_record;
                                                $unitModalTerm = $unit->term?->name ?? $unit->term?->title ?? 'Current term';
                                            @endphp
                                            <div class="unit-row {{ $studied ? 'is-done' : '' }}">
                                                <div class="unit-index">
                                                    @if($studied)
                                                        <i class="bi bi-check-lg"></i>
                                                    @else
                                                        {{ str_pad($unitIndex + 1, 2, '0', STR_PAD_LEFT) }}
                                                    @endif
                                                </div>
                                                <div class="unit-info">
                                                    <h5>{{ $unit->unit_name }}</h5>
                                                    <div class="unit-meta">
                                                        <span><i class="bi bi-clock"></i>{{ $unit->hours ?? '-' }} hours</span>
                                                        <span><i class="bi bi-layers"></i>{{ ucfirst($unit->course_level ?? 'Not set') }}</span>
                                                        <span><i class="bi bi-calendar3"></i>{{ $unitModalTerm }}</span>
                                                    </div>
                                                </div>
                                                @if($studied)
                                                    <div class="done-block">
                                                        <span class="done-badge"><i class="bi bi-check-circle-fill"></i> Done</span>
                                                        @if($studied->date)
                                                            <small>{{ \Carbon\Carbon::parse($studied->date)->format('M d, Y') }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="pending-badge">Not studied</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-units">
                                        <i class="bi bi-journal-x"></i>
                                        <h5>No units have been added</h5>
                                        <p>The lesson units will appear here once they are published.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="no-search-results" id="noSearchResults" hidden>
            <i class="bi bi-search"></i>
            <h3>No matching lessons</h3>
            <p>Try searching by lesson name or teacher.</p>
        </div>
    @else
        <div class="empty-lessons">
            <i class="bi bi-journal-bookmark"></i>
            <h2>No lessons assigned</h2>
            <p>Your course lessons will appear here when they are available.</p>
        </div>
    @endif
</div>

<style>
:root {
    --course-navy: #13233a;
    --course-blue: #1d4ed8;
    --course-cyan: #53bde8;
    --course-ink: #172033;
    --course-muted: #6d7585;
    --course-line: #e7ebf2;
}

.course-page {
    max-width: 1180px;
    margin: 0 auto;
    padding: 28px 8px 48px;
}

.course-hero {
    min-height: 190px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 32px;
    position: relative;
    overflow: hidden;
    margin-bottom: 22px;
    padding: 38px 42px;
    border-radius: 22px;
    background:
        radial-gradient(circle at 82% 18%, rgba(83, 189, 232, .24), transparent 21%),
        linear-gradient(135deg, #101f35 0%, #1a3556 68%, #244768 100%);
    color: #fff;
    box-shadow: 0 18px 45px rgba(19, 35, 58, .18);
}

.course-hero::after {
    content: "";
    position: absolute;
    right: 35%;
    bottom: -80px;
    width: 170px;
    height: 170px;
    border: 1px solid rgba(255, 255, 255, .09);
    border-radius: 50%;
}

.eyebrow,
.unit-heading p {
    margin: 0 0 8px;
    color: #8fd3f2;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .16em;
    text-transform: uppercase;
}

.course-hero h1 {
    margin: 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(2rem, 4vw, 3.25rem);
    line-height: 1;
    letter-spacing: -.035em;
}

.hero-copy {
    margin: 14px 0 0;
    color: #c6d3e3;
    font-size: .92rem;
}

.hero-stats {
    display: flex;
    flex-shrink: 0;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.hero-stats div {
    min-width: 118px;
    padding: 16px 18px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 14px;
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(12px);
}

.hero-stats strong,
.hero-stats span {
    display: block;
}

.hero-stats strong {
    font-size: 1.4rem;
}

.hero-stats span {
    color: #bdcce0;
    font-size: .75rem;
}

.course-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}

.lesson-search {
    width: min(420px, 100%);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    padding: 11px 14px;
    border: 1px solid var(--course-line);
    border-radius: 12px;
    background: #fff;
    color: var(--course-muted);
    box-shadow: 0 5px 18px rgba(20,35,58,.04);
}

.lesson-search input {
    width: 100%;
    border: 0;
    outline: 0;
    color: var(--course-ink);
}

.lesson-search input::placeholder {
    color: #a0a7b4;
}

.toolbar-status {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--course-muted);
    font-size: .82rem;
    font-weight: 700;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22b07d;
    box-shadow: 0 0 0 4px #def5eb;
}

.lesson-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.lesson-card {
    min-width: 0;
    padding: 22px;
    border: 1px solid var(--course-line);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 8px 28px rgba(26, 43, 67, .055);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.lesson-card:hover {
    transform: translateY(-4px);
    border-color: #cbd8e8;
    box-shadow: 0 16px 36px rgba(26, 43, 67, .11);
}

.lesson-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
}

.lesson-number {
    color: #9aa4b4;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.15rem;
    font-weight: 700;
}

.course-tag {
    max-width: 170px;
    overflow: hidden;
    padding: 6px 9px;
    border-radius: 999px;
    background: #edf4ff;
    color: #315b96;
    font-size: .68rem;
    font-weight: 800;
    text-overflow: ellipsis;
    text-transform: uppercase;
    white-space: nowrap;
}

.lesson-title-block {
    min-height: 128px;
}

.lesson-title-block h2 {
    min-height: 52px;
    margin: 0 0 18px;
    color: var(--course-ink);
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.28rem;
    line-height: 1.25;
}

.teacher-row,
.overview-instructor {
    display: flex;
    align-items: center;
    gap: 10px;
}

.teacher-avatar {
    width: 38px;
    height: 38px;
    display: grid;
    flex-shrink: 0;
    place-items: center;
    border-radius: 50%;
    background: var(--course-navy);
    color: #fff;
    font-size: .72rem;
    font-weight: 800;
}

.teacher-avatar-img {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 50%;
    overflow: hidden;
    background: var(--course-navy);
}

.teacher-avatar-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.teacher-avatar-img.large {
    width: 48px;
    height: 48px;
}

.teacher-avatar-img {
    width: 38px;
    height: 38px;
    display: grid;
    flex-shrink: 0;
    position: relative;
    place-items: center;
    overflow: hidden;
    border: 2px solid #edf1f6;
    border-radius: 50%;
    background: #edf1f6;
}

.teacher-avatar-img img {
    width: 100%;
    height: 100%;
    display: block;
    position: relative;
    z-index: 1;
    object-fit: cover;
}

.teacher-avatar-fallback {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    background: var(--course-navy);
    color: #fff;
    font-size: .72rem;
    font-weight: 800;
}

.teacher-avatar-img.large {
    width: 48px;
    height: 48px;
}

.teacher-avatar.large {
    width: 48px;
    height: 48px;
    font-size: .82rem;
}

.teacher-row span,
.overview-instructor span,
.overview-progress span {
    display: block;
    color: #9aa2af;
    font-size: .68rem;
    text-transform: uppercase;
}

.teacher-row strong,
.overview-instructor strong {
    display: block;
    color: var(--course-ink);
    font-size: .84rem;
}

.progress-block {
    padding: 14px 0 18px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    color: var(--course-muted);
    font-size: .72rem;
}

.progress-label strong {
    color: var(--course-ink);
}

.progress-track {
    height: 5px;
    overflow: hidden;
    border-radius: 99px;
    background: #edf0f5;
}

.progress-fill {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, #1d4ed8, #53bde8);
}

.view-details-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border: 1px solid #dbe3ef;
    border-radius: 10px;
    background: #fff;
    color: var(--course-navy);
    font-size: .78rem;
    font-weight: 800;
    transition: .2s ease;
}

.view-details-btn:hover {
    border-color: var(--course-navy);
    background: var(--course-navy);
    color: #fff;
}

.lesson-modal .modal-content {
    overflow: hidden;
    border: 0;
    border-radius: 18px;
}

.lesson-modal-header {
    min-height: 116px;
    align-items: center;
    border: 0;
    background: var(--course-navy);
    color: #fff;
}

.lesson-modal-header p {
    margin: 0 0 5px;
    color: #84ccec;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.lesson-modal-header h3 {
    margin: 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.55rem;
}

.lesson-modal-body {
    padding: 24px 28px 28px;
    background: #f7f9fc;
}

.lesson-overview {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding: 14px 16px;
    border: 1px solid var(--course-line);
    border-radius: 12px;
    background: #fff;
}

.overview-progress {
    text-align: right;
}

.overview-progress strong {
    display: block;
    color: var(--course-blue);
    font-size: 1.25rem;
}

.unit-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 12px;
}

.unit-heading p {
    color: #8a95a6;
}

.unit-heading h4 {
    margin: 0;
    color: var(--course-ink);
    font-family: Georgia, 'Times New Roman', serif;
}

.unit-heading > span {
    color: #22a371;
    font-size: .76rem;
    font-weight: 800;
}

.unit-list {
    overflow: hidden;
    border: 1px solid var(--course-line);
    border-radius: 12px;
    background: #fff;
}

.unit-row {
    display: grid;
    grid-template-columns: 44px minmax(0, 1fr) auto;
    align-items: center;
    gap: 13px;
    padding: 15px 16px;
    border-bottom: 1px solid #edf0f5;
}

.unit-row:last-child {
    border-bottom: 0;
}

.unit-row.is-done {
    background: linear-gradient(90deg, #f4fbf8 0%, #fff 55%);
}

.unit-index {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border: 1px solid #dfe5ed;
    border-radius: 50%;
    color: #8690a0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: .78rem;
    font-weight: 700;
}

.is-done .unit-index {
    border: 0;
    background: #22a371;
    color: #fff;
}

.unit-info h5 {
    margin: 0 0 5px;
    color: var(--course-ink);
    font-size: .9rem;
}

.unit-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 15px;
    color: #8992a0;
    font-size: .68rem;
}

.unit-meta i {
    margin-right: 5px;
}

.done-block {
    min-width: 86px;
    text-align: right;
}

.done-badge,
.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 8px;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 800;
}

.done-badge {
    background: #dcf5e9;
    color: #16885e;
}

.done-block small {
    display: block;
    margin-top: 4px;
    color: #96a0ae;
    font-size: .62rem;
}

.pending-badge {
    background: #f1f3f6;
    color: #7e8795;
}

.empty-units,
.empty-lessons,
.no-search-results {
    text-align: center;
}

.empty-units {
    padding: 38px;
    border: 1px dashed #d7dee8;
    border-radius: 12px;
    background: #fff;
}

.empty-units i,
.empty-lessons i,
.no-search-results i {
    display: block;
    margin-bottom: 10px;
    color: #91a0b5;
    font-size: 2rem;
}

.empty-units h5,
.empty-lessons h2,
.no-search-results h3 {
    margin: 0 0 5px;
}

.empty-units p,
.empty-lessons p,
.no-search-results p {
    margin: 0;
    color: var(--course-muted);
}

.empty-lessons {
    padding: 80px 20px;
    border: 1px dashed #d7dee8;
    border-radius: 16px;
    background: #fff;
}

.no-search-results {
    padding: 50px;
}

@media (max-width: 768px) {
    .course-page { padding: 16px 0 32px; }
    .course-hero {
        min-height: 220px;
        align-items: flex-start;
        flex-direction: column;
        padding: 28px 24px;
    }
    .hero-stats { width: 100%; }
    .hero-stats div { flex: 1; min-width: 0; }
    .course-toolbar { align-items: stretch; flex-direction: column; }
    .lesson-search { width: 100%; }
    .lesson-grid { grid-template-columns: 1fr; }
    .lesson-title-block { min-height: 112px; }
    .lesson-modal-body { padding: 18px 14px; }
    .lesson-modal-header { min-height: 100px; }
    .lesson-overview { align-items: flex-start; }
    .unit-row {
        grid-template-columns: 38px minmax(0, 1fr);
    }
    .done-block,
    .pending-badge {
        grid-column: 2;
        min-width: 0;
        text-align: left;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('lessonSearch');
    const cards = Array.from(document.querySelectorAll('.lesson-card'));
    const noResults = document.getElementById('noSearchResults');

    if (search) {
        search.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visible = 0;

            cards.forEach(function (card) {
                const matches = card.dataset.search.includes(query);
                card.hidden = !matches;
                if (matches) visible++;
            });

            if (noResults) noResults.hidden = visible !== 0;
        });
    }
});
</script>
@endsection
