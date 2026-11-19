@extends('dos.layout')

@section('title', 'Resource Details')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>
            Resource Details
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('dos.resources.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <div class="btn-group">
                <a href="{{ route('dos.resources.edit', $resource->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                @if($resource->document_blob || $resource->video_blob)
                    <a href="{{ route('dos.resources.download', $resource->id) }}" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Resource Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Resource Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Resource ID:</label>
                            <p class="form-control-plaintext">#{{ $resource->id }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Type:</label>
                            <p class="form-control-plaintext">
                                @if($resource->document_blob && $resource->video_blob)
                                    <span class="badge bg-info">Document + Video</span>
                                @elseif($resource->document_blob)
                                    <span class="badge bg-primary">Document</span>
                                @elseif($resource->video_blob)
                                    <span class="badge bg-success">Video</span>
                                @elseif($resource->youtube_link)
                                    <span class="badge bg-danger">YouTube Link</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="form-control-plaintext">
                                @if($resource->is_public)
                                    @if($resource->is_free)
                                        <span class="badge bg-success">Public & Free</span>
                                    @else
                                        <span class="badge bg-primary">Public - ${{ number_format($resource->price, 2) }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-warning">Private</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Document Information -->
                    @if($resource->document_blob || $resource->document_name)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>
                                Document Details
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Filename:</label>
                                        <p class="form-control-plaintext">{{ $resource->document_name ?: 'Unnamed Document' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">File Type:</label>
                                        <p class="form-control-plaintext">{{ $resource->document_mime ?: 'Unknown' }}</p>
                                    </div>
                                </div>
                                @if($resource->watermark_applied)
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            <i class="bi bi-droplet me-1"></i>
                                            Watermark Applied
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Video Information -->
                    @if($resource->video_blob || $resource->video_name)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-play-circle me-2"></i>
                                Video Details
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Filename:</label>
                                        <p class="form-control-plaintext">{{ $resource->video_name ?: 'Unnamed Video' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">File Type:</label>
                                        <p class="form-control-plaintext">{{ $resource->video_mime ?: 'Unknown' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- YouTube Link -->
                    @if($resource->youtube_link)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-youtube me-2"></i>
                                YouTube Link
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <a href="{{ $resource->youtube_link }}" target="_blank" class="text-primary">
                                    {{ $resource->youtube_link }}
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Admin Notes -->
                    @if($resource->admin_notes)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-sticky-note me-2"></i>
                                Admin Notes
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ nl2br(e($resource->admin_notes)) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Information -->
        <div class="col-md-4">
            <!-- Associated Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg me-2"></i>
                        Associated With
                    </h6>
                </div>
                <div class="card-body">
                    @if($resource->teacher)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teacher:</label>
                            <p class="form-control-plaintext">
                                <i class="bi bi-person me-1"></i>
                                {{ $resource->teacher->first_name }} {{ $resource->teacher->last_name }}
                            </p>
                        </div>
                    @endif

                    @if($resource->course)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Course:</label>
                            <p class="form-control-plaintext">
                                <i class="bi bi-book me-1"></i>
                                {{ $resource->course->name }}
                            </p>
                        </div>
                    @endif

                    @if($resource->branch)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Branch:</label>
                            <p class="form-control-plaintext">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $resource->branch->name }}
                            </p>
                        </div>
                    @endif

                    @if($resource->lesson)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lesson:</label>
                            <p class="form-control-plaintext">
                                <i class="bi bi-journal-text me-1"></i>
                                {{ $resource->lesson->title }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Pricing Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price:</label>
                        <p class="form-control-plaintext">
                            @if($resource->is_free)
                                <span class="badge bg-success">FREE</span>
                            @else
                                ${{ number_format($resource->price, 2) }}
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Visibility:</label>
                        <p class="form-control-plaintext">
                            @if($resource->is_public)
                                <span class="badge bg-primary">Public</span>
                            @else
                                <span class="badge bg-warning">Private</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Technical Information -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Technical Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Created:</label>
                        <p class="form-control-plaintext">
                            <i class="bi bi-calendar-plus me-1"></i>
                            {{ $resource->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Last Updated:</label>
                        <p class="form-control-plaintext">
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $resource->updated_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                    @if($resource->watermark_path)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Watermark:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">
                                    <i class="bi bi-droplet me-1"></i>
                                    Applied
                                </span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
