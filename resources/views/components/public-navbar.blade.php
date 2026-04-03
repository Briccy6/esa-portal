@php
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA-Parnel');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp

<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center shadow-sm">
                    @if(\App\Helpers\LogoHelper::hasLogo())
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-10 object-contain">
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold section-heading">E-Learning Portal</h1>
                    <p class="text-sm text-text-light">Excellent Servant Academy</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('welcome') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-home mr-1"></i>Home
                </a>
                <a href="{{ route('blogs.public.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-newspaper mr-1"></i>Blog
                </a>
                <a href="{{ route('public.exams.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-brain mr-1"></i>Test Skills
                </a>
                <a href="{{ route('public.resources.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-book mr-1"></i>Notes
                </a>
                <a href="{{ route('public.babyeyi.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-child mr-1"></i>Babyeyi
                </a>
                <a href="{{ route('public.fees.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-credit-card mr-1"></i>Fees
                </a>
                <a href="#quick-access" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center">
                    <i class="fas fa-sign-in-alt mr-1"></i>Portals
                </a>
            </nav>

            <!-- Login / Register (desktop) -->
            <div class="hidden md:flex items-center space-x-3">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300 shadow-sm">
                        <span class="text-gray-700 font-medium">Login</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-border-color py-2 z-10">
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
                        <a href="{{ route('client.auth.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                            <i class="fas fa-briefcase mr-2 text-orange-600"></i>Client Login
                        </a>
                    </div>
                </div>
                <a href="{{ route('register.choice') }}" class="btn-primary px-6 py-2 rounded-lg font-medium shadow-md">
                    Register
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Mobile Navigation -->
                <div x-show="open" @click.away="open = false" x-transition.origin.top
                     class="absolute left-0 top-full w-full bg-white shadow-lg py-4 mt-2">
                    <nav class="flex flex-col items-center space-y-4">
                        <a href="{{ route('welcome') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
                        <a href="{{ route('blogs.public.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-newspaper mr-1"></i>Blog
                        </a>
                        <a href="{{ route('public.exams.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-brain mr-1"></i>Test Skills
                        </a>
                        <a href="{{ route('public.resources.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-book mr-1"></i>Notes
                        </a>
                        <a href="{{ route('public.babyeyi.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-child mr-1"></i>Babyeyi
                        </a>
                        <a href="{{ route('public.fees.index') }}" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-credit-card mr-1"></i>Fees
                        </a>
                        <a href="#quick-access" class="text-text-light hover:text-accent-color font-medium transition duration-300 flex items-center" @click="open = false">
                            <i class="fas fa-sign-in-alt mr-1"></i>Portals
                        </a>
                        <div class="w-full border-t border-gray-200 pt-4 mt-4"></div>
                        <div class="relative w-full text-center" x-data="{ loginOpen: false }">
                            <button @click="loginOpen = !loginOpen"
                                    class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-300">
                                <span class="text-gray-700 font-medium">Login</span>
                                <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                            </button>
                            <div x-show="loginOpen" @click.away="loginOpen = false" x-transition
                                 class="mt-2 w-full bg-white rounded-lg shadow-lg border border-border-color py-2">
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
                                <a href="{{ route('client.auth.login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition duration-150">
                                    <i class="fas fa-briefcase mr-2 text-orange-600"></i>Client Login
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('register.choice') }}" class="btn-primary w-11/12 px-6 py-2 rounded-lg font-medium shadow-md">
                            Register
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

