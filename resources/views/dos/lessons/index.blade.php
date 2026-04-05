@extends('dos.layout')

@section('content')
<style>
    .index-container {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .index-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin: 0 auto;
        overflow: hidden;
    }
    .index-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .index-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .index-body { padding: 40px; }
    
    /* Table Styles */
    .lessons-table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .lessons-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .lessons-table thead th {
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .lessons-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .lessons-table tbody tr:hover {
        background-color: #f8f9ff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }
    .lessons-table tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border-color: #f0f0f0;
    }
    
    /* Assignment Badges */
    .assignment-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        margin: 2px;
        font-weight: 500;
    }
    
    /* Action Buttons */
    .btn-action {
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 0 3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-action i {
        font-size: 1rem;
        margin: 0;
    }
    .btn-view {
        background: #667eea;
        color: white;
        border: none;
    }
    .btn-view:hover {
        background: #5a6fd8;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    .btn-edit {
        background: #28a745;
        color: white;
        border: none;
    }
    .btn-edit:hover {
        background: #218838;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    .btn-assign {
        background: #17a2b8;
        color: white;
        border: none;
    }
    .btn-assign:hover {
        background: #138496;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    .empty-state i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    /* Modal Styles */
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .assignment-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    .assignment-item:hover {
        background: #e9ecef;
    }
    .current-assignment {
        background: #e7f3ff;
        border: 2px solid #b3d9ff;
        border-left: 4px solid #667eea;
    }
    .current-assignment:hover {
        background: #cce5ff;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }
</style>

<div class="index-container">
    <div class="container">
        <div class="index-card">
            <div class="index-header">
                <h2><i class="fas fa-book me-2"></i>Lessons Management</h2>
                <p class="mb-0 mt-1 opacity-75">Manage lessons across all branches and courses</p>
            </div>

            <div class="index-body">
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">All Lessons</h4>
                    <div>
                        <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus-circle me-2"></i>Create New Lesson
                        </a>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#assignLessonModal">
                            <i class="fas fa-network-wired me-2"></i>Assign to Branches
                        </button>
                    </div>
                </div>

                <!-- Live Search -->
                <div class="search-container mb-3" style="margin-bottom: 20px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="lessonSearch" placeholder="Search lessons by title, course, teacher, terms... (searches ALL data)">
                        <span class="input-group-text text-muted small"><span id="lessonCount">{{ $lessonDetails->total() ?? 0 }}</span> results</span>
                    </div>
                </div>

                @if($lessonDetails->count() > 0)
                    <div class="table-responsive" id="tableContainer">
                        <table class="table lessons-table">
                            <thead>
                                <tr>
                                    <th>Lesson Title</th>
                                    <th>Weight</th>
                                    <th>Test Marks</th>
                                    <th>Exam Marks</th>
                                    <th>Terms</th>
                                    <th>Branch Assignments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="lessonTableBody">
                                @foreach($lessonDetails as $lesson)
                                    <tr class="lesson-row">
                                        <td>
                                            <strong>{{ $lesson->title }}</strong>
                                            @if($lesson->academicYear)
                                                <br><small class="text-muted">Year: {{ $lesson->academicYear->year }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $lesson->lesson_weight ?? 100 }}%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $lesson->max_test_marks ?? 100 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $lesson->max_exam_marks ?? 100 }}</span>
                                        </td>
                                        <td>
                                            @if($lesson->terms->count() > 0)
                                                @foreach($lesson->terms as $term)
                                                    <span class="badge bg-info">{{ $term->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No terms</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lesson->branchCourses->count() > 0)
                                                @foreach($lesson->branchCourses as $branchCourse)
                                                    @if($branchCourse->branch && $branchCourse->course)
                                                        <span class="assignment-badge">
                                                            {{ $branchCourse->branch->name }}
                                                            - {{ $branchCourse->course->course_name }}
                                                            @if($branchCourse->teacher)
                                                                ({{ $branchCourse->teacher->teacher_name }})
                                                            @endif
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @else
                                                {{-- Show single course assignment for backward compatibility --}}
                                                @if($lesson->course)
                                                    <span class="assignment-badge">
                                                        {{ $lesson->course->course_name }}
                                                        @if($lesson->teacher)
                                                            ({{ $lesson->teacher->teacher_name }})
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('staff.lessons.show', $lesson->id) }}" class="btn btn-action btn-view" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('staff.lessons.edit', $lesson->id) }}" class="btn btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-action btn-assign" 
                                                        onclick="openAssignModal({{ $lesson->id }}, '{{ $lesson->title }}')" 
                                                        title="Assign to Branches">
                                                    <i class="fas fa-network-wired"></i>
                                                </button>
                                                <form action="{{ route('staff.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this lesson? This will permanently delete the lesson and ALL related data including units, marks, attendance, and exam associations. This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action btn-delete" title="Delete" style="background: #dc3545; color: white;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($lessonDetails->hasPages())
                        <div style="padding: 15px; border-top: 1px solid #e9ecef; background: #f8f9fa;" id="paginationContainer">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0">
                                    Showing <strong>{{ $lessonDetails->firstItem() }}</strong> to <strong>{{ $lessonDetails->lastItem() }}</strong> 
                                    of <strong>{{ $lessonDetails->total() }}</strong> lessons
                                </p>
                                <div>
                                    {{ $lessonDetails->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <h4>No Lessons Found</h4>
                        <p class="text-muted">Start by creating your first lesson using the button above.</p>
                        <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle me-2"></i>Create Your First Lesson
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Lesson Modal -->
<div class="modal fade" id="assignLessonModal" tabindex="-1" aria-labelledby="assignLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignLessonModalLabel">
                    <i class="fas fa-network-wired me-2"></i>Assign Lesson to Branches and Courses
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignLessonForm" method="POST" action="{{ route('dos.lessons.assign') }}">
                @csrf
                <input type="hidden" name="lesson_id" id="lessonIdInput">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Selected Lesson:</label>
                        <div class="alert alert-info">
                            <i class="fas fa-book me-2"></i><span id="selectedLessonName">No lesson selected</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Current Assignments:</h6>
                        <div id="currentAssignments">
                            <p class="text-muted">Loading current assignments...</p>
                        </div>
                        <div id="currentAssignmentsForm" style="display:none;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="newAssignments" class="form-label fw-bold">Add New Assignments:</label>
                        <div id="newAssignments">
                            <div class="assignment-item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Branch:</label>
                                        <select name="assignments[0][branch_id]" class="form-select branch-select" required>
                                            <option value="">Select Branch</option>
                                            @foreach($branches ?? \App\Models\Branch::all() as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Course:</label>
                                        <select name="assignments[0][course_id]" class="form-select course-select" required>
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Teacher:</label>
                                        <select name="assignments[0][teacher_id]" class="form-select teacher-select">
                                            <option value="">Select Teacher (Optional)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addAssignmentRow()">
                            <i class="fas fa-plus me-1"></i>Add More Assignments
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteLessonConfirm(document.getElementById('lessonIdInput').value, document.getElementById('selectedLessonName').textContent)">
                        <i class="fas fa-trash me-2"></i>Delete Lesson
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Assignments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let assignmentRowCount = 1;

// Store branches and teachers data for use in JavaScript
const branchesData = {!! json_encode($branches ?? \App\Models\Branch::all()) !!};
const teachersData = {!! json_encode(\App\Models\Teacher::all()) !!};

function openAssignModal(lessonId, lessonTitle) {
    document.getElementById('lessonIdInput').value = lessonId;
    document.getElementById('selectedLessonName').textContent = lessonTitle;
    
    // Load current assignments
    fetch(`/dos/lessons/${lessonId}/assignments`)
        .then(response => response.json())
        .then(data => {
            let currentAssignmentsDiv = document.getElementById('currentAssignments');
            let currentAssignmentsFormDiv = document.getElementById('currentAssignmentsForm');
            
            if (data.assignments && data.assignments.length > 0) {
                let formHtml = '';
                data.assignments.forEach((assignment, index) => {
                    // Build branch options
                    let branchOptions = '<option value="">Select Branch</option>';
                    branchesData.forEach(branch => {
                        const selected = assignment.branch_id == branch.id ? 'selected' : '';
                        branchOptions += `<option value="${branch.id}" ${selected}>${branch.name}</option>`;
                    });
                    
                    // Build teacher options
                    let teacherOptions = '<option value="">Select Teacher (Optional)</option>';
                    if (assignment.teacher_id) {
                        teacherOptions += `<option value="${assignment.teacher_id}" selected>${assignment.teacher_name || 'No Teacher'}</option>`;
                    }
                    
                    formHtml += `
                        <div class="assignment-item current-assignment" data-assignment-id="${assignment.id}">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Branch:</label>
                                    <select name="current_assignments[${index}][branch_id]" class="form-select branch-select-current" data-assignment-id="${assignment.id}" required>
                                        ${branchOptions}
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Course:</label>
                                    <select name="current_assignments[${index}][course_id]" class="form-select course-select-current" data-assignment-id="${assignment.id}" required>
                                        <option value="${assignment.course_id}" selected>${assignment.course_name}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Teacher:</label>
                                    <select name="current_assignments[${index}][teacher_id]" class="form-select teacher-select-current" data-assignment-id="${assignment.id}">
                                        ${teacherOptions}
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeCurrentAssignment('${assignment.id}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                currentAssignmentsDiv.innerHTML = '';
                currentAssignmentsFormDiv.innerHTML = formHtml;
                currentAssignmentsFormDiv.style.display = 'block';
            } else {
                currentAssignmentsDiv.innerHTML = '<p class="text-muted text-center py-3">No current assignments</p>';
                currentAssignmentsFormDiv.innerHTML = '';
                currentAssignmentsFormDiv.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading assignments:', error);
            document.getElementById('currentAssignments').innerHTML = '<p class="text-danger">Error loading assignments</p>';
        });
    
    // Reset new assignments form
    let branchOptions = '<option value="">Select Branch</option>';
    branchesData.forEach(branch => {
        branchOptions += `<option value="${branch.id}">${branch.name}</option>`;
    });
    
    document.getElementById('newAssignments').innerHTML = `
        <div class="assignment-item">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Branch:</label>
                    <select name="assignments[0][branch_id]" class="form-select branch-select" required>
                        ${branchOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Course:</label>
                    <select name="assignments[0][course_id]" class="form-select course-select" required>
                        <option value="">Select Course</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teacher:</label>
                    <select name="assignments[0][teacher_id]" class="form-select teacher-select">
                        <option value="">Select Teacher (Optional)</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    assignmentRowCount = 1;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('assignLessonModal')).show();
}

function addAssignmentRow() {
    const container = document.getElementById('newAssignments');
    const newRow = document.createElement('div');
    newRow.className = 'assignment-item';
    
    // Build branch options
    let branchOptions = '<option value="">Select Branch</option>';
    branchesData.forEach(branch => {
        branchOptions += `<option value="${branch.id}">${branch.name}</option>`;
    });
    
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Branch:</label>
                <select name="assignments[${assignmentRowCount}][branch_id]" class="form-select branch-select" required>
                    ${branchOptions}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Course:</label>
                <select name="assignments[${assignmentRowCount}][course_id]" class="form-select course-select" required>
                    <option value="">Select Course</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Teacher:</label>
                <select name="assignments[${assignmentRowCount}][teacher_id]" class="form-select teacher-select">
                    <option value="">Select Teacher (Optional)</option>
                </select>
                <button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="this.closest('.assignment-item').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    assignmentRowCount++;
}

// Handle branch selection to load courses
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('branch-select') || e.target.classList.contains('branch-select-current')) {
        const branchSelect = e.target;
        const courseSelect = branchSelect.closest('.assignment-item').querySelector('.course-select, .course-select-current');
        const teacherSelect = branchSelect.closest('.assignment-item').querySelector('.teacher-select, .teacher-select-current');
        
        if (branchSelect.value) {
            // Load courses for this branch
            fetch(`/api/branches/${branchSelect.value}/courses`)
                .then(response => response.json())
                .then(courses => {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    courses.forEach(course => {
                        courseSelect.innerHTML += `<option value="${course.id}">${course.course_name}</option>`;
                    });
                    
                    // Clear teacher selection
                    teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                    courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                });
        } else {
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
        }
    }
});

// Handle course selection to load teachers
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('course-select') || e.target.classList.contains('course-select-current')) {
        const courseSelect = e.target;
        const branchSelect = courseSelect.closest('.assignment-item').querySelector('.branch-select, .branch-select-current');
        const teacherSelect = courseSelect.closest('.assignment-item').querySelector('.teacher-select, .teacher-select-current');
        
        if (courseSelect.value && branchSelect.value) {
            // Load teachers for this branch-course combination
            fetch(`/api/branches/${branchSelect.value}/courses/${courseSelect.value}/teachers`)
                .then(response => response.json())
                .then(teachers => {
                    teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                    teachers.forEach(teacher => {
                        teacherSelect.innerHTML += `<option value="${teacher.id}">${teacher.teacher_name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error loading teachers:', error);
                    teacherSelect.innerHTML = '<option value="">Error loading teachers</option>';
                });
        } else {
            teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
        }
    }
});

function removeCurrentAssignment(assignmentId) {
    if (confirm('Are you sure you want to remove this assignment?')) {
        const assignmentDiv = document.querySelector(`.current-assignment[data-assignment-id="${assignmentId}"]`);
        if (assignmentDiv) {
            // Add hidden input to mark for deletion
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `deleted_assignments[]`;
            input.value = assignmentId;
            document.getElementById('assignLessonForm').appendChild(input);
            
            // Remove from display
            assignmentDiv.remove();
            
            // If no more current assignments, show the default message
            const remaining = document.querySelectorAll('.current-assignment').length;
            if (remaining === 0) {
                document.getElementById('currentAssignments').innerHTML = '<p class="text-muted text-center py-3">No current assignments</p>';
                document.getElementById('currentAssignmentsForm').style.display = 'none';
            }
        }
    }
}

function deleteLessonConfirm(lessonId, lessonTitle) {
    if (!lessonId) {
        alert('Error: Lesson ID not found');
        return;
    }
    
    if (confirm(`⚠️ WARNING!\n\nAre you absolutely sure you want to DELETE the lesson "${lessonTitle}"?\n\nThis will permanently delete:\n• The lesson itself\n• All lesson units\n• All student marks\n• All attendance records\n• All exam associations\n\nThis action CANNOT be undone!`)) {
        // Create and submit a hidden form for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('staff.lessons.destroy', ':id') }}`.replace(':id', lessonId);
        
        // Add CSRF token - get from meta tag or form
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                        || document.querySelector('input[name="_token"]')?.value;
        
        if (!csrfToken) {
            alert('Error: Security token not found. Please refresh the page and try again.');
            return;
        }
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add method spoofing
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Live search functionality with AJAX
let searchDebounceTimer;

function performLessonAjaxSearch(searchTerm = '') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const params = new URLSearchParams({
        search: searchTerm,
        ajax: 1
    });
    
    fetch(`{{ route('dos.lessons.index') }}?${params}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');
        
        const newTableBody = newDoc.querySelector('#lessonTableBody');
        const newPagination = newDoc.querySelector('#paginationContainer');
        
        if (newTableBody) {
            document.getElementById('lessonTableBody').innerHTML = newTableBody.innerHTML;
        }
        
        if (newPagination) {
            document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
            attachLessonPaginationHandlers();
        }
        
        // Update result count
        const totalLessons = newDoc.querySelector('#lessonCount');
        if (totalLessons) {
            document.getElementById('lessonCount').textContent = totalLessons.textContent;
        }
    })
    .catch(error => console.error('Error:', error));
}

function attachLessonPaginationHandlers() {
    document.querySelectorAll('#paginationContainer a[href*="page="]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const urlParams = new URLSearchParams(new URL(this.href).search);
            const page = urlParams.get('page');
            const search = document.getElementById('lessonSearch').value || '';
            
            const params = new URLSearchParams({
                search: search,
                page: page,
                ajax: 1
            });
            
            fetch(`{{ route('dos.lessons.index') }}?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                
                const newTableBody = newDoc.querySelector('#lessonTableBody');
                const newPagination = newDoc.querySelector('#paginationContainer');
                
                if (newTableBody) {
                    document.getElementById('lessonTableBody').innerHTML = newTableBody.innerHTML;
                }
                
                if (newPagination) {
                    document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
                    attachLessonPaginationHandlers();
                }
                
                document.querySelector('#tableContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('lessonSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                performLessonAjaxSearch(searchInput.value);
            }, 300);
        });
    }
    
    attachLessonPaginationHandlers();
});

// Live search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('lessonSearch');
    const lessonRows = document.querySelectorAll('.lesson-row');
    const lessonCountSpan = document.getElementById('lessonCount');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            lessonRows.forEach(row => {
                const searchText = row.getAttribute('data-search-text');
                if (searchTerm === '' || searchText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            lessonCountSpan.textContent = visibleCount;
        });
        
        // Initialize count on page load
        lessonCountSpan.textContent = lessonRows.length;
    }
});
</script>
@endsection
