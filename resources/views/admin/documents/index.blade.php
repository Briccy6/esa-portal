@extends('admin.layout')

@section('title', 'Document Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Document Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.documents.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="teacher_id" class="form-control form-control-sm">
                                    <option value="">All Teachers</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->teacher_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="course_id" class="form-control form-control-sm">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="branch_id" class="form-control form-control-sm">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="visibility" class="form-control form-control-sm">
                                    <option value="">All Visibility</option>
                                    <option value="public" {{ request('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ request('visibility') == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="pricing" class="form-control form-control-sm">
                                    <option value="">All Pricing</option>
                                    <option value="free" {{ request('pricing') == 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="paid" {{ request('pricing') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Bulk Actions -->
                    <form id="bulkActionForm" method="POST" action="{{ route('admin.documents.bulk.update') }}" class="mb-3">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <select name="action" id="bulkAction" class="form-control form-control-sm" required>
                                    <option value="">Bulk Actions</option>
                                    <option value="make_public">Make Public</option>
                                    <option value="make_private">Make Private</option>
                                    <option value="make_free">Make Free</option>
                                    <option value="make_paid">Make Paid</option>
                                    <option value="apply_watermark">Apply Watermark</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="priceField" style="display: none;">
                                <input type="number" name="price" step="0.01" min="0" class="form-control form-control-sm" placeholder="Price">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-warning btn-sm">Apply</button>
                            </div>
                        </div>
                        
                        <!-- Documents Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Teacher</th>
                                        <th>Course</th>
                                        <th>Branch</th>
                                        <th>Lesson</th>
                                        <th>Document</th>
                                        <th>Price</th>
                                        <th>Visibility</th>
                                        <th>Watermark</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resources as $resource)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="resource_ids[]" value="{{ $resource->id }}" class="resource-checkbox">
                                            </td>
                                            <td>{{ $resource->teacher->teacher_name }}</td>
                                            <td>{{ $resource->course->course_name }}</td>
                                            <td>{{ $resource->branch->name }}</td>
                                            <td>{{ $resource->lesson->title ?? 'N/A' }}</td>
                                            <td>
                                                @if($resource->document_name)
                                                    <a href="#" onclick="previewDocument({{ $resource->id }})" class="text-primary">
                                                        {{ Str::limit($resource->document_name, 30) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No document</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($resource->is_free)
                                                    <span class="badge badge-success">Free</span>
                                                @else
                                                    <span class="badge badge-info">${{ number_format($resource->price, 2) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($resource->is_public)
                                                    <span class="badge badge-primary">Public</span>
                                                @else
                                                    <span class="badge badge-secondary">Private</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($resource->watermark_applied)
                                                    <span class="badge badge-success">Applied</span>
                                                @else
                                                    <span class="badge badge-warning">Not Applied</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.documents.edit', $resource->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($resource->document_name)
                                                        <a href="{{ route('admin.documents.download', $resource->id) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between">
                            <div>
                                Showing {{ $resources->firstItem() }} to {{ $resources->lastItem() }} of {{ $resources->total() }} entries
                            </div>
                            <div>
                                {{ $resources->links() }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select all checkboxes
    $('#selectAll').change(function() {
        $('.resource-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Show/hide price field based on bulk action
    $('#bulkAction').change(function() {
        if ($(this).val() === 'make_paid') {
            $('#priceField').show();
        } else {
            $('#priceField').hide();
        }
    });
    
    // Preview document
    window.previewDocument = function(resourceId) {
        $('#previewFrame').attr('src', '{{ route('admin.documents.preview', ':id') }}'.replace(':id', resourceId));
        $('#previewModal').modal('show');
    };
});
</script>
@endsection
