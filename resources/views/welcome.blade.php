<!-- resources/views/welcome.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Welcome to Our App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        /* subtle animation for the heading */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeInUp {
            animation: fadeInUp 1s ease forwards;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-800 min-h-screen flex items-center justify-center text-gray-100">

    <div class="max-w-4xl mx-auto p-8 bg-white bg-opacity-10 rounded-xl shadow-lg backdrop-blur-sm">
        <header class="mb-8 text-center">
            <h1 class="text-5xl font-extrabold mb-4 animate-fadeInUp">Welcome to YourApp</h1>
            <p class="text-lg text-indigo-100 max-w-xl mx-auto">
                A reliable and modern platform built to simplify your workflow and empower your business.
            </p>
        </header>

        <div class="flex flex-col sm:flex-row justify-center gap-6">
            <a href="{{ route('admin.login') }}" 
               class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-md font-semibold shadow-md transition duration-300 text-center">
                Admin Login
            </a>

            <a href="{{ route('dashboard') }}" 
               class="px-8 py-3 border-2 border-indigo-600 hover:bg-indigo-600 hover:text-white rounded-md font-semibold shadow-md transition duration-300 text-center">
                User Dashboard
            </a>
        </div>

        <footer class="mt-12 text-center text-indigo-200 text-sm">
            &copy; {{ date('Y') }} YourApp. All rights reserved.
        </footer>
    </div>

</body>
</html>
