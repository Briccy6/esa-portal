@extends('staff.partials.sidebar_dos')

@section('content')
<style>
    .live-search-input {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .live-search-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        background: white;
    }
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        pointer-events: none;
    }
    .search-container {
        position: relative;
        max-width: 400px;
    }
    .no-results-message {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    .highlight-match {
        background-color: #fff3cd;
        font-weight: 600;
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Manage Lessons</h1>
                    <p class="text-muted mb-0">View, edit, and manage all lessons</p>
                </div>
                <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Lesson
                </a>
            </div>
        </div>
    </div>

    @if($lessonDetails->count())
        <div class="row mb-3">
            <div class="col-12">
                <div class="search-container">
                    <input 
                        type="text" 
                        id="lessonSearch" 
                        class="form-control live-search-input" 
                        placeholder="🔍 Search lessons by title, course, or teacher..."
                        autocomplete="off"
                    >
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">All Lessons (<span id="lessonCount">{{ $lessonDetails->count() }}</span>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="lessonsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Units</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="lessonsTableBody">
                                    @foreach($lessonDetails as $lesson)
                                        <tr class="lesson-row" data-search-text="{{ strtolower($lesson->title . ' ' . ($lesson->course->course_name ?? '') . ' ' . ($lesson->teacher->teacher_name ?? '')) }}">
                                            <td class="lesson-id">{{ $lesson->id }}</td>
                                            <td class="lesson-title">{{ $lesson->title }}</td>
                                            <td class="lesson-course">{{ $lesson->course->course_name ?? 'N/A' }}</td>
                                            <td class="lesson-teacher">{{ $lesson->teacher->teacher_name ?? 'N/A' }}</td>
                                            <td>{{ $lesson->units->count() }}</td>
                                            <td>
                                                <a href="{{ route('staff.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="{{ route('staff.lesson-units.create', $lesson->id) }}" class="btn btn-sm btn-outline-success" title="Manage Units">
                                                    <i class="bi bi-diagram-3"></i> Units
                                                </a>
                                                <form action="{{ route('staff.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this lesson? This will permanently delete the lesson and ALL related data including units, marks, attendance, and exam associations. This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="noResults" class="no-results-message" style="display: none;">
                            <i class="bi bi-search" style="font-size: 2rem; color: #ccc; margin-bottom: 15px; display: block;"></i>
                            <p><strong>No lessons found</strong></p>
                            <p class="text-muted">Try adjusting your search terms</p>
                        </div>
                    </div>
                    @if($lessonDetails->hasPages())
                        <div class="card-footer bg-white">
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
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm text-center py-5">
                    <div class="card-body">
                        <h3 class="text-muted">No Lessons Found</h3>
                        <p class="text-muted mb-4">There are no lessons created yet.</p>
                        <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Lesson
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('lessonSearch');
        const tableRows = document.querySelectorAll('.lesson-row');
        const noResults = document.getElementById('noResults');
        const lessonCount = document.getElementById('lessonCount');

        if (!searchInput) return;

        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                const searchText = row.getAttribute('data-search-text');
                
                if (searchText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }

            // Update count
            lessonCount.textContent = visibleCount;
        });

        // Clear search on Escape key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('keyup'));
                this.blur();
            }
        });

        // Focus search input with Ctrl+/
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    });
</script>
@endsection
