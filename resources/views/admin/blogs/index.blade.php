@extends('admin.layout')

@section('title', 'Blog Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark"><i class="fas fa-blog text-primary me-2"></i>Blog Management</h1>
            <p class="text-muted mb-0 small">Create, publish, and manage blog posts for the public site.</p>
        </div>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New blog post
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total posts</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Published</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($stats['published'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Drafts</div>
                    <div class="fs-4 fw-bold text-warning">{{ number_format($stats['drafts'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total views</div>
                    <div class="fs-4 fw-bold text-primary">{{ number_format($stats['views'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="fas fa-list me-2 text-secondary"></i>All posts</span>
            <span class="badge bg-secondary">{{ $blogs->total() }} total</span>
        </div>
        <div class="card-body p-0">
            @forelse($blogs as $blog)
                <div class="border-bottom px-3 py-3 px-md-4 hover-bg-light">
                    <div class="row align-items-center g-3">
                        <div class="col-auto d-none d-sm-block">
                            @if($blog->images->first())
                                <img src="{{ $blog->images->first()->url }}"
                                     alt=""
                                     class="rounded"
                                     style="width: 72px; height: 72px; object-fit: cover;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-secondary"
                                     style="width: 72px; height: 72px;">
                                    <i class="fas fa-image fa-lg"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col min-w-0">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h2 class="h6 mb-0 text-truncate">{{ $blog->title }}</h2>
                                @if($blog->is_featured)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Featured</span>
                                @endif
                                @php
                                    $st = $blog->status;
                                    $badgeClass = $st === 'published' ? 'bg-success' : ($st === 'draft' ? 'bg-secondary' : 'bg-dark');
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($st) }}</span>
                            </div>
                            <p class="text-muted small mb-2 mb-md-0 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 140) }}</p>
                            <div class="small text-muted">
                                <i class="far fa-eye me-1"></i>{{ number_format($blog->views ?? 0) }} views
                                <span class="mx-2">·</span>
                                {{ $blog->created_at->format('M j, Y') }}
                                @if($blog->images->count())
                                    <span class="mx-2">·</span>{{ $blog->images->count() }} image(s)
                                @endif
                                @if($blog->documents->count())
                                    <span class="mx-2">·</span>{{ $blog->documents->count() }} file(s)
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-lg-auto">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> View
                                </a>
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button"
                                        class="btn btn-sm {{ $blog->is_featured ? 'btn-warning' : 'btn-outline-warning' }}"
                                        data-featured-url="{{ route('admin.blogs.toggle-featured', $blog) }}"
                                        onclick="toggleBlogFeatured(this)"
                                        title="Toggle featured">
                                    <i class="fas fa-star"></i>
                                </button>
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this post permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 px-3">
                    <div class="text-muted mb-3"><i class="fas fa-newspaper fa-3x"></i></div>
                    <h3 class="h5">No blog posts yet</h3>
                    <p class="text-muted mb-4">Create your first post to show content on the public blog.</p>
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create blog post
                    </a>
                </div>
            @endforelse
        </div>
        @if($blogs->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</div>
<style>
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .min-w-0 { min-width: 0; }
</style>
@endsection

@push('scripts')
<script>
function toggleBlogFeatured(btn) {
    const url = btn.getAttribute('data-featured-url');
    if (!url) return;
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Could not update featured status.');
            btn.innerHTML = original;
            btn.disabled = false;
        }
    })
    .catch(function () {
        alert('Request failed.');
        btn.innerHTML = original;
        btn.disabled = false;
    });
}
</script>
@endpush
