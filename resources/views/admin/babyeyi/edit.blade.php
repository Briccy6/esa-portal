@extends('admin.layout')

@section('title', 'Edit Babyeyi')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-contract text-primary me-2"></i>
                Edit Babyeyi Document
            </h2>
            <div class="text-muted">
                Reference: <code>{{ $babyeyi->reference_number }}</code>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('babyeyi.preview', $babyeyi) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-1"></i> Preview
            </a>
            <a href="{{ route('babyeyi.show', $babyeyi) }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-alt me-1"></i> View
            </a>
            <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-dark">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('babyeyi.update', $babyeyi) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <strong>Document Content</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label"><strong>Document Title (Optional)</strong></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $babyeyi->title) }}"
                                   placeholder="Enter document title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="summernote" class="form-label"><strong>Content *</strong></label>
                            <textarea class="summernote @error('content') is-invalid @enderror"
                                      id="summernote"
                                      name="content"
                                      required>{{ old('content', $babyeyi->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-info text-white">
                        <strong>Trip Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="trip_known"
                                   name="trip_known"
                                   value="1"
                                   @checked($babyeyi->trip_known)>
                            <label class="form-check-label" for="trip_known">
                                <strong>Trip place is known</strong>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="trip_place" class="form-label">Trip Place / Destination</label>
                            <input type="text"
                                   class="form-control @error('trip_place') is-invalid @enderror"
                                   id="trip_place"
                                   name="trip_place"
                                   value="{{ old('trip_place', $babyeyi->trip_place) }}"
                                   placeholder="e.g., Kigali, Volcanoes National Park">
                            <div class="form-text">Leave empty if not yet determined. The letter will state it will be communicated.</div>
                            @error('trip_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="trip_fees" class="form-label">Trip Fees (RWF)</label>
                            <input type="number"
                                   class="form-control @error('trip_fees') is-invalid @enderror"
                                   id="trip_fees"
                                   name="trip_fees"
                                   value="{{ old('trip_fees', $babyeyi->trip_fees) }}"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0">
                            @error('trip_fees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">
                        <strong>Document Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block"><strong>Reference</strong></small>
                            <code>{{ $babyeyi->reference_number }}</code>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="text-muted d-block"><strong>Academic Year</strong></small>
                            {{ $babyeyi->academicYear->year ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block"><strong>Term</strong></small>
                            {{ $babyeyi->term->name ?? 'N/A' }}
                        </div>
                        @if ($babyeyi->course)
                            <div class="mb-3">
                                <small class="text-muted d-block"><strong>Course</strong></small>
                                {{ $babyeyi->course->name }}
                            </div>
                        @endif
                        @if ($babyeyi->course_type)
                            <div class="mb-3">
                                <small class="text-muted d-block"><strong>Course Type</strong></small>
                                {{ $babyeyi->course_type }}
                            </div>
                        @endif
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted d-block"><strong>Created by</strong></small>
                            {{ $babyeyi->creator->name ?? 'System' }} · {{ $babyeyi->created_at->format('M d, Y H:i') }}
                        </div>
                        @if ($babyeyi->editor)
                            <div class="mb-0">
                                <small class="text-muted d-block"><strong>Last edited by</strong></small>
                                {{ $babyeyi->editor->name }} · {{ $babyeyi->updated_at->format('M d, Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <strong><i class="fas fa-lightbulb me-1"></i> Tip</strong>
                    <div class="small mt-1">
                        Use the editor toolbar to format content (bold, lists, tables). After saving, use Preview before publishing.
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Save & Preview
                    </button>
                    @if ($babyeyi->status === 'draft')
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> Delete Draft
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Summernote CSS + JS (Bootstrap 5 build) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.js"></script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Babyeyi Draft</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                    <strong>Are you sure you want to delete this draft?</strong>
                </p>
                <p class="mb-0 text-muted">
                    <small>Reference: <code>{{ $babyeyi->reference_number }}</code></small><br>
                    <small>This action cannot be undone.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('babyeyi.destroy', $babyeyi) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Yes, delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('summernote');
    if (!el) return;

    $('#summernote').summernote({
        placeholder: 'Enter the babyeyi content here...',
        tabsize: 2,
        height: 420,
        minHeight: 300,
        maxHeight: 700,
        focus: false,
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

    const form = document.getElementById('editForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const content = $('#summernote').summernote('code');
            if (!content || content.trim() === '' || content.trim() === '<p><br></p>') {
                e.preventDefault();
                alert('Please enter content in the editor.');
                $('#summernote').summernote('focus');
            }
        });
    }
});
</script>
@endsection

