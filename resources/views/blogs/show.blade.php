@extends('layouts.app')

@section('title', $blog->title)

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
.blog-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    margin-bottom: 40px;
}
.blog-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #2c3e50;
}
.blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4, .blog-content h5, .blog-content h6 {
    color: #2c3e50;
    font-weight: 600;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
}
.blog-content h1 { font-size: 2.5rem; }
.blog-content h2 { font-size: 2rem; }
.blog-content h3 { font-size: 1.75rem; }
.blog-content h4 { font-size: 1.5rem; }
.blog-content h5 { font-size: 1.25rem; }
.blog-content h6 { font-size: 1.1rem; }
.blog-content p {
    margin-bottom: 1.5rem;
}
.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 2rem 0;
}
.blog-content blockquote {
    border-left: 4px solid #667eea;
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #6c757d;
}
.blog-content ul, .blog-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}
.blog-content li {
    margin-bottom: 0.5rem;
}
.blog-content code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.9rem;
}
.blog-content pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 2rem 0;
}
.blog-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
}
.blog-content th, .blog-content td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    text-align: left;
}
.blog-content th {
    background: #f8f9fa;
    font-weight: 600;
}
.author-bio {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2rem;
    margin-top: 3rem;
}
.author-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #667eea;
    color: white;
    display: flex;
    align-items: center;
    justify-content-center;
    font-size: 2rem;
    font-weight: 600;
}
.related-blog {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    height: 100%;
}
.related-blog:hover {
    transform: translateY(-5px);
}
.related-blog img {
    height: 150px;
    object-fit: cover;
}
.category-badge {
    background: #667eea;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}
.blog-meta {
    color: #6c757d;
    font-size: 0.9rem;
}
.blog-meta i {
    margin-right: 0.25rem;
}
.share-buttons {
    position: sticky;
    top: 100px;
}
.share-btn {
    display: block;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    color: white;
}
.share-btn:hover {
    transform: scale(1.1);
}
.share-facebook { background: #1877f2; }
.share-twitter { background: #1da1f2; }
.share-linkedin { background: #0077b5; }
.share-whatsapp { background: #25d366; }
.share-email { background: #ea4335; }
.document-list {
    list-style: none;
    padding: 0;
}
.document-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: background 0.3s ease;
}
.document-item:hover {
    background: #e9ecef;
}
.document-item i {
    font-size: 1.5rem;
    margin-right: 1rem;
    color: #667eea;
}
</style>
@endsection

@section('content')
<!-- Blog Header -->
<section class="blog-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blogs.index') }}" class="text-white">Blog</a></li>
                        <li class="breadcrumb-item active text-white">{{ $blog->title }}</li>
                    </ol>
                </nav>
                @if($blog->category)
                    <span class="category-badge d-inline-block mb-3">{{ $blog->category }}</span>
                @endif
                <h1 class="display-5 fw-bold mb-3">{{ $blog->title }}</h1>
                <div class="blog-meta text-white-50">
                    <div class="d-flex align-items-center mb-2">
                        <div class="author-avatar me-3">
                            {{ strtoupper(substr($blog->author->first_name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold text-white">{{ $blog->author->first_name ?? 'Admin' }} {{ $blog->author->last_name ?? '' }}</div>
                            <div class="small">
                                <i class="bi bi-calendar"></i> {{ $blog->published_at->format('F d, Y') }}
                                <span class="ms-3"><i class="bi bi-clock"></i> {{ $blog->reading_time }} min read</span>
                                <span class="ms-3"><i class="bi bi-eye"></i> {{ number_format($blog->views) }} views</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="mb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Featured Image -->
                @if($blog->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="img-fluid rounded">
                    </div>
                @endif

                <!-- Blog Content -->
                <div class="blog-content">
                    {!! $blog->content !!}
                </div>

                <!-- Tags -->
                @if($blog->tags)
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Tags:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(explode(',', $blog->tags) as $tag)
                                <span class="badge bg-light text-dark">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Documents -->
                @if($blog->documents->count() > 0)
                    <div class="mt-5">
                        <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Download Documents</h5>
                        <ul class="document-list">
                            @foreach($blog->documents as $document)
                                <li class="document-item">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $document->document_name }}</div>
                                        <small class="text-muted">{{ $document->formatted_size }}</small>
                                    </div>
                                    <a href="{{ $document->download_url }}" class="btn btn-primary btn-sm" download>
                                        <i class="bi bi-download"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Author Bio -->
                <div class="author-bio">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="author-avatar mx-auto">
                                {{ strtoupper(substr($blog->author->first_name ?? 'A', 0, 1)) }}
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h5 class="mb-1">{{ $blog->author->first_name ?? 'Admin' }} {{ $blog->author->last_name ?? '' }}</h5>
                            <p class="text-muted mb-2">Blog Author</p>
                            <p class="mb-0">Passionate about sharing knowledge and insights through engaging content. Follow for more articles on education, technology, and personal development.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h6 class="mb-3">Share Article</h6>
                    <button class="share-btn share-facebook" onclick="shareOnFacebook()" title="Share on Facebook">
                        <i class="bi bi-facebook"></i>
                    </button>
                    <button class="share-btn share-twitter" onclick="shareOnTwitter()" title="Share on Twitter">
                        <i class="bi bi-twitter"></i>
                    </button>
                    <button class="share-btn share-linkedin" onclick="shareOnLinkedIn()" title="Share on LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </button>
                    <button class="share-btn share-whatsapp" onclick="shareOnWhatsApp()" title="Share on WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </button>
                    <button class="share-btn share-email" onclick="shareByEmail()" title="Share via Email">
                        <i class="bi bi-envelope"></i>
                    </button>
                </div>

                <!-- Related Blogs -->
                @if($relatedBlogs->count() > 0)
                    <div class="mt-5">
                        <h5 class="mb-4">Related Articles</h5>
                        <div class="row g-3">
                            @foreach($relatedBlogs as $relatedBlog)
                                <div class="col-12">
                                    <div class="card related-blog">
                                        @if($relatedBlog->featured_image)
                                            <img src="{{ asset('storage/' . $relatedBlog->featured_image) }}" class="card-img-top" alt="{{ $relatedBlog->title }}">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                                <i class="bi bi-image display-4 text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            @if($relatedBlog->category)
                                                <span class="category-badge d-inline-block mb-2">{{ $relatedBlog->category }}</span>
                                            @endif
                                            <h6 class="card-title">
                                                <a href="{{ route('blogs.show', $relatedBlog) }}" class="text-decoration-none text-dark">
                                                    {{ Str::limit($relatedBlog->title, 50) }}
                                                </a>
                                            </h6>
                                            <div class="blog-meta">
                                                <i class="bi bi-calendar"></i> {{ $relatedBlog->published_at->format('M d, Y') }}
                                                <span class="ms-2"><i class="bi bi-clock"></i> {{ $relatedBlog->reading_time }} min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $blog->title }}');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $blog->title }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $blog->title }}');
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $blog->title }}');
    window.open(`https://wa.me/?text=${title}%20${url}`, '_blank');
}

function shareByEmail() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $blog->title }}');
    window.location.href = `mailto:?subject=${title}&body=Check out this article: ${url}`;
}

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Copy link functionality
function copyLink() {
    const dummy = document.createElement('input');
    document.body.appendChild(dummy);
    dummy.value = window.location.href;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    
    showToast('Link copied to clipboard!');
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 p-3';
    toast.style.zIndex = '11';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection
