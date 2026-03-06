@extends('admin.layout')

@section('title', 'Blog Management')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Blog Management</h1>
            <p class="text-muted mb-0">Manage your blog posts and content</p>
        </div>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Blog
        </a>
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
            <form method="GET" action="{{ route('admin.blogs.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control search-box" placeholder="Search blogs..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
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
                                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleFeatured({{ $blog->id }})" title="Toggle Featured">
                                                <i class="bi bi-star{{ $blog->is_featured ? '-fill' : '' }}"></i>
                                            </button>
                                            <a href="{{ route('blogs.show', $blog) }}" target="_blank" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteBlog({{ $blog->id }})" title="Delete">
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
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Blog
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleFeatured(blogId) {
    fetch(`/admin/blogs/${blogId}/toggle-featured`, {
        method: 'POST',
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
        alert('An error occurred while toggling featured status.');
    });
}

function deleteBlog(blogId) {
    if (confirm('Are you sure you want to delete this blog? This action cannot be undone.')) {
        if (confirm('This will permanently delete the blog and all associated images and documents. Are you absolutely sure?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/blogs/${blogId}`;
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endsection
