@extends('staff.partials.sidebar_dos')

@section('content')
<style>
    .edit-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .edit-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .edit-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .edit-body { padding: 40px; }
    .section-title {
        color: #667eea;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 25px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #667eea;
    }
    .form-label { font-weight: 600; color: #333; }
    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 14px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .required::after { content: ' *'; color: #dc3545; }
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 13px 40px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
    }
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 13px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
    .current-info {
        background: #e8f4fd;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 0 8px 8px 0;
    }
    .btn-remove-unit {
        background: #dc3545;
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        float: right;
        transition: all 0.3s ease;
    }
    .btn-remove-unit:hover {
        background: #c82333;
        transform: scale(1.1);
    }
    .unit-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
</style>

<div class="edit-container">
    <div class="edit-card">
        <div class="edit-header">
            <h2>Edit Lesson</h2>
            <p class="mb-0">Modify lesson information for: {{ $lesson->title }}</p>
        </div>
        
        <div class="edit-body">
            <!-- Current Lesson Information -->
            <div class="current-info">
                <h5 class="mb-2">Current Lesson Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Course:</strong> {{ $lesson->course->course_name }}</p>
                        <p class="mb-1"><strong>Teacher:</strong> {{ $lesson->teacher ? $lesson->teacher->teacher_name : 'Not assigned' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Test Marks:</strong> {{ $lesson->max_test_marks }}</p>
                        <p class="mb-0"><strong>Exam Marks:</strong> {{ $lesson->max_exam_marks }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('staff.lessons.update', $lesson->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="lesson_title" class="form-label required">Lesson Title</label>
                            <input type="text" class="form-control" id="lesson_title" name="lesson_title" 
                                   value="{{ $lesson->title }}" required>
                            @error('lesson_title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="lesson_weight" class="form-label required">Lesson Weight</label>
                            <input type="number" class="form-control" id="lesson_weight" name="lesson_weight" 
                                   value="{{ $lesson->lesson_weight }}" min="1" max="100" required>
                            @error('lesson_weight')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Multi-Branch Course Assignment -->
                
                @if($lesson->branchCourses && $lesson->branchCourses->count() > 0)
                    <div class="mb-4">
                        <h6 class="section-title">Current Branch-Course Assignments</h6>
                        <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
                                @foreach($lesson->branchCourses as $bc)
                                    <div style="background: white; border: 1px solid #dee2e6; border-radius: 6px; padding: 12px;">
                                        <div style="font-weight: 600; color: #667eea; margin-bottom: 5px;">
                                            <i class="fas fa-building" style="margin-right: 5px;"></i> {{ $bc->branch->name }}
                                        </div>
                                        <div style="font-size: 0.9rem; margin-bottom: 5px;">
                                            <strong>Course:</strong> {{ $bc->course->course_name }}
                                        </div>
                                        <div style="font-size: 0.9rem; color: #666;">
                                            <strong>Teacher:</strong> {{ $bc->teacher ? $bc->teacher->teacher_name : 'Not assigned' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Add New Branch-Course Assignments -->
                <div class="mb-4" id="multiBranchSection">
                    <h6 class="section-title">Add More Branch-Course Assignments</h6>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px;">
                        Select branches and courses to add additional assignments to this lesson. Existing assignments will be preserved.
                    </p>

                    <!-- Step 1: Select Branches -->
                    <div class="mb-4">
                        <label class="form-label">Select Branches to Add</label>
                        <div class="row" style="margin: -8px;">
                            @foreach($branches as $branch)
                                <div class="col-sm-6 col-lg-4 p-2">
                                    <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; transition: all 0.3s ease; margin-bottom: 0;" 
                                           class="branch-label" onchange="updateBranchSelection()">
                                        <input type="checkbox" name="additional_branches[]" value="{{ $branch->id }}" 
                                               class="additional-branch-checkbox" style="margin-right: 8px;">
                                        <span style="font-weight: 500;">
                                            <i class="fas fa-building" style="margin-right: 5px;"></i>
                                            {{ $branch->name }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p style="font-size: 0.82rem; color: #999; margin-top: 10px;">
                            Selected: <span id="selectedBranchCount">0</span> branch(es)
                        </p>
                    </div>

                    <!-- Step 2: Select Courses -->
                    <div class="mb-4">
                        <label class="form-label">Select Course-Branch Pairs to Add</label>
                        <div id="coursePairsContainer" style="background: #f8f9fa; border-radius: 6px; padding: 15px;">
                            <p style="font-size: 0.9rem; color: #999; margin: 0; text-align: center;">
                                <i class="fas fa-arrow-up"></i> Select branches above first
                            </p>
                        </div>
                        <p style="font-size: 0.82rem; color: #999; margin-top: 10px;">
                            Selected: <span id="selectedCourseCount">0</span> course-branch pair(s)
                        </p>
                    </div>

                    <!-- Step 3: Assign Teachers -->
                    <div class="mb-4">
                        <label class="form-label">Assign Teachers to New Assignments</label>
                        <div id="teacherAssignmentsContainer" style="background: #f8f9fa; border-radius: 6px; padding: 15px;">
                            <p style="font-size: 0.9rem; color: #999; margin: 0; text-align: center;">
                                <i class="fas fa-arrow-up"></i> Select course-branch pairs above first
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Academic Year -->
                <div class="mb-3">
                    <label for="academic_year_id" class="form-label required">Academic Year</label>
                    <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $lesson->academic_year_id == $year->id ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Marks Configuration -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_test_marks" class="form-label required">Max Test Marks</label>
                            <input type="number" class="form-control" id="max_test_marks" name="max_test_marks" 
                                   value="{{ $lesson->max_test_marks }}" min="1" required>
                            @error('max_test_marks')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_exam_marks" class="form-label required">Max Exam Marks</label>
                            <input type="number" class="form-control" id="max_exam_marks" name="max_exam_marks" 
                                   value="{{ $lesson->max_exam_marks }}" min="1" required>
                            @error('max_exam_marks')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms Selection -->
                <div class="mb-4">
                    <h6 class="section-title">Terms</h6>
                    <div class="row">
                        @foreach($terms as $term)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms[]" 
                                           value="{{ $term->id }}" id="term_{{ $term->id }}"
                                           @if($lesson->terms->contains($term->id)) checked @endif>
                                    <label class="form-check-label" for="term_{{ $term->id }}">
                                        {{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->year . ')' : '' }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('terms')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lesson Units Management -->
                <div class="mb-4">
                    <h6 class="section-title">Lesson Units</h6>
                    <div id="units-container">
                        @if($lesson->units && $lesson->units->count() > 0)
                            @foreach($lesson->units as $index => $unit)
                                <div class="unit-item" data-unit-id="{{ $unit->id }}">
                                    <button type="button" class="btn-remove-unit" onclick="removeUnit(this)">×</button>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-label required">Unit Name</label>
                                            <input type="text" class="form-control" name="units[{{ $index }}][name]" 
                                                   value="{{ $unit->unit_name }}" placeholder="Enter unit name" required>
                                            <input type="hidden" name="units[{{ $index }}][id]" value="{{ $unit->id }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required">Hours</label>
                                            <input type="number" class="form-control" name="units[{{ $index }}][hours]" 
                                                   value="{{ $unit->hours }}" placeholder="Hours" min="1" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Term</label>
                                            <select class="form-select" name="units[{{ $index }}][term_id]">
                                                @foreach($terms as $term)
                                                    <option value="{{ $term->id }}" {{ $unit->term_id == $term->id ? 'selected' : '' }}>
                                                        {{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->year . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Course Level</label>
                                            <select class="form-select" name="units[{{ $index }}][course_level]">
                                                <option value="beginner" {{ $unit->course_level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="intermediate" {{ $unit->course_level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                <option value="advanced" {{ $unit->course_level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-3">No units have been added to this lesson yet.</p>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary mt-3" onclick="addUnit()">
                        <i class="fas fa-plus"></i> Add New Unit
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('staff.lessons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Lessons
                        </a>
                        <a href="{{ route('staff.lesson-units.create', $lesson->id) }}" class="btn btn-outline-success ms-2">
                            <i class="bi bi-diagram-3"></i> Manage Units
                        </a>
                    </div>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Update Lesson
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let unitCount = {{ $lesson->units ? $lesson->units->count() : 0 }};

// ── Multi-branch course selection state ────────────────────────
let selectedAdditionalBranches = new Set();
let selectedAdditionalCourses = new Set();
let selectedTeachers = {}; // Map: "courseId_branchId" -> teacher_id

// Course-teachers data from server
window.courseTeachersMap = {!! json_encode($courseTeachersData ?? []) !!};
window.courseBranchesMap = {!! json_encode($courseBranchesMap ?? []) !!};
window.branchNames = {!! json_encode($branches->pluck('name', 'id')->toArray()) !!};
window.courseNames = {!! json_encode($courses->pluck('course_name', 'id')->toArray()) !!};

// ── Branch Selection ─────────────────────────────────────────────
function updateBranchSelection() {
    selectedAdditionalBranches.clear();
    const checkboxes = document.querySelectorAll('.additional-branch-checkbox:checked');
    const labels = document.querySelectorAll('.branch-label');
    
    // Clear all selected states
    labels.forEach(label => label.style.borderColor = '#e0e0e0');
    
    // Add selected state and collect selected branches
    checkboxes.forEach(checkbox => {
        selectedAdditionalBranches.add(parseInt(checkbox.value));
        const label = checkbox.closest('.branch-label');
        if (label) {
            label.style.borderColor = '#667eea';
            label.style.backgroundColor = '#f0f4ff';
        }
    });
    
    // Update display
    const countEl = document.getElementById('selectedBranchCount');
    if (countEl) countEl.textContent = selectedAdditionalBranches.size;
    
    // Update course pairs container
    renderCoursePairs();
}

// ── Course-Branch Pair Selection ────────────────────────────────
function renderCoursePairs() {
    const container = document.getElementById('coursePairsContainer');
    
    if (selectedAdditionalBranches.size === 0) {
        container.innerHTML = '<p style="font-size: 0.9rem; color: #999; margin: 0; text-align: center;"><i class="fas fa-arrow-up"></i> Select branches above first</p>';
        return;
    }
    
    let html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
    
    @foreach($courses as $course)
        const courseId = {{ $course->id }};
        const courseName = '{{ $course->course_name }}';
        
        const branchesForCourse = {!! json_encode(
            $courses->mapWithKeys(function($c) {
                return [$c->id => $c->branches->pluck('id')->toArray()];
            })->toArray()
        ) !!}[courseId] || [];
        
        const availableBranches = Array.from(selectedAdditionalBranches).filter(bId => 
            branchesForCourse.includes(bId)
        );
        
        if (availableBranches.length > 0) {
            html += `<div style="background: white; border: 1px solid #dee2e6; border-radius: 6px; padding: 12px;">
                <strong style="display: block; margin-bottom: 8px;">` + courseName + `</strong>
                <div style="display: flex; flex-direction: column; gap: 6px;">`;
            
            availableBranches.forEach(branchId => {
                const checkboxId = 'additional_course_' + courseId + '_' + branchId;
                const branchName = window.branchNames[branchId];
                html += `
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem; margin: 0;">
                        <input type="checkbox" id="${checkboxId}" class="additional-course-checkbox" 
                               value="${courseId}_${branchId}" onchange="updateSelectedCourses()">
                        📍 ${branchName}
                    </label>`;
            });
            
            html += `</div></div>`;
        }
    @endforeach
    
    html += '</div>';
    container.innerHTML = html;
}

// ── Course-Branch Selection ─────────────────────────────────────
function updateSelectedCourses() {
    selectedAdditionalCourses.clear();
    const checkboxes = document.querySelectorAll('.additional-course-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        selectedAdditionalCourses.add(checkbox.value);
    });
    
    const countEl = document.getElementById('selectedCourseCount');
    if (countEl) countEl.textContent = selectedAdditionalCourses.size;
    
    // Update teacher assignment UI
    renderTeacherAssignments();
}

// ── Teacher Assignment UI ───────────────────────────────────────
function renderTeacherAssignments() {
    const container = document.getElementById('teacherAssignmentsContainer');
    
    if (selectedAdditionalCourses.size === 0) {
        container.innerHTML = '<p style="font-size: 0.9rem; color: #999; margin: 0; text-align: center;"><i class="fas fa-arrow-up"></i> Select course-branch pairs above first</p>';
        return;
    }
    
    let html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
    
    const courseBranchPairs = Array.from(selectedAdditionalCourses);
    
    courseBranchPairs.forEach((pair, index) => {
        const [courseId, branchId] = pair.split('_');
        const pairKey = `${courseId}_${branchId}`;
        
        const courseName = window.courseNames[courseId];
        const branchName = window.branchNames[branchId];
        const teachers = window.courseTeachersMap[courseId] || [];
        
        html += `
            <div style="padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; background: #f8f9fa;">
                <div style="margin-bottom: 8px;">
                    <strong style="display: block; font-size: 0.95rem;">` + courseName + `</strong>
                    <span style="font-size: 0.85rem; color: #666;">📍 ` + branchName + `</span>
                </div>
                <div>
                    <label class="form-label" style="margin-bottom: 6px;">Teacher</label>
                    <select class="form-select teacher-select" data-pair="${pairKey}" onchange="updateTeacherSelection(this)" style="font-size: 0.9rem;">
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

// ── Teacher Selection Update ────────────────────────────────────
function updateTeacherSelection(selectElement) {
    const pairKey = selectElement.dataset.pair;
    const teacherId = selectElement.value;
    
    if (teacherId) {
        selectedTeachers[pairKey] = teacherId;
    } else {
        delete selectedTeachers[pairKey];
    }
}

// ── Unit Management ──────────────────────────────────────────────
function addUnit() {
    const container = document.getElementById('units-container');
    const unitHtml = `
        <div class="unit-item">
            <button type="button" class="btn-remove-unit" onclick="removeUnit(this)">×</button>
            <div class="row">
                <div class="col-md-8">
                    <label class="form-label required">Unit Name</label>
                    <input type="text" class="form-control" name="units[${unitCount}][name]" placeholder="Enter unit name" required>
                    <input type="hidden" name="units[${unitCount}][id]" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Hours</label>
                    <input type="number" class="form-control" name="units[${unitCount}][hours]" placeholder="Hours" min="1" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Term</label>
                    <select class="form-select" name="units[${unitCount}][term_id]">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->year . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Course Level</label>
                    <select class="form-select" name="units[${unitCount}][course_level]">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', unitHtml);
    unitCount++;
}

function removeUnit(button) {
    const unitItem = button.closest('.unit-item');
    const unitId = unitItem.dataset.unitId;
    
    if (unitId) {
        // This is an existing unit, mark for deletion
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = `delete_units[]`;
        deleteInput.value = unitId;
        unitItem.appendChild(deleteInput);
        unitItem.style.display = 'none';
    } else {
        // This is a new unit, just remove it
        unitItem.remove();
    }
}

// ── Form Submission Handler ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Add form submission handler to convert course-branch pairs to required format
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Convert selected course-branch pairs to hidden inputs with teacher_id
            const courseCheckboxes = document.querySelectorAll('.additional-course-checkbox:checked');
            
            courseCheckboxes.forEach((checkbox, index) => {
                const [courseId, branchId] = checkbox.value.split('_');
                const pairKey = `${courseId}_${branchId}`;
                const teacherId = selectedTeachers[pairKey] || null;
                
                const courseInput = document.createElement('input');
                courseInput.type = 'hidden';
                courseInput.name = `additional_courses[${index}][course_id]`;
                courseInput.value = courseId;
                form.appendChild(courseInput);
                
                const branchInput = document.createElement('input');
                branchInput.type = 'hidden';
                branchInput.name = `additional_courses[${index}][branch_id]`;
                branchInput.value = branchId;
                form.appendChild(branchInput);
                
                if (teacherId) {
                    const teacherInput = document.createElement('input');
                    teacherInput.type = 'hidden';
                    teacherInput.name = `additional_courses[${index}][teacher_id]`;
                    teacherInput.value = teacherId;
                    form.appendChild(teacherInput);
                }
            });
        });
    }
});
</script>
@endsection
