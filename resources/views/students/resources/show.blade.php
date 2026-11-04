@extends('students.app')

@section('content')
@php
    use Illuminate\Support\Str;
    $resourceTitle = $resource->document_name ?: $resource->video_name ?: ($resource->youtube_link ? 'YouTube Resource' : 'Educational Resource');
    $resourceType = $resource->document_blob ? 'Document' : ($resource->video_blob ? 'Video' : ($resource->youtube_link ? 'YouTube Link' : 'Resource'));
@endphp

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Resource Details</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="fw-bold">{{ $resourceTitle }}</h5>
                        <p class="text-muted mb-1">Type: {{ $resourceType }}</p>
                        <p class="text-muted mb-0">Uploaded: {{ $resource->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Course:</strong> {{ $resource->course->course_name ?? 'General Course' }}</p>
                            <p class="mb-1"><strong>Lesson:</strong> {{ $resource->lesson->title ?? 'Not specified' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Teacher:</strong> {{ $resource->teacher->teacher_name ?? 'Unknown Teacher' }}</p>
                            <p class="mb-1"><strong>Branch:</strong> {{ $resource->branch->name ?? 'Not specified' }}</p>
                        </div>
                    </div>

                    @if($resource->admin_notes)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-secondary">Description</h6>
                            <p>{{ $resource->admin_notes }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <span class="badge bg-{{ $resource->is_free ? 'success' : 'warning' }} text-white">
                            {{ $resource->is_free ? 'Free' : 'Paid' }}
                        </span>
                        <span class="badge bg-info text-white ms-2">
                            {{ $resourceType }}
                        </span>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @if($resource->youtube_link)
                            <a href="{{ $resource->youtube_link }}" target="_blank" class="btn btn-outline-danger">
                                <i class="bi bi-youtube"></i> View on YouTube
                            </a>
                        @endif

                        @if($resource->document_blob || $resource->video_blob)
                            @if($resource->is_free)
                                <a href="{{ route('student.resources.download', $resource->id) }}" class="btn btn-success">
                                    <i class="bi bi-download"></i> Download Resource
                                </a>
                            @else
                                <a href="{{ route('student.resources.payment', $resource->id) }}" class="btn btn-warning">
                                    <i class="bi bi-cart"></i> Purchase Access
                                </a>
                            @endif
                        @endif

                        <a href="{{ route('student.resources.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to resources
                        </a>
                    </div>

                    @if($resource->video_blob)
                        <div class="ratio ratio-16x9 mb-4">
                            <video controls class="w-100 h-100">
                                <source src="{{ route('student.resources.download', $resource->id) }}" type="{{ $resource->video_mime ?: 'video/mp4' }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endif

                    @if($resource->document_blob)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-secondary">Preview</h6>
                            <div class="border rounded overflow-hidden" style="min-height:500px;">
                                <iframe src="{{ route('student.resources.download', $resource->id) }}#toolbar=0&navpanes=0&scrollbar=0" 
                                        class="w-100" 
                                        style="min-height:500px;border:none;"
                                        onerror="this.parentElement.innerHTML='<div class=\'alert alert-warning m-3\'><i class=\'bi bi-exclamation-triangle me-2\'></i>Preview not available. Please download the file to view it.</div>'">
                                </iframe>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">If the preview doesn't load, please download the file using the button above.</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
