@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'Excellent Servant Academy');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
    $systemLogo = \App\Helpers\LogoHelper::getSystemLogo();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $systemName }} - Best Online Learning Platform</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $systemName }} - Quality education through innovative e-learning solutions">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #06A3DA;
            --secondary-color: #0056B3;
            --accent-color: #FF6B6B;
            --success-color: #28A745;
            --warning-color: #FFC107;
            --dark-color: #2C3E50;
            --light-color: #F8F9FA;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 10px;
            transition: color 0.3s ease;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,96C1248,75,1344,53,1392,42.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 2px 4px 8px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-weight: 400;
            animation: fadeInUp 1s ease-out 0.2s;
            animation-fill-mode: both;
        }
        
        .hero-buttons {
            animation: fadeInUp 1s ease-out 0.4s;
            animation-fill-mode: both;
        }
        
        .btn-hero {
            padding: 15px 35px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: var(--light-color);
        }
        
        .btn-secondary-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary-hero:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: var(--light-color);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e9ecef;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.6;
        }
        
        /* Courses Section */
        .courses-section {
            padding: 100px 0;
            background: white;
        }
        
        .course-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e9ecef;
        }
        
        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            color: white;
            position: relative;
        }
        
        .course-header.exams {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        }
        
        .course-header.resources {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
        
        .course-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .course-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .course-body {
            padding: 30px;
        }
        
        .course-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .course-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: 20px;
        }
        
        .course-price.paid {
            color: var(--warning-color);
        }
        
        .course-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-course {
            flex: 1;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary-course {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary-course:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-secondary-course {
            background: var(--light-color);
            color: var(--dark-color);
            border: 1px solid #dee2e6;
        }
        
        .btn-secondary-course:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        /* Categories Section */
        .categories-section {
            padding: 100px 0;
            background: var(--light-color);
        }
        
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            cursor: pointer;
        }
        
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .category-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        .category-card:hover .category-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .category-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .category-count {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Instructors Section */
        .instructors-section {
            padding: 100px 0;
            background: white;
        }
        
        .instructor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        
        .instructor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .instructor-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 30px auto 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .instructor-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .instructor-title {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .instructor-social {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            background: var(--light-color);
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
        }
        
        /* Testimonials Section */
        .testimonials-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .testimonial-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 30px;
            font-style: italic;
        }
        
        .testimonial-author {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .testimonial-role {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .testimonial-rating {
            color: #FFC107;
            margin-bottom: 20px;
        }
        
        /* Footer */
        footer {
            background: var(--dark-color);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-widget h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: white;
        }
        
        .footer-widget ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-widget ul li {
            margin-bottom: 12px;
        }
        
        .footer-widget ul li a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-widget ul li a:hover {
            color: var(--primary-color);
        }
        
        .footer-bottom {
            border-top: 1px solid #444;
            margin-top: 40px;
            padding-top: 30px;
            text-align: center;
            color: #bbb;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .feature-card,
            .course-card,
            .category-card,
            .instructor-card {
                margin-bottom: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .btn-hero {
                padding: 12px 25px;
                font-size: 1rem;
                margin: 5px;
            }
            
            .hero-buttons {
                text-align: center;
            }
        }
        
        /* Animation Classes */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-graduation-cap me-2"></i>{{ $systemName }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('welcome') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#courses">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#instructors">Instructors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.exams.index') }}">Exams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blogs.public.index') }}">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('students.create') }}">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">The Best Online Learning Platform</h1>
                    <p class="hero-subtitle">Get quality education from experienced instructors. Join thousands of students already learning with us.</p>
                    <div class="hero-buttons">
                        <a href="{{ route('public.exams.index') }}" class="btn-hero btn-primary-hero">
                            <i class="fas fa-play-circle me-2"></i>Start Learning
                        </a>
                        <a href="#about" class="btn-hero btn-secondary-hero">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title animate-on-scroll">Why Choose Us</h2>
                    <p class="section-subtitle animate-on-scroll">We provide the best learning experience with modern features</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3 class="feature-title">Skilled Instructors</h3>
                        <p class="feature-description">Learn from experienced teachers who are experts in their fields and passionate about education.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3 class="feature-title">Online Classes</h3>
                        <p class="feature-description">Attend classes from anywhere with our flexible online learning platform.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3 class="feature-title">Home Projects</h3>
                        <p class="feature-description">Practice what you learn with practical projects and assignments.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 class="feature-title">Book Library</h3>
                        <p class="feature-description">Access our comprehensive digital library with thousands of resources.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="courses-section" id="courses">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title animate-on-scroll">Popular Courses</h2>
                    <p class="section-subtitle animate-on-scroll">Explore our most popular courses and exams</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card animate-on-scroll">
                        <div class="course-header">
                            <h3 class="course-title">Public Exams</h3>
                            <p class="course-subtitle">Take exams online</p>
                        </div>
                        <div class="course-body">
                            <div class="course-meta">
                                <span><i class="fas fa-clock me-1"></i> Flexible Time</span>
                                <span><i class="fas fa-signal me-1"></i> All Levels</span>
                            </div>
                            <div class="course-price free">
                                <i class="fas fa-check-circle me-1"></i> Free & Paid
                            </div>
                            <div class="course-actions">
                                <a href="{{ route('public.exams.index') }}" class="btn-course btn-primary-course">
                                    <i class="fas fa-play me-1"></i> Start Exam
                                </a>
                                <a href="#" class="btn-course btn-secondary-course">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card animate-on-scroll">
                        <div class="course-header exams">
                            <h3 class="course-title">Practice Tests</h3>
                            <p class="course-subtitle">Improve your skills</p>
                        </div>
                        <div class="course-body">
                            <div class="course-meta">
                                <span><i class="fas fa-clock me-1"></i> Self-paced</span>
                                <span><i class="fas fa-signal me-1"></i> Beginner</span>
                            </div>
                            <div class="course-price free">
                                <i class="fas fa-check-circle me-1"></i> Free
                            </div>
                            <div class="course-actions">
                                <a href="{{ route('public.exams.index') }}" class="btn-course btn-primary-course">
                                    <i class="fas fa-play me-1"></i> Practice
                                </a>
                                <a href="#" class="btn-course btn-secondary-course">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card animate-on-scroll">
                        <div class="course-header resources">
                            <h3 class="course-title">Study Resources</h3>
                            <p class="course-subtitle">Learning materials</p>
                        </div>
                        <div class="course-body">
                            <div class="course-meta">
                                <span><i class="fas fa-download me-1"></i> Download</span>
                                <span><i class="fas fa-signal me-1"></i> All Levels</span>
                            </div>
                            <div class="course-price free">
                                <i class="fas fa-check-circle me-1"></i> Free
                            </div>
                            <div class="course-actions">
                                <a href="{{ route('blogs.public.index') }}" class="btn-course btn-primary-course">
                                    <i class="fas fa-book me-1"></i> Resources
                                </a>
                                <a href="#" class="btn-course btn-secondary-course">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title animate-on-scroll">Course Categories</h2>
                    <p class="section-subtitle animate-on-scroll">Browse courses by category</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card animate-on-scroll">
                        <div class="category-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3 class="category-title">Web Design</h3>
                        <p class="category-count">49 Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card animate-on-scroll">
                        <div class="category-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3 class="category-title">Graphic Design</h3>
                        <p class="category-count">49 Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card animate-on-scroll">
                        <div class="category-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <h3 class="category-title">Video Editing</h3>
                        <p class="category-count">49 Courses</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card animate-on-scroll">
                        <div class="category-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="category-title">Online Marketing</h3>
                        <p class="category-count">49 Courses</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instructors Section -->
    <section class="instructors-section" id="instructors">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title animate-on-scroll">Expert Instructors</h2>
                    <p class="section-subtitle animate-on-scroll">Learn from the best in the field</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="instructor-card animate-on-scroll">
                        <div class="instructor-image">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="instructor-name">Dr. John Smith</h3>
                        <p class="instructor-title">Mathematics Expert</p>
                        <div class="instructor-social">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="instructor-card animate-on-scroll">
                        <div class="instructor-image">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="instructor-name">Prof. Sarah Johnson</h3>
                        <p class="instructor-title">Science Specialist</p>
                        <div class="instructor-social">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="instructor-card animate-on-scroll">
                        <div class="instructor-image">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="instructor-name">Mr. Michael Brown</h3>
                        <p class="instructor-title">Language Teacher</p>
                        <div class="instructor-social">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="instructor-card animate-on-scroll">
                        <div class="instructor-image">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="instructor-name">Ms. Emily Davis</h3>
                        <p class="instructor-title">Arts & Design</p>
                        <div class="instructor-social">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title animate-on-scroll">Our Students Say!</h2>
                    <p class="section-subtitle animate-on-scroll">Real experiences from our learners</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card animate-on-scroll">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"ESA Portal has transformed my learning experience. The platform is easy to use and the instructors are amazing!"</p>
                        <h4 class="testimonial-author">Alex Johnson</h4>
                        <p class="testimonial-role">Student</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card animate-on-scroll">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"The quality of education and the flexibility of online learning have helped me achieve my goals."</p>
                        <h4 class="testimonial-author">Maria Garcia</h4>
                        <p class="testimonial-role">Student</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card animate-on-scroll">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"I love the interactive features and the support from teachers. It's the best learning platform I've used."</p>
                        <h4 class="testimonial-author">David Kim</h4>
                        <p class="testimonial-role">Student</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3><i class="fas fa-graduation-cap me-2"></i>{{ $systemName }}</h3>
                        <p>Excellent Servant Academy provides quality education through innovative e-learning solutions.</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="{{ route('welcome') }}">Home</a></li>
                            <li><a href="{{ route('public.exams.index') }}">Exams</a></li>
                            <li><a href="{{ route('blogs.public.index') }}">Blog</a></li>
                            <li><a href="{{ route('students.create') }}">Register</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3>Support</h3>
                        <ul>
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3>Contact</h3>
                        <ul>
                            <li><i class="fas fa-envelope me-2"></i>info@esa.ac.rw</li>
                            <li><i class="fas fa-phone me-2"></i>+250 788 525 247</li>
                            <li><i class="fas fa-map-marker-alt me-2"></i>Kigali, Rwanda</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved. | Designed with <i class="fas fa-heart text-danger"></i> for better education</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 5px 30px rgba(0, 0, 0, 0.15)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animation on scroll
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementBottom = element.getBoundingClientRect().bottom;
                if (elementTop < window.innerHeight && elementBottom > 0) {
                    element.classList.add('animated');
                }
            });
        }

        // Run animation on scroll
        window.addEventListener('scroll', animateOnScroll);
        
        // Run animation on load
        window.addEventListener('load', animateOnScroll);

        // Category cards click effect
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                // Add ripple effect or navigation logic here
                console.log('Category clicked');
            });
        });

        // Counter animation for statistics (if needed)
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start);
                }
            }, 16);
        }
    </script>
</body>
</html>
