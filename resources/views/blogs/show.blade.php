@extends('layouts.app')

@section('title', $blog->title . ' — ESA Blog')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --ink:       #0f1117;
        --ink-light: #3d4451;
        --muted:     #7a7f8e;
        --rule:      #e4e6eb;
        --surface:   #f7f8fa;
        --white:     #ffffff;
        --accent:    #1a56db;
        --accent-lt: #e8effe;
        --featured:  #b45309;
        --radius:    10px;
        --shadow-sm: 0 1px 4px rgba(0,0,0,.06);
        --shadow-md: 0 4px 20px rgba(0,0,0,.09);
        --ff-head:   'Playfair Display', Georgia, serif;
        --ff-body:   'DM Sans', sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
        font-family: var(--ff-body);
        color: var(--ink);
        background: var(--white);
        -webkit-font-smoothing: antialiased;
    }

    /* ── BACK BAR ─────────────────────────────────────── */
    .back-bar {
        background: var(--ink);
        padding: 12px 0;
        border-bottom: 3px solid var(--accent);
    }

    .back-bar a {
        color: rgba(255,255,255,.7);
        text-decoration: none;
        font-size: .8125rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color .18s;
    }

    .back-bar a:hover { color: var(--white); }

    .back-bar a i { font-size: .75rem; }

    .back-bar__divider {
        color: rgba(255,255,255,.25);
        margin: 0 8px;
    }

    .back-bar__current {
        color: rgba(255,255,255,.45);
        font-size: .8125rem;
        max-width: 320px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ── ARTICLE HEADER ──────────────────────────────── */
    .article-header {
        padding: 56px 0 40px;
        border-bottom: 1px solid var(--rule);
    }

    .article-header__inner {
        max-width: 740px;
        margin: 0 auto;
        text-align: center;
    }

    .article-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fef3c7;
        color: var(--featured);
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .article-title {
        font-family: var(--ff-head);
        font-size: clamp(1.9rem, 4vw, 2.85rem);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -.02em;
        color: var(--ink);
        margin: 0 0 24px;
    }

    .article-meta {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        font-size: .8125rem;
        color: var(--muted);
    }

    .article-meta__item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .article-meta__item i { font-size: .75rem; }

    .article-meta__sep {
        width: 3px;
        height: 3px;
        background: var(--rule);
        border-radius: 50%;
    }

    /* ── HERO IMAGE ──────────────────────────────────── */
    .hero-image-wrap {
        max-width: 880px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .hero-image-wrap img {
        width: 100%;
        max-height: 480px;
        object-fit: cover;
        border-radius: var(--radius);
        display: block;
        box-shadow: var(--shadow-md);
    }

    .hero-image-wrap figcaption {
        text-align: center;
        font-size: .8rem;
        color: var(--muted);
        margin-top: 10px;
        font-style: italic;
    }

    /* ── ARTICLE BODY ────────────────────────────────── */
    .article-layout {
        display: grid;
        grid-template-columns: 1fr min(680px, 100%) 1fr;
        gap: 0;
        padding: 52px 24px;
    }

    .article-body {
        grid-column: 2;
    }

    /* Content typography */
    .prose {
        font-size: 1.0625rem;
        line-height: 1.8;
        color: var(--ink-light);
    }

    .prose h1, .prose h2, .prose h3,
    .prose h4, .prose h5, .prose h6 {
        font-family: var(--ff-head);
        color: var(--ink);
        margin: 2.2rem 0 .9rem;
        line-height: 1.25;
        font-weight: 700;
    }

    .prose h2 { font-size: 1.65rem; }
    .prose h3 { font-size: 1.3rem; }
    .prose h4 { font-size: 1.1rem; }

    .prose p { margin: 0 0 1.5rem; }

    .prose a {
        color: var(--accent);
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .prose a:hover { text-decoration: none; }

    .prose img {
        max-width: 100%;
        height: auto;
        border-radius: var(--radius);
        margin: 2rem 0;
        display: block;
    }

    .prose blockquote {
        border-left: 3px solid var(--accent);
        margin: 2rem 0;
        padding: 1rem 1.5rem;
        background: var(--accent-lt);
        border-radius: 0 var(--radius) var(--radius) 0;
        font-style: italic;
        color: var(--ink-light);
    }

    .prose blockquote p { margin: 0; }

    .prose ul, .prose ol {
        padding-left: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .prose li { margin-bottom: .5rem; }

    .prose code {
        background: var(--surface);
        border: 1px solid var(--rule);
        padding: .15rem .4rem;
        border-radius: 4px;
        font-size: .875em;
        color: #be185d;
    }

    .prose pre {
        background: #1e1e2e;
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        overflow-x: auto;
        margin: 2rem 0;
    }

    .prose pre code {
        background: none;
        border: none;
        color: #cdd6f4;
        padding: 0;
        font-size: .875rem;
    }

    .prose hr {
        border: none;
        border-top: 1px solid var(--rule);
        margin: 2.5rem 0;
    }

    .prose table {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
        font-size: .9rem;
    }

    .prose th {
        background: var(--surface);
        font-weight: 600;
        text-align: left;
        padding: 10px 14px;
        border: 1px solid var(--rule);
        color: var(--ink);
    }

    .prose td {
        padding: 10px 14px;
        border: 1px solid var(--rule);
        color: var(--ink-light);
    }

    .prose tr:nth-child(even) td { background: var(--surface); }

    /* ── DIVIDER ─────────────────────────────────────── */
    .section-divider {
        max-width: 680px;
        margin: 0 auto 40px;
        border: none;
        border-top: 1px solid var(--rule);
    }

    /* ── GALLERY ─────────────────────────────────────── */
    .gallery-section {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 24px 52px;
    }

    .section-heading {
        font-family: var(--ff-head);
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-heading::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--rule);
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (max-width: 480px) { .gallery-grid { grid-template-columns: 1fr; } }

    .gallery-item {
        border-radius: var(--radius);
        overflow: hidden;
        border: 1px solid var(--rule);
        cursor: pointer;
        transition: box-shadow .2s, transform .2s;
    }

    .gallery-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }

    .gallery-item:hover img { transform: scale(1.04); }

    .gallery-caption {
        padding: 10px 12px;
        font-size: .8rem;
        color: var(--muted);
        border-top: 1px solid var(--rule);
        background: var(--white);
    }

    /* ── DOCUMENTS ───────────────────────────────────── */
    .documents-section {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 24px 52px;
    }

    .doc-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .doc-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 18px;
        border: 1px solid var(--rule);
        border-radius: var(--radius);
        background: var(--white);
        transition: box-shadow .2s, border-color .2s;
    }

    .doc-item:hover {
        box-shadow: var(--shadow-sm);
        border-color: var(--accent);
    }

    .doc-icon {
        width: 44px;
        height: 44px;
        background: var(--accent-lt);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--accent);
        font-size: 1.1rem;
    }

    .doc-info { flex: 1; min-width: 0; }

    .doc-name {
        font-weight: 600;
        font-size: .9rem;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .doc-size {
        font-size: .775rem;
        color: var(--muted);
    }

    .doc-download {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border: 1.5px solid var(--accent);
        border-radius: 6px;
        font-size: .8rem;
        font-weight: 600;
        color: var(--accent);
        text-decoration: none;
        transition: all .18s;
        flex-shrink: 0;
    }

    .doc-download:hover {
        background: var(--accent);
        color: var(--white);
        text-decoration: none;
    }

    /* ── SHARE SECTION ───────────────────────────────── */
    .share-section {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 24px 52px;
    }

    .share-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .share-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 7px;
        font-size: .8375rem;
        font-weight: 600;
        text-decoration: none;
        transition: opacity .18s, transform .18s;
        border: none;
        cursor: pointer;
    }

    .share-btn:hover { opacity: .88; transform: translateY(-1px); text-decoration: none; }

    .share-btn--whatsapp { background: #25d366; color: #fff; }
    .share-btn--facebook { background: #1877f2; color: #fff; }
    .share-btn--twitter  { background: #000;    color: #fff; }
    .share-btn--linkedin { background: #0a66c2; color: #fff; }
    .share-btn--email    { background: var(--surface); color: var(--ink-light); border: 1.5px solid var(--rule); }
    .share-btn--copy     { background: var(--surface); color: var(--ink-light); border: 1.5px solid var(--rule); }
    .share-btn--copied   { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }

    /* ── POST NAVIGATION ─────────────────────────────── */
    .post-nav {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 24px 72px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .post-nav__btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: 1.5px solid var(--rule);
        border-radius: 7px;
        font-size: .875rem;
        font-weight: 600;
        color: var(--ink-light);
        text-decoration: none;
        transition: all .18s;
        background: var(--white);
    }

    .post-nav__btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        text-decoration: none;
    }

    .post-nav__btn--primary {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--white);
    }

    .post-nav__btn--primary:hover {
        background: #1648c0;
        border-color: #1648c0;
        color: var(--white);
    }

    /* ── IMAGE MODAL ─────────────────────────────────── */
    .img-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.88);
        z-index: 9000;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .img-modal-overlay.open { display: flex; }

    .img-modal-box {
        position: relative;
        max-width: 900px;
        width: 100%;
        animation: modalIn .25s ease;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(.96); }
        to   { opacity: 1; transform: scale(1); }
    }

    .img-modal-box img {
        width: 100%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: var(--radius);
        display: block;
    }

    .img-modal-caption {
        text-align: center;
        color: rgba(255,255,255,.6);
        font-size: .85rem;
        margin-top: 12px;
    }

    .img-modal-close {
        position: absolute;
        top: -14px;
        right: -14px;
        width: 36px;
        height: 36px;
        background: var(--white);
        border: none;
        border-radius: 50%;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ink);
        transition: background .18s;
        box-shadow: 0 2px 8px rgba(0,0,0,.3);
    }

    .img-modal-close:hover { background: var(--surface); }

    /* ── RESPONSIVE ──────────────────────────────────── */
    @media (max-width: 768px) {
        .article-layout { padding: 36px 16px; }
        .gallery-section,
        .documents-section,
        .share-section,
        .post-nav { padding-left: 16px; padding-right: 16px; }
        .post-nav { flex-wrap: wrap; }
        .post-nav__btn { flex: 1; justify-content: center; }
    }

    @media (max-width: 480px) {
        .share-btn { padding: 8px 14px; font-size: .8rem; }
        .article-meta { gap: 12px; }
    }
