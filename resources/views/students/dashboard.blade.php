@extends('students.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">

    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-header">
                <h1 class="welcome-title">Welcome back, {{ $student->full_name ?? 'Student' }}!</h1>
                <p class="welcome-subtitle">Track your academic progress and manage your fees</p>
            </div>
        </div>
    </div>

    <!-- Attendance Details Modal -->
    <div class="modal fade" id="attendanceDetailsModal" tabindex="-1" aria-labelledby="attendanceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceDetailsModalLabel">
                        <i class="bi bi-calendar-check me-2"></i>Attendance Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Lesson</th>
                                <th>Teacher</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedAttendances as $attendance)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($attendance->marked_at)->format('d M Y H:i') }}</td>
                                    <td>{{ $attendance->lesson->title ?? '-' }}</td>
                                    <td>{{ $attendance->lesson->teacher->teacher_name ?? '-' }}</td>
                                    <td>
                                        @if($attendance->status === 'P')
                                            <span class="badge bg-success">Present</span>
                                        @elseif($attendance->status === 'L')
                                            <span class="badge bg-warning text-dark">Late</span>
                                        @else
                                            <span class="badge bg-danger">Absent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->status === 'present')
                                            <a href="{{ route('lesson.show', $attendance->lesson_id) }}" class="btn btn-sm btn-primary">View Lesson</a>
                                        @else
                                            @if($attendance->lesson && $attendance->lesson->quiz_link)
                                                <a href="{{ $attendance->lesson->quiz_link }}" class="btn btn-sm btn-warning" target="_blank">Class Quiz</a>
                                            @endif
                                            @if($attendance->lesson && $attendance->lesson->marking_guide_link)
                                                <a href="{{ $attendance->lesson->marking_guide_link }}" class="btn btn-sm btn-info" target="_blank">Marking Guide</a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Card Notification -->
    @php
        $hasStudentCard    = \App\Models\StudentCard::where('student_id', $student->id)->exists();
        $hasPaidStudentCard = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->exists();
    @endphp

    <!-- Timetable Quick Access -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-calendar3 me-2"></i>Your Timetable
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-calendar-week me-2"></i>Class Schedule</h4>
                    <a href="{{ route('student.timetables.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View Full Timetable
                    </a>
                </div>
                <div class="info-card-body">
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-check display-4 text-primary mb-3"></i>
                        <h5>View Your Class Schedule</h5>
                        <p class="text-muted mb-3">Access your weekly timetable, see class times, rooms, and teacher assignments.</p>
                        <a href="{{ route('student.timetables.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-calendar3 me-2"></i>Go to Timetables
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-primary d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-calendar-check me-2"></i>
                    Attendance Percentage: <strong>{{ $attendancePercent }}%</strong>
                </div>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceDetailsModal">
                    View Details
                </button>
            </div>
        </div>
    </div>

    @if($hasStudentCard)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                        <div>
                            <h5 class="alert-heading mb-1">🎓 Your Student Card is Ready!</h5>
                            <p class="mb-2">Your student card has been generated and is ready for pickup at the administration office.</p>
                            <div class="mt-2">
                                <strong>Pickup Instructions:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>Visit the administration office during business hours</li>
                                    <li>Bring a valid ID for verification</li>
                                    <li>You may be asked to sign a receipt</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @elseif($hasPaidStudentCard)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <h5 class="alert-heading mb-1">📋 Student Card Payment Received</h5>
                            <p class="mb-0">Your student card payment has been recorded. The card will be generated soon and you'll be notified when it's ready for pickup.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Fee Overview Cards - Term Based -->
    <div class="row mb-5">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h3 class="section-title mb-0">
                <i class="bi bi-credit-card-2-front me-2"></i>Term-Based Fee Overview
            </h3>
            <a href="{{ route('student.payment-transcript') }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-printer me-1"></i> View &amp; print payment transcript
            </a>
        </div>

        @if(empty($termFeesData))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>All Fees Paid!</strong> You have no outstanding fees. Congratulations!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @else
            @foreach($termFeesData as $termData)
                <div class="col-12 mb-4">
                    <!-- Term Header Card -->
                    <div class="card border-primary shadow-sm mb-3">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        {{ $termData['term']->name }}
                                    </h5>
                                    <small>
                                        {{ $termData['term']->start_date->format('M d, Y') }} - 
                                        {{ $termData['term']->end_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <div class="h5 mb-0">{{ number_format($termData['total_fees'], 0) }} RWF</div>
                                    <small>Total Fees</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Term Progress -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Progress</span>
                                    <span class="badge bg-info">{{ $termData['payment_percentage'] }}%</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $termData['payment_percentage'] }}%"
                                         aria-valuenow="{{ $termData['payment_percentage'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        <small class="text-white fw-bold">
                                            {{ number_format($termData['total_paid'], 0) }} RWF Paid
                                        </small>
                                    </div>
                                </div>
                                <small class="text-muted">Remaining: {{ number_format($termData['total_remaining'], 0) }} RWF</small>
                            </div>

                            <!-- Fee Breakdown -->
                            <div class="row">
                                @foreach($termData['fees'] as $fee)
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <div class="card border-left-{{ $fee['type'] === 'registration_fee' ? 'primary' : ($fee['type'] === 'school_fee' ? 'info' : ($fee['type'] === 'trip_fee' ? 'warning' : 'success')) }} h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">{{ $fee['name'] }}</h6>
                                                <span class="badge bg-secondary">{{ $fee['type'] }}</span>
                                            </div>
                                            
                                            <div class="fee-detail-row">
                                                <span class="text-muted">Fee Amount:</span>
                                                <strong>{{ number_format($fee['total'], 0) }} RWF</strong>
                                            </div>
                                            <div class="fee-detail-row text-success">
                                                <span>✓ Paid:</span>
                                                <strong>{{ number_format($fee['paid'], 0) }} RWF</strong>
                                            </div>
                                            <div class="fee-detail-row text-danger">
                                                <span>⚠ Remaining:</span>
                                                <strong>{{ number_format($fee['remaining'], 0) }} RWF</strong>
                                            </div>

                                            <div class="progress mt-2" style="height: 18px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $fee['total'] > 0 ? (($fee['paid'] / $fee['total']) * 100) : 0 }}%"
                                                     aria-valuenow="{{ $fee['total'] > 0 ? (($fee['paid'] / $fee['total']) * 100) : 0 }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>

                                            @if($fee['remaining'] > 0)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary mt-2 w-100"
                                                    onclick="selectTermAndFee({{ $termData['term']->id }}, '{{ $fee['type'] }}', {{ $fee['remaining'] }})">
                                                <i class="fas fa-money-bill-wave me-1"></i>Pay {{ number_format($fee['remaining'], 0) }} RWF
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    

    <script>
    function selectTermAndFee(termId, feeType, remaining) {
        // Populate payment modal with fee details
        const feeTypeInput = document.getElementById('feeTypeInput');
        const termIdInput = document.getElementById('termIdInput');
        const selectedFeeLabel = document.getElementById('selectedFeeLabel');
        const selectedTermLabel = document.getElementById('selectedTermLabel');
        const payAmountInput = document.getElementById('payAmountInput');
        
        if (feeTypeInput) {
            feeTypeInput.value = feeType;
        }
        if (termIdInput) {
            termIdInput.value = termId;
        }
        if (payAmountInput) {
            payAmountInput.value = remaining;
        }

        if (selectedFeeLabel) {
            selectedFeeLabel.textContent = 'Fee: ' + formatFeeType(feeType);
        }

        if (selectedTermLabel) {
            selectedTermLabel.textContent = 'Term: ' + (getTermName(termId) || 'Selected term');
        }
        
        // Open the payment modal
        const modalElement = document.getElementById('payFeeModal');
        if (modalElement) {
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    new bootstrap.Modal(modalElement).show();
                } else {
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    document.body.classList.add('modal-open');
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            } catch (error) {
                console.error('Error showing payment modal:', error);
                // Fallback: show alert
                alert('Error opening payment modal. Please try again.');
            }
        } else {
            console.error('Payment modal not found');
            alert('Payment modal not found. Please refresh the page.');
        }
    }

    function formatFeeType(feeType) {
        switch (feeType) {
            case 'registration_fee': return 'Registration Fee';
            case 'school_fee': return 'Tuition Fee';
            case 'practical_fee': return 'Practical Fee';
            case 'trip_fee': return 'Trip Fee';
            case 'student_card': return 'Student Card Fee';
            default: return feeType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    }

    function getTermName(termId) {
        const termElement = document.querySelector('[data-term-id="' + termId + '"]');
        return termElement ? termElement.textContent.trim() : null;
    }
    </script>

    <!-- Token Management Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-coin me-2"></i>Token Management
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4>
                        <i class="bi bi-wallet2 me-2"></i>Your Token Balance
                    </h4>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-gear"></i> Manage Tokens
                    </a>
                </div>
                <div class="info-card-body">

                    {{-- Active subscription banner --}}
                    @if(!empty($activeSubscription))
                        <div class="alert alert-success mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-crown-fill me-3 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Active Subscription</h6>
                                    <p class="mb-1">
                                        <strong>{{ $activeSubscription->subscription->name }}</strong> —
                                        {{ $activeSubscription->days_remaining }} days remaining
                                    </p>
                                    <small class="text-muted">Valid until: {{ $activeSubscription->ends_at->format('d M Y') }}</small>
                                    <div class="mt-2">
                                        <span class="badge bg-info">{{ $activeSubscription->subscription->tokens_included }} tokens included</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="token-overview">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="token-balance-display">
                                    <div class="balance-amount">
                                        <span class="token-icon"><i class="bi bi-coin"></i></span>
                                        <span class="balance-number">{{ $student->token_balance ?? 0 }}</span>
                                        <span class="balance-label">tokens</span>
                                    </div>
                                    <div class="balance-details">
                                        <div class="detail-item">
                                            <i class="bi bi-arrow-down-circle text-success"></i>
                                            <span>Total Earned: {{ $student->token ? $student->token->total_earned : 0 }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="bi bi-arrow-up-circle text-danger"></i>
                                            <span>Total Spent: {{ $student->token ? $student->token->total_spent : 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="token-actions">
                                    @if(empty($activeSubscription))
                                        <a href="{{ route('subscriptions.index') }}" class="btn btn-success btn-lg w-100 mb-2">
                                            <i class="bi bi-star me-2"></i>Get Subscription
                                        </a>
                                    @endif
                                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                                        <i class="bi bi-coins me-1"></i>Buy Individual Tokens
                                    </a>
                                    <a href="{{ route('tokens.dashboard') }}" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-clock-history me-2"></i>View History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(($student->token_balance ?? 0) < 10 && empty($activeSubscription))
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Low Token Balance!</strong>
                            You have less than 10 tokens. Consider purchasing a subscription for better value.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Report Card Countdown Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-file-earmark-text me-2"></i>Report Card Access
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-clock-history me-2"></i>Report Card Availability</h4>
                    <a href="{{ route('student.report-cards.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View Report Cards
                    </a>
                </div>
                <div class="info-card-body">
                    <div id="report-card-status">
                        <div class="d-flex align-items-center mb-3">
                            <div class="spinner-border spinner-border-sm me-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>Checking report card access status...</span>
                        </div>
                    </div>
                    
                    <!-- Countdown Timer (will be populated by JavaScript) -->
                    <div id="countdown-container" style="display: none;">
                        <div class="countdown-timer">
                            <h5 class="text-center mb-3">Time Until Report Cards Available</h5>
                            <div class="countdown-display">
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="months">0</div>
                                    <div class="countdown-label">Months</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="weeks">0</div>
                                    <div class="countdown-label">Weeks</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="days">0</div>
                                    <div class="countdown-label">Days</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="hours">0</div>
                                    <div class="countdown-label">Hours</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="minutes">0</div>
                                    <div class="countdown-label">Minutes</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="seconds">0</div>
                                    <div class="countdown-label">Seconds</div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <small class="text-muted" id="available-date"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Plans Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-star me-2"></i>Subscription Plans
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-crown me-2"></i>Get More Tokens with Subscriptions</h4>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View All Plans
                    </a>
                </div>
                <div class="info-card-body">

                    @if(!empty($activeSubscription))
                        <div class="alert alert-success mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Active Subscription</h6>
                                    <p class="mb-1">
                                        <strong>{{ $activeSubscription->subscription->name }}</strong> —
                                        {{ $activeSubscription->days_remaining }} days remaining
                                    </p>
                                    <small class="text-muted">Expires: {{ $activeSubscription->ends_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="subscription-preview">
                        <div class="row">
                            @foreach($subscriptions->take(3) as $subscription)
                            <div class="col-md-4 mb-3">
                                <div class="subscription-card {{ $subscription->is_recommended ? 'recommended' : '' }}">
                                    @if($subscription->is_recommended)
                                        <div class="recommended-badge">
                                            <i class="bi bi-star-fill"></i> Recommended
                                        </div>
                                    @endif
                                    <div class="subscription-header">
                                        <h5>{{ $subscription->name }}</h5>
                                        <div class="subscription-price">
                                            <span class="price-amount">{{ number_format($subscription->price, 0) }} RWF</span>
                                            <span class="price-period"> / {{ $subscription->duration_days }} days</span>
                                        </div>
                                    </div>
                                    <div class="subscription-tokens">
                                        <i class="bi bi-coin"></i> {{ $subscription->tokens_included }} Tokens
                                    </div>
                                    <div class="subscription-features">
                                        @if($subscription->features)
                                            @foreach(array_slice($subscription->features, 0, 3) as $feature)
                                                <div class="feature-item">
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                    <span>{{ Str::limit($feature, 30) }}</span>
                                                </div>
                                            @endforeach
                                            @if(count($subscription->features) > 3)
                                                <div class="feature-item">
                                                    <small class="text-muted">+{{ count($subscription->features) - 3 }} more features</small>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="subscription-action">
                                        @if(!empty($activeSubscription))
                                            <button class="btn btn-outline-secondary w-100" disabled>
                                                <i class="bi bi-check-circle me-1"></i>Already Active
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary w-100"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#subscriptionModal"
                                                    data-subscription-id="{{ $subscription->id }}"
                                                    data-subscription-name="{{ $subscription->name }}"
                                                    data-subscription-price="{{ $subscription->price }}"
                                                    data-subscription-tokens="{{ $subscription->tokens_included }}">
                                                <i class="bi bi-cart-plus me-1"></i>Choose Plan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-grid me-1"></i>View All Subscription Plans
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Live Class Alert -->
    @if($liveClass)
        <div class="row mb-4">
            <div class="col-12">
                <div class="live-class-alert">
                    <div class="live-class-icon"><i class="bi bi-camera-video"></i></div>
                    <div class="live-class-content">
                        <h5>Upcoming Live Class</h5>
                        <p class="mb-1">{{ $liveClass->title }}</p>
                        <small>Starts at: {{ \Carbon\Carbon::parse($liveClass->start_time)->format('d M Y H:i') }}</small>
                    </div>
                    <div class="live-class-action">
                        <button class="btn btn-join">
                            <i class="bi bi-play-circle me-1"></i>Join Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Live Classes Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-broadcast me-2"></i>Live Classes</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-camera-video me-2"></i>Available Live Classes</h4>
                    <a href="{{ route('student.live-classes.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View All
                    </a>
                </div>
                <div class="info-card-body">
                    <div id="live-classes-container">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading live classes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Study Reference Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-book me-2"></i>Study Reference</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-journal-text me-2"></i>Educational Resources</h4>
                    <a href="{{ route('student.resources.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-grid"></i> View All
                    </a>
                </div>
                <div class="info-card-body">
                    <div class="row">
                        @forelse($resources as $resource)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="alx-card">
                                    <div class="alx-card-header resources">
                                        <button onclick="shareResource({{ $resource->id }}, @json(Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 30)))"
                                                class="alx-share-btn" title="Share this resource">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <h6 class="alx-card-title">{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Educational Resource', 40) }}</h6>
                                        <div class="alx-card-subtitle">
                                            <i class="fas fa-graduation-cap mr-2"></i>
                                            {{ $resource->course->course_name ?? 'General Course' }}
                                        </div>
                                    </div>
                                    <div class="alx-card-body">
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
                                        <div class="alx-card-price {{ $resource->is_free ? 'free' : 'paid' }}">
                                            @if($resource->is_free)
                                                <i class="fas fa-gift mr-2"></i>FREE
                                            @else
                                                <i class="fas fa-tag mr-2"></i>RWF {{ number_format($resource->price, 0) }}
                                            @endif
                                        </div>
                                        <div class="alx-card-actions">
                                            <a href="{{ route('student.resources.show', $resource->id) }}" class="alx-btn alx-btn-secondary">
                                                <i class="fas fa-eye"></i>View Details
                                            </a>
                                            @if($resource->document_blob || $resource->video_blob)
                                                <a href="{{ $resource->is_free ? route('student.resources.download', $resource->id) : route('student.resources.payment', $resource->id) }}"
                                                   class="alx-btn alx-btn-success">
                                                    <i class="fas fa-{{ $resource->is_free ? 'download' : 'shopping-cart' }} mr-2"></i>
                                                    {{ $resource->is_free ? 'Download Now' : 'Purchase Access' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-book text-muted fs-1"></i>
                                <p class="text-muted mt-2">No study resources available at the moment. Check back soon!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exams Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-file-earmark-text me-2"></i>Exams</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-journal-check me-2"></i>Upcoming Exams</h4>
                    <a href="{{ route('student.exams') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-list"></i> View All
                    </a>
                </div>
                <div class="info-card-body">
                    <div class="exam-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Final Examinations</h6>
                                <p class="text-muted mb-0">Check your exam schedule and results</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark">Upcoming</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NESA Registration Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-shield-check me-2"></i>NESA Registration</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-award me-2"></i>Registration Status</h4>
                    <button class="btn btn-sm btn-info" onclick="showNesaModal()">
                        <i class="bi bi-info-circle"></i> Check Status
                    </button>
                </div>
                <div class="info-card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">National Examination and Assessment Authority</h6>
                            <p class="text-muted mb-0">Check your NESA registration status and download forms</p>
                        </div>
                        <div class="text-end">
                            @if($student->nesa_registered)
                                <span id="nesa-status-badge" class="badge bg-success">Registered</span>
                            @else
                                <span id="nesa-status-badge" class="badge bg-warning text-dark">Not Registered</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Announcements -->
        @if($announcements->count())
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <h4><i class="bi bi-megaphone me-2"></i>Latest Announcements</h4>
                    </div>
                    <div class="info-card-body">
                        @foreach($announcements as $announcement)
                            <div class="announcement-item">
                                <div class="announcement-content">
                                    <h6>{{ $announcement->title }}</h6>
                                    <div class="announcement-body">{!! $announcement->content !!}</div>
                                    @if($announcement->attachment)
                                        <div class="mt-2">
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/' . $announcement->attachment) }}">
                                                <i class="bi bi-paperclip me-1"></i>View attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="announcement-meta">
                                    <span class="announcement-date">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($announcement->created_at)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Exams & Results -->
        @if($exams->count())
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <h4><i class="bi bi-clipboard-check me-2"></i>Exams & Results</h4>
                    </div>
                    <div class="info-card-body">
                        @foreach($exams as $exam)
                            <div class="exam-item">
                                <div class="exam-content">
                                    <h6>{{ $exam->title }}</h6>
                                    <p class="exam-date">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ \Carbon\Carbon::parse($exam->class_date)->format('d M Y') }}
                                    </p>
                                    @if($exam->requiresTokens())
                                        <p class="exam-tokens">
                                            <i class="bi bi-coins me-1"></i>
                                            {{ $exam->formatted_token_cost }}
                                            @if(auth('student')->user()->token)
                                                | Your balance: {{ auth('student')->user()->token->balance }} tokens
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="exam-status">
                                    @if(\Carbon\Carbon::parse($exam->class_date)->isPast())
                                        @if(isset($marks[$exam->id]))
                                            <span class="status-badge completed">
                                                <i class="bi bi-check-circle me-1"></i>Mark: {{ $marks[$exam->id] }}
                                            </span>
                                        @else
                                            <span class="status-badge pending">
                                                <i class="bi bi-clock me-1"></i>No mark yet
                                            </span>
                                        @endif
                                    @else
                                        @if($exam->requiresTokens() && (!auth('student')->user()->token || !auth('student')->user()->token->canAfford($exam->getCalculatedTokenCost())))
                                            <span class="status-badge insufficient-tokens">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Insufficient tokens
                                            </span>
                                        @else
                                            <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-play-circle me-1"></i>Take Exam
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="payFeeModal" tabindex="-1" aria-labelledby="payFeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content payment-modal">
                <form id="payFeeForm" action="{{ route('student.fees.pay.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fee_type" id="feeTypeInput">
                    <input type="hidden" name="term_id" id="termIdInput">
                    <div class="modal-header">
                        <h5 class="modal-title" id="payFeeModalLabel">
                            <i class="bi bi-credit-card me-2"></i>Pay Fee
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2 text-muted small" id="payFeeModalMeta">
                            <span id="selectedFeeLabel">Fee: </span>
                            <span id="selectedTermLabel">Term: </span>
                        </div>
                        <div class="payment-form">
                            <div class="mb-3">
                                <label for="payAmountInput" class="form-label">
                                    <i class="bi bi-currency-exchange me-1"></i>Amount (Rwf)
                                </label>
                                <input type="number" class="form-control form-control-lg" id="payAmountInput" name="pay_amount" min="1" required>
                            </div>
                            <div id="payFeeError" class="alert alert-danger d-none"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card me-1"></i>Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
function showPaymentModal(button) {
    const feeTypeInput = document.getElementById('feeTypeInput');
    const amountInput  = document.getElementById('payAmountInput');
    if (feeTypeInput) feeTypeInput.value = button.getAttribute('data-feetype');
    if (amountInput)  amountInput.value  = button.getAttribute('data-amount');

    const modalElement = document.getElementById('payFeeModal');
    if (modalElement) {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(modalElement).show();
            } else {
                modalElement.classList.add('show');
                modalElement.style.display = 'block';
                document.body.classList.add('modal-open');
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        } catch (error) {
            alert('Error showing payment modal: ' + error.message);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.pay-fee-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showPaymentModal(btn);
        });
    });

    document.getElementById('payFeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form      = this;
        var errorDiv  = document.getElementById('payFeeError');
        var submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Processing...';
        submitBtn.disabled  = true;
        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':  form.querySelector('[name=_token]').value,
                'Accept':        'application/json',
                'Content-Type':  'application/json'
            },
            body: JSON.stringify({
                student_id: {{ auth('student')->user()->id }},
                fee_type:   document.getElementById('feeTypeInput').value,
                pay_amount: document.getElementById('payAmountInput').value,
                term_id:    document.getElementById('termIdInput').value || null
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.payment_link) {
                window.location.href = data.payment_link;
            } else {
                errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + (data.message || 'Payment failed.');
                errorDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Payment failed. Please try again.';
            errorDiv.classList.remove('d-none');
        })
        .finally(() => {
            submitBtn.innerHTML = '<i class="bi bi-credit-card me-1"></i>Process Payment';
            submitBtn.disabled  = false;
        });
    });

    // Subscription modal population
    document.querySelectorAll('[data-bs-target="#subscriptionModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const id     = this.getAttribute('data-subscription-id');
            const name   = this.getAttribute('data-subscription-name');
            const price  = this.getAttribute('data-subscription-price');
            const tokens = this.getAttribute('data-subscription-tokens');
            const idEl   = document.getElementById('modalSubscriptionId');
            if (idEl) idEl.value = id;
            const nameEl = document.getElementById('modalPlanName');
            if (nameEl) nameEl.textContent = name + ' Plan';
            const priceEl = document.getElementById('modalPlanPrice');
            if (priceEl) priceEl.textContent = parseFloat(price).toLocaleString();
            const tokensEl = document.getElementById('modalPlanTokens');
            if (tokensEl) tokensEl.textContent = tokens + ' Tokens Included';
            const summaryEl = document.getElementById('summaryPrice');
            if (summaryEl) summaryEl.textContent = parseFloat(price).toLocaleString() + ' RWF';
            const totalEl = document.getElementById('summaryTotal');
            if (totalEl) totalEl.textContent = parseFloat(price).toLocaleString() + ' RWF';
        });
    });

    const subscriptionForm = document.getElementById('subscriptionForm');
    if (subscriptionForm) {
        subscriptionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const errorDiv  = document.getElementById('subscriptionError');
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Processing...';
            submitBtn.disabled  = true;
            if (errorDiv) { errorDiv.classList.add('d-none'); errorDiv.innerHTML = ''; }

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.querySelector('[name=_token]').value,
                    'Accept':       'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ subscription_id: document.getElementById('modalSubscriptionId').value })
            })
            .then(r => r.json())
            .then(data => {
                if (data.payment_link) {
                    window.location.href = data.payment_link;
                } else if (errorDiv) {
                    errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + (data.message || 'Payment failed.');
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(() => {
                if (errorDiv) {
                    errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Payment failed. Please try again.';
                    errorDiv.classList.remove('d-none');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Complete Purchase';
                submitBtn.disabled  = false;
            });
        });
    }

    loadLiveClasses();
});

