@extends('layouts.public')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <i class="fas fa-file-alt text-primary mr-3"></i>Babyeyi Documents
        </h1>
        <p class="text-gray-600 text-lg">
            Term registration documents and course requirements for all programs
        </p>
    </div>

    @if ($babyeyis->count() > 0)
        <!-- Documents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($babyeyis as $babyeyi)
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-primary to-primary-dark text-white p-4">
                        <h3 class="text-lg font-bold flex items-center">
                            <i class="fas fa-scroll mr-2"></i>
                            {{ $babyeyi->academicYear->year ?? 'N/A' }} - {{ $babyeyi->term->name ?? 'N/A' }}
                        </h3>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <!-- Course Info -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-gray-600 text-sm font-semibold uppercase mb-1">Course</p>
                            <p class="text-gray-900 font-semibold">
                                @if ($babyeyi->course)
                                    {{ $babyeyi->course->name }}
                                @elseif ($babyeyi->course_type)
                                    {{ $babyeyi->course_type }}
                                @else
                                    Multiple Courses
                                @endif
                            </p>
                        </div>

                        <!-- Reference Number -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-gray-600 text-sm font-semibold uppercase mb-1">Reference</p>
                            <code class="bg-gray-100 text-gray-900 text-sm px-2 py-1 rounded">{{ $babyeyi->reference_number }}</code>
                        </div>

                        <!-- Term Duration -->
                        @if ($babyeyi->term)
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-gray-600 text-sm font-semibold uppercase mb-1">
                                <i class="fas fa-calendar mr-1"></i>Term Duration
                            </p>
                            <p class="text-gray-900">
                                {{ $babyeyi->term->start_date->format('M d, Y') }} - 
                                {{ $babyeyi->term->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        @endif

                        <!-- Trip Info -->
                        @if ($babyeyi->trip_known && $babyeyi->trip_place)
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-gray-600 text-sm font-semibold uppercase mb-1">
                                <i class="fas fa-bus mr-1"></i>Trip
                            </p>
                            <p class="text-gray-900 mb-1">Destination: <strong>{{ $babyeyi->trip_place }}</strong></p>
                            @if ($babyeyi->trip_fees)
                                <p class="text-gray-900">Fee: <strong>RWF {{ number_format($babyeyi->trip_fees, 2) }}</strong></p>
                            @endif
                        </div>
                        @endif

                        <!-- Created Date -->
                        <p class="text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            Created: {{ $babyeyi->created_at->format('M d, Y') }}
                        </p>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="bg-gray-50 px-6 py-4 flex gap-2">
                        <a href="{{ route('public.babyeyi.show', $babyeyi) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold shadow-md hover:shadow-lg">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </a>
                        <a href="{{ route('public.babyeyi.download', $babyeyi) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition-colors text-sm font-semibold shadow-md hover:shadow-lg">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $babyeyis->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-8 text-center">
            <div class="text-4xl mb-4">
                <i class="fas fa-inbox text-blue-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Documents Available</h3>
            <p class="text-gray-600">
                Babyeyi documents will appear here once they are published by the administration.
            </p>
        </div>
    @endif
</div>
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
@endsection
