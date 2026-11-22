@php
    $currentTimetable = $timetable ?? null;
    $oldSessionRows = old('sessions');
    if ($oldSessionRows === null && $editing) {
        $oldSessionRows = $currentTimetable->sessions->map(function ($session) {
            $row = $session->toArray();
            $row['course_ids'] = $session->courses->pluck('id')->all();
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
    .builder-footer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .builder-footer-copy { color: var(--muted); font-size: .83rem; }
    .builder-actions { display: flex; gap: .55rem; flex-wrap: wrap; }
    .schedule-builder .btn-primary { border-color: var(--green); background: var(--green); }
    .schedule-builder .btn-primary:hover { border-color: #105544; background: #105544; }
    .approval-panel { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.2rem; border: 1px solid #dfe7df; border-radius: 15px; background: #fff; }
    .approval-panel p { margin: .2rem 0 0; color: var(--muted); font-size: .82rem; }
    .approval-actions { display: flex; gap: .5rem; flex-wrap: wrap; }
    #lesson-load-message { display: none; margin-bottom: .8rem; }
    .duplicate-shifts-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1050; }
    .duplicate-shifts-modal.show { display: flex; }
    .duplicate-shifts-content { background: white; padding: 2rem; border-radius: 12px; max-width: 400px; width: 100%; }
    .duplicate-shifts-content h5 { margin-bottom: 1rem; }
    .duplicate-shifts-options { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem; }
    .duplicate-shifts-options label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .duplicate-shifts-actions { display: flex; gap: 0.5rem; justify-content: flex-end; }
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
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" type="button" id="duplicate-session"><i class="bi bi-copy me-1"></i>Duplicate to Shifts</button>
                        <button class="btn btn-outline-success" type="button" id="add-session"><i class="bi bi-plus-lg me-1"></i>Add a session</button>
                    </div>
                </div>
                <div id="sessions-container">
                    @foreach($oldSessionRows as $index => $sessionData)
                        @php
                            $rowType = !empty($sessionData['mark_as_test']) && ($sessionData['session_type'] ?? 'class') === 'class'
                                ? 'test'
                                : ($sessionData['session_type'] ?? 'class');
                            $rowCourseIds = old("sessions.{$index}.course_ids", $sessionData['course_ids'] ?? []);
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
                                <div class="row g-3">
                                    <div class="col-12 col-lg-5">
                                        <label class="form-label" for="lesson-{{ $index }}">Lesson</label>
                                        <select class="form-select lesson-select" id="lesson-{{ $index }}" name="sessions[{{ $index }}][lesson_id]" required>
                                            <option value="">Choose a lesson</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="day-{{ $index }}">Day</label>
                                        <select class="form-select" id="day-{{ $index }}" name="sessions[{{ $index }}][day_of_week]" required>
                                            @foreach([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'] as $day => $name)
                                                <option value="{{ $day }}" @selected((string) old("sessions.{$index}.day_of_week", $sessionData['day_of_week'] ?? '') === (string) $day)>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3 col-lg-2">
                                        <label class="form-label" for="shift-{{ $index }}">Shift</label>
                                        <select class="form-select" id="shift-{{ $index }}" name="sessions[{{ $index }}][shift]" required>
                                            <option value="morning" @selected(old("sessions.{$index}.shift", $sessionData['shift'] ?? 'morning') === 'morning')">Morning</option>
                                            <option value="afternoon" @selected(old("sessions.{$index}.shift", $sessionData['shift'] ?? 'morning') === 'afternoon')">Afternoon</option>
                                            <option value="evening" @selected(old("sessions.{$index}.shift", $sessionData['shift'] ?? 'morning') === 'evening')">Evening</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3 col-lg-2">
                                        <label class="form-label" for="start-{{ $index }}">Starts</label>
                                        <input type="time" class="form-control" id="start-{{ $index }}" name="sessions[{{ $index }}][start_time]" value="{{ old("sessions.{$index}.start_time", isset($sessionData['start_time']) ? substr($sessionData['start_time'], 0, 5) : '') }}" required>
                                    </div>
                                    <div class="col-6 col-sm-3 col-lg-2">
                                        <label class="form-label" for="end-{{ $index }}">Ends</label>
                                        <input type="time" class="form-control" id="end-{{ $index }}" name="sessions[{{ $index }}][end_time]" value="{{ old("sessions.{$index}.end_time", isset($sessionData['end_time']) ? substr($sessionData['end_time'], 0, 5) : '') }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Courses learning together</label>
                                        <input type="text" class="form-control mb-2 course-search" placeholder="Search courses..." style="min-height: 38px;">
                                        <div class="course-picker" data-course-picker></div>
                                        <div class="teacher-preview" data-teacher-preview>Select a lesson to see its assigned teacher.</div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="activity-{{ $index }}">Activity Type</label>
                                        <select class="form-select activity-type" id="activity-{{ $index }}" name="sessions[{{ $index }}][activity_type]" required>
                                            <option value="lesson" @selected(old("sessions.{$index}.activity_type", $sessionData['activity_type'] ?? 'lesson') === 'lesson')">Lesson</option>
                                            <option value="self_study" @selected(old("sessions.{$index}.activity_type", $sessionData['activity_type'] ?? 'lesson') === 'self_study')">Self Study</option>
                                            <option value="group_work" @selected(old("sessions.{$index}.activity_type", $sessionData['activity_type'] ?? 'lesson') === 'group_work')">Group Work</option>
                                            <option value="reserve" @selected(old("sessions.{$index}.activity_type", $sessionData['activity_type'] ?? 'lesson') === 'reserve')">Reserve (No Class)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 activity-type-panel" data-activity-type-panel style="display: none;">
                                        <label class="form-label" for="reserve-text-{{ $index }}">Reserve Instructions</label>
                                        <textarea class="form-control" id="reserve-text-{{ $index }}" name="sessions[{{ $index }}][reserve_text]" rows="2" placeholder="What students should do during this reserved time...">{{ old("sessions.{$index}.reserve_text", $sessionData['reserve_text'] ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12 col-md-6 lesson-panel" data-lesson-panel>
                                        <label class="form-label" for="type-{{ $index }}">Session Type</label>
                                        <select class="form-select session-type" id="type-{{ $index }}" name="sessions[{{ $index }}][session_type]">
                                            <option value="class" @selected(old("sessions.{$index}.session_type", $rowType) === 'class')">Class</option>
                                            <option value="test" @selected(old("sessions.{$index}.session_type", $rowType) === 'test')">Test / exam</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="room-{{ $index }}">Room / location</label>
                                        <input class="form-control" id="room-{{ $index }}" name="sessions[{{ $index }}][classroom]" value="{{ old("sessions.{$index}.classroom", $sessionData['classroom'] ?? '') }}" placeholder="Optional">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="notes-{{ $index }}">Student instructions</label>
                                        <input class="form-control" id="notes-{{ $index }}" name="sessions[{{ $index }}][notes]" value="{{ old("sessions.{$index}.notes", $sessionData['notes'] ?? '') }}" placeholder="Optional details">
                                    </div>
                                    <div class="col-12 eligibility-panel" data-eligibility-panel>
                                        <label class="form-label" for="eligibility-{{ $index }}">Minimum fee percentage paid</label>
                                        <div class="input-group">
                                            <input type="number" min="0" max="100" step="0.01" class="form-control" id="eligibility-{{ $index }}" name="sessions[{{ $index }}][minimum_paid_percentage]"
                                                value="{{ old("sessions.{$index}.minimum_paid_percentage", $sessionData['minimum_paid_percentage'] ?? 0) }}">
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

<!-- Duplicate Shifts Modal -->
<div class="duplicate-shifts-modal" id="duplicateShiftsModal">
    <div class="duplicate-shifts-content">
        <h5>Duplicate Session to Multiple Shifts</h5>
        <p class="text-muted mb-3">Select which shifts to duplicate this session to:</p>
        <div class="duplicate-shifts-options">
            <label>
                <input type="checkbox" value="morning" checked> Morning
            </label>
            <label>
                <input type="checkbox" value="afternoon" checked> Afternoon
            </label>
            <label>
                <input type="checkbox" value="evening" checked> Evening
            </label>
        </div>
        <div class="duplicate-shifts-actions">
            <button class="btn btn-secondary" type="button" id="cancelDuplicate">Cancel</button>
            <button class="btn btn-primary" type="button" id="confirmDuplicate">Duplicate</button>
        </div>
    </div>
</div>

<template id="session-template">
    <article class="session-card" data-index="__INDEX__" data-selected-courses="">
        <div class="session-card-head">
            <span class="session-index">__NUMBER__</span><h3 class="session-card-title">Session __NUMBER__</h3>
            <span class="session-kind-pill">Class</span>
            <button class="btn btn-sm btn-outline-danger remove-session ms-2" type="button" aria-label="Remove session"><i class="bi bi-trash"></i></button>
        </div>
        <div class="session-card-body"><div class="row g-3">
            <div class="col-12 col-lg-5"><label class="form-label" for="lesson-__INDEX__">Lesson</label><select class="form-select lesson-select" id="lesson-__INDEX__" name="sessions[__INDEX__][lesson_id]" required><option value="">Choose a lesson</option></select></div>
            <div class="col-12 col-sm-6 col-lg-3"><label class="form-label" for="day-__INDEX__">Day</label><select class="form-select" id="day-__INDEX__" name="sessions[__INDEX__][day_of_week]" required>
                <option value="1">Monday</option><option value="2">Tuesday</option><option value="3">Wednesday</option><option value="4">Thursday</option><option value="5">Friday</option><option value="6">Saturday</option><option value="0">Sunday</option>
            </select></div>
            <div class="col-6 col-sm-3 col-lg-2"><label class="form-label" for="shift-__INDEX__">Shift</label><select class="form-select" id="shift-__INDEX__" name="sessions[__INDEX__][shift]" required>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="evening">Evening</option>
            </select></div>
            <div class="col-6 col-sm-3 col-lg-2"><label class="form-label" for="start-__INDEX__">Starts</label><input type="time" class="form-control" id="start-__INDEX__" name="sessions[__INDEX__][start_time]" required></div>
            <div class="col-6 col-sm-3 col-lg-2"><label class="form-label" for="end-__INDEX__">Ends</label><input type="time" class="form-control" id="end-__INDEX__" name="sessions[__INDEX__][end_time]" required></div>
            <div class="col-12 col-md-6"><label class="form-label">Courses learning together</label><input type="text" class="form-control mb-2 course-search" placeholder="Search courses..." style="min-height: 38px;"><div class="course-picker" data-course-picker></div><div class="teacher-preview" data-teacher-preview>Select a lesson to see its assigned teacher.</div></div>
            <div class="col-12 col-md-6"><label class="form-label" for="activity-__INDEX__">Activity Type</label><select class="form-select activity-type" id="activity-__INDEX__" name="sessions[__INDEX__][activity_type]" required>
                <option value="lesson">Lesson</option>
                <option value="self_study">Self Study</option>
                <option value="group_work">Group Work</option>
                <option value="reserve">Reserve (No Class)</option>
            </select></div>
            <div class="col-12 col-md-6 activity-type-panel" data-activity-type-panel style="display: none;"><label class="form-label" for="reserve-text-__INDEX__">Reserve Instructions</label><textarea class="form-control" id="reserve-text-__INDEX__" name="sessions[__INDEX__][reserve_text]" rows="2" placeholder="What students should do..."></textarea></div>
            <div class="col-12 col-md-6 lesson-panel" data-lesson-panel><label class="form-label" for="type-__INDEX__">Session Type</label><select class="form-select session-type" id="type-__INDEX__" name="sessions[__INDEX__][session_type]"><option value="class">Class</option><option value="test">Test / exam</option></select></div>
            <div class="col-12 col-md-6"><label class="form-label" for="room-__INDEX__">Room / location</label><input class="form-control" id="room-__INDEX__" name="sessions[__INDEX__][classroom]" placeholder="Optional"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="notes-__INDEX__">Student instructions</label><input class="form-control" id="notes-__INDEX__" name="sessions[__INDEX__][notes]" placeholder="Optional details"></div>
            <div class="col-12 eligibility-panel" data-eligibility-panel><label class="form-label" for="eligibility-__INDEX__">Minimum fee percentage paid</label><div class="input-group"><input type="number" min="0" max="100" step="0.01" class="form-control" id="eligibility-__INDEX__" name="sessions[__INDEX__][minimum_paid_percentage]" value="0"><span class="input-group-text">%</span></div><small class="builder-hint">Students below this threshold will see why they are not eligible and how to become eligible.</small></div>
        </div></div>
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
    let lessonData = @json($branchLessonsData);

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
        }
    }

    function refreshLessonSelect(row, preserve = true) {
        const select = row.querySelector('.lesson-select');
        const previous = preserve ? (row.dataset.lessonId || select.value) : '';
        select.replaceChildren(new Option('Choose a lesson', ''));
        lessonData.forEach(lesson => select.add(new Option(lesson.lesson_title, lesson.lesson_id)));
        select.value = lessonData.some(lesson => String(lesson.lesson_id) === previous) ? previous : '';
        row.dataset.lessonId = select.value;
        renderCourses(row, row.dataset.selectedCourses ? row.dataset.selectedCourses.split(',') : []);
    }

    function renderCourses(row, selectedIds = []) {
        const lesson = lessonData.find(item => String(item.lesson_id) === row.querySelector('.lesson-select').value);
        const picker = row.querySelector('[data-course-picker]');
        const preview = row.querySelector('[data-teacher-preview]');
        const searchInput = row.querySelector('.course-search');
        picker.replaceChildren();
        if (!lesson) {
            preview.textContent = 'Select a lesson to see its assigned teacher.';
            row.dataset.selectedCourses = '';
            if (searchInput) searchInput.style.display = 'none';
            return;
        }
        if (searchInput) searchInput.style.display = 'block';
        const selected = selectedIds.map(String);
        lesson.courses.forEach(course => {
            const label = document.createElement('label');
            label.className = 'course-option';
            label.dataset.courseName = (course.abbreviation || course.name).toLowerCase();
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = `sessions[${row.dataset.index}][course_ids][]`;
            checkbox.value = course.id;
            checkbox.checked = selected.includes(String(course.id));
            const copy = document.createElement('span');
            copy.className = 'course-option-copy';
            const name = document.createElement('strong');
            name.textContent = course.abbreviation || course.name;
            const teacher = document.createElement('small');
            teacher.textContent = course.teacher_name ? `Teacher: ${course.teacher_name}` : 'Teacher: not assigned';
            copy.append(name, teacher);
            label.append(checkbox, copy);
            picker.append(label);
        });
        updateTeacherPreview(row);
        picker.onchange = () => updateTeacherPreview(row);

        // Add course search functionality
        if (searchInput) {
            searchInput.value = '';
            searchInput.oninput = function() {
                const value = this.value.toLowerCase();
                Array.from(picker.querySelectorAll('.course-option')).forEach(option => {
                    const courseName = option.dataset.courseName || '';
                    option.style.display = courseName.includes(value) ? 'flex' : 'none';
                });
            };
        }
    }

    function updateTeacherPreview(row) {
        const selected = Array.from(row.querySelectorAll('[data-course-picker] input:checked'));
        const details = selected.map(input => {
            const course = lessonData.find(item => String(item.lesson_id) === row.querySelector('.lesson-select').value)
                ?.courses.find(item => String(item.id) === input.value);
            return course ? `${course.abbreviation || course.name}: ${course.teacher_name || 'not assigned'}` : '';
        }).filter(Boolean);
        row.querySelector('[data-teacher-preview]').textContent = details.length
            ? `Assigned teacher${details.length > 1 ? 's' : ''} · ${details.join(' · ')}`
            : 'Choose one or more courses to study together.';
        row.dataset.selectedCourses = selected.map(input => input.value).join(',');
    }

    function syncSession(row) {
        const activityType = row.querySelector('.activity-type').value;
        const sessionType = row.querySelector('.session-type')?.value || 'class';
        
        // Show/hide reserve text panel
        const reservePanel = row.querySelector('[data-activity-type-panel]');
        const lessonPanel = row.querySelector('[data-lesson-panel]');
        const lessonSelect = row.querySelector('.lesson-select');
        const coursePicker = row.querySelector('[data-course-picker]');
        const teacherPreview = row.querySelector('[data-teacher-preview]');
        
        if (activityType === 'reserve') {
            reservePanel.style.display = 'block';
            lessonPanel.style.display = 'none';
            lessonSelect.disabled = true;
            lessonSelect.required = false;
            coursePicker.style.opacity = '0.5';
            coursePicker.style.pointerEvents = 'none';
            teacherPreview.textContent = 'Lesson selection is disabled for reserve sessions.';
        } else {
            reservePanel.style.display = 'none';
            lessonPanel.style.display = activityType === 'lesson' ? 'block' : 'none';
            lessonSelect.disabled = false;
            lessonSelect.required = true;
            coursePicker.style.opacity = '1';
            coursePicker.style.pointerEvents = 'auto';
            if (row.querySelector('.lesson-select').value) {
                teacherPreview.textContent = 'Select a lesson to see its assigned teacher.';
            }
        }
        
        // Show/hide eligibility panel for tests
        row.querySelector('[data-eligibility-panel]').classList.toggle('is-visible', sessionType === 'test');
        
        // Update session kind pill
        const kindLabels = {
            'lesson': 'Lesson',
            'self_study': 'Self Study',
            'group_work': 'Group Work',
            'reserve': 'Reserve'
        };
        row.querySelector('.session-kind-pill').textContent = kindLabels[activityType] || 'Class';
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
        row.querySelector('.lesson-select').addEventListener('change', () => {
            row.dataset.lessonId = row.querySelector('.lesson-select').value;
            row.dataset.selectedCourses = '';
            renderCourses(row, []);
        });
        row.querySelector('.activity-type')?.addEventListener('change', () => syncSession(row));
        row.querySelector('.session-type')?.addEventListener('change', () => syncSession(row));
        row.querySelector('.remove-session').addEventListener('click', () => {
            row.remove();
            renumber();
        });
        syncSession(row);
    }

    // Make lesson select searchable
    function makeLessonSearchable(select) {
        select.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            Array.from(this.options).forEach(option => {
                if (option.value) {
                    const text = option.textContent.toLowerCase();
                    option.hidden = !text.includes(value);
                }
            });
        });
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
                row.dataset.lessonId = '';
                row.dataset.selectedCourses = '';
                refreshLessonSelect(row, false);
            });
            if (!lessonData.length) {
                lessonMessage.textContent = 'No lessons are assigned to this branch and academic year yet.';
                lessonMessage.style.display = 'block';
            }
        } catch (error) {
            lessonMessage.textContent = error.message || 'Lessons could not be loaded. Please try again.';
            lessonMessage.style.display = 'block';
        }
    }

    container.querySelectorAll('.session-card').forEach(row => {
        bindRow(row);
        refreshLessonSelect(row);
        makeLessonSearchable(row.querySelector('.lesson-select'));
    });

    document.getElementById('add-session').addEventListener('click', () => {
        const index = container.querySelectorAll('.session-card').length;
        const html = template.replace(/__INDEX__/g, index).replace(/__NUMBER__/g, index + 1);
        container.insertAdjacentHTML('beforeend', html);
        const row = container.lastElementChild;
        bindRow(row);
        refreshLessonSelect(row, false);
        makeLessonSearchable(row.querySelector('.lesson-select'));
        renumber();
    });

    // Duplicate session to multiple shifts
    let currentSessionToDuplicate = null;
    const duplicateModal = document.getElementById('duplicateShiftsModal');
    const cancelDuplicateBtn = document.getElementById('cancelDuplicate');
    const confirmDuplicateBtn = document.getElementById('confirmDuplicate');

    document.getElementById('duplicate-session').addEventListener('click', () => {
        const rows = container.querySelectorAll('.session-card');
        if (rows.length === 0) {
            alert('Please add a session first before duplicating.');
            return;
        }
        currentSessionToDuplicate = rows[rows.length - 1];
        duplicateModal.classList.add('show');
    });

    cancelDuplicateBtn.addEventListener('click', () => {
        duplicateModal.classList.remove('show');
        currentSessionToDuplicate = null;
    });

    confirmDuplicateBtn.addEventListener('click', () => {
        if (!currentSessionToDuplicate) return;

        const selectedShifts = Array.from(duplicateModal.querySelectorAll('.duplicate-shifts-options input:checked'))
            .map(input => input.value);

        const currentShift = currentSessionToDuplicate.querySelector('.form-select[name$="[shift]"]').value;

        selectedShifts.forEach(shift => {
            if (shift === currentShift) return; // Skip current shift

            const index = container.querySelectorAll('.session-card').length;
            const html = template.replace(/__INDEX__/g, index).replace(/__NUMBER__/g, index + 1);
            container.insertAdjacentHTML('beforeend', html);
            const newRow = container.lastElementChild;

            // Copy all values from source session
            newRow.querySelector('.lesson-select').value = currentSessionToDuplicate.querySelector('.lesson-select').value;
            newRow.querySelector('.form-select[name$="[day_of_week]"]').value = currentSessionToDuplicate.querySelector('.form-select[name$="[day_of_week]"]').value;
            newRow.querySelector('.form-select[name$="[shift]"]').value = shift;
            newRow.querySelector('input[name$="[start_time]"]').value = currentSessionToDuplicate.querySelector('input[name$="[start_time]"]').value;
            newRow.querySelector('input[name$="[end_time]"]').value = currentSessionToDuplicate.querySelector('input[name$="[end_time]"]').value;
            newRow.querySelector('.activity-type').value = currentSessionToDuplicate.querySelector('.activity-type').value;
            newRow.querySelector('.session-type').value = currentSessionToDuplicate.querySelector('.session-type').value;
            newRow.querySelector('input[name$="[classroom]"]').value = currentSessionToDuplicate.querySelector('input[name$="[classroom]"]').value;
            newRow.querySelector('input[name$="[notes]"]').value = currentSessionToDuplicate.querySelector('input[name$="[notes]"]').value;
            newRow.querySelector('textarea[name$="[reserve_text]"]').value = currentSessionToDuplicate.querySelector('textarea[name$="[reserve_text]"]').value;
            newRow.querySelector('input[name$="[minimum_paid_percentage]"]').value = currentSessionToDuplicate.querySelector('input[name$="[minimum_paid_percentage]"]').value;

            // Copy course selections
            const selectedCourses = Array.from(currentSessionToDuplicate.querySelectorAll('[data-course-picker] input:checked'))
                .map(input => input.value);
            newRow.dataset.selectedCourses = selectedCourses.join(',');

            bindRow(newRow);
            refreshLessonSelect(newRow, false);
            makeLessonSearchable(newRow.querySelector('.lesson-select'));
            renderCourses(newRow, selectedCourses);
            syncSession(newRow);
        });

        renumber();
        duplicateModal.classList.remove('show');
        currentSessionToDuplicate = null;
    });

    branchSelect.addEventListener('change', reloadBranchLessons);
    yearSelect.addEventListener('change', () => {
        updateTermOptions(true);
        reloadBranchLessons();
    });
    termSelect.addEventListener('change', () => updateTermOptions(true));
    updateTermOptions(false);
    document.getElementById('timetable-form').addEventListener('submit', event => {
        const missingCourses = Array.from(container.querySelectorAll('.session-card'))
            .some(row => {
                const activityType = row.querySelector('.activity-type').value;
                // Only require courses for lesson activities
                if (activityType !== 'lesson') return false;
                return !row.querySelector('[data-course-picker] input:checked');
            });
        if (missingCourses) {
            event.preventDefault();
            lessonMessage.textContent = 'Choose at least one branch-assigned course for every lesson session.';
            lessonMessage.style.display = 'block';
            lessonMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
