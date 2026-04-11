@extends('admin.layout')

@section('title', 'Edit Blog Post')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-edit text-primary me-2"></i>Edit blog post</h1>
            <p class="text-muted small mb-0">Update content, status, and media.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($blog->status === 'published')
                <a href="{{ route('blogs.show', $blog) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i> View live
                </a>
            @endif
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to list
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                   value="{{ old('title', $blog->title) }}" required maxlength="255" placeholder="Post title">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="16" required
                                      placeholder="HTML allowed">{{ old('content', $blog->content) }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">You can use HTML in the body.</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light"><i class="fas fa-search me-2"></i>SEO (optional)</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="meta_title" class="form-label">Meta title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title"
                                       value="{{ old('meta_title', $blog->meta_title) }}" maxlength="60">
                                <div class="form-text" id="meta_title_counter">{{ strlen(old('meta_title', $blog->meta_title ?? '')) }}/60</div>
                                @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="meta_description" class="form-label">Meta description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="3" maxlength="160">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                <div class="form-text" id="meta_desc_counter">{{ strlen(old('meta_description', $blog->meta_description ?? '')) }}/160</div>
                                @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light"><i class="fas fa-paper-plane me-2"></i>Publish</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" @selected(old('status', $blog->status) === 'draft')>Draft</option>
                                <option value="published" @selected(old('status', $blog->status) === 'published')>Published</option>
                                <option value="archived" @selected(old('status', $blog->status) === 'archived')>Archived</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $blog->is_featured))>
                            <label class="form-check-label" for="is_featured"><i class="fas fa-star text-warning me-1"></i> Featured post</label>
                        </div>
                    </div>
                </div>

                @if($blog->images->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light"><i class="fas fa-images me-2"></i>Current images</div>
                    <div class="card-body">
                        @foreach($blog->images as $image)
                        <div class="d-flex align-items-center gap-2 border rounded p-2 mb-2" id="image-row-{{ $image->id }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?? $blog->title }}"
                                 class="rounded flex-shrink-0" style="width:56px;height:56px;object-fit:cover">
                            <div class="flex-grow-1 min-w-0 small">
                                <div class="fw-medium text-truncate">{{ $image->caption ?? 'No caption' }}</div>
                                <div class="text-muted text-truncate">{{ $image->alt_text ?? 'No alt text' }}</div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                    onclick="deleteBlogImage({{ $image->id }}, '{{ route('admin.blogs.delete-image', $image) }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($blog->documents->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light"><i class="fas fa-file-alt me-2"></i>Current documents</div>
                    <div class="card-body">
                        @foreach($blog->documents as $document)
                        <div class="d-flex align-items-center gap-2 border rounded p-2 mb-2" id="doc-row-{{ $document->id }}">
                            <span class="fs-4 flex-shrink-0">{{ str_contains($document->mime_type, 'pdf') ? '📄' : '📝' }}</span>
                            <div class="flex-grow-1 min-w-0 small">
                                <div class="fw-medium text-truncate">{{ $document->document_name }}</div>
                                <div class="text-muted">{{ number_format($document->file_size / 1024, 1) }} KB</div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                    onclick="deleteBlogDocument({{ $document->id }}, '{{ route('admin.blogs.delete-document', $document) }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light"><i class="fas fa-plus-circle me-2"></i>Add images</div>
                    <div class="card-body">
                        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">JPEG, PNG, GIF. Max 2 MB each.</div>
                        @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div id="image-previews" class="mt-3 small"></div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light"><i class="fas fa-file-upload me-2"></i>Add documents</div>
                    <div class="card-body">
                        <input type="file" class="form-control @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx">
                        <div class="form-text">PDF, Word. Max 5 MB each.</div>
                        @error('documents')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div id="document-previews" class="mt-3 small"></div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Update post
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var img = document.getElementById('images');
    if (img) img.addEventListener('change', function (e) {
        var el = document.getElementById('image-previews');
        el.innerHTML = '';
        Array.from(e.target.files || []).forEach(function (file) {
            if (!file.type.startsWith('image/')) return;
            var row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-2 border rounded p-2 mb-2';
            row.innerHTML = '<img src="' + URL.createObjectURL(file) + '" class="rounded" style="width:48px;height:48px;object-fit:cover">' +
                '<div class="text-truncate"><strong>' + file.name + '</strong><br><span class="text-muted">' + (file.size / 1024 / 1024).toFixed(2) + ' MB</span></div>';
            el.appendChild(row);
        });
    });
    var doc = document.getElementById('documents');
    if (doc) doc.addEventListener('change', function (e) {
        var el = document.getElementById('document-previews');
        el.innerHTML = '';
        Array.from(e.target.files || []).forEach(function (file) {
            var row = document.createElement('div');
            row.className = 'border rounded p-2 mb-2';
            row.textContent = file.name + ' — ' + (file.size / 1024 / 1024).toFixed(2) + ' MB';
            el.appendChild(row);
        });
    });
    var mt = document.getElementById('meta_title');
    if (mt) mt.addEventListener('input', function () {
        document.getElementById('meta_title_counter').textContent = this.value.length + '/60';
    });
    var md = document.getElementById('meta_description');
    if (md) md.addEventListener('input', function () {
        document.getElementById('meta_desc_counter').textContent = this.value.length + '/160';
    });
})();

function deleteBlogImage(imageId, url) {
    if (!confirm('Delete this image? This cannot be undone.')) return;
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            var row = document.getElementById('image-row-' + imageId);
            if (row) row.remove();
        } else {
            alert('Failed to delete image.');
        }
    })
    .catch(function () { alert('An error occurred.'); });
}

function deleteBlogDocument(docId, url) {
    if (!confirm('Delete this document? This cannot be undone.')) return;
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            var row = document.getElementById('doc-row-' + docId);
            if (row) row.remove();
        } else {
            alert('Failed to delete document.');
        }
    })
    .catch(function () { alert('An error occurred.'); });
}
</script>
@endpush
