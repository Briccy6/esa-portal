@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp

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
                <a href="{{ route('public.exams.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Test Skills</a>
                <a href="{{ route('public.resources.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300">Notes</a>
                <a href="#quick-access" class="text-text-light hover:text-accent-color font-medium transition duration-300">Portals</a>
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
    <div class="container mx-auto px-6">
        <nav class="flex flex-col items-center space-y-4">
            <a href="#home" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Home</a>
            <a href="{{ route('public.exams.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Test Skills</a>
            <a href="{{ route('public.resources.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Notes</a>
            <a href="#quick-access" class="text-text-light hover:text-accent-color font-medium transition duration-300" @click="open = false">Portals</a>
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
</div>

<!-- Include the welcome page styles for navigation -->
@push('styles')
<style>
    /* Modern Design System */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    :root {
        --primary-color: #1e293b;
        --secondary-color: #3b82f6;
        --accent-color: #06b6d4;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* Enhanced Typography */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        line-height: 1.2;
    }

    /* Smooth Scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 10px;
    }

    ::-webkit-scrollbar-track {
        background: var(--bg-light);
    }

    ::-webkit-scrollbar-thumb {
        background: var(--secondary-color);
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--accent-color);
    }

    /* Enhanced Button Styles */
    .btn-primary {
        background: var(--gradient-primary);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
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

    .btn-primary:active {
        transform: translateY(0);
    }

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

    .text-accent-color {
        color: var(--secondary-color);
    }

    .text-text-light {
        color: var(--text-light);
    }
</style>
@endpush
