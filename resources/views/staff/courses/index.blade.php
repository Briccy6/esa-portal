@extends('dos.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>
                        Courses & Assigned Lessons
                    </h2>
                    <p class="text-muted mb-0">Manage course assignments and teacher allocations</p>
                </div>
                <div class="badge bg-light text-dark fs-6">
                    <i class="fas fa-book me-1"></i>
                    {{ count($courses) }} Course{{ count($courses) != 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search and Filter Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       id="searchInput" 
                                       class="form-control border-start-0" 
                                       placeholder="Search courses, lessons, or teachers..."
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6 text-end mt-2 mt-md-0">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Search works across all columns
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Course Management Overview
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="coursesTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-book text-primary me-2"></i>Course
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-list text-success me-2"></i>Lessons & Assignments
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-users text-info me-2"></i>Teacher Overview
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach($courses as $course)
                                    <tr class="course-row">
                                        <!-- Course Name Column -->
                                        <td class="align-top py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-graduation-cap text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold course-name">{{ $course->course_name }}</h6>
                                                    <small class="text-muted">
                                                        {{ count($course->lessons) }} lesson{{ count($course->lessons) != 1 ? 's' : '' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Lessons Column -->
                                        <td class="align-top py-4">
                                            <div class="lesson-list">
                                                @if(count($course->lessons) > 0)
                                                    @foreach($course->lessons as $lesson)
                                                        <div class="lesson-item mb-3 p-3 bg-light rounded border-start border-4 border-primary">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-2 lesson-title">{{ $lesson->title }}</h6>
                                                                    @if($lesson->teacher)
                                                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                                            <i class="fas fa-user-check me-1"></i>
                                                                            <span class="teacher-name">{{ $lesson->teacher->teacher_name }}</span>
                                                                        </span>
                                                                    @else
                                                                        <form method="POST" action="{{ route('dos.courses.assignLesson') }}" class="assignment-form">
                                                                            @csrf
                                                                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                                                                            <div class="row g-2 align-items-center">
                                                                                <div class="col-md-8">
                                                                                    <select name="teacher_id" class="form-select form-select-sm" required>
                                                                                        <option value="">Select Teacher</option>
                                                                                        @foreach($teachers as $teacher)
                                                                                            <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                                                                        <i class="fas fa-plus me-1"></i>Assign
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center text-muted py-3">
                                                        <i class="fas fa-inbox fs-2 mb-2"></i>
                                                        <p class="mb-0">No lessons available</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Teachers Overview Column -->
                                        <td class="align-top py-4">
                                            <div class="teacher-overview">
                                                @if(count($course->teachers) > 0)
                                                    @foreach($course->teachers as $teacher)
                                                        <div class="teacher-section mb-3">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div class="bg-info bg-opacity-10 rounded-circle p-1 me-2">
                                                                    <i class="fas fa-user text-info"></i>
                                                                </div>
                                                                <h6 class="mb-0 teacher-name">{{ $teacher->teacher_name }}</h6>
                                                            </div>
                                                            <div class="ms-4">
                                                                @php
                                                                    $teacherLessons = $course->lessons->where('teacher_id', $teacher->id);
                                                                @endphp
                                                                @if(count($teacherLessons) > 0)
                                                                    <div class="lessons-list">
                                                                        @foreach($teacherLessons as $lesson)
                                                                            <span class="badge bg-light text-dark me-1 mb-1 lesson-title">
                                                                                <i class="fas fa-book-open me-1"></i>{{ $lesson->title }}
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        {{ count($teacherLessons) }} lesson{{ count($teacherLessons) != 1 ? 's' : '' }} assigned
                                                                    </small>
                                                                @else
                                                                    <small class="text-muted fst-italic">No lessons assigned yet</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center text-muted">
                                                        <i class="fas fa-user-slash mb-2"></i>
                                                        <p class="mb-0 small">No teachers assigned</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- No Results Message -->
                    <div id="noResults" class="text-center py-5 d-none">
                        <i class="fas fa-search fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No results found</h5>
                        <p class="text-muted mb-0">Try adjusting your search terms</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .course-row:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .lesson-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .lesson-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .assignment-form {
        background: rgba(255,193,7,0.1);
        border-radius: 0.375rem;
        padding: 0.75rem;
        border-left: 4px solid #ffc107;
    }
    
    .teacher-section {
        border-left: 3px solid #17a2b8;
        padding-left: 1rem;
        margin-left: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .lesson-item {
            margin-bottom: 1rem;
        }
        
        .assignment-form .row {
            flex-direction: column;
        }
        
        .assignment-form .col-md-4 {
            margin-top: 0.5rem;
        }
    }
</style>

<!-- Live Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const noResults = document.getElementById('noResults');
    const rows = tableBody.querySelectorAll('.course-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleRows = 0;

        rows.forEach(function(row) {
            const courseText = row.querySelector('.course-name').textContent.toLowerCase();
            const lessonTitles = Array.from(row.querySelectorAll('.lesson-title'))
                .map(el => el.textContent.toLowerCase())
                .join(' ');
            const teacherNames = Array.from(row.querySelectorAll('.teacher-name'))
                .map(el => el.textContent.toLowerCase())
                .join(' ');
            
            const rowText = courseText + ' ' + lessonTitles + ' ' + teacherNames;
            
            if (searchTerm === '' || rowText.includes(searchTerm)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleRows === 0 && searchTerm !== '') {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    });

    // Add loading state to assignment forms
    const assignmentForms = document.querySelectorAll('.assignment-form');
    assignmentForms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Assigning...';
            submitBtn.disabled = true;
            
            // Reset after 3 seconds if form doesn't redirect
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    });
});
</script>
@endsection