</style>
@endpush

@section('content')

{{-- BACK BAR --}}
<div class="back-bar">
    <div class="container">
        <div class="d-flex align-items-center">
            <a href="{{ route('blogs.public.index') }}">
                <i class="fas fa-arrow-left"></i> Blog
            </a>
            <span class="back-bar__divider">/</span>
            <span class="back-bar__current">{{ $blog->title }}</span>
        </div>
    </div>
</div>

{{-- ARTICLE HEADER --}}
<header class="article-header">
    <div class="container">
        <div class="article-header__inner">
            @if($blog->is_featured)
                <div class="article-badge">
                    <i class="fas fa-star"></i> Featured Post
                </div>
            @endif

            <h1 class="article-title">{{ $blog->title }}</h1>

            <div class="article-meta">
                <div class="article-meta__item">
                    <i class="fas fa-calendar-alt"></i>
                    {{-- ✅ Fixed: uses created_at (no published_at column) --}}
                    {{ $blog->created_at->format('F d, Y') }}
                </div>
                <span class="article-meta__sep"></span>
                <div class="article-meta__item">
                    <i class="fas fa-eye"></i>
                    {{-- ✅ Fixed: column is 'views' not 'view_count' --}}
                    {{ number_format($blog->views) }} views
                </div>
                @if($blog->images->count() > 0)
                    <span class="article-meta__sep"></span>
                    <div class="article-meta__item">
                        <i class="fas fa-images"></i>
                        {{ $blog->images->count() }} image{{ $blog->images->count() > 1 ? 's' : '' }}
                    </div>
                @endif
                @if($blog->documents->count() > 0)
                    <span class="article-meta__sep"></span>
                    <div class="article-meta__item">
                        <i class="fas fa-paperclip"></i>
                        {{ $blog->documents->count() }} file{{ $blog->documents->count() > 1 ? 's' : '' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- HERO IMAGE --}}
@if($blog->images->count() > 0)
    @php $heroImage = $blog->images->first(); @endphp
    <div class="container py-5">
        <figure class="hero-image-wrap">
            {{-- Image stored in database - using data URI --}}
            <img src="{{ $heroImage->url }}"
                 alt="{{ $heroImage->alt_text ?? $blog->title }}">
            @if($heroImage->caption)
                <figcaption>{{ $heroImage->caption }}</figcaption>
            @endif
        </figure>
    </div>
@endif

{{-- ARTICLE BODY --}}
<div class="article-layout">
    <div class="article-body">
        <div class="prose">
            {!! $blog->content !!}
        </div>
    </div>
</div>

{{-- GALLERY (images after first) --}}
@if($blog->images->count() > 1)
    <hr class="section-divider">
    <section class="gallery-section">
        <h2 class="section-heading">Gallery</h2>
        <div class="gallery-grid">
            @foreach($blog->images->skip(1) as $image)
                <div class="gallery-item"
                     onclick="openModal('{{ $image->url }}', '{{ addslashes($image->caption ?? '') }}', '{{ addslashes($image->alt_text ?? $blog->title) }}')">
                    <img src="{{ $image->url }}"
                         alt="{{ $image->alt_text ?? $blog->title }}"
                         loading="lazy">
                    @if($image->caption)
                        <div class="gallery-caption">{{ $image->caption }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- DOCUMENTS --}}
