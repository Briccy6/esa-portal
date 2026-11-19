@extends('dos.layout')

@section('content')
<style>
    :root {
        --ink:        #111318;
        --ink-1:      #374151;
        --ink-2:      #6b7280;
        --ink-3:      #9ca3af;
        --ink-4:      #d1d5db;
        --rule:       #e5e7eb;
        --bg:         #f9fafb;
        --paper:      #ffffff;
        --primary:    #2563eb;
        --primary-1:  #1d4ed8;
        --success:    #10b981;
        --danger:     #ef4444;
        --warning:    #f59e0b;
        --info:       #3b82f6;
        --border:     #e5e7eb;
        --shadow:     rgba(0,0,0,0.1);
        --radius:     12px;
        --radius-sm:  6px;
        --transition: all 0.2s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
        background: var(--bg);
        color: var(--ink);
        line-height: 1.5;
    }

    .page-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 8px;
    }

    .page-desc {
        color: var(--ink-2);
        font-size: 16px;
    }

    .form-card {
        background: var(--paper);
        border-radius: var(--radius);
        box-shadow: 0 1px 3px var(--shadow);
        margin-bottom: 24px;
    }

    .draft-status {
        display: none;
        align-items: center;
        gap: 7px;
        padding: 9px 24px;
        border-bottom: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1e40af;
        font-size: 12px;
    }

    .draft-status.visible {
        display: flex;
    }

    .section-block {
        padding: 24px;
        border-bottom: 1px solid var(--rule);
    }

    .section-block:last-child {
        border-bottom: none;
    }

    .section-hd {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .section-hd-badge {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .section-hd h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 2px;
    }

    .section-hd p {
        font-size: 14px;
        color: var(--ink-2);
    }

    .progress-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0;
        padding: 24px 24px 18px;
        position: relative;
        background: #fff;
        border-bottom: 1px solid var(--rule);
    }

    .progress-bar::before {
        content: '';
        position: absolute;
        top: 44px;
        left: 8%;
        right: 8%;
        height: 2px;
        background: var(--rule);
        z-index: 0;
    }

    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    .ps-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--paper);
        border: 2px solid var(--rule);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        color: var(--ink-2);
        transition: var(--transition);
    }

    .ps-dot.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .ps-label {
        font-size: 12px;
        color: var(--ink-2);
        text-align: center;
    }

    .step-panel {
        display: none;
    }

    .step-panel.active {
        display: block;
    }

    .wizard-navigation {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 24px;
        border-top: 1px solid var(--rule);
        background: #fff;
    }

    .wizard-navigation-group {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }

    .wizard-error {
        display: none;
        margin: 0 24px 16px;
        padding: 12px 14px;
        border: 1px solid #fecaca;
        border-radius: var(--radius-sm);
        background: #fef2f2;
        color: #b91c1c;
        font-size: 14px;
    }

    .wizard-error.show {
        display: block;
    }

    .terms-hint,
    .step-hint {
        color: var(--ink-2);
        font-size: 13px;
        margin-top: 6px;
    }

    .term-check-pill.selected {
        border-color: var(--primary);
        background: #eff6ff;
        color: #1d4ed8;
    }

    .term-date {
        display: block;
        color: var(--ink-2);
        font-size: 12px;
    }

    .course-assignment-card {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        margin-bottom: 12px;
        background: #fff;
    }

    .course-assignment-options {
        flex: 1;
        min-width: 180px;
        margin-left: 12px;
    }

    .course-branch-option.is-disabled {
        opacity: 0.45;
        cursor: not-allowed !important;
    }

    .teacher-assignment-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
    }

    .teacher-assignment-card {
        display: grid;
        gap: 8px;
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: #fff;
    }

    .teacher-assignment-card > strong {
        color: var(--ink);
    }

    .teacher-assignment-card > span {
        color: var(--ink-2);
        font-size: 13px;
    }

    .review-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .review-item {
        padding: 16px;
        border: 1px solid var(--rule);
        border-radius: 8px;
        background: #fff;
        min-width: 0;
    }

    .review-item strong {
        display: block;
        margin-bottom: 8px;
        color: var(--ink-2);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .review-item span {
        overflow-wrap: anywhere;
        color: var(--ink);
    }

    .review-item-wide {
        grid-column: 1 / -1;
    }

    .review-assignment-list {
        display: grid;
        gap: 10px;
    }

    .review-branch-group {
        border: 1px solid var(--rule);
        border-radius: 8px;
        overflow: hidden;
    }

    .review-branch-title {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background: #f3f6fb;
        color: #1f2937;
        font-size: 13px;
        font-weight: 700;
    }

    .review-branch-title i {
        color: var(--primary);
    }

    .review-course-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(180px, 0.45fr);
        gap: 12px;
        align-items: center;
        padding: 11px 12px;
        border-top: 1px solid var(--rule);
    }

    .review-course-name {
        min-width: 0;
        font-weight: 600;
        color: var(--ink);
        overflow-wrap: anywhere;
    }

    .review-teacher {
        color: var(--ink-2);
        font-size: 13px;
        text-align: right;
    }

    .review-teacher strong {
        display: inline;
        margin: 0 4px 0 0;
        color: var(--ink-2);
        font-size: 11px;
    }

    .progress-step.complete .ps-dot {
        background: #e8f5ee;
        border-color: #16845b;
        color: #16845b;
    }

    .field-group {
        margin-bottom: 20px;
    }

    .field-label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--ink);
        font-size: 14px;
    }

    .req {
        color: var(--danger);
    }

    .f-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 14px;
        transition: var(--transition);
    }

    .f-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-1);
    }

    .btn-secondary {
        background: var(--ink-4);
        color: var(--ink);
    }

    .btn-secondary:hover {
        background: var(--ink-3);
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .alert-box {
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-box.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: var(--success);
    }

    .alert-box.danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: var(--danger);
    }

    .debug-bar {
        background: var(--bg);
        padding: 12px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .debug-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .debug-cell strong {
        font-size: 12px;
        color: var(--ink-2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .debug-cell span {
        font-size: 13px;
        color: var(--ink);
    }

    .branch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .branch-card {
        padding: 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }

    .branch-card:hover {
        border-color: var(--primary);
        background: var(--bg);
    }

    .branch-card:focus-within {
        outline: 3px solid rgba(37, 99, 235, 0.2);
        outline-offset: 2px;
    }

    .branch-card.selected {
        border-color: var(--primary);
        background: #eff6ff;
    }

    .branch-card-icon {
        font-size: 24px;
        color: var(--ink-2);
        margin-bottom: 8px;
    }

    .branch-card-name {
        font-weight: 500;
        color: var(--ink);
    }

    .fallback-toggle {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 13px;
        cursor: pointer;
        text-decoration: underline;
        margin-bottom: 16px;
    }

    .fallback-body {
        background: var(--bg);
        padding: 16px;
        border-radius: var(--radius-sm);
    }

    .term-checks {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .term-check-pill {
        padding: 8px 16px;
        background: var(--paper);
        border: 1px solid var(--border);
        border-radius: 20px;
        cursor: pointer;
        transition: var(--transition);
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .term-check-pill:hover {
        border-color: var(--primary);
    }

    .term-check-pill input[type="checkbox"] {
        margin: 0;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--ink-2);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .page-wrapper {
            padding: 12px;
        }

        .debug-bar {
            flex-direction: column;
            gap: 12px;
        }

        .branch-grid {
            grid-template-columns: 1fr;
        }

        .term-checks {
            flex-direction: column;
        }

        .section-block {
            padding: 18px;
        }

        .progress-bar {
            padding: 18px 12px 14px;
        }

        .progress-bar::before {
            top: 36px;
        }

        .ps-dot {
            width: 34px;
            height: 34px;
        }

        .ps-label {
            max-width: 76px;
            font-size: 10px;
            line-height: 1.2;
        }

        .course-assignment-card {
            flex-direction: column;
        }

        .course-assignment-options {
            width: 100%;
            min-width: 0;
            margin: 10px 0 0;
        }

        .wizard-navigation {
            align-items: stretch;
            flex-direction: column;
            padding: 14px;
        }

        .wizard-navigation-group {
            width: 100%;
            margin-left: 0;
        }

        .wizard-navigation .btn {
            flex: 1;
            justify-content: center;
        }

        .wizard-navigation .btn[hidden] {
            display: none !important;
        }

        .review-grid {
            grid-template-columns: 1fr;
        }

        .review-course-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }

        .review-teacher {
            text-align: left;
        }
    }

    @media (max-width: 400px) {
        .page-wrapper {
            padding: 8px;
        }

        .page-title {
            font-size: 23px;
        }

        .progress-bar {
            padding-right: 6px;
            padding-left: 6px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Create a lesson</h1>
        <p class="page-desc">Follow the steps to define the lesson, assign branches and teachers, and choose its study terms.</p>
    </div>

    @if(session('success'))
        <div class="alert-box success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-box danger">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="alert-box danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('dos.lessons.store') }}" method="POST" id="multiBranchForm">
        @csrf
        <input type="hidden" name="is_multi_branch" value="1">
        
        @php
            $oldTeacherAssignments = collect(old('selected_courses', []))
                ->filter(fn ($assignment) => !empty($assignment['teacher_id']))
                ->mapWithKeys(fn ($assignment) => [
                    $assignment['course_id'] . '_' . $assignment['branch_id'] => $assignment['teacher_id'],
                ])
                ->all();
        @endphp

        <!-- Store course-teachers mapping for JavaScript -->
        <script>
            window.courseTeachersMap = @json($courseTeachersData ?? []);
            window.courseNamesMap = @json($courses->pluck('course_name', 'id')->toArray());
            window.branchNamesMap = @json($branches->pluck('name', 'id')->toArray());
            window.oldTeacherAssignments = @json($oldTeacherAssignments);
        </script>

        <div class="form-card">
            <div class="draft-status" id="draftStatus" role="status" aria-live="polite">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <span>Draft saved on this device</span>
            </div>
            <div class="progress-bar" aria-label="Lesson creation progress">
                <div class="progress-step" id="ps1" aria-current="step">
                    <div class="ps-dot" id="pd1">1</div>
                    <span class="ps-label">Lesson details</span>
                </div>
                <div class="progress-step" id="ps2">
                    <div class="ps-dot" id="pd2">2</div>
                    <span class="ps-label">Assignments</span>
                </div>
                <div class="progress-step" id="ps3">
                    <div class="ps-dot" id="pd3">3</div>
                    <span class="ps-label">Study terms</span>
                </div>
                <div class="progress-step" id="ps4">
                    <div class="ps-dot" id="pd4">4</div>
                    <span class="ps-label">Review</span>
                </div>
            </div>
            <div class="wizard-error" id="wizardError" role="alert"></div>

            {{-- Basic Info --}}
            <section class="step-panel active" data-step-panel="1" aria-labelledby="step1-title">
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-info fa-xs"></i></div>
                    <div>
                        <h2 id="step1-title">Lesson details</h2>
                        <p>Name the lesson and set its grading values.</p>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="lesson-title">Lesson title <span class="req">*</span></label>
                    <input type="text" id="lesson-title" name="lesson_title" class="f-control"
                           value="{{ old('lesson_title') }}" maxlength="255" autocomplete="off" required>
                    <p class="step-hint">Use a clear name that teachers and students will recognize.</p>
                </div>

                <div class="row">
                    <div class="col-md-4 field-group">
                        <label class="field-label" for="max-test-marks">Maximum test marks</label>
                        <input type="number" id="max-test-marks" name="max_test_marks" class="f-control"
                               value="{{ old('max_test_marks', 100) }}" min="0" step="1">
                    </div>
                    <div class="col-md-4 field-group">
                        <label class="field-label" for="max-exam-marks">Maximum exam marks</label>
                        <input type="number" id="max-exam-marks" name="max_exam_marks" class="f-control"
                               value="{{ old('max_exam_marks', 100) }}" min="0" step="1">
                    </div>
                    <div class="col-md-4 field-group">
                        <label class="field-label" for="lesson-weight">Lesson weight (%) <span class="req">*</span></label>
                        <input type="number" id="lesson-weight" name="lesson_weight" class="f-control"
                               value="{{ old('lesson_weight', 1) }}" min="1" max="100" step="1" required>
                    </div>
                </div>
            </div>
            </section>

            <section class="step-panel" data-step-panel="2" aria-labelledby="step2-title">
            {{-- Step 2: Branches --}}
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-map-marker-alt fa-xs"></i></div>
                    <div>
                        <h2 id="step2-title">Select branches</h2>
                        <p>Choose which branches this lesson will be available in</p>
                    </div>
                </div>

                @if($branches->count() > 0)
                    <div class="branch-grid" id="branchGrid">
                        @foreach($branches as $branch)
                            <label class="branch-card" style="cursor:pointer;">
                                <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                                       class="branch-checkbox visually-hidden"
                                       {{ in_array($branch->id, old('branches', [])) ? 'checked' : '' }}
                                       onchange="updateBranchSelection()">
                                <div class="branch-card-icon"><i class="fas fa-building"></i></div>
                                <div class="branch-card-name">{{ $branch->name }}</div>
                            </label>
                        @endforeach
                    </div>
                    <p style="font-size:.82rem;color:var(--ink-2);margin-top:12px;">
                        <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                        Selected branches: <span id="selectedBranchCount">0</span>
                    </p>
                @else
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>No branches found. Please create branches first.</p>
                    </div>
                @endif
            </div>

            {{-- Course assignments --}}
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-book fa-xs"></i></div>
                    <div>
                        <h2>Assign Courses</h2>
                        <p>Link this lesson to specific courses in each branch</p>
                    </div>
                </div>
                
                <!-- Hidden fields container for course selections -->
                <div id="courseFieldsContainer" style="display:none;"></div>
                
                @if($courses->count() > 0)
                    <div style="margin-bottom:20px;">
                        <p style="font-size:.82rem;color:var(--ink-2);margin-bottom:12px;">
                            <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                            For each course, select which branches should have this lesson in that course:
                        </p>
                        @foreach($courses as $course)
                            <div class="course-assignment-card">
                                <div style="flex:1;">
                                    <strong style="font-size:.88rem;display:block;">{{ $course->course_name }}</strong>
                                    <div style="font-size:.82rem;color:var(--ink-2);margin-top:4px;">
                                        {{ $course->course_code }}{{ $course->description ? ' - ' . $course->description : '' }}
                                    </div>
                                </div>
                                <div class="course-assignment-options">
                                    <div style="display:flex;flex-direction:column;gap:6px;">
                                        @if($course->branches && $course->branches->count() > 0)
                                            @foreach($course->branches as $b)
                                                <label class="course-branch-option" style="display:flex;align-items:center;gap:6px;font-size:.82rem;cursor:pointer;">
                                                    <input type="checkbox" name="course_branch_pairs[]" 
                                                           value="{{ $course->id }}_{{ $b->id }}"
                                                           class="course-checkbox"
                                                           {{ in_array($course->id . '_' . $b->id, old('course_branch_pairs', [])) ? 'checked' : '' }}
                                                           onchange="updateCourseSelection()">
                                                    {{ $b->name }}
                                                </label>
                                            @endforeach
                                        @else
                                            <span style="font-size:.82rem;color:var(--ink-3);">No branches</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <p style="font-size:.82rem;color:var(--ink-2);margin-top:12px;">
                            <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                            Selected course-branch pairs: <span id="selectedCourseCount">0</span>
                        </p>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No courses available.</p>
                    </div>
                @endif
            </div>

            {{-- Teacher assignments --}}
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-chalkboard-user fa-xs"></i></div>
                    <div>
                        <h2>Assign Teachers</h2>
                        <p>Assign a teacher to each selected course-branch combination</p>
                    </div>
                </div>

                <div id="teacherAssignmentsContainer">
                    <p style="font-size:.82rem;color:var(--ink-2);margin-bottom:16px;padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                        Select courses above first, then assign teachers to each branch-course combination.
                    </p>
                </div>
            </div>
            </section>

            <section class="step-panel" data-step-panel="3" aria-labelledby="step3-title">
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-calendar fa-xs"></i></div>
                    <div>
                        <h2 id="step3-title">Academic year and study terms</h2>
                        <p>Select the school year and the terms in which this lesson will be studied.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="field-group">
                        <label class="field-label" for="academic-year">Academic year <span class="req">*</span></label>
                        <select id="academic-year" name="academic_year_id" class="f-control" required>
                            <option value="">Select academic year</option>
                            @if($ongoingAcademicYear)
                                <option value="{{ $ongoingAcademicYear->id }}" @selected(old('academic_year_id', $ongoingAcademicYear->id) == $ongoingAcademicYear->id)>
                                    {{ $ongoingAcademicYear->year }} ({{ ucfirst($ongoingAcademicYear->status) }})
                                </option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Terms to be studied <span class="req">*</span></label>
                    <p class="terms-hint">Choose one or two terms for this lesson. At least one term is required.</p>
                    <div class="term-checks">
                        @foreach($terms as $term)
                            <label class="term-check-pill {{ in_array($term->id, old('terms', [])) ? 'selected' : '' }}">
                                <input type="checkbox" name="terms[]" value="{{ $term->id }}"
                                       {{ in_array($term->id, old('terms', [])) ? 'checked' : '' }}
                                       onchange="updateTermSelection(this)">
                                <span>
                                    {{ $term->name }}
                                    @if($term->start_date || $term->end_date)
                                        <span class="term-date">
                                            {{ $term->start_date?->format('M j, Y') ?? 'Start date not set' }}
                                            – {{ $term->end_date?->format('M j, Y') ?? 'End date not set' }}
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                        @if($terms->count() === 0)
                            <span style="font-size:.82rem;color:var(--ink-2);">No terms are configured for this academic year. Add terms before creating a lesson.</span>
                        @endif
                    </div>
                    <p class="terms-hint"><span id="selectedTermCount">0</span> selected (maximum 2).</p>
                </div>
            </div>
            </section>

            <section class="step-panel" data-step-panel="4" aria-labelledby="step4-title">
                <div class="section-block">
                    <div class="section-hd">
                        <div class="section-hd-badge"><i class="fas fa-clipboard-check fa-xs"></i></div>
                        <div>
                            <h2 id="step4-title">Review lesson</h2>
                            <p>Confirm the lesson details, terms, and branch assignments before saving.</p>
                        </div>
                    </div>
                    <div class="review-grid" id="lessonReview" aria-live="polite"></div>
                </div>
            </section>

            <div class="wizard-navigation">
                <a href="{{ route('staff.lessons.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times" aria-hidden="true"></i>Cancel
                </a>
                <div class="wizard-navigation-group">
                    <button type="button" class="btn btn-secondary" id="previousStepBtn" onclick="changeStep(-1)" hidden>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>Back
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn" onclick="changeStep(1)">
                        Continue<i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" hidden>
                        <i class="fas fa-save" aria-hidden="true"></i>Create lesson
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // ── State Management ──────────────────────────────────────────
    let selectedBranches = new Set();
    let selectedCourses = new Set();
    let selectedTeachers = window.oldTeacherAssignments || {};
    let currentStep = 1;
    const lessonDraftKey = 'dos.lesson-create-draft';
    let draftSaveTimer = null;
    const failedFields = @json($errors->keys());
    if (failedFields.some(field => field.startsWith('branches') || field.startsWith('selected_courses'))) {
        currentStep = 2;
    } else if (failedFields.some(field => field.startsWith('terms') || field === 'academic_year_id')) {
        currentStep = 3;
    }

    function saveLessonDraft() {
        const draft = {
            lessonTitle: document.querySelector('[name="lesson_title"]')?.value || '',
            maxTestMarks: document.querySelector('[name="max_test_marks"]')?.value || '',
            maxExamMarks: document.querySelector('[name="max_exam_marks"]')?.value || '',
            lessonWeight: document.querySelector('[name="lesson_weight"]')?.value || '',
            academicYearId: document.querySelector('[name="academic_year_id"]')?.value || '',
            branches: Array.from(document.querySelectorAll('[name="branches[]"]:checked')).map(input => input.value),
            courses: Array.from(document.querySelectorAll('[name="course_branch_pairs[]"]:checked')).map(input => input.value),
            terms: Array.from(document.querySelectorAll('[name="terms[]"]:checked')).map(input => input.value),
            teachers: selectedTeachers
        };

        try {
            localStorage.setItem(lessonDraftKey, JSON.stringify(draft));
            const status = document.getElementById('draftStatus');
            status.classList.add('visible');
            clearTimeout(draftSaveTimer);
            draftSaveTimer = setTimeout(() => status.classList.remove('visible'), 2500);
        } catch (error) {
            // Storage may be unavailable in private or restricted browser contexts.
        }
    }

    function clearLessonDraft() {
        try {
            localStorage.removeItem(lessonDraftKey);
        } catch (error) {
            // Storage may be unavailable in private or restricted browser contexts.
        }
    }

    function restoreLessonDraft() {
        if (failedFields.length > 0) {
            return false;
        }

        let draft;
        try {
            const storedDraft = localStorage.getItem(lessonDraftKey);
            draft = storedDraft ? JSON.parse(storedDraft) : null;
        } catch (error) {
            return false;
        }

        if (!draft || typeof draft !== 'object') {
            return false;
        }

        const setValue = (name, value) => {
            const input = document.querySelector(`[name="${name}"]`);
            if (input && value !== undefined && value !== null) {
                input.value = value;
            }
        };

        setValue('lesson_title', draft.lessonTitle);
        setValue('max_test_marks', draft.maxTestMarks);
        setValue('max_exam_marks', draft.maxExamMarks);
        setValue('lesson_weight', draft.lessonWeight);
        setValue('academic_year_id', draft.academicYearId);

        document.querySelectorAll('[name="branches[]"]').forEach(input => {
            input.checked = Array.isArray(draft.branches) && draft.branches.includes(input.value);
        });
        document.querySelectorAll('[name="course_branch_pairs[]"]').forEach(input => {
            input.checked = Array.isArray(draft.courses) && draft.courses.includes(input.value);
        });
        document.querySelectorAll('[name="terms[]"]').forEach(input => {
            input.checked = Array.isArray(draft.terms) && draft.terms.includes(input.value);
        });
        selectedTeachers = draft.teachers && typeof draft.teachers === 'object' ? draft.teachers : {};

        const status = document.getElementById('draftStatus');
        status.querySelector('span').textContent = 'Saved draft restored';
        status.classList.add('visible');
        setTimeout(() => status.classList.remove('visible'), 3500);
        return true;
    }

    // ── Branch Selection ──────────────────────────────────────────
    function updateBranchSelection() {
        selectedBranches.clear();
        const checkboxes = document.querySelectorAll('.branch-checkbox:checked');
        const allCards = document.querySelectorAll('label.branch-card');
        
        // Clear all selected states
        allCards.forEach(card => card.classList.remove('selected'));
        
        // Add selected state to checked branches
        checkboxes.forEach(checkbox => {
            const branchId = checkbox.value;
            selectedBranches.add(parseInt(branchId));
            
            // Find and highlight the card
            const card = checkbox.closest('label.branch-card');
            if (card) {
                card.classList.add('selected');
            }
        });

        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
            const branchId = parseInt(checkbox.value.split('_')[1], 10);
            const branchIsSelected = selectedBranches.has(branchId);
            checkbox.disabled = !branchIsSelected;
            if (!branchIsSelected) {
                checkbox.checked = false;
            }
            checkbox.closest('.course-branch-option')?.classList.toggle('is-disabled', !branchIsSelected);
        });
        
        // Update display
        const countEl = document.getElementById('selectedBranchCount');
        if (countEl) countEl.textContent = selectedBranches.size;
        updateCourseSelection();
        saveLessonDraft();
    }

    // ── Course Selection ──────────────────────────────────────────
    function updateCourseSelection() {
        selectedCourses.clear();
        const checkboxes = document.querySelectorAll('.course-checkbox:checked');
        
        checkboxes.forEach(checkbox => {
            selectedCourses.add(checkbox.value);
        });
        Object.keys(selectedTeachers).forEach(pair => {
            if (!selectedCourses.has(pair)) {
                delete selectedTeachers[pair];
            }
        });
        
        const countEl = document.getElementById('selectedCourseCount');
        if (countEl) countEl.textContent = selectedCourses.size;
        
        // Update teacher assignment UI
        renderTeacherAssignments();
        updateSubmitButton();
        saveLessonDraft();
    }

    function updateTermSelection(changedCheckbox) {
        const checkboxes = Array.from(document.querySelectorAll('input[name="terms[]"]'));
        let selected = checkboxes.filter(checkbox => checkbox.checked);
        if (selected.length > 2) {
            changedCheckbox.checked = false;
            selected = checkboxes.filter(checkbox => checkbox.checked);
            showWizardError('A lesson can be assigned to no more than two terms.');
        } else {
            clearWizardError();
        }
        const countEl = document.getElementById('selectedTermCount');
        if (countEl) countEl.textContent = selected.length;

        checkboxes.forEach(checkbox => {
            checkbox.closest('.term-check-pill')?.classList.toggle('selected', checkbox.checked);
        });
        updateSubmitButton();
        saveLessonDraft();
    }

    // ── Teacher Selection UI ──────────────────────────────────────
    function renderTeacherAssignments() {
        const container = document.getElementById('teacherAssignmentsContainer');
        container.replaceChildren();
        
        if (selectedCourses.size === 0) {
            const hint = document.createElement('p');
            hint.className = 'step-hint';
            hint.textContent = 'Select course and branch combinations above to assign teachers.';
            container.appendChild(hint);
            return;
        }

        const assignments = document.createElement('div');
        assignments.className = 'teacher-assignment-list';
        selectedCourses.forEach(pair => {
            const [courseId, branchId] = pair.split('_');
            const pairKey = `${courseId}_${branchId}`;
            const teachers = window.courseTeachersMap[courseId] || [];

            const card = document.createElement('div');
            card.className = 'teacher-assignment-card';
            const courseName = document.createElement('strong');
            courseName.textContent = getCourseNameById(courseId);
            const branchName = document.createElement('span');
            branchName.textContent = getBranchNameById(branchId);

            const label = document.createElement('label');
            label.className = 'field-label';
            label.textContent = 'Assign teacher (optional)';
            const select = document.createElement('select');
            const selectId = `teacher-${pairKey}`;
            label.htmlFor = selectId;
            select.id = selectId;
            select.className = 'f-control teacher-select';
            select.dataset.pair = pairKey;
            select.addEventListener('change', function () {
                updateTeacherSelection(this);
            });

            const noTeacher = document.createElement('option');
            noTeacher.value = '';
            noTeacher.textContent = 'No teacher assigned';
            select.appendChild(noTeacher);
            teachers.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = teacher.teacher_name;
                option.selected = selectedTeachers[pairKey] == teacher.id;
                select.appendChild(option);
            });

            card.append(courseName, branchName, label, select);
            assignments.appendChild(card);
        });
        container.appendChild(assignments);
    }

    function updateTeacherSelection(selectElement) {
        const pairKey = selectElement.dataset.pair;
        const teacherId = selectElement.value;
        
        if (teacherId) {
            selectedTeachers[pairKey] = teacherId;
        } else {
            delete selectedTeachers[pairKey];
        }
        
        updateSubmitButton();
        saveLessonDraft();
    }

    function getCourseNameById(courseId) {
        return window.courseNamesMap[courseId] || `Course ${courseId}`;
    }

    function getBranchNameById(branchId) {
        return window.branchNamesMap[branchId] || `Branch ${branchId}`;
    }

    // ── UI Helpers ────────────────────────────────────────────────
    function syncSteps() {
        document.querySelectorAll('.step-panel').forEach(panel => {
            const isCurrent = Number(panel.dataset.stepPanel) === currentStep;
            panel.classList.toggle('active', isCurrent);
            panel.setAttribute('aria-hidden', String(!isCurrent));
        });

        document.querySelectorAll('.progress-step').forEach((step, index) => {
            const stepNumber = index + 1;
            step.classList.toggle('complete', stepNumber < currentStep);
            step.classList.toggle('current', stepNumber === currentStep);
            step.removeAttribute('aria-current');
            if (stepNumber === currentStep) {
                step.setAttribute('aria-current', 'step');
            }
            const dot = step.querySelector('.ps-dot');
            dot.classList.toggle('active', stepNumber === currentStep);
            dot.textContent = String(stepNumber);
        });

        document.getElementById('previousStepBtn').hidden = currentStep === 1;
        document.getElementById('nextStepBtn').hidden = currentStep === 4;
        document.getElementById('submitBtn').hidden = currentStep !== 4;

        if (currentStep === 4) {
            renderLessonReview();
        }
    }

    function showWizardError(message) {
        const error = document.getElementById('wizardError');
        error.textContent = message;
        error.classList.add('show');
    }

    function clearWizardError() {
        const error = document.getElementById('wizardError');
        error.textContent = '';
        error.classList.remove('show');
    }

    function validateCurrentStep() {
        clearWizardError();

        if (currentStep === 1) {
            const fields = document.querySelectorAll('[data-step-panel="1"] input[required], [data-step-panel="1"] input[type="number"]');
            for (const field of fields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
        }

        if (currentStep === 2) {
            if (selectedBranches.size === 0) {
                showWizardError('Select at least one branch to continue.');
                return false;
            }
            if (selectedCourses.size === 0) {
                showWizardError('Select at least one available course and branch combination.');
                return false;
            }
        }

        if (currentStep === 3) {
            const academicYear = document.getElementById('academic-year');
            if (!academicYear.checkValidity()) {
                academicYear.reportValidity();
                return false;
            }
            const selectedTerms = document.querySelectorAll('input[name="terms[]"]:checked').length;
            if (selectedTerms === 0 || selectedTerms > 2) {
                showWizardError('Choose at least one term and no more than two terms for this lesson.');
                return false;
            }
        }

        return true;
    }

    function changeStep(direction) {
        if (direction > 0 && !validateCurrentStep()) {
            return;
        }

        currentStep = Math.min(4, Math.max(1, currentStep + direction));
        syncSteps();
        clearWizardError();
        document.querySelector('.page-header').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function renderLessonReview() {
        const container = document.getElementById('lessonReview');
        container.replaceChildren();

        const academicYear = document.getElementById('academic-year');
        const terms = Array.from(document.querySelectorAll('input[name="terms[]"]:checked'))
            .map(checkbox => checkbox.closest('label').innerText.trim().replace(/\s+/g, ' '));
        const branches = Array.from(selectedBranches)
            .map(branchId => window.branchNamesMap[branchId])
            .filter(Boolean);
        const assignmentsByBranch = new Map();

        Array.from(selectedCourses).forEach(pair => {
            const [courseId, branchId] = pair.split('_');
            const branchName = getBranchNameById(branchId);
            const teacherSelect = document.querySelector(`.teacher-select[data-pair="${pair}"]`);
            const teacher = teacherSelect?.selectedOptions[0]?.textContent.trim();

            if (!assignmentsByBranch.has(branchName)) {
                assignmentsByBranch.set(branchName, []);
            }
            assignmentsByBranch.get(branchName).push({
                courseName: getCourseNameById(courseId),
                teacher: teacher && teacher !== 'Select a teacher' && teacher !== 'No teacher assigned'
                    ? teacher
                    : 'Not assigned'
            });
        });

        const summaryItems = [
            ['Lesson', document.getElementById('lesson-title').value.trim()],
            ['Academic year', academicYear.selectedOptions[0]?.textContent.trim() || 'Not selected'],
            ['Study terms', terms.join(', ') || 'None selected'],
            ['Branches', branches.join(', ') || 'None selected'],
            ['Grading', `Test: ${document.getElementById('max-test-marks').value || 0} · Exam: ${document.getElementById('max-exam-marks').value || 0} · Weight: ${document.getElementById('lesson-weight').value}%`]
        ];

        summaryItems.forEach(([label, value]) => {
            const item = document.createElement('div');
            item.className = 'review-item';
            const heading = document.createElement('strong');
            heading.textContent = label;
            const detail = document.createElement('span');
            detail.textContent = value;
            item.append(heading, detail);
            container.appendChild(item);
        });

        const assignmentsItem = document.createElement('div');
        assignmentsItem.className = 'review-item review-item-wide';

        const assignmentsHeading = document.createElement('strong');
        assignmentsHeading.textContent = 'Course assignments';

        if (assignmentsByBranch.size === 0) {
            const empty = document.createElement('span');
            empty.textContent = 'No course assignments selected';
            assignmentsItem.append(assignmentsHeading, empty);
        } else {
            const list = document.createElement('div');
            list.className = 'review-assignment-list';

            Array.from(assignmentsByBranch.entries()).forEach(([branchName, courses]) => {
                const group = document.createElement('div');
                group.className = 'review-branch-group';

                const title = document.createElement('div');
                title.className = 'review-branch-title';
                title.innerHTML = '<i class="fas fa-code-branch" aria-hidden="true"></i>';
                const titleText = document.createElement('span');
                titleText.textContent = branchName;
                title.appendChild(titleText);
                group.appendChild(title);

                courses
                    .sort((a, b) => a.courseName.localeCompare(b.courseName))
                    .forEach(assignment => {
                        const row = document.createElement('div');
                        row.className = 'review-course-row';

                        const courseName = document.createElement('span');
                        courseName.className = 'review-course-name';
                        courseName.textContent = assignment.courseName;

                        const teacher = document.createElement('span');
                        teacher.className = 'review-teacher';
                        const teacherLabel = document.createElement('strong');
                        teacherLabel.textContent = 'TEACHER';
                        teacher.append(teacherLabel, document.createTextNode(assignment.teacher));

                        row.append(courseName, teacher);
                        group.appendChild(row);
                    });

                list.appendChild(group);
            });

            assignmentsItem.append(assignmentsHeading, list);
        }

        container.appendChild(assignmentsItem);
    }

    function updateSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        const titleField = document.querySelector('input[name="lesson_title"]');
        const weightField = document.querySelector('input[name="lesson_weight"]');
        const hasBranches = selectedBranches.size > 0;
        const hasCourses = selectedCourses.size > 0;
        const hasTitle = titleField && titleField.value.trim().length > 0;
        const hasWeight = weightField && parseFloat(weightField.value) > 0;
        const hasAcademicYear = Boolean(document.getElementById('academic-year')?.value);
        const termCount = document.querySelectorAll('input[name="terms[]"]:checked').length;
        
        submitBtn.disabled = !(hasTitle && hasWeight && hasBranches && hasCourses && hasAcademicYear && termCount >= 1 && termCount <= 2);
    }

    // ── Form Submission Handler ──────────────────────────────────
    function prepareFormSubmission(event) {
        if (currentStep !== 4) {
            event.preventDefault();
            if (validateCurrentStep()) {
                changeStep(1);
            }
            return false;
        }

        const courseCheckboxes = document.querySelectorAll('.course-checkbox:checked');
        const branchCheckboxes = document.querySelectorAll('.branch-checkbox:checked');
        const selectedTerms = document.querySelectorAll('input[name="terms[]"]:checked').length;
        const academicYear = document.getElementById('academic-year');

        const basicFields = document.querySelectorAll('[data-step-panel="1"] input[required], [data-step-panel="1"] input[type="number"]');
        const invalidBasicField = Array.from(basicFields).find(field => !field.checkValidity());
        if (invalidBasicField) {
            event.preventDefault();
            currentStep = 1;
            syncSteps();
            invalidBasicField.reportValidity();
            return false;
        }

        if (branchCheckboxes.length === 0 || courseCheckboxes.length === 0) {
            event.preventDefault();
            currentStep = 2;
            syncSteps();
            showWizardError('Select at least one branch and one course-branch combination.');
            return false;
        }

        if (!academicYear.checkValidity() || selectedTerms === 0 || selectedTerms > 2) {
            event.preventDefault();
            currentStep = 3;
            syncSteps();
            if (!academicYear.checkValidity()) {
                academicYear.reportValidity();
            } else {
                showWizardError('Choose at least one term and no more than two terms for this lesson.');
            }
            return false;
        }
        
        // Convert course_branch_pairs into selected_courses array format with teacher_id
        const selectedCoursesContainer = document.createElement('div');
        
        courseCheckboxes.forEach((checkbox, index) => {
            const [courseId, branchId] = checkbox.value.split('_');
            const pairKey = `${courseId}_${branchId}`;
            const teacherId = selectedTeachers[pairKey] || null;
            
            const courseInput = document.createElement('input');
            courseInput.type = 'hidden';
            courseInput.name = `selected_courses[${index}][course_id]`;
            courseInput.value = courseId;
            selectedCoursesContainer.appendChild(courseInput);
            
            const branchInput = document.createElement('input');
            branchInput.type = 'hidden';
            branchInput.name = `selected_courses[${index}][branch_id]`;
            branchInput.value = branchId;
            selectedCoursesContainer.appendChild(branchInput);
            
            if (teacherId) {
                const teacherInput = document.createElement('input');
                teacherInput.type = 'hidden';
                teacherInput.name = `selected_courses[${index}][teacher_id]`;
                teacherInput.value = teacherId;
                selectedCoursesContainer.appendChild(teacherInput);
            }
        });
        
        // Append to form
        const form = document.getElementById('multiBranchForm');
        form.appendChild(selectedCoursesContainer);
        clearLessonDraft();
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Creating lesson…';
        
        return true;
    }

    // ── Init ──────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('multiBranchForm');
        form.noValidate = true;
        restoreLessonDraft();
        syncSteps();
        updateBranchSelection();
        updateCourseSelection();
        updateTermSelection();
        
        // Add form submission handler
        if (form) {
            form.addEventListener('submit', prepareFormSubmission);
        }
        
        // Listen to all input changes to update button state
        const titleInput = document.querySelector('input[name="lesson_title"]');
        const weightInput = document.querySelector('input[name="lesson_weight"]');
        if (titleInput) titleInput.addEventListener('input', function() {
            updateSubmitButton();
            saveLessonDraft();
        });
        if (weightInput) weightInput.addEventListener('input', function() {
            updateSubmitButton();
            saveLessonDraft();
        });
        document.querySelectorAll('input[name="max_test_marks"], input[name="max_exam_marks"]').forEach(input => {
            input.addEventListener('input', function() {
                updateSubmitButton();
                saveLessonDraft();
            });
        });
        document.getElementById('academic-year').addEventListener('change', function() {
            updateSubmitButton();
            saveLessonDraft();
        });
        window.addEventListener('pagehide', saveLessonDraft);
    });

</script>

@endsection

