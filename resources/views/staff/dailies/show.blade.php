@extends('dos.layout')

@section('page_title', 'Daily report review')

@section('content')
@php
    $reportStatus = $daily->approved ? 'approved' : 'pending';
    $attendanceLabels = [
        'P' => 'Present',
        'A' => 'Absent',
        'AR' => 'Absent with reason',
        'L' => 'Late',
    ];
    $attendanceCounts = [
        'P' => 0,
        'A' => 0,
        'AR' => 0,
        'L' => 0,
        'unmarked' => 0,
    ];

    foreach ($students as $student) {
        $studentAttendance = $attendances->get($student->id);
        $studentStatus = $studentAttendance->status ?? 'unmarked';
        $attendanceCounts[array_key_exists($studentStatus, $attendanceCounts) ? $studentStatus : 'unmarked']++;
    }

    $activities = is_array($daily->activity) ? $daily->activity : [];
@endphp

<main class="daily-detail">
    <div class="detail-toolbar">
        <a href="{{ route('dos.dailies.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            <span>All daily reports</span>
        </a>
        <span class="report-reference">Report <strong>#{{ $daily->id }}</strong></span>
    </div>

    @if(session('success'))
        <div class="alert alert-success detail-alert" role="status">
            <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger detail-alert" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger detail-alert" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i><span>{{ $errors->first() }}</span>
        </div>
    @endif

    <header class="detail-hero">
        <div class="hero-main">
            <div class="hero-kicker"><i class="bi bi-journal-check"></i> Teacher daily report</div>
            <h1>{{ $daily->date ? $daily->date->format('l, F j, Y') : 'Teaching session' }}</h1>
            <p class="hero-teacher">
                <span class="teacher-avatar">{{ strtoupper(substr($daily->teacher->teacher_name ?? 'NA', 0, 2)) }}</span>
                <span>
                    <small>Submitted by</small>
                    <strong>{{ $daily->teacher->teacher_name ?? 'Teacher unavailable' }}</strong>
                </span>
                @if($daily->branch)
                    <span class="hero-divider"></span>
                    <span class="branch-label"><i class="bi bi-geo-alt"></i>{{ $daily->branch->name }}</span>
                @endif
            </p>
        </div>
        <div class="hero-status status-{{ $reportStatus }}">
            <span class="status-indicator"></span>
            {{ ucfirst($reportStatus) }}
        </div>

        <div class="hero-courses">
            <span class="hero-courses-label"><i class="bi bi-collection"></i> Courses covered</span>
            <div class="course-chip-list">
                @forelse($courses as $course)
                    <span class="course-chip">{{ $course->course_name }}</span>
                @empty
                    <span class="course-chip course-chip-empty">No courses linked to this report</span>
                @endforelse
            </div>
            <span class="course-total">{{ $courses->count() }} {{ \Illuminate\Support\Str::plural('course', $courses->count()) }}</span>
        </div>

        <div class="hero-bottom">
            <span><i class="bi bi-bookmark"></i><strong>Lesson</strong> {{ $daily->lesson->title ?? 'Not assigned' }}</span>
            @if($daily->unit)
                <span><i class="bi bi-layers"></i><strong>Unit</strong> {{ $daily->unit->unit_name }}</span>
            @endif
        </div>
    </header>

    <section class="detail-metrics" aria-label="Session summary">
        <article class="metric-card">
            <span class="metric-icon metric-teal"><i class="bi bi-clock-history"></i></span>
            <div><small>Hours taught</small><strong>{{ $daily->hours_studied ?? '—' }}</strong></div>
            <span class="metric-suffix">hrs</span>
        </article>
        <article class="metric-card">
            <span class="metric-icon metric-blue"><i class="bi bi-people"></i></span>
            <div><small>Reported students</small><strong>{{ $daily->total_students ?? '—' }}</strong></div>
        </article>
        <article class="metric-card">
            <span class="metric-icon metric-amber"><i class="bi bi-person-x"></i></span>
            <div><small>Reported absences</small><strong>{{ $daily->absences ?? '—' }}</strong></div>
        </article>
        <article class="metric-card">
            <span class="metric-icon metric-slate"><i class="bi bi-person-lines-fill"></i></span>
            <div><small>Students on roster</small><strong>{{ $students->count() }}</strong></div>
        </article>
    </section>

    <section class="learning-grid">
        <article class="detail-card learning-card">
            <div class="section-heading">
                <div class="section-icon"><i class="bi bi-journal-text"></i></div>
                <div><h2>Teaching log</h2><p>What was covered in this session</p></div>
            </div>
            @if(count($activities))
                <ol class="activity-list">
                    @foreach($activities as $activity)
                        <li><span class="activity-marker"><i class="bi bi-check2"></i></span><span>{{ $activity }}</span></li>
                    @endforeach
                </ol>
            @else
                <div class="soft-empty"><i class="bi bi-journal-x"></i><span>No teaching activities were recorded.</span></div>
            @endif
        </article>

        <article class="detail-card notes-card">
            <div class="section-heading">
                <div class="section-icon notes-icon"><i class="bi bi-chat-square-text"></i></div>
                <div><h2>Session notes</h2><p>Additional context from the teacher</p></div>
            </div>
            <div class="note-block">
                <span class="note-label">Challenges</span>
                <p>{{ $daily->challenges ?: 'No challenges recorded.' }}</p>
            </div>
            <div class="note-block">
                <span class="note-label">Teacher comment</span>
                <p>{{ $daily->comment ?: 'No additional comment.' }}</p>
            </div>
            @if($daily->quiz_link || $daily->marking_guide || $daily->total_marks)
                <div class="resources-row">
                    @if($daily->quiz_link)
                        <a href="{{ $daily->quiz_link }}" target="_blank" rel="noopener noreferrer" class="resource-link">
                            <i class="bi bi-box-arrow-up-right"></i> Open quiz
                        </a>
                    @endif
                    @if($daily->marking_guide)
                        <span class="resource-detail"><i class="bi bi-file-earmark-check"></i> Marking guide: {{ $daily->marking_guide }}</span>
                    @endif
                    @if($daily->total_marks)
                        <span class="resource-detail"><i class="bi bi-award"></i> {{ $daily->total_marks }} total marks</span>
                    @endif
                </div>
            @endif
        </article>
    </section>

    <section class="detail-card attendance-card" aria-labelledby="attendanceHeading">
        <div class="attendance-heading">
            <div class="section-heading">
                <div class="section-icon attendance-icon"><i class="bi bi-person-check"></i></div>
                <div>
                    <h2 id="attendanceHeading">Student attendance</h2>
                    <p>Roster for all courses in this report · {{ $students->count() }} students</p>
                </div>
            </div>
            <div class="attendance-counts">
                <span class="count-pill count-present"><i></i>{{ $attendanceCounts['P'] }} Present</span>
                <span class="count-pill count-absent"><i></i>{{ $attendanceCounts['A'] }} Absent</span>
                <span class="count-pill count-reason"><i></i>{{ $attendanceCounts['AR'] }} With reason</span>
                <span class="count-pill count-late"><i></i>{{ $attendanceCounts['L'] }} Late</span>
                <span class="count-pill count-unmarked"><i></i>{{ $attendanceCounts['unmarked'] }} Not marked</span>
            </div>
        </div>

        <div class="attendance-tools">
            <label class="student-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" id="studentSearch" placeholder="Search a student or course…" aria-label="Search students">
            </label>
            <label class="filter-label" for="statusFilter">Show</label>
            <select id="statusFilter" class="attendance-filter">
                <option value="">All students</option>
                <option value="P">Present</option>
                <option value="A">Absent</option>
                <option value="AR">Absent with reason</option>
                <option value="L">Late</option>
                <option value="unmarked">Not marked</option>
            </select>
        </div>

        @if($students->isNotEmpty())
            <form method="POST" action="{{ route('dos.dailies.attendance', $daily->id) }}" id="attendanceForm">
                @csrf
                <div class="table-responsive attendance-table-wrap">
                    <table class="table attendance-table align-middle mb-0" id="attendanceTable">
                        <thead>
                            <tr><th scope="col">Student</th><th scope="col">Course</th><th scope="col">Current attendance</th><th scope="col">Set status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $studentAttendance = $attendances->get($student->id);
                                    $currentStatus = $studentAttendance->status ?? '';
                                    $filterStatus = $currentStatus ?: 'unmarked';
                                @endphp
                                <tr class="attendance-row" data-status="{{ $filterStatus }}">
                                    <td>
                                        <div class="student-cell">
                                            <span class="student-avatar">{{ strtoupper(substr($student->full_name ?? 'NA', 0, 2)) }}</span>
                                            <span><strong class="student-name">{{ $student->full_name ?? 'Student name unavailable' }}</strong><small>Student #{{ $student->id }}@if($student->reg_number) · {{ $student->reg_number }}@endif</small></span>
                                        </div>
                                    </td>
                                    <td><span class="student-course">{{ $courseNamesById->get($student->course_id, 'Course #' . $student->course_id) }}</span></td>
                                    <td>
                                        <span class="attendance-status status-{{ $currentStatus ?: 'unmarked' }}">
                                            <span class="status-indicator"></span>
                                            {{ $attendanceLabels[$currentStatus] ?? 'Not marked' }}
                                        </span>
                                    </td>
                                    <td>
                                        <select name="attendance[{{ $student->id }}]" class="attendance-select" aria-label="Set attendance for {{ $student->full_name }}">
                                            <option value="" @selected(!$currentStatus)>Not marked</option>
                                            <option value="P" @selected($currentStatus === 'P')>Present</option>
                                            <option value="A" @selected($currentStatus === 'A')>Absent</option>
                                            <option value="AR" @selected($currentStatus === 'AR')>Absent with reason</option>
                                            <option value="L" @selected($currentStatus === 'L')>Late</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="noStudentsFound" class="filtered-empty d-none">
                    <i class="bi bi-search"></i><strong>No matching students</strong><span>Try changing the search or attendance filter.</span>
                </div>
                <div class="attendance-footer">
                    <p><i class="bi bi-info-circle"></i> Blank statuses are left unchanged. Choose a status to record or update attendance.</p>
                    <button type="submit" class="save-attendance" id="saveAttendanceBtn" disabled>
                        <i class="bi bi-check2-circle"></i> Save attendance
                    </button>
                </div>
            </form>
        @else
            <div class="roster-empty">
                <span><i class="bi bi-people"></i></span>
                <strong>No students found for this report</strong>
                <p>There are no students assigned to the selected courses{{ $daily->branch ? ' in ' . $daily->branch->name : '' }}.</p>
            </div>
        @endif
    </section>

    <section class="approval-panel">
        <div>
            <span class="approval-kicker">Report review</span>
            <h2>{{ $daily->approved ? 'This report is approved' : 'This report is awaiting approval' }}</h2>
            <p>Changing the report status also updates approval for its related attendance records.</p>
        </div>
        <form method="POST" action="{{ route('dos.dailies.status', $daily->id) }}" class="approval-form">
            @csrf
            <label class="visually-hidden" for="reportStatus">Report status</label>
            <select name="status" id="reportStatus">
                <option value="pending" @selected(!$daily->approved)>Pending review</option>
                <option value="approved" @selected($daily->approved)>Approved</option>
            </select>
            <button type="submit"><i class="bi bi-check2"></i> Save status</button>
        </form>
    </section>
