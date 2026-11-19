@extends('dos.layout')

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
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Manage Units</h1>
                    <p class="text-muted mb-0">View and manage all lesson units</p>
                </div>
            </div>
        </div>
    </div>

    @if($lessonDetails->count())
        <div class="row mb-3">
            <div class="col-12">
                <div class="search-container">
                    <input 
                        type="text" 
                        id="unitSearch" 
                        class="form-control live-search-input" 
                        placeholder="🔍 Search units by name, lesson, course, or teacher..."
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
                        <h5 class="card-title mb-0">All Units (<span id="unitCount">{{ $lessonDetails->total() }}</span>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="tableContainer">
                            <table class="table table-striped" id="unitsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Unit Name</th>
                                        <th>Lesson</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Hours</th>
                                        <th>Term</th>
                                        <th>Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="unitsTableBody">
                                    @foreach($lessonDetails as $unit)
                                        <tr class="unit-row">
                                            <td class="unit-id">{{ $unit->id }}</td>
                                            <td class="unit-name">{{ $unit->unit_name }}</td>
                                            <td>
                                                @if($unit->lesson)
                                                    <a href="{{ route('staff.lessons.edit', $unit->lesson->id) }}" class="text-decoration-none">
                                                        {{ $unit->lesson->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No lesson</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($unit->lesson && $unit->lesson->course)
                                                    {{ $unit->lesson->course->course_name ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($unit->lesson && $unit->lesson->teacher)
                                                    {{ $unit->lesson->teacher->teacher_name ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $unit->hours }}</td>
                                            <td>
                                                @if($unit->term)
                                                    {{ $unit->term->name }}
                                                    @if($unit->term->academicYear)
                                                        <small class="text-muted">({{ $unit->term->academicYear->year }})</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No term</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $unit->course_level == 'beginner' ? 'success' : 
                                                    ($unit->course_level == 'intermediate' ? 'warning' : 'danger') 
                                                }}">
                                                    {{ ucfirst($unit->course_level) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('staff.lesson-units.edit', $unit->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('staff.lesson-units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this unit?')" title="Delete">
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
                            <p><strong>No units found</strong></p>
                            <p class="text-muted">Try adjusting your search terms</p>
                        </div>
                    </div>
                    @if($lessonDetails->hasPages())
                        <div class="card-footer bg-white" id="paginationContainer">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0">
                                    Showing <strong>{{ $lessonDetails->firstItem() }}</strong> to <strong>{{ $lessonDetails->lastItem() }}</strong> 
                                    of <strong>{{ $lessonDetails->total() }}</strong> units
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
                        <div class="mb-4">
                            <i class="bi bi-diagram-3 display-1 text-muted"></i>
                        </div>
                        <h3 class="text-muted">No Units Found</h3>
                        <p class="text-muted mb-4">There are no units created yet.</p>
                        <a href="{{ route('staff.lessons.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Manage Lessons to Add Units
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
let searchDebounceTimer;

function performUnitAjaxSearch(searchTerm = '') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const params = new URLSearchParams({
        search: searchTerm,
        ajax: 1
    });
    
    fetch(`{{ route('staff.lesson-units.index') }}?${params}`, {
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
        
        const newTableBody = newDoc.querySelector('#unitsTableBody');
        const newPagination = newDoc.querySelector('#paginationContainer');
        
        if (newTableBody) {
            document.getElementById('unitsTableBody').innerHTML = newTableBody.innerHTML;
        }
        
        if (newPagination) {
            document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
            attachUnitPaginationHandlers();
        }
        
        const totalUnits = newDoc.querySelector('#unitCount');
        if (totalUnits) {
            document.getElementById('unitCount').textContent = totalUnits.textContent;
        }
    })
    .catch(error => console.error('Error:', error));
}

function attachUnitPaginationHandlers() {
    document.querySelectorAll('#paginationContainer a[href*="page="]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const urlParams = new URLSearchParams(new URL(this.href).search);
            const page = urlParams.get('page');
            const search = document.getElementById('unitSearch').value || '';
            
            const params = new URLSearchParams({
                search: search,
                page: page,
                ajax: 1
            });
            
            fetch(`{{ route('staff.lesson-units.index') }}?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                
                const newTableBody = newDoc.querySelector('#unitsTableBody');
                const newPagination = newDoc.querySelector('#paginationContainer');
                
                if (newTableBody) {
                    document.getElementById('unitsTableBody').innerHTML = newTableBody.innerHTML;
                }
                
                if (newPagination) {
                    document.getElementById('paginationContainer').innerHTML = newPagination.innerHTML;
                    attachUnitPaginationHandlers();
                }
                
                document.querySelector('#tableContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('unitSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                performUnitAjaxSearch(searchInput.value);
            }, 300);
        });
        
        // Clear search on Escape key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                performUnitAjaxSearch('');
                this.blur();
            }
        });
    }
    
    attachUnitPaginationHandlers();
});
</script>
@endsection
