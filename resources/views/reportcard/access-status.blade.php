@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Report Card Access Status</h1>
        <p class="text-gray-600 mb-8">Check the access status of each report card below</p>

        <div class="space-y-4">
            @forelse($reportCards as $item)
                @php
                    $reportCard = $item['reportCard'];
                    $status = $item['status'];
                    $message = $item['message'];
                @endphp

                <div class="bg-white border-l-4 rounded-lg shadow p-6 flex items-center justify-between
                    @if($status === 'blocked_fees') border-red-500 hover:shadow-lg
                    @elseif($status === 'countdown') border-yellow-500 hover:shadow-lg
                    @else border-green-500 hover:shadow-lg
                    @endif
                    transition duration-300
                ">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $reportCard->course->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Academic Year:</strong> {{ $reportCard->academicYear->year ?? 'N/A' }} | 
                            <strong>Term:</strong> {{ $reportCard->term->name ?? 'N/A' }}
                        </p>
                        <p class="text-sm
                            @if($status === 'blocked_fees') text-red-700 font-semibold
                            @elseif($status === 'countdown') text-yellow-700 font-semibold
                            @else text-green-700 font-semibold
                            @endif
                        ">
                            @if($status === 'blocked_fees')
                                <i class="fas fa-lock"></i> {{ $message }}
                            @elseif($status === 'countdown')
                                <i class="fas fa-hourglass-half"></i> {{ $message }}
                            @else
                                <i class="fas fa-check-circle"></i> {{ $message }}
                            @endif
                        </p>
                    </div>

                    <a 
                        href="{{ route('student.report-cards.show', $reportCard->id) }}"
                        class="ml-6 px-6 py-2 rounded-lg font-semibold transition duration-300
                            @if($status === 'blocked_fees') bg-red-100 text-red-700 hover:bg-red-200
                            @elseif($status === 'countdown') bg-yellow-100 text-yellow-700 hover:bg-yellow-200
                            @else bg-green-100 text-green-700 hover:bg-green-200
                            @endif
                        "
                    >
                        @if($status === 'blocked_fees')
                            Pay Now
                        @elseif($status === 'countdown')
                            View Countdown
                        @else
                            View Report
                        @endif
                    </a>
                </div>
            @empty
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-12 text-center">
                    <i class="fas fa-file-pdf text-5xl text-gray-400 mb-4"></i>
                    <p class="text-lg text-gray-600">No report cards available yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Legend -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-lock text-red-600 text-2xl"></i>
                    <h3 class="font-semibold text-gray-900">Blocked</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Outstanding fees must be paid before accessing the report card.
                </p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
                    <h3 class="font-semibold text-gray-900">Pending</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Report card will be available on the scheduled release date and time.
                </p>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    <h3 class="font-semibold text-gray-900">Available</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Report card is ready to view. Download or print at any time.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .space-y-4 > * {
        animation: fadeIn 0.4s ease-out;
    }

    .space-y-4 > :nth-child(1) { animation-delay: 0.1s; }
    .space-y-4 > :nth-child(2) { animation-delay: 0.2s; }
    .space-y-4 > :nth-child(3) { animation-delay: 0.3s; }
</style>
@endsection
