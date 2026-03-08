@extends('admin.layout')

@section('title', 'Assign Course to ' . $teacher->id_number)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        Assign Courses to Teacher: {{ $teacher->id_number }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.teachers.storeCourseAssignment', $teacher) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-book me-2"></i>Select Courses
                            </label>
                            
                            <!-- Search Input -->
                            <div class="mb-3">
                                <input type="text" id="courseSearch" class="form-control" placeholder="Search courses...">
                            </div>
                            
                            <!-- Course Selection Container -->
                            <div class="course-selection-container">
                                <!-- Selected Courses Display -->
                                <div class="selected-courses mb-3" id="selectedCoursesDisplay" style="display: none;">
                                    <label class="form-label text-muted small">Selected Courses:</label>
                                    <div class="selected-courses-list" id="selectedCoursesList"></div>
                                </div>
                                
                                <!-- Available Courses -->
                                <div class="available-courses">
                                    <label class="form-label text-muted small">Available Courses:</label>
                                    <div class="courses-grid" id="coursesGrid">
                                        @foreach ($courses as $course)
                                            <div class="course-item" data-course-id="{{ $course->id }}" data-course-name="{{ strtolower($course->course_name) }}">
                                                <div class="course-checkbox">
                                                    <input type="checkbox" 
                                                           name="course_ids[]" 
                                                           value="{{ $course->id }}" 
                                                           id="course_{{ $course->id }}"
                                                           {{ (collect(old('course_ids', $teacher->courses->pluck('id')->toArray()))->contains($course->id)) ? 'checked' : '' }}>
                                                    <label for="course_{{ $course->id }}" class="course-label">
                                                        <div class="course-info">
                                                            <span class="course-name">{{ $course->course_name }}</span>
                                                            @if(isset($course->course_code))
                                                                <small class="course-code text-muted">{{ $course->course_code }}</small>
                                                            @endif
                                                        </div>
                                                        <div class="checkmark">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            @error('course_ids')
                                <div class="alert alert-danger mt-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Assign Courses
                            </button>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Teachers List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-selection-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background-color: #f8f9fa;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
    background-color: white;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.course-item {
    transition: all 0.2s ease;
}

.course-item.hidden {
    display: none;
}

.course-checkbox {
    position: relative;
}

.course-checkbox input[type="checkbox"] {
    display: none;
}

.course-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: white;
    margin-bottom: 0;
}

.course-label:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.15);
    transform: translateY(-1px);
}

.course-checkbox input[type="checkbox"]:checked + .course-label {
    border-color: #28a745;
    background-color: #d4edda;
    box-shadow: 0 2px 8px rgba(40,167,69,0.2);
}

.course-info {
    flex: 1;
}

.course-name {
    display: block;
    font-weight: 500;
    color: #333;
    margin-bottom: 2px;
}

.course-code {
    font-size: 0.85em;
    color: #6c757d;
}

.checkmark {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.course-checkbox input[type="checkbox"]:checked + .course-label .checkmark {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.checkmark i {
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.course-checkbox input[type="checkbox"]:checked + .course-label .checkmark i {
    opacity: 1;
}

.selected-courses-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.selected-course-tag {
    background-color: #007bff;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9em;
    display: flex;
    align-items: center;
    gap: 6px;
}

.selected-course-tag .remove-course {
    cursor: pointer;
    padding: 0;
    background: none;
    border: none;
    color: white;
    font-size: 14px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background-color: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.selected-course-tag .remove-course:hover {
    background-color: rgba(255,255,255,0.3);
}

#courseSearch {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 10px 16px;
}

#courseSearch:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.courses-grid::-webkit-scrollbar {
    width: 6px;
}

.courses-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.courses-grid::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.courses-grid::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

@media (max-width: 768px) {
    .courses-grid {
        grid-template-columns: 1fr;
    }
    
    .course-label {
        padding: 10px 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('courseSearch');
    const coursesGrid = document.getElementById('coursesGrid');
    const selectedCoursesDisplay = document.getElementById('selectedCoursesDisplay');
    const selectedCoursesList = document.getElementById('selectedCoursesList');
    const courseItems = document.querySelectorAll('.course-item');
    const checkboxes = document.querySelectorAll('input[name="course_ids[]"]');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        courseItems.forEach(item => {
            const courseName = item.getAttribute('data-course-name');
            if (courseName.includes(searchTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });

    // Update selected courses display
    function updateSelectedCoursesDisplay() {
        const selectedCourses = [];
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const courseItem = checkbox.closest('.course-item');
                const courseName = courseItem.querySelector('.course-name').textContent;
                selectedCourses.push({
                    id: checkbox.value,
                    name: courseName
                });
            }
        });

        if (selectedCourses.length > 0) {
            selectedCoursesDisplay.style.display = 'block';
            selectedCoursesList.innerHTML = selectedCourses.map(course => 
                `<span class="selected-course-tag">
                    ${course.name}
                    <button type="button" class="remove-course" data-course-id="${course.id}">
                        <i class="fas fa-times"></i>
                    </button>
                </span>`
            ).join('');
        } else {
            selectedCoursesDisplay.style.display = 'none';
        }
    }

    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCoursesDisplay);
    });

    // Handle remove course from selected list
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-course')) {
            const courseId = e.target.closest('.remove-course').getAttribute('data-course-id');
            const checkbox = document.querySelector(`input[value="${courseId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedCoursesDisplay();
            }
        }
    });

    // Initialize display
    updateSelectedCoursesDisplay();
});
</script>
@endsection