<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Excellent Servant Academy')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Excellent Servant Academy - Quality Education for All')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Enhanced CSS Variables for Public Pages -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Enhanced CSS Variables for Public Pages */
        :root {
            --primary-color: #1e293b;
            --secondary-color: #3498db;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-dark: #263238;
            --text-light: #455a64;
            --bg-light: #ecf0f1;
            --bg-white: #ffffff;
            --border-color: #cfd8dc;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        /* Enhanced Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-dark);
        }
        
        /* Enhanced Button Styles */
        .btn {
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--gradient-success);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
        }
        
        /* Enhanced Card Styles */
        .card {
            background: var(--bg-white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .card:hover::before {
            transform: scaleX(1);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--secondary-color);
        }
        
        .card-header {
            padding: 2.5rem 2rem 2rem;
            background: var(--gradient-primary);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
            50% { transform: translate(-30%, -30%) rotate(180deg); }
        }
        
        .card-header.exams {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        }
        
        .card-header.resources {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
        
        .card-body {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .card-title {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            color: var(--text-dark);
        }
        
        .card-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .card-price {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-price.free {
            color: var(--success-color);
        }
        
        .card-price.paid {
            color: var(--warning-color);
        }
        
        .card-actions {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        /* Section Headings */
        .section-heading {
            color: var(--primary-color);
            font-weight: 800;
            position: relative;
            display: inline-block;
        }
        
        .section-heading::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }
        
        /* Text Colors */
        .text-accent-color {
            color: var(--secondary-color);
        }
        
        .text-text-light {
            color: var(--text-light);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-light);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-color);
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Enhanced Mobile Responsive Styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            .card-header {
                padding: 2rem 1.5rem 1.5rem !important;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
            
            .btn {
                padding: 0.75rem 1.5rem !important;
                font-size: 0.875rem !important;
            }
            
            .card-title {
                font-size: 1.125rem !important;
                line-height: 1.4 !important;
            }
            
            .card-subtitle {
                font-size: 0.875rem !important;
            }
            
            .section-heading {
                font-size: 2rem !important;
            }
            
            .card {
                margin: 0 1rem 1.5rem 1rem !important;
            }
        }
        
        @media (max-width: 640px) {
            .card-header {
                padding: 1.5rem 1rem 1rem !important;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            .card-title {
                font-size: 1rem !important;
            }
            
            .btn {
                padding: 0.625rem 1.25rem !important;
                font-size: 0.8125rem !important;
            }
            
            .section-heading {
                font-size: 1.75rem !important;
            }
            
            .card {
                margin: 0 0.5rem 1rem 0.5rem !important;
                border-radius: 0.75rem !important;
            }
        }
        
        /* Touch-friendly buttons */
        @media (hover: none) and (pointer: coarse) {
            .btn {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation - Hidden for public pages that use their own navigation -->
    @if(!request()->is('public/*') && !request()->is('resources') && !request()->is('resources/*'))
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center shadow-sm">
                        @if(\App\Helpers\LogoHelper::hasLogo())
                            <img src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="Logo" class="h-10 w-10 object-contain">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-blue-900">E-Learning Portal</h1>
                        <p class="text-sm text-gray-600">Excellent Servant Academy</p>
                    </div>
                </div>
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">Home</a>
                    <a href="{{ route('public.exams.index') }}" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">Exams</a>
                    <a href="#announcements" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">Announcements</a>
                    <a href="{{ route('blogs.index') }}" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">Blog</a>
                    <a href="#quick-access" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">Portals</a>
                    <a href="#about" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">About</a>
                    <a href="#contact" class="text-gray-600 hover:text-accent-color font-medium transition duration-300">Contact</a>
                </nav>
                <!-- Login Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    @if(auth()->guard('student')->check())
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300 shadow-sm">
                                <i class="fas fa-user-circle text-gray-700"></i>
                                <span class="text-gray-700 font-medium">{{ auth()->guard('student')->user()->full_name }}</span>
                                <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10">
                                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                    <i class="fas fa-tachometer-alt mr-2 text-purple-600"></i>Dashboard
                                </a>
                                <a href="{{ route('subscriptions.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                    <i class="fas fa-crown mr-2 text-yellow-600"></i>Subscriptions
                                </a>
                                <a href="{{ route('tokens.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                    <i class="fas fa-coins mr-2 text-green-600"></i>Tokens
                                </a>
                                <hr class="my-2 border-gray-200">
                                <form method="POST" action="{{ route('student.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                        <i class="fas fa-sign-out-alt mr-2 text-red-600"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300 shadow-sm">
                                <span class="text-gray-700 font-medium">Login</span>
                                <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10">
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
                        <a href="{{ route('students.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium shadow-md hover:bg-blue-700 transition duration-300">
                            Register
                        </a>
                    @endif
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
            <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">Home</a>
            <a href="{{ route('public.exams.index') }}" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">Exams</a>
            <a href="#announcements" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">Announcements</a>
            <a href="{{ route('blogs.index') }}" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">Blog</a>
            <a href="#quick-access" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">Portals</a>
            <a href="#about" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">About</a>
            <a href="#contact" class="text-gray-600 hover:text-accent-color font-medium transition duration-300" @click="open = false">Contact</a>
            <div class="w-full border-t border-gray-200 pt-4 mt-4"></div>
            @if(auth()->guard('student')->check())
                <div class="relative w-full text-center" x-data="{ loginOpen: false }">
                    <button @click="loginOpen = !loginOpen" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300">
                        <i class="fas fa-user-circle text-gray-700"></i>
                        <span class="text-gray-700 font-medium">{{ auth()->guard('student')->user()->full_name }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
                    <div x-show="loginOpen" @click.away="loginOpen = false" x-transition class="mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                        <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                            <i class="fas fa-tachometer-alt mr-2 text-purple-600"></i>Dashboard
                        </a>
                        <a href="{{ route('subscriptions.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                            <i class="fas fa-crown mr-2 text-yellow-600"></i>Subscriptions
                        </a>
                        <a href="{{ route('tokens.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                            <i class="fas fa-coins mr-2 text-green-600"></i>Tokens
                        </a>
                        <hr class="my-2 border-gray-200">
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                <i class="fas fa-sign-out-alt mr-2 text-red-600"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="relative w-full text-center" x-data="{ loginOpen: false }">
                    <button @click="loginOpen = !loginOpen" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300">
                        <span class="text-gray-700 font-medium">Login</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
                    <div x-show="loginOpen" @click.away="loginOpen = false" x-transition class="mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-2">
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
                <a href="{{ route('students.create') }}" class="bg-blue-600 text-white w-full px-6 py-2 rounded-lg font-medium shadow-md hover:bg-blue-700 transition duration-300">
                    Register
                </a>
            @endif
        </nav>
    </div>
    @endif
                
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-b border-green-200">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-b border-red-200">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <p class="text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="fade-in">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">ESA Portal</h3>
                    <p class="text-gray-400 text-sm">
                        Excellent Servant Academy provides quality education through innovative e-learning solutions.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('welcome') }}" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('public.exams.index') }}" class="text-gray-400 hover:text-white transition-colors">Exams</a></li>
                        <li><a href="{{ route('blogs.index') }}" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="{{ route('students.create') }}" class="text-gray-400 hover:text-white transition-colors">Register</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p><i class="fas fa-envelope mr-2"></i>info@esa.ac.rw</p>
                        <p><i class="fas fa-phone mr-2"></i>+250 788 525 247</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Kigali, Rwanda</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Excellent Servant Academy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>
