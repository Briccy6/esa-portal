<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name', 'E-Learning Portal') }}</title>
        
        <!-- Modern Design System -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Modern Design System -->
        <style>
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
            
            /* Enhanced Typography */
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
                background: var(--bg-light);
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
                box-shadow: var(--shadow-lg);
                background: var(--gradient-primary);
            }
            
            .btn-primary:active {
                transform: translateY(0);
            }
            
            /* Enhanced Card Styles */
            .alx-card {
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
            
            .alx-card::before {
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
            
            .alx-card:hover::before {
                transform: scaleX(1);
            }
            
            .alx-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-lg);
                border-color: var(--secondary-color);
            }
            
            /* Enhanced Logo Animation */
            .logo-container {
                transition: all 0.3s ease;
            }
            
            .logo-container:hover {
                transform: scale(1.05);
            }
            
            /* Enhanced Form Styles */
            .form-input {
                background: var(--bg-white);
                border: 2px solid var(--border-color);
                border-radius: 12px;
                padding: 0.75rem 1rem;
                font-size: 1rem;
                transition: all 0.3s ease;
                width: 100%;
            }
            
            .form-input:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 5px rgba(30, 64, 175, 0.3);
            }
            
            .form-label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                color: var(--text-dark);
                margin-bottom: 0.5rem;
            }
            
            /* Enhanced Link Styles */
            .link-primary {
                color: var(--primary-color);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }
            
            .link-primary:hover {
                color: var(--accent-color);
                text-decoration: underline;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/" class="logo-container">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>
            
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                @yield('content')
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>
