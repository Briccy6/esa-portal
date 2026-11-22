@php
    $currentTimetable = $timetable ?? null;
    $oldSessionRows = old('sessions');
    if ($oldSessionRows === null && $editing) {
        $oldSessionRows = $currentTimetable->sessions->map(function ($session) {
            $row = $session->toArray();
            $row['course_ids'] = $session->courses->pluck('id')->all();
            $row['shifts'] = $session->shifts->pluck('shift')->all();
            $row['lesson_title'] = $session->lesson->title ?? '';
            $row['shift_times'] = $session->shifts->mapWithKeys(function ($shift) {
                return [$shift->shift => [
                    'start_time' => $shift->start_time ? substr($shift->start_time, 0, 5) : '',
                    'end_time' => $shift->end_time ? substr($shift->end_time, 0, 5) : '',
                ]];
            })->toArray();
            return $row;
        })->all();
    }
    if (!$oldSessionRows) {
        $oldSessionRows = [[]];
    }
    $selectedBranchId = old('branch_id', $branchId);
    $selectedYearId = old('academic_year_id', $selectedAcademicYearId);
    $selectedTerm = old('term_id', $selectedTermId);
@endphp

<style>
    .schedule-builder {
        --ink: #1d2d2b;
        --muted: #64736e;
        --line: #dce5df;
        --paper: #f5f7f2;
        --green: #176b58;
        --sun: #f0c66c;
        max-width: 1440px;
        margin: 0 auto;
        padding: 1.25rem 0 3rem;
        color: var(--ink);
    }
    .schedule-builder * { box-sizing: border-box; }
    .builder-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1.5rem;
        padding: clamp(1.4rem, 4vw, 2.8rem);
        margin-bottom: 1.2rem;
        border-radius: 22px;
        color: #fff;
        background: radial-gradient(circle at 88% 10%, rgba(240,198,108,.24), transparent 26%), #1d3833;
    }
    .builder-hero:after {
        content: "";
        position: absolute;
        width: 250px;
        height: 250px;
        right: 3%;
        bottom: -180px;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 50%;
        box-shadow: 0 0 0 28px rgba(255,255,255,.035), 0 0 0 58px rgba(255,255,255,.025);
        pointer-events: none;
    }
    .builder-kicker { color: var(--sun); font-size: .73rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .builder-hero h1 { margin: .45rem 0 .35rem; color: #fff; font: 700 clamp(1.7rem, 4vw, 2.7rem)/1.08 Georgia, serif; }
    .builder-hero p { max-width: 650px; margin: 0; color: #c4d2cc; }
    .builder-status {
        z-index: 1;
        flex: 0 0 auto;
        padding: .55rem .85rem;
        border: 1px solid rgba(255,255,255,.24);
        border-radius: 999px;
        color: #f8e9be;
        background: rgba(255,255,255,.08);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .builder-alert { border-radius: 12px; }
    .builder-section {
        margin-bottom: 1rem;
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 26px rgba(29,56,51,.045);
    }
    section[aria-labelledby="sessions-title"] { overflow: visible; }
    .builder-section-head { display: flex; align-items: center; gap: .85rem; padding: 1.05rem 1.25rem; border-bottom: 1px solid #e8eee9; background: #fbfcf9; }
    .builder-step { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 11px; color: #fff; background: var(--green); font-size: .83rem; font-weight: 800; }
    .builder-section-head h2 { margin: 0; color: var(--ink); font: 700 1.1rem Georgia, serif; }
    .builder-section-head p { margin: .15rem 0 0; color: var(--muted); font-size: .84rem; }
    .builder-section-body { padding: clamp(1rem, 2.5vw, 1.5rem); }
    .schedule-builder .form-label { margin-bottom: .4rem; color: #364743; font-size: .82rem; font-weight: 750; }
    .schedule-builder .form-control, .schedule-builder .form-select {
        min-height: 46px;
        border: 1px solid #cedbd3;
        border-radius: 10px;
        color: var(--ink);
        background-color: #fff;
    }
    .schedule-builder .form-control:focus, .schedule-builder .form-select:focus {
        border-color: #39836f;
        box-shadow: 0 0 0 3px rgba(57,131,111,.13);
    }
    .builder-hint { display: block; margin-top: .35rem; color: var(--muted); font-size: .76rem; }
    .sessions-heading { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
    .sessions-heading h2 { margin: 0; color: var(--ink); font: 700 1.3rem Georgia, serif; }
    .sessions-heading {
        position: sticky;
        top: .75rem;
        z-index: 20;
        padding: .8rem 1rem;
        border: 1px solid var(--line);
        border-radius: 13px;
        background: rgba(255,255,255,.96);
        box-shadow: 0 8px 24px rgba(29,56,51,.1);
        backdrop-filter: blur(10px);
    }
    .session-card { margin-bottom: .85rem; overflow: hidden; border: 1px solid var(--line); border-radius: 15px; background: #fff; transition: border-color .2s, box-shadow .2s; }
    .session-card:focus-within { border-color: #8db4a7; box-shadow: 0 7px 22px rgba(29,56,51,.08); }
    .session-card-head { display: flex; align-items: center; gap: .7rem; padding: .9rem 1rem; border-bottom: 1px solid #edf1ed; background: #fbfcf9; }
    .session-index { display: grid; width: 30px; height: 30px; flex: 0 0 auto; place-items: center; border-radius: 50%; color: #20594d; background: #e2efe8; font-size: .8rem; font-weight: 800; }
    .session-card-title { margin: 0; font-size: .96rem; font-weight: 800; }
    .session-kind-pill { margin-left: auto; padding: .3rem .6rem; border-radius: 999px; color: #76531b; background: #fff2d6; font-size: .7rem; font-weight: 800; }
    .session-card-body { padding: 1rem; }
    .course-picker { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr)); gap: .5rem; }
    .course-option {
        display: flex;
        min-height: 48px;
        align-items: center;
        gap: .6rem;
        padding: .6rem .7rem;
        border: 1px solid #e0e8e2;
        border-radius: 10px;
        cursor: pointer;
    }
    .course-option:has(input:checked) { border-color: #72a393; background: #f0f7f3; }
    .course-option input { width: 17px; height: 17px; accent-color: var(--green); }
    .course-option-copy { min-width: 0; }
    .course-option-copy strong { display: block; font-size: .82rem; }
    .course-option-copy small { display: block; margin-top: .1rem; color: var(--muted); font-size: .72rem; }
    .teacher-preview { min-height: 20px; margin-top: .5rem; color: #176b58; font-size: .79rem; font-weight: 700; }
    .eligibility-panel { display: none; padding: .9rem; border: 1px solid #f0ddb0; border-radius: 11px; background: #fffbf0; }
    .eligibility-panel.is-visible { display: block; }
    .lesson-picker { position: relative; }
    .lesson-dropdown {
        position: absolute;
        top: calc(100% + .25rem);
        right: 0;
        left: 0;
        z-index: 30;
        display: none;
        max-height: 260px;
        overflow-y: auto;
        padding: .35rem;
        border: 1px solid var(--line);
        border-radius: 11px;
        background: #fff;
        box-shadow: 0 12px 30px rgba(29,56,51,.16);
    }
    .lesson-dropdown.is-open { display: block; }
    .lesson-result {
        display: block;
        width: 100%;
        padding: .65rem .75rem;
        border: 0;
        border-radius: 7px;
        color: var(--ink);
        background: transparent;
        text-align: left;
    }
    .lesson-result:hover, .lesson-result:focus { background: #eef6f1; outline: 0; }
    .lesson-empty { padding: .65rem .75rem; color: var(--muted); font-size: .82rem; }
    .shift-options { display: flex; flex-wrap: wrap; gap: .45rem; }
    .shift-choice {
        display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .7rem;
        border: 1px solid #dce5df; border-radius: 9px; background: #fff; cursor: pointer;
    }
    .shift-choice:has(input:checked) { border-color: #72a393; background: #f0f7f3; }
    .shift-choice input { accent-color: var(--green); }
    .shift-time-panel { display: none; padding: .8rem; border: 1px solid #dce5df; border-radius: 10px; background: #f8fbf8; }
    .shift-time-panel.is-visible { display: block; }
    .shift-time-title { margin-bottom: .65rem; color: var(--green); font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; }
    .activity-first { order: -1; }
    .builder-footer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .builder-footer-copy { color: var(--muted); font-size: .83rem; }
    .builder-actions { display: flex; gap: .55rem; flex-wrap: wrap; }
    .schedule-builder .btn-primary { border-color: var(--green); background: var(--green); }
    .schedule-builder .btn-primary:hover { border-color: #105544; background: #105544; }
    .approval-panel { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.2rem; border: 1px solid #dfe7df; border-radius: 15px; background: #fff; }
    .approval-panel p { margin: .2rem 0 0; color: var(--muted); font-size: .82rem; }
    .approval-actions { display: flex; gap: .5rem; flex-wrap: wrap; }
    #lesson-load-message { display: none; margin-bottom: .8rem; }
    @media (max-width: 700px) {
        .schedule-builder { padding-top: .35rem; }
        .builder-hero { align-items: flex-start; flex-direction: column; }
        .builder-status { align-self: flex-start; }
        .builder-section-head { align-items: flex-start; }
        .sessions-heading, .approval-panel { align-items: stretch; flex-direction: column; }
        .sessions-heading .btn, .builder-actions .btn, .approval-actions form, .approval-actions .btn { width: 100%; }
        .builder-footer { align-items: stretch; flex-direction: column; }
        .builder-actions, .approval-actions { display: grid; grid-template-columns: 1fr; }
    }
</style>

<div class="schedule-builder">
    <header class="builder-hero">
        <div>
            <div class="builder-kicker">Academic operations · Schedule studio</div>
            <h1>{{ $editing ? 'Tune the timetable' : 'Build a timetable' }}</h1>
            <p>Set the term and live dates, then place branch-assigned lessons into clear weekly sessions.</p>
        </div>
        @if($editing)
            <span class="builder-status">{{ ucfirst($currentTimetable->status) }}</span>
        @else
            <span class="builder-status">Saved as draft</span>
        @endif
    </header>

    @if(session('success')) <div class="alert alert-success builder-alert">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger builder-alert">{{ session('error') }}</div> @endif
    @if(session('warning')) <div class="alert alert-warning builder-alert">{{ session('warning') }}</div> @endif
    @if(session('validation_errors'))
        <div class="alert alert-warning builder-alert">
            <strong>Approval checks need attention</strong>
            <ul class="mb-0 mt-2">@foreach(session('validation_errors') as $issue)<li>{{ $issue }}</li>@endforeach</ul>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger builder-alert">
            <strong>Please review these items:</strong>
            <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $editing ? route('dos.timetable.update', $currentTimetable) : route('dos.timetable.store') }}" method="POST" id="timetable-form">
        @csrf
        @if($editing) @method('PUT') @endif

        <section class="builder-section" aria-labelledby="schedule-details-title">
            <div class="builder-section-head">
                <span class="builder-step">01</span>
                <div><h2 id="schedule-details-title">Schedule details</h2><p>Choose who this timetable is for and when it applies.</p></div>
            </div>
            <div class="builder-section-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="title">Timetable name</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title', $currentTimetable->title ?? '') }}" placeholder="e.g. Term 2 · Day classes" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="branch_id">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value="">Choose a branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="academic_year_id">Academic year</label>
                        <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                            <option value="">Choose an academic year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected($selectedYearId == $year->id)>{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="term_id">Term</label>
                        <select class="form-select" id="term_id" name="term_id" required>
                            <option value="">Choose a term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" data-year="{{ $term->academic_year_id }}"
                                    data-start="{{ \Carbon\Carbon::parse($term->start_date)->format('Y-m-d') }}"
                                    data-end="{{ \Carbon\Carbon::parse($term->end_date)->format('Y-m-d') }}"
                                    @selected($selectedTerm == $term->id)>
                                    {{ $term->name }} · {{ \Carbon\Carbon::parse($term->start_date)->format('M j') }}–{{ \Carbon\Carbon::parse($term->end_date)->format('M j, Y') }}
                                </option>
                            @endforeach
                        </select>
                        <small class="builder-hint">The timetable dates must remain within the selected term.</small>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="valid_from">First valid date</label>
                        <input type="date" class="form-control" id="valid_from" name="valid_from"
                            value="{{ old('valid_from', $currentTimetable?->valid_from?->format('Y-m-d') ?? '') }}" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="valid_until">Last valid date</label>
                        <input type="date" class="form-control" id="valid_until" name="valid_until"
                            value="{{ old('valid_until', $currentTimetable?->valid_until?->format('Y-m-d') ?? '') }}" required>
                    </div>
                </div>
            </div>
        </section>

        <section class="builder-section" aria-labelledby="sessions-title">
            <div class="builder-section-head">
                <span class="builder-step">02</span>
                <div><h2 id="sessions-title">Weekly sessions</h2><p>Pick a lesson to see its branch-assigned courses and teacher.</p></div>
            </div>
            <div class="builder-section-body">
                <div id="lesson-load-message" class="alert alert-danger" role="status"></div>
                <div class="sessions-heading">
                    <div><h2>Build the week</h2><small class="text-muted"><span id="session-count">{{ count($oldSessionRows) }}</span> sessions</small></div>
                    <button class="btn btn-outline-success" type="button" id="add-session"><i class="bi bi-plus-lg me-1"></i>Add a session</button>
                </div>
                <div id="sessions-container">
                    @foreach($oldSessionRows as $index => $sessionData)
                        @php
                            $rowType = !empty($sessionData['mark_as_test']) && ($sessionData['session_type'] ?? 'class') === 'class'
                                ? 'test'
                                : ($sessionData['session_type'] ?? 'class');
                            $rowCourseIds = old("sessions.{$index}.course_ids", $sessionData['course_ids'] ?? []);
                            $rowShifts = old("sessions.{$index}.shifts", $sessionData['shifts'] ?? [$sessionData['shift'] ?? 'day']);
                            $rowShiftTimes = old("sessions.{$index}.shift_times", $sessionData['shift_times'] ?? []);
                            $rowActivity = old("sessions.{$index}.activity_type", $sessionData['activity_type'] ?? 'lesson');
                            $legacyShift = $rowShifts[0] ?? null;
                            if ($legacyShift) {
                                $rowShiftTimes[$legacyShift] = [
                                    'start_time' => ($rowShiftTimes[$legacyShift]['start_time'] ?? '') ?: (isset($sessionData['start_time']) ? substr($sessionData['start_time'], 0, 5) : ''),
                                    'end_time' => ($rowShiftTimes[$legacyShift]['end_time'] ?? '') ?: (isset($sessionData['end_time']) ? substr($sessionData['end_time'], 0, 5) : ''),
                                ];
                            }
                        @endphp
                        <article class="session-card" data-index="{{ $index }}" data-lesson-id="{{ old("sessions.{$index}.lesson_id", $sessionData['lesson_id'] ?? '') }}" data-selected-courses="{{ implode(',', $rowCourseIds) }}">
                            <div class="session-card-head">
                                <span class="session-index">{{ $index + 1 }}</span>
                                <h3 class="session-card-title">Session {{ $index + 1 }}</h3>
                                <span class="session-kind-pill">{{ $rowType === 'self_study' ? 'Self study' : ucfirst($rowType) }}</span>
                                <button class="btn btn-sm btn-outline-danger remove-session ms-2" type="button" aria-label="Remove session {{ $index + 1 }}"><i class="bi bi-trash"></i></button>
                            </div>
                            <div class="session-card-body">
                                @if(!empty($sessionData['session_id']) || !empty($sessionData['id']))
                                    <input type="hidden" name="sessions[{{ $index }}][session_id]" value="{{ $sessionData['session_id'] ?? $sessionData['id'] }}">
                                @endif
                                <input type="hidden" name="sessions[{{ $index }}][start_time]" value="{{ old("sessions.{$index}.start_time", $sessionData['start_time'] ?? '') }}" data-primary-start>
                                <input type="hidden" name="sessions[{{ $index }}][end_time]" value="{{ old("sessions.{$index}.end_time", $sessionData['end_time'] ?? '') }}" data-primary-end>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6 activity-first">
                                        <label class="form-label" for="activity-{{ $index }}">Activity type</label>
                                        <select class="form-select activity-type" id="activity-{{ $index }}" name="sessions[{{ $index }}][activity_type]" required>
                                            <option value="lesson" @selected($rowActivity === 'lesson')>Lesson</option>
                                            <option value="self_study" @selected($rowActivity === 'self_study')>Self study</option>
                                            <option value="group_work" @selected($rowActivity === 'group_work')>Group work</option>
                                            <option value="reserve" @selected($rowActivity === 'reserve')>Reserve (no class)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="type-{{ $index }}">Session type</label>
                                        <select class="form-select session-type" id="type-{{ $index }}" name="sessions[{{ $index }}][session_type]">
                                            <option value="class" @selected($rowType === 'class')>Class</option>
                                            <option value="test" @selected($rowType === 'test')>Test / exam</option>
                                        </select>
                                    </div>
                                    <div class="col-12 lesson-picker-panel" data-lesson-picker-panel>
                                        <label class="form-label" for="lesson-search-{{ $index }}">Lesson <span class="lesson-required-mark">*</span></label>
                                        <div class="lesson-picker">
                                            <input type="text" class="form-control lesson-search" id="lesson-search-{{ $index }}" placeholder="Search by lesson name, even with a partial or misspelled match..." autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="lesson-options-{{ $index }}" value="{{ old("sessions.{$index}.lesson_title", $sessionData['lesson_title'] ?? '') }}">
                                            <input type="hidden" class="lesson-id-hidden" id="lesson-{{ $index }}" name="sessions[{{ $index }}][lesson_id]" value="{{ old("sessions.{$index}.lesson_id", $sessionData['lesson_id'] ?? '') }}">
                                            <div class="lesson-dropdown" id="lesson-options-{{ $index }}" role="listbox"></div>
                                        </div>
                                        <small class="builder-hint" data-lesson-hint>Search and select a lesson to show its assigned courses.</small>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label class="form-label" for="day-{{ $index }}">Day</label>
                                        <select class="form-select" id="day-{{ $index }}" name="sessions[{{ $index }}][day_of_week]" required>
                                            @foreach([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'] as $day => $name)
                                                <option value="{{ $day }}" @selected((string) old("sessions.{$index}.day_of_week", $sessionData['day_of_week'] ?? 1) === (string) $day)>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-8">
                                        <label class="form-label d-block">Shifts</label>
                                        <div class="shift-options">
                                            @foreach(['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening', 'weekend' => 'Weekend'] as $shift => $shiftLabel)
                                                <label class="shift-choice" for="shift-{{ $shift }}-{{ $index }}">
                                                    <input class="shift-checkbox" type="checkbox" id="shift-{{ $shift }}-{{ $index }}" name="sessions[{{ $index }}][shifts][]" value="{{ $shift }}" @checked(in_array($shift, $rowShifts, true))>
                                                    <span>{{ $shiftLabel }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <small class="builder-hint">Select one or more shifts. Only selected shifts need times.</small>
                                    </div>
                                    @foreach(['morning' => 'Morning shift', 'afternoon' => 'Afternoon shift', 'evening' => 'Evening shift', 'weekend' => 'Weekend shift'] as $shift => $shiftLabel)
                                        <div class="col-12 shift-time-column">
                                            <div class="shift-time-panel" data-shift-time="{{ $shift }}">
                                                <div class="shift-time-title">{{ $shiftLabel }} time</div>
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <label class="form-label" for="{{ $shift }}-start-{{ $index }}">Starts</label>
                                                        <input type="time" class="form-control shift-time-input" id="{{ $shift }}-start-{{ $index }}" name="sessions[{{ $index }}][shift_times][{{ $shift }}][start_time]" value="{{ $rowShiftTimes[$shift]['start_time'] ?? '' }}" data-time-kind="start">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label" for="{{ $shift }}-end-{{ $index }}">Ends</label>
                                                        <input type="time" class="form-control shift-time-input" id="{{ $shift }}-end-{{ $index }}" name="sessions[{{ $index }}][shift_times][{{ $shift }}][end_time]" value="{{ $rowShiftTimes[$shift]['end_time'] ?? '' }}" data-time-kind="end">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-12 col-lg-6 course-panel">
                                        <label class="form-label">Courses for this session</label>
                                        <input type="search" class="form-control mb-2 course-search" placeholder="Filter courses..." style="min-height: 40px;">
                                        <div class="course-picker" data-course-picker></div>
                                        <div class="teacher-preview" data-teacher-preview>Select an activity and lesson to see available courses.</div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label" for="room-{{ $index }}">Room / location</label>
                                        <input class="form-control" id="room-{{ $index }}" name="sessions[{{ $index }}][classroom]" value="{{ old("sessions.{$index}.classroom", $sessionData['classroom'] ?? '') }}" placeholder="Add a room or location">
                                    </div>
                                    <div class="col-12 col-md-6 activity-type-panel" data-activity-type-panel>
                                        <label class="form-label" for="reserve-text-{{ $index }}">Reserve instructions</label>
                                        <textarea class="form-control" id="reserve-text-{{ $index }}" name="sessions[{{ $index }}][reserve_text]" rows="2" placeholder="What should students do during this reserved time?">{{ old("sessions.{$index}.reserve_text", $sessionData['reserve_text'] ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="notes-{{ $index }}">Student instructions <span class="text-muted fw-normal">(optional)</span></label>
                                        <input class="form-control" id="notes-{{ $index }}" name="sessions[{{ $index }}][notes]" value="{{ old("sessions.{$index}.notes", $sessionData['notes'] ?? '') }}" placeholder="Optional details">
                                    </div>
                                    <div class="col-12 eligibility-panel" data-eligibility-panel>
                                        <label class="form-label" for="eligibility-{{ $index }}">Minimum fee percentage paid</label>
                                        <div class="input-group">
                                            <input type="number" min="0" max="100" step="0.01" class="form-control" id="eligibility-{{ $index }}" name="sessions[{{ $index }}][minimum_paid_percentage]" value="{{ old("sessions.{$index}.minimum_paid_percentage", $sessionData['minimum_paid_percentage'] ?? 0) }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="builder-hint">Students below this threshold will see why they are not eligible and how to become eligible.</small>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="builder-section">
            <div class="builder-section-body builder-footer">
                <div class="builder-footer-copy"><i class="bi bi-info-circle me-1"></i>Save your changes as a draft. Review conflicts before approving.</div>
                <div class="builder-actions">
                    <a href="{{ route('dos.timetable.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>{{ $editing ? 'Save changes' : 'Save draft' }}</button>
                </div>
            </div>
        </section>
    </form>

    @if($editing)
        <section class="approval-panel">
            <div><strong>{{ $currentTimetable->status === 'published' ? 'Published timetable' : 'Ready for review?' }}</strong>
                <p>{{ $currentTimetable->status === 'published' ? 'Students can see this schedule for their branch and course.' : 'Approval checks for room, teacher, and schedule conflicts run before publishing.' }}</p>
            </div>
            <div class="approval-actions">
                @if($currentTimetable->status === 'draft')
                    <form action="{{ route('dos.timetable.publish', $currentTimetable) }}" method="POST">
                        @csrf
                        <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle me-1"></i>Approve &amp; publish</button>
                    </form>
                    <form action="{{ route('dos.timetable.destroy', $currentTimetable) }}" method="POST" onsubmit="return confirm('Delete this draft timetable?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit">Delete draft</button>
                    </form>
                @endif
            </div>
        </section>
    @endif
</div>

<template id="session-template">
    <article class="session-card" data-index="__INDEX__" data-selected-courses="">
        <div class="session-card-head">
            <span class="session-index">__NUMBER__</span><h3 class="session-card-title">Session __NUMBER__</h3>
            <span class="session-kind-pill">Class</span>
            <button class="btn btn-sm btn-outline-danger remove-session ms-2" type="button" aria-label="Remove session"><i class="bi bi-trash"></i></button>
        </div>
        <div class="session-card-body">
            <input type="hidden" name="sessions[__INDEX__][start_time]" data-primary-start>
            <input type="hidden" name="sessions[__INDEX__][end_time]" data-primary-end>
            <div class="row g-3">
                <div class="col-12 col-md-6 activity-first">
                    <label class="form-label" for="activity-__INDEX__">Activity type</label>
                    <select class="form-select activity-type" id="activity-__INDEX__" name="sessions[__INDEX__][activity_type]" required>
                        <option value="lesson">Lesson</option><option value="self_study">Self study</option>
                        <option value="group_work">Group work</option><option value="reserve">Reserve (no class)</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="type-__INDEX__">Session type</label>
                    <select class="form-select session-type" id="type-__INDEX__" name="sessions[__INDEX__][session_type]"><option value="class">Class</option><option value="test">Test / exam</option></select>
                </div>
                <div class="col-12 lesson-picker-panel" data-lesson-picker-panel>
                    <label class="form-label" for="lesson-search-__INDEX__">Lesson <span class="lesson-required-mark">*</span></label>
                    <div class="lesson-picker">
                        <input type="text" class="form-control lesson-search" id="lesson-search-__INDEX__" placeholder="Search by lesson name, including partial or misspelled words..." autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="lesson-options-__INDEX__">
                        <input type="hidden" class="lesson-id-hidden" id="lesson-__INDEX__" name="sessions[__INDEX__][lesson_id]">
                        <div class="lesson-dropdown" id="lesson-options-__INDEX__" role="listbox"></div>
                    </div>
                    <small class="builder-hint" data-lesson-hint>Search and select a lesson to show its assigned courses.</small>
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="day-__INDEX__">Day</label>
                    <select class="form-select" id="day-__INDEX__" name="sessions[__INDEX__][day_of_week]" required>
                        <option value="1">Monday</option><option value="2">Tuesday</option><option value="3">Wednesday</option><option value="4">Thursday</option><option value="5">Friday</option><option value="6">Saturday</option><option value="0">Sunday</option>
                    </select>
                </div>
                <div class="col-12 col-lg-8">
                    <label class="form-label d-block">Shifts</label>
                    <div class="shift-options">
                        <label class="shift-choice" for="shift-morning-__INDEX__"><input class="shift-checkbox" type="checkbox" id="shift-morning-__INDEX__" name="sessions[__INDEX__][shifts][]" value="morning" checked><span>Morning</span></label>
                        <label class="shift-choice" for="shift-afternoon-__INDEX__"><input class="shift-checkbox" type="checkbox" id="shift-afternoon-__INDEX__" name="sessions[__INDEX__][shifts][]" value="afternoon"><span>Afternoon</span></label>
                        <label class="shift-choice" for="shift-evening-__INDEX__"><input class="shift-checkbox" type="checkbox" id="shift-evening-__INDEX__" name="sessions[__INDEX__][shifts][]" value="evening"><span>Evening</span></label>
                        <label class="shift-choice" for="shift-weekend-__INDEX__"><input class="shift-checkbox" type="checkbox" id="shift-weekend-__INDEX__" name="sessions[__INDEX__][shifts][]" value="weekend"><span>Weekend</span></label>
                    </div>
                    <small class="builder-hint">Only the shifts you select will ask for times.</small>
                </div>
                <div class="col-12 shift-time-column"><div class="shift-time-panel" data-shift-time="morning"><div class="shift-time-title">Morning shift time</div><div class="row g-3">
                    <div class="col-6"><label class="form-label" for="morning-start-__INDEX__">Starts</label><input type="time" class="form-control shift-time-input" id="morning-start-__INDEX__" name="sessions[__INDEX__][shift_times][morning][start_time]" data-time-kind="start"></div>
                    <div class="col-6"><label class="form-label" for="morning-end-__INDEX__">Ends</label><input type="time" class="form-control shift-time-input" id="morning-end-__INDEX__" name="sessions[__INDEX__][shift_times][morning][end_time]" data-time-kind="end"></div>
                </div></div></div>
                <div class="col-12 shift-time-column"><div class="shift-time-panel" data-shift-time="afternoon"><div class="shift-time-title">Afternoon shift time</div><div class="row g-3">
                    <div class="col-6"><label class="form-label" for="afternoon-start-__INDEX__">Starts</label><input type="time" class="form-control shift-time-input" id="afternoon-start-__INDEX__" name="sessions[__INDEX__][shift_times][afternoon][start_time]" data-time-kind="start"></div>
                    <div class="col-6"><label class="form-label" for="afternoon-end-__INDEX__">Ends</label><input type="time" class="form-control shift-time-input" id="afternoon-end-__INDEX__" name="sessions[__INDEX__][shift_times][afternoon][end_time]" data-time-kind="end"></div>
                </div></div></div>
                <div class="col-12 shift-time-column"><div class="shift-time-panel" data-shift-time="evening"><div class="shift-time-title">Evening shift time</div><div class="row g-3">
                    <div class="col-6"><label class="form-label" for="evening-start-__INDEX__">Starts</label><input type="time" class="form-control shift-time-input" id="evening-start-__INDEX__" name="sessions[__INDEX__][shift_times][evening][start_time]" data-time-kind="start"></div>
                    <div class="col-6"><label class="form-label" for="evening-end-__INDEX__">Ends</label><input type="time" class="form-control shift-time-input" id="evening-end-__INDEX__" name="sessions[__INDEX__][shift_times][evening][end_time]" data-time-kind="end"></div>
                </div></div></div>
                <div class="col-12 shift-time-column"><div class="shift-time-panel" data-shift-time="weekend"><div class="shift-time-title">Weekend shift time</div><div class="row g-3">
                    <div class="col-6"><label class="form-label" for="weekend-start-__INDEX__">Starts</label><input type="time" class="form-control shift-time-input" id="weekend-start-__INDEX__" name="sessions[__INDEX__][shift_times][weekend][start_time]" data-time-kind="start"></div>
                    <div class="col-6"><label class="form-label" for="weekend-end-__INDEX__">Ends</label><input type="time" class="form-control shift-time-input" id="weekend-end-__INDEX__" name="sessions[__INDEX__][shift_times][weekend][end_time]" data-time-kind="end"></div>
                </div></div></div>
                <div class="col-12 col-lg-6 course-panel">
                    <label class="form-label">Courses for this session</label><input type="search" class="form-control mb-2 course-search" placeholder="Filter courses...">
                    <div class="course-picker" data-course-picker></div><div class="teacher-preview" data-teacher-preview>Select an activity and lesson to see available courses.</div>
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label" for="room-__INDEX__">Room / location</label><input class="form-control" id="room-__INDEX__" name="sessions[__INDEX__][classroom]" placeholder="Add a room or location">
                </div>
                <div class="col-12 col-md-6 activity-type-panel" data-activity-type-panel>
                    <label class="form-label" for="reserve-text-__INDEX__">Reserve instructions</label><textarea class="form-control" id="reserve-text-__INDEX__" name="sessions[__INDEX__][reserve_text]" rows="2" placeholder="What should students do during this reserved time?"></textarea>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="notes-__INDEX__">Student instructions <span class="text-muted fw-normal">(optional)</span></label><input class="form-control" id="notes-__INDEX__" name="sessions[__INDEX__][notes]" placeholder="Optional details">
                </div>
                <div class="col-12 eligibility-panel" data-eligibility-panel>
                    <label class="form-label" for="eligibility-__INDEX__">Minimum fee percentage paid</label><div class="input-group"><input type="number" min="0" max="100" step="0.01" class="form-control" id="eligibility-__INDEX__" name="sessions[__INDEX__][minimum_paid_percentage]" value="0"><span class="input-group-text">%</span></div>
                    <small class="builder-hint">Students below this threshold will see why they are not eligible and how to become eligible.</small>
                </div>
            </div>
        </div>
    </article>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const branchSelect = document.getElementById('branch_id');
    const yearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term_id');
    const validFrom = document.getElementById('valid_from');
    const validUntil = document.getElementById('valid_until');
    const container = document.getElementById('sessions-container');
    const template = document.getElementById('session-template').innerHTML;
    const lessonMessage = document.getElementById('lesson-load-message');
    const form = document.getElementById('timetable-form');
    let lessonData = @json($branchLessonsData);
    const storageKey = @json('timetableFormData:' . auth('staff')->id() . ':' . ($editing ? $currentTimetable->id : 'new'));
    const hasValidationErrors = @json($errors->any());
    const saveSucceeded = @json((bool) session('success'));

    if (saveSucceeded) localStorage.removeItem(storageKey);
    if (@json((bool) session('clear_timetable_draft'))) {
        localStorage.removeItem(@json('timetableFormData:' . auth('staff')->id() . ':new'));
    }

    function saveFormData() {
        const formData = {
            title: document.getElementById('title').value,
            branch_id: document.getElementById('branch_id').value,
            academic_year_id: document.getElementById('academic_year_id').value,
            term_id: document.getElementById('term_id').value,
            valid_from: document.getElementById('valid_from').value,
            valid_until: document.getElementById('valid_until').value,
            sessions: []
        };

        container.querySelectorAll('.session-card').forEach(row => {
            const sessionData = {
                session_id: row.querySelector('input[name$="[session_id]"]')?.value || '',
                lesson_id: row.querySelector('.lesson-id-hidden')?.value || '',
                day_of_week: row.querySelector('select[name$="[day_of_week]"]')?.value || '',
                shifts: Array.from(row.querySelectorAll('.shift-checkbox:checked')).map(input => input.value),
                shift_times: {},
                start_time: row.querySelector('[data-primary-start]')?.value || '',
                end_time: row.querySelector('[data-primary-end]')?.value || '',
                classroom: row.querySelector('input[name$="[classroom]"]')?.value || '',
                notes: row.querySelector('input[name$="[notes]"]')?.value || '',
                activity_type: row.querySelector('.activity-type')?.value || '',
                reserve_text: row.querySelector('textarea[name$="[reserve_text]"]')?.value || '',
                session_type: row.querySelector('.session-type')?.value || '',
                minimum_paid_percentage: row.querySelector('input[name$="[minimum_paid_percentage]"]')?.value || '',
                course_ids: Array.from(row.querySelectorAll('[data-course-picker] input:checked')).map(input => input.value),
                lesson_title: row.querySelector('.lesson-search')?.value || ''
            };
            row.querySelectorAll('[data-shift-time]').forEach(panel => {
                sessionData.shift_times[panel.dataset.shiftTime] = {
                    start_time: panel.querySelector('[data-time-kind="start"]').value,
                    end_time: panel.querySelector('[data-time-kind="end"]').value
                };
            });
            formData.sessions.push(sessionData);
        });

        localStorage.setItem(storageKey, JSON.stringify(formData));
    }

    function loadFormData() {
        const saved = localStorage.getItem(storageKey);
        if (!saved || hasValidationErrors || saveSucceeded) return false;

        try {
            const formData = JSON.parse(saved);
            document.getElementById('title').value = formData.title || '';
            document.getElementById('branch_id').value = formData.branch_id || '';
            document.getElementById('academic_year_id').value = formData.academic_year_id || '';
            document.getElementById('term_id').value = formData.term_id || '';
            document.getElementById('valid_from').value = formData.valid_from || '';
            document.getElementById('valid_until').value = formData.valid_until || '';

            // Load sessions
            if (formData.sessions && formData.sessions.length > 0) {
                container.innerHTML = '';
                formData.sessions.forEach((sessionData, index) => {
                    const html = template.replace(/__INDEX__/g, index).replace(/__NUMBER__/g, index + 1);
                    container.insertAdjacentHTML('beforeend', html);
                    const row = container.lastElementChild;
                    if (sessionData.session_id) {
                        const sessionId = document.createElement('input');
                        sessionId.type = 'hidden';
                        sessionId.name = `sessions[${index}][session_id]`;
                        sessionId.value = sessionData.session_id;
                        row.querySelector('.session-card-body').prepend(sessionId);
                    }
                    row.querySelector('.lesson-id-hidden').value = sessionData.lesson_id || '';
                    row.querySelector('.lesson-search').value = sessionData.lesson_title || '';
                    row.querySelector('select[name$="[day_of_week]"]').value = sessionData.day_of_week || '';
                    row.querySelectorAll('.shift-checkbox').forEach(input => {
                        input.checked = (sessionData.shifts || ['day']).includes(input.value);
                    });
                    Object.entries(sessionData.shift_times || {}).forEach(([shift, times]) => {
                        const panel = row.querySelector(`[data-shift-time="${shift}"]`);
                        if (!panel) return;
                        panel.querySelector('[data-time-kind="start"]').value = times.start_time || '';
                        panel.querySelector('[data-time-kind="end"]').value = times.end_time || '';
                    });
                    row.querySelector('[data-primary-start]').value = sessionData.start_time || '';
                    row.querySelector('[data-primary-end]').value = sessionData.end_time || '';
                    row.querySelector('input[name$="[classroom]"]').value = sessionData.classroom || '';
                    row.querySelector('input[name$="[notes]"]').value = sessionData.notes || '';
                    row.querySelector('.activity-type').value = sessionData.activity_type || 'lesson';
                    row.querySelector('textarea[name$="[reserve_text]"]').value = sessionData.reserve_text || '';
                    row.querySelector('.session-type').value = sessionData.session_type || 'class';
                    row.querySelector('input[name$="[minimum_paid_percentage]"]').value = sessionData.minimum_paid_percentage || '0';
                    row.dataset.lessonId = sessionData.lesson_id || '';
                    row.dataset.selectedCourses = (sessionData.course_ids || []).join(',');
                    bindRow(row);
                    renderCourses(row, sessionData.course_ids || []);
                    syncSession(row);
                });
                renumber();
            }
            updateTermOptions(false);
            return true;
        } catch (e) {
            console.error('Could not restore the saved timetable draft.', e);
            return false;
        }
    }

    form.addEventListener('input', saveFormData);
    form.addEventListener('change', saveFormData);

    function updateTermOptions(setDates) {
        const year = yearSelect.value;
        let selectedOption = null;
        Array.from(termSelect.options).forEach(option => {
            if (!option.value) return;
            const available = option.dataset.year === year;
            option.hidden = !available;
            option.disabled = !available;
            if (option.selected && available) selectedOption = option;
        });
        if (!selectedOption) termSelect.value = '';
        selectedOption = termSelect.selectedOptions[0];
        if (!selectedOption || !selectedOption.value) return;
        validFrom.min = selectedOption.dataset.start;
        validFrom.max = selectedOption.dataset.end;
        validUntil.min = selectedOption.dataset.start;
        validUntil.max = selectedOption.dataset.end;
        if (setDates) {
            validFrom.value = selectedOption.dataset.start;
            validUntil.value = selectedOption.dataset.end;
            saveFormData();
        }
    }

    function refreshLessonSelect(row, preserve = true) {
        const searchInput = row.querySelector('.lesson-search');
        const hiddenInput = row.querySelector('.lesson-id-hidden');
        const previous = preserve ? (row.dataset.lessonId || hiddenInput.value) : '';
        if (!preserve) {
            searchInput.value = '';
            hiddenInput.value = '';
            row.dataset.lessonId = '';
            row.dataset.selectedCourses = '';
        }
        if (previous) {
            const lesson = lessonData.find(l => String(l.lesson_id) === String(previous));
            if (lesson) {
                searchInput.value = lesson.lesson_title;
                hiddenInput.value = lesson.lesson_id;
            }
        }
        row.dataset.lessonId = hiddenInput.value;
        renderCourses(row, row.dataset.selectedCourses ? row.dataset.selectedCourses.split(',') : []);
    }

    function renderCourses(row, selectedIds = []) {
        const activityType = row.querySelector('.activity-type').value;
        const lessonId = row.querySelector('.lesson-id-hidden').value;
        const lesson = lessonData.find(item => String(item.lesson_id) === String(lessonId));
        const picker = row.querySelector('[data-course-picker]');
        const searchInput = row.querySelector('.course-search');
        picker.replaceChildren();
        const selected = selectedIds.map(String);
        const usesLessonCourses = ['lesson', 'group_work'].includes(activityType) && lesson;
        let coursesToShow;
        if (usesLessonCourses) {
            coursesToShow = lesson.courses;
        } else {
            const allCourses = new Map();
            lessonData.forEach(item => {
                item.courses.forEach(course => {
                    if (!allCourses.has(course.id)) {
                        allCourses.set(course.id, course);
                    }
                });
            });
            coursesToShow = Array.from(allCourses.values());
        }

        if (searchInput) {
            searchInput.hidden = !coursesToShow.length;
            searchInput.value = '';
        }
        if (activityType === 'lesson' && !lesson) {
            if (searchInput) searchInput.hidden = true;
            row.querySelector('[data-teacher-preview]').textContent = 'Select a lesson to see its assigned courses.';
            return;
        }

        coursesToShow.forEach(course => {
            const label = document.createElement('label');
            label.className = 'course-option';
            label.dataset.courseName = `${course.abbreviation || ''} ${course.name || ''}`.toLowerCase();
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = `sessions[${row.dataset.index}][course_ids][]`;
            checkbox.value = course.id;
            checkbox.checked = selected.includes(String(course.id));
            checkbox.dataset.courseId = course.id;
            const copy = document.createElement('span');
            copy.className = 'course-option-copy';
            const name = document.createElement('strong');
            name.textContent = course.abbreviation || course.name;
            const teacher = document.createElement('small');
            if (usesLessonCourses) {
                teacher.textContent = course.teacher_name ? `Teacher: ${course.teacher_name}` : 'Teacher: not assigned';
            } else {
                teacher.textContent = '';
            }
            copy.append(name, teacher);
            label.append(checkbox, copy);
            picker.append(label);
        });
        updateTeacherPreview(row);
        picker.onchange = () => updateTeacherPreview(row);

        if (searchInput) {
            searchInput.oninput = function () {
                const value = this.value.toLowerCase();
                Array.from(picker.querySelectorAll('.course-option')).forEach(option => {
                    const courseName = option.dataset.courseName || '';
                    option.hidden = Boolean(value && !courseName.includes(value));
                });
            };
        }
    }

    function updateTeacherPreview(row) {
        const activityType = row.querySelector('.activity-type').value;
        const selected = Array.from(row.querySelectorAll('[data-course-picker] input:checked'));
        const lessonId = row.querySelector('.lesson-id-hidden').value;
        const lesson = lessonData.find(item => String(item.lesson_id) === String(lessonId));
        const details = selected.map(input => {
            const course = lesson?.courses.find(item => String(item.id) === input.value);
            return course ? `${course.abbreviation || course.name}: ${course.teacher_name || 'not assigned'}` : '';
        }).filter(Boolean);
        
        if (['lesson', 'group_work'].includes(activityType) && lesson) {
            row.querySelector('[data-teacher-preview]').textContent = details.length
                ? `Assigned teacher${details.length > 1 ? 's' : ''} · ${details.join(' · ')}`
                : 'Choose one or more courses assigned to this lesson.';
        } else {
            const courseNames = selected.map(input => {
                const course = Array.from(lessonData).flatMap(item => item.courses)
                    .find(item => String(item.id) === input.value);
                return course ? (course.abbreviation || course.name) : '';
            }).filter(Boolean);
            row.querySelector('[data-teacher-preview]').textContent = courseNames.length
                ? `Selected course${courseNames.length > 1 ? 's' : ''} · ${courseNames.join(' · ')}`
                : 'Choose one or more courses for this session.';
        }
        row.dataset.selectedCourses = selected.map(input => input.value).join(',');
    }

    function syncSession(row) {
        const activityType = row.querySelector('.activity-type').value;
        const sessionType = row.querySelector('.session-type')?.value || 'class';
        const reservePanel = row.querySelector('[data-activity-type-panel]');
        const lessonPanel = row.querySelector('[data-lesson-picker-panel]');
        const lessonSearch = row.querySelector('.lesson-search');
        const lessonHidden = row.querySelector('.lesson-id-hidden');
        const isLessonActivity = ['lesson', 'group_work'].includes(activityType);
        lessonPanel.hidden = !isLessonActivity;
        reservePanel.hidden = activityType !== 'reserve';
        lessonSearch.disabled = !isLessonActivity;
        lessonHidden.disabled = !isLessonActivity;
        lessonHidden.required = activityType === 'lesson';
        lessonPanel.querySelector('.lesson-required-mark').hidden = activityType !== 'lesson';
        lessonPanel.querySelector('[data-lesson-hint]').textContent = activityType === 'group_work'
            ? 'Lesson and course selection are optional for group work.'
            : 'Search by partial or approximate lesson name, then select a result.';
        row.querySelector('[data-eligibility-panel]').classList.toggle('is-visible', sessionType === 'test');

        row.querySelectorAll('.shift-checkbox').forEach(input => {
            const panel = row.querySelector(`[data-shift-time="${input.value}"]`);
            panel.closest('.shift-time-column').hidden = !input.checked;
            panel.classList.toggle('is-visible', input.checked);
            panel.querySelectorAll('.shift-time-input').forEach(timeInput => {
                timeInput.required = input.checked;
                timeInput.disabled = !input.checked;
            });
        });

        const primaryShift = row.querySelector('.shift-checkbox:checked')?.value;
        const primaryTimes = primaryShift ? row.querySelector(`[data-shift-time="${primaryShift}"]`) : null;
        row.querySelector('[data-primary-start]').value = primaryTimes?.querySelector('[data-time-kind="start"]').value || '';
        row.querySelector('[data-primary-end]').value = primaryTimes?.querySelector('[data-time-kind="end"]').value || '';

        const kindLabels = {
            'lesson': 'Lesson',
            'self_study': 'Self Study',
            'group_work': 'Group Work',
            'reserve': 'Reserve'
        };
        row.querySelector('.session-kind-pill').textContent = kindLabels[activityType] || 'Class';
        renderCourses(row, row.dataset.selectedCourses ? row.dataset.selectedCourses.split(',') : []);
    }

    function renumber() {
        const rows = Array.from(container.querySelectorAll('.session-card'));
        rows.forEach((row, index) => {
            const oldIndex = row.dataset.index;
            row.dataset.index = index;
            row.querySelector('.session-index').textContent = index + 1;
            row.querySelector('.session-card-title').textContent = `Session ${index + 1}`;
            row.querySelector('.remove-session').setAttribute('aria-label', `Remove session ${index + 1}`);
            row.querySelectorAll('[name]').forEach(field => {
                field.name = field.name.replace(new RegExp(`sessions\\[${oldIndex}\\]`, 'g'), `sessions[${index}]`);
            });
            row.querySelectorAll('[id], label[for]').forEach(element => {
                ['id', 'for'].forEach(attribute => {
                    if (element.hasAttribute(attribute)) element.setAttribute(attribute, element.getAttribute(attribute).replace(/-\d+$/, `-${index}`));
                });
            });
        });
        document.getElementById('session-count').textContent = rows.length;
    }

    function bindRow(row) {
        const searchInput = row.querySelector('.lesson-search');
        const hiddenInput = row.querySelector('.lesson-id-hidden');
        if (!searchInput || !hiddenInput) return;
        const dropdown = row.querySelector('.lesson-dropdown');

        function normalize(value) {
            return value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9 ]/g, ' ').trim().replace(/\s+/g, ' ');
        }

        function editDistance(left, right) {
            const previous = Array.from({ length: right.length + 1 }, (_, index) => index);
            for (let i = 1; i <= left.length; i++) {
                let diagonal = previous[0];
                previous[0] = i;
                for (let j = 1; j <= right.length; j++) {
                    const above = previous[j];
                    previous[j] = Math.min(previous[j] + 1, previous[j - 1] + 1, diagonal + (left[i - 1] === right[j - 1] ? 0 : 1));
                    diagonal = above;
                }
            }
            return previous[right.length];
        }

        function lessonScore(title, query) {
            const cleanTitle = normalize(title);
            if (cleanTitle === query) return 1;
            if (cleanTitle.includes(query)) return .95;
            const words = cleanTitle.split(' ');
            if (query.split(' ').every(token => words.some(word => word.includes(token)))) return .86;
            const queryWords = query.split(' ');
            const bestWordScore = queryWords.map(token => Math.max(...words.map(word => {
                const distance = editDistance(token, word);
                return 1 - distance / Math.max(token.length, word.length, 1);
            }))).reduce((score, value) => Math.min(score, value), 1);
            const fullScore = 1 - editDistance(query, cleanTitle) / Math.max(query.length, cleanTitle.length, 1);
            return Math.max(bestWordScore, fullScore);
        }

        function showLessonResults() {
            const query = normalize(searchInput.value);
            const matches = lessonData.map(lesson => ({
                lesson,
                score: query ? lessonScore(lesson.lesson_title, query) : 0
            })).filter(item => !query || item.score >= .55)
                .sort((a, b) => b.score - a.score || a.lesson.lesson_title.localeCompare(b.lesson.lesson_title))
                .slice(0, 10);
            dropdown.replaceChildren();
            if (!matches.length) {
                const empty = document.createElement('div');
                empty.className = 'lesson-empty';
                empty.textContent = lessonData.length ? 'No close lesson matches. Try another word.' : 'No lessons available for this branch and year.';
                dropdown.append(empty);
            } else {
                matches.forEach(({ lesson }) => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'lesson-result';
                    option.role = 'option';
                    option.textContent = lesson.lesson_title;
                    option.addEventListener('click', () => {
                        hiddenInput.value = lesson.lesson_id;
                        searchInput.value = lesson.lesson_title;
                        row.dataset.lessonId = lesson.lesson_id;
                        row.dataset.selectedCourses = '';
                        dropdown.classList.remove('is-open');
                        searchInput.setAttribute('aria-expanded', 'false');
                        renderCourses(row, []);
                        saveFormData();
                    });
                    dropdown.append(option);
                });
            }
            dropdown.classList.add('is-open');
            searchInput.setAttribute('aria-expanded', 'true');
        }

        if (hiddenInput.value) row.dataset.lessonId = hiddenInput.value;
        searchInput.addEventListener('focus', showLessonResults);
        searchInput.addEventListener('input', () => {
            hiddenInput.value = '';
            row.dataset.lessonId = '';
            row.dataset.selectedCourses = '';
            renderCourses(row, []);
            showLessonResults();
        });
        searchInput.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                dropdown.classList.remove('is-open');
                searchInput.setAttribute('aria-expanded', 'false');
            }
        });
        searchInput.addEventListener('blur', () => {
            window.setTimeout(() => {
                dropdown.classList.remove('is-open');
                searchInput.setAttribute('aria-expanded', 'false');
            }, 150);
        });

        row.querySelector('.activity-type')?.addEventListener('change', () => {
            syncSession(row);
        });
        row.querySelector('.session-type')?.addEventListener('change', () => syncSession(row));
        row.querySelectorAll('.shift-checkbox').forEach(input => input.addEventListener('change', () => syncSession(row)));
        row.querySelectorAll('.shift-time-input').forEach(input => {
            input.addEventListener('input', () => syncSession(row));
            input.addEventListener('change', () => syncSession(row));
        });
        row.querySelector('.remove-session').addEventListener('click', () => {
            row.remove();
            renumber();
            saveFormData();
        });
        syncSession(row);
    }

    async function reloadBranchLessons() {
        lessonMessage.style.display = 'none';
        if (!branchSelect.value || !yearSelect.value) {
            lessonData = [];
            container.querySelectorAll('.session-card').forEach(row => refreshLessonSelect(row, false));
            return;
        }
        try {
            const url = new URL(@json(route('dos.timetable.branch-lessons')), window.location.origin);
            url.searchParams.set('branch_id', branchSelect.value);
            url.searchParams.set('academic_year_id', yearSelect.value);
            const response = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(`Could not load branch lessons (${response.status}).`);
            lessonData = await response.json();
            container.querySelectorAll('.session-card').forEach(row => {
                const selectedLesson = lessonData.find(item => String(item.lesson_id) === String(row.querySelector('.lesson-id-hidden').value));
                if (!selectedLesson) {
                    row.querySelector('.lesson-id-hidden').value = '';
                    row.querySelector('.lesson-search').value = '';
                    row.dataset.lessonId = '';
                    row.dataset.selectedCourses = '';
                } else {
                    row.querySelector('.lesson-search').value = selectedLesson.lesson_title;
                    row.dataset.lessonId = selectedLesson.lesson_id;
                }
                renderCourses(row, row.dataset.selectedCourses ? row.dataset.selectedCourses.split(',') : []);
            });
            if (!lessonData.length) {
                lessonMessage.textContent = 'No lessons are assigned to this branch and academic year yet.';
                lessonMessage.style.display = 'block';
            }
            saveFormData();
        } catch (error) {
            lessonMessage.textContent = error.message || 'Lessons could not be loaded. Please try again.';
            lessonMessage.style.display = 'block';
        }
    }

    container.querySelectorAll('.session-card').forEach(row => {
        bindRow(row);
        refreshLessonSelect(row);
        // Initialize courses for existing sessions
        const lessonId = row.querySelector('.lesson-id-hidden').value;
        const selectedCourses = row.dataset.selectedCourses ? row.dataset.selectedCourses.split(',') : [];
        if (lessonId && selectedCourses.length > 0) {
            renderCourses(row, selectedCourses);
        }
    });

    document.getElementById('add-session').addEventListener('click', () => {
        const index = container.querySelectorAll('.session-card').length;
        const html = template.replace(/__INDEX__/g, index).replace(/__NUMBER__/g, index + 1);
        container.insertAdjacentHTML('beforeend', html);
        const row = container.lastElementChild;
        bindRow(row);
        refreshLessonSelect(row, false);
        renumber();
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        row.querySelector('.activity-type').focus({ preventScroll: true });
        saveFormData();
    });

    branchSelect.addEventListener('change', reloadBranchLessons);
    yearSelect.addEventListener('change', () => {
        updateTermOptions(true);
        reloadBranchLessons();
    });
    termSelect.addEventListener('change', () => updateTermOptions(true));
    updateTermOptions(false);
    const restoredDraft = loadFormData();
    if (restoredDraft) {
        container.querySelectorAll('.session-card').forEach(row => syncSession(row));
        reloadBranchLessons();
    }
    document.getElementById('timetable-form').addEventListener('submit', event => {
        const missingCourses = Array.from(container.querySelectorAll('.session-card'))
            .some(row => row.querySelector('.activity-type').value !== 'group_work'
                && !row.querySelector('[data-course-picker] input:checked'));
        
        const missingShiftTimes = Array.from(container.querySelectorAll('.session-card'))
            .some(row => {
                const checkedShifts = Array.from(row.querySelectorAll('.shift-checkbox:checked'));
                if (checkedShifts.length === 0) return true;
                
                // Check if each checked shift has both start and end times
                for (const checkbox of checkedShifts) {
                    const shift = checkbox.value;
                    const startTime = row.querySelector(`[name*="[shift_times][${shift}][start_time]"]`)?.value;
                    const endTime = row.querySelector(`[name*="[shift_times][${shift}][end_time]"]`)?.value;
                    if (!startTime || !endTime) return true;
                }
                return false;
            });
        
        if (missingCourses || missingShiftTimes) {
            event.preventDefault();
            lessonMessage.textContent = missingShiftTimes
                ? 'Select at least one shift for every session and add its start and end times.'
                : 'Choose at least one course for every session. Group work may be saved without a course selection.';
            lessonMessage.style.display = 'block';
            lessonMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            saveFormData();
        }
    });
});
</script>
