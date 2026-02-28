@extends('admin.layout')

@section('title', 'Teachers List')

@section('content')
<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-users me-3"></i>
                    Teachers Management
                </h1>
                <p class="page-subtitle">Manage your teaching staff and course assignments</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>
                    Add Teacher
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert-container">
                <div class="alert alert-success">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-content">
                        <h5 class="alert-title">Success!</h5>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Temporary Password Alert -->
        @if(session('temp_password'))
            <div class="alert alert-info">
                Temporary password: <strong>{{ session('temp_password') }}</strong>
            </div>
        @endif

        <!-- Controls Section -->
        <div class="controls-card">
            <div class="controls-header">
                <h3 class="controls-title">
                    <i class="fas fa-filter me-2"></i>
                    Search & Filter
                </h3>
            </div>
            <div class="controls-body">
                <div class="search-controls">
                    <div class="search-group">
                        <label for="searchInput" class="form-label">Search Teachers</label>
                        <div class="input-group">
                            <span class="input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, phone, or education level...">
                        </div>
                    </div>
                    <div class="action-group">
                        <button id="exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>
                            Export to Excel
                        </button>
                        <button id="clearSearch" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers Table -->
        @if($teachers->count())
            <div class="table-card">
                <div class="table-header">
                    <h3 class="table-title">
                        <i class="fas fa-table me-2"></i>
                        Teachers List
                        <span class="record-count">(<span id="recordCount">{{ $teachers->count() }}</span> records)</span>
                    </h3>
                </div>
                <div class="table-wrapper">
                    <table class="table" id="teachersTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="telephone">
                                    <span>Teacher Names</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </th>
                                <th class="sortable" data-column="email">
                                    <span>Branch</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </th>
                                <th class="sortable" data-column="level_of_education">
                                    <span>Department</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </th>
                                <th>Assigned Courses</th>
                                <th class="actions-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                            <tr>
                                <td>
                                    <div class="cell-content">
                                        @if($teacher->photo)
                                            <img src="data:image/jpeg;base64,{{ $teacher->photo }}" alt="Photo" width="32" height="32" style="border-radius:50%;object-fit:cover;margin-right:8px;">
                                        @endif
                                        <span>{{ $teacher->teacher_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content">
                                        <i class="fas fa-envelope cell-icon"></i>
                                        @if($teacher->branches && $teacher->branches->count())
                                            {{ $teacher->branches->pluck('name')->join(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content">
                                        <i class="fas fa-graduation-cap cell-icon"></i>
                                        {{ $teacher->department }}
                                    </div>
                                </td>
                                <td>
                                    @if($teacher->courses->count())
                                        <div class="courses-list">
                                            @foreach($teacher->courses as $course)
                                                <span class="course-badge">{{ $course->abbreviation }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="no-assignment">
                                            <i class="fas fa-minus-circle me-1"></i>
                                            No courses assigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.teachers.assignCourse', $teacher) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Assign Course"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="{{ route('teachers.edit', $teacher) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit Teacher"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.teachers.resetPassword', $teacher->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Reset password for this teacher?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Reset Password" data-bs-toggle="tooltip">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </form>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-btn" 
                                                data-teacher-id="{{ $teacher->id }}"
                                                data-teacher-email="{{ $teacher->email }}"
                                                title="Delete Teacher"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $teachers->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="empty-title">No Teachers Found</h3>
                <p class="empty-description">Start by adding your first teacher to the system.</p>
                <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>
                    Add Your First Teacher
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the teacher: <strong id="teacherEmail"></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Teacher
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Page Container */
    .page-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 0;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .header-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .page-subtitle {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    /* Content Wrapper */
    .content-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem 2rem;
    }

    /* Alert Styles */
    .alert-container {
        margin-bottom: 2rem;
    }

    .alert {
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-icon {
        font-size: 1.5rem;
        margin-top: 0.2rem;
    }

    .alert-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }

    /* Controls Card */
    .controls-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .controls-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .controls-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .controls-body {
        padding: 2rem;
    }

    .search-controls {
        display: flex;
        gap: 2rem;
        align-items: end;
        flex-wrap: wrap;
    }

    .search-group {
        flex: 1;
        min-width: 300px;
    }

    .action-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: block;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: stretch;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
        font-size: 1.1rem;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.8rem 1rem 0.8rem 3rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
        width: 100%;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: white;
        outline: none;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .table-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .record-count {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 400;
        margin-left: 0.5rem;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: #f8f9fa;
        padding: 1.2rem 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #e9ecef;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody td {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    .sortable:hover {
        background: #e9ecef;
    }

    .sort-icon {
        margin-left: 0.5rem;
        opacity: 0.5;
        transition: all 0.3s ease;
    }

    .sortable.sorted .sort-icon {
        opacity: 1;
        color: #667eea;
    }

    .cell-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cell-icon {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .courses-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .course-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .no-assignment {
        color: #6c757d;
        font-style: italic;
        display: flex;
        align-items: center;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .actions-column {
        width: 150px;
        text-align: center;
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }

    .btn:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-success:hover {
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        color: white;
    }

    .btn-outline-secondary {
        background: transparent;
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }

    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #212529;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .empty-icon {
        font-size: 4rem;
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .empty-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .empty-description {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 2rem;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 2rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1.5rem 2rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .search-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .search-group {
            min-width: auto;
        }

        .action-group {
            justify-content: stretch;
        }

        .action-group .btn {
            flex: 1;
        }

        .content-wrapper {
            padding: 0 1rem 2rem;
        }

        .controls-body {
            padding: 1.5rem;
        }

        .table-wrapper {
            border-radius: 0;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.3rem;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .table {
            font-size: 0.9rem;
        }

        .table thead th,
        .table tbody td {
            padding: 0.8rem 1rem;
        }
    }

    /* Animations */
    .table-card,
    .controls-card {
        animation: slideInUp 0.6s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Loading States */
    .loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid #667eea;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#teachersTable tbody tr');
    const recordCount = document.getElementById('recordCount');
    let visibleCount = tableRows.length;

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        visibleCount = 0;

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        recordCount.textContent = visibleCount;
    });

    // Clear search
    document.getElementById('clearSearch').addEventListener('click', function() {
        searchInput.value = '';
        tableRows.forEach(row => {
            row.style.display = '';
        });
        recordCount.textContent = tableRows.length;
    });

    // Sort functionality
    const sortableHeaders = document.querySelectorAll('.sortable');
    let sortColumn = '';
    let sortDirection = 'asc';

    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.column;
            
            // Reset other headers
            sortableHeaders.forEach(h => {
                if (h !== this) {
                    h.classList.remove('sorted');
                    h.querySelector('.sort-icon').className = 'fas fa-sort sort-icon';
                }
            });

            // Toggle sort direction
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortDirection = 'asc';
            }
            
            sortColumn = column;
            this.classList.add('sorted');
            
            // Update icon
            const icon = this.querySelector('.sort-icon');
            icon.className = `fas fa-sort-${sortDirection === 'asc' ? 'up' : 'down'} sort-icon`;

            // Sort table
            sortTable(column, sortDirection);
        });
    });

    function sortTable(column, direction) {
        const tbody = document.querySelector('#teachersTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(column) {
                case 'telephone':
                    aValue = a.cells[0].textContent.trim();
                    bValue = b.cells[0].textContent.trim();
                    break;
                case 'email':
                    aValue = a.cells[1].textContent.trim();
                    bValue = b.cells[1].textContent.trim();
                    break;
                case 'level_of_education':
                    aValue = a.cells[2].textContent.trim();
                    bValue = b.cells[2].textContent.trim();
                    break;
            }
            
            if (direction === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        // Re-append sorted rows
        rows.forEach(row => tbody.appendChild(row));
    }

    // Export to Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        const table = document.getElementById('teachersTable');
        const workbook = XLSX.utils.table_to_book(table, {sheet: "Teachers"});
        XLSX.writeFile(workbook, `teachers_list_${new Date().toISOString().split('T')[0]}.xlsx`);
    });

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const teacherEmailSpan = document.getElementById('teacherEmail');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const teacherId = this.dataset.teacherId;
            const teacherEmail = this.dataset.teacherEmail;
            
            deleteForm.action = `/admin/teachers/${teacherId}`;
            teacherEmailSpan.textContent = teacherEmail;
            deleteModal.show();
        });
    });
});
</script>

<!-- Include XLSX library for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endsection