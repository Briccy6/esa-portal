@extends('dos.layout')

@section('content')
<style>
    :root {
        --ink:        #111318;
        --ink-2:      #3d404a;
        --ink-3:      #6b6f7a;
        --ink-4:      #9ea2ad;
        --rule:       #e3e5ea;
        --rule-2:     #c8cad0;
        --surface:    #ffffff;
        --surface-2:  #f5f6f8;
        --surface-3:  #eceef2;
        --accent:     #2563eb;
        --accent-2:   #1d4ed8;
        --accent-bg:  #eff4ff;
        --accent-rule:#c7d7fd;
        --danger:     #dc2626;
        --danger-bg:  #fef2f2;
        --success:    #16a34a;
        --success-bg: #f0fdf4;
        --radius:     10px;
        --radius-lg:  16px;
        --shadow-lg:  0 12px 32px rgba(0,0,0,.10), 0 2px 8px rgba(0,0,0,.06);
    }

    .page-wrap { background: var(--surface-2); min-height: 100vh; padding: 36px 0 60px; }
    .page-inner { max-width: 960px; margin: 0 auto; padding: 0 20px; }

    .page-hd { display: flex; align-items: center; gap: 16px; margin-bottom: 28px; }
    .page-hd-icon {
        width: 48px; height: 48px; border-radius: var(--radius);
        background: var(--accent); display: flex; align-items: center;
        justify-content: center; color: #fff; font-size: 20px; flex-shrink: 0;
    }
    .page-hd-text h1 { font-size: 1.5rem; font-weight: 700; color: var(--ink); margin: 0 0 2px; letter-spacing: -.02em; }
    .page-hd-text p  { margin: 0; color: var(--ink-3); font-size: .9rem; }

    .form-card { background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: visible; }

    .section-block { padding: 28px 32px; border-bottom: 1px solid var(--rule); }
    .section-block:last-child { border-bottom: none; }

    .section-hd { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; }
    .section-hd-badge {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--accent); color: #fff;
        font-size: .78rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .section-hd-badge.done  { background: var(--success); }
    .section-hd-badge.muted { background: var(--rule-2); color: var(--ink-3); }
    .section-hd h2 { font-size: 1rem; font-weight: 650; color: var(--ink); margin: 0; letter-spacing: -.01em; }
    .section-hd p  { font-size: .82rem; color: var(--ink-3); margin: 0; }

    .progress-track {
        display: flex; align-items: center;
        padding: 20px 32px; border-bottom: 1px solid var(--rule);
        background: var(--surface-2);
    }
    .progress-step { display: flex; align-items: center; gap: 8px; flex: 1; position: relative; }
    .progress-step:not(:last-child)::after {
        content: ''; position: absolute;
        left: calc(50% + 20px); right: calc(-50% + 20px);
        top: 13px; height: 2px; background: var(--rule); transition: background .3s;
    }
    .progress-step.done:not(:last-child)::after { background: var(--success); }
    .ps-dot {
        width: 28px; height: 28px; border-radius: 50%;
        border: 2px solid var(--rule-2); background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        font-size: .78rem; font-weight: 700; color: var(--ink-3);
        transition: all .3s; position: relative; z-index: 1; flex-shrink: 0;
    }
    .progress-step.active .ps-dot { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }
    .progress-step.done   .ps-dot { border-color: var(--success); background: var(--success); color: #fff; }
    .ps-label { font-size: .8rem; font-weight: 600; color: var(--ink-4); white-space: nowrap; }
    .progress-step.active .ps-label { color: var(--accent); }
    .progress-step.done   .ps-label { color: var(--success); }

    .field-group { margin-bottom: 18px; }
    .field-group:last-child { margin-bottom: 0; }
    .field-label { display: block; font-size: .83rem; font-weight: 600; color: var(--ink-2); margin-bottom: 6px; }
    .field-label .req { color: var(--danger); margin-left: 2px; }
    .f-control {
        width: 100%; padding: 9px 12px; font-size: .9rem; color: var(--ink);
        background: var(--surface); border: 1.5px solid var(--rule);
        border-radius: var(--radius); outline: none;
        transition: border-color .2s, box-shadow .2s;
        appearance: none; -webkit-appearance: none;
        box-sizing: border-box;
    }
    .f-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .f-control::placeholder { color: var(--ink-4); }

    .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .row-2, .row-3 { grid-template-columns: 1fr; } }

    .branch-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
    .branch-card {
        border: 1.5px solid var(--rule); border-radius: var(--radius);
        padding: 16px; background: var(--surface); cursor: pointer;
        transition: border-color .2s, box-shadow .2s, background .2s;
        text-align: center; user-select: none;
    }
    .branch-card:hover { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,99,235,.07); }
    .branch-card.selected { border-color: var(--accent); background: var(--accent-bg); }
    .branch-card-icon {
        width: 40px; height: 40px; border-radius: 50%;
        background: var(--surface-3); margin: 0 auto 10px;
        display: flex; align-items: center; justify-content: center;
        color: var(--ink-3); font-size: 16px; transition: background .2s, color .2s;
    }
    .branch-card.selected .branch-card-icon { background: var(--accent); color: #fff; }
    .branch-card-name { font-size: .88rem; font-weight: 600; color: var(--ink); }
    .branch-card.selected .branch-card-name { color: var(--accent-2); }

    .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
    .course-card {
        border: 1.5px solid var(--rule); border-radius: var(--radius);
        padding: 14px 16px; background: var(--surface);
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .course-card.selected { border-color: var(--accent); background: var(--accent-bg); }
    .course-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
    .course-card-name { font-size: .88rem; font-weight: 600; color: var(--ink); line-height: 1.3; }
    .course-card-abbr {
        font-size: .72rem; font-weight: 700; color: var(--accent-2);
        background: var(--accent-bg); border: 1px solid var(--accent-rule);
        padding: 2px 7px; border-radius: 20px; white-space: nowrap;
        flex-shrink: 0; margin-left: 8px;
    }
    .course-card-branch { font-size: .78rem; color: var(--ink-3); margin-bottom: 12px; }
    .btn-toggle {
        font-size: .8rem; font-weight: 600; padding: 5px 12px;
        border-radius: var(--radius); border: 1.5px solid; cursor: pointer;
        transition: all .15s; display: inline-flex; align-items: center; gap: 5px;
        background: transparent;
    }
    .btn-toggle.add  { border-color: var(--accent); color: var(--accent); }
    .btn-toggle.add:hover  { background: var(--accent); color: #fff; }
    .btn-toggle.remove { border-color: var(--danger); color: var(--danger); }
    .btn-toggle.remove:hover { background: var(--danger-bg); }

    .assignment-list { display: flex; flex-direction: column; gap: 10px; }
    .assignment-row {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 14px; border: 1.5px solid var(--rule);
        border-radius: var(--radius); background: var(--surface); transition: border-color .2s;
    }
    .assignment-row:hover { border-color: var(--rule-2); }
    .ar-info { flex: 1; min-width: 0; }
    .ar-name { font-size: .88rem; font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ar-branch { font-size: .78rem; color: var(--ink-3); margin-top: 1px; }
    .ar-select { flex: 0 0 220px; }
    .ar-remove {
        width: 30px; height: 30px; border: none; background: none; cursor: pointer;
        color: var(--ink-4); border-radius: 6px; display: flex;
        align-items: center; justify-content: center;
        transition: background .15s, color .15s; flex-shrink: 0;
    }
    .ar-remove:hover { background: var(--danger-bg); color: var(--danger); }

    .empty-state { text-align: center; padding: 36px 20px; color: var(--ink-4); }
    .empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; }
    .empty-state p { margin: 0; font-size: .88rem; }

    .debug-bar { display: flex; border: 1px solid var(--rule); border-radius: var(--radius); overflow: hidden; margin-bottom: 24px; font-size: .8rem; }
    .debug-cell { flex: 1; padding: 10px 14px; border-right: 1px solid var(--rule); background: var(--surface-2); }
    .debug-cell:last-child { border-right: none; }
    .debug-cell strong { display: block; color: var(--ink-2); margin-bottom: 2px; }
    .debug-cell span { color: var(--ink-3); }

    .term-checks { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
    .term-check-pill {
        display: flex; align-items: center; gap: 6px; padding: 6px 12px;
        border: 1.5px solid var(--rule); border-radius: 20px; cursor: pointer;
        transition: border-color .15s, background .15s; font-size: .83rem;
        color: var(--ink-2); user-select: none;
    }
    .term-check-pill:has(input:checked) { border-color: var(--accent); background: var(--accent-bg); color: var(--accent-2); }
    .term-check-pill input { width: 14px; height: 14px; accent-color: var(--accent); cursor: pointer; }

    .alert-box {
        border-radius: var(--radius); padding: 12px 16px; font-size: .88rem;
        margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;
    }
    .alert-box.success { background: var(--success-bg); color: #15803d; border: 1px solid #bbf7d0; }
    .alert-box.danger  { background: var(--danger-bg);  color: #991b1b; border: 1px solid #fecaca; }
    .alert-box i { flex-shrink: 0; margin-top: 1px; }
    .alert-box ul { margin: 6px 0 0; padding-left: 16px; }

    .btn-row {
        display: flex; align-items: center; justify-content: flex-end; gap: 10px;
        padding: 24px 32px; border-top: 1px solid var(--rule);
        background: var(--surface-2); border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }
    .btn-ghost {
        padding: 9px 20px; border-radius: var(--radius); border: 1.5px solid var(--rule);
        background: var(--surface); color: var(--ink-2); font-size: .88rem; font-weight: 600;
        cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
        gap: 6px; transition: border-color .15s, color .15s;
    }
    .btn-ghost:hover { border-color: var(--rule-2); color: var(--ink); }
    .btn-primary-solid {
        padding: 9px 24px; border-radius: var(--radius); border: none;
        background: var(--accent); color: #fff; font-size: .88rem; font-weight: 700;
        cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
        transition: background .15s, opacity .15s; letter-spacing: -.01em;
    }
    .btn-primary-solid:hover:not(:disabled) { background: var(--accent-2); }
    .btn-primary-solid:disabled { opacity: .45; cursor: not-allowed; }

    .fallback-toggle {
        font-size: .8rem; color: var(--ink-4); background: none; border: none;
        padding: 0; cursor: pointer; text-decoration: underline;
        margin-top: 12px; display: inline-block;
    }
    .fallback-toggle:hover { color: var(--ink-3); }
    .fallback-body {
        margin-top: 14px; padding: 16px; background: var(--surface-2);
        border: 1px solid var(--rule); border-radius: var(--radius);
    }
</style>

<div class="page-wrap">
    <div class="page-inner">

        <div class="page-hd">
            <div class="page-hd-icon"><i class="fas fa-book-open"></i></div>
            <div class="page-hd-text">
                <h1>Create Lesson</h1>
                <p>Assign a lesson to one or more branches and courses in three steps</p>
            </div>
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

        <form action="{{ route('staff.lessons.store') }}" method="POST" id="multiBranchForm">
            @csrf
            <input type="hidden" name="is_multi_branch" value="1">

            <div class="form-card">

                {{-- Progress bar --}}
                <div class="progress-track">
                    <div class="progress-step active" id="ps1">
                        <div class="ps-dot" id="pd1">1</div>
                        <span class="ps-label">Select branches</span>
                    </div>
                    <div class="progress-step" id="ps2">
                        <div class="ps-dot" id="pd2">2</div>
                        <span class="ps-label">Choose courses</span>
                    </div>
                    <div class="progress-step" id="ps3">
                        <div class="ps-dot" id="pd3">3</div>
                        <span class="ps-label">Assign teachers</span>
                    </div>
                </div>

                {{-- Basic Info --}}
                <div class="section-block">
                    <div class="section-hd">
                        <div class="section-hd-badge"><i class="fas fa-info fa-xs"></i></div>
                        <div>
                            <h2>Basic Information</h2>
                            <p>Lesson title and core details</p>
                        </div>
                    </div>

                    <div class="debug-bar">
                        <div class="debug-cell">
                            <strong>Branches</strong>
                            <span>{{ $branches->count() }} available@if($branches->count() > 0) — {{ $branches->pluck('name')->implode(', ') }}@endif</span>
                        </div>
                        <div class="debug-cell">
                            <strong>Courses</strong>
                            <span>{{ $courses->count() }} available@if($courses->count() > 0) — {{ $courses->take(3)->pluck('course_name')->implode(', ') }}{{ $courses->count() > 3 ? '…' : '' }}@endif</span>
                        </div>
                        <div class="debug-cell">
                            <strong>Terms</strong>
                            <span>{{ $terms->count() }} available@if($terms->count() > 0) — {{ $terms->pluck('name')->implode(', ') }}@endif</span>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Lesson Title <span class="req">*</span></label>
                        <input type="text" name="lesson_title" class="f-control"
                               value="{{ old('lesson_title') }}" required
                               placeholder="e.g. Introduction to Organic Chemistry">
                    </div>
                </div>

                {{-- Step 1: Branches --}}
                <div class="section-block">
                    <div class="section-hd">
                        <div class="section-hd-badge muted" id="s1badge">1</div>
                        <div>
                            <h2>Step 1 — Select Branches</h2>
                            <p>Choose one or more branches this lesson will be active in</p>
                        </div>
                    </div>

                    @if($branches->count() > 0)
                        <div class="branch-grid" id="branchGrid">
                            @foreach($branches as $branch)
                                <div class="branch-card" data-branch-id="{{ $branch->id }}"
                                     onclick="toggleBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}')">
                                    <div class="branch-card-icon"><i class="fas fa-building"></i></div>
                                    <div class="branch-card-name">{{ $branch->name }}</div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="fallback-toggle" onclick="toggleFallbackSelection()">
                            Having trouble? Use simple checkboxes instead
                        </button>
                        <div id="fallbackSelection" style="display:none;">
                            <div class="fallback-body">
                                <p style="font-size:.82rem;color:var(--ink-3);margin:0 0 10px;">
                                    <i class="fas fa-info-circle" style="margin-right:4px;"></i>Check the branches to include:
                                </p>
                                @foreach($branches as $branch)
                                    <div style="margin-bottom:6px;">
                                        <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                                               id="fbranch_{{ $branch->id }}">
                                        <label for="fbranch_{{ $branch->id }}" style="font-size:.88rem;margin-left:6px;">{{ $branch->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>No branches found. Please create branches first.</p>
                        </div>
                    @endif
                </div>

                {{-- Step 2: Courses --}}
                <div class="section-block" id="courseSection" style="display:none;">
                    <div class="section-hd">
                        <div class="section-hd-badge muted" id="s2badge">2</div>
                        <div>
                            <h2>Step 2 — Choose Courses</h2>
                            <p>Select which courses in the chosen branches will include this lesson</p>
                        </div>
                    </div>
                    <div id="courseContent">
                        <div class="empty-state">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            <p>Loading courses for selected branches…</p>
                        </div>
                    </div>
                    <button type="button" class="fallback-toggle" onclick="toggleCourseFallback()">
                        Not seeing your course? Show all courses
                    </button>
                    <div id="courseFallback" style="display:none;">
                        <div class="fallback-body">
                            @if($courses->count() > 0)
                                @foreach($courses as $course)
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--rule);">
                                        <div>
                                            <strong style="font-size:.88rem;">{{ $course->course_name }}</strong>
                                            <span style="font-size:.78rem;color:var(--ink-3);margin-left:6px;">({{ $course->abbreviation }})</span>
                                            <br><small style="color:var(--ink-3);">{{ $course->branches->pluck('name')->implode(', ') }}</small>
                                        </div>
                                        <select class="f-control" name="course_assignments[]" style="width:180px;font-size:.82rem;">
                                            <option value="">Select branch</option>
                                            @foreach($course->branches as $b)
                                                <option value="{{ $course->id }}_{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            @else
                                <p style="font-size:.82rem;color:var(--ink-3);margin:0;">No courses available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Step 3: Teacher Assignment --}}
                <div class="section-block" id="assignmentSection" style="display:none;">
                    <div class="section-hd">
                        <div class="section-hd-badge muted" id="s3badge">3</div>
                        <div>
                            <h2>Step 3 — Assign Teachers &amp; Review</h2>
                            <p>Optionally assign a teacher to each selected course</p>
                        </div>
                    </div>
                    <div class="assignment-list" id="assignmentList">
                        <div class="empty-state">
                            <i class="fas fa-list-check"></i>
                            <p>No courses selected yet</p>
                        </div>
                    </div>
                </div>

                {{-- Academic Settings --}}
                <div class="section-block">
                    <div class="section-hd">
                        <div class="section-hd-badge"><i class="fas fa-calendar fa-xs"></i></div>
                        <div>
                            <h2>Academic Settings</h2>
                            <p>Academic year and term association</p>
                        </div>
                    </div>
                    <div class="row-2">
                        <div class="field-group">
                            <label class="field-label">Academic Year <span class="req">*</span></label>
                            <select name="academic_year_id" class="f-control" required>
                                <option value="">Select academic year</option>
                                @if($ongoingAcademicYear)
                                    <option value="{{ $ongoingAcademicYear->id }}" selected>
                                        {{ $ongoingAcademicYear->year }} (Current)
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Terms <span class="req">*</span></label>
                            <div class="term-checks">
                                @foreach($terms as $term)
                                    <label class="term-check-pill">
                                        <input type="checkbox" name="terms[]" value="{{ $term->id }}"
                                               {{ in_array($term->id, old('terms', [])) ? 'checked' : '' }}>
                                        {{ $term->name }}
                                    </label>
                                @endforeach
                                @if($terms->count() === 0)
                                    <span style="font-size:.82rem;color:var(--ink-4);">No terms available</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grading Settings --}}
                <div class="section-block">
                    <div class="section-hd">
                        <div class="section-hd-badge"><i class="fas fa-chart-bar fa-xs"></i></div>
                        <div>
                            <h2>Grading Settings</h2>
                            <p>Define marks allocation for this lesson</p>
                        </div>
                    </div>
                    <div class="row-3">
                        <div class="field-group">
                            <label class="field-label">Max Test Marks</label>
                            <input type="number" name="max_test_marks" class="f-control"
                                   value="{{ old('max_test_marks') }}" min="0" placeholder="e.g. 30">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Max Exam Marks</label>
                            <input type="number" name="max_exam_marks" class="f-control"
                                   value="{{ old('max_exam_marks') }}" min="0" placeholder="e.g. 70">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Lesson Weight (%) <span class="req">*</span></label>
                            <input type="number" name="lesson_weight" class="f-control"
                                   value="{{ old('lesson_weight', 100) }}" min="1" max="100" required placeholder="100">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="btn-row">
                    <a href="{{ route('dos.lessons.index') }}" class="btn-ghost">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn-primary-solid" id="submitBtn" disabled>
                        <i class="fas fa-plus-circle"></i>
                        <span id="submitLabel">Create Lesson</span>
                    </button>
                </div>

            </div>{{-- /form-card --}}
        </form>

    </div>{{-- /page-inner --}}
</div>{{-- /page-wrap --}}

<script>
(function () {
    'use strict';

    // ── State ────────────────────────────────────────
    var selectedBranches = {};   // { branchId: true }
    var selectedCourses  = {};   // { "courseId_branchId": courseObj }
    var allLoadedCourses = [];

    // ── DOM refs ─────────────────────────────────────
    var courseSection     = document.getElementById('courseSection');
    var courseContent     = document.getElementById('courseContent');
    var assignmentSection = document.getElementById('assignmentSection');
    var assignmentList    = document.getElementById('assignmentList');
    var submitBtn         = document.getElementById('submitBtn');
    var submitLabel       = document.getElementById('submitLabel');

    // ── Helpers ───────────────────────────────────────
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function branchCount() {
        return Object.keys(selectedBranches).length;
    }

    function courseCount() {
        return Object.keys(selectedCourses).length;
    }

    // ── Branch toggle ─────────────────────────────────
    window.toggleBranch = function (branchId, branchName) {
        var card = document.querySelector('.branch-card[data-branch-id="' + branchId + '"]');
        if (!card) return;

        if (selectedBranches[branchId]) {
            delete selectedBranches[branchId];
            card.classList.remove('selected');
            // Remove courses belonging to this branch
            Object.keys(selectedCourses).forEach(function (key) {
                if (key.indexOf('_' + branchId) !== -1) {
                    delete selectedCourses[key];
                }
            });
        } else {
            selectedBranches[branchId] = true;
            card.classList.add('selected');
        }

        syncSteps();
        loadCoursesForBranches();
    };

    // ── Fetch courses ─────────────────────────────────
    function loadCoursesForBranches() {
        var branchIds = Object.keys(selectedBranches).map(Number);

        if (branchIds.length === 0) {
            courseSection.style.display = 'none';
            allLoadedCourses = [];
            updateAssignmentSection();
            updateSubmitButton();
            return;
        }

        courseSection.style.display = 'block';
        courseContent.innerHTML = '<div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i><p>Loading courses\u2026</p></div>';

        var csrfMeta  = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        fetch('/staff/lessons/get-courses-for-branches', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ branch_ids: branchIds })
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            allLoadedCourses = data.courses || [];
            renderCourses(allLoadedCourses);
            syncSteps();
        })
        .catch(function (err) {
            console.error('Error loading courses:', err);
            courseContent.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i><p>Failed to load courses. Please use the fallback below.</p></div>';
        });
    }

    // ── Render course cards ───────────────────────────
    function renderCourses(courses) {
        if (!courses || courses.length === 0) {
            courseContent.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>No courses found for the selected branches.</p></div>';
            return;
        }

        var html = '<div class="course-grid">';
        for (var i = 0; i < courses.length; i++) {
            var c    = courses[i];
            var cid  = String(c.id);
            var bid  = String(c.branch_id);
            var key  = cid + '_' + bid;
            var isSel = !!selectedCourses[key];
            var abbr  = c.abbreviation || '';

            // Build onclick safely – avoid template literals to prevent truncation issues
            var onclickAttr = 'handleToggleCourse(this)';

            html += '<div class="course-card' + (isSel ? ' selected' : '') + '" id="cc_' + key + '"'
                  + ' data-course-id="' + cid + '"'
                  + ' data-branch-id="' + bid + '"'
                  + ' data-course-name="' + escHtml(c.course_name) + '"'
                  + ' data-branch-name="' + escHtml(c.branch_name || '') + '"'
                  + ' data-abbr="' + escHtml(abbr) + '">'
                  + '<div class="course-card-top">'
                  + '<div class="course-card-name">' + escHtml(c.course_name) + '</div>'
                  + (abbr ? '<div class="course-card-abbr">' + escHtml(abbr) + '</div>' : '')
                  + '</div>'
                  + '<div class="course-card-branch"><i class="fas fa-map-marker-alt" style="font-size:.7rem;margin-right:4px;"></i>' + escHtml(c.branch_name || '') + '</div>'
                  + '<button type="button" class="btn-toggle ' + (isSel ? 'remove' : 'add') + '" onclick="' + onclickAttr + '">'
                  + (isSel ? '<i class="fas fa-times"></i> Remove' : '<i class="fas fa-plus"></i> Select')
                  + '</button>'
                  + '</div>';
        }
        html += '</div>';
        courseContent.innerHTML = html;
    }

    // ── Course toggle via data attributes (avoids inline JSON issues) ─
    window.handleToggleCourse = function (btn) {
        var card       = btn.closest('.course-card');
        if (!card) return;
        var courseId   = card.getAttribute('data-course-id');
        var branchId   = card.getAttribute('data-branch-id');
        var courseName = card.getAttribute('data-course-name');
        var branchName = card.getAttribute('data-branch-name');
        var abbr       = card.getAttribute('data-abbr');
        toggleCourse(courseId, branchId, courseName, branchName, abbr);
    };

    // ── Toggle course state ───────────────────────────
    window.toggleCourse = function (courseId, branchId, courseName, branchName, abbr) {
        var key = String(courseId) + '_' + String(branchId);

        if (selectedCourses[key]) {
            delete selectedCourses[key];
        } else {
            selectedCourses[key] = {
                course_id:    courseId,
                branch_id:    branchId,
                course_name:  courseName,
                branch_name:  branchName,
                abbreviation: abbr || '',
                teacher_id:   null
            };
        }

        renderCourses(allLoadedCourses);
        updateAssignmentSection();
        syncSteps();
        updateSubmitButton();
    };

    // ── Assignment section ────────────────────────────
    function updateAssignmentSection() {
        if (courseCount() === 0) {
            assignmentSection.style.display = 'none';
            return;
        }

        assignmentSection.style.display = 'block';
        var html = '';

        Object.keys(selectedCourses).forEach(function (key) {
            var course = selectedCourses[key];
            var selId  = 'tsel_' + key;
            html += '<div class="assignment-row">'
                  + '<div class="ar-info">'
                  + '<div class="ar-name">' + escHtml(course.course_name) + '</div>'
                  + '<div class="ar-branch">' + escHtml(course.branch_name) + '</div>'
                  + '</div>'
                  + '<div class="ar-select">'
                  + '<select class="f-control" id="' + selId + '" style="font-size:.82rem;" onchange="updateTeacherAssignment(\'' + key + '\', this.value)">'
                  + '<option value="">No teacher assigned</option>'
                  + '</select>'
                  + '</div>'
                  + '<button type="button" class="ar-remove" onclick="removeAssignment(\'' + key + '\')" title="Remove">'
                  + '<i class="fas fa-times"></i>'
                  + '</button>'
                  + '</div>';

            loadTeachers(course.course_id, selId, course.teacher_id);
        });

        assignmentList.innerHTML = html;
    }

    // ── Load teachers ─────────────────────────────────
    function loadTeachers(courseId, selectId, currentTeacherId) {
        fetch('/staff/lessons/get-teachers/' + courseId)
            .then(function (r) { return r.json(); })
            .then(function (teachers) {
                var sel = document.getElementById(selectId);
                if (!sel) return;
                sel.innerHTML = '<option value="">No teacher assigned</option>';
                teachers.forEach(function (t) {
                    var opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.teacher_name;
                    if (currentTeacherId && String(t.id) === String(currentTeacherId)) {
                        opt.selected = true;
                    }
                    sel.appendChild(opt);
                });
            })
            .catch(function (err) { console.error('Error loading teachers:', err); });
    }

    // ── Teacher assignment update ─────────────────────
    window.updateTeacherAssignment = function (key, teacherId) {
        if (selectedCourses[key]) {
            selectedCourses[key].teacher_id = teacherId ? parseInt(teacherId, 10) : null;
        }
    };

    // ── Remove assignment ─────────────────────────────
    window.removeAssignment = function (key) {
        if (!selectedCourses[key]) return;
        var c = selectedCourses[key];
        toggleCourse(c.course_id, c.branch_id, c.course_name, c.branch_name, c.abbreviation);
    };

    // ── Step progress sync ────────────────────────────
    function syncSteps() {
        var ps1 = document.getElementById('ps1');
        var ps2 = document.getElementById('ps2');
        var ps3 = document.getElementById('ps3');
        var b1  = document.getElementById('s1badge');
        var b2  = document.getElementById('s2badge');
        var b3  = document.getElementById('s3badge');
        var pd1 = document.getElementById('pd1');
        var pd2 = document.getElementById('pd2');
        var pd3 = document.getElementById('pd3');

        // Step 1
        if (branchCount() > 0) {
            ps1.className = 'progress-step done';
            pd1.innerHTML = '<i class="fas fa-check" style="font-size:.7rem;"></i>';
            b1.className  = 'section-hd-badge done';
            b1.innerHTML  = '<i class="fas fa-check fa-xs"></i>';
        } else {
            ps1.className = 'progress-step active';
            pd1.textContent = '1';
            b1.className  = 'section-hd-badge muted';
            b1.textContent = '1';
        }

        // Step 2
        if (courseCount() > 0) {
            ps2.className = 'progress-step done';
            pd2.innerHTML = '<i class="fas fa-check" style="font-size:.7rem;"></i>';
            b2.className  = 'section-hd-badge done';
            b2.innerHTML  = '<i class="fas fa-check fa-xs"></i>';
        } else if (branchCount() > 0) {
            ps2.className = 'progress-step active';
            pd2.textContent = '2';
            b2.className  = 'section-hd-badge';
            b2.textContent = '2';
        } else {
            ps2.className = 'progress-step';
            pd2.textContent = '2';
            b2.className  = 'section-hd-badge muted';
            b2.textContent = '2';
        }

        // Step 3
        if (courseCount() > 0) {
            ps3.className = 'progress-step active';
            pd3.textContent = '3';
            b3.className  = 'section-hd-badge';
            b3.textContent = '3';
        } else {
            ps3.className = 'progress-step';
            pd3.textContent = '3';
            b3.className  = 'section-hd-badge muted';
            b3.textContent = '3';
        }
    }

    // ── Submit button ─────────────────────────────────
    function updateSubmitButton() {
        var count = courseCount();
        submitBtn.disabled = count === 0;
        submitLabel.textContent = count > 0
            ? 'Create Lesson (' + count + ' course' + (count > 1 ? 's' : '') + ')'
            : 'Create Lesson';
    }

    // ── Form submit ───────────────────────────────────
    document.getElementById('multiBranchForm').addEventListener('submit', function (e) {
        if (courseCount() === 0) {
            e.preventDefault();
            alert('Please select at least one course before creating the lesson.');
            return;
        }

        // Remove any previously injected fields
        var old = document.querySelectorAll('.injected-course-field');
        for (var x = 0; x < old.length; x++) { old[x].remove(); }

        // Inject properly-named hidden inputs for each selected course
        var form = document.getElementById('multiBranchForm');
        var idx  = 0;
        Object.keys(selectedCourses).forEach(function (key) {
            var c = selectedCourses[key];
            ['course_id', 'branch_id', 'teacher_id'].forEach(function (field) {
                var inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'selected_courses[' + idx + '][' + field + ']';
                inp.value = c[field] !== null && c[field] !== undefined ? c[field] : '';
                inp.classList.add('injected-course-field');
                form.appendChild(inp);
            });
            idx++;
        });

        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating\u2026';
    });

    // ── Fallback toggles ──────────────────────────────
    window.toggleFallbackSelection = function () {
        var el = document.getElementById('fallbackSelection');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    };
    window.toggleCourseFallback = function () {
        var el = document.getElementById('courseFallback');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    };

    // ── Init ──────────────────────────────────────────
    syncSteps();
    updateSubmitButton();

}());
</script>
@endsection