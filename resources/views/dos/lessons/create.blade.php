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
    .course-branch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .branch-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    .branch-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102,126,234,0.15);
    }
    .branch-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
    }
    .branch-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    .course-item {
        display: flex;
        align-items: center;
        padding: 10px;
        margin: 5px 0;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .course-item:hover {
        background: rgba(102,126,234,0.05);
    }
    .course-checkbox {
        margin-right: 10px;
        transform: scale(1.2);
    }
    .teacher-select {
        margin-left: 10px;
        min-width: 150px;
        font-size: 0.9rem;
    }
    .assignment-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
    }
    .assignment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .assignment-item:last-child {
        border-bottom: none;
    }
    .remove-assignment {
        color: #dc3545;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    .remove-assignment:hover {
        transform: scale(1.2);
    }
</style>

<div class="create-container">
    <div class="container">
        <div class="create-card">
            <div class="create-header">
                <h2><i class="fas fa-plus-circle me-2"></i>Create Lesson for Multiple Branches</h2>
                <p class="mb-0 mt-1 opacity-75">Create a lesson that can be assigned to multiple courses across different branches</p>
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

                    {{-- ── Basic Information ── --}}
                    <h5 class="section-title"><i class="fas fa-book me-2"></i>Basic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label required">Lesson Title</label>
                            <input type="text" name="lesson_title" class="form-control"
                                   value="{{ old('lesson_title') }}" required placeholder="Enter lesson title">
                        </div>
                    </div>

                    {{-- ── Course-Branch Selection ── --}}
                    <h5 class="section-title"><i class="fas fa-network-wired me-2"></i>Course & Branch Assignment</h5>
                    <div class="course-branch-grid" id="branchGrid">
                        @foreach($branches as $branch)
                            <div class="branch-card" data-branch-id="{{ $branch->id }}">
                                <div class="branch-title">
                                    <i class="fas fa-map-marker-alt me-2"></i>{{ $branch->name }}
                                </div>
                                @foreach($courses as $course)
                                    @if($course->branches->contains($branch->id))
                                        <div class="course-item">
                                            <input type="checkbox" 
                                                   class="form-check-input course-checkbox" 
                                                   name="course_branch_{{ $course->id }}_{{ $branch->id }}"
                                                   data-course-id="{{ $course->id }}" 
                                                   data-branch-id="{{ $branch->id }}"
                                                   data-course-name="{{ $course->course_name }}"
                                                   data-branch-name="{{ $branch->name }}">
                                            <label class="form-check-label" style="margin-right: 10px;">
                                                {{ $course->course_name }} ({{ $course->abbreviation }})
                                            </label>
                                            <select class="form-select teacher-select" 
                                                    name="teacher_{{ $course->id }}_{{ $branch->id }}"
                                                    data-course-id="{{ $course->id }}" 
                                                    data-branch-id="{{ $branch->id }}">
                                                <option value="">Select Teacher</option>
                                            </select>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- ── Assignment Preview ── --}}
                    <h5 class="section-title"><i class="fas fa-list me-2"></i>Selected Assignments</h5>
                    <div class="assignment-preview">
                        <div id="assignmentPreview">
                            <p class="text-muted text-center">No course-branch assignments selected yet</p>
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
    const assignments = new Map(); // Store assignments as Map<key, {course_id, branch_id, teacher_id, course_name, branch_name}>
    const form = document.getElementById('multiBranchForm');
    const submitBtn = document.getElementById('submitBtn');
    const assignmentPreview = document.getElementById('assignmentPreview');
    const selectedCoursesData = document.getElementById('selectedCoursesData');

    // Handle course-branch selection
    document.querySelectorAll('.course-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const courseId = this.dataset.courseId;
            const branchId = this.dataset.branchId;
            const courseName = this.dataset.courseName;
            const branchName = this.dataset.branchName;
            const key = `${courseId}_${branchId}`;
            
            const teacherSelect = document.querySelector(`select[name="teacher_${courseId}_${branchId}"]`);
            
            if (this.checked) {
                // Enable teacher selection
                teacherSelect.disabled = false;
                loadTeachersForCourseBranch(courseId, branchId, teacherSelect);
                
                // Add to assignments
                assignments.set(key, {
                    course_id: parseInt(courseId),
                    branch_id: parseInt(branchId),
                    teacher_id: null,
                    course_name: courseName,
                    branch_name: branchName
                });
                
                // Highlight branch card
                this.closest('.branch-card').classList.add('selected');
            } else {
                // Disable teacher selection
                teacherSelect.disabled = true;
                teacherSelect.value = '';
                
                // Remove from assignments
                assignments.delete(key);
                
                // Remove highlight if no courses selected in this branch
                const branchCard = this.closest('.branch-card');
                const hasSelectedCourses = branchCard.querySelectorAll('.course-checkbox:checked').length > 0;
                if (!hasSelectedCourses) {
                    branchCard.classList.remove('selected');
                }
            }
            
            updateAssignmentPreview();
            updateSubmitButton();
        });
    });

    // Handle teacher selection
    document.querySelectorAll('.teacher-select').forEach(select => {
        select.addEventListener('change', function() {
            const courseId = this.dataset.courseId;
            const branchId = this.dataset.branchId;
            const key = `${courseId}_${branchId}`;
            
            if (assignments.has(key)) {
                assignments.get(key).teacher_id = this.value ? parseInt(this.value) : null;
                updateAssignmentPreview();
            }
        });
    });

    function loadTeachersForCourseBranch(courseId, branchId, selectElement) {
        // Load teachers for this course
        fetch(`/staff/lessons/get-teachers/${courseId}`)
            .then(response => response.json())
            .then(teachers => {
                selectElement.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                teachers.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = teacher.teacher_name;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading teachers:', error);
            });
    }

    function updateAssignmentPreview() {
        if (assignments.size === 0) {
            assignmentPreview.innerHTML = '<p class="text-muted text-center">No course-branch assignments selected yet</p>';
            selectedCoursesData.value = '';
            return;
        }

        let html = '';
        let assignmentsArray = [];
        
        assignments.forEach((assignment, key) => {
            html += `
                <div class="assignment-item">
                    <div>
                        <strong>${assignment.course_name}</strong> - ${assignment.branch_name}
                        ${assignment.teacher_id ? '<br><small class="text-muted">Teacher: ' + getTeacherName(assignment.teacher_id) + '</small>' : ''}
                    </div>
                    <i class="fas fa-times remove-assignment" onclick="removeAssignment('${key}')"></i>
                </div>
            `;
            
            assignmentsArray.push({
                course_id: assignment.course_id,
                branch_id: assignment.branch_id,
                teacher_id: assignment.teacher_id
            });
        });

        assignmentPreview.innerHTML = html;
        selectedCoursesData.value = JSON.stringify(assignmentsArray);
    }

    function getTeacherName(teacherId) {
        const select = document.querySelector(`option[value="${teacherId}"]`);
        return select ? select.textContent : 'Unknown';
    }

    window.removeAssignment = function(key) {
        const [courseId, branchId] = key.split('_');
        const checkbox = document.querySelector(`input[data-course-id="${courseId}"][data-branch-id="${branchId}"]`);
        
        if (checkbox) {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        }
    };

    function updateSubmitButton() {
        submitBtn.disabled = assignments.size === 0;
        if (assignments.size > 0) {
            submitBtn.innerHTML = `<i class="fas fa-plus-circle me-2"></i>Create Lesson (${assignments.size} assignments)`;
        } else {
            submitBtn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Create Lesson';
        }
    }

    // Form submission
    form.addEventListener('submit', function (e) {
        if (assignments.size === 0) {
            e.preventDefault();
            alert('Please select at least one course-branch assignment');
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating…';
    });

    // Initialize teacher selects as disabled
    document.querySelectorAll('.teacher-select').forEach(select => {
        select.disabled = true;
    });
});
</script>
@endsection
