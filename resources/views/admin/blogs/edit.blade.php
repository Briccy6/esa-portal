@extends('admin.layout')

@section('title', 'Edit Blog Post')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
<style>
.ql-container {
    min-height: 300px;
}
.ql-toolbar {
    border-radius: 8px 8px 0 0;
}
.ql-container {
    border-radius: 0 0 8px 8px;
}
.image-preview {
    position: relative;
    display: inline-block;
    margin: 5px;
}
.image-preview img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e9ecef;
}
.image-preview .remove-btn {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.document-item {
    display: flex;
    align-items: center;
    padding: 8px;
    margin: 5px 0;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}
.document-item .remove-btn {
    margin-left: auto;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 12px;
    cursor: pointer;
}
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
.upload-area:hover {
    border-color: #007bff;
    background: #f8f9ff;
}
.upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}
.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}
.form-section h5 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Blog Post</h1>
            <p class="text-muted mb-0">Update your blog content</p>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Blogs
        </a>
    </div>

    <form method="POST" action="{{ route('admin.blogs.update', $blog) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="form-section">
            <h5><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Blog Title *</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $blog->title) }}" required 
                               placeholder="Enter an engaging title for your blog post">
                        @error('title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $blog->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category" class="form-label fw-bold">Category</label>
                        <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $blog->category) }}" 
                               placeholder="e.g., Technology, Education, News">
                        @error('category')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tags" class="form-label fw-bold">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags', $blog->tags) }}" 
                               placeholder="e.g., web development, laravel, tutorial">
                        <small class="text-muted">Separate tags with commas</small>
                        @error('tags')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="excerpt" class="form-label fw-bold">Excerpt</label>
                <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $blog->excerpt) }}</textarea>
                <small class="text-muted">If left empty, it will be automatically generated from your content</small>
                @error('excerpt')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Featured Image -->
        <div class="form-section">
            <h5><i class="bi bi-image me-2"></i>Featured Image</h5>
            <div class="mb-3">
                <label for="featured_image" class="form-label fw-bold">Upload Featured Image</label>
                <input type="file" class="form-control" id="featured_image" name="featured_image" 
                       accept="image/*" onchange="previewFeaturedImage(this)">
                <small class="text-muted">Recommended size: 1200x630px. Max size: 2MB</small>
                @error('featured_image')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div id="featured_image_preview" class="mt-3">
                @if($blog->featured_image)
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="Current featured image">
                    </div>
                @endif
            </div>
        </div>

        <!-- Content Editor -->
        <div class="form-section">
            <h5><i class="bi bi-pencil-square me-2"></i>Blog Content *</h5>
            <div class="mb-3">
                <input type="hidden" name="content" id="content" value="{{ old('content', $blog->content) }}">
                <div id="editor">{{ $blog->content }}</div>
                @error('content')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Existing Images -->
        @if($blog->images->count() > 0)
        <div class="form-section">
            <h5><i class="bi bi-images me-2"></i>Existing Images</h5>
            <div class="mb-3">
                @foreach($blog->images as $image)
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->image_alt }}">
                        <button type="button" class="remove-btn" onclick="deleteExistingImage({{ $image->id }})">×</button>
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm mb-1" placeholder="Alt text" value="{{ $image->image_alt }}">
                            <input type="text" class="form-control form-control-sm" placeholder="Caption" value="{{ $image->image_caption }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Additional Images -->
        <div class="form-section">
            <h5><i class="bi bi-images me-2"></i>Add More Images</h5>
            <div class="mb-3">
                <div class="upload-area" onclick="document.getElementById('images').click()" 
                     ondrop="handleDrop(event)" ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                    <i class="bi bi-cloud-upload display-4 text-muted"></i>
                    <p class="mt-2 mb-0">Click to upload or drag and drop images here</p>
                    <small class="text-muted">Multiple images allowed (JPG, PNG, GIF - Max 2MB each)</small>
                </div>
                <input type="file" class="d-none" id="images" name="images[]" multiple accept="image/*" 
                       onchange="previewImages(this)">
                <div id="images_preview" class="mt-3"></div>
            </div>
        </div>

        <!-- Existing Documents -->
        @if($blog->documents->count() > 0)
        <div class="form-section">
            <h5><i class="bi bi-file-earmark-text me-2"></i>Existing Documents</h5>
            <div class="mb-3">
                @foreach($blog->documents as $document)
                    <div class="document-item">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span>{{ $document->document_name }}</span>
                        <small class="text-muted ms-2">({{ $document->formatted_size }})</small>
                        <a href="{{ asset('storage/' . $document->document_path) }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                            <i class="bi bi-download"></i>
                        </a>
                        <button type="button" class="remove-btn" onclick="deleteExistingDocument({{ $document->id }})">×</button>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Documents -->
        <div class="form-section">
            <h5><i class="bi bi-file-earmark-text me-2"></i>Add More Documents</h5>
            <div class="mb-3">
                <div class="upload-area" onclick="document.getElementById('documents').click()" 
                     ondrop="handleDocumentDrop(event)" ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                    <i class="bi bi-file-earmark-arrow-up display-4 text-muted"></i>
                    <p class="mt-2 mb-0">Click to upload or drag and drop documents here</p>
                    <small class="text-muted">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max 5MB each)</small>
                </div>
                <input type="file" class="d-none" id="documents" name="documents[]" multiple 
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" 
                       onchange="previewDocuments(this)">
                <div id="documents_preview" class="mt-3"></div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="form-section">
            <h5><i class="bi bi-search me-2"></i>SEO Settings</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                               value="{{ old('meta_title', $blog->meta_title) }}" placeholder="SEO title (optional)">
                        <small class="text-muted">If left empty, blog title will be used</small>
                        @error('meta_title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2">{{ old('meta_description', $blog->meta_description) }}</textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                        @error('meta_description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Update Blog
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// Initialize Quill editor
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['clean']
        ]
    },
    placeholder: 'Write your amazing blog content here...',
    theme: 'snow'
});

