@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-calendar-check me-2 text-warning"></i>
                        Teacher Dailies Approval
                    </h2>
                    <p class="text-muted mb-0">Review and approve daily teaching reports</p>
                </div>
                <div class="d-flex gap-3">
                    <div class="badge bg-light text-dark fs-6">
                        <i class="fas fa-clock me-1 text-warning"></i>
                        {{ $dailies->where('approved', false)->count() }} Pending
                    </div>
                    <div class="badge bg-light text-dark fs-6">
                        <i class="fas fa-check-circle me-1 text-success"></i>
                        {{ $dailies->where('approved', true)->count() }} Approved
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
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
                                       placeholder="Search by teacher, course, lesson, or date..."
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending">Pending Only</option>
                                <option value="approved">Approved Only</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end mt-2 mt-md-0">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="resultCount">{{ $dailies->count() }}</span> records found
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-warning text-dark py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-list-alt me-2"></i>
                        Daily Reports Overview
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="dailiesTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-calendar text-primary me-2"></i>Date
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-user-tie text-info me-2"></i>Teacher
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-book text-success me-2"></i>Course
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-bookmark text-secondary me-2"></i>Lesson
                                    </th>
                                    <th class="border-0 fw-semibold py-3 text-center">
                                        <i class="fas fa-flag text-warning me-2"></i>Status
                                    </th>
                                    <th class="border-0 fw-semibold py-3 text-center">
                                        <i class="fas fa-cogs text-dark me-2"></i>Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach($dailies as $daily)
                                    <tr class="daily-row" data-status="{{ $daily->approved ? 'approved' : 'pending' }}">
                                        <!-- Date Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                    <i class="fas fa-calendar-day text-primary"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold daily-date">{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($daily->date)->format('l') }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Teacher Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-info"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-medium teacher-name">{{ $daily->teacher->teacher_name ?? '-' }}</span>
                                                    @if($daily->teacher)
                                                        <br><small class="text-muted">ID: {{ $daily->teacher->id }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Course Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                                    <i class="fas fa-graduation-cap text-success"></i>
                                                </div>
                                                <span class="course-name">{{ $daily->course->course_name ?? '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- Lesson Column -->
                                        <td class="align-middle py-3">
                                            <div class="lesson-info">
                                                <span class="lesson-title">{{ $daily->lesson->title ?? '-' }}</span>
                                                @if($daily->lesson)
                                                    <br><small class="text-muted">Lesson ID: {{ $daily->lesson->id }}</small>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status Column -->
                                        <td class="align-middle py-3 text-center">
                                            <div class="status-container">
                                                @if($daily->approved)
                                                    <span class="badge bg-success bg-opacity-15 text-success px-3 py-2 fs-6">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Approved
                                                    </span>
                                                    <br>
                                                    <small class="text-muted mt-1 d-block">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $daily->updated_at ? $daily->updated_at->diffForHumans() : '-' }}
                                                    </small>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-15 text-warning px-3 py-2 fs-6">
                                                        <i class="fas fa-hourglass-half me-1"></i>
                                                        Pending
                                                    </span>
                                                    <br>
                                                    <small class="text-muted mt-1 d-block">
                                                        <i class="fas fa-calendar-plus me-1"></i>
                                                        {{ $daily->created_at ? $daily->created_at->diffForHumans() : '-' }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Action Column -->
                                        <td class="align-middle py-3 text-center">
                                            <a href="{{ route('dos.dailies.show', $daily->id) }}" 
                                               class="btn btn-primary btn-sm px-3 py-2 review-btn">
                                                <i class="fas fa-eye me-1"></i>
                                                Review
                                            </a>
                                            @if(!$daily->approved)
                                                <div class="mt-2">
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Action Required
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- No Results Message -->
                    <div id="noResults" class="text-center py-5 d-none">
                        <i class="fas fa-search fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No dailies found</h5>
                        <p class="text-muted mb-0">Try adjusting your search terms or filters</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $dailies->links() }}
            </div>

            <!-- Quick Stats Card -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-list-alt fs-2 mb-2"></i>
                            <h4 class="fw-bold">{{ $dailies->total() }}</h4>
                            <small>Total Reports</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-warning text-dark">
                        <div class="card-body text-center">
                            <i class="fas fa-hourglass-half fs-2 mb-2"></i>
                            <h4 class="fw-bold">{{ $dailies->where('approved', false)->count() }}</h4>
                            <small>Pending Approval</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fs-2 mb-2"></i>
                            <h4 class="fw-bold">{{ $dailies->where('approved', true)->count() }}</h4>
                            <small>Approved</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fs-2 mb-2"></i>
                            <h4 class="fw-bold">{{ $dailies->count() > 0 ? round(($dailies->where('approved', true)->count() / $dailies->count()) * 100) : 0 }}%</h4>
                            <small>Approval Rate</small>
                        </div>
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
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .daily-row {
        transition: all 0.2s ease;
    }
    
    .daily-row:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .review-btn {
        transition: all 0.2s ease;
    }
    
    .review-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }
    
    .status-container .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .badge {
            font-size: 0.75rem !important;
            padding: 0.375rem 0.5rem !important;
        }
        
        .btn-sm {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .d-flex.gap-3 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
    }
</style>

<!-- Live Search and Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('tableBody');
    const noResults = document.getElementById('noResults');
    const resultCount = document.getElementById('resultCount');
    const rows = tableBody.querySelectorAll('.daily-row');

    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value.toLowerCase();
        let visibleRows = 0;

        rows.forEach(function(row) {
            // Get text content for search
            const dateText = row.querySelector('.daily-date').textContent.toLowerCase();
            const teacherText = row.querySelector('.teacher-name').textContent.toLowerCase();
            const courseText = row.querySelector('.course-name').textContent.toLowerCase();
            const lessonText = row.querySelector('.lesson-title').textContent.toLowerCase();
            
            const rowText = dateText + ' ' + teacherText + ' ' + courseText + ' ' + lessonText;
            
            // Get status for filtering
            const rowStatus = row.getAttribute('data-status');
            
            // Check search term match
            const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
            
            // Check status filter match
            const matchesStatus = statusValue === '' || rowStatus === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update result count
        resultCount.textContent = visibleRows;

        // Show/hide no results message
        if (visibleRows === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterRows);
    statusFilter.addEventListener('change', filterRows);

    // Add loading state to review buttons
    const reviewButtons = document.querySelectorAll('.review-btn');
    reviewButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
            this.disabled = true;
            
            // Reset after 3 seconds if page doesn't redirect
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
    });

    // Auto-refresh pending count every 30 seconds
    setInterval(function() {
        // You can add AJAX call here to update pending count
        // This is just a placeholder for the concept
    }, 30000);
});
</script>
@endsection