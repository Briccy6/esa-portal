@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h1>Edit Babyeyi Document</h1>
            <small class="text-muted">Reference: {{ $babyeyi->reference_number }}</small>
        </div>
    </div>

    <form action="{{ route('babyeyi.update', $babyeyi) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Document Content</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group mb-3">
                            <label for="title" class="form-label"><strong>Document Title (Optional)</strong></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $babyeyi->title) }}"
                                   placeholder="Enter document title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rich Text Editor -->
                        <div class="form-group mb-3">
                            <label for="summernote" class="form-label"><strong>Content *</strong></label>
                            <textarea class="summernote @error('content') is-invalid @enderror" 
                                      id="summernote" name="content" required>{{ old('content', $babyeyi->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Trip Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Trip Information</h5>
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
                                   placeholder="e.g., Kigali, Nagano Park, Volcanoes National Park">
                            <small class="form-text text-muted">Leave empty if not yet determined. The letter will state it will be communicated.</small>
                            @error('trip_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="trip_fees" class="form-label">Trip Fees (RWF)</label>
                            <input type="number" class="form-control @error('trip_fees') is-invalid @enderror" 
                                   id="trip_fees" name="trip_fees" value="{{ old('trip_fees', $babyeyi->trip_fees) }}"
                                   placeholder="0.00" step="0.01" min="0">
                            @error('trip_fees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Document Info & Actions -->
            <div class="col-lg-4">
                <!-- Document Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Document Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted"><strong>Reference:</strong></small><br>
                            <code>{{ $babyeyi->reference_number }}</code>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="text-muted"><strong>Academic Year:</strong></small><br>
                            {{ $babyeyi->academicYear->name ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <small class="text-muted"><strong>Term:</strong></small><br>
                            {{ $babyeyi->term->name ?? 'N/A' }}
                        </div>
                        @if ($babyeyi->course)
                            <div class="mb-3">
                                <small class="text-muted"><strong>Course:</strong></small><br>
                                {{ $babyeyi->course->name }}
                            </div>
                        @endif
                        @if ($babyeyi->course_type)
                            <div class="mb-3">
                                <small class="text-muted"><strong>Course Type:</strong></small><br>
                                {{ $babyeyi->course_type }}
                            </div>
                        @endif
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted"><strong>Created by:</strong></small><br>
                            {{ $babyeyi->creator->name ?? 'System' }} @ {{ $babyeyi->created_at->format('M d, Y H:i') }}
                        </div>
                        @if ($babyeyi->editor)
                            <div class="mb-3">
                                <small class="text-muted"><strong>Last edited by:</strong></small><br>
                                {{ $babyeyi->editor->name }} @ {{ $babyeyi->updated_at->format('M d, Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Editor Tips -->
                <div class="card shadow mb-4 bg-light">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Editor Tips</h5>
                    </div>
                    <div class="card-body">
                        <small>
                            <p><strong>Format your content:</strong></p>
                            <ul class="pl-3 mb-2">
                                <li>Use <strong>Bold</strong> for important terms</li>
                                <li>Use <em>Italics</em> for emphasis</li>
                                <li>Create <u>Underline</u> for urgent items</li>
                                <li>Add links to relevant resources</li>
                                <li>Use bullet lists for conditions</li>
                                <li>Use numbered lists for sequential steps</li>
                            </ul>
                            <p><strong>Formatting buttons:</strong> Located above the editor</p>
                            <p><strong>Preview:</strong> After saving, you can preview before publishing</p>
                        </small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save & Preview
                    </button>
                    <a href="{{ route('babyeyi.show', $babyeyi) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View Current Version
                    </a>
                    <a href="{{ route('babyeyi.index') }}" class="btn btn-light border">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    @if ($babyeyi->status === 'draft')
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Delete Draft
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Include Summernote CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.js"></script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Delete Babyeyi Draft</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <strong>Are you sure you want to delete this draft babyeyi document?</strong>
                </p>
                <p class="mb-0 text-muted">
                    <small>Reference: <code>{{ $babyeyi->reference_number }}</code></small><br>
                    <small>This action cannot be undone.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('babyeyi.destroy', $babyeyi) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Yes, Delete it
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#summernote').summernote({
        placeholder: 'Enter the babyeyi content here...',
        tabsize: 2,
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                // Handle image upload if needed
            }
        }
    });
});
</script>
@endsection
