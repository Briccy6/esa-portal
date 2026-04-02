@extends('dos.layout')

@section('content')
<style>
    .create-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .create-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 1200px;
        margin: 0 auto;
        overflow: hidden;
    }
    .create-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .create-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .create-body { padding: 40px; }
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
        font-size: 1.05rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        color: white;
    }
    .btn-submit:disabled {
        opacity: 0.6;
        transform: none;
        box-shadow: none;
    }
    .branch-selection {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    .branch-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    .branch-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102,126,234,0.15);
        transform: translateY(-2px);
    }
    .branch-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
        box-shadow: 0 4px 12px rgba(102,126,234,0.25);
    }
    .branch-icon {
        font-size: 2.5rem;
        color: #667eea;
        margin-bottom: 10px;
    }
    .branch-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        font-size: 1.1rem;
    }
    .branch-description {
        color: #666;
        font-size: 0.9rem;
    }
    .course-selection {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-top: 20px;
        border: 2px solid #e0e0e0;
    }
    .course-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .course-item {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        background: white;
        transition: all 0.3s ease;
    }
    .course-item:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102,126,234,0.15);
    }
    .course-item.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102,126,234,0.05) 0%, rgba(118,75,162,0.05) 100%);
    }
    .course-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .course-title {
        font-weight: 600;
        color: #333;
        font-size: 1rem;
    }
    .course-abbreviation {
        background: #667eea;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .teacher-selection {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
    }
    .assignment-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        max-height: 300px;
        overflow-y: auto;
    }
    .assignment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        margin: 5px 0;
        background: white;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    .assignment-item:hover {
        border-color: #667eea;
        box-shadow: 0 2px 4px rgba(102,126,234,0.1);
    }
    .remove-assignment {
        color: #dc3545;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        padding: 5px;
    }
    .remove-assignment:hover {
        transform: scale(1.2);
    }
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }
    .step {
        display: flex;
        align-items: center;
        color: #999;
        font-weight: 600;
    }
    .step.active {
        color: #667eea;
    }
    .step.completed {
        color: #28a745;
    }
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid currentColor;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-weight: bold;
    }
    .step-arrow {
        margin: 0 15px;
        color: #ccc;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    .empty-state i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 15px;
    }
</style>

