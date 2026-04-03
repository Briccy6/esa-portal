@extends('layouts.public')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-scroll text-primary mr-3"></i>Babyeyi Document
                </h1>
                <p class="text-gray-600">
                    <strong>Reference:</strong> <code class="bg-gray-100 px-2 py-1 rounded">{{ $babyeyi->reference_number }}</code>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('public.babyeyi.download', $babyeyi) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <i class="fas fa-download mr-2"></i> Download PDF
                </a>
                <a href="{{ route('public.babyeyi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Document Information Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-primary">
            <p class="text-gray-600 text-sm font-semibold">ACADEMIC YEAR</p>
            <p class="text-lg font-bold text-gray-900">{{ $babyeyi->academicYear->year ?? 'N/A' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-secondary">
            <p class="text-gray-600 text-sm font-semibold">TERM</p>
            <p class="text-lg font-bold text-gray-900">{{ $babyeyi->term->name ?? 'N/A' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-info">
            <p class="text-gray-600 text-sm font-semibold">COURSE</p>
            <p class="text-lg font-bold text-gray-900">
                @if ($babyeyi->course)
                    {{ $babyeyi->course->name }}
                @elseif ($babyeyi->course_type)
                    {{ $babyeyi->course_type }}
                @else
                    Multiple
                @endif
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-success">
            <p class="text-gray-600 text-sm font-semibold">GENERATED DATE</p>
            <p class="text-lg font-bold text-gray-900">{{ $babyeyi->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <!-- Main Document Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- School Header in Document -->
        @if ($schoolInfo)
        <div class="bg-gray-50 p-8 text-center border-b-2 border-gray-300">
            @if ($schoolInfo->logo_url)
                <img src="{{ $schoolInfo->logo_url }}" alt="School Logo" class="h-20 mx-auto mb-4 object-contain">
            @endif
            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $schoolInfo->school_name ?? 'School Name' }}</h2>
            @if ($schoolInfo->address)
                <p class="text-gray-600 mb-1">{{ $schoolInfo->address }}</p>
            @endif
            <div class="space-y-0.5">
                @if ($schoolInfo->phone)
                    <p class="text-gray-600 text-sm"><i class="fas fa-phone mr-2"></i>Tel: {{ $schoolInfo->phone }}</p>
                @endif
                @if ($schoolInfo->email)
                    <p class="text-gray-600 text-sm"><i class="fas fa-envelope mr-2"></i>Email: {{ $schoolInfo->email }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Document Content -->
        <div class="p-8 md:p-12">
            <h3 class="text-center text-3xl font-bold text-gray-900 mb-8 pb-4 border-b-2 border-gray-200">BABYEYI</h3>
            
            <!-- Document Metadata Box -->
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-8 rounded">
                <p class="text-sm text-gray-700">
                    <strong class="text-blue-700">Reference:</strong> {{ $babyeyi->reference_number }} | 
                    <strong class="text-blue-700">Generated:</strong> {{ $babyeyi->created_at->format('M d, Y H:i') }} | 
                    <strong class="text-blue-700">Term:</strong> {{ $babyeyi->term->name ?? 'N/A' }}
                </p>
            </div>
            
            <div class="document-content prose prose-lg max-w-none mb-12">
                {!! $babyeyi->content !!}
            </div>

            <!-- Signature Section -->
            <div class="mt-12 pt-8 border-t-2 border-gray-300">
                <h4 class="text-center font-bold text-gray-900 mb-8">OFFICIAL APPROVAL & SIGNATURE</h4>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-16 md:max-w-md md:mx-auto">
                    <!-- School Stamp & Principal Signature -->
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-4 h-32 border-2 border-gray-400 rounded bg-gray-50">
                            @if ($schoolInfo && $schoolInfo->stamp_signature_url)
                                <img src="{{ $schoolInfo->stamp_signature_url }}" alt="School Stamp" class="max-h-28 max-w-28 object-contain">
                            @elseif ($schoolInfo && $schoolInfo->principal_signature_url)
                                <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal Signature" class="max-h-28 max-w-28 object-contain">
                            @else
                                <div class="text-center text-gray-400">
                                    <i class="fas fa-stamp text-4xl mb-2"></i>
                                    <p class="text-xs">School Stamp<br>& Signature</p>
                                </div>
                            @endif
                        </div>
                        <p class="font-bold text-gray-900 mb-1">Head Teacher/Principal</p>
                        <p class="text-sm text-gray-600">{{ $headTeacher->first_name ?? 'Signature Area' }} {{ $headTeacher->last_name ?? '' }}</p>
                        <p class="text-xs text-gray-500 mt-2">Date: _______________</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <!-- Term Duration -->
        @if ($babyeyi->term)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h4 class="font-bold text-gray-900 mb-3">
                <i class="fas fa-calendar text-primary mr-2"></i>Term Duration
            </h4>
            <p class="text-gray-600">
                {{ $babyeyi->term->start_date->format('F d, Y') }} to {{ $babyeyi->term->end_date->format('F d, Y') }}
            </p>
        </div>
        @endif

        <!-- Trip Information -->
        @if ($babyeyi->trip_known && $babyeyi->trip_place)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h4 class="font-bold text-gray-900 mb-3">
                <i class="fas fa-bus text-primary mr-2"></i>{{ $babyeyi->trip_place }}
            </h4>
            @if ($babyeyi->trip_fees)
                <p class="text-gray-600">
                    <strong>Trip Fee:</strong> RWF {{ number_format($babyeyi->trip_fees, 2) }}
                </p>
            @endif
        </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col md:flex-row gap-4 mt-8">
        <a href="{{ route('public.babyeyi.download', $babyeyi) }}" class="flex-1 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-center font-semibold">
            <i class="fas fa-download mr-2"></i>Download as PDF
        </a>
        <a href="{{ route('public.babyeyi.index') }}" class="flex-1 py-3 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition-colors text-center font-semibold">
            <i class="fas fa-list mr-2"></i>View All Documents
        </a>
    </div>

    <!-- Document Footer -->
    <div class="mt-12 pt-6 border-t border-gray-300 text-center">
        <p class="text-xs text-gray-500 mb-1">This document was generated by the {{ $schoolInfo->school_name ?? 'School' }} Student Management System</p>
        <p class="text-xs text-gray-500">Reference: {{ $babyeyi->reference_number }} | Generated: {{ now()->format('M d, Y H:i') }}</p>
    </div>
</div>

<style>
.document-content {
    font-family: 'Calibri', 'Arial', sans-serif;
    line-height: 1.7;
    color: #333;
    font-size: 14px;
}

.document-content h1 {
    font-size: 24px;
    margin: 20px 0 10px 0;
    color: #1a1a1a;
}

.document-content h2 {
    font-size: 18px;
    margin: 15px 0 8px 0;
    color: #1a1a1a;
}

.document-content h3 {
    font-size: 16px;
    margin: 12px 0 6px 0;
    color: #333;
}

.document-content p {
    margin-bottom: 8px;
}

.document-content ul,
.document-content ol {
    margin-left: 30px;
    margin-bottom: 10px;
}

.document-content li {
    margin-bottom: 5px;
}

.document-content table {
    margin: 15px 0;
    width: 100%;
    border-collapse: collapse;
}

.document-content table td,
.document-content table th {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}

.document-content table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
@endsection
