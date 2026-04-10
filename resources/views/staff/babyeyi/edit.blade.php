@extends('staff.partials.sidebar_sec')

@section('content')
<div class="page-content">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-edit text-primary"></i> Edit Babyeyi Document
                    </h1>
                    <p class="text-muted mb-0">Reference: <code>{{ $babyeyi->reference_number }}</code> | Status: <span class="badge bg-info">{{ ucfirst($babyeyi->status) }}</span></p>
                </div>
                <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('babyeyi.update', $babyeyi) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-9">
                <!-- Title Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-heading"></i> Document Title
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="title" class="form-label">
                                <strong>Title (Optional)</strong>
                            </label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $babyeyi->title) }}"
                                   placeholder="e.g., ACCOUNTING - Term 1 Registration">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Give your document a meaningful title
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Content Editor Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-pen-fancy"></i> Document Content
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="summernote" class="form-label">
                                <strong>Content</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="summernote @error('content') is-invalid @enderror" 
                                      id="summernote" name="content" required>{{ old('content', $babyeyi->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-lightbulb"></i> Use the formatting toolbar above to style your document
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Trip Information Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bus"></i> Trip Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="trip_known" 
                                   name="trip_known" value="1" @checked($babyeyi->trip_known)>
                            <label class="form-check-label" for="trip_known">
                                <strong>Trip place is known</strong>
                            </label>
                        </div>

                        <div class="form-group mb-3">
                            <label for="trip_place" class="form-label">Trip Place / Destination</label>
                            <input type="text" class="form-control @error('trip_place') is-invalid @enderror" 
                                   id="trip_place" name="trip_place" value="{{ old('trip_place', $babyeyi->trip_place) }}"
                                   placeholder="e.g., Kigali, Volcanoes National Park">
                            @error('trip_place')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                Leave empty if not yet determined - the letter will state it will be communicated
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="trip_fees" class="form-label">Trip Fees (RWF)</label>
                            <input type="number" class="form-control @error('trip_fees') is-invalid @enderror" 
                                   id="trip_fees" name="trip_fees" value="{{ old('trip_fees', $babyeyi->trip_fees) }}"
                                   placeholder="0.00" step="0.01" min="0">
                            @error('trip_fees')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Information & Actions -->
            <div class="col-lg-3">
                <!-- Document Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Document Information
                        </h5>
                    </div>
                    <div class="card-body small">
                        <div class="mb-3">
                            <strong class="text-muted">Reference Number:</strong><br>
                            <code class="bg-light px-2 py-1 rounded d-inline-block">{{ $babyeyi->reference_number }}</code>
                        </div>
                        <hr class="my-2">
                        <div class="mb-3">
                            <strong class="text-muted">Academic Year:</strong><br>
                            {{ $babyeyi->academicYear->year ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong class="text-muted">Term:</strong><br>
                            {{ $babyeyi->term->name ?? 'N/A' }}
                        </div>
                        @if ($babyeyi->course)
                            <div class="mb-3">
                                <strong class="text-muted">Course:</strong><br>
                                {{ $babyeyi->course->course_name ?? $babyeyi->course->name }}
                            </div>
                        @endif
                        @if ($babyeyi->course_type)
                            <div class="mb-3">
                                <strong class="text-muted">Course Type:</strong><br>
                                {{ $babyeyi->course_type }}
                            </div>
                        @endif
                        <hr class="my-2">
                        <div class="mb-2">
                            <strong class="text-muted">Created:</strong><br>
                            <small>{{ $babyeyi->creator->name ?? 'System' }} - {{ $babyeyi->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        @if ($babyeyi->editor)
                            <div class="mb-3">
                                <strong class="text-muted">Last Edited:</strong><br>
                                <small>{{ $babyeyi->editor->name }} - {{ $babyeyi->updated_at->format('M d, Y H:i') }}</small>
                            </div>
                        @endif
                        <hr class="my-2">
                        <div>
                            <strong class="text-muted">Status:</strong><br>
                            @if ($babyeyi->status === 'draft')
                                <span class="badge bg-warning text-dark">Draft</span>
                            @elseif ($babyeyi->status === 'preview')
                                <span class="badge bg-info">Preview</span>
                            @else
                                <span class="badge bg-success">Published</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Editor Tips Card -->
                <div class="card shadow-sm border-0 bg-light mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb"></i> Tips
                        </h5>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2"><strong>Formatting Options:</strong></p>
                        <ul class="ps-3 mb-0">
                            <li>Bold for emphasis</li>
                            <li>Italics for terms</li>
                            <li>Lists for items</li>
                            <li>Tables for data</li>
                            <li>Links for resources</li>
                            <li>Images for visuals</li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('babyeyi.preview', $babyeyi) }}" class="btn btn-info btn-lg" target="_blank">
                        <i class="fas fa-eye"></i> Preview
                    </a>
                    @if ($babyeyi->status !== 'published')
                        <form action="{{ route('babyeyi.publish', $babyeyi) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-globe"></i> Publish Now
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-primary w-100 btn-lg" disabled>
                            <i class="fas fa-check-circle"></i> Published
                        </button>
                    @endif
                    <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Include jQuery (for Summernote) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Summernote CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.js"></script>

<script>
$(document).ready(function() {
    $('#summernote').summernote({
        placeholder: 'Enter the babyeyi content here...',
        tabsize: 2,
        height: 400,
        minHeight: 350,
        maxHeight: 700,
        focus: true,
        toolbar: [
            ['style', ['style', 'strikethrough', 'superscript', 'subscript']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'hr']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // Validate content before submit
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const content = $('#summernote').summernote('code');
        if (!content || content.trim() === '<p><br></p>' || content.trim() === '') {
            e.preventDefault();
            alert('Please enter content in the editor.');
            $('#summernote').focus();
        }
    });
});
</script>

@endsection
