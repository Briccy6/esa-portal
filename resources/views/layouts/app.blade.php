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
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    @if(\App\Helpers\LogoHelper::hasLogo())
                        <img src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="ESA Logo" class="h-10 w-auto mr-3">
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">ESA Portal</h1>
                        <p class="text-xs text-gray-600">Excellent Servant Academy</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Home</a>
                    <a href="{{ route('public.exams.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Exams</a>
                    <a href="{{ route('blogs.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Blog</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Contact</a>
                    
                    @if(auth()->guard('student')->check())
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 font-medium">
                                <i class="fas fa-user-circle"></i>
                                <span>{{ auth()->guard('student')->user()->full_name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border border-gray-200">
                                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('subscriptions.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-crown mr-2"></i>Subscriptions
                                </a>
                                <a href="{{ route('tokens.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-coins mr-2"></i>Tokens
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('student.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('student.login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Login</a>
                        <a href="{{ route('students.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Register
                        </a>
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="open" @click.away="open = false" x-transition class="md:hidden bg-white border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('welcome') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Home</a>
                <a href="{{ route('public.exams.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Exams</a>
                <a href="{{ route('blogs.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Blog</a>
                <a href="#contact" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Contact</a>
                
                @if(auth()->guard('student')->check())
                    <hr class="my-2">
                    <a href="{{ route('student.dashboard') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                        <i class="fas fa-crown mr-2"></i>Subscriptions
                    </a>
                    <a href="{{ route('tokens.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                        <i class="fas fa-coins mr-2"></i>Tokens
                    </a>
                    <form method="POST" action="{{ route('student.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <hr class="my-2">
                    <a href="{{ route('student.login') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Login</a>
                    <a href="{{ route('students.create') }}" class="block px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Register</a>
                @endif
            </div>
        </div>
    </nav>

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
