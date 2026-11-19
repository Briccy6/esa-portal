@extends('staff.partials.sidebar_dos')

@section('title', 'Courses Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-book-fill me-2"></i>
            Courses Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="try { exportCourses(); } catch(e) { console.error('Export courses error:', e); alert('Unable to export courses. Please try again.'); }">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="bi bi-plus-circle me-1"></i> Add Course
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $courses->count() ?? 0 }}</h4>
                            <p class="card-text">Total Courses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $courses->where('is_active', 1)->count() ?? 0 }}</h4>
                            <p class="card-text">Active Courses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $courses->where('is_active', 0)->count() ?? 0 }}</h4>
                            <p class="card-text">Inactive Courses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-pause-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $branches->count() ?? 0 }}</h4>
                            <p class="card-text">Branches</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.courses.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="branch_filter" class="form-label">Branch</label>
                        <select name="branch" id="branch_filter" class="form-select">
                            <option value="">All Branches</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Live Search</label>
                        <input type="text" id="search" name="search" class="form-control live-search-input" 
                               value="{{ request('search') }}" placeholder="Search courses...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.courses.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card">
        <div class="card-body">
            <div id="tableContainer">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Branch</th>
                                <th>Duration</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="courseTableBody">
                            @if(isset($courses) && $courses->count() > 0)
                                @foreach($courses as $course)
                                    <tr class="course-row">
                                        <td><strong>{{ $course->course_code ?? 'N/A' }}</strong></td>
                                        <td>{{ $course->course_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ $course->branches->first()->name ?? 'N/A' }}</span></td>
                                        <td>{{ $course->duration ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $course->students_count }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary"
                                                        onclick="try { viewCourse({{ $course->id }}); } catch(e) { console.error('View course error:', e); alert('Unable to view course. Please try again.'); }">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning"
                                                        onclick="try { editCourse({{ $course->id }}); } catch(e) { console.error('Edit course error:', e); alert('Unable to edit course. Please try again.'); }">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"
                                                        onclick="try { deleteCourse({{ $course->id }}); } catch(e) { console.error('Delete course error:', e); alert('Unable to delete course. Please try again.'); }">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">No courses found</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                                <i class="bi bi-plus-circle me-1"></i> Add First Course
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($courses->hasPages())
                    <div class="card-footer bg-white" id="paginationContainer">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">
                                Showing <strong>{{ $courses->firstItem() }}</strong> to <strong>{{ $courses->lastItem() }}</strong> 
                                of <strong>{{ $courses->total() }}</strong> courses
                            </p>
                            <div>
                                {{ $courses->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCourseForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="course_code" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" required>
                        </div>
                        <div class="col-md-6">
                            <label for="course_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @if(isset($branches))
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="duration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 2 years">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Debounce timer
let searchDebounceTimer;

// Live search with AJAX - searches ALL data, not just current page
function performAjaxSearch(searchTerm = '', branch = '', status = '') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const params = new URLSearchParams({
        search: searchTerm,
        branch: branch,
        status: status,
        ajax: 1  // Flag to indicate AJAX request
    });
    
    fetch(`{{ route('dos.courses.index') }}?${params}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the new HTML
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');
        
        // Extract table body
        const newTableBody = newDoc.querySelector('#courseTableBody');
        const newPagination = newDoc.querySelector('#paginationContainer');
        
        if (newTableBody) {
            // Update table body
            document.getElementById('courseTableBody').innerHTML = newTableBody.innerHTML;
        }
        
        if (newPagination) {
            // Update pagination
            document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
            
            // Re-attach pagination link handlers
            attachPaginationHandlers();
        }
    })
    .catch(error => console.error('Error:', error));
}

function attachPaginationHandlers() {
    // Handle pagination links with AJAX
    document.querySelectorAll('#paginationContainer a[href*="page="]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const urlParams = new URLSearchParams(new URL(this.href).search);
            const page = urlParams.get('page');
            const search = document.getElementById('search').value || '';
            const branch = document.getElementById('branch_filter').value || '';
            const status = document.getElementById('status_filter').value || '';
            
            const params = new URLSearchParams({
                search: search,
                branch: branch,
                status: status,
                page: page,
                ajax: 1
            });
            
            fetch(`{{ route('dos.courses.index') }}?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                
                const newTableBody = newDoc.querySelector('#courseTableBody');
                const newPagination = newDoc.querySelector('#paginationContainer');
                
                if (newTableBody) {
                    document.getElementById('courseTableBody').innerHTML = newTableBody.innerHTML;
                }
                
                if (newPagination) {
                    document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
                    attachPaginationHandlers();
                }
                
                // Scroll to table
                document.querySelector('#tableContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const branchFilter = document.getElementById('branch_filter');
    
    // Live search on input
    searchInput.addEventListener('input', function() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            performAjaxSearch(
                searchInput.value,
                branchFilter.value,
                ''
            );
        }, 300); // 300ms debounce
    });
    
    // Filter changes
    branchFilter.addEventListener('change', function() {
        performAjaxSearch(
            searchInput.value,
            branchFilter.value,
            ''
        );
    });
    
    // Attach pagination handlers
    attachPaginationHandlers();
});

function viewCourse(id) {
    window.location.href = `/dos/courses/${id}`;
}

function editCourse(id) {
    window.location.href = `/dos/courses/${id}/edit`;
}

function deleteCourse(id) {
    if(confirm('Are you sure you want to delete this course?')) {
        window.location.href = `/dos/courses/${id}/delete`;
    }
}

function exportCourses() {
    window.location.href = `/dos/courses/export`;
}

document.getElementById('addCourseForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/dos/courses/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Course added successfully!');
            location.reload();
        } else {
            alert('Error adding course: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding course');
    });
});
</script>
@endsection
