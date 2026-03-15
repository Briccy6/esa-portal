@extends('layouts.admin')

@section('title', 'Create Blog Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create Blog Post</h1>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Blogs
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
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
                                              required>{{ old('content') }}</textarea>
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
                                                   value="{{ old('meta_title') }}"
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
                                                      placeholder="SEO description (optional)">{{ old('meta_description') }}</textarea>
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
                                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
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
                                                   {{ old('featured') ? 'checked' : '' }}>
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
                                        <h6 class="mb-0">Images</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="images" class="form-label">Upload Images</label>
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
                                        <div class="mb-3">
                                            <label for="documents" class="form-label">Upload Documents</label>
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
                                    <i class="fas fa-save"></i> Create Blog Post
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
</script>
@endpush
