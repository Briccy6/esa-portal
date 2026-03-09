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
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
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
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Home</a>
                    <a href="{{ route('public.exams.index') }}" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Exams</a>
                    <a href="#announcements" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Announcements</a>
                    <a href="{{ route('blogs.index') }}" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Blog</a>
                    <a href="#quick-access" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Portals</a>
                    <a href="#about" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">About</a>
                    <a href="#contact" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Contact</a>
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
            <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">Home</a>
            <a href="{{ route('public.exams.index') }}" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">Exams</a>
            <a href="#announcements" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">Announcements</a>
            <a href="{{ route('blogs.index') }}" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">Blog</a>
            <a href="#quick-access" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">Portals</a>
            <a href="#about" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">About</a>
            <a href="#contact" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300" @click="open = false">Contact</a>
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

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/250788525247" target="_blank" 
       class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full shadow-lg hover:bg-green-600 transition-all hover:scale-110 z-50 flex items-center justify-center">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>
