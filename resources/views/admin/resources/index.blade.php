@extends('admin.layout')

@section('title', 'Resources Management')

@section('styles')
<style>
.resources-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.resources-header h3 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.resources-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.resources-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.resources-card .card-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.resources-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
    overflow-x: auto;
}

.resources-table .table {
    margin-bottom: 0;
    min-width: 1200px;
    background: white;
    border: 1px solid #e2e8f0;
}

.resources-table .table thead {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
}

.resources-table .table thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: white;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.resources-table .table thead th:last-child {
    border-right: none;
}

.resources-table .table td {
    padding: 0.875rem;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    color: #374151;
}

.resources-table .table tbody tr:hover {
    background-color: #f0f9ff;
    border-left: 3px solid #3b82f6;
}

.resources-table .table tbody tr:nth-child(even) {
    background-color: #fafafa;
}

.resources-table .table tbody tr:nth-child(even):hover {
    background-color: #f0f9ff;
}

.badge-custom {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.badge-free {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-paid {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-public {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: white;
    border: 1px solid rgba(139, 92, 246, 0.3);
}

.badge-private {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    border: 1px solid rgba(75, 85, 99, 0.3);
}

.btn-action {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    display: inline-block;
    min-width: 32px;
    text-align: center;
    color: white !important;
    text-decoration: none !important;
}

.btn-action:hover {
    transform: translateY(-1px);
    color: white !important;
    text-decoration: none !important;
}

.btn-edit {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
    border: 1px solid rgba(14, 165, 233, 0.3);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(14, 165, 233, 0.4);
    color: white;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
}

.btn-price {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.btn-price:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
    color: white;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.btn-public {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border: 1px solid rgba(139, 92, 246, 0.3);
}

.btn-public:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(139, 92, 246, 0.4);
    color: white;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
}

.btn-download {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(245, 158, 11, 0.4);
    color: white;
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(239, 68, 68, 0.4);
    color: white;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

.link-action {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.link-action:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    border: 2px dashed #e5e7eb;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.alert-custom {
    border-radius: 10px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(245, 158, 11, 0.3);
    color: white;
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.modal-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    border: none;
}

.modal-header .btn-close {
    color: white;
    opacity: 0.8;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

@media (max-width: 768px) {
    .resources-table .table th {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    .resources-table .table td {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .btn-action {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}

.btn-group {
    display: flex;
    gap: 4px;
    flex-wrap: nowrap;
}

.btn-group .btn-action {
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.btn-group .btn-action:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.3);
}

/* Search Bar Styling */
.search-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e2e8f0;
}

.search-container .input-group {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.search-container .input-group-text {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border: none;
    color: white;
}

.search-container .form-control,
.search-container .form-select {
    border: 1px solid #e2e8f0;
    border-radius: 0;
    padding: 0.75rem;
    font-weight: 500;
    background: white;
}

.search-container .form-control:focus,
.search-container .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.no-results {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 12px;
    margin: 1rem;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="resources-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><i class="bi bi-folder"></i> Resources Management</h3>
                    <p class="mb-0">Manage and monitor all educational resources uploaded by teachers</p>
                </div>
                <a href="{{ route('admin.resources.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i> Upload New Resource
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="resources-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Resources</h5>
            </div>
            
            <div class="card-body p-0">
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search resources by teacher, course, branch, lesson...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="visibilityFilter" class="form-select">
                                <option value="">All Visibility</option>
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="priceFilter" class="form-select">
                                <option value="">All Prices</option>
                                <option value="free">Free</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-custom" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Resources Table -->
                <div class="table-responsive resources-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Teacher</th>
                                <th>Course</th>
                                <th>Branch</th>
                                <th>Lesson</th>
                                <th>Document</th>
                                <th>Price</th>
                                <th>Visibility</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resources as $resource)
                                <tr>
                                    <td><strong>{{ $resource->id }}</strong></td>
                                    <td>{{ $resource->teacher->teacher_name }}</td>
                                    <td>{{ $resource->course->course_name }}</td>
                                    <td>{{ $resource->branch->name }}</td>
                                    <td>{{ $resource->lesson->title ?? 'N/A' }}</td>
                                    <td>
                                        @if($resource->document_name)
                                            @if(str_starts_with($resource->document_mime, 'text/url'))
                                                <a href="{{ $resource->document_name }}" target="_blank" class="link-action" title="Open Link">
                                                    <i class="bi bi-link-45deg"></i> Link
                                                </a>
                                            @else
                                                <a href="#" onclick="previewDocument({{ $resource->id }})" class="link-action" title="Preview">
                                                    <i class="bi bi-eye"></i> {{ Str::limit($resource->document_name, 20) }}
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted">No document</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($resource->is_free)
                                            <span class="badge badge-custom badge-free">Free</span>
                                        @else
                                            <span class="badge badge-custom badge-paid">${{ number_format($resource->price, 2) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($resource->is_public)
                                            <span class="badge badge-custom badge-public">Public</span>
                                        @else
                                            <span class="badge badge-custom badge-private">Private</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.resources.edit', $resource->id) }}" class="btn btn-action btn-edit" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-action btn-price" title="Set Price" onclick="openPriceModal({{ $resource->id }}, '{{ $resource->is_public ? 'public' : 'private' }}', {{ $resource->is_free ? 0 : $resource->price }})">
                                                <i class="bi bi-cash-coin"></i>
                                            </button>
                                            @if(!$resource->is_public)
                                                <a href="{{ route('admin.resources.make-public', $resource->id) }}" class="btn btn-action btn-public" title="Make Public" onclick="return confirm('This will apply watermarks and make the resource public. Continue?')">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            @endif
                                            @if($resource->document_name && !str_starts_with($resource->document_mime, 'text/url'))
                                                <a href="{{ route('admin.resources.download', $resource->id) }}" class="btn btn-action btn-download" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                            <form method="POST" action="{{ route('admin.resources.destroy', $resource->id) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this resource?')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="empty-state m-4">
                                            <i class="bi bi-folder-open display-1"></i>
                                            <h5 class="mt-4">No Resources Found</h5>
                                            <p class="text-muted mb-4">Start by uploading your first resource.</p>
                                            <a href="{{ route('admin.resources.create') }}" class="btn btn-primary-custom">
                                                <i class="bi bi-plus-circle"></i> Upload Resource
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($resources->hasPages())
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="pagination-info">
                            Showing {{ $resources->firstItem() }} to {{ $resources->lastItem() }} of {{ $resources->total() }} entries
                        </div>
                        <div>
                            {{ $resources->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye"></i> Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Price Modal -->
<div class="modal fade" id="priceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash"></i> Set Resource Price & Visibility</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="priceForm" method="POST" action="">
                @csrf
                <input type="hidden" id="resourceId" name="resource_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="visibility" class="form-label fw-bold">Visibility</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibility" id="public" value="public" required>
                            <label class="form-check-label" for="public">
                                <i class="bi bi-globe"></i> Public
                                <small class="text-muted d-block">Students can access this resource</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibility" id="private" value="private" required>
                            <label class="form-check-label" for="private">
                                <i class="bi bi-lock"></i> Private
                                <small class="text-muted d-block">Only administrators can access this resource</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label fw-bold">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                        <small class="text-muted">Enter 0 for free resource, or any amount for paid access</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_free" name="is_free" value="1">
                            <label class="form-check-label" for="is_free">
                                <i class="bi bi-gift"></i> Mark as Free Resource
                                <small class="text-muted d-block">Override price and make resource completely free</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Price & Visibility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Preview document
    window.previewDocument = function(resourceId) {
        $('#previewFrame').attr('src', '{{ route('admin.resources.preview', ':id') }}'.replace(':id', resourceId));
        $('#previewModal').modal('show');
    };

    // Open price modal
    window.openPriceModal = function(resourceId, visibility, price) {
        $('#resourceId').val(resourceId);
        
        // Set form action with resource ID
        $('#priceForm').attr('action', '{{ route('admin.resources.update-price', ':id') }}'.replace(':id', resourceId));
        
        // Set visibility
        $('input[name="visibility"][value="' + visibility + '"]').prop('checked', true);
        
        // Set price
        $('#price').val(price);
        
        // Set free checkbox
        if (price == 0) {
            $('#is_free').prop('checked', true);
        } else {
            $('#is_free').prop('checked', false);
        }
        
        // Handle free checkbox change
        $('#is_free').change(function() {
            if ($(this).is(':checked')) {
                $('#price').val(0).prop('readonly', true);
            } else {
                $('#price').prop('readonly', false);
            }
        });
        
        $('#priceModal').modal('show');
    };

    // Handle form submission
    $('#priceForm').submit(function(e) {
        e.preventDefault();
        
        var formData = {
            _token: $('input[name="_token"]').val(),
            resource_id: $('#resourceId').val(),
            visibility: $('input[name="visibility"]:checked').val(),
            price: $('#price').val(),
            is_free: $('#is_free').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            beforeSend: function() {
                // Disable submit button and show loading
                $('#priceForm button[type="submit"]').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Updating...');
            },
            success: function(response) {
                $('#priceModal').modal('hide');
                // Show success message
                showAlert('success', 'Resource price and visibility updated successfully!');
                // Reload page after short delay
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                var errorMessage = 'Error updating resource';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('error', errorMessage);
                // Re-enable submit button
                $('#priceForm button[type="submit"]').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Price & Visibility');
            },
            complete: function() {
                // Re-enable submit button if not reloading
                setTimeout(function() {
                    $('#priceForm button[type="submit"]').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Price & Visibility');
                }, 2000);
            }
        });
    });

    // Live search functionality
    $('#searchInput, #visibilityFilter, #priceFilter').on('input change', function() {
        var searchTerm = $('#searchInput').val().toLowerCase();
        var visibilityFilter = $('#visibilityFilter').val();
        var priceFilter = $('#priceFilter').val();
        
        $('tbody tr').each(function() {
            var $row = $(this);
            var showRow = true;
            
            // Skip empty state row
            if ($row.find('.empty-state').length > 0) {
                return;
            }
            
            // Search functionality
            if (searchTerm) {
                var text = $row.text().toLowerCase();
                if (text.indexOf(searchTerm) === -1) {
                    showRow = false;
                }
            }
            
            // Visibility filter
            if (visibilityFilter && showRow) {
                var visibility = $row.find('.badge-public, .badge-private').text().toLowerCase();
                if (visibility !== visibilityFilter) {
                    showRow = false;
                }
            }
            
            // Price filter
            if (priceFilter && showRow) {
                var priceBadge = $row.find('.badge-free, .badge-paid');
                if (priceFilter === 'free' && !priceBadge.hasClass('badge-free')) {
                    showRow = false;
                } else if (priceFilter === 'paid' && !priceBadge.hasClass('badge-paid')) {
                    showRow = false;
                }
            }
            
            // Show/hide row
            $row.toggle(showRow);
        });
        
        // Show no results message if needed
        var visibleRows = $('tbody tr:visible').length;
        var emptyState = $('.empty-state').closest('tr');
        
        if (visibleRows === 0 && emptyState.length === 0) {
            $('tbody').append('<tr class="no-results"><td colspan="9" class="text-center p-4"><i class="bi bi-search display-1 text-muted"></i><h5 class="mt-3 text-muted">No resources found</h5><p class="text-muted">Try adjusting your search criteria</p></td></tr>');
        } else if (visibleRows > 0) {
            $('.no-results').remove();
        }
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show alert-custom" role="alert">' +
                      '<i class="bi bi-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message +
                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        
        // Remove existing alerts
        $('.alert').alert('close');
        
        // Add new alert
        $('.card-body').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
@endsection
