@extends('layouts.app')

@section('title', 'Edit Blog Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Blog Post</h1>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Blogs
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $blog->title) }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" 
                                              name="content" 
                                              rows="15" 
                                              required>{{ old('content', $blog->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" 
                                                   class="form-control @error('meta_title') is-invalid @enderror" 
                                                   id="meta_title" 
                                                   name="meta_title" 
                                                   value="{{ old('meta_title', $blog->meta_title) }}"
                                                   placeholder="SEO title (optional)">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                      id="meta_description" 
                                                      name="meta_description" 
                                                      rows="3" 
                                                      placeholder="SEO description (optional)">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Publish Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" 
                                                    name="status" 
                                                    required>
                                                <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                                                <option value="archived" {{ old('status', $blog->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input @error('featured') is-invalid @enderror" 
                                                   type="checkbox" 
                                                   id="featured" 
                                                   name="featured" 
                                                   value="1"
                                                   {{ old('featured', $blog->featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured">
                                                Featured Post
                                            </label>
                                            @error('featured')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Current Images</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($blog->images->count() > 0)
                                            @foreach($blog->images as $image)
                                                <div class="mb-3 p-2 border rounded">
                                                    <img src="{{ $image->url }}" class="img-thumbnail mb-2" style="max-height: 100px;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ $image->caption ?? 'No caption' }}</small>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="deleteImage({{ $image->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">No images uploaded yet.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Add New Images</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <input type="file" 
                                                   class="form-control @error('images') is-invalid @enderror" 
                                                   id="images" 
                                                   name="images[]" 
                                                   multiple 
                                                   accept="image/*">
                                            @error('images')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                You can upload multiple images. Max size: 2MB per image.
                                            </small>
                                        </div>

                                        <div id="image-previews" class="mt-3"></div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Documents</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($blog->documents->count() > 0)
                                            @foreach($blog->documents as $document)
                                                <div class="mb-2 p-2 border rounded">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="fas fa-file"></i> {{ $document->document_name }}
                                                            <small class="text-muted d-block">{{ $document->human_file_size }}</small>
                                                        </div>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="deleteDocument({{ $document->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">No documents uploaded yet.</p>
                                        @endif

                                        <hr>

                                        <div class="mb-3">
                                            <label for="documents" class="form-label">Add New Documents</label>
                                            <input type="file" 
                                                   class="form-control @error('documents') is-invalid @enderror" 
                                                   id="documents" 
                                                   name="documents[]" 
                                                   multiple 
                                                   accept=".pdf,.doc,.docx">
                                            @error('documents')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Upload PDF, DOC, or DOCX files. Max size: 5MB per file.
                                            </small>
                                        </div>

                                        <div id="document-previews" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Blog Post
                                </button>
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('images').addEventListener('change', function(e) {
    const previews = document.getElementById('image-previews');
    previews.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `
            <img src="${URL.createObjectURL(file)}" class="img-thumbnail" style="max-height: 100px;">
            <div class="mt-1">
                <input type="text" class="form-control form-control-sm" placeholder="Caption" name="image_captions[${index}]">
                <input type="text" class="form-control form-control-sm mt-1" placeholder="Alt Text" name="image_alt_texts[${index}]">
            </div>
        `;
        previews.appendChild(div);
    });
});

document.getElementById('documents').addEventListener('change', function(e) {
    const previews = document.getElementById('document-previews');
    previews.innerHTML = '';
    
    Array.from(e.target.files).forEach(file => {
        const div = document.createElement('div');
        div.className = 'mb-2 p-2 border rounded';
        div.innerHTML = `
            <i class="fas fa-file"></i> ${file.name}
            <small class="text-muted d-block">Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</small>
        `;
        previews.appendChild(div);
    });
});

function deleteImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/blogs/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the image.');
        });
    }
}

function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        fetch(`/admin/blogs/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the document.');
        });
    }
}
</script>
@endpush
