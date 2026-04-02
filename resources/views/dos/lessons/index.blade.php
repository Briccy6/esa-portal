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
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 0 2px;
    }
    .btn-view {
        background: #667eea;
        color: white;
        border: none;
    }
    .btn-view:hover {
        background: #5a6fd8;
        transform: translateY(-1px);
    }
    .btn-edit {
        background: #28a745;
        color: white;
        border: none;
    }
    .btn-edit:hover {
        background: #218838;
        transform: translateY(-1px);
    }
    .btn-assign {
        background: #17a2b8;
        color: white;
        border: none;
    }
    .btn-assign:hover {
        background: #138496;
        transform: translateY(-1px);
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

                @if($lessonDetails->count() > 0)
                    <div class="table-responsive">
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
                            <tbody>
                                @foreach($lessonDetails as $lesson)
                                    <tr>
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
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

function openAssignModal(lessonId, lessonTitle) {
    document.getElementById('lessonIdInput').value = lessonId;
    document.getElementById('selectedLessonName').textContent = lessonTitle;
    
    // Load current assignments
    fetch(`/dos/lessons/${lessonId}/assignments`)
        .then(response => response.json())
        .then(data => {
            let currentHtml = '';
            if (data.assignments && data.assignments.length > 0) {
                data.assignments.forEach(assignment => {
                    currentHtml += `
                        <div class="assignment-item">
                            <strong>${assignment.branch_name} - ${assignment.course_name}</strong>
                            ${assignment.teacher_name ? `<br><small class="text-muted">Teacher: ${assignment.teacher_name}</small>` : ''}
                        </div>
                    `;
                });
            } else {
                currentHtml = '<p class="text-muted">No current assignments</p>';
            }
            document.getElementById('currentAssignments').innerHTML = currentHtml;
        })
        .catch(error => {
            console.error('Error loading assignments:', error);
            document.getElementById('currentAssignments').innerHTML = '<p class="text-danger">Error loading assignments</p>';
        });
    
    // Reset new assignments form
    document.getElementById('newAssignments').innerHTML = `
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
    `;
    assignmentRowCount = 1;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('assignLessonModal')).show();
}

function addAssignmentRow() {
    const container = document.getElementById('newAssignments');
    const newRow = document.createElement('div');
    newRow.className = 'assignment-item';
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Branch:</label>
                <select name="assignments[${assignmentRowCount}][branch_id]" class="form-select branch-select" required>
                    <option value="">Select Branch</option>
                    @foreach($branches ?? \App\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
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
    if (e.target.classList.contains('branch-select')) {
        const branchSelect = e.target;
        const courseSelect = branchSelect.closest('.assignment-item').querySelector('.course-select');
        const teacherSelect = branchSelect.closest('.assignment-item').querySelector('.teacher-select');
        
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
    if (e.target.classList.contains('course-select')) {
        const courseSelect = e.target;
        const branchSelect = courseSelect.closest('.assignment-item').querySelector('.branch-select');
        const teacherSelect = courseSelect.closest('.assignment-item').querySelector('.teacher-select');
        
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
</script>
@endsection
