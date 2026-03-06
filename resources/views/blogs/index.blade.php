@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - {{$systemName}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="google-site-verification" content="ysodRmOEgSFFmQNcCcXAbjP2xSW5XbpJNtWrJ9ffgxs" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom Styles for Academic & Professional Look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary-color: #2c3e50; /* Darker Slate Blue - Academic feel */
            --secondary-color: #3498db; /* Professional Blue */
            --accent-color: #e74c3c; /* Red for highlights */
            --text-dark: #263238; /* Very dark grey-blue */
            --text-light: #455a64; /* Medium grey-blue */
            --bg-light: #ecf0f1; /* Light Gray for sections */
            --bg-white: #ffffff;
            --border-color: #cfd8dc; /* Light Blue Grey */
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #2980b9; /* Darker secondary */
            transform: translateY(-2px);
        }

        .text-accent-color {
            color: var(--secondary-color);
        }

        .section-heading {
            color: var(--text-dark);
        }

        .text-text-light {
            color: var(--text-light);
        }

        .bg-bg-light {
            background-color: var(--bg-light);
        }

        .border-border-color {
            border-color: var(--border-color);
        }

        .card-shadow-hover {
            transition: all 0.3s ease;
        }

        .card-shadow-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* Blog specific styles */
        .blog-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .blog-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        .blog-card .card-body {
            padding: 1.5rem;
        }
        
        .blog-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            text-decoration: none;
            line-height: 1.4;
        }
        
        .blog-title:hover {
            color: var(--secondary-color);
        }
        
        .blog-excerpt {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .blog-meta {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .blog-meta i {
            margin-right: 0.25rem;
        }
        
        .featured-blog {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            height: 400px;
        }
        
        .featured-blog .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);
            color: white;
            padding: 2rem;
        }
        
        .featured-blog .featured-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .featured-blog .featured-excerpt {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .search-box {
            border-radius: 25px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1.5rem;
        }
        
        .search-box:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .filter-btn {
            border-radius: 25px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            background: white;
            color: var(--text-light);
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover, .filter-btn.active {
            border-color: var(--secondary-color);
            background: var(--secondary-color);
            color: white;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .share-buttons {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 10;
        }
        
        .share-btn {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .share-btn:hover {
            background: white;
            transform: scale(1.1);
        }

        /* Navigation styles matching welcome page */
        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--bg-light);
            color: var(--secondary-color);
        }

        .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .nav-text {
            margin-left: 0.5rem;
        }

        .nav-indicator {
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background-color: var(--secondary-color);
            transition: width 0.3s ease;
        }

        .nav-item:hover .nav-indicator {
            width: 80%;
        }

        /* Hero section matching welcome page style */
        .hero-blog {
            height: 500px; /* Same as welcome page hero-carousel */
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .carousel-text h2 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .carousel-text p {
            font-size: 1.5rem;
            max-width: 800px;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Navigation (same as welcome page) -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    @if($systemLogo)
                        <img src="{{ asset('storage/' . $systemLogo) }}" alt="Logo" class="h-10 mr-3">
                    @endif
                    <h1 class="text-2xl font-bold section-heading">{{$systemName}}</h1>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ url('/') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Home</a>
                    <a href="{{ url('/#announcements') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Announcements</a>
                    <a href="{{ route('blogs.index') }}" class="text-accent-color font-medium transition duration-300">Blog</a>
                    <a href="{{ url('/#quick-access') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Portals</a>
                    <a href="{{ url('/#about') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">About</a>
                    <a href="{{ url('/#contact') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Contact</a>
                </nav>
                
                <!-- Login Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300 shadow-sm">
                            <span class="text-gray-700 font-medium">Login</span>
                            <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-border-color py-2 z-10">
                            <a href="{{ route('admin.login.form') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                <i class="fas fa-user-shield mr-2 text-red-600"></i>Admin Login
                            </a>
                            <a href="{{ route('staff.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                <i class="fas fa-users mr-2 text-blue-600"></i>Staff Login
                            </a>
                            <a href="{{ route('teacher.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                <i class="fas fa-chalkboard-teacher mr-2 text-green-600"></i>Teacher Login
                            </a>
                            <a href="{{ route('student.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                <i class="fas fa-user-graduate mr-2 text-purple-600"></i>Student Login
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('students.create') }}" class="btn-primary px-6 py-2 rounded-lg font-medium shadow-md">
                        Register
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-gray-700" x-data="{ open: false }" @click="open = !open">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="md:hidden bg-white shadow-lg py-4" x-data="{ open: false }" x-show="open" @click.away="open = false" x-transition.origin.top>
        <nav class="flex flex-col items-center space-y-4">
            <a href="{{ url('/') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Home</a>
            <a href="{{ url('/#announcements') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Announcements</a>
            <a href="{{ route('blogs.index') }}" class="text-accent-color font-medium transition duration-300" @click="open = false">Blog</a>
            <a href="{{ url('/#quick-access') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Portals</a>
            <a href="{{ url('/#about') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">About</a>
            <a href="{{ url('/#contact') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Contact</a>
            <div class="w-full border-t border-gray-200 pt-4 mt-4"></div>
            <div class="relative w-full text-center" x-data="{ loginOpen: false }">
                <button @click="loginOpen = !loginOpen" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300">
                    <span class="text-gray-700 font-medium">Login</span>
                    <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                </button>
                <div x-show="loginOpen" @click.away="loginOpen = false" x-transition class="mt-2 w-full bg-white rounded-lg shadow-lg border border-border-color py-2">
                    <a href="{{ route('admin.login.form') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                        <i class="fas fa-user-shield mr-2 text-red-600"></i>Admin Login
                    </a>
                    <a href="{{ route('staff.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                        <i class="fas fa-users mr-2 text-blue-600"></i>Staff Login
                    </a>
                    <a href="{{ route('teacher.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                        <i class="fas fa-chalkboard-teacher mr-2 text-green-600"></i>Teacher Login
                    </a>
                    <a href="{{ route('student.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                        <i class="fas fa-user-graduate mr-2 text-purple-600"></i>Student Login
                    </a>
                </div>
            </div>
            <a href="{{ route('students.create') }}" class="btn-primary w-full px-6 py-2 rounded-lg font-medium shadow-md">
                Register
            </a>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="hero-blog">
        <div class="carousel-overlay">
            <div class="carousel-text">
                <h2 class="text-white">Our <span class="text-yellow-300">Blog</span></h2>
                <p class="text-gray-200">Discover the latest insights, tutorials, and news from our expert team. Stay informed with our comprehensive articles on education, technology, and more.</p>
                <div class="flex justify-center gap-8 mb-4">
                    <div class="text-center">
                        <h3 class="text-3xl font-bold text-yellow-300">{{ $blogs->total() }}</h3>
                        <p class="text-gray-300">Total Articles</p>
                    </div>
                    <div class="text-center">
                        <h3 class="text-3xl font-bold text-yellow-300">{{ $categories->count() }}</h3>
                        <p class="text-gray-300">Categories</p>
                    </div>
                </div>
                <div class="flex justify-center">
                    <a href="#blog-content" class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105">
                        Explore Articles <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="blog-content" class="py-16">
        <div class="container mx-auto px-6">
            <div class="max-w-6xl mx-auto">
                
                <!-- Featured Blogs -->
                @if($featuredBlogs->count() > 0)
                <section class="mb-16">
                    <div class="text-center mb-10">
                        <h2 class="text-4xl font-extrabold section-heading mb-4">
                            Featured <span class="text-accent-color">Articles</span>
                        </h2>
                        <p class="text-lg text-text-light">Handpicked stories from our expert contributors</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($featuredBlogs as $blog)
                            <div class="featured-blog card-shadow-hover">
                                @if($blog->featured_image)
                                    <img src="{{ asset('storage/' . $blog->featured_image) }}" class="w-full h-full object-cover" alt="{{ $blog->title }}">
                                @else
                                    <div class="w-full h-full bg-gray-200 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-gray-400 text-6xl"></i>
                                    </div>
                                @endif
                                <div class="featured-overlay">
                                    @if($blog->category)
                                        <span class="category-badge d-inline-block mb-2">{{ $blog->category }}</span>
                                    @endif
                                    <h3 class="featured-title">{{ $blog->title }}</h3>
                                    <p class="featured-excerpt">{{ Str::limit($blog->excerpt, 100) }}</p>
                                    <div class="blog-meta text-white-50">
                                        <i class="fas fa-calendar"></i> {{ $blog->published_at->format('M d, Y') }}
                                        <span class="ms-3"><i class="fas fa-clock"></i> {{ $blog->reading_time }} min read</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('blogs.show', $blog) }}" class="btn btn-light btn-sm">
                                            Read More <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Search and Filters -->
                <section class="mb-12">
                    <div class="bg-white rounded-2xl p-8 border border-border-color shadow-lg">
                        <div class="flex flex-col md:flex-row gap-4 items-center">
                            <div class="flex-1">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="search-box w-full pl-10" placeholder="Search articles..." 
                                           value="{{ request('search') }}" id="searchInput">
                                </div>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <button class="filter-btn {{ !request('category') ? 'active' : '' }}" onclick="filterByCategory('')">
                                    All
                                </button>
                                @foreach($categories as $category)
                                    <button class="filter-btn {{ request('category') == $category ? 'active' : '' }}" 
                                            onclick="filterByCategory('{{ $category }}')">
                                        {{ $category }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Blog Grid -->
                <section class="mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="blogGrid">
                        @foreach($blogs as $blog)
                            <div class="blog-card card-shadow-hover" data-category="{{ $blog->category ?? '' }}">
                                @if($blog->featured_image)
                                    <img src="{{ asset('storage/' . $blog->featured_image) }}" class="card-img-top" alt="{{ $blog->title }}">
                                @else
                                    <div class="card-img-top bg-gray-200 d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    @if($blog->category)
                                        <span class="category-badge d-inline-block mb-2">{{ $blog->category }}</span>
                                    @endif
                                    <h5 class="card-title mb-3">
                                        <a href="{{ route('blogs.show', $blog) }}" class="blog-title">{{ $blog->title }}</a>
                                    </h5>
                                    <p class="blog-excerpt mb-4">{{ $blog->excerpt }}</p>
                                    <div class="blog-meta mb-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-user"></i> {{ $blog->author->first_name ?? 'Admin' }}
                                            </span>
                                            <span>
                                                <i class="fas fa-eye"></i> {{ number_format($blog->views) }}
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            <i class="fas fa-calendar"></i> {{ $blog->published_at->format('M d, Y') }}
                                            <span class="ms-3"><i class="fas fa-clock"></i> {{ $blog->reading_time }} min read</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('blogs.show', $blog) }}" class="btn-primary">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- No Results -->
                    @if($blogs->count() == 0)
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-200">
                            <i class="fas fa-search text-gray-400 text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold section-heading mb-2">No articles found</h4>
                        <p class="text-text-light text-lg">Try adjusting your search or filter criteria.</p>
                    </div>
                    @endif

                    <!-- Pagination -->
                    @if($blogs->hasPages())
                        <div class="d-flex justify-content-center mt-8">
                            {{ $blogs->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>

    <script>
    function filterByCategory(category) {
        const url = new URL(window.location);
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }
        window.location.href = url.toString();
    }

    function shareBlog(url, title) {
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(err => console.log('Error sharing:', err));
        } else {
            // Fallback - copy to clipboard
            const dummy = document.createElement('input');
            document.body.appendChild(dummy);
            dummy.value = url;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            
            // Show toast notification
            showToast('Link copied to clipboard!');
        }
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

    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value;
        
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 2 || searchTerm.length === 0) {
                performSearch(searchTerm);
            }
        }, 500);
    });

    function performSearch(searchTerm) {
        const url = new URL(window.location);
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
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
    </script>
</body>
</html>