// Update hidden content field on form submit
document.querySelector('form').addEventListener('submit', function(e) {
    document.getElementById('content').value = quill.root.innerHTML;
});

// Featured image preview
function previewFeaturedImage(input) {
    const preview = document.getElementById('featured_image_preview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="image-preview">
                    <img src="${e.target.result}" alt="Featured image preview">
                </div>
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Multiple images preview
function previewImages(input) {
    const preview = document.getElementById('images_preview');
    
    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imageDiv = document.createElement('div');
            imageDiv.className = 'image-preview';
            imageDiv.innerHTML = `
                <img src="${e.target.result}" alt="Image preview">
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">×</button>
                <input type="hidden" name="image_alts[]" placeholder="Alt text">
                <input type="hidden" name="image_captions[]" placeholder="Caption">
            `;
            preview.appendChild(imageDiv);
        };
        
        reader.readAsDataURL(file);
    }
}

// Documents preview
function previewDocuments(input) {
    const preview = document.getElementById('documents_preview');
    
    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        const documentDiv = document.createElement('div');
        documentDiv.className = 'document-item';
        documentDiv.innerHTML = `
            <i class="bi bi-file-earmark-text me-2"></i>
            <span>${file.name}</span>
            <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">×</button>
        `;
        preview.appendChild(documentDiv);
    }
}

// Delete existing image
function deleteExistingImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/blogs/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the image.');
        });
    }
}

// Delete existing document
function deleteExistingDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        fetch(`/admin/blogs/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the document.');
        });
    }
}

// Drag and drop handlers
function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.querySelector('.upload-area').classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    const imagesInput = document.getElementById('images');
    imagesInput.files = files;
    previewImages(imagesInput);
}

function handleDocumentDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.querySelector('.upload-area').classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    const documentsInput = document.getElementById('documents');
    documentsInput.files = files;
    previewDocuments(documentsInput);
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove('dragover');
}

// Utility function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endsection
