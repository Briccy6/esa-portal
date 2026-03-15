@extends('layouts.admin')

@section('title', 'Blog Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Blog Management</h1>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Blog
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Views</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blogs as $blog)
                                    <tr>
                                        <td>
                                            <strong>{{ $blog->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($blog->excerpt, 80) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $blog->status === 'published' ? 'success' : ($blog->status === 'draft' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($blog->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($blog->featured)
                                                <span class="badge bg-primary">Featured</span>
                                            @else
                                                <span class="text-muted">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $blog->view_count }}</td>
                                        <td>{{ $blog->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('blogs.show', $blog) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Public">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.blogs.edit', $blog) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        onclick="toggleFeatured({{ $blog->id }})"
                                                        title="Toggle Featured">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <form action="{{ route('admin.blogs.destroy', $blog) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this blog post?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                                            <h5>No blog posts found</h5>
                                            <p class="text-muted">Create your first blog post to get started.</p>
                                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Create Blog Post
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($blogs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $blogs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFeatured(blogId) {
    fetch(`/admin/blogs/${blogId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
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
        alert('An error occurred while toggling featured status.');
    });
}
</script>
@endpush