function showNesaModal() {
    const modal = new bootstrap.Modal(document.getElementById('nesaModal'));
    modal.show();
    setTimeout(() => {
        const nesaContent = document.getElementById('nesa-content');
        const nesaLoading = document.getElementById('nesa-loading');
        const nesaFooter  = document.getElementById('nesa-modal-footer');
        const nesaBadge   = document.getElementById('nesa-status-badge');
        
        // Use actual NESA registration status from student data
        const isRegistered = @json($student->nesa_registered);
        const regNumber = @json($student->nesa_reg_number ?? 'N/A');
        const sdmsCode = @json($student->nesa_sdms_code ?? 'N/A');
        const regDate = @json($student->nesa_registration_date ? $student->nesa_registration_date->format('M d, Y') : 'N/A');
        const remarks = @json($student->nesa_remarks ?? 'N/A');
        
        if (isRegistered) {
            nesaContent.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>NESA Registration Complete</strong><br>
                    <small>You are registered for the current academic year.</small>
                </div>
                <div class="mt-3">
                    <h6>Registration Details:</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Registration Number:</strong></td>
                            <td>${regNumber}</td>
                        </tr>
                        <tr>
                            <td><strong>SDMS Code:</strong></td>
                            <td>${sdmsCode}</td>
                        </tr>
                        <tr>
                            <td><strong>Registration Date:</strong></td>
                            <td>${regDate}</td>
                        </tr>
                        <tr>
                            <td><strong>Remarks:</strong></td>
                            <td>${remarks}</td>
                        </tr>
                    </table>
                </div>
            `;
            if (nesaFooter) nesaFooter.innerHTML = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-success" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print Certificate</button>`;
            if (nesaBadge) { nesaBadge.className = 'badge bg-success'; nesaBadge.textContent = 'Registered'; }
        } else {
            nesaContent.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>NESA Registration Required</strong><br>
                    <small>You are not currently registered for this academic year. Please complete your NESA registration.</small>
                </div>
                <div class="mt-3">
                    <h6>Registration Requirements:</h6>
                    <ul class="small">
                        <li>Complete student identification form</li>
                        <li>Submit required documents (ID, passport photo)</li>
                        <li>Pay registration fees</li>
                        <li>Receive NESA registration number</li>
                    </ul>
                </div>
            `;
            if (nesaFooter) nesaFooter.innerHTML = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-warning"><i class="bi bi-envelope me-1"></i> Contact Support</button>`;
            if (nesaBadge) { nesaBadge.className = 'badge bg-warning text-dark'; nesaBadge.textContent = 'Not Registered'; }
        }
        if (nesaLoading) nesaLoading.classList.add('d-none');
        if (nesaContent) nesaContent.classList.remove('d-none');
    }, 1500);
}

