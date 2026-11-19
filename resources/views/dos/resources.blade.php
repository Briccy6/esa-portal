@extends('staff.partials.sidebar_dos')

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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">
                <i class="bi bi-plus-circle me-1"></i> Add Resource
            </button>
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
                            <i class="bi bi-journal-text fs-1"></i>
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
                            <h4 class="card-title">{{ $resources->where('is_free', 1)->count() ?? 0 }}</h4>
                            <p class="card-text">Free Resources</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-gift fs-1"></i>
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
                            <h4 class="card-title">{{ $resources->where('is_free', 0)->count() ?? 0 }}</h4>
                            <p class="card-text">Paid Resources</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fs-1"></i>
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
                            <h4 class="card-title">{{ $resources->whereNotNull('video_blob')->count() ?? 0 }}</h4>
                            <p class="card-text">Video Resources</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-play-circle fs-1"></i>
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
                        <label for="type_filter" class="form-label">Resource Type</label>
                        <select name="type" id="type_filter" class="form-select">
                            <option value="">All Types</option>
                            <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Document</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="youtube" {{ request('type') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="price_filter" class="form-label">Price Type</label>
                        <select name="price_type" id="price_filter" class="form-select">
                            <option value="">All</option>
                            <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Free</option>
                            <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Paid</option>
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

    <!-- Resources Grid -->
    <div class="row">
        @if(isset($resources) && $resources->count() > 0)
            @foreach($resources as $resource)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 resource-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 30) }}</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="try { viewResource({{ $resource->id }}); } catch(e) { console.error('View error:', e); alert('Unable to view resource. Please try again.'); }">
                                        <i class="bi bi-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="try { editResource({{ $resource->id }}); } catch(e) { console.error('Edit error:', e); alert('Unable to edit resource. Please try again.'); }">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="try { deleteResource({{ $resource->id }}); } catch(e) { console.error('Delete error:', e); alert('Unable to delete resource. Please try again.'); }">
                                        <i class="bi bi-trash"></i> Delete
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-primary">{{ $resource->course->course_name ?? 'General' }}</span>
                            </div>
                            
                            <!-- Resource Type Badges -->
                            <div class="mb-3">
                                @if($resource->document_blob)
                                    <span class="badge bg-info me-1">
                                        <i class="bi bi-file-earmark-text"></i> Document
                                    </span>
                                @endif
                                @if($resource->video_blob)
                                    <span class="badge bg-danger me-1">
                                        <i class="bi bi-play-circle"></i> Video
                                    </span>
                                @endif
                                @if($resource->youtube_link)
                                    <span class="badge bg-danger me-1">
                                        <i class="bi bi-youtube"></i> YouTube
                                    </span>
                                @endif
                            </div>

                            <!-- Price Display -->
                            <div class="mb-3">
                                @if($resource->is_free)
                                    <span class="badge bg-success">
                                        <i class="bi bi-gift"></i> FREE
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-currency-dollar"></i> RWF {{ number_format($resource->price, 0) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Meta Info -->
                            <div class="text-muted small mb-3">
                                <div><i class="bi bi-person"></i> {{ $resource->teacher->teacher_name ?? 'N/A' }}</div>
                                <div><i class="bi bi-geo-alt"></i> {{ $resource->branch->name ?? 'N/A' }}</div>
                                <div><i class="bi bi-calendar"></i> {{ $resource->created_at->format('d M Y') }}</div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="try { viewResource({{ $resource->id }}); } catch(e) { console.error('View error:', e); alert('Unable to view resource. Please try again.'); }">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
                                @if($resource->document_blob || $resource->video_blob)
                                    <a href="{{ $resource->is_free ? route('dos.resources.download', $resource->id) : '#' }}" 
                                       class="btn btn-success btn-sm {{ !$resource->is_free ? 'disabled' : '' }}">
                                        <i class="bi bi-download"></i> {{ $resource->is_free ? 'Download' : 'Purchase Required' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No study resources found</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">
                        <i class="bi bi-plus-circle me-1"></i> Add First Resource
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add Resource Modal -->
<div class="modal fade" id="addResourceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Study Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addResourceForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="resource_title" class="form-label">Resource Title</label>
                            <input type="text" class="form-control" id="resource_title" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label">Teacher</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                @if(isset($teachers))
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @if(isset($courses))
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @if(isset($branches))
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="resource_type" class="form-label">Resource Type</label>
                            <select class="form-select" id="resource_type" name="resource_type" required>
                                <option value="">Select Type</option>
                                <option value="document">Document</option>
                                <option value="video">Video</option>
                                <option value="youtube">YouTube Link</option>
                            </select>
                        </div>
                        <div class="col-12" id="document_field" style="display:none;">
                            <label for="document_file" class="form-label">Document File</label>
                            <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf,.doc,.docx,.ppt,.pptx">
                        </div>
                        <div class="col-12" id="video_field" style="display:none;">
                            <label for="video_file" class="form-label">Video File</label>
                            <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                        </div>
                        <div class="col-12" id="youtube_field" style="display:none;">
                            <label for="youtube_link" class="form-label">YouTube Link</label>
                            <input type="url" class="form-control" id="youtube_link" name="youtube_link" 
                                   placeholder="https://youtube.com/watch?v=...">
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price (RWF)</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="is_free" class="form-label">Free Resource?</label>
                            <select class="form-select" id="is_free" name="is_free">
                                <option value="1">Free</option>
                                <option value="0">Paid</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Resource</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.resource-card {
    transition: transform 0.2s ease-in-out;
}
.resource-card:hover {
    transform: translateY(-2px);
}
</style>

<script>
function viewResource(id) {
    window.location.href = `/dos/resources/${id}`;
}

function editResource(id) {
    window.location.href = `/dos/resources/${id}/edit`;
}

function deleteResource(id) {
    if(confirm('Are you sure you want to delete this resource?')) {
        window.location.href = `/dos/resources/${id}/delete`;
    }
}

function exportResources() {
    window.location.href = `/dos/resources/export`;
}

// Handle resource type field visibility
document.getElementById('resource_type').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('document_field').style.display = type === 'document' ? 'block' : 'none';
    document.getElementById('video_field').style.display = type === 'video' ? 'block' : 'none';
    document.getElementById('youtube_field').style.display = type === 'youtube' ? 'block' : 'none';
});

// Handle free/paid toggle
document.getElementById('is_free').addEventListener('change', function() {
    const isFree = this.value === '1';
    document.getElementById('price').disabled = isFree;
    if(isFree) {
        document.getElementById('price').value = '0';
    }
});

// Form submission
document.getElementById('addResourceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/dos/resources/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Resource added successfully!');
            location.reload();
        } else {
            alert('Error adding resource: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding resource');
    });
});
</script>
@endsection
