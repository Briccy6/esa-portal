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
            transform: translateY(-12px);
            box-shadow: var(--shadow-xl);
            border-color: var(--secondary-color);
        }
        
        .alx-card-header {
            padding: 2.5rem 2rem 2rem;
            background: var(--gradient-primary);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .alx-card-header::before {
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
        
        .alx-card-header.exams {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        }
        
        .alx-card-header.resources {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
        
        .alx-card-body {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .alx-card-title {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            color: var(--text-dark);
        }
        
        .alx-card-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .alx-card-duration {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-light);
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        
        .alx-card-price {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alx-card-price.free {
            color: var(--success-color);
        }
        
        .alx-card-price.paid {
            color: var(--warning-color);
        }
        
        .alx-card-actions {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .alx-btn {
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }
        
        .alx-btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .alx-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .alx-btn-secondary {
            background: var(--bg-light);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }
        
        .alx-btn-secondary:hover {
            background: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .alx-btn-success {
            background: var(--gradient-success);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .alx-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
        }
        
        .alx-meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .alx-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-light);
            font-weight: 500;
        }
        
        .alx-type-badges {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .alx-type-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .alx-share-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.625rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .alx-share-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1) rotate(15deg);
        }
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

        /* ALX Africa Inspired Card Styles */
        .alx-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .alx-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .alx-card-header {
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
        }
        
        .alx-card-header.exams {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        }
        
        .alx-card-header.resources {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }
        
        .alx-card-body {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .alx-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .alx-card-subtitle {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }
        
        .alx-card-duration {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .alx-card-price {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .alx-card-price.free {
            color: #10b981;
        }
        
        .alx-card-price.paid {
            color: #f59e0b;
        }
        
        .alx-card-actions {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .alx-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .alx-btn-primary {
            background: #6366f1;
            color: white;
        }
        
        .alx-btn-primary:hover {
            background: #4f46e5;
            transform: translateY(-1px);
        }
        
        .alx-btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        
        .alx-btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }
        
        .alx-btn-success {
            background: #10b981;
            color: white;
        }
        
        .alx-btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .alx-meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .alx-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .alx-type-badges {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .alx-type-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .alx-share-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .alx-share-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        /* Enhanced Hero Carousel */
        .hero-carousel {
            height: 600px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .carousel-item.active {
            opacity: 1;
        }

        .carousel-overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%);
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .carousel-text {
            max-width: 800px;
            padding: 2rem;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .carousel-text h2 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            text-shadow: 2px 4px 8px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .carousel-text p {
            font-size: 1.4rem;
            margin-bottom: 2.5rem;
            text-shadow: 1px 2px 4px rgba(0,0,0,0.3);
            opacity: 0.95;
            font-weight: 400;
        }

        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            z-index: 10;
            opacity: 0.8;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 1.2rem;
        }
        
        .carousel-control:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-50%) scale(1.1);
        }
        
        .carousel-control.left {
            left: 2rem;
        }
        
        .carousel-control.right {
            right: 2rem;
        }

        /* Enhanced Section Styles */
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

        /* Enhanced Quick Access Cards */
        .card-shadow-hover {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }

        .card-shadow-hover::before {
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

        .card-shadow-hover:hover::before {
            transform: scaleX(1);
        }

        .card-shadow-hover:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--secondary-color);
        }

        .card-shadow-hover .w-16 {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }

        .card-shadow-hover:hover .w-16 {
            transform: scale(1.1) rotate(5deg);
        }

        /* Enhanced Announcement Section */
        .announcement-scroll-area {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 1rem;
        }
        
        .announcement-scroll-area::-webkit-scrollbar {
            width: 6px;
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
            background: var(--accent-color);
        }

        /* Enhanced Floating Elements */
        .whatsapp-float {
            position: fixed;
            width: 65px;
            height: 65px;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 28px;
            box-shadow: var(--shadow-lg);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .whatsapp-float:hover {
            transform: scale(1.15) rotate(15deg);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, #128c7e 0%, #25d366 100%);
        }

        /* Enhanced Notice Board */
        .notice-board {
            position: fixed;
            top: 50%;
            right: -420px;
            transform: translateY(-50%);
            width: 400px;
            max-height: 650px;
            background: var(--gradient-primary);
            border-radius: 24px 0 0 24px;
            box-shadow: var(--shadow-xl);
            z-index: 1000;
            transition: right 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow: hidden;
            border-left: 4px solid var(--accent-color);
        }

        .notice-board.open {
            right: 0;
        }

        .notice-board-toggle {
            position: absolute;
            left: -60px;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 120px;
            background: var(--gradient-primary);
            border-radius: 16px 0 0 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 24px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--accent-color);
        }

        .notice-board-toggle:hover {
            left: -65px;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-50%) scale(1.05);
        }

        .notice-board-header {
            background: rgba(255, 255, 255, 0.15);
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
        }

        .notice-board-content {
            padding: 24px;
            max-height: 500px;
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
            background: rgba(255, 255, 255, 0.4);
            border-radius: 10px;
        }

        .notice-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }

        .notice-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
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
            line-height: 1.6;
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
            padding: 48px 24px;
            color: rgba(255, 255, 255, 0.9);
        }

        .notice-empty i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.7;
        }

        .notice-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
            box-shadow: var(--shadow-md);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            70% {
                box-shadow: 0 0 0 12px rgba(239, 68, 68, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-carousel {
                height: 500px;
            }
            
            .carousel-text h2 {
                font-size: 2.5rem;
            }
            
            .carousel-text p {
                font-size: 1.2rem;
            }
            
            .carousel-control {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }
            
            .carousel-control.left {
                left: 1rem;
            }
            
            .carousel-control.right {
                right: 1rem;
            }
            
            .notice-board {
                width: 320px;
                right: -340px;
            }
            
            .notice-board.open {
                right: 0;
            }
            
            .notice-board-toggle {
                left: -50px;
                width: 50px;
                height: 80px;
                font-size: 20px;
            }
            
            .whatsapp-float {
                width: 55px;
                height: 55px;
                bottom: 20px;
                right: 20px;
                font-size: 24px;
            }
            
            .alx-card-header {
                padding: 2rem 1.5rem 1.5rem;
            }
            
            .alx-card-body {
                padding: 1.5rem;
            }
            
            .card-shadow-hover {
                padding: 2rem;
            }
            
            /* Enhanced Mobile Styles */
            .section-heading {
                font-size: 2rem !important;
            }
            
            .alx-card {
                margin: 0 1rem 1.5rem 1rem !important;
            }
            
            .alx-card-title {
                font-size: 1.125rem !important;
                line-height: 1.4 !important;
            }
            
            .alx-card-subtitle {
                font-size: 0.875rem !important;
            }
            
            .alx-meta-info {
                font-size: 0.875rem !important;
            }
            
            .alx-btn {
                padding: 0.75rem 1.5rem !important;
                font-size: 0.875rem !important;
            }
            
            .btn-primary {
                padding: 0.875rem 1.5rem !important;
                font-size: 1rem !important;
            }
        }

        @media (max-width: 640px) {
            .hero-carousel {
                height: 400px;
            }
            
            .carousel-text h2 {
                font-size: 2rem;
            }
            
            .carousel-text p {
                font-size: 1rem;
            }
            
            .section-heading {
                font-size: 1.75rem !important;
            }
            
            .alx-card {
                margin: 0 0.5rem 1rem 0.5rem !important;
                border-radius: 0.75rem !important;
            }
            
            .alx-card-header {
                padding: 1.5rem 1rem 1rem !important;
            }
            
            .alx-card-body {
                padding: 1rem !important;
            }
            
            .alx-card-title {
                font-size: 1rem !important;
            }
            
            .alx-btn {
                padding: 0.625rem 1.25rem !important;
                font-size: 0.8125rem !important;
            }
        }

        /* Touch-friendly buttons */
        @media (hover: none) and (pointer: coarse) {
            .alx-btn {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .btn-primary {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .carousel-control {
                min-height: 44px;
                min-width: 44px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    @include('components.public-navbar')

    <!-- Live Streaming Banner -->
    @include('components.live-streaming-banner')

    <!-- Hero Section - ALX-style split layout -->
    <section id="home" class="bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 text-slate-800 py-16 md:py-24">
        <div class="container mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left column: messaging -->
            <div>
                <div class="inline-flex items-center space-x-2 rounded-full bg-white/10 px-4 py-1 mb-6 backdrop-blur-sm border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <p class="text-sm font-medium tracking-wide uppercase">Excellence in digital learning</p>
                </div>

                <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                    Build exam confidence<br>
                    with a modern <span class="text-accent-color">ESA</span> portal.
                </h2>

                <p class="text-lg md:text-xl text-gray-200 max-w-xl mb-8">
                    Practice real exams, access curated notes, and join a learning community
                    designed to help you perform at your best — anytime, on any device.
                </p>

                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <a href="{{ route('public.exams.index') }}"
                       class="btn-primary px-8 py-3 rounded-full text-lg font-semibold shadow-xl hover:scale-105">
                        Start practicing exams
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>

                    <a href="#quick-access"
                       class="inline-flex items-center px-6 py-3 rounded-full border border-white/20 text-sm md:text-base font-semibold bg-white/5 hover:bg-white/10 transition">
                        Go to portals
                        <i class="fas fa-sign-in-alt ml-2 text-sm"></i>
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-6 text-sm text-gray-300">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Secure online assessments</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                        <span>Teacher-created resources</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>Real-time announcements</span>
                    </div>
                </div>
            </div>

            <!-- Right column: stacked summary cards -->
            <div class="grid sm:grid-cols-2 gap-4 lg:gap-6">
                <div class="bg-white text-slate-900 rounded-2xl shadow-2xl p-6 flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-indigo-600 uppercase mb-2">
                            Practice tests
                        </p>
                        <h3 class="text-xl font-bold mb-2">Public skill exams</h3>
                        <p class="text-sm text-slate-600 mb-4">
                            Timed, auto‑marked exams to help you measure progress before the real sitting.
                        </p>
                    </div>
                    <a href="{{ route('public.exams.index') }}"
                       class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                        Browse exams
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>

                <div class="bg-slate-100/70 border border-slate-300 rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-emerald-400 uppercase mb-2">
                            Learning hub
                        </p>
                        <h3 class="text-xl font-bold mb-2">Notes & videos</h3>
                        <p class="text-sm text-slate-200 mb-4">
                            Curriculum‑aligned notes, documents, and videos from experienced ESA teachers.
                        </p>
                    </div>
                    <a href="{{ route('public.resources.index') }}"
                       class="inline-flex items-center text-sm font-semibold text-emerald-300 hover:text-emerald-200">
                        View resources
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>

                <div class="bg-slate-100/60 border border-slate-300 rounded-2xl p-6 flex flex-col justify-between sm:col-span-2">
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-amber-300 uppercase mb-2">
                            Portals
                        </p>
                        <h3 class="text-xl font-bold mb-2">Admin, staff, teacher & student access</h3>
                        <p class="text-sm text-slate-600 mb-4">
                            A single entry point for managing classes, results, subscriptions, and student journeys.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="#quick-access"
                           class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 text-xs font-semibold hover:bg-white/15">
                            Explore portals
                            <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                        </a>
                        <a href="{{ route('students.create') }}"
                           class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-500 text-xs font-semibold text-white hover:bg-emerald-600">
                            Join as a student
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
                    <div class="alx-card">
                        <!-- Exam Header -->
                        <div class="alx-card-header exams">
                            <button onclick="shareExam({{ $exam->id }}, '{{ Str::limit($exam->title, 30) }}')" 
                                    class="alx-share-btn"
                                    title="Share this exam">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <h3 class="alx-card-title">{{ Str::limit($exam->title, 50) }}</h3>
                            @if($exam->courses->count() > 0)
                                <div class="alx-card-subtitle">
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    {{ $exam->courses->pluck('name')->implode(', ') }}
                                </div>
                            @endif
                        </div>

                        <!-- Exam Body -->
                        <div class="alx-card-body">
                            <!-- Duration Info -->
                            <div class="alx-card-duration">
                                <i class="fas fa-clock mr-2"></i>
                                {{ $exam->duration ?? '60' }} minutes
                            </div>

                            <!-- Price Display -->
                            <div class="alx-card-price {{ $exam->requiresPayment() ? 'paid' : 'free' }}">
                                @if($exam->requiresPayment())
                                    RWF {{ number_format($exam->public_price, 0) }}
                                @else
                                    FREE
                                @endif
                            </div>

                            <!-- Meta Info -->
                            <div class="alx-meta-info">
                                <div class="alx-meta-item">
                                    <i class="fas fa-question-circle text-purple-600"></i>
                                    <span>{{ $exam->questions->count() }} Questions</span>
                                </div>
                                <div class="alx-meta-item">
                                    <i class="fas fa-trophy text-yellow-500"></i>
                                    <span>Certificate</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="alx-card-actions">
                                <a href="{{ route('public.exams.show', $exam) }}" 
                                   class="alx-btn alx-btn-secondary">
                                    <i class="fas fa-eye"></i>View Details
                                </a>
                                
                                @if($exam->isAccessible())
                                    <a href="{{ route('public.exams.start', $exam) }}" 
                                       class="alx-btn alx-btn-primary">
                                        <i class="fas fa-play"></i>Start Practice
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
                    <div class="alx-card">
                        <!-- Resource Header -->
                        <div class="alx-card-header resources">
                            <button onclick="shareResource({{ $resource->id }}, '{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 30) }}')" 
                                    class="alx-share-btn"
                                    title="Share this resource">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <h3 class="alx-card-title">{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Educational Resource', 40) }}</h3>
                            <div class="alx-card-subtitle">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                {{ $resource->course->course_name ?? 'General Course' }}
                            </div>
                        </div>

                        <!-- Resource Body -->
                        <div class="alx-card-body">
                            <!-- Teacher & Branch Info -->
                            <div class="alx-meta-info">
                                <div class="alx-meta-item">
                                    <i class="fas fa-user-tie text-blue-600"></i>
                                    <span>{{ $resource->teacher->teacher_name ?? 'Expert Teacher' }}</span>
                                </div>
                                <div class="alx-meta-item">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                    <span>{{ $resource->branch->name ?? 'Main Campus' }}</span>
                                </div>
                            </div>

                            <!-- Resource Type Badges -->
                            <div class="alx-type-badges">
                                @if($resource->document_blob)
                                    <span class="alx-type-badge bg-blue-100 text-blue-800">
                                        <i class="fas fa-file-alt mr-1"></i>Document
                                    </span>
                                @endif
                                @if($resource->video_blob)
                                    <span class="alx-type-badge bg-red-100 text-red-800">
                                        <i class="fas fa-video mr-1"></i>Video
                                    </span>
                                @endif
                                @if($resource->youtube_link)
                                    <span class="alx-type-badge bg-red-100 text-red-800">
                                        <i class="fab fa-youtube mr-1"></i>YouTube
                                    </span>
                                @endif
                            </div>

                            <!-- Price Display -->
                            <div class="alx-card-price {{ $resource->is_free ? 'free' : 'paid' }}">
                                @if($resource->is_free)
                                    <i class="fas fa-gift mr-2"></i>FREE
                                @else
                                    <i class="fas fa-tag mr-2"></i>RWF {{ number_format($resource->price, 0) }}
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="alx-card-actions">
                                <a href="{{ route('public.resources.show', $resource->id) }}" 
                                   class="alx-btn alx-btn-secondary">
                                    <i class="fas fa-eye"></i>View Details
                                </a>
                                
                                @if($resource->document_blob || $resource->video_blob)
                                    <a href="{{ $resource->is_free ? route('public.resources.download', $resource->id) : route('public.resources.payment', $resource->id) }}" 
                                       class="alx-btn alx-btn-success">
                                        <i class="fas fa-{{ $resource->is_free ? 'download' : 'shopping-cart' }} mr-2"></i>{{ $resource->is_free ? 'Download Now' : 'Purchase Access' }}
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

    <script>
        // Enhanced Carousel JavaScript
        let slideIndex = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;
        let autoSlideInterval;
        let isTransitioning = false;

        function showSlides(n) {
            if (isTransitioning) return;
            isTransitioning = true;
            
            // Reset index if out of bounds
            if (n >= totalSlides) { slideIndex = 0; }
            if (n < 0) { slideIndex = totalSlides - 1; }

            // Hide all slides with fade effect
            slides.forEach((slide, index) => {
                slide.classList.remove('active');
                if (index === slideIndex) {
                    setTimeout(() => {
                        slide.classList.add('active');
                        isTransitioning = false;
                    }, 50);
                }
            });

            // Reset carousel text animation
            const activeSlide = slides[slideIndex];
            const carouselText = activeSlide.querySelector('.carousel-text');
            if (carouselText) {
                carouselText.style.animation = 'none';
                setTimeout(() => {
                    carouselText.style.animation = 'fadeInUp 1s ease-out';
                }, 100);
            }
        }

        function plusSlides(n) {
            resetAutoSlide();
            slideIndex += n;
            showSlides(slideIndex);
        }

        function autoSlides() {
            slideIndex++;
            showSlides(slideIndex);
        }

        function startAutoSlide() {
            autoSlideInterval = setInterval(autoSlides, 6000);
        }

        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            startAutoSlide();
        }

        // Enhanced keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                plusSlides(-1);
            } else if (e.key === 'ArrowRight') {
                plusSlides(1);
            }
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        const carousel = document.querySelector('.hero-carousel');
        if (carousel) {
            carousel.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });
            
            carousel.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });
        }
        
        function handleSwipe() {
            if (touchEndX < touchStartX - 50) {
                plusSlides(1); // Swipe left - next slide
            }
            if (touchEndX > touchStartX + 50) {
                plusSlides(-1); // Swipe right - previous slide
            }
        }

        // Initialize carousel
        document.addEventListener('DOMContentLoaded', () => {
            showSlides(slideIndex);
            startAutoSlide();
            
            // Pause on hover
            const carousel = document.querySelector('.hero-carousel');
            if (carousel) {
                carousel.addEventListener('mouseenter', () => {
                    clearInterval(autoSlideInterval);
                });
                
                carousel.addEventListener('mouseleave', () => {
                    startAutoSlide();
                });
            }
        });

        // Enhanced Share Functions
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
                    showNotification('Link copied to clipboard!', 'success');
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
                    showNotification('Link copied to clipboard!', 'success');
                }).catch(() => {
                    // Last resort - open in new window
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                });
            }
        }

        // Enhanced Notification System
        function showNotification(message, type = 'success') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification-toast');
            existingNotifications.forEach(notif => notif.remove());
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification-toast fixed top-4 right-4 px-6 py-4 rounded-xl shadow-xl z-50 transition-all duration-500 flex items-center gap-3`;
            
            // Set colors based on type
            const colors = {
                success: 'bg-green-600 text-white',
                error: 'bg-red-600 text-white',
                warning: 'bg-yellow-600 text-white',
                info: 'bg-blue-600 text-white'
            };
            
            notification.className += ` ${colors[type] || colors.success}`;
            
            // Add icon
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            
            notification.innerHTML = `
                <i class="${icons[type] || icons.success} text-xl"></i>
                <span class="font-medium">${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Remove after 4 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(400px)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 4000);
        }

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll animations for sections
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });
    </script>
 

</body>
</html>