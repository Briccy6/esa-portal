@extends('dos.layout')

@section('content')
<style>
    .show-container {
        background: #f1f5f9;
        min-height: 100vh;
        padding: 30px 0;
    }
    .show-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .show-header {
        background: #1e293b;
        color: white;
        padding: 30px;
        text-align: center;
    }
    .show-header h2 { margin: 0; font-size: 1.75rem; font-weight: 600; }
    .show-body { padding: 30px; }
    .section-title {
        color: #3b82f6;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 20px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #3b82f6;
    }
    .info-box {
        background: #f8fafc;
        border-left: 4px solid #3b82f6;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 0 8px 8px 0;
    }
    .info-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
    }
    .info-value {
        color: #64748b;
        margin-bottom: 0;
    }
    .unit-item {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .unit-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .unit-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 10px;
    }
    .unit-name {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }
    .unit-hours {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    .term-badge {
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 10px;
    }
    .branch-course-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .branch-course-table thead {
        background: #f8f9fa;
    }
    .branch-course-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }
    .branch-course-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }
    .branch-course-table tbody tr:hover {
        background: #f8f9fa;
    }
    .branch-badge {
        background: #007bff;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .course-badge {
        background: #6c757d;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .teacher-badge {
        background: #17a2b8;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .btn-back {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        color: white;
        text-decoration: none;
    }
    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="show-container">
    <div class="show-card">
        <div class="show-header">
            <h2>Lesson Details</h2>
            <p class="mb-0">{{ $lesson->title }}</p>
        </div>
        
        <div class="show-body">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Lesson Information -->
            <div class="info-box">
                <h5 class="mb-3">Lesson Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">Course</div>
                        <div class="info-value">{{ $lesson->course->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Teacher</div>
                        <div class="info-value">
                            {{ $lesson->teacher ? $lesson->teacher->teacher_name : 'Not assigned' }}
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="info-label">Max Test Marks</div>
                        <div class="info-value">{{ $lesson->max_test_marks }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Max Exam Marks</div>
                        <div class="info-value">{{ $lesson->max_exam_marks }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Lesson Weight</div>
                        <div class="info-value">{{ $lesson->lesson_weight }}</div>
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="mb-4">
                <h6 class="section-title">Associated Terms</h6>
                @if($lesson->terms->count() > 0)
                    <div>
                        @foreach($lesson->terms as $term)
                            <span class="term-badge">{{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->name . ')' : '' }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No terms assigned to this lesson.</p>
                @endif
            </div>

            <!-- Multi-Branch Assignments (if applicable) -->
            @if($lesson->branchCourses && $lesson->branchCourses->count() > 0)
                <div class="mb-4">
                    <h6 class="section-title">Branch-Course Assignments</h6>
                    <p class="text-muted small mb-3">This lesson is assigned to the following branches and courses:</p>
                    <table class="branch-course-table">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Course</th>
                                <th>Assigned Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lesson->branchCourses as $assignment)
                                <tr>
                                    <td>
                                        <span class="branch-badge">
                                            {{ $assignment->branch ? $assignment->branch->name : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="course-badge">
                                            {{ $assignment->course ? $assignment->course->course_name : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignment->teacher)
                                            <span class="teacher-badge">
                                                {{ $assignment->teacher->teacher_name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Lesson Units -->
            <div class="mb-4">
                <h6 class="section-title">Lesson Units</h6>
                @if($lesson->units && $lesson->units->count() > 0)
                    @foreach($lesson->units as $unit)
                        <div class="unit-item">
                            <div class="unit-header">
                                <div class="unit-name">{{ $unit->unit_name }}</div>
                                <div class="unit-hours">{{ $unit->hours }} hours</div>
                            </div>
                            <div class="text-muted small">
                                Term: {{ $unit->term->name ?? 'N/A' }} | Level: {{ ucfirst($unit->course_level ?? 'N/A') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-3">No units have been added to this lesson yet.</p>
                        <a href="{{ route('staff.lesson.details.create', $lesson->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Lesson Units
                        </a>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('staff.lessons.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Lessons
                </a>
                <div>
                    <a href="{{ route('staff.lessons.edit', $lesson->id) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit"></i> Edit Lesson
                    </a>
                    <button type="button" class="btn btn-secondary me-2" onclick="openUnitsModal({{ $lesson->id }}, '{{ $lesson->title }}')">
                        <i class="fas fa-layer-group"></i> Manage Units
                    </button>
                    <form action="{{ route('staff.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this lesson? This will permanently delete the lesson and ALL related data including units, marks, attendance, and exam associations. This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Delete Lesson
                        </button>
                    </form>
                </div>
            </div>
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
    
    alert('Edit functionality - implement full edit form if needed');
}
</script>
@endsection
