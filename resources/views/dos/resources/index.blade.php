@extends('dos.layout')

@section('title', 'Study Resources Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>
            Study Resources Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="try { exportResources(); } catch(e) { console.error('Export error:', e); alert('Unable to export resources. Please try again.'); }">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <a href="{{ route('dos.resources.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add Resource
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $resources->count() ?? 0 }}</h4>
                            <p class="card-text">Total Resources</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $resources->where('is_public', true)->count() ?? 0 }}</h4>
                            <p class="card-text">Public</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-globe fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $resources->where('is_free', true)->count() ?? 0 }}</h4>
                            <p class="card-text">Free</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-gift fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $resources->where('price', '>', 0)->count() ?? 0 }}</h4>
                            <p class="card-text">Paid</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.resources.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="branch_filter" class="form-label">Branch</label>
                        <select name="branch" id="branch_filter" class="form-select">
                            <option value="">All Branches</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="course_filter" class="form-label">Course</label>
                        <select name="course" id="course_filter" class="form-select">
                            <option value="">All Courses</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type_filter" class="form-label">Type</label>
                        <select name="type" id="type_filter" class="form-select">
                            <option value="">All Types</option>
                            <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
                            <option value="youtube" {{ request('type') == 'youtube' ? 'selected' : '' }}>YouTube Links</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.resources.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resources Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Teacher</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($resources) && $resources->count() > 0)
                            @foreach($resources as $resource)
                                <tr>
                                    <td>
                                        <strong>{{ $resource->document_name ?? 'Resource' }}</strong>
                                        @if($resource->admin_notes)
                                            <br><small class="text-muted">{{ Str::limit($resource->admin_notes, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $resource->teacher->teacher_name ?? 'N/A' }}</td>
                                    <td>{{ $resource->course->course_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($resource->document_name)
                                            <span class="badge bg-primary">Document</span>
                                        @endif
                                        @if($resource->video_name)
                                            <span class="badge bg-success">Video</span>
                                        @endif
                                        @if($resource->youtube_link)
                                            <span class="badge bg-danger">YouTube</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($resource->is_free)
                                            <span class="badge bg-success">Free</span>
                                        @else
                                            ${{ number_format($resource->price, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($resource->is_public)
                                            <span class="badge bg-info">Public</span>
                                        @else
                                            <span class="badge bg-secondary">Private</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('dos.resources.show', $resource->id) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($resource->document_blob)
                                                <a href="{{ route('dos.resources.download', $resource->id) }}" class="btn btn-outline-success">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('dos.resources.edit', $resource->id) }}" class="btn btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteResource({{ $resource->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-file-earmark-text fs-1"></i>
                                        <p class="mt-2">No resources found</p>
                                        <a href="{{ route('dos.resources.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Add First Resource
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($resources) && $resources->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $resources->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteResource(id) {
    if(confirm('Are you sure you want to delete this resource?')) {
        fetch(`/dos/resources/${id}/delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Resource deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting resource: ' + data.message);
            }
        });
    }
}

function exportResources() {
    window.location.href = `/dos/resources/export`;
}
</script>
@endsection
