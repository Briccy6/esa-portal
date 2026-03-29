@extends('staff.partials.sidebar_sec')

@section('content')
<div class="container-fluid py-4">
    <!-- Success/Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                @if(request('nesa') == 'true')
                    <h2 class="page-title mb-2">
                        <i class="fas fa-award me-2"></i>NESA Registration Management
                    </h2>
                    <p class="page-subtitle mb-0">Register students for NESA examination and manage approval status</p>
                @else
                    <h2 class="page-title mb-2">
                        <i class="fas fa-users me-2"></i>All Students
                    </h2>
                    <p class="page-subtitle mb-0">Manage student records and payments</p>
                @endif
            </div>
            <div>
                <span class="badge bg-primary fs-6">
                    <i class="fas fa-database me-1"></i>{{ $students->total() }} Total Students
                </span>
            </div>
        </div>
    </div>

    <!-- Live Search Form -->
    <div class="card mb-4 search-card">
        <div class="card-body">
            <form id="liveSearchForm">
                <div class="row g-3">
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchName" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchRegNumber" name="reg_number" class="form-control" placeholder="Reg Number" value="{{ request('reg_number') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchEmail" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchTelephone" name="telephone" class="form-control" placeholder="Telephone" value="{{ request('telephone') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchCourse" name="course" class="form-control" placeholder="Course" value="{{ request('course') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-1"></i>Reset
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="loadingIndicator" class="text-primary" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-1"></span> Searching...
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div id="studentsTableWrapper">
        @include('staff.partials.students_table', ['students' => $students])
    </div>
</div>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .page-title {
        font-size: clamp(1.5rem, 4vw, 2rem);
        font-weight: 700;
        margin-bottom: 0;
    }

    .page-subtitle {
        font-size: clamp(0.9rem, 2vw, 1rem);
        opacity: 0.9;
    }

    .search-card {
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-radius: 15px;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }

    #loadingIndicator {
        font-size: 14px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .search-card .card-body {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .badge {
            font-size: 0.85rem !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(function() {
    let searchTimeout;
    
    // Live search functionality
    function performSearch(page = 1) {
        const searchData = {
            name: $('#searchName').val(),
            reg_number: $('#searchRegNumber').val(),
            email: $('#searchEmail').val(),
            telephone: $('#searchTelephone').val(),
            course: $('#searchCourse').val(),
            page: page
        };

        $('#loadingIndicator').show();
        
        $.ajax({
            url: "{{ route('students.index') }}",
            method: 'GET',
            data: searchData,
            success: function(response) {
                $('#studentsTableWrapper').html(response);
                
                const urlParams = new URLSearchParams(searchData);
                const newUrl = "{{ route('students.index') }}?" + urlParams.toString();
                window.history.pushState({path: newUrl}, '', newUrl);
                
                initializeModals();
                initializePaginationLinks();
                
                $('#loadingIndicator').hide();
            },
            error: function(xhr) {
                console.error('Search failed:', xhr);
                $('#loadingIndicator').hide();
                alert('Search failed. Please try again.');
            }
        });
    }

    // Debounced live search on input
    $('#liveSearchForm input').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch();
        }, 500);
    });

    // Reset button
    $('#resetBtn').on('click', function() {
        $('#liveSearchForm')[0].reset();
        performSearch();
    });

    // Pagination click handler
    function initializePaginationLinks() {
        $(document).off('click', '#paginationLinks a, .pagination a');
        $(document).on('click', '#paginationLinks a, .pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page') || 1;
            performSearch(page);
        });
    }

    // Initialize modals
    function initializeModals() {
        // Transcript modal
        $('[id^=transcriptModal]').off('show.bs.modal').on('show.bs.modal', function (event) {
            var modal = $(this);
            var studentId = modal.attr('id').replace('transcriptModal', '');
            var body = modal.find('.transcript-modal-body');
            body.html('<div class="text-center"><span class="spinner-border"></span> Loading...</div>');
            $.get("{{ url('/students') }}/" + studentId + "/transcript/ajax", function(data) {
                body.html(data);
            }).fail(function() {
                body.html('<div class="alert alert-danger">Failed to load transcript.</div>');
            });
        });
    }

    // Initialize on page load
    initializeModals();
    initializePaginationLinks();
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endsection
