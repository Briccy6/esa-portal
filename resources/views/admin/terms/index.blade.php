@extends('admin.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-calendar-alt me-3 text-primary"></i>Academic Terms Management
            </h1>
            <p class="text-muted mb-0">Manage all academic terms for {{ $academicYear->year ?? 'Current Year' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.terms.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Add New Term
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Terms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $terms->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Terms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $terms->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Upcoming Terms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $terms->where('starting_date', '>', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Completed Terms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $terms->where('ending_date', '<', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Table Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-table me-2"></i>Terms Overview
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light btn-sm" onclick="exportTable()">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <button class="btn btn-outline-light btn-sm" onclick="printTable()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($terms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="termsTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="border-0 fw-bold">#</th>
                                <th class="border-0 fw-bold">
                                    <i class="fas fa-tag me-2"></i>Term
                                </th>
                                <th class="border-0 fw-bold">
                                    <i class="fas fa-graduation-cap me-2"></i>Academic Year
                                </th>
                                <th class="border-0 fw-bold">
                                    <i class="fas fa-calendar-day me-2"></i>Start Date
                                </th>
                                <th class="border-0 fw-bold">
                                    <i class="fas fa-calendar-times me-2"></i>End Date
                                </th>
                                <th class="border-0 fw-bold">
                                    <i class="fas fa-clock me-2"></i>Duration
                                </th>
                                <th class="border-0 fw-bold">
                                    <i class="fas fa-info-circle me-2"></i>Status
                                </th>
                                <th class="border-0 fw-bold text-center">
                                    <i class="fas fa-cogs me-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($terms as $index => $term)
                                <tr class="table-row-hover">
                                    <td class="fw-semibold text-primary">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="term-icon me-3">
                                                @if($term->term == 'Term 1')
                                                    <i class="fas fa-play-circle text-success"></i>
                                                @elseif($term->term == 'Term 2')
                                                    <i class="fas fa-pause-circle text-warning"></i>
                                                @else
                                                    <i class="fas fa-stop-circle text-danger"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $term->term }}</div>
                                                <small class="text-muted">{{ ucfirst($term->term) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-info px-3 py-2 fs-7">
                                            {{ $term->academicYear->year ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-display">
                                            <i class="fas fa-calendar-plus text-success me-2"></i>
                                            <span class="fw-semibold">{{ $term->starting_date ? $term->starting_date->format('M d, Y') : 'Not Set' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $term->starting_date ? $term->starting_date->format('l') : '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-display">
                                            <i class="fas fa-calendar-minus text-danger me-2"></i>
                                            <span class="fw-semibold">{{ $term->ending_date ? $term->ending_date->format('M d, Y') : 'Not Set' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $term->ending_date ? $term->ending_date->format('l') : '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($term->starting_date && $term->ending_date)
                                            @php
                                                $duration = $term->starting_date->diffInDays($term->ending_date);
                                                $weeks = floor($duration / 7);
                                                $days = $duration % 7;
                                            @endphp
                                            <div class="duration-display">
                                                <span class="fw-bold text-primary">{{ $duration }} days</span>
                                                <br>
                                                <small class="text-muted">{{ $weeks }}w {{ $days }}d</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $now = now();
                                            $status = 'upcoming';
                                            $badgeClass = 'bg-info';
                                            $icon = 'fas fa-clock';
                                            
                                            if($term->starting_date && $term->ending_date) {
                                                if($now->lt($term->starting_date)) {
                                                    $status = 'upcoming';
                                                    $badgeClass = 'bg-info';
                                                    $icon = 'fas fa-clock';
                                                } elseif($now->between($term->starting_date, $term->ending_date)) {
                                                    $status = 'active';
                                                    $badgeClass = 'bg-success';
                                                    $icon = 'fas fa-play-circle';
                                                } else {
                                                    $status = 'completed';
                                                    $badgeClass = 'bg-secondary';  
                                                    $icon = 'fas fa-check-circle';
                                                }
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2">
                                            <i class="{{ $icon }} me-1"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons d-flex justify-content-center gap-1">
                                            <!-- View Button -->
                                            <button class="btn btn-outline-info btn-sm action-btn" 
                                                    title="View Details" 
                                                    onclick="viewTerm({{ $term->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.terms.edit', $term->id) }}" 
                                               class="btn btn-outline-warning btn-sm action-btn" 
                                               title="Edit Term">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <button class="btn btn-outline-danger btn-sm action-btn" 
                                                    title="Delete Term"
                                                    onclick="confirmDelete({{ $term->id }}, '{{ $term->term }}')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($terms, 'links'))
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $terms->firstItem() ?? 1 }} to {{ $terms->lastItem() ?? $terms->count() }} of {{ $terms->total() ?? $terms->count() }} results
                            </div>
                            {{ $terms->links() }}
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Terms Found</h4>
                        <p class="text-muted mb-4">There are no academic terms created yet. Start by adding your first term.</p>
                        <a href="{{ route('admin.terms.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle me-2"></i>Create First Term
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h6 class="fw-bold">Are you sure you want to delete this term?</h6>
                    <p class="text-muted mb-0">This action cannot be undone. All associated data will be permanently removed.</p>
                </div>
                <div class="alert alert-light border-start border-danger border-4">
                    <strong>Term:</strong> <span id="termName"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Delete Term
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-bold" id="viewModalLabel">
                    <i class="fas fa-eye me-2"></i>Term Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="termDetails">
                <!-- Term details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.border-left-primary {
    border-left: 4px solid #667eea !important;
}

.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}

.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table-row-hover:hover {
    background-color: #f8f9ff !important;
    transform: scale(1.01);
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border-width: 2px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.badge {
    font-size: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
}

.date-display {
    line-height: 1.2;
}

.duration-display {
    line-height: 1.2;
}

.term-icon {
    width: 30px;
    text-align: center;
}

.empty-state {
    padding: 3rem 2rem;
}

.table th {
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.fs-7 {
    font-size: 0.875rem;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem !important;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .date-display small {
        display: none;
    }
}

/* Print styles */
@media print {
    .action-buttons,
    .card-header button,
    .btn {
        display: none !important;
    }
}
</style>

<script>
// Delete confirmation
function confirmDelete(termId, termName) {
    document.getElementById('termName').textContent = termName;
    document.getElementById('deleteForm').action = `/admin/terms/${termId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// View term details
function viewTerm(termId) {
    // You can make an AJAX call here to fetch term details
    // For now, showing a placeholder
    document.getElementById('termDetails').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading term details...</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('viewModal')).show();
    
    // Simulate loading (replace with actual AJAX call)
    setTimeout(() => {
        document.getElementById('termDetails').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Basic Information</h6>
                    <table class="table table-borderless">
                        <tr><td class="fw-semibold">Term ID:</td><td>${termId}</td></tr>
                        <tr><td class="fw-semibold">Term Name:</td><td>Term 1</td></tr>
                        <tr><td class="fw-semibold">Academic Year:</td><td>2024-2025</td></tr>
                        <tr><td class="fw-semibold">Status:</td><td><span class="badge bg-success">Active</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Duration Details</h6>
                    <table class="table table-borderless">
                        <tr><td class="fw-semibold">Start Date:</td><td>Sep 15, 2024</td></tr>
                        <tr><td class="fw-semibold">End Date:</td><td>Dec 20, 2024</td></tr>
                        <tr><td class="fw-semibold">Duration:</td><td>96 days</td></tr>
                        <tr><td class="fw-semibold">Weeks:</td><td>13 weeks 5 days</td></tr>
                    </table>
                </div>
            </div>
        `;
    }, 1000);
}

// Export table
function exportTable() {
    alert('Export functionality would be implemented here');
}

// Print table
function printTable() {
    window.print();
}

// Table search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add search functionality if needed
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#termsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection