<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Dashboard - ESA Portal')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Excellent Servant Academy - Quality Education for All')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
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
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>
