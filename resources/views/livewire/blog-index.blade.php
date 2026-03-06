@livewireStyles
<style>
.blog-card {
    transition: transform 0.2s ease-in-out;
    border-left: 4px solid transparent;
}
.blog-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.blog-card.published {
    border-left-color: #28a745;
}
.blog-card.draft {
    border-left-color: #ffc107;
}
.blog-card.archived {
    border-left-color: #6c757d;
}
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
.featured-star {
    color: #ffc107;
}
.search-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.blog-image-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Blog Management</h1>
            <p class="text-muted mb-0">Manage your blog posts and content</p>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Blog
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <p class="mb-0">Total Blogs</p>
                        </div>
                        <i class="bi bi-file-text fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['published'] }}</h3>
                            <p class="mb-0">Published</p>
                        </div>
                        <i class="bi bi-check-circle fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['draft'] }}</h3>
                            <p class="mb-0">Drafts</p>
                        </div>
                        <i class="bi bi-pencil fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['archived'] }}</h3>
                            <p class="mb-0">Archived</p>
                        </div>
                        <i class="bi bi-archive fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" 
                               class="form-control search-box" placeholder="Search blogs...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Blogs List -->
    <div class="card">
        <div class="card-body">
            @if($blogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Blog</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blogs as $blog)
                                <tr class="blog-card {{ $blog->status }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($blog->featured_image)
                                                <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="blog-image-thumb me-3">
                                            @else
                                                <div class="blog-image-thumb me-3 bg-light d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-1 me-2">{{ Str::limit($blog->title, 50) }}</h6>
                                                    @if($blog->is_featured)
                                                        <i class="bi bi-star-fill featured-star" title="Featured"></i>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ Str::limit(strip_tags($blog->content), 100) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($blog->author->first_name ?? 'A', 0, 1)) }}
                                            </div>
                                            <span>{{ $blog->author->first_name ?? 'Admin' }} {{ $blog->author->last_name ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($blog->category)
                                            <span class="badge bg-info">{{ $blog->category }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge status-badge bg-{{ $blog->status === 'published' ? 'success' : ($blog->status === 'draft' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($blog->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="bi bi-eye me-1"></i>{{ number_format($blog->views) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($blog->published_at)
                                            <small class="text-muted">{{ $blog->published_at->format('M d, Y') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button wire:click="$set('selectedBlogId', $blog->id)" 
                                                    wire:click="$set('showEditModal', true)" 
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button wire:click="toggleFeatured({{ $blog->id }})" 
                                                    class="btn btn-sm btn-outline-warning" title="Toggle Featured">
                                                <i class="bi bi-star{{ $blog->is_featured ? '-fill' : '' }}"></i>
                                            </button>
                                            <a href="{{ route('blogs.show', $blog) }}" target="_blank" 
                                                    class="btn btn-sm btn-outline-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button wire:click="deleteBlog({{ $blog->id }})" 
                                                    class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $blogs->firstItem() }} to {{ $blogs->lastItem() }} of {{ $blogs->total() }} entries
                    </div>
                    {{ $blogs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-text display-1 text-muted"></i>
                    <h4 class="mt-3">No blogs found</h4>
                    <p class="text-muted">Get started by creating your first blog post.</p>
                    <button wire:click="$set('showCreateModal', true)" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Blog
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Loading Overlay -->
    @if($this->isUploading)
        <div class="loading-overlay">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Processing...</span>
            </div>
        </div>
    @endif
</div>

<!-- Create/Edit Modal -->
@if($showCreateModal || $showEditModal)
    <div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-{{ $showEditModal ? 'pencil' : 'plus-circle' }} me-2"></i>
                        {{ $showEditModal ? 'Edit' : 'Create' }} Blog Post
                    </h5>
                    <button wire:click="$set('showCreateModal', false); $set('showEditModal', false)" 
                            class="btn-close" type="button">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <livewire:blog-form :blog-id="$showEditModal ? $selectedBlogId : null" />
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@livewireScripts
<script>
window.addEventListener('blog-saved', function(event) {
    const { type, message, blogId } = event.detail;
    
    // Close modal
    @this.set('showCreateModal', false);
    @this.set('showEditModal', false);
    
    // Show message
    if (type === 'success') {
        // Refresh the blog list
        @this.call('$refresh');
    }
});
</script>
@endsection