function loadLiveClasses() {
    fetch('/api/live-classes/homepage')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('live-classes-container');
            if (data.liveClasses && data.liveClasses.length > 0) {
                container.innerHTML = data.liveClasses.map(lc => `
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">${lc.title}</h6>
                            <p class="text-muted mb-0 small"><i class="bi bi-calendar me-1"></i>${new Date(lc.start_time).toLocaleString()}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">Active</span><br>
                            <button class="btn btn-sm btn-primary mt-1" onclick="joinLiveClass(${lc.id})">
                                <i class="bi bi-camera-video me-1"></i>Join
                            </button>
                        </div>
                    </div>`).join('');
            } else {
                container.innerHTML = `<div class="text-center py-3"><i class="bi bi-camera-video-off fs-1 text-muted"></i><p class="text-muted mt-2">No live classes available right now</p></div>`;
            }
        })
        .catch(() => {
            document.getElementById('live-classes-container').innerHTML = `<div class="text-center py-3"><p class="text-danger">Error loading live classes</p></div>`;
        });
}

function joinLiveClass(classId) {
    window.location.href = `/student/live-classes/${classId}/join`;
}

function shareResource(resourceId, resourceName) {
    const url = `${window.location.origin}/student/resources/${resourceId}`;
    if (navigator.share) {
        navigator.share({ title: 'Educational Resource', text: `Check out: ${resourceName}`, url });
    } else {
        navigator.clipboard.writeText(url).then(() => alert('Resource link copied to clipboard!'));
    }
}

