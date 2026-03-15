@extends('layouts.app')

@section('title', $blog->title)

@section('content')
<div class="container">
    <!-- Blog Header -->
    <article class="py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Blog Title and Meta -->
                <header class="text-center mb-5">
                    @if($blog->featured)
                        <span class="badge bg-warning text-dark mb-3">
                            <i class="fas fa-star"></i> Featured Post
                        </span>
                    @endif
                    <h1 class="display-5 fw-bold mb-3">{{ $blog->title }}</h1>
                    <div class="d-flex justify-content-center align-items-center text-muted mb-4">
                        <span class="me-3">
                            <i class="fas fa-calendar"></i> 
                            {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-clock"></i> {{ $blog->reading_time }} min read
                        </span>
                        <span>
                            <i class="fas fa-eye"></i> {{ $blog->view_count }} views
                        </span>
                    </div>
                </header>

                <!-- Featured Image -->
                @if($blog->images->count() > 0)
                    <div class="mb-5">
                        @foreach($blog->images as $image)
                            @if($loop->first)
                                <img src="{{ $image->url }}" 
                                     class="img-fluid rounded shadow" 
                                     alt="{{ $image->alt_text ?? $blog->title }}"
                                     style="width: 100%; max-height: 500px; object-fit: cover;">
                                @if($image->caption)
                                    <p class="text-center text-muted mt-2">
                                        <small>{{ $image->caption }}</small>
                                    </p>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Blog Content -->
                <div class="blog-content mb-5">
                    <div class="content-wrapper">
                        {!! $blog->content !!}
                    </div>
                </div>

                <!-- Image Gallery -->
                @if($blog->images->count() > 1)
                    <section class="mb-5">
                        <h3 class="mb-4">Gallery</h3>
                        <div class="row">
                            @foreach($blog->images as $image)
                                @if(!$loop->first)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <img src="{{ $image->url }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $image->alt_text ?? $blog->title }}"
                                                 style="height: 200px; object-fit: cover;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal" 
                                                 data-image="{{ $image->url }}"
                                                 data-caption="{{ $image->caption ?? '' }}"
                                                 data-alt="{{ $image->alt_text ?? $blog->title }}">
                                            @if($image->caption)
                                                <div class="card-body p-2">
                                                    <small class="text-muted">{{ $image->caption }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Documents -->
                @if($blog->documents->count() > 0)
                    <section class="mb-5">
                        <h3 class="mb-4">Documents</h3>
                        <div class="row">
                            @foreach($blog->documents as $document)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $document->document_name }}</h6>
                                                    <small class="text-muted">{{ $document->human_file_size }}</small>
                                                </div>
                                                <a href="{{ $document->url }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Share Section -->
                <section class="mb-5">
                    <h3 class="mb-4">Share this post</h3>
                    <div class="d-flex gap-2">
                        <!-- WhatsApp Share -->
                        <a href="https://wa.me/?text={{ urlencode('Check out this blog post: ' . $blog->title . ' ' . route('blogs.show', $blog)) }}" 
                           target="_blank" 
                           class="btn btn-success">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        
                        <!-- Facebook Share -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blogs.show', $blog)) }}" 
                           target="_blank" 
                           class="btn btn-primary">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        
                        <!-- Twitter Share -->
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(route('blogs.show', $blog)) }}" 
                           target="_blank" 
                           class="btn btn-info">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        
                        <!-- LinkedIn Share -->
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blogs.show', $blog)) }}" 
                           target="_blank" 
                           class="btn btn-secondary">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </a>
                        
                        <!-- Email Share -->
                        <a href="mailto:?subject={{ urlencode($blog->title) }}&body={{ urlencode('Check out this blog post: ' . route('blogs.show', $blog)) }}" 
                           class="btn btn-outline-dark">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                        
                        <!-- Copy Link -->
                        <button type="button" 
                                class="btn btn-outline-secondary" 
                                onclick="copyLink()">
                            <i class="fas fa-link"></i> Copy Link
                        </button>
                    </div>
                </section>

                <!-- Navigation -->
                <nav class="mt-5">
                    <div class="row">
                        <div class="col-6">
                            @if($blog->id > 1)
                                <a href="{{ route('blogs.show', $blog->id - 1) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Previous Post
                                </a>
                            @endif
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{ route('blogs.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> All Posts
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </article>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $blog->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="">
                @if(isset($image->caption))
                    <p id="modalCaption" class="mt-3 text-muted"></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.blog-content {
    font-size: 1.1rem;
    line-height: 1.8;
}

.content-wrapper h1,
.content-wrapper h2,
.content-wrapper h3,
.content-wrapper h4,
.content-wrapper h5,
.content-wrapper h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.content-wrapper p {
    margin-bottom: 1.5rem;
}

.content-wrapper img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.content-wrapper blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1.5rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
}

.content-wrapper ul,
.content-wrapper ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.content-wrapper li {
    margin-bottom: 0.5rem;
}

.content-wrapper code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.9rem;
}

.content-wrapper pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script>
// Image Modal
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    
    document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#imageModal"]').forEach(element => {
        element.addEventListener('click', function() {
            modalImage.src = this.getAttribute('data-image');
            modalImage.alt = this.getAttribute('data-alt');
            if (modalCaption) {
                modalCaption.textContent = this.getAttribute('data-caption');
            }
        });
    });
});

// Copy Link Function
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy link. Please copy manually.');
    });
}

// Increment view count
fetch(`/blogs/{{ $blog->slug }}/view`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
}).catch(function(error) {
    console.log('View count update failed:', error);
});
</script>
@endpush
