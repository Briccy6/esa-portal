@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            <i class="fas fa-book text-green-500 mr-3"></i>Learning Resources
        </h1>
        <p class="text-gray-600">Access our comprehensive collection of educational materials and resources.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-video text-blue-500 text-3xl"></i>
                <h3 class="text-xl font-semibold text-gray-800 ml-3">Video Tutorials</h3>
            </div>
            <p class="text-gray-700">Access our video lessons covering key concepts and topics.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                <h3 class="text-xl font-semibold text-gray-800 ml-3">Study Materials</h3>
            </div>
            <p class="text-gray-700">Download comprehensive study guides and reference materials.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-microscope text-purple-500 text-3xl"></i>
                <h3 class="text-xl font-semibold text-gray-800 ml-3">Practicals & Labs</h3>
            </div>
            <p class="text-gray-700">Explore hands-on experiments and practical exercises.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-book-open text-yellow-500 text-3xl"></i>
                <h3 class="text-xl font-semibold text-gray-800 ml-3">E-Books</h3>
            </div>
            <p class="text-gray-700">Read our collection of digital textbooks and reference books.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-link text-indigo-500 text-3xl"></i>
                <h3 class="text-xl font-semibold text-gray-800 ml-3">External Links</h3>
            </div>
            <p class="text-gray-700">Curated links to quality educational websites and resources.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-laptop text-cyan-500 text-3xl"></i>
                <h3 class="text-xl font-semibold text-gray-800 ml-3">Online Courses</h3>
            </div>
            <p class="text-gray-700">Enroll in our online courses and certification programs.</p>
        </div>
    </div>
</div>