// Report Card Countdown Functionality
let countdownInterval;
let targetDate;
let serverTimeOffset = 0;

function updateReportCardStatus() {
    fetch('{{ route("student.report-cards.countdown") }}')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('report-card-status');
            const countdownContainer = document.getElementById('countdown-container');
            
            // Store server time offset for accurate countdown
            if (data.server_time) {
                serverTimeOffset = new Date(data.server_time).getTime() - Date.now();
            }
            
            if (data.can_access) {
                // Student can access report cards
                statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Report Cards Available!</strong><br>
                        <small class="text-muted">You can now download and view your report cards.</small>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('student.report-cards.index') }}" class="btn btn-success btn-lg">
                            <i class="bi bi-download me-2"></i>Download Report Cards
                        </a>
                    </div>
                `;
                countdownContainer.style.display = 'none';
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
            } else {
                // Student cannot access yet - show countdown
                statusDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Report Cards Not Yet Available</strong><br>
                        <small class="text-muted">${data.reason}</small>
                    </div>
                `;
                
                if (data.countdown_data && data.countdown_data.total_seconds > 0) {
                    targetDate = new Date(data.countdown_data.target_date);
                    countdownContainer.style.display = 'block';
                    startCountdown(data.countdown_data);
                    
                    // Show available date
                    const availableDateElement = document.getElementById('available-date');
                    if (availableDateElement && data.available_date) {
                        const date = new Date(data.available_date);
                        availableDateElement.textContent = `Available on: ${date.toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}`;
                    }
                } else {
                    countdownContainer.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching report card status:', error);
            const statusDiv = document.getElementById('report-card-status');
            statusDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error</strong><br>
                    <small class="text-muted">Unable to check report card status. Please refresh the page.</small>
                </div>
            `;
        });
}

function startCountdown(initialData) {
    // Clear any existing interval
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
    
    function updateCountdownDisplay() {
        // Get current time with server offset for accuracy
        const now = new Date(Date.now() + serverTimeOffset);
        const difference = targetDate - now;
        
        if (difference <= 0) {
            // Time's up - refresh status immediately
            updateReportCardStatus();
            return;
        }
        
        // Calculate time units
        const months = Math.floor(difference / (1000 * 60 * 60 * 24 * 30));
        const weeks = Math.floor((difference % (1000 * 60 * 60 * 24 * 30)) / (1000 * 60 * 60 * 24 * 7));
        const days = Math.floor((difference % (1000 * 60 * 60 * 24 * 7)) / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);
        
        // Update display with smooth animations
        updateCountdownSegment('months', months);
        updateCountdownSegment('weeks', weeks);
        updateCountdownSegment('days', days);
        updateCountdownSegment('hours', hours);
        updateCountdownSegment('minutes', minutes);
        updateCountdownSegment('seconds', seconds);
    }
    
    function updateCountdownSegment(id, value) {
        const element = document.getElementById(id);
        if (element && element.textContent !== value.toString()) {
            element.textContent = value;
            // Add pulse animation for seconds
            if (id === 'seconds') {
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 100);
            }
        }
    }
    
    // Initial update
    updateCountdownDisplay();
    
    // Update every second for truly live countdown
    countdownInterval = setInterval(updateCountdownDisplay, 1000);
}

// Initialize countdown when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateReportCardStatus();
    
    // Refresh status every 30 seconds to sync with server (but countdown stays live)
    setInterval(updateReportCardStatus, 30000);
});
</script>
@endsection


