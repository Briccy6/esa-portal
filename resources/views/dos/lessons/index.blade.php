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

    /* Assignment Item */
    .assignment-item {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 4px;
    }

    .course-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .teacher-badge {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .teacher-badge.tba {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .action-btn {
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .assignment-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .action-btn {
            width: 100%;
            margin-bottom: 4px;
        }

        .lessons-table thead th,
        .lessons-table tbody td {
            padding: 10px 8px;
            font-size: 0.85rem;
        }
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
    .modal-assignment-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    .modal-assignment-item:hover {
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
                        <a href="{{ route('dos.lessons.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus-circle me-2"></i>Create New Lesson
                        </a>
                        <a href="/dos/teachers" class="btn btn-success me-2">
                            <i class="fas fa-chalkboard-teacher me-2"></i>View Teachers
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
                        <input type="text" class="form-control" id="lessonSearch" placeholder="Search lessons by title, course, teacher, branch, terms... (searches ALL data)">
                        <span class="input-group-text text-muted small"><span id="lessonCount">{{ $lessonDetails->total() ?? 0 }}</span> results</span>
                    </div>
                </div>

                @if($lessonDetails->count() > 0)
                    <div class="table-responsive" id="tableContainer">
                        <table class="table lessons-table">
                            <thead>
                                <tr>
                                    <th>Lesson Title</th>
                                    <th>Assigned Course & Teacher</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="lessonTableBody">
                                @foreach($lessonDetails as $lesson)
                                    @php
                                        // Collect all branch names from branch courses
                                        $branchNames = $lesson->branchCourses->pluck('branch.name')->filter()->implode(' ');
                                        
                                        $searchText = strtolower($lesson->title . ' ' . 
                                            ($lesson->course->course_name ?? '') . ' ' . 
                                            ($lesson->teacher->teacher_name ?? '') . ' ' . 
                                            $lesson->terms->pluck('name')->implode(' ') . ' ' .
                                            $branchNames);
                                    @endphp
                                    <tr class="lesson-row" data-search-text="{{ $searchText }}">
                                        <td>
                                            <strong>{{ $lesson->title }}</strong>
                                        </td>
                                        <td>
                                            @if($lesson->branchCourses->count() > 0)
                                                @foreach($lesson->branchCourses as $branchCourse)
                                                    @if($branchCourse->branch && $branchCourse->course)
                                                        <div class="assignment-item">
                                                            <span class="course-badge">{{ $branchCourse->course->course_code ?? $branchCourse->course->course_name }}</span>
                                                            @if($branchCourse->teacher)
                                                                <span class="teacher-badge">{{ $branchCourse->teacher->teacher_name }}</span>
                                                            @else
                                                                <span class="teacher-badge tba">TBA</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                {{-- Show single course assignment for backward compatibility --}}
                                                @if($lesson->course)
                                                    <div class="assignment-item">
                                                        <span class="course-badge">{{ $lesson->course->course_code ?? $lesson->course->course_name }}</span>
                                                        @if($lesson->teacher)
                                                            <span class="teacher-badge">{{ $lesson->teacher->teacher_name }}</span>
                                                        @else
                                                            <span class="teacher-badge tba">TBA</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('dos.lessons.show', $lesson->id) }}" class="btn btn-sm btn-info action-btn">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="{{ route('dos.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-warning action-btn">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary action-btn" 
                                                        onclick="openAssignModal({{ $lesson->id }}, '{{ $lesson->title }}')">
                                                    <i class="fas fa-network-wired me-1"></i>Assign
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary action-btn" 
                                                        onclick="openUnitsModal({{ $lesson->id }}, '{{ $lesson->title }}')">
                                                    <i class="fas fa-layer-group me-1"></i>Units
                                                </button>
                                                <form action="{{ route('dos.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this lesson? This will permanently delete the lesson and ALL related data including units, marks, attendance, and exam associations. This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger action-btn">
                                                        <i class="fas fa-trash me-1"></i>Delete
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
                        <a href="{{ route('dos.lessons.create') }}" class="btn btn-primary mt-3">
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
                            <div class="modal-assignment-item">
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

<!-- Units Management Modal -->
<div class="modal fade" id="unitsModal" tabindex="-1" aria-labelledby="unitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unitsModalLabel">
                    <i class="fas fa-layer-group me-2"></i>Manage Units
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-book me-2"></i><span id="unitsLessonName">No lesson selected</span>
                </div>

                <!-- Add New Unit Form -->
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Unit</h6>
                    </div>
                    <div class="card-body">
                        <form id="addUnitForm" class="row g-3">
                            <input type="hidden" name="lesson_id" id="unitLessonId">
                            <div class="col-md-4">
                                <label class="form-label">Unit Name</label>
                                <input type="text" name="unit_name" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hours</label>
                                <input type="number" name="hours" class="form-control" required min="1" step="0.5">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Term</label>
                                <select name="term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                    @foreach($terms ?? \App\Models\Term::all() as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Course Level</label>
                                <select name="course_level" class="form-select" required>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Add Unit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing Units List -->
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Existing Units</h6>
                    </div>
                    <div class="card-body">
                        <div id="unitsList">
                            <p class="text-muted">Loading units...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Units Management
function openUnitsModal(lessonId, lessonTitle) {
    document.getElementById('unitLessonId').value = lessonId;
    document.getElementById('unitsLessonName').textContent = lessonTitle;
    
    // Load units for this lesson
    loadUnits(lessonId);
    
    const modal = new bootstrap.Modal(document.getElementById('unitsModal'));
    modal.show();
}

async function loadUnits(lessonId) {
    try {
        const response = await fetch(`/api/lessons/${lessonId}/units`);
        const data = await response.json();
        
        const unitsList = document.getElementById('unitsList');
        
        if (data.length === 0) {
            unitsList.innerHTML = '<p class="text-muted">No units added yet.</p>';
            return;
        }
        
        unitsList.innerHTML = data.map(unit => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                <div>
                    <strong>${unit.unit_name}</strong>
                    <small class="d-block text-muted">${unit.hours} hrs - ${unit.course_level} - ${unit.term ? unit.term.name : 'N/A'}</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-warning me-1" onclick="editUnit(${unit.id}, '${unit.unit_name}', ${unit.hours}, '${unit.course_level}', ${unit.term_id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUnit(${unit.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading units:', error);
        document.getElementById('unitsList').innerHTML = '<p class="text-danger">Failed to load units.</p>';
    }
}

document.getElementById('addUnitForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/lesson-units/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            // Refresh units list
            loadUnits(document.getElementById('unitLessonId').value);
            // Reset form
            this.reset();
            alert('Unit added successfully!');
        } else {
            alert('Failed to add unit. Please try again.');
        }
    } catch (error) {
        console.error('Error adding unit:', error);
        alert('An error occurred. Please try again.');
    }
});

async function deleteUnit(unitId) {
    if (!confirm('Are you sure you want to delete this unit?')) {
        return;
    }
    
    try {
        const response = await fetch(`/lesson-units/${unitId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            // Refresh units list
            loadUnits(document.getElementById('unitLessonId').value);
            alert('Unit deleted successfully!');
        } else {
            alert('Failed to delete unit. Please try again.');
        }
    } catch (error) {
        console.error('Error deleting unit:', error);
        alert('An error occurred. Please try again.');
    }
}

function editUnit(unitId, unitName, hours, courseLevel, termId) {
    const newUnitName = prompt('Edit Unit Name:', unitName);
    if (newUnitName === null) return;
    
    const newHours = prompt('Edit Hours:', hours);
    if (newHours === null) return;
    
    // Add update logic here if needed
    alert('Edit functionality - implement full edit form if needed');
}
</script>
@endsection

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
                    
                    // Build teacher options - load all teachers for this branch-course combination
                    let teacherOptions = '<option value="">Select Teacher (Optional)</option>';
                    teachersData.forEach(teacher => {
                        const selected = assignment.teacher_id == teacher.id ? 'selected' : '';
                        teacherOptions += `<option value="${teacher.id}" ${selected}>${teacher.teacher_name}</option>`;
                    });
                    
                    formHtml += `
                        <div class="modal-assignment-item current-assignment" data-assignment-id="${assignment.id}">
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
        <div class="modal-assignment-item">
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
    newRow.className = 'modal-assignment-item';
    
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
                <button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="this.closest('.modal-assignment-item').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    assignmentRowCount++;
}

// Handle branch selection to load courses and refresh teacher dropdown
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('branch-select') || e.target.classList.contains('branch-select-current')) {
        const branchSelect = e.target;
        const assignmentItem = branchSelect.closest('.assignment-item, .modal-assignment-item');
        const courseSelect = assignmentItem.querySelector('.course-select, .course-select-current');
        const teacherSelect = assignmentItem.querySelector('.teacher-select, .teacher-select-current');
        
        if (branchSelect.value) {
            // Load courses for this branch
            fetch(`/api/branches/${branchSelect.value}/courses`)
                .then(response => response.json())
                .then(courses => {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    courses.forEach(course => {
                        courseSelect.innerHTML += `<option value="${course.id}">${course.course_name}</option>`;
                    });
                    
                    // Refresh teacher dropdown with all teachers (will be filtered when course is selected)
                    teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                    teachersData.forEach(teacher => {
                        teacherSelect.innerHTML += `<option value="${teacher.id}">${teacher.teacher_name}</option>`;
                    });
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
        const assignmentItem = courseSelect.closest('.assignment-item, .modal-assignment-item');
        const branchSelect = assignmentItem.querySelector('.branch-select, .branch-select-current');
        const teacherSelect = assignmentItem.querySelector('.teacher-select, .teacher-select-current');
        
        if (courseSelect.value && branchSelect.value) {
            // Load teachers for this branch-course combination
            fetch(`/api/branches/${branchSelect.value}/courses/${courseSelect.value}/teachers`)
                .then(response => response.json())
                .then(teachers => {
                    // Save current selection
                    const currentSelection = teacherSelect.value;
                    teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                    if (teachers.length > 0) {
                        teachers.forEach(teacher => {
                            const selected = teacher.id == currentSelection ? 'selected' : '';
                            teacherSelect.innerHTML += `<option value="${teacher.id}" ${selected}>${teacher.teacher_name}</option>`;
                        });
                    } else {
                        // If no teachers are assigned to this branch-course, show all teachers as fallback
                        teachersData.forEach(teacher => {
                            const selected = teacher.id == currentSelection ? 'selected' : '';
                            teacherSelect.innerHTML += `<option value="${teacher.id}" ${selected}>${teacher.teacher_name}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading teachers:', error);
                    // Fallback to all teachers on error
                    const currentSelection = teacherSelect.value;
                    teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
                    teachersData.forEach(teacher => {
                        const selected = teacher.id == currentSelection ? 'selected' : '';
                        teacherSelect.innerHTML += `<option value="${teacher.id}" ${selected}>${teacher.teacher_name}</option>`;
                    });
                });
        } else {
            teacherSelect.innerHTML = '<option value="">Select Teacher (Optional)</option>';
        }
    }
});

function removeCurrentAssignment(assignmentId) {
    if (confirm('Are you sure you want to remove this assignment?')) {
        // Delete via AJAX
        fetch(`/dos/lessons/${assignmentId}/delete-assignment`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignmentDiv = document.querySelector(`.current-assignment[data-assignment-id="${assignmentId}"]`);
                if (assignmentDiv) {
                    assignmentDiv.remove();
                    
                    // If no more current assignments, show the default message
                    const remaining = document.querySelectorAll('.current-assignment').length;
                    if (remaining === 0) {
                        document.getElementById('currentAssignments').innerHTML = '<p class="text-muted text-center py-3">No current assignments</p>';
                        document.getElementById('currentAssignmentsForm').style.display = 'none';
                    }
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting assignment. Please try again.');
        });
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
        form.action = `{{ route('dos.lessons.destroy', ':id') }}`.replace(':id', lessonId);
        
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
    document.querySelectorAll('#paginationContainer a').forEach(link => {
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
