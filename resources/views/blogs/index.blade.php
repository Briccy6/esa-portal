@extends('layouts.app')

@section('title', 'Blog - Excellent Servant Academy')

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
        --accent:    #1a56db;        /* single clean blue accent — no gradient */
        --accent-lt: #e8effe;
        --featured:  #b45309;        /* amber for featured badge */
        --radius:    12px;
        --shadow-sm: 0 1px 4px rgba(0,0,0,.06);
        --shadow-md: 0 4px 20px rgba(0,0,0,.09);
        --shadow-lg: 0 12px 40px rgba(0,0,0,.13);
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

    /* ─── PAGE HEADER ─────────────────────────────────── */
    .blog-header {
        background: var(--ink);
        color: var(--white);
        padding: 72px 0 56px;
        border-bottom: 4px solid var(--accent);
    }

    .blog-header__kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 16px;
    }

    .blog-header__kicker::before {
        content: '';
        display: block;
        width: 24px;
        height: 2px;
        background: var(--accent);
    }

    .blog-header__title {
        font-family: var(--ff-head);
        font-size: clamp(2.4rem, 5vw, 3.8rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -.02em;
        margin: 0 0 16px;
        color: var(--white);
    }

    .blog-header__desc {
        font-size: 1.0625rem;
        color: rgba(255,255,255,.65);
        max-width: 520px;
        line-height: 1.65;
        margin: 0;
    }

    .blog-header__stats {
        display: flex;
        gap: 32px;
        margin-top: 32px;
    }

    .blog-header__stat {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .blog-header__stat-num {
        font-family: var(--ff-head);
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--white);
        line-height: 1;
    }

    .blog-header__stat-label {
        font-size: .75rem;
        color: rgba(255,255,255,.5);
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    /* ─── SEARCH / FILTER BAR ─────────────────────────── */
    .filter-bar {
        background: var(--white);
        border-bottom: 1px solid var(--rule);
        padding: 20px 0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: var(--shadow-sm);
    }

    .filter-bar__inner {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .search-wrap {
        position: relative;
        flex: 1;
        min-width: 220px;
        max-width: 380px;
    }

    .search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: .875rem;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        padding: 10px 16px 10px 38px;
        border: 1.5px solid var(--rule);
        border-radius: 8px;
        font-family: var(--ff-body);
        font-size: .9rem;
        color: var(--ink);
        background: var(--surface);
        transition: border-color .2s, box-shadow .2s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--accent);
        background: var(--white);
        box-shadow: 0 0 0 3px var(--accent-lt);
    }

    .filter-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-pill {
        padding: 7px 16px;
        border-radius: 6px;
        font-size: .8125rem;
        font-weight: 500;
        cursor: pointer;
        border: 1.5px solid var(--rule);
        background: var(--white);
        color: var(--ink-light);
        transition: all .18s ease;
        user-select: none;
    }

    .filter-pill:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-lt);
    }

    .filter-pill.active {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--white);
    }

    /* ─── SECTION LABEL ───────────────────────────────── */
    .section-label {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
    }

    .section-label__text {
        font-family: var(--ff-head);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--ink);
        white-space: nowrap;
    }

    .section-label__rule {
        flex: 1;
        height: 1px;
        background: var(--rule);
    }

    .section-label__count {
        font-size: .75rem;
        color: var(--muted);
        font-weight: 500;
        background: var(--surface);
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid var(--rule);
    }

    /* ─── FEATURED STRIP (top card, wider) ───────────── */
    .featured-section {
        padding: 52px 0 40px;
    }

    .featured-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    @media (max-width: 992px) { .featured-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 640px)  { .featured-grid { grid-template-columns: 1fr; } }

    /* ─── BLOG CARD ───────────────────────────────────── */
    .blog-card {
        background: var(--white);
        border: 1px solid var(--rule);
        border-radius: var(--radius);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: box-shadow .25s ease, transform .25s ease, border-color .25s ease;
        opacity: 0;
        transform: translateY(20px);
        animation: cardIn .5s ease forwards;
    }

    .blog-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-5px);
        border-color: transparent;
    }

    @keyframes cardIn {
        to { opacity: 1; transform: translateY(0); }
    }

    .blog-card:nth-child(1) { animation-delay: .05s; }
    .blog-card:nth-child(2) { animation-delay: .12s; }
    .blog-card:nth-child(3) { animation-delay: .19s; }
    .blog-card:nth-child(4) { animation-delay: .26s; }
    .blog-card:nth-child(5) { animation-delay: .33s; }
    .blog-card:nth-child(6) { animation-delay: .40s; }
    .blog-card:nth-child(7) { animation-delay: .47s; }
    .blog-card:nth-child(8) { animation-delay: .54s; }
    .blog-card:nth-child(9) { animation-delay: .61s; }

    /* Card image */
    .card-img-wrap {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: var(--surface);
        flex-shrink: 0;
    }

    .card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .5s ease;
        display: block;
    }

    .blog-card:hover .card-img-wrap img {
        transform: scale(1.05);
    }

    .card-img-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--surface);
    }

    .card-img-placeholder i {
        font-size: 2rem;
        color: var(--rule);
    }

    /* Featured badge */
    .card-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--featured);
        color: var(--white);
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 4px;
    }

    /* Status badge */
    .card-status {
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: .7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .card-status--published { background: #d1fae5; color: #065f46; }
    .card-status--draft     { background: #fef3c7; color: #92400e; }
    .card-status--archived  { background: #e5e7eb; color: #374151; }

    /* Card body */
    .card-body {
        padding: 22px 22px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .card-meta {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: .78rem;
        color: var(--muted);
    }

    .card-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .card-title {
        font-family: var(--ff-head);
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.35;
        color: var(--ink);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
        transition: color .18s;
    }

    .blog-card:hover .card-title {
        color: var(--accent);
    }

    .card-excerpt {
        font-size: .875rem;
        color: var(--ink-light);
        line-height: 1.6;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }

    /* Card footer */
    .card-footer {
        padding: 14px 22px;
        border-top: 1px solid var(--rule);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--white);
    }

    .card-date {
        font-size: .78rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .read-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .8125rem;
        font-weight: 600;
        color: var(--accent);
        text-decoration: none;
        padding: 7px 14px;
        border: 1.5px solid var(--accent);
        border-radius: 6px;
        transition: all .18s ease;
    }

    .read-btn:hover {
        background: var(--accent);
        color: var(--white);
        text-decoration: none;
    }

    .read-btn i {
        font-size: .7rem;
        transition: transform .18s;
    }

    .read-btn:hover i {
        transform: translateX(3px);
    }

    /* ─── LATEST POSTS GRID ───────────────────────────── */
    .latest-section {
        padding: 52px 0 64px;
        background: var(--surface);
    }

    .posts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    @media (max-width: 992px) { .posts-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 600px)  { .posts-grid { grid-template-columns: 1fr; } }

    /* ─── EMPTY STATE ─────────────────────────────────── */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 72px 24px;
        background: var(--white);
        border: 1px solid var(--rule);
        border-radius: var(--radius);
    }

    .empty-state__icon {
        width: 72px;
        height: 72px;
        background: var(--surface);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .empty-state__icon i {
        font-size: 1.75rem;
        color: var(--muted);
    }

    .empty-state h3 {
        font-family: var(--ff-head);
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--muted);
        font-size: .9375rem;
        margin: 0;
    }

    /* ─── PAGINATION ──────────────────────────────────── */
    .pagination-wrap {
        margin-top: 48px;
        display: flex;
        justify-content: center;
    }

    .pagination .page-item .page-link {
        border: 1.5px solid var(--rule);
        color: var(--ink-light);
        border-radius: 6px !important;
        margin: 0 3px;
        font-size: .875rem;
        font-weight: 500;
        transition: all .15s;
    }

    .pagination .page-item.active .page-link,
    .pagination .page-item .page-link:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--white);
    }

    /* ─── NO-RESULTS MESSAGE ──────────────────────────── */
    .no-results {
        display: none;
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px;
        color: var(--muted);
        font-size: .9375rem;
    }

    /* ─── RESPONSIVE TWEAKS ───────────────────────────── */
    @media (max-width: 768px) {
        .blog-header { padding: 48px 0 40px; }
        .blog-header__stats { gap: 20px; }
        .filter-bar__inner { gap: 12px; }
        .search-wrap { max-width: 100%; }
        .card-img-wrap { height: 180px; }
    }

    @media (max-width: 480px) {
        .card-img-wrap { height: 160px; }
        .card-body { padding: 16px; gap: 8px; }
        .card-footer { padding: 12px 16px; }
    }
</style>
@endpush

@section('content')

{{-- PAGE HEADER --}}
<header class="blog-header">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-lg-7">
                <span class="blog-header__kicker">
                    <i class="fas fa-pen-nib"></i> ESA Blog
                </span>
                <h1 class="blog-header__title">Stories, Insights &amp;<br>Academy News</h1>
                <p class="blog-header__desc">Discover educational articles, campus stories, and thought-provoking insights from the Excellent Servant Academy community.</p>
                <div class="blog-header__stats">
                    <div class="blog-header__stat">
                        <span class="blog-header__stat-num">{{ $blogs->total() }}</span>
                        <span class="blog-header__stat-label">Articles</span>
                    </div>
                    <div class="blog-header__stat">
                        <span class="blog-header__stat-num">{{ number_format($blogs->sum('views')) }}</span>
                        <span class="blog-header__stat-label">Total Views</span>
                    </div>
                    @if($featuredBlogs->count())
                    <div class="blog-header__stat">
                        <span class="blog-header__stat-num">{{ $featuredBlogs->count() }}</span>
                        <span class="blog-header__stat-label">Featured</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

{{-- STICKY FILTER BAR --}}
<div class="filter-bar">
    <div class="container">
        <div class="filter-bar__inner">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" id="blogSearch" placeholder="Search articles…" autocomplete="off">
            </div>
            <div class="filter-pills" id="filterPills">
                <div class="filter-pill active" data-filter="all">All</div>
                <div class="filter-pill" data-filter="published">Published</div>
                <div class="filter-pill" data-filter="featured">Featured</div>
            </div>
        </div>
    </div>
</div>

{{-- FEATURED POSTS --}}
@if($featuredBlogs->count() > 0)
<section class="featured-section">
    <div class="container">
        <div class="section-label">
            <span class="section-label__text">Featured</span>
            <span class="section-label__rule"></span>
            <span class="section-label__count">{{ $featuredBlogs->count() }} post{{ $featuredBlogs->count() > 1 ? 's' : '' }}</span>
        </div>

        <div class="featured-grid" id="featuredGrid">
            @foreach($featuredBlogs as $blog)
            <article class="blog-card" data-title="{{ strtolower($blog->title) }}" data-status="published" data-featured="1">
                <div class="card-img-wrap">
                    @if($blog->images && $blog->images->count() > 0)
                        <img src="{{ asset('storage/' . $blog->images->first()->image_path) }}"
                             alt="{{ $blog->images->first()->alt_text ?? $blog->title }}"
                             loading="lazy">
                    @else
                        <div class="card-img-placeholder">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    @endif
                    <span class="card-badge"><i class="fas fa-star me-1"></i>Featured</span>
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span><i class="fas fa-eye"></i> {{ number_format($blog->views) }}</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                    </div>
                    <h3 class="card-title">{{ $blog->title }}</h3>
                    <p class="card-excerpt">{{ Str::limit(strip_tags($blog->content), 140) }}</p>
                </div>
                <div class="card-footer">
                    <span class="card-date">
                        <i class="fas fa-clock"></i>
                        {{ $blog->created_at->diffForHumans() }}
                    </span>
                    <a href="{{ route('blogs.show', $blog->slug) }}" class="read-btn">
                        Read <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- LATEST POSTS --}}
<section class="latest-section">
    <div class="container">
        <div class="section-label">
            <span class="section-label__text">Latest Posts</span>
            <span class="section-label__rule"></span>
            <span class="section-label__count">{{ $blogs->total() }} total</span>
        </div>

        <div class="posts-grid" id="postsGrid">
            @forelse($blogs as $blog)
            <article class="blog-card"
                     data-title="{{ strtolower($blog->title) }}"
                     data-excerpt="{{ strtolower(strip_tags($blog->content)) }}"
                     data-status="{{ $blog->status }}"
                     data-featured="{{ $blog->is_featured ? '1' : '0' }}">
                <div class="card-img-wrap">
                    @if($blog->images && $blog->images->count() > 0)
                        <img src="{{ asset('storage/' . $blog->images->first()->image_path) }}"
                             alt="{{ $blog->images->first()->alt_text ?? $blog->title }}"
                             loading="lazy">
                    @else
                        <div class="card-img-placeholder">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    @endif
                    @if($blog->is_featured)
                        <span class="card-badge"><i class="fas fa-star me-1"></i>Featured</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span><i class="fas fa-eye"></i> {{ number_format($blog->views) }}</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                    </div>
                    <h3 class="card-title">{{ $blog->title }}</h3>
                    <p class="card-excerpt">{{ Str::limit(strip_tags($blog->content), 140) }}</p>
                </div>
                <div class="card-footer">
                    <span class="card-date">
                        <i class="fas fa-clock"></i>
                        {{ $blog->created_at->diffForHumans() }}
                    </span>
                    <a href="{{ route('blogs.show', $blog->slug) }}" class="read-btn">
                        Read <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            @empty
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3>No posts published yet</h3>
                <p>Check back soon — new content is on its way.</p>
            </div>
            @endforelse

            <div class="no-results" id="noResults">
                <i class="fas fa-search me-2"></i> No articles match your search.
            </div>
        </div>

        {{-- Pagination --}}
        @if($blogs->hasPages())
        <div class="pagination-wrap">
            {{ $blogs->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    const searchInput  = document.getElementById('blogSearch');
    const pills        = document.querySelectorAll('.filter-pill');
    const postsGrid    = document.getElementById('postsGrid');
    const noResults    = document.getElementById('noResults');

    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        const cards = postsGrid.querySelectorAll('.blog-card');
        let visible = 0;

        cards.forEach(card => {
            const title    = card.dataset.title    || '';
            const excerpt  = card.dataset.excerpt  || '';
            const status   = card.dataset.status   || '';
            const featured = card.dataset.featured || '0';

            const matchesSearch = !currentSearch ||
                title.includes(currentSearch) ||
                excerpt.includes(currentSearch);

            const matchesFilter =
                currentFilter === 'all'      ||
                (currentFilter === 'published' && status === 'published') ||
                (currentFilter === 'featured'  && featured === '1');

            if (matchesSearch && matchesFilter) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    // Search
    searchInput.addEventListener('input', function () {
        currentSearch = this.value.trim().toLowerCase();
        applyFilters();
    });

    // Filter pills
    pills.forEach(pill => {
        pill.addEventListener('click', function () {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            applyFilters();
        });
    });

    // Sticky bar shadow on scroll
    const filterBar = document.querySelector('.filter-bar');
    window.addEventListener('scroll', () => {
        filterBar.style.boxShadow = window.scrollY > 10
            ? '0 4px 20px rgba(0,0,0,.10)'
            : '0 1px 4px rgba(0,0,0,.06)';
    }, { passive: true });
})();
</script>
@endpush