<div class="create-container">
    <div class="container">
        <div class="create-card">
            <div class="create-header">
                <h2><i class="fas fa-plus-circle me-2"></i>Create Professional Lesson</h2>
                <p class="mb-0 mt-1 opacity-75">Design your lesson with a step-by-step branch and course selection process</p>
            </div>

            <div class="create-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('staff.lessons.store') }}" method="POST" id="multiBranchForm">
                    @csrf
                    <input type="hidden" name="is_multi_branch" value="1">

                    {{-- Debug Information --}}
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Debug Information</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Branches:</strong> {{ $branches->count() }} available
                                @if($branches->count() > 0)
                                    <br><small>{{ $branches->pluck('name')->implode(', ') }}</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <strong>Courses:</strong> {{ $courses->count() }} available
                                @if($courses->count() > 0)
                                    <br><small>{{ $courses->pluck('course_name')->take(3)->implode(', ') }}{{ $courses->count() > 3 ? '...' : '' }}</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <strong>Terms:</strong> {{ $terms->count() }} available
                                @if($terms->count() > 0)
                                    <br><small>{{ $terms->pluck('name')->implode(', ') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Step Indicator --}}
                    <div class="step-indicator">
                        <div class="step" id="step1">
                            <div class="step-number">1</div>
                            <span>Select Branches</span>
                        </div>
                        <div class="step-arrow">→</div>
                        <div class="step" id="step2">
                            <div class="step-number">2</div>
                            <span>Choose Courses</span>
                        </div>
                        <div class="step-arrow">→</div>
                        <div class="step" id="step3">
                            <div class="step-number">3</div>
                            <span>Assign Teachers</span>
                        </div>
                    </div>

                    {{-- ── Basic Information ──}}
                    <h5 class="section-title"><i class="fas fa-book me-2"></i>Basic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label required">Lesson Title</label>
                            <input type="text" name="lesson_title" class="form-control"
                                   value="{{ old('lesson_title') }}" required placeholder="Enter a descriptive lesson title">
                        </div>
                    </div>

                    {{-- ── Branch Selection (Step 1) ──}}
                    <h5 class="section-title"><i class="fas fa-map-marker-alt me-2"></i>Step 1: Select Branches</h5>
                    
                    @if($branches->count() > 0)
                        <div class="branch-selection" id="branchSelection">
                            @foreach($branches as $branch)
                                <div class="branch-card" data-branch-id="{{ $branch->id }}" onclick="toggleBranch({{ $branch->id }})">
                                    <div class="branch-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="branch-title">{{ $branch->name }}</div>
                                    <div class="branch-description">Click to select this branch</div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Fallback: Simple checkbox selection --}}
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFallbackSelection()">
                                <i class="fas fa-list me-1"></i>Use Simple Selection
                            </button>
                            <div id="fallbackSelection" style="display: none; margin-top: 15px;">
                                <div class="alert alert-secondary">
                                    <h6><i class="fas fa-check-square me-2"></i>Select Branches (Simple Mode)</h6>
                                    @foreach($branches as $branch)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="branches[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}">
                                            <label class="form-check-label" for="branch_{{ $branch->id }}">
                                                {{ $branch->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>No branches found!</strong> Please add branches to the system first before creating lessons.
                        </div>
                        <div class="text-center">
                            <a href="{{ url('/admin/branches') }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-plus me-2"></i>Add Branches
                            </a>
                        </div>
                    @endif

                    {{-- ── Course Selection (Step 2) ──}}
                    <div id="courseSection">
                        <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Step 2: Choose Courses for Selected Branches</h5>
                        <div class="course-selection" id="courseSelection">
                            <div class="empty-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading courses for selected branches...</p>
                            </div>
                        </div>
                        
                        {{-- Fallback: Show all courses with branch selection --}}
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleCourseFallback()">
                                <i class="fas fa-list me-1"></i>Show All Courses
                            </button>
                            <div id="courseFallback" style="display: none; margin-top: 15px;">
                                <div class="alert alert-secondary">
                                    <h6><i class="fas fa-graduation-cap me-2"></i>Select Courses (Simple Mode)</h6>
                                    @if($courses->count() > 0)
                                        @foreach($courses as $course)
                                            <div class="border rounded p-3 mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $course->course_name }}</strong> ({{ $course->abbreviation }})
                                                        <br><small class="text-muted">Available in: {{ $course->branches->pluck('name')->implode(', ') }}</small>
                                                    </div>
                                                    <div>
                                                        <select class="form-select form-select-sm" name="course_assignments[]">
                                                            <option value="">Select Branch</option>
                                                            @foreach($course->branches as $branch)
                                                                <option value="{{ $course->id }}_{{ $branch->id }}">
                                                                    {{ $branch->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No courses available. Please add courses first.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Assignment Preview (Step 3) ──}}
                    <div id="assignmentSection">
                        <h5 class="section-title"><i class="fas fa-users me-2"></i>Step 3: Assign Teachers & Review</h5>
                        <div class="assignment-preview">
                            <div id="assignmentPreview">
                                <p class="text-muted text-center">No course assignments selected yet</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── Academic Settings ── --}}
                    <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Academic Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @if($ongoingAcademicYear)
                                    <option value="{{ $ongoingAcademicYear->id }}" selected>
                                        {{ $ongoingAcademicYear->year }} (Current)
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Terms</label>
                            <div class="mt-2">
                                @foreach($terms as $term)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="terms[]" value="{{ $term->id }}" 
                                               id="term_{{ $term->id }}" {{ in_array($term->id, old('terms', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="term_{{ $term->id }}">
                                            {{ $term->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── Grading Settings ── --}}
                    <h5 class="section-title"><i class="fas fa-chart-line me-2"></i>Grading Settings</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Maximum Test Marks</label>
                            <input type="number" name="max_test_marks" class="form-control"
                                   value="{{ old('max_test_marks') }}" min="0" placeholder="e.g. 30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Maximum Exam Marks</label>
                            <input type="number" name="max_exam_marks" class="form-control"
                                   value="{{ old('max_exam_marks') }}" min="0" placeholder="e.g. 70">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Lesson Weight (%)</label>
                            <input type="number" name="lesson_weight" class="form-control"
                                   value="{{ old('lesson_weight', 100) }}" min="1" max="100" required placeholder="e.g. 100">
                        </div>
                    </div>

                    {{-- Hidden field for selected courses data --}}
                    <input type="hidden" name="selected_courses" id="selectedCoursesData" value="">

                    {{-- ── Action Buttons ── --}}
                    <div class="text-center mt-5">
                        <a href="{{ route('dos.lessons.index') }}" class="btn btn-secondary me-3 px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn" disabled>
                            <i class="fas fa-plus-circle me-2"></i>Create Lesson
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectedBranches = new Set();
    const selectedCourses = new Map(); // Store course assignments
    const form = document.getElementById('multiBranchForm');
    const submitBtn = document.getElementById('submitBtn');
    const assignmentPreview = document.getElementById('assignmentPreview');
    const selectedCoursesData = document.getElementById('selectedCoursesData');
    
    // Toggle branch selection
    window.toggleBranch = function(branchId) {
        const branchCard = document.querySelector(`.branch-card[data-branch-id="${branchId}"]`);
        
        if (selectedBranches.has(branchId)) {
            selectedBranches.delete(branchId);
            branchCard.classList.remove('selected');
            // Remove all courses for this branch
            selectedCourses.forEach((course, key) => {
                if (course.branch_id === branchId) {
                    selectedCourses.delete(key);
                }
            });
        } else {
            selectedBranches.add(branchId);
            branchCard.classList.add('selected');
        }
        
        updateStepIndicator();
        loadCoursesForBranches();
    };
    
    // Load courses for selected branches
    function loadCoursesForBranches() {
        const courseSection = document.getElementById('courseSection');
        const courseSelection = document.getElementById('courseSelection');
        
        if (selectedBranches.size === 0) {
            courseSection.style.display = 'none';
            updateStepIndicator();
            updateSubmitButton();
            return;
        }
        
        courseSection.style.display = 'block';
        courseSelection.innerHTML = '<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading courses...</p></div>';
        
        // Fetch courses for selected branches
        const branchIds = Array.from(selectedBranches);
        fetch('/staff/lessons/get-courses-for-branches', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ branch_ids: branchIds })
        })
        .then(response => response.json())
        .then(data => {
            displayCourses(data.courses);
            updateStepIndicator();
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            courseSelection.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Error loading courses</p></div>';
        });
    }
    
    // Display courses for selection
    function displayCourses(courses) {
        const courseSelection = document.getElementById('courseSelection');
        
        if (courses.length === 0) {
            courseSelection.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>No courses available for selected branches</p></div>';
            return;
        }
        
        let html = '<div class="course-grid">';
        courses.forEach(course => {
            const key = `${course.id}_${course.branch_id}`;
            const isSelected = selectedCourses.has(key);
            
            html += `
                <div class="course-item ${isSelected ? 'selected' : ''}" id="course_${key}">
                    <div class="course-header">
                        <div class="course-title">${course.course_name}</div>
                        <div class="course-abbreviation">${course.abbreviation}</div>
                    </div>
                    <div class="course-meta">
                        <small class="text-muted">Branch: ${course.branch_name}</small>
                    </div>
                    <div class="course-selection mt-2">
                        <button type="button" class="btn btn-sm ${isSelected ? 'btn-danger' : 'btn-primary'}" 
                                onclick="toggleCourse(${course.id}, ${course.branch_id}, '${course.course_name}', '${course.branch_name}')">
                            ${isSelected ? '<i class="fas fa-times"></i> Remove' : '<i class="fas fa-plus"></i> Select'}
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        courseSelection.innerHTML = html;
    }
    
    // Toggle course selection
    window.toggleCourse = function(courseId, branchId, courseName, branchName) {
        const key = `${courseId}_${branchId}`;
        const courseElement = document.getElementById(`course_${key}`);
        
        if (selectedCourses.has(key)) {
            selectedCourses.delete(key);
            courseElement.classList.remove('selected');
        } else {
            selectedCourses.set(key, {
                course_id: courseId,
                branch_id: branchId,
                course_name: courseName,
                branch_name: branchName,
                teacher_id: null
            });
            courseElement.classList.add('selected');
        }
        
        displayCourses(getCurrentCoursesData());
        updateAssignmentSection();
        updateStepIndicator();
        updateSubmitButton();
    };
    
    // Get current courses data for display
    function getCurrentCoursesData() {
        const courses = [];
        selectedCourses.forEach((course, key) => {
            const [courseId, branchId] = key.split('_');
            courses.push({
                id: parseInt(courseId),
                branch_id: parseInt(branchId),
                course_name: course.course_name,
                branch_name: course.branch_name,
                abbreviation: course.abbreviation || 'N/A'
            });
        });
        return courses;
    }
    
    // Update assignment section
    function updateAssignmentSection() {
        const assignmentSection = document.getElementById('assignmentSection');
        
        if (selectedCourses.size === 0) {
            assignmentSection.style.display = 'none';
            return;
        }
        
        assignmentSection.style.display = 'block';
        updateAssignmentPreview();
    }
    
    // Update assignment preview
    function updateAssignmentPreview() {
        if (selectedCourses.size === 0) {
            assignmentPreview.innerHTML = '<p class="text-muted text-center">No course assignments selected yet</p>';
            selectedCoursesData.value = '';
            return;
        }

        let html = '';
        let assignmentsArray = [];
        
        selectedCourses.forEach((course, key) => {
            const teacherSelectId = `teacher_${key}`;
            html += `
                <div class="assignment-item">
                    <div>
                        <strong>${course.course_name}</strong> - ${course.branch_name}
                        <div class="teacher-selection mt-2">
                            <select class="form-select form-select-sm" id="${teacherSelectId}" 
                                    onchange="updateTeacherAssignment('${key}', this.value)">
                                <option value="">Select Teacher (Optional)</option>
                            </select>
                        </div>
                    </div>
                    <i class="fas fa-times remove-assignment" onclick="removeAssignment('${key}')"></i>
                </div>
            `;
            
            assignmentsArray.push({
                course_id: course.course_id,
                branch_id: course.branch_id,
                teacher_id: course.teacher_id
            });
            
            // Load teachers for this course
            loadTeachersForCourse(course.course_id, teacherSelectId);
        });

        assignmentPreview.innerHTML = html;
        selectedCoursesData.value = JSON.stringify(assignmentsArray);
    }
    
    // Load teachers for a course
    function loadTeachersForCourse(courseId, selectElementId) {
        fetch(`/staff/lessons/get-teachers/${courseId}`)
            .then(response => response.json())
            .then(teachers => {
                const selectElement = document.getElementById(selectElementId);
                if (selectElement) {
                    selectElement.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                    teachers.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.teacher_name;
                        selectElement.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading teachers:', error);
            });
    }
    
    // Update teacher assignment
    window.updateTeacherAssignment = function(key, teacherId) {
        if (selectedCourses.has(key)) {
            selectedCourses.get(key).teacher_id = teacherId ? parseInt(teacherId) : null;
            updateAssignmentPreview();
        }
    };
    
    // Remove assignment
    window.removeAssignment = function(key) {
        const [courseId, branchId] = key.split('_');
        toggleCourse(parseInt(courseId), parseInt(branchId));
    };
    
    // Update step indicator
    function updateStepIndicator() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        
        // Reset all steps
        [step1, step2, step3].forEach(step => {
            step.classList.remove('active', 'completed');
        });
        
        // Step 1: Branch selection
        if (selectedBranches.size > 0) {
            step1.classList.add('completed');
        } else {
            step1.classList.add('active');
        }
        
        // Step 2: Course selection
        if (selectedBranches.size > 0) {
            if (selectedCourses.size > 0) {
                step2.classList.add('completed');
            } else {
                step2.classList.add('active');
            }
        }
        
        // Step 3: Teacher assignment
        if (selectedCourses.size > 0) {
            step3.classList.add('active');
        }
    }
    
    // Update submit button
    function updateSubmitButton() {
        submitBtn.disabled = selectedCourses.size === 0;
        if (selectedCourses.size > 0) {
            submitBtn.innerHTML = `<i class="fas fa-plus-circle me-2"></i>Create Lesson (${selectedCourses.size} course${selectedCourses.size > 1 ? 's' : ''})`;
        } else {
            submitBtn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Create Lesson';
        }
    }
    
    // Form submission
    form.addEventListener('submit', function (e) {
        if (selectedCourses.size === 0) {
            e.preventDefault();
            alert('Please select at least one course assignment');
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating…';
    });
    
    // Toggle fallback selection
    window.toggleFallbackSelection = function() {
        const fallbackDiv = document.getElementById('fallbackSelection');
        fallbackDiv.style.display = fallbackDiv.style.display === 'none' ? 'block' : 'none';
    };
    
    // Toggle course fallback
    window.toggleCourseFallback = function() {
        const fallbackDiv = document.getElementById('courseFallback');
        fallbackDiv.style.display = fallbackDiv.style.display === 'none' ? 'block' : 'none';
    };
    
    // Initialize
    updateStepIndicator();
    updateSubmitButton();
});
</script>
@endsection
