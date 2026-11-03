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

    <form action="{{ route('dos.lessons.store') }}" method="POST" id="multiBranchForm">
        @csrf
        <input type="hidden" name="is_multi_branch" value="1">
        
        <!-- Store course-teachers mapping for JavaScript -->
        <script>
            window.courseTeachersMap = {!! json_encode($courseTeachersData ?? []) !!};
            window.courseNamesMap = {!! json_encode($courses->pluck('course_name', 'id')->toArray()) !!};
            window.branchNamesMap = {!! json_encode($branches->pluck('name', 'id')->toArray()) !!};
        </script>

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
                        <span>
                            {{ $branches->count() }} available
                            @if($branches->count() > 0)
                                - {{ $branches->pluck('name')->implode(', ') }}
                            @endif
                        </span>
                    </div>
                    <div class="debug-cell">
                        <strong>Courses</strong>
                        <span>
                            {{ $courses->count() }} available
                            @if($courses->count() > 0)
                                - {{ $courses->take(3)->pluck('course_name')->implode(', ') }}{{ $courses->count() > 3 ? '...' : '' }}
                            @endif
                        </span>
                    </div>
                    <div class="debug-cell">
                        <strong>Terms</strong>
                        <span>
                            {{ $terms->count() }} available
                            @if($terms->count() > 0)
                                - {{ $terms->pluck('name')->implode(', ') }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Lesson Title <span class="req">*</span></label>
                    <input type="text" name="lesson_title" class="f-control" value="{{ old('lesson_title') }}" required>
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
                            <label class="branch-card" style="cursor:pointer;">
                                <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                                       class="branch-checkbox" style="display:none;"
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

            {{-- Step 2: Courses --}}
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
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:12px;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:12px;background:var(--bg);">
                                <div style="flex:1;">
                                    <strong style="font-size:.88rem;display:block;">{{ $course->course_name }}</strong>
                                    <div style="font-size:.82rem;color:var(--ink-2);margin-top:4px;">
                                        {{ $course->course_code }}{{ $course->description ? ' - ' . $course->description : '' }}
                                    </div>
                                </div>
                                <div style="flex:1;margin-left:20px;">
                                    <div style="display:flex;flex-direction:column;gap:6px;">
                                        @if($course->branches && $course->branches->count() > 0)
                                            @foreach($course->branches as $b)
                                                <label style="display:flex;align-items:center;gap:6px;font-size:.82rem;cursor:pointer;">
                                                    <input type="checkbox" name="course_branch_pairs[]" 
                                                           value="{{ $course->id }}_{{ $b->id }}"
                                                           class="course-checkbox"
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

            {{-- Step 3: Assign Teachers --}}
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

            {{-- Step 4: Academic Details --}}
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
                                    {{ $ongoingAcademicYear->year }} ({{ ucfirst($ongoingAcademicYear->status) }})
                                </option>
                            @endif
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Lesson Weight <span class="req">*</span></label>
                        <input type="number" name="lesson_weight" class="f-control" value="{{ old('lesson_weight', 1) }}" min="1" max="100" required>
                    </div>
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
    let selectedCourses = new Set();
    let selectedTeachers = {}; // Map: "courseId_branchId" -> teacher_id
    let currentStep = 1;

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
        
        // Update display
        const countEl = document.getElementById('selectedBranchCount');
        if (countEl) countEl.textContent = selectedBranches.size;
        updateSubmitButton();
    }

    // ── Course Selection ──────────────────────────────────────────
    function updateCourseSelection() {
        selectedCourses.clear();
        const checkboxes = document.querySelectorAll('.course-checkbox:checked');
        
        checkboxes.forEach(checkbox => {
            selectedCourses.add(checkbox.value);
        });
        
        const countEl = document.getElementById('selectedCourseCount');
        if (countEl) countEl.textContent = selectedCourses.size;
        
        // Update teacher assignment UI
        renderTeacherAssignments();
        updateSubmitButton();
    }

    // ── Teacher Selection UI ──────────────────────────────────────
    function renderTeacherAssignments() {
        const container = document.getElementById('teacherAssignmentsContainer');
        
        if (selectedCourses.size === 0) {
            container.innerHTML = '<p style="font-size:.82rem;color:var(--ink-2);margin-bottom:16px;padding:12px;background:var(--bg);border-radius:var(--radius-sm);"><i class="fas fa-info-circle" style="margin-right:4px;"></i>Select courses above first, then assign teachers to each branch-course combination.</p>';
            return;
        }
        
        let html = '<div style="display:flex;flex-direction:column;gap:16px;">';
        
        // Group selected courses by branch-course pair
        const courseBranchPairs = Array.from(selectedCourses);
        
        courseBranchPairs.forEach((pair, index) => {
            const [courseId, branchId] = pair.split('_');
            const pairKey = `${courseId}_${branchId}`;
            
            // Get course and branch names
            const courseName = getCourseNameById(courseId);
            const branchName = getBranchNameById(branchId);
            const teachers = window.courseTeachersMap[courseId] || [];
            
            html += `
                <div style="padding:16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);">
                    <div style="margin-bottom:12px;">
                        <strong style="font-size:.88rem;display:block;color:var(--ink);">${courseName}</strong>
                        <span style="font-size:.82rem;color:var(--ink-2);">📍 ${branchName}</span>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Assign Teacher</label>
                        <select class="f-control teacher-select" data-pair="${pairKey}" onchange="updateTeacherSelection(this)">
                            <option value="">-- No teacher --</option>
                            ${teachers.map(teacher => `
                                <option value="${teacher.id}" ${selectedTeachers[pairKey] == teacher.id ? 'selected' : ''}>
                                    ${teacher.teacher_name}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
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
    }

    function getCourseNameById(courseId) {
        return window.courseNamesMap[courseId] || `Course ${courseId}`;
    }

    function getBranchNameById(branchId) {
        return window.branchNamesMap[branchId] || `Branch ${branchId}`;
    }

    // ── UI Helpers ────────────────────────────────────────────────
    function syncSteps() {
        const steps = document.querySelectorAll('.progress-step');
        
        steps.forEach((step, index) => {
            const stepNum = index + 1;
            const dot = step.querySelector('.ps-dot');
            
            if (stepNum <= currentStep) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function updateSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        const titleField = document.querySelector('input[name="lesson_title"]');
        const weightField = document.querySelector('input[name="lesson_weight"]');
        const hasBranches = selectedBranches.size > 0;
        const hasCourses = selectedCourses.size > 0;
        const hasTitle = titleField && titleField.value.trim().length > 0;
        const hasWeight = weightField && parseFloat(weightField.value) > 0;
        
        // All fields must be selected to enable submit
        submitBtn.disabled = !(hasTitle && hasWeight && hasBranches && hasCourses);
    }

    // ── Form Submission Handler ──────────────────────────────────
    function prepareFormSubmission(event) {
        const titleField = document.querySelector('input[name="lesson_title"]');
        const weightField = document.querySelector('input[name="lesson_weight"]');
        const courseCheckboxes = document.querySelectorAll('.course-checkbox:checked');
        const branchCheckboxes = document.querySelectorAll('.branch-checkbox:checked');
        
        let errors = [];
        
        if (!titleField || !titleField.value.trim()) {
            errors.push('Please enter a lesson title');
        }
        
        if (!weightField || parseFloat(weightField.value) <= 0) {
            errors.push('Please enter a valid lesson weight');
        }
        
        if (branchCheckboxes.length === 0) {
            errors.push('Please select at least one branch');
        }
        
        if (courseCheckboxes.length === 0) {
            errors.push('Please select at least one course-branch combination');
        }
        
        if (errors.length > 0) {
            event.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
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
        
        return true;
    }

    // ── Init ──────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        syncSteps();
        updateBranchSelection();
        updateCourseSelection();
        
        // Add form submission handler
        const form = document.getElementById('multiBranchForm');
        if (form) {
            form.addEventListener('submit', prepareFormSubmission);
        }
        
        // Listen to all input changes to update button state
        const titleInput = document.querySelector('input[name="lesson_title"]');
        const weightInput = document.querySelector('input[name="lesson_weight"]');
        if (titleInput) titleInput.addEventListener('input', updateSubmitButton);
        if (weightInput) weightInput.addEventListener('input', updateSubmitButton);
    });

</script>

@endsection

