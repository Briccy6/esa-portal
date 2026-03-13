@livewireStyles
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
@endlivewireStyles

<form wire:submit.prevent="save">
    <!-- Basic Information -->
    <div class="form-section">
        <h5><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Blog Title *</label>
                    <input type="text" class="form-control" id="title" wire:model="title" required 
                           placeholder="Enter an engaging title for your blog post">
                    @error('title')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status *</label>
                    <select class="form-select" id="status" wire:model="status" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
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
                    <input type="text" class="form-control" id="category" wire:model="category" 
                           placeholder="e.g., Technology, Education, News">
                    @error('category')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tags" class="form-label fw-bold">Tags</label>
                    <input type="text" class="form-control" id="tags" wire:model="tags" 
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
            <textarea class="form-control" id="excerpt" wire:model="excerpt" rows="3" 
                      placeholder="Brief summary of your blog post (optional)"></textarea>
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
            <input type="file" class="form-control" id="featured_image" wire:model="featured_image" 
                   accept="image/*">
            <small class="text-muted">Recommended size: 1200x630px. Max size: 2MB</small>
            @error('featured_image')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
        @if($featured_image)
            <div class="mt-3">
                <div class="image-preview">
                    <img src="{{ $featured_image->temporaryUrl() }}" alt="Featured image preview">
                </div>
            </div>
        @endif
    </div>

    <!-- Content Editor -->
    <div class="form-section">
        <h5><i class="bi bi-pencil-square me-2"></i>Blog Content *</h5>
        <div class="mb-3">
            <input type="hidden" wire:model="content">
            <div id="editor"></div>
            @error('content')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Existing Images (for edit mode) -->
    @if($blog && $blog->images->count() > 0)
        <div class="form-section">
            <h5><i class="bi bi-images me-2"></i>Existing Images</h5>
            <div class="mb-3">
                @foreach($blog->images as $image)
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->image_alt }}">
                        <button type="button" class="remove-btn" wire:click="deleteImage({{ $image->id }})">×</button>
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
        <h5><i class="bi bi-images me-2"></i>Additional Images</h5>
        <div class="mb-3">
            <div class="upload-area" onclick="document.getElementById('images').click()">
                <i class="bi bi-cloud-upload display-4 text-muted"></i>
                <p class="mt-2 mb-0">Click to upload images here</p>
                <small class="text-muted">Multiple images allowed (JPG, PNG, GIF - Max 2MB each)</small>
            </div>
            <input type="file" class="d-none" id="images" wire:model="images" multiple accept="image/*">
            <div class="mt-3">
                @foreach($images as $index => $image)
                    <div class="image-preview">
                        <img src="{{ $image->temporaryUrl() }}" alt="Image preview">
                        <button type="button" class="remove-btn" wire:click="removeImage('{{ $index }}')">×</button>
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm mb-1" placeholder="Alt text" wire:model="image_alts.{{ $index }}">
                            <input type="text" class="form-control form-control-sm" placeholder="Caption" wire:model="image_captions.{{ $index }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Existing Documents (for edit mode) -->
    @if($blog && $blog->documents->count() > 0)
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
                        <button type="button" class="remove-btn" wire:click="deleteDocument({{ $document->id }})">×</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Documents -->
    <div class="form-section">
        <h5><i class="bi bi-file-earmark-text me-2"></i>Documents</h5>
        <div class="mb-3">
            <div class="upload-area" onclick="document.getElementById('documents').click()">
                <i class="bi bi-file-earmark-arrow-up display-4 text-muted"></i>
                <p class="mt-2 mb-0">Click to upload documents here</p>
                <small class="text-muted">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max 5MB each)</small>
            </div>
            <input type="file" class="d-none" id="documents" wire:model="documents" multiple 
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
            <div class="mt-3">
                @foreach($documents as $index => $document)
                    <div class="document-item">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span>{{ $document->getClientOriginalName() }}</span>
                        <small class="text-muted ms-2">({{ formatFileSize($document->getSize()) }})</small>
                        <button type="button" class="remove-btn" wire:click="removeDocument('{{ $index }}')">×</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- SEO Settings -->
    <div class="form-section">
        <h5><i class="bi bi-search me-2"></i>SEO Settings</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" wire:model="meta_title" 
                           placeholder="SEO title (optional)">
                    <small class="text-muted">If left empty, blog title will be used</small>
                    @error('meta_title')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                    <textarea class="form-control" id="meta_description" wire:model="meta_description" rows="2" 
                              placeholder="SEO description (optional)"></textarea>
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
        <button type="button" wire:click="$emit('closeModal')" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle me-2"></i>Cancel
        </button>
        <div>
            <button type="submit" wire:target="saveAsDraft" class="btn btn-outline-primary me-2">
                <i class="bi bi-save me-2"></i>Save Draft
            </button>
            <button type="submit" wire:target="publish" class="btn btn-primary">
                <i class="bi bi-send me-2"></i>{{ $blog->id ? 'Update' : 'Publish' }} Blog
            </button>
        </div>
    </div>
</form>

@livewireScripts
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

// Update hidden content field on change
quill.on('text-change', function() {
    @this.set('content', quill.root.innerHTML);
});

// Handle different submit targets
window.addEventListener('livewire:init', () => {
    const saveButtons = document.querySelectorAll('button[wire\\:target]');
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const target = this.getAttribute('wire:target');
            if (target === 'saveAsDraft') {
                @this.set('status', 'draft');
            } else if (target === 'publish') {
                @this.set('status', 'published');
            }
        });
    });
});

// Utility function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endlivewireScripts
