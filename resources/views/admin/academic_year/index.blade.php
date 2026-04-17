@extends('admin.layout')

@section('title', 'Academic Years')

@section('content')
<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-calendar-alt me-3"></i>
                    Academic Years
                </h1>
                <p class="page-subtitle">Manage and organize academic years for your institution</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.academic_year.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Add New Year
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert-container">
                <div class="alert alert-success">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-content">
                        <h5 class="alert-title">Success!</h5>
                        <p class="alert-message">{{ session('success') }}</p>
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Table Card -->
        <div class="table-card">
            <!-- Table Header -->
            <div class="table-header">
                <div class="table-title">
                    <h3>All Academic Years</h3>
                    <span class="record-count">{{ count($academicYears) }} record(s) found</span>
                </div>
                
                <!-- Search and Filter Controls -->
                <div class="table-controls">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Search academic years..." class="search-input">
                    </div>
                    
                    <div class="filter-group">
                        <select id="statusFilter" class="filter-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="ended">Ended</option>
                        </select>
                        
                        <select id="sortBy" class="filter-select">
                            <option value="year">Sort by Year</option>
                            <option value="start_date">Sort by Start Date</option>
                            <option value="end_date">Sort by End Date</option>
                            <option value="status">Sort by Status</option>
                        </select>
                        
                        <button class="filter-btn" onclick="resetFilters()">
                            <i class="fas fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="data-table" id="academicYearsTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="year">
                                    <div class="th-content">
                                        Academic Year
                                        <i class="fas fa-sort sort-icon"></i>
                                    </div>
                                </th>
                                <th class="sortable" data-sort="start_date">
                                    <div class="th-content">
                                        Starting Date
                                        <i class="fas fa-sort sort-icon"></i>
                                    </div>
                                </th>
                                <th class="sortable" data-sort="end_date">
                                    <div class="th-content">
                                        Expiry Date
                                        <i class="fas fa-sort sort-icon"></i>
                                    </div>
                                </th>
                                <th class="sortable" data-sort="status">
                                    <div class="th-content">
                                        Status
                                        <i class="fas fa-sort sort-icon"></i>
                                    </div>
                                </th>
                                <th class="no-sort">
                                    <div class="th-content">
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicYears as $year)
                                <tr class="table-row" data-year="{{ $year->year }}" data-status="{{ $year->is_ongoing ? 'active' : 'ended' }}">
                                    <td class="year-cell">
                                        <div class="cell-content">
                                            <span class="year-badge">{{ $year->year }}</span>
                                        </div>
                                    </td>
                                    <td class="date-cell" data-date="{{ $year->starting_date }}">
                                        <div class="cell-content">
                                            <i class="fas fa-play-circle me-2 text-success"></i>
                                            {{ \Carbon\Carbon::parse($year->starting_date)->format('M d, Y') }}
                                            <small class="date-detail">{{ \Carbon\Carbon::parse($year->starting_date)->format('l') }}</small>
                                        </div>
                                    </td>
                                    <td class="date-cell" data-date="{{ $year->expiry_date }}">
                                        <div class="cell-content">
                                            <i class="fas fa-stop-circle me-2 text-danger"></i>
                                            {{ \Carbon\Carbon::parse($year->expiry_date)->format('M d, Y') }}
                                            <small class="date-detail">{{ \Carbon\Carbon::parse($year->expiry_date)->format('l') }}</small>
                                        </div>
                                    </td>
                                    <td class="status-cell">
                                        <div class="cell-content">
                                            @if($year->is_ongoing)
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-check-circle"></i>
                                                    Active
                                                </span>
                                            @else
                                                <span class="status-badge status-ended">
                                                    <i class="fas fa-times-circle"></i>
                                                    Ended
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="action-cell">
                                        <div class="action-group">
                                            <a href="{{ route('admin.academic_year.edit', $year->id) }}" class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="action-btn delete-btn" 
                                                    onclick="confirmDelete({{ $year->id }}, '{{ $year->year }}')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                            @empty
                                <tr class="empty-row">
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times empty-icon"></i>
                                            <h4 class="empty-title">No Academic Years Found</h4>
                                            <p class="empty-message">Start by creating your first academic year</p>
                                            <a href="{{ route('admin.academic_year.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>
                                                Create Academic Year
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Footer -->
            <div class="table-footer">
                <div class="showing-info">
                    Showing <span id="showingCount">{{ count($academicYears) }}</span> of <span id="totalCount">{{ count($academicYears) }}</span> entries
                </div>
                <div class="table-actions">
                    <button class="btn btn-outline-secondary" onclick="exportData()">
                        <i class="fas fa-download me-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the academic year <strong id="deleteYear"></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete
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

    .header-actions .btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        padding: 0.8rem 2rem;
    }

    .header-actions .btn:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
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
        align-items: center;
        gap: 1rem;
        position: relative;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-icon {
        font-size: 1.5rem;
    }

    .alert-title {
        font-weight: 600;
        margin: 0 0 0.3rem 0;
    }

    .alert-message {
        margin: 0;
    }

    .alert-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s;
    }

    .alert-close:hover {
        opacity: 1;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Table Header */
    .table-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-title h3 {
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .record-count {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.3rem;
        display: block;
    }

    .table-controls {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
    }

    .search-input {
        padding: 0.7rem 1rem 0.7rem 2.5rem;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        width: 300px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: white;
    }

    .search-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .filter-select {
        padding: 0.7rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        background: white;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #667eea;
        outline: none;
    }

    .filter-btn {
        padding: 0.7rem 1rem;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        background: #5a6268;
        transform: rotate(90deg);
    }

    /* Table Styles */
    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .data-table th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 1.2rem 1rem;
        text-align: left;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .th-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
    }

    .sort-icon {
        opacity: 0.5;
        transition: all 0.3s ease;
    }

    .sortable:hover .sort-icon {
        opacity: 1;
    }

    .data-table td {
        padding: 1.2rem 1rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .table-row {
        transition: all 0.3s ease;
    }

    .table-row:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .cell-content {
        display: flex;
        align-items: center;
        flex-direction: column;
        align-items: flex-start;
    }

    .year-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .date-detail {
        color: #6c757d;
        font-size: 0.8rem;
        margin-top: 0.2rem;
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .status-active {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .status-ended {
        background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
        color: #721c24;
    }

    .action-group {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .edit-btn {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: white;
    }

    .edit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
    }

    .delete-btn {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .delete-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-title {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .empty-message {
        margin-bottom: 2rem;
    }

    /* Table Footer */
    .table-footer {
        background: #f8f9fa;
        padding: 1.5rem 2rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .showing-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    /* Button Styles */
    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
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

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1.5rem;
        gap: 0.5rem;
    }

    /* Hidden rows */
    .table-row.hidden {
        display: none;
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

        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .table-controls {
            justify-content: center;
        }

        .search-input {
            width: 100%;
        }

        .content-wrapper {
            padding: 0 1rem 2rem;
        }

        .data-table {
            font-size: 0.85rem;
        }

        .data-table th,
        .data-table td {
            padding: 0.8rem 0.5rem;
        }

        .table-footer {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .action-group {
            flex-direction: column;
        }

        .action-btn {
            width: 35px;
            height: 35px;
        }
    }
</style>

<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const year = row.querySelector('.year-cell').textContent.toLowerCase();
            const shouldShow = year.includes(searchValue);
            
            if (shouldShow) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('showingCount').textContent = visibleCount;
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        const filterValue = this.value;
        const rows = document.querySelectorAll('.table-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            const shouldShow = !filterValue || status === filterValue;
            
            if (shouldShow && !row.classList.contains('hidden')) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('showingCount').textContent = visibleCount;
    });

    // Sort functionality
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            const tbody = document.querySelector('.data-table tbody');
            const rows = Array.from(tbody.querySelectorAll('.table-row'));
            
            // Toggle sort direction
            const isAsc = this.classList.contains('sort-asc');
            
            // Remove sort classes from all headers
            document.querySelectorAll('.sortable').forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Add sort class to current header
            this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
            
            // Sort rows
            rows.sort((a, b) => {
                let aValue, bValue;
                
                switch(sortBy) {
                    case 'year':
                        aValue = a.getAttribute('data-year');
                        bValue = b.getAttribute('data-year');
                        break;
                    case 'start_date':
                        aValue = new Date(a.querySelector('[data-date]').getAttribute('data-date'));
                        bValue = new Date(b.querySelector('[data-date]').getAttribute('data-date'));
                        break;
                    case 'end_date':
                        aValue = new Date(a.querySelectorAll('[data-date]')[1].getAttribute('data-date'));
                        bValue = new Date(b.querySelectorAll('[data-date]')[1].getAttribute('data-date'));
                        break;
                    case 'status':
                        aValue = a.getAttribute('data-status');
                        bValue = b.getAttribute('data-status');
                        break;
                }
                
                if (aValue < bValue) return isAsc ? 1 : -1;
                if (aValue > bValue) return isAsc ? -1 : 1;
                return 0;
            });
            
            // Reorder rows in DOM
            rows.forEach(row => tbody.appendChild(row));
        });
    });

    // Reset filters
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('sortBy').value = 'year';
        
        // Show all rows
        document.querySelectorAll('.table-row').forEach(row => {
            row.classList.remove('hidden');
            row.style.display = '';
        });
        
        // Reset count
        const totalRows = document.querySelectorAll('.table-row').length;
        document.getElementById('showingCount').textContent = totalRows;
        
        // Remove sort classes
        document.querySelectorAll('.sortable').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });
    }

    // Delete confirmation
    function confirmDelete(yearId, yearName) {
        document.getElementById('deleteYear').textContent = yearName;
        document.getElementById('deleteForm').action = `/admin/academic_year/${yearId}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Export functionality
    function exportData() {
        // Simple CSV export
        const rows = document.querySelectorAll('.table-row:not(.hidden)');
        let csvContent = 'Academic Year,Starting Date,Expiry Date,Status\n';
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const year = cells[0].textContent.trim();
            const startDate = cells[1].textContent.trim().replace(/\s+/g, ' ');
            const endDate = cells[2].textContent.trim().replace(/\s+/g, ' ');
            const status = cells[3].textContent.trim();
            
            csvContent += `"${year}","${startDate}","${endDate}","${status}"\n`;
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'academic_years.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
</script>
@endsection