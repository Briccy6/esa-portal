@extends('admin.layout')

@section('title', 'Resources Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resources Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.resources.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Upload New Resource
                        </a>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                
                <div class="card-body">
                    <!-- Resources Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Teacher</th>
                                    <th>Course</th>
                                    <th>Branch</th>
                                    <th>Lesson</th>
                                    <th>Document</th>
                                    <th>Video</th>
                                    <th>YouTube</th>
                                    <th>Price</th>
                                    <th>Visibility</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resources as $resource)
                                    <tr>
                                        <td>{{ $resource->id }}</td>
                                        <td>{{ $resource->teacher->teacher_name }}</td>
                                        <td>{{ $resource->course->course_name }}</td>
                                        <td>{{ $resource->branch->name }}</td>
                                        <td>{{ $resource->lesson->title ?? 'N/A' }}</td>
                                        <td>
                                            @if($resource->document_name)
                                                @if(str_starts_with($resource->document_mime, 'text/url'))
                                                    <a href="{{ $resource->document_name }}" target="_blank" class="text-primary" title="Open Link">
                                                        <i class="fas fa-link"></i> Link
                                                    </a>
                                                @else
                                                    <a href="#" onclick="previewDocument({{ $resource->id }})" class="text-primary" title="Preview">
                                                        <i class="fas fa-eye"></i> {{ Str::limit($resource->document_name, 20) }}
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">No document</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($resource->video_name)
                                                <span class="text-success">
                                                    <i class="fas fa-video"></i> {{ Str::limit($resource->video_name, 15) }}
                                                </span>
                                            @else
                                                <span class="text-muted">No video</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($resource->youtube_link)
                                                <a href="{{ $resource->youtube_link }}" target="_blank" class="text-danger" title="Open YouTube">
                                                    <i class="fab fa-youtube"></i> YouTube
                                                </a>
                                            @else
                                                <span class="text-muted">No link</span>
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
                                            <div class="btn-group">
                                                <a href="{{ route('admin.resources.edit', $resource->id) }}" class="btn btn-sm btn-info" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($resource->document_name && !str_starts_with($resource->document_mime, 'text/url'))
                                                    <a href="{{ route('admin.resources.download', $resource->id) }}" class="btn btn-sm btn-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.resources.destroy', $resource->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this resource?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Resources Found</h5>
                                                <p class="text-muted">Start by uploading your first resource.</p>
                                                <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Upload Resource
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
                        <div class="d-flex justify-content-between mt-3">
                            <div>
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
    // Preview document
    window.previewDocument = function(resourceId) {
        $('#previewFrame').attr('src', '{{ route('admin.resources.preview', ':id') }}'.replace(':id', resourceId));
        $('#previewModal').modal('show');
    };
});
</script>
@endsection
