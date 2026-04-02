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
        margin-bottom: 40px;
        position: relative;
    }

    .progress-bar::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
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
            padding: 16px;
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
    }
</style>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Create Multi-Branch Lesson</h1>
        <p class="page-desc">Set up a lesson that spans multiple branches with proper assignments</p>
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
            <div class="progress-bar">
                <div class="progress-step" id="ps1">
                    <div class="ps-dot" id="pd1">1</div>
                    <span class="ps-label">Select branches</span>
                </div>
                <div class="progress-step" id="ps2">
                    <div class="ps-dot" id="pd2">2</div>
                    <span class="ps-label">Assign courses</span>
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
                        <span>{{ $branches->count() }} available@if($branches->count() > 0) - {{ $branches->pluck('name')->implode(', ') }}@endif</span>
                    </div>
                    <div class="debug-cell">
                        <strong>Courses</strong>
                        <span>{{ $courses->count() }} available@if($courses->count() > 0) - {{ $courses->take(3)->pluck('course_name')->implode(', ') }}{{ $courses->count() > 3 ? '...' : '' }}@endif</span>
                    </div>
                    <div class="debug-cell">
                        <strong>Terms</strong>
                        <span>{{ $terms->count() }} available@if($terms->count() > 0) - {{ $terms->pluck('name')->implode(', ') }}@endif</span>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Lesson Title <span class="req">*</span></label>
                    <input type="text" name="title" class="f-control" value="{{ old('title') }}" required>
                </div>

                <div class="field-group">
                    <label class="field-label">Description</label>
                    <textarea name="description" class="f-control" rows="4">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Step 1: Branches --}}
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-map-marker-alt fa-xs"></i></div>
                    <div>
                        <h2>Select Branches</h2>
                        <p>Choose which branches this lesson will be available in</p>
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
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-book fa-xs"></i></div>
                    <div>
                        <h2>Assign Courses</h2>
                        <p>Link this lesson to specific courses in each branch</p>
                    </div>
                </div>
                <button type="button" class="fallback-toggle" onclick="toggleCourseFallback()">
                    Advanced course assignment options
                </button>
                <div id="courseFallback" style="display:none;">
                    <div class="fallback-body">
                        @if($courses->count() > 0)
                            @foreach($courses as $course)
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--rule);">
                                    <div>
                                        <strong style="font-size:.88rem;">{{ $course->course_name }}</strong>
                                        <div style="font-size:.82rem;color:var(--ink-2);margin-top:2px;">
                                            {{ $course->course_code }} - {{ $course->description }}
                                        </div>
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

            {{-- Step 3: Academic Details --}}
            <div class="section-block">
                <div class="section-hd">
                    <div class="section-hd-badge"><i class="fas fa-calendar fa-xs"></i></div>
                    <div>
                        <h2>Academic Details</h2>
                        <p>Set academic year and terms for this lesson</p>
                    </div>
                </div>

                <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
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

            {{-- Submit Buttons --}}
            <div class="section-block">
                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <a href="{{ route('staff.lessons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> Create Lesson
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // ── State Management ──────────────────────────────────────────
    let selectedBranches = new Set();
    let currentStep = 1;

    // ── Branch Selection ──────────────────────────────────────────
    function toggleBranch(branchId, branchName) {
        const card = document.querySelector(`[data-branch-id="${branchId}"]`);
        const checkbox = document.getElementById(`fbranch_${branchId}`);
        
        if (selectedBranches.has(branchId)) {
            selectedBranches.delete(branchId);
            card.classList.remove('selected');
            if (checkbox) checkbox.checked = false;
        } else {
            selectedBranches.add(branchId);
            card.classList.add('selected');
            if (checkbox) checkbox.checked = true;
        }
        
        syncSteps();
        updateSubmitButton();
    }

    // ── UI Helpers ────────────────────────────────────────────────
    function toggleFallbackSelection() {
        const el = document.getElementById('fallbackSelection');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function toggleCourseFallback() {
        const el = document.getElementById('courseFallback');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function syncSteps() {
        const steps = document.querySelectorAll('.progress-step');
        const dots = document.querySelectorAll('.ps-dot');
        
        steps.forEach((step, index) => {
            const stepNum = index + 1;
            const dot = step.querySelector('.ps-dot');
            
            if (stepNum < currentStep) {
                dot.classList.add('active');
            } else if (stepNum === currentStep) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function updateSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        const hasBranches = selectedBranches.size > 0;
        
        submitBtn.disabled = !hasBranches;
    }

    // ── Init ──────────────────────────────────────────
    syncSteps();
    updateSubmitButton();

}());
</script>

@endsection

