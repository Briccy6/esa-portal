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
    <title>{{$systemName}}</title>
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
            color: var(--primary-color);
        }

        .card-shadow-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
        }
        .card-shadow-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        /* Hero Section Specifics */
        .hero-carousel {
            height: 500px; /* Fixed height for carousel */
            overflow: hidden;
            position: relative;
            background-color: var(--primary-color); /* Fallback */
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .carousel-item.active {
            opacity: 1;
        }

        .carousel-overlay {
            background: rgba(0, 0, 0, 0.4); /* Dark overlay for text readability */
            width: 100%;
            height: 100%;
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

        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.3);
            color: white;
            padding: 1rem 1.2rem;
            border-radius: 9999px;
            cursor: pointer;
            z-index: 10;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        .carousel-control:hover {
            opacity: 1;
        }
        .carousel-control.left {
            left: 1.5rem;
        }
        .carousel-control.right {
            right: 1.5rem;
        }

        /* Announcement Scroll Area */
        .announcement-scroll-area {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 1rem;
        }
        .announcement-scroll-area::-webkit-scrollbar {
            width: 8px;
        }
        .announcement-scroll-area::-webkit-scrollbar-track {
            background: var(--bg-light);
            border-radius: 10px;
        }
        .announcement-scroll-area::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 10px;
        }
        .announcement-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #2196f3;
        }

        /* Floating WhatsApp Button */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366; /* WhatsApp Green */
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 4px 4px 8px #999;
        }

        /* Floating Notice Board */
        .notice-board {
            position: fixed;
            top: 50%;
            right: -400px;
            transform: translateY(-50%);
            width: 380px;
            max-height: 600px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px 0 0 20px;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow: hidden;
        }

        .notice-board.open {
            right: 0;
        }

        .notice-board-toggle {
            position: absolute;
            left: -50px;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px 0 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 24px;
            transition: all 0.3s ease;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2);
        }

        .notice-board-toggle:hover {
            left: -55px;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .notice-board-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .notice-board-content {
            padding: 20px;
            max-height: 450px;
            overflow-y: auto;
        }

        .notice-board-content::-webkit-scrollbar {
            width: 6px;
        }

        .notice-board-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .notice-board-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .notice-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .notice-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .notice-item h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .notice-item p {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .notice-item .date {
            color: var(--secondary-color);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .notice-empty {
            text-align: center;
            padding: 40px 20px;
            color: rgba(255, 255, 255, 0.8);
        }

        .notice-empty i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.6;
        }

        .notice-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(231, 76, 60, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .notice-board {
                width: 320px;
                right: -340px;
            }
            
            .notice-board.open {
                right: 0;
            }
            
            .notice-board-toggle {
                left: -45px;
                width: 45px;
                height: 80px;
                font-size: 20px;
            }
            
            .hero-carousel {
                height: 400px;
            }
            .carousel-text h2 {
                font-size: 2.5rem;
            }
            .carousel-text p {
                font-size: 1.2rem;
            }
            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
                font-size: 24px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center shadow-sm">
                         @if(\App\Helpers\LogoHelper::hasLogo())
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-10 object-contain">
                    </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold section-heading">E-Learning Portal</h1>
                        <p class="text-sm text-text-light">Excellent Servant Academy</p>
                    </div>
                </div>
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="#home" class="text-text-light hover:text-accent-color font-medium transition duration-300">Home</a>
                    <a href="{{ route('public.exams.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Exams</a>
                    <a href="#announcements" class="text-text-light hover:text-accent-color font-medium transition duration-300">Announcements</a>
                    <a href="{{ route('blogs.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Blog</a>
                    <a href="#quick-access" class="text-text-light hover:text-accent-color font-medium transition duration-300">Portals</a>
                    <a href="#about" class="text-text-light hover:text-accent-color font-medium transition duration-300">About</a>
                    <a href="#contact" class="text-text-light hover:text-accent-color font-medium transition duration-300">Contact</a>
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
                    <a href="{{ route('register.choice') }}" class="btn-primary px-6 py-2 rounded-lg font-medium shadow-md">
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

    <!-- Mobile Navigation (hidden by default, shown by Alpine.js) -->
    <div class="md:hidden bg-white shadow-lg py-4" x-data="{ open: false }" x-show="open" @click.away="open = false" x-transition.origin.top>
        <nav class="flex flex-col items-center space-y-4">
            <a href="#home" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Home</a>
            <a href="{{ route('public.exams.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Exams</a>
            <a href="#announcements" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Announcements</a>
            <a href="{{ route('blogs.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Blog</a>
            <a href="#quick-access" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Portals</a>
            <a href="#about" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">About</a>
            <a href="#contact" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Contact</a>
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
            <a href="{{ route('register.choice') }}" class="btn-primary w-full px-6 py-2 rounded-lg font-medium shadow-md">
                Register
            </a>
        </nav>
    </div>

    <!-- Live Streaming Banner -->
    @include('components.live-streaming-banner')

    <!-- Hero Section with Carousel -->
    <section id="home" class="hero-carousel">
        <!-- Carousel Items - Using placeholder images for demo -->
        <div class="carousel-item active" style="background-image: url('images/1.jpg');">
            <div class="carousel-overlay">
                <div class="carousel-text">
                    <h2 class="text-white">Empowering Minds for a Brighter Future</h2>
                    <p class="text-gray-200">Discover a world of knowledge and opportunity with our comprehensive e-learning programs.</p>
                    <a href="#quick-access" class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105">Explore Programs <i class="fas fa-arrow-right ml-2"></i></a>
                </div>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('images/2.jpg');">
            <div class="carousel-overlay">
                <div class="carousel-text">
                    <h2 class="text-white">Interactive Learning, Anywhere, Anytime</h2>
                    <p class="text-gray-200">Engage with dynamic content, collaborate with peers, and learn at your own pace.</p>
                    <a href="#announcements" class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105">View Latest Updates <i class="fas fa-bell ml-2"></i></a>
                </div>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('images/3.jpg');">
            <div class="carousel-overlay">
                <div class="carousel-text">
                    <h2 class="text-white">Dedicated Educators, Proven Success</h2>
                    <p class="text-gray-200">Learn from experienced teachers committed to your academic and personal growth.</p>
                    <a href="{{ route('students.create') }}" class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105">Join Our Community <i class="fas fa-user-plus ml-2"></i></a>
                </div>
            </div>
        </div>

        <!-- Carousel Controls -->
        <div class="carousel-control left" onclick="plusSlides(-1)">&#10094;</div>
        <div class="carousel-control right" onclick="plusSlides(1)">&#10095;</div>
    </section>

    <!-- Public Exams Section -->
    <section id="public-exams" class="py-16 bg-bg-light">
        <div class="container mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-extrabold section-heading mb-4">
                    Test Your <span class="text-accent-color">Skills</span>
                </h2>
                <p class="text-lg text-text-light">Practice and prepare for your exams with our comprehensive skill assessments. Build confidence before the real exam.</p>
            </div>
            
            <!-- Featured Exams Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                @php
                    $featuredExams = \App\Models\Exam::where('is_public', true)
                        ->with(['courses', 'questions'])
                        ->take(6)
                        ->get();
                @endphp
                
                @forelse($featuredExams as $exam)
                    <div class="card-shadow-hover bg-white rounded-xl overflow-hidden shadow-md">
                        <!-- Exam Header -->
                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xl font-bold">{{ Str::limit($exam->title, 50) }}</h3>
                                <button onclick="shareExam({{ $exam->id }}, '{{ Str::limit($exam->title, 30) }}')" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-lg transition-all duration-200"
                                        title="Share this exam">
                                    <i class="fas fa-share-alt text-white"></i>
                                </button>
                            </div>
                            
                            @if($exam->courses->count() > 0)
                                <div class="text-sm text-purple-100">
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    {{ $exam->courses->pluck('name')->implode(', ') }}
                                </div>
                            @endif
                        </div>

                        <!-- Exam Body -->
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 text-sm">{{ Str::limit($exam->description ?? 'Practice your skills with this comprehensive assessment.', 80) }}</p>
                            
                            <!-- Exam Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ $exam->questions->count() }}</div>
                                    <div class="text-xs text-gray-600">Questions</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">
                                        @if($exam->requiresPayment())
                                            RWF {{ number_format($exam->public_price, 0) }}
                                        @else
                                            FREE
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-600">Price</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <a href="{{ route('public.exams.show', $exam) }}" 
                                   class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center block font-medium">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                
                                @if($exam->isAccessible())
                                    <a href="{{ route('public.exams.start', $exam) }}" 
                                       class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block font-medium">
                                        <i class="fas fa-play mr-2"></i>Start Practice
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-brain text-purple-400 text-3xl"></i>
                        </div>
                        <p class="text-gray-600 text-lg">No practice tests available at the moment. Check back soon!</p>
                    </div>
                @endforelse
            </div>
            
            <!-- View All Button -->
            <div class="text-center">
                <a href="{{ route('public.exams.index') }}" class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105 transition-all">
                    <i class="fas fa-dumbbell mr-2"></i>View All Practice Tests
                </a>
            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <section id="resources" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-extrabold section-heading mb-4">
                    Educational <span class="text-accent-color">Resources</span>
                </h2>
                <p class="text-lg text-text-light">Access quality learning materials, notes, and videos from our expert teachers.</p>
            </div>
            
            <!-- Featured Resources Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                @php
                    $featuredResources = \App\Models\Resource::with(['course', 'teacher', 'branch'])
                        ->take(6)
                        ->get();
                @endphp
                
                @forelse($featuredResources as $resource)
                    <div class="card-shadow-hover bg-white rounded-xl overflow-hidden shadow-md">
                        <!-- Resource Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xl font-bold">{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 40) }}</h3>
                                <button onclick="shareResource({{ $resource->id }}, '{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 30) }}')" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-lg transition-all duration-200"
                                        title="Share this resource">
                                    <i class="fas fa-share-alt text-white"></i>
                                </button>
                            </div>
                            
                            <div class="text-sm text-blue-100">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                {{ $resource->course->course_name ?? 'General' }}
                            </div>
                        </div>

                        <!-- Resource Body -->
                        <div class="p-6">
                            <!-- Resource Info -->
                            <div class="space-y-2 mb-4 text-sm text-gray-600">
                                <div>
                                    <i class="fas fa-user-tie mr-2 text-blue-600"></i>
                                    <span>{{ $resource->teacher->teacher_name ?? 'Expert Teacher' }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    <span>{{ $resource->branch->name ?? 'Main Campus' }}</span>
                                </div>
                            </div>

                            <!-- Resource Type Icons -->
                            <div class="flex space-x-3 mb-4">
                                @if($resource->document_blob)
                                    <span class="text-blue-600" title="Document Available">
                                        <i class="fas fa-file-alt text-lg"></i>
                                    </span>
                                @endif
                                @if($resource->video_blob)
                                    <span class="text-red-600" title="Video Available">
                                        <i class="fas fa-video text-lg"></i>
                                    </span>
                                @endif
                                @if($resource->youtube_link)
                                    <span class="text-red-600" title="YouTube Video">
                                        <i class="fab fa-youtube text-lg"></i>
                                    </span>
                                @endif
                            </div>

                            <!-- Price Badge -->
                            <div class="mb-4">
                                @if($resource->is_free)
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        <i class="fas fa-gift mr-1"></i>FREE
                                    </span>
                                @else
                                    <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        <i class="fas fa-tag mr-1"></i>RWF {{ number_format($resource->price, 0) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <a href="{{ route('public.resources.show', $resource->id) }}" 
                                   class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block font-medium">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                
                                @if($resource->document_blob || $resource->video_blob)
                                    <a href="{{ $resource->is_free ? route('public.resources.download', $resource->id) : route('public.resources.payment', $resource->id) }}" 
                                       class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block font-medium">
                                        <i class="fas fa-{{ $resource->is_free ? 'download' : 'shopping-cart' }} mr-2"></i>{{ $resource->is_free ? 'Download' : 'Purchase' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-book text-blue-400 text-3xl"></i>
                        </div>
                        <p class="text-gray-600 text-lg">No resources available at the moment. Check back soon!</p>
                    </div>
                @endforelse
            </div>
            
            <!-- View All Button -->
            <div class="text-center">
                <a href="{{ route('public.resources.index') }}" class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105 transition-all">
                    <i class="fas fa-external-link-alt mr-2"></i>Browse All Resources
                </a>
            </div>
        </div>
    </section>

    <!-- Announcements Section -->
    <section id="announcements" class="py-16 bg-bg-light">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-4xl font-extrabold section-heading mb-4">
                        Important <span class="text-accent-color">Announcements</span>
                    </h2>
                    <p class="text-lg text-text-light">Stay informed with the latest news and updates from ESA.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 border border-border-color shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-secondary-color rounded-lg flex items-center justify-center mr-4 shadow-md">
                            <i class="fas fa-bullhorn text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold section-heading">Public Announcements</h3>
                    </div>
                    
                    <div class="space-y-5 announcement-scroll-area">
                        @forelse($publicAnnouncements as $announcement)
                            <div class="card-shadow-hover bg-white rounded-lg p-6 shadow-md">
                                <h4 class="font-bold section-heading text-xl mb-2">{{ $announcement->title }}</h4>
                                <p class="text-text-light leading-relaxed mb-3">{{ $announcement->content }}</p>
                                <span class="text-sm text-accent-color font-semibold flex items-center">
                                    <i class="far fa-calendar-alt mr-2 text-sm"></i>{{ $announcement->created_at->format('M d, Y H:i A') }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-200">
                                    <i class="fas fa-bell-slash text-gray-400 text-3xl"></i>
                                </div>
                                <p class="text-gray-600 text-xl font-medium mb-2">No public announcements at this time.</p>
                                <p class="text-gray-500 text-md">Check back later for updates and important news.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access Section -->
    <section id="quick-access" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-extrabold section-heading mb-4">Quick <span class="text-accent-color">Access Portals</span></h2>
                <p class="text-lg text-text-light">Select your role to securely log in and start your journey.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <a href="{{ route('admin.login.form') }}" class="card-shadow-hover bg-white rounded-xl p-8 text-center shadow-md">
                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                        <i class="fas fa-user-shield text-white text-3xl"></i>
                    </div>
                    <h3 class="font-bold section-heading text-xl mb-2">Admin Portal</h3>
                    <p class="text-text-light text-sm mb-4">Access administrative tools and manage the platform.</p>
                    <span class="text-red-600 font-semibold text-md flex items-center justify-center">
                        Access Now <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </span>
                </a>
                
                <a href="{{ route('staff.login') }}" class="card-shadow-hover bg-white rounded-xl p-8 text-center shadow-md">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                        <i class="fas fa-users text-white text-3xl"></i>
                    </div>
                    <h3 class="font-bold section-heading text-xl mb-2">Staff Portal</h3>
                    <p class="text-text-light text-sm mb-4">Handle student records, course enrollment, and operations.</p>
                    <span class="text-blue-600 font-semibold text-md flex items-center justify-center">
                        Access Now <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </span>
                </a>
                
                <a href="{{ route('teacher.login') }}" class="card-shadow-hover bg-white rounded-xl p-8 text-center shadow-md">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                        <i class="fas fa-chalkboard-teacher text-white text-3xl"></i>
                    </div>
                    <h3 class="font-bold section-heading text-xl mb-2">Teacher Portal</h3>
                    <p class="text-text-light text-sm mb-4">Create engaging lessons, manage classes, and grade assignments.</p>
                    <span class="text-green-600 font-semibold text-md flex items-center justify-center">
                        Access Now <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </span>
                </a>
                
                <a href="{{ route('student.login') }}" class="card-shadow-hover bg-white rounded-xl p-8 text-center shadow-md">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                        <i class="fas fa-user-graduate text-white text-3xl"></i>
                    </div>
                    <h3 class="font-bold section-heading text-xl mb-2">Student Portal</h3>
                    <p class="text-text-light text-sm mb-4">Access courses, submit work, and track your academic progress.</p>
                    <span class="text-purple-600 font-semibold text-md flex items-center justify-center">
                        Access Now <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <!-- Placeholder sections for About and Contact -->
    <section id="about" class="py-16 bg-bg-light">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-extrabold section-heading mb-4">About <span class="text-accent-color">ESA</span></h2>
            <p class="text-lg text-text-light max-w-3xl mx-auto">Excellent Servant Academy is committed to providing high-quality, accessible education through innovative e-learning solutions. We aim to empower students with the knowledge and skills necessary for success in a rapidly changing world.</p>
            <p class="text-lg text-text-light max-w-3xl mx-auto mt-4">Our platform offers a rich curriculum, expert instructors, and a supportive community, ensuring a comprehensive and engaging learning experience for all.</p>
            <a href="#" class="mt-8 inline-block btn-primary px-6 py-3 rounded-lg font-medium shadow-md">Learn More <i class="fas fa-info-circle ml-2"></i></a>
        </div>
    </section>

    <section id="contact" class="py-16 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-extrabold section-heading mb-4">Get in <span class="text-accent-color">Touch</span></h2>
            <p class="text-lg text-text-light max-w-3xl mx-auto">Have questions? We're here to help. Reach out to us through the following channels:</p>
            <div class="mt-8 flex justify-center space-x-8">
                <a href="mailto:info@esa.com" class="text-text-light hover:text-accent-color transition duration-300 flex items-center text-lg">
                    <i class="fas fa-envelope mr-2 text-2xl"></i> info@excellentservantacademy.com
                </a>
                <a href="tel:+250788525247" class="text-text-light hover:text-accent-color transition duration-300 flex items-center text-lg">
                    <i class="fas fa-phone mr-2 text-2xl"></i> +250 788 525 247
                </a>
            </div>
            <div class="mt-8">
                <p class="text-gray-500 text-sm">You can also use the floating chat button at the bottom right for a quick WhatsApp message!</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary-color py-8 text-white text-center">
        <div class="container mx-auto px-6">
            <p>&copy; {{ date('Y') }} {{ $systemName }}. All rights reserved.</p>
            <div class="flex justify-center space-x-4 mt-4">
                <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-gray-300 hover:text-white transition duration-300"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <!-- Floating Notice Board -->
    <div class="notice-board" x-data="{ open: false, announcementCount: {{ $publicAnnouncements->count() }} }">
        <div class="notice-board-toggle" @click="open = !open">
            <i class="fas fa-bullhorn"></i>
            @if($publicAnnouncements->count() > 0)
                <span class="notice-badge" x-show="!open">{{ $publicAnnouncements->count() }}</span>
            @endif
        </div>
        
        <div class="notice-board-header">
            <h3 class="text-white text-xl font-bold mb-2">
                <i class="fas fa-bullhorn mr-2"></i>Notice Board
            </h3>
            <p class="text-white opacity-80 text-sm">Latest announcements & updates</p>
        </div>
        
        <div class="notice-board-content">
            @forelse($publicAnnouncements as $announcement)
                <div class="notice-item">
                    <h4>{{ Str::limit($announcement->title, 50) }}</h4>
                    <p>{{ Str::limit($announcement->content, 100) }}</p>
                    <div class="date">
                        <i class="far fa-calendar-alt mr-2"></i>
                        {{ $announcement->created_at->format('M d, Y H:i A') }}
                    </div>
                </div>
            @empty
                <div class="notice-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No announcements at this time</p>
                    <p class="text-sm opacity-60">Check back later for updates</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Floating WhatsApp Chat Button -->
    <a href="https://wa.me/250788525247" target="_blank" class="whatsapp-float" title="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script>
        // Carousel JavaScript
        let slideIndex = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;
        let autoSlideInterval;

        function showSlides(n) {
            // Reset index if out of bounds
            if (n >= totalSlides) { slideIndex = 0; }
            if (n < 0) { slideIndex = totalSlides - 1; }

            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));

            // Show current slide
            slides[slideIndex].classList.add('active');
        }

        function plusSlides(n) {
            resetAutoSlide(); // Reset auto-slide when manual navigation occurs
            slideIndex += n;
            showSlides(slideIndex);
        }

        function autoSlides() {
            slideIndex++;
            showSlides(slideIndex);
        }

        function startAutoSlide() {
            autoSlideInterval = setInterval(autoSlides, 5000); // Change image every 5 seconds
        }

        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            startAutoSlide();
        }

        document.addEventListener('DOMContentLoaded', () => {
            showSlides(slideIndex); // Initial display
            startAutoSlide(); // Start auto-sliding
        });

        // Alpine.js setup for mobile menu (already configured, but adding a note)
        // The mobile menu button's @click="open = !open" handles showing/hiding
        // and @click.away="open = false" closes it when clicking outside.
        // The mobile navigation links also close the menu on click for better UX.

        // Share Functions
        function shareExam(examId, examTitle) {
            const url = window.location.origin + '/public/exams/' + examId;
            const text = `Check out this practice test: ${examTitle}`;
            
            if (navigator.share) {
                navigator.share({
                    title: examTitle,
                    text: text,
                    url: url
                }).catch(err => console.log('Error sharing:', err));
            } else {
                // Fallback - copy to clipboard
                const shareText = `${text} - ${url}`;
                navigator.clipboard.writeText(shareText).then(() => {
                    showNotification('Link copied to clipboard!');
                }).catch(() => {
                    // Last resort - open in new window
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                });
            }
        }

        function shareResource(resourceId, resourceTitle) {
            const url = window.location.origin + '/public/resources/' + resourceId;
            const text = `Check out this educational resource: ${resourceTitle}`;
            
            if (navigator.share) {
                navigator.share({
                    title: resourceTitle,
                    text: text,
                    url: url
                }).catch(err => console.log('Error sharing:', err));
            } else {
                // Fallback - copy to clipboard
                const shareText = `${text} - ${url}`;
                navigator.clipboard.writeText(shareText).then(() => {
                    showNotification('Link copied to clipboard!');
                }).catch(() => {
                    // Last resort - open in new window
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                });
            }
        }

        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
 

</body>
</html>