@if($blog->documents->count() > 0)
    <hr class="section-divider">
    <section class="documents-section">
        <h2 class="section-heading">Attachments</h2>
        <div class="doc-list">
            @foreach($blog->documents as $document)
                <div class="doc-item">
                    <div class="doc-icon">
                        @if(str_contains($document->mime_type, 'pdf'))
                            <i class="fas fa-file-pdf"></i>
                        @elseif(str_contains($document->mime_type, 'word') || str_contains($document->mime_type, 'docx'))
                            <i class="fas fa-file-word"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                        @endif
                    </div>
                    <div class="doc-info">
                        <div class="doc-name">{{ $document->document_name }}</div>
                        {{-- ✅ Fixed: uses file_size column directly --}}
                        <div class="doc-size">{{ number_format($document->file_size / 1024, 1) }} KB</div>
                    </div>
                    <a href="{{ asset('storage/' . $document->document_path) }}"
                       target="_blank"
                       download="{{ $document->document_name }}"
                       class="doc-download">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- SHARE --}}
<hr class="section-divider">
<section class="share-section">
    <h2 class="section-heading">Share</h2>
    <div class="share-grid">
        <a href="https://wa.me/?text={{ urlencode($blog->title . ' — ' . route('blogs.show', $blog->slug)) }}"
           target="_blank" rel="noopener"
           class="share-btn share-btn--whatsapp">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blogs.show', $blog->slug)) }}"
           target="_blank" rel="noopener"
           class="share-btn share-btn--facebook">
            <i class="fab fa-facebook-f"></i> Facebook
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(route('blogs.show', $blog->slug)) }}"
           target="_blank" rel="noopener"
           class="share-btn share-btn--twitter">
            <i class="fab fa-x-twitter"></i> X / Twitter
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blogs.show', $blog->slug)) }}"
           target="_blank" rel="noopener"
           class="share-btn share-btn--linkedin">
            <i class="fab fa-linkedin-in"></i> LinkedIn
        </a>
        <a href="mailto:?subject={{ urlencode($blog->title) }}&body={{ urlencode(route('blogs.show', $blog->slug)) }}"
           class="share-btn share-btn--email">
            <i class="fas fa-envelope"></i> Email
        </a>
        <button type="button" class="share-btn share-btn--copy" id="copyBtn" onclick="copyLink(this)">
            <i class="fas fa-link"></i> Copy Link
        </button>
    </div>
</section>

{{-- POST NAVIGATION --}}
<hr class="section-divider">
<nav class="post-nav" aria-label="Post navigation">
    <a href="{{ route('blogs.public.index') }}" class="post-nav__btn">
        <i class="fas fa-arrow-left"></i> All Posts
    </a>
    <a href="{{ route('blogs.public.index') }}" class="post-nav__btn post-nav__btn--primary">
        <i class="fas fa-th-large"></i> Browse Blog
    </a>
</nav>

{{-- IMAGE LIGHTBOX --}}
<div class="img-modal-overlay" id="imgModal" onclick="closeModal(event)">
    <div class="img-modal-box">
        <button class="img-modal-close" onclick="closeModalDirect()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImg" src="" alt="">
        <p class="img-modal-caption" id="modalCaption"></p>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Lightbox ──────────────────────────────────────────────
function openModal(src, caption, alt) {
    const modal = document.getElementById('imgModal');
    document.getElementById('modalImg').src = src;
    document.getElementById('modalImg').alt = alt;
    document.getElementById('modalCaption').textContent = caption;
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(e) {
    if (e.target === document.getElementById('imgModal')) closeModalDirect();
}

function closeModalDirect() {
    document.getElementById('imgModal').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModalDirect();
});

// ── Copy link ─────────────────────────────────────────────
function copyLink(btn) {
    navigator.clipboard.writeText(window.location.href).then(function() {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.remove('share-btn--copy');
        btn.classList.add('share-btn--copied');
        setTimeout(function() {
            btn.innerHTML = original;
            btn.classList.remove('share-btn--copied');
            btn.classList.add('share-btn--copy');
        }, 2200);
    }).catch(function() {
        alert('Please copy the URL from your address bar.');
    });
}

// ── Increment view count ──────────────────────────────────
fetch('/blogs/{{ $blog->slug }}/view', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
}).catch(function() {});
</script>
@endpush