@extends('layouts.app')

@section('title', 'Blog')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <section class="bg-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Our Blog</h1>
                    <p class="lead">Stay updated with the latest news, insights, and stories from our community.</p>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-blog fa-5x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Posts -->
    @if($featuredBlogs->count() > 0)
        <section class="mb-5">
            <div class="container">
                <h2 class="text-center mb-4">Featured Posts</h2>
                <div class="row">
                    @foreach($featuredBlogs as $blog)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                @if($blog->images->count() > 0)
                                    <img src="{{ $blog->images->first()->url }}" 
                                         class="card-img-top" 
                                         alt="{{ $blog->images->first()->alt_text ?? $blog->title }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-star"></i> Featured
                                        </span>
                                        <small class="text-muted">{{ $blog->reading_time }} min read</small>
                                    </div>
                                    <h5 class="card-title">{{ $blog->title }}</h5>
                                    <p class="card-text">{{ $blog->excerpt }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                                        </small>
                                        <a href="{{ route('blogs.show', $blog) }}" class="btn btn-primary btn-sm">
                                            Read More
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- All Posts -->
    <section>
        <div class="container">
            <h2 class="text-center mb-4">Latest Posts</h2>
            <div class="row">
                @forelse($blogs as $blog)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            @if($blog->images->count() > 0)
                                <img src="{{ $blog->images->first()->url }}" 
                                     class="card-img-top" 
                                     alt="{{ $blog->images->first()->alt_text ?? $blog->title }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">{{ $blog->reading_time }} min read</small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye"></i> {{ $blog->view_count }}
                                    </small>
                                </div>
                                <h5 class="card-title">{{ $blog->title }}</h5>
                                <p class="card-text">{{ $blog->excerpt }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                                    </small>
                                    <a href="{{ route('blogs.show', $blog) }}" class="btn btn-outline-primary btn-sm">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-blog fa-4x text-muted mb-3"></i>
                        <h3>No blog posts found</h3>
                        <p class="text-muted">Check back later for new content.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($blogs->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endpush
