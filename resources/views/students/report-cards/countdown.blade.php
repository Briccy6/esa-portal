@extends('students.app')

@section('title', 'Report Card Countdown')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Report Card Coming Soon</h1>
            <p class="text-gray-600">Your report card will be available at the scheduled time</p>
        </div>

        <!-- Main Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg shadow-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Left: Release Info -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Report Card Release Date & Time</h2>
                    <div class="bg-white p-4 rounded-lg border border-blue-200 mb-6">
                        <p class="text-gray-600 text-sm mb-2">Scheduled Release:</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $availableDate->format('F d, Y') }}
                        </p>
                        <p class="text-lg font-semibold text-blue-700">
                            {{ $availableDate->format('H:i A') }}
                        </p>
                    </div>

                    <div class="space-y-2 text-sm text-gray-700">
                        <p><strong>Course:</strong> {{ $reportCard->course->name ?? 'N/A' }}</p>
                        <p><strong>Academic Year:</strong> {{ $reportCard->academicYear->year ?? 'N/A' }}</p>
                        <p><strong>Term:</strong> {{ $reportCard->term->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Right: Countdown Timer -->
                <div class="flex justify-center">
                    <div class="text-center p-8 bg-white rounded-lg border-2 border-blue-300 shadow-md">
                        <p class="text-sm text-gray-600 mb-4">Time Remaining</p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-white countdown-days">{{ $countdownData['days'] ?? 0 }}</div>
                                <div class="text-xs text-blue-100 mt-1">DAYS</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-600 to-purple-700 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-white countdown-hours">{{ $countdownData['hours'] ?? 0 }}</div>
                                <div class="text-xs text-purple-100 mt-1">HOURS</div>
                            </div>
                            <div class="bg-gradient-to-br from-pink-600 to-pink-700 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-white countdown-minutes">{{ $countdownData['minutes'] ?? 0 }}</div>
                                <div class="text-xs text-pink-100 mt-1">MINUTES</div>
                            </div>
                            <div class="bg-gradient-to-br from-red-600 to-red-700 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-white countdown-seconds">{{ $countdownData['seconds'] ?? 0 }}</div>
                                <div class="text-xs text-red-100 mt-1">SECONDS</div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500">Timer updates every second</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-600 text-xl mt-1"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Fees Status</h3>
                        <p class="text-sm text-gray-600">All fees are paid ✓</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-calendar-check text-blue-600 text-xl mt-1"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Schedule</h3>
                        <p class="text-sm text-gray-600">Waiting for release date</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lock-open text-purple-600 text-xl mt-1"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Access</h3>
                        <p class="text-sm text-gray-600">Unlocking soon...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notice -->
        <div class="mt-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex gap-4">
                <i class="fas fa-info-circle text-yellow-600 text-xl flex-shrink-0 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Important Notice</h3>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>✓ Your report card will be automatically available at the scheduled time</li>
                        <li>✓ You will receive an email notification when it's ready</li>
                        <li>✓ No action is required from your side right now</li>
                        <li>✓ Refresh this page to see the updated status</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetIso = '{{ $availableDate->toIso8601String() }}';
    const target = new Date(targetIso).getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const diff = target - now;
        
        if (diff <= 0) {
            // Countdown finished, refresh page
            location.reload();
            return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        document.querySelector('.countdown-days').textContent = String(days).padStart(2, '0');
        document.querySelector('.countdown-hours').textContent = String(hours).padStart(2, '0');
        document.querySelector('.countdown-minutes').textContent = String(minutes).padStart(2, '0');
        document.querySelector('.countdown-seconds').textContent = String(seconds).padStart(2, '0');
    }
    
    // Initial update
    updateCountdown();
    
    // Update every second
    setInterval(updateCountdown, 1000);
});
</script>

<style>
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .container > * {
        animation: slideIn 0.6s ease-out;
    }
</style>
@endsection
