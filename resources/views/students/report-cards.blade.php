@extends('students.app')

@section('title', 'Report Cards')

@section('content')
<style>
.report-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.countdown-timer {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 2rem;
}

.countdown-timer .countdown-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.countdown-timer .countdown-label {
    font-size: 1rem;
    opacity: 0.9;
}

.report-card-item {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.report-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.report-card-header-info {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e0e0e0;
}

.report-card-body {
    padding: 1.5rem;
}

.report-card-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2563eb;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.alert-fee-block {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.alert-fee-block h5 {
    margin-bottom: 1rem;
}

.fee-amount {
    font-size: 2rem;
    font-weight: bold;
    margin: 1rem 0;
}

.download-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.download-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="report-card-header">
        <h1 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>My Report Cards
        </h1>
        <p class="mb-0 mt-2">View and download your approved report cards</p>
    </div>

    @if($hasOutstandingFees)
        <!-- Fee Block Alert -->
        <div class="alert-fee-block">
            <h5><i class="bi bi-exclamation-triangle me-2"></i>Outstanding Fees</h5>
            <p>You have outstanding fees that must be paid before accessing your report cards.</p>
            <div class="fee-amount">
                {{ number_format($outstanding, 2) }} Rwf
            </div>
            <p class="mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Please pay the remaining amount at our office to access your report cards.
            </p>
        </div>
    @endif

    @if($reportCardReleaseDate && !$releaseDatePassed)
        <!-- Countdown Timer -->
        <div class="countdown-timer">
            <h5><i class="bi bi-clock me-2"></i>Report Cards Release Countdown</h5>
            <div class="countdown-number">
                @if($countdown)
                    @if($countdown->days > 0)
                        {{ $countdown->days }} days, {{ $countdown->h }} hours, {{ $countdown->i }} minutes
                    @elseif($countdown->h > 0)
                        {{ $countdown->h }} hours, {{ $countdown->i }} minutes
                    @else
                        {{ $countdown->i }} minutes, {{ $countdown->s }} seconds
                    @endif
                @else
                    Calculating...
                @endif
            </div>
            <div class="countdown-label">
                Until release on {{ \Carbon\Carbon::parse($reportCardReleaseDate)->format('d M Y H:i') }}
            </div>
        </div>
    @endif

    <!-- Report Cards List -->
    @if($reportCards->count() > 0)
        <div class="row">
            @foreach($reportCards as $reportCard)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="report-card-item">
                        <div class="report-card-header-info">
                            <h6 class="mb-1">{{ $reportCard->academicYear->year ?? 'Academic Year' }}</h6>
                            <p class="mb-0 text-muted">{{ $reportCard->term->name ?? 'Term' }} - {{ $reportCard->course->course_name ?? 'Course' }}</p>
                        </div>
                        <div class="report-card-body">
                            <div class="report-card-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $reportCard->average_marks ? number_format($reportCard->average_marks, 1) : 'N/A' }}</div>
                                    <div class="stat-label">Average</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $reportCard->grade ?? 'N/A' }}</div>
                                    <div class="stat-label">Grade</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $reportCard->position_display }}</div>
                                    <div class="stat-label">Position</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    Approved: {{ $reportCard->approved_at ? $reportCard->approved_at->format('d M Y H:i') : 'N/A' }}
                                </small>
                            </div>
                            <div class="mt-3">
                                @if($hasOutstandingFees || ($reportCardReleaseDate && !$releaseDatePassed))
                                    <button class="download-btn w-100" disabled>
                                        <i class="bi bi-lock me-2"></i>
                                        @if($hasOutstandingFees)
                                            Pay Fees to Download
                                        @else
                                            Not Yet Available
                                        @endif
                                    </button>
                                @else
                                    <a href="{{ route('student.report-cards.download', $reportCard->id) }}" 
                                       class="btn download-btn w-100">
                                        <i class="bi bi-download me-2"></i>Download PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="bi bi-file-earmark-text"></i>
            <h4>No Report Cards Available</h4>
            <p class="text-muted">
                @if($hasOutstandingFees)
                    Your report cards will be available once all outstanding fees are paid.
                @elseif($reportCardReleaseDate && !$releaseDatePassed)
                    Report cards will be available after the release date.
                @else
                    No approved report cards have been generated for you yet.
                @endif
            </p>
        </div>
    @endif
</div>

<script>
// Update countdown every second
@if($reportCardReleaseDate && !$releaseDatePassed && $countdown)
    function updateCountdown() {
        const releaseDate = new Date('{{ \Carbon\Carbon::parse($reportCardReleaseDate)->format("Y-m-d\\TH:i:s") }}');
        const now = new Date();
        const rwandaTime = new Date(now.toLocaleString("en-US", {timeZone: "Africa/Kigali"}));
        
        const diff = releaseDate - rwandaTime;
        
        if (diff > 0) {
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            let countdownText = '';
            if (days > 0) {
                countdownText = `${days} days, ${hours} hours, ${minutes} minutes`;
            } else if (hours > 0) {
                countdownText = `${hours} hours, ${minutes} minutes`;
            } else {
                countdownText = `${minutes} minutes, ${seconds} seconds`;
            }
            
            const countdownElement = document.querySelector('.countdown-number');
            if (countdownElement) {
                countdownElement.textContent = countdownText;
            }
        } else {
            // Countdown expired, reload page
            window.location.reload();
        }
    }
    
    // Update countdown immediately and then every second
    updateCountdown();
    setInterval(updateCountdown, 1000);
@endif
</script>
@endsection
