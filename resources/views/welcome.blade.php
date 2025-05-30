<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-learning Portal</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/esa.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <!-- Alpine.js for slider -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
<body class="relative min-h-screen flex items-center justify-center text-gray-100">

    <!-- Background image slider -->
    <div x-data="bgSlider()" x-init="init()" class="fixed inset-0 -z-10">
        <template x-for="(slide, index) in slides" :key="index">
            <div 
                x-show="current === index"
                x-transition:enter="transition-opacity duration-1000"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-1000"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-cover bg-center"
                :style="`background-image: url('${slide}')`"
            ></div>
        </template>
    </div>

    <!-- Main content -->
    <div class="max-w-6xl mx-auto p-8 bg-white bg-opacity-10 rounded-xl shadow-lg backdrop-blur-sm grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Slider -->
        <div x-data="contentSlider()" x-init="init()" class="md:col-span-2 relative overflow-hidden rounded-lg shadow-lg bg-indigo-900 bg-opacity-50">
            <template x-for="(slide, index) in slides" :key="index">
                <div
                    x-show="current === index"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full"
                    class="absolute inset-0 flex flex-col items-center justify-center px-10 py-16 text-center text-white"
                    style="background-size: cover; background-position: center;"
                    :style="`background-image: url('${slide.image}')`"
                >
                    <h2 class="text-4xl font-extrabold mb-4" x-text="slide.title"></h2>
                    <p class="text-lg max-w-xl" x-text="slide.description"></p>
                </div>
            </template>

            <!-- Controls -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-3">
                <template x-for="(slide, index) in slides" :key="index">
                    <button
                        @click="goToSlide(index)"
                        :class="{'bg-indigo-500': current === index, 'bg-indigo-300': current !== index}"
                        class="w-4 h-4 rounded-full focus:outline-none"
                        aria-label="Slide indicator"
                    ></button>
                </template>
            </div>
        </div>

        <!-- Notice Board -->
        <div class="bg-indigo-800 bg-opacity-70 rounded-lg p-6 flex flex-col">
            <h3 class="text-2xl font-bold mb-4 border-b border-indigo-400 pb-2">Notice Board</h3>
            <ul class="space-y-3 flex-1 overflow-y-auto">
                <li class="bg-indigo-700 bg-opacity-50 rounded p-3 shadow-inner">
                    <strong>Exam Schedule Released</strong>
                    <p class="text-sm mt-1">The final exams will start from June 10, 2025.</p>
                </li>
                <li class="bg-indigo-700 bg-opacity-50 rounded p-3 shadow-inner">
                    <strong>School Closed on May 1</strong>
                    <p class="text-sm mt-1">In observance of Labor Day, the school will be closed.</p>
                </li>
                <li class="bg-indigo-700 bg-opacity-50 rounded p-3 shadow-inner">
                    <strong>New Course: Advanced AI</strong>
                    <p class="text-sm mt-1">Enrollments for the new AI course are now open.</p>
                </li>
                <li class="bg-indigo-700 bg-opacity-50 rounded p-3 shadow-inner">
                    <strong>Library Renovation</strong>
                    <p class="text-sm mt-1">Library will be closed for renovations from May 5 to May 15.</p>
                </li>
            </ul>

            <a href="{{ route('admin.login') }}" 
               class="mt-6 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-md font-semibold shadow-md transition duration-300 text-center text-white">
                Admin Login
            </a>

            <a href="{{ route('dashboard') }}" 
               class="mt-4 px-6 py-3 border-2 border-indigo-600 hover:bg-indigo-600 hover:text-white rounded-md font-semibold shadow-md transition duration-300 text-center text-white">
                User Dashboard
            </a>
        </div>

    </div>

    <footer class="mt-12 text-center text-indigo-200 text-sm">
        &copy; {{ date('Y') }} YourApp. All rights reserved.
    </footer>

    <script>
        function bgSlider() {
            return {
                current: 0,
                slides: [
                    "{{ asset('images/slide1.jpg') }}",
                    "{{ asset('images/slide2.jpg') }}",
                    "{{ asset('images/slide3.jpg') }}"
                ],
                init() {
                    setInterval(() => {
                        this.next();
                    }, 5000); // 5 seconds per slide
                },
                next() {
                    this.current = (this.current + 1) % this.slides.length;
                }
            }
        }

        function contentSlider() {
            return {
                current: 0,
                slides: [
                    {
                        title: "Welcome to E-Learning Portal",
                        description: "A reliable and modern platform built to simplify your workflow and empower your business.",
                        image: "{{ asset('images/slide1.jpg') }}"
                    },
                    {
                        title: "Explore New Courses",
                        description: "We offer a wide range of courses tailored for your success.",
                        image: "{{ asset('images/slide2.jpg') }}"
                    },
                    {
                        title: "Join Our Community",
                        description: "Connect with learners and experts around the globe.",
                        image: "{{ asset('images/slide3.jpg') }}"
                    }
                ],
                init() {
                    setInterval(() => {
                        this.next();
                    }, 3000);
                },
                next() {
                    this.current = (this.current + 1) % this.slides.length;
                },
                goToSlide(index) {
                    this.current = index;
                }
            };
        }
    </script>

</body>
</html>
