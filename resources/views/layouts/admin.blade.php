<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-learning Portal | Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/esa.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <nav class="bg-white border-b border-gray-200 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Breadcrumb or Page Title -->
                        @isset($header)
                            <h1 class="text-xl font-semibold text-gray-800">{{ $header }}</h1>
                        @else
                            <h1 class="text-xl font-semibold text-gray-800">Admin Dashboard</h1>
                        @endisset
                    </div>

                    <!-- User Dropdown -->
                    <div class="flex items-center space-x-4">
                        <!-- Support Info -->
                        <div class="hidden md:flex items-center text-sm text-gray-600">
                            <span class="mr-2">📞 Support:</span>
                            <a href="tel:+250788123456" class="text-blue-600 hover:underline">+250 788 123 456</a>
                        </div>

                        <!-- Current Time -->
                        <div class="hidden md:flex items-center text-sm text-gray-600">
                            <span class="mr-2">🕒</span>
                            <span id="currentTime" class="font-medium"></span>
                        </div>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" 
                                    class="flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium mr-2">
                                        {{ Auth::check() ? substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1) : 'A' }}
                                    </div>
                                    {{ Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : 'Guest' }}
                                </div>
                                <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1" role="menu">
                                    <!-- Profile -->
                                    <a href="{{ route('profile.edit') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Profile
                                        </div>
                                    </a>

                                    <!-- Settings -->
                                    <a href="#" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c.94-1.543-.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            </svg>
                                            Settings
                                        </div>
                                    </a>

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3V1"/>
                                                </svg>
                                                Logout
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Page Heading -->
                @isset($header)
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $header }}</h2>
                        @isset($subtitle)
                            <p class="text-gray-600 mt-1">{{ $subtitle }}</p>
                        @endisset
                    </div>
                @endisset

                <!-- Main Content Area -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js for interactive components -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <!-- Time Update Script -->
    <script>
        function updateTime() {
            const timeElement = document.getElementById("currentTime");
            const now = new Date();
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const formattedDateTime = now.toLocaleDateString('en-US', options);
            
            if (timeElement) {
                timeElement.textContent = formattedDateTime;
            }
        }

        // Update time immediately and then every minute
        updateTime();
        setInterval(updateTime, 60000);

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('[x-data*="open"]');
            dropdowns.forEach(function(dropdown) {
                if (!dropdown.contains(event.target)) {
                    dropdown.__x.$data.open = false;
                }
            });
        });
    </script>
</body>
</html>
