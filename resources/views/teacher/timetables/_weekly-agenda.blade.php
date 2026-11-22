@once
<style id="teacher-agenda-styles">
    .teacher-agenda {
        --agenda-ink: #17243b;
        --agenda-muted: #748094;
        --agenda-line: #e7eaf0;
        --agenda-accent: #d26c4f;
        overflow: hidden;
        border: 1px solid var(--agenda-line);
        border-radius: 0 0 20px 20px;
        background: #fff;
    }
    .teacher-agenda__overview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .9rem 1.35rem;
        border-bottom: 1px solid var(--agenda-line);
        color: var(--agenda-muted);
        font-size: .73rem;
        font-weight: 750;
        letter-spacing: .12em;
        text-transform: uppercase;
    }
    .teacher-agenda__overview strong {
        color: var(--agenda-ink);
        font-size: .8rem;
        letter-spacing: 0;
        text-transform: none;
    }
    .teacher-agenda__day {
        display: grid;
        grid-template-columns: minmax(145px, 19%) minmax(0, 1fr);
        gap: 1.25rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--agenda-line);
    }
    .teacher-agenda__day:last-child { border-bottom: 0; }
    .teacher-agenda__day-heading {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding-top: .2rem;
    }
    .teacher-agenda__day-number {
        display: grid;
        width: 2rem;
        height: 2rem;
        flex: 0 0 2rem;
        place-items: center;
        border: 1px solid #e8e4dc;
        border-radius: 9px;
        background: #faf8f4;
        color: var(--agenda-accent);
        font-size: .7rem;
        font-weight: 800;
    }
    .teacher-agenda__day-name {
        display: block;
        color: var(--agenda-ink);
        font-size: .93rem;
        font-weight: 800;
        line-height: 1.25;
    }
    .teacher-agenda__day-count {
        display: block;
        margin-top: .22rem;
        color: var(--agenda-muted);
        font-size: .72rem;
    }
    .teacher-agenda__sessions {
        display: grid;
        gap: .7rem;
        min-width: 0;
    }
    .teacher-agenda__session {
        display: grid;
        grid-template-columns: minmax(135px, 21%) minmax(0, 1fr);
        overflow: hidden;
        border: 1px solid #e9ebef;
        border-radius: 13px;
        background: #fff;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .teacher-agenda__session:hover {
        transform: translateY(-1px);
        border-color: #d8dce4;
        box-shadow: 0 8px 20px rgba(23, 36, 59, .07);
    }
    .teacher-agenda__times {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: .45rem;
        padding: .85rem;
        border-right: 1px solid #e9ebef;
        background: #f7f8fa;
    }
    .teacher-agenda__time {
        display: grid;
        gap: .12rem;
        color: var(--agenda-ink);
        font-size: .8rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }
    .teacher-agenda__shift {
        color: var(--agenda-muted);
        font-size: .66rem;
        font-weight: 750;
        letter-spacing: .05em;
        text-transform: uppercase;
    }
    .teacher-agenda__session-body { min-width: 0; padding: .85rem 1rem; }
    .teacher-agenda__session-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
    }
    .teacher-agenda__session-title {
        margin: 0;
        color: var(--agenda-ink);
        font-size: .92rem;
        font-weight: 800;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }
    .teacher-agenda__badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: .35rem;
        padding: .28rem .55rem;
        border: 1px solid #eadbd3;
        border-radius: 999px;
        background: #fff7f2;
        color: #a44f35;
        font-size: .65rem;
        font-weight: 800;
        white-space: nowrap;
    }
    .teacher-agenda__courses {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        margin-top: .55rem;
    }
    .teacher-agenda__course {
        display: inline-flex;
        padding: .26rem .52rem;
        border: 1px solid #dfe9e8;
        border-radius: 6px;
        background: #f1f7f6;
        color: #285d59;
        font-size: .68rem;
        font-weight: 700;
        line-height: 1.3;
    }
    .teacher-agenda__details {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem 1rem;
        margin-top: .72rem;
        color: #647084;
        font-size: .73rem;
    }
    .teacher-agenda__detail { display: inline-flex; align-items: center; gap: .38rem; }
    .teacher-agenda__detail i { color: #a0a8b5; font-size: .72rem; }
    .teacher-agenda__note {
        margin: .65rem 0 0;
        padding-top: .55rem;
        border-top: 1px dashed #e6e8ed;
        color: #697488;
        font-size: .74rem;
        overflow-wrap: anywhere;
    }
    .teacher-agenda__empty {
        display: flex;
        min-height: 3.5rem;
        align-items: center;
        gap: .6rem;
        padding: .65rem .85rem;
        border: 1px dashed #e4e6eb;
        border-radius: 11px;
        color: #939baa;
        font-size: .78rem;
    }
    .teacher-agenda__empty i { color: #aab1bd; }
    @media (max-width: 767.98px) {
        .teacher-agenda__day { grid-template-columns: 1fr; gap: .8rem; padding: 1rem; }
        .teacher-agenda__day-heading { align-items: center; }
        .teacher-agenda__session { grid-template-columns: minmax(115px, 25%) minmax(0, 1fr); }
    }
    @media (max-width: 520px) {
        .teacher-agenda__overview { align-items: flex-start; flex-direction: column; gap: .2rem; padding: .8rem 1rem; }
        .teacher-agenda__session { grid-template-columns: 1fr; }
        .teacher-agenda__times { flex-direction: row; flex-wrap: wrap; align-items: center; gap: .35rem 1rem; border-right: 0; border-bottom: 1px solid #e9ebef; }
        .teacher-agenda__time { grid-template-columns: auto auto; align-items: baseline; gap: .45rem; }
        .teacher-agenda__session-heading { align-items: flex-start; }
        .teacher-agenda__day { padding: .9rem .75rem; }
    }
    @media print {
        .teacher-agenda { border-color: #cbd0d9; border-radius: 0; }
        .teacher-agenda__session { break-inside: avoid; box-shadow: none; }
        .teacher-agenda__day { break-inside: avoid; }
    }
</style>

<script>
function downloadTimetable(timetableId) {
    const agenda = document.getElementById('teacher-agenda-' + timetableId);
    if (!agenda) return;

    const title = agenda.dataset.title || 'Teaching timetable';
    const exportHeading = agenda.dataset.exportHeading || 'Teaching timetable';
    const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
    const clone = agenda.cloneNode(true);
    const styles = document.getElementById('teacher-agenda-styles')?.textContent || '';
    const details = [
        agenda.dataset.branch,
        agenda.dataset.academicYear,
        agenda.dataset.term,
        agenda.dataset.validPeriod
    ].filter(Boolean).join(' · ');
    const html = `<!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>${escapeHtml(title)} · ${escapeHtml(exportHeading)}</title>
            <style>
                ${styles}
                body { margin: 0; padding: 28px; background: #fff; color: #17243b; font-family: Arial, sans-serif; }
                .teacher-agenda__export-heading { margin: 0 auto 18px; max-width: 1100px; }
                .teacher-agenda__export-heading h1 { margin: 0 0 6px; font-size: 24px; }
                .teacher-agenda__export-heading p { margin: 0; color: #657187; font-size: 13px; }
                .teacher-agenda { max-width: 1100px; margin: 0 auto; border-radius: 14px; }
                @media print { body { padding: 0; } }
            </style>
        </head>
        <body>
            <header class="teacher-agenda__export-heading">
                <p>${escapeHtml(exportHeading)}</p>
                <h1>${escapeHtml(title)}</h1>
                <p>${escapeHtml(details)}</p>
            </header>
            ${clone.outerHTML}
        </body>
        </html>`;

    const url = URL.createObjectURL(new Blob([html], { type: 'text/html;charset=utf-8' }));
    const anchor = document.createElement('a');
    const filename = title.replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '').toLowerCase() || 'teaching-timetable';
    anchor.href = url;
    anchor.download = `${filename}-timetable.html`;
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
}
</script>
@endonce

@php
    $weekDays = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
    $sessionTotal = $sessions->count();
    $shiftFilter = $shiftFilter ?? '';
@endphp

<div
    class="teacher-agenda"
    id="teacher-agenda-{{ $timetable->id }}"
    data-title="{{ $timetable->title }}"
    data-export-heading="{{ $exportHeading ?? 'Teaching timetable' }}"
    data-branch="{{ $timetable->branch->name ?? 'Branch not specified' }}"
    data-academic-year="{{ $timetable->academicYear->year ?? 'Academic year not specified' }}"
    data-term="{{ $timetable->term->name ?? 'Term not specified' }}"
    data-valid-period="{{ $timetable->valid_from?->format('d M Y') ?? 'N/A' }} - {{ $timetable->valid_until?->format('d M Y') ?? 'N/A' }}"
>
    <div class="teacher-agenda__overview">
        <span>Weekly schedule</span>
        <strong>{{ $sessionTotal }} {{ \Illuminate\Support\Str::plural('session', $sessionTotal) }} across 7 days</strong>
    </div>
    @foreach($weekDays as $dayNum => $dayName)
        @php
            $daySessions = $sessions->where('day_of_week', $dayNum)->sortBy(function ($session) use ($shiftFilter) {
                $selectedValues = $shiftFilter === 'morning' ? ['morning', 'day'] : [$shiftFilter];
                $shifts = $shiftFilter
                    ? $session->shifts->filter(fn ($shift) => in_array($shift->shift, $selectedValues, true))
                    : $session->shifts;
                $shiftStart = $shifts->pluck('start_time')->filter()->sort()->first();
                return $shiftStart ?: ($session->start_time ?: '99:99');
            });
        @endphp
        <section class="teacher-agenda__day" aria-label="{{ $dayName }} schedule">
            <div class="teacher-agenda__day-heading">
                <span class="teacher-agenda__day-number">{{ sprintf('%02d', $loop->iteration) }}</span>
                <span>
                    <span class="teacher-agenda__day-name">{{ $dayName }}</span>
                    <span class="teacher-agenda__day-count">{{ $daySessions->count() }} {{ \Illuminate\Support\Str::plural('session', $daySessions->count()) }}</span>
                </span>
            </div>
            <div class="teacher-agenda__sessions">
                @forelse($daySessions as $session)
                    @php
                        $selectedValues = $shiftFilter === 'morning' ? ['morning', 'day'] : [$shiftFilter];
                        $visibleShifts = $shiftFilter
                            ? $session->shifts->filter(fn ($shift) => in_array($shift->shift, $selectedValues, true))
                            : $session->shifts;
                        $shiftBlocks = $visibleShifts->map(function ($shift) use ($session) {
                            $shiftName = $shift->shift === 'day' ? 'morning' : $shift->shift;

                            return [
                                'label' => ucfirst($shiftName ?: 'Session'),
                                'start' => $shift->start_time ?: $session->start_time,
                                'end' => $shift->end_time ?: $session->end_time,
                            ];
                        });
                        if ($shiftBlocks->isEmpty()) {
                            $shiftName = $session->shift === 'day' ? 'morning' : $session->shift;
                            $shiftBlocks->push([
                                'label' => $shiftName ? ucfirst($shiftName) : 'Scheduled',
                                'start' => $session->start_time,
                                'end' => $session->end_time,
                            ]);
                        }
                        $activityType = $session->activity_type ?: 'lesson';
                        $activityLabel = match ($activityType) {
                            'group_work' => 'Group work',
                            'self_study' => 'Self study',
                            'reserve' => 'Reserved time',
                            default => ucfirst(str_replace('_', ' ', $activityType)),
                        };
                        $sessionTitle = match ($activityType) {
                            'self_study' => 'Independent study',
                            'reserve' => $session->reserve_text ?: 'Reserved time',
                            default => $session->lesson?->title ?: $activityLabel,
                        };
                        $isTest = $session->session_type === 'test' || $session->mark_as_test;
                        $sessionType = $isTest ? 'Test' : ucfirst($session->session_type ?: 'Class');
                        $formatTime = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('H:i') : '--:--';
                        $teacherNames = collect();
                        if ($showTeachers ?? false) {
                            $teacherNames = collect($session->getAttribute('branch_teacher_names') ?? []);
                            if ($teacherNames->isEmpty() && $session->lesson?->teacher) {
                                $teacherNames->push($session->lesson->teacher->teacher_name);
                            }
                            if ($teacherNames->isEmpty() && $session->lesson) {
                                $teacherNames = $session->lesson->teachers->pluck('teacher_name')->filter();
                            }
                        }
                    @endphp
                    <article class="teacher-agenda__session">
                        <div class="teacher-agenda__times">
                            @foreach($shiftBlocks as $shiftBlock)
                                <div class="teacher-agenda__time">
                                    <span class="teacher-agenda__shift">{{ $shiftBlock['label'] }}</span>
                                    <span>{{ $formatTime($shiftBlock['start']) }} – {{ $formatTime($shiftBlock['end']) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="teacher-agenda__session-body">
                            <div class="teacher-agenda__session-heading">
                                <h3 class="teacher-agenda__session-title">{{ $sessionTitle }}</h3>
                                <span class="teacher-agenda__badge">
                                    <i class="fa-solid {{ $isTest ? 'fa-clipboard-check' : 'fa-book-open' }}" aria-hidden="true"></i>
                                    {{ $sessionType }}
                                </span>
                            </div>
                            <div class="teacher-agenda__courses">
                                @forelse($session->courses as $course)
                                    <span class="teacher-agenda__course">{{ $course->course_name }}</span>
                                @empty
                                    <span class="teacher-agenda__course">Course not specified</span>
                                @endforelse
                            </div>
                            <div class="teacher-agenda__details">
                                <span class="teacher-agenda__detail">
                                    <i class="fa-solid fa-layer-group" aria-hidden="true"></i>{{ $activityLabel }}
                                </span>
                                <span class="teacher-agenda__detail">
                                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $session->classroom ?: 'Room not assigned' }}
                                </span>
                                @if($showTeachers ?? false)
                                    <span class="teacher-agenda__detail">
                                        <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                                        @if($teacherNames->isNotEmpty())
                                            {{ $teacherNames->join(', ') }}
                                        @elseif(in_array($activityType, ['lesson', 'group_work'], true))
                                            Teacher to be assigned
                                        @else
                                            Teacher not applicable
                                        @endif
                                    </span>
                                @endif
                                @if($isTest && $session->minimum_paid_percentage !== null)
                                    <span class="teacher-agenda__detail">
                                        <i class="fa-solid fa-percent" aria-hidden="true"></i>Minimum paid: {{ number_format((float) $session->minimum_paid_percentage, 0) }}%
                                    </span>
                                @endif
                            </div>
                            @if($session->notes)
                                <p class="teacher-agenda__note"><strong>Notes:</strong> {{ $session->notes }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="teacher-agenda__empty">
                        <i class="fa-regular fa-circle-check" aria-hidden="true"></i>
                        No sessions scheduled
                    </div>
                @endforelse
            </div>
        </section>
    @endforeach
</div>