</main>

<style>
    .daily-detail {
        --detail-ink: #172f39;
        --detail-muted: #74848b;
        --detail-line: #e4ecea;
        --detail-accent: #1d7668;
        max-width: 1500px;
        margin: 0 auto;
        padding: 28px clamp(14px, 3vw, 40px) 48px;
        color: var(--detail-ink);
    }

    .detail-toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .back-link { display: inline-flex; align-items: center; gap: 8px; color: #536871; font-size: .82rem; font-weight: 700; text-decoration: none; }
    .back-link:hover { color: var(--detail-accent); }
    .report-reference { color: #8a999d; font-size: .75rem; }
    .report-reference strong { color: var(--detail-ink); }
    .detail-alert { display: flex; align-items: center; gap: 10px; border: 0; border-radius: 12px; }

    .detail-hero {
        position: relative;
        overflow: hidden;
        padding: clamp(24px, 4vw, 42px);
        border-radius: 20px;
        background:
            radial-gradient(ellipse at 87% 14%, rgba(114, 185, 157, .2), transparent 34%),
            linear-gradient(118deg, #173b3b 0%, #205c52 55%, #2d7462 100%);
        color: #fff;
        box-shadow: 0 16px 38px rgba(23, 59, 59, .15);
    }

    .hero-main { position: relative; z-index: 1; }
    .hero-kicker { color: #b6d8cb; font-size: .7rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .hero-kicker i { margin-right: 7px; }
    .detail-hero h1 { margin: 10px 0 15px; color: #fff; font-size: clamp(1.7rem, 3.2vw, 2.45rem); font-weight: 750; letter-spacing: -.045em; }
    .hero-teacher { display: flex; align-items: center; gap: 11px; margin: 0; }
    .teacher-avatar { display: grid; width: 40px; height: 40px; place-items: center; border: 1px solid rgba(255,255,255,.28); border-radius: 50%; background: rgba(255,255,255,.12); color: #fff; font-size: .73rem; font-weight: 800; }
    .hero-teacher small, .hero-teacher strong { display: block; }
    .hero-teacher small { color: #b6d8cb; font-size: .67rem; }
    .hero-teacher strong { margin-top: 2px; color: #fff; font-size: .86rem; }
    .hero-divider { width: 1px; height: 27px; margin: 0 4px; background: rgba(255,255,255,.24); }
    .branch-label { display: inline-flex; align-items: center; gap: 6px; color: #e2eee8; font-size: .78rem; }

    .hero-status { position: absolute; top: 30px; right: 34px; display: inline-flex; align-items: center; gap: 7px; padding: 8px 12px; border-radius: 999px; font-size: .73rem; font-weight: 800; }
    .hero-status.status-approved { background: #d8f3e8; color: #16634f; }
    .hero-status.status-pending { background: #ffefce; color: #885b18; }
    .status-indicator { width: 7px; height: 7px; flex: 0 0 7px; border-radius: 50%; background: currentColor; }

    .hero-courses { position: relative; z-index: 1; display: grid; grid-template-columns: 145px 1fr auto; align-items: center; gap: 12px; margin-top: 28px; padding: 16px 0; border-top: 1px solid rgba(255,255,255,.17); border-bottom: 1px solid rgba(255,255,255,.17); }
    .hero-courses-label { display: inline-flex; align-items: center; gap: 8px; color: #c1ded2; font-size: .74rem; font-weight: 750; }
    .course-chip-list { display: flex; flex-wrap: wrap; gap: 7px; }
    .course-chip { display: inline-flex; align-items: center; padding: 6px 10px; border: 1px solid rgba(225,245,236,.22); border-radius: 8px; background: rgba(255,255,255,.1); color: #fff; font-size: .74rem; font-weight: 650; }
    .course-chip-empty { color: #e7d4b8; }
    .course-total { color: #c1ded2; font-size: .7rem; white-space: nowrap; }

    .hero-bottom { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: 24px; padding-top: 16px; color: #dcebe5; font-size: .76rem; }
    .hero-bottom span { display: inline-flex; align-items: center; gap: 7px; }
    .hero-bottom i { color: #9dcbb8; }
    .hero-bottom strong { color: #a9cec0; font-size: .68rem; text-transform: uppercase; letter-spacing: .05em; }

    .detail-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; margin: 17px 0; }
    .metric-card { display: flex; min-height: 84px; align-items: center; gap: 12px; padding: 15px 17px; border: 1px solid var(--detail-line); border-radius: 14px; background: #fff; box-shadow: 0 5px 18px rgba(26, 55, 55, .035); }
    .metric-icon { display: grid; width: 39px; height: 39px; flex: 0 0 39px; place-items: center; border-radius: 11px; font-size: 1rem; }
    .metric-teal { color: #1c7768; background: #e7f4ef; }
    .metric-blue { color: #456f9c; background: #edf3fa; }
    .metric-amber { color: #a36c1c; background: #fff3dc; }
    .metric-slate { color: #637783; background: #eef2f4; }
    .metric-card small { display: block; margin-bottom: 3px; color: var(--detail-muted); font-size: .67rem; font-weight: 650; }
    .metric-card strong { color: var(--detail-ink); font-size: 1.15rem; font-weight: 800; line-height: 1; }
    .metric-suffix { align-self: flex-end; margin: 0 0 1px -8px; color: #86969b; font-size: .68rem; }

    .learning-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 16px; }
    .detail-card { border: 1px solid var(--detail-line); border-radius: 16px; background: #fff; box-shadow: 0 7px 22px rgba(26, 55, 55, .035); }
    .learning-card, .notes-card { min-height: 230px; padding: 23px; }
    .section-heading { display: flex; align-items: center; gap: 12px; }
    .section-icon { display: grid; width: 39px; height: 39px; flex: 0 0 39px; place-items: center; border-radius: 11px; background: #e9f4f0; color: var(--detail-accent); font-size: 1rem; }
    .section-heading h2 { margin: 0; color: var(--detail-ink); font-size: 1rem; font-weight: 780; letter-spacing: -.02em; }
    .section-heading p { margin: 3px 0 0; color: #86959a; font-size: .72rem; }
    .activity-list { display: grid; gap: 10px; margin: 21px 0 0; padding: 0; list-style: none; }
    .activity-list li { display: flex; align-items: flex-start; gap: 10px; color: #41565f; font-size: .82rem; line-height: 1.5; }
    .activity-marker { display: grid; width: 22px; height: 22px; flex: 0 0 22px; place-items: center; border-radius: 7px; background: #eaf5f0; color: #237b69; font-size: .74rem; }
    .soft-empty { display: flex; align-items: center; gap: 9px; margin-top: 25px; padding: 14px; border-radius: 10px; background: #f7f9f8; color: #88969a; font-size: .78rem; }
    .soft-empty i { color: #819e95; font-size: 1rem; }
    .notes-icon { background: #f0eef9; color: #726899; }
    .note-block { margin-top: 16px; }
    .note-label { color: #88969a; font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .note-block p { margin: 5px 0 0; color: #41565f; font-size: .78rem; line-height: 1.55; white-space: pre-line; }
    .resources-row { display: flex; flex-wrap: wrap; gap: 10px 16px; margin-top: 16px; padding-top: 13px; border-top: 1px solid #edf1ef; }
    .resource-link, .resource-detail { display: inline-flex; align-items: center; gap: 6px; color: #387968; font-size: .71rem; font-weight: 650; text-decoration: none; }
    .resource-link:hover { color: #165c50; text-decoration: underline; }
    .resource-detail { color: #72848a; font-weight: 550; }

    .attendance-card { overflow: hidden; }
    .attendance-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; padding: 22px 24px 18px; border-bottom: 1px solid #edf1ef; }
    .attendance-icon { background: #e8f2f7; color: #487d99; }
    .attendance-counts { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 6px; }
    .count-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 9px; border-radius: 999px; font-size: .64rem; font-weight: 750; white-space: nowrap; }
    .count-pill i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .count-present { background: #e8f5ef; color: #21755e; }
    .count-absent { background: #fff0ed; color: #ad514b; }
    .count-reason { background: #fff5df; color: #996a20; }
    .count-late { background: #edf3fb; color: #5578a7; }
    .count-unmarked { background: #f0f3f4; color: #73848a; }

    .attendance-tools { display: flex; align-items: center; gap: 9px; padding: 15px 22px; }
    .student-search { display: flex; width: min(390px, 50%); align-items: center; gap: 8px; padding: 0 11px; border: 1px solid #e3ebe8; border-radius: 9px; color: #8a999b; }
    .student-search:focus-within { border-color: #9fc7ba; box-shadow: 0 0 0 3px rgba(29, 118, 104, .08); }
    .student-search input { width: 100%; height: 38px; border: 0; outline: 0; background: transparent; color: var(--detail-ink); font: inherit; font-size: .76rem; }
    .student-search input::placeholder { color: #94a1a3; }
    .filter-label { margin-left: auto; color: #7e8d91; font-size: .7rem; }
    .attendance-filter, .attendance-select, .approval-form select { min-height: 38px; padding: 0 30px 0 11px; border: 1px solid #e3ebe8; border-radius: 9px; background-color: #fff; color: #475d65; font-size: .74rem; }

    .attendance-table { min-width: 760px; }
    .attendance-table thead th { padding: 11px 20px; border-color: #e9efed; background: #f7f9f8; color: #819094; font-size: .64rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .attendance-table tbody td { padding: 12px 20px; border-color: #eff3f1; }
    .attendance-row:hover { background: #fbfcfb; }
    .student-cell { display: flex; min-width: 220px; align-items: center; gap: 10px; }
    .student-avatar { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border: 1px solid #deebe5; border-radius: 50%; background: #f0f7f3; color: #3c7968; font-size: .67rem; font-weight: 800; }
    .student-cell strong, .student-cell small { display: block; }
    .student-cell strong { color: #29424b; font-size: .78rem; font-weight: 720; }
    .student-cell small { margin-top: 3px; color: #91a0a1; font-size: .65rem; }
    .student-course { color: #637a7d; font-size: .72rem; font-weight: 650; }
    .attendance-status { display: inline-flex; align-items: center; gap: 7px; color: #65777d; font-size: .7rem; font-weight: 750; white-space: nowrap; }
    .attendance-status .status-indicator { width: 6px; height: 6px; flex-basis: 6px; }
    .attendance-status.status-P { color: #21755e; }
    .attendance-status.status-A { color: #ad514b; }
    .attendance-status.status-AR { color: #996a20; }
    .attendance-status.status-L { color: #5578a7; }
    .attendance-status.status-unmarked { color: #829195; }
    .attendance-select { min-width: 150px; min-height: 35px; }

    .filtered-empty { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 30px; color: #849397; text-align: center; }
    .filtered-empty > i { margin-bottom: 3px; font-size: 1.25rem; }
    .filtered-empty strong { color: #52666d; font-size: .82rem; }
    .filtered-empty span { font-size: .72rem; }
    .attendance-footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 15px 21px; border-top: 1px solid #edf1ef; }
    .attendance-footer p { display: flex; align-items: center; gap: 7px; margin: 0; color: #819094; font-size: .7rem; }
    .attendance-footer p i { color: #699188; }
    .save-attendance, .approval-form button { display: inline-flex; min-height: 39px; align-items: center; justify-content: center; gap: 7px; padding: 0 15px; border: 0; border-radius: 9px; background: #1d7668; color: #fff; font-size: .75rem; font-weight: 750; transition: background .15s ease, transform .15s ease; }
    .save-attendance:hover:not(:disabled), .approval-form button:hover { transform: translateY(-1px); background: #155e54; }
    .save-attendance:disabled { cursor: not-allowed; opacity: .46; }

    .roster-empty { display: flex; min-height: 220px; flex-direction: column; align-items: center; justify-content: center; padding: 28px; text-align: center; }
    .roster-empty > span { display: grid; width: 48px; height: 48px; margin-bottom: 8px; place-items: center; border-radius: 14px; background: #f1f5f4; color: #7d9690; font-size: 1.25rem; }
    .roster-empty strong { color: #425860; font-size: .86rem; }
    .roster-empty p { margin: 5px 0 0; color: #89979a; font-size: .74rem; }

    .approval-panel { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-top: 16px; padding: 20px 23px; border: 1px solid #dfeae5; border-radius: 15px; background: linear-gradient(105deg, #f3f8f5, #fff); }
    .approval-kicker { color: #398171; font-size: .64rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .approval-panel h2 { margin: 4px 0 3px; color: var(--detail-ink); font-size: .95rem; font-weight: 780; }
    .approval-panel p { margin: 0; color: #809094; font-size: .7rem; }
    .approval-form { display: flex; align-items: center; gap: 8px; }
    .approval-form select { min-width: 150px; }
    .approval-form button { white-space: nowrap; }

    @media (max-width: 1100px) {
        .detail-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .attendance-heading { flex-direction: column; }
        .attendance-counts { justify-content: flex-start; }
    }

    @media (max-width: 720px) {
        .daily-detail { padding: 18px 12px 32px; }
        .detail-hero { padding: 24px 18px; border-radius: 16px; }
        .hero-status { position: static; width: fit-content; margin-top: 16px; }
        .hero-courses { grid-template-columns: 1fr; gap: 9px; margin-top: 20px; }
        .course-total { grid-row: 3; }
        .hero-bottom { gap: 12px 18px; }
        .learning-grid { grid-template-columns: 1fr; }
        .learning-card, .notes-card { min-height: 0; padding: 18px; }
        .attendance-heading { padding: 18px; }
        .attendance-counts { gap: 5px; }
        .attendance-tools { flex-wrap: wrap; padding: 13px; }
        .student-search { width: 100%; }
        .filter-label { margin-left: 0; }
        .attendance-filter { flex: 1; }
        .attendance-footer, .approval-panel { align-items: stretch; flex-direction: column; }
        .attendance-footer p { align-items: flex-start; line-height: 1.45; }
        .save-attendance { width: 100%; }
        .approval-form { align-items: stretch; flex-direction: column; }
        .approval-form select, .approval-form button { width: 100%; }
    }

    @media (max-width: 440px) {
        .detail-metrics { gap: 8px; }
        .metric-card { min-height: 75px; gap: 8px; padding: 11px; }
        .metric-icon { width: 33px; height: 33px; flex-basis: 33px; font-size: .9rem; }
        .metric-card small { font-size: .61rem; }
        .metric-card strong { font-size: 1rem; }
        .attendance-counts { display: grid; grid-template-columns: repeat(2, 1fr); }
        .count-pill { justify-content: center; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('studentSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = Array.from(document.querySelectorAll('.attendance-row'));
    const emptyState = document.getElementById('noStudentsFound');
    const saveButton = document.getElementById('saveAttendanceBtn');
    const statusSelects = Array.from(document.querySelectorAll('.attendance-select'));

    function filterStudents() {
        const searchTerm = searchInput.value.trim().toLocaleLowerCase();
        const selectedStatus = statusFilter.value;
        let visibleRows = 0;

        rows.forEach(function (row) {
            const matchesSearch = row.textContent.toLocaleLowerCase().includes(searchTerm);
            const matchesStatus = !selectedStatus || row.dataset.status === selectedStatus;
            row.classList.toggle('d-none', !matchesSearch || !matchesStatus);
            if (matchesSearch && matchesStatus) visibleRows++;
        });

        if (emptyState) emptyState.classList.toggle('d-none', visibleRows > 0 || rows.length === 0);
    }

    function updateSaveButton() {
        if (saveButton) {
            saveButton.disabled = !statusSelects.some(function (select) {
                return select.value !== '';
            });
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterStudents);
    if (statusFilter) statusFilter.addEventListener('change', filterStudents);

    statusSelects.forEach(function (select) {
        select.addEventListener('change', function () {
            const row = select.closest('.attendance-row');
            row.dataset.status = select.value || 'unmarked';
            filterStudents();
            updateSaveButton();
        });
    });

    updateSaveButton();
});
</script>
@endsection
