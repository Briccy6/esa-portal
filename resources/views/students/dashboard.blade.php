@extends('students.app')

@section('title', 'Dashboard')

@section('content')
<style>
.timetable-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.timetable-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.timetable-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.timetable-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.timetable-body {
    padding: 1.5rem;
}

.timetable-image {
    text-align: center;
    margin-bottom: 1rem;
}

.timetable-image img {
    border: 2px solid #f0f0f0;
    transition: transform 0.2s ease;
}

.timetable-image img:hover {
    transform: scale(1.02);
}

.timetable-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.timetable-dates {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.timetable-time-remaining {
    font-size: 0.85rem;
}

.timetable-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

/* Modal styles for timetable image */
.timetable-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
    cursor: pointer;
}

.timetable-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 90%;
    max-height: 90%;
}

.timetable-modal-content img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border: 4px solid white;
    border-radius: 8px;
}

.timetable-modal-close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.timetable-modal-close:hover {
    color: #bbb;
}

/* Countdown Timer Styles */
.countdown-timer {
    text-align: center;
}

.countdown-display {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    margin: 1rem 0;
}

.countdown-segment {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 8px;
    min-width: 80px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.countdown-segment:hover {
    transform: translateY(-2px);
}

.countdown-value {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.countdown-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .countdown-display {
        gap: 0.5rem;
    }
    
    .countdown-segment {
        min-width: 60px;
        padding: 0.75rem 0.5rem;
    }
    
    .countdown-value {
        font-size: 1.5rem;
    }
    
    .countdown-label {
        font-size: 0.65rem;
    }
}

@media (max-width: 480px) {
    .countdown-display {
        gap: 0.25rem;
    }
    
    .countdown-segment {
        min-width: 50px;
        padding: 0.5rem 0.25rem;
    }
    
    .countdown-value {
        font-size: 1.25rem;
    }
    
    .countdown-label {
        font-size: 0.6rem;
    }
}
</style>

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

    <!-- Fee Overview Cards -->
    <div class="row mb-5">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h3 class="section-title mb-0">
                <i class="bi bi-credit-card-2-front me-2"></i>Fee Overview
            </h3>
            <a href="{{ route('student.payment-transcript') }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-printer me-1"></i> View &amp; print payment transcript
            </a>
        </div>

        {{-- Registration Fee --}}
        @if(($registrationFee - ($paidRegistration ?? 0)) > 0)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card registration-card">
                <div class="fee-card-header">
                    <div class="fee-icon"><i class="bi bi-person-plus"></i></div>
                    <div class="fee-title"><h6>Registration Fee</h6></div>
                </div>
                <div class="fee-card-body">
                    <div class="fee-amount">{{ number_format($registrationFee, 2) }} <span>Rwf</span></div>
                    <div class="fee-details">
                        <div class="fee-detail-item paid">
                            <i class="bi bi-check-circle"></i>
                            <span>Paid: {{ number_format($paidRegistration ?? 0, 2) }} Rwf</span>
                        </div>
                        <div class="fee-detail-item remaining">
                            <i class="bi bi-clock"></i>
                            <span>Remaining: {{ number_format(max($registrationFee - ($paidRegistration ?? 0), 0), 2) }} Rwf</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-pay pay-fee-btn"
                            data-feetype="registration_fee"
                            data-amount="{{ $registrationFee - ($paidRegistration ?? 0) }}"
                            onclick="showPaymentModal(this)">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                    </button>
                    @if(($paidRegistration ?? 0) > 0)
                    <a href="{{ route('student.payment-transcript') }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </a>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $registrationFee > 0 ? (($paidRegistration ?? 0) / $registrationFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Trip Fee --}}
        @if(($tripFee - ($paidTrip ?? 0)) > 0)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card trip-card">
                <div class="fee-card-header">
                    <div class="fee-icon"><i class="bi bi-bus-front"></i></div>
                    <div class="fee-title"><h6>Trip Fee</h6></div>
                </div>
                <div class="fee-card-body">
                    <div class="fee-amount">{{ number_format($tripFee, 2) }} <span>Rwf</span></div>
                    <div class="fee-details">
                        <div class="fee-detail-item paid">
                            <i class="bi bi-check-circle"></i>
                            <span>Paid: {{ number_format($paidTrip ?? 0, 2) }} Rwf</span>
                        </div>
                        <div class="fee-detail-item remaining">
                            <i class="bi bi-clock"></i>
                            <span>Remaining: {{ number_format(max($tripFee - ($paidTrip ?? 0), 0), 2) }} Rwf</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-pay pay-fee-btn"
                            data-feetype="trip_fee"
                            data-amount="{{ $tripFee - ($paidTrip ?? 0) }}"
                            onclick="showPaymentModal(this)">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                    </button>
                    @if(($paidTrip ?? 0) > 0)
                    <a href="{{ route('student.payment-transcript') }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </a>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $tripFee > 0 ? (($paidTrip ?? 0) / $tripFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tuition Fee --}}
        @if(($tuitionFee - ($paidTuition ?? 0)) > 0)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card tuition-card">
                <div class="fee-card-header">
                    <div class="fee-icon"><i class="bi bi-book"></i></div>
                    <div class="fee-title"><h6>Tuition Fee</h6></div>
                </div>
                <div class="fee-card-body">
                    <div class="fee-amount">{{ number_format($tuitionFee, 2) }} <span>Rwf</span></div>
                    <div class="fee-details">
                        <div class="fee-detail-item paid">
                            <i class="bi bi-check-circle"></i>
                            <span>Paid: {{ number_format($paidTuition ?? 0, 2) }} Rwf</span>
                        </div>
                        <div class="fee-detail-item remaining">
                            <i class="bi bi-clock"></i>
                            <span>Remaining: {{ number_format(max($tuitionFee - ($paidTuition ?? 0), 0), 2) }} Rwf</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-pay pay-fee-btn"
                            data-feetype="school_fee"
                            data-amount="{{ $tuitionFee - ($paidTuition ?? 0) }}"
                            onclick="showPaymentModal(this)">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                    </button>
                    @if(($paidTuition ?? 0) > 0)
                    <a href="{{ route('student.payment-transcript') }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </a>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $tuitionFee > 0 ? (($paidTuition ?? 0) / $tuitionFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Practical Fee --}}
        @if(($praticalFee - ($paidPractical ?? 0)) > 0)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card practical-card">
                <div class="fee-card-header">
                    <div class="fee-icon"><i class="bi bi-tools"></i></div>
                    <div class="fee-title"><h6>Practical Fee</h6></div>
                </div>
                <div class="fee-card-body">
                    <div class="fee-amount">{{ number_format($praticalFee, 2) }} <span>Rwf</span></div>
                    <div class="fee-details">
                        <div class="fee-detail-item paid">
                            <i class="bi bi-check-circle"></i>
                            <span>Paid: {{ number_format($paidPractical ?? 0, 2) }} Rwf</span>
                        </div>
                        <div class="fee-detail-item remaining">
                            <i class="bi bi-clock"></i>
                            <span>Remaining: {{ number_format(max($praticalFee - ($paidPractical ?? 0), 0), 2) }} Rwf</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-pay pay-fee-btn"
                            data-feetype="practical_fee"
                            data-amount="{{ $praticalFee - ($paidPractical ?? 0) }}"
                            onclick="showPaymentModal(this)">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                    </button>
                    @if(($paidPractical ?? 0) > 0)
                    <a href="{{ route('student.payment-transcript') }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </a>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $praticalFee > 0 ? (($paidPractical ?? 0) / $praticalFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Student Card Fee --}}
        @if(($studentCardFee - $paidStudentCard) > 0)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card student-card-fee">
                <div class="fee-card-header">
                    <div class="fee-icon"><i class="bi bi-person-badge"></i></div>
                    <div class="fee-title"><h6>Student Card</h6></div>
                </div>
                <div class="fee-card-body">
                    <div class="fee-amount">{{ number_format($studentCardFee, 2) }} <span>Rwf</span></div>
                    <div class="fee-details">
                        <div class="fee-detail-item paid">
                            <i class="bi bi-check-circle"></i>
                            <span>Paid: {{ number_format($paidStudentCard, 2) }} Rwf</span>
                        </div>
                        <div class="fee-detail-item remaining">
                            <i class="bi bi-clock"></i>
                            <span>Remaining: {{ number_format(max($studentCardFee - $paidStudentCard, 0), 2) }} Rwf</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-pay pay-fee-btn"
                            data-feetype="student_card"
                            data-amount="{{ $studentCardFee - $paidStudentCard }}"
                            onclick="showPaymentModal(this)">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                    </button>
                    @if($paidStudentCard > 0)
                    <a href="{{ route('student.payment-transcript') }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </a>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $studentCardFee > 0 ? ($paidStudentCard / $studentCardFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        @endif
    </div>

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

    <!-- Timetables Section -->
    <div class="row mb-4" id="timetables">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-calendar2-week me-2"></i>Timetables</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-calendar2-week me-2"></i>Timetables</h4>
                    <span class="badge bg-info">{{ $timetables->count() }} Total</span>
                </div>
                <div class="info-card-body">
                    @php
                        $nowKigali = \Carbon\Carbon::now('Africa/Kigali');
                        $activeTimetables = $timetables->filter(function($t) use ($nowKigali) {
                            if ($t->valid_from && $t->valid_until) {
                                return $nowKigali->between(\Carbon\Carbon::parse($t->valid_from), \Carbon\Carbon::parse($t->valid_until));
                            }
                            return true; // Always valid if no dates set
                        });
                        
                        $upcomingTimetables = $timetables->filter(function($t) use ($nowKigali) {
                            if ($t->valid_from) {
                                return $nowKigali->lt(\Carbon\Carbon::parse($t->valid_from));
                            }
                            return false;
                        });
                    @endphp
                    
                    @if($activeTimetables->count() > 0)
                        <div class="mb-4">
                            <h5 class="text-success mb-3">
                                <i class="bi bi-check-circle me-2"></i>Currently Active ({{ $activeTimetables->count() }})
                            </h5>
                            @foreach($activeTimetables as $timetable)
                                @include('partials.timetable-card', ['timetable' => $timetable, 'showStatus' => false])
                            @endforeach
                        </div>
                    @endif
                    
                    @if($upcomingTimetables->count() > 0)
                        <div class="mb-4">
                            <h5 class="text-warning mb-3">
                                <i class="bi bi-clock me-2"></i>Upcoming ({{ $upcomingTimetables->count() }})
                            </h5>
                            @foreach($upcomingTimetables as $timetable)
                                @include('partials.timetable-card', ['timetable' => $timetable, 'showStatus' => true])
                            @endforeach
                        </div>
                    @endif
                    
                    @if($timetables->count() === 0)
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No Timetables Available</h5>
                            <p class="text-muted">There are currently no timetables available. Please check back later.</p>
                        </div>
                    @endif
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
                                    <p>{{ $announcement->body ?? $announcement->content }}</p>
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
                <form id="payFeeForm" action="{{ route('student.payment.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fee_type" id="feeTypeInput">
                    <div class="modal-header">
                        <h5 class="modal-title" id="payFeeModalLabel">
                            <i class="bi bi-credit-card me-2"></i>Pay Fee
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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

<style>
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    }
    .welcome-title { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
    .welcome-subtitle { font-size: 1.1rem; opacity: 0.9; margin: 0; }
    .section-title { color: #1e293b; font-weight: 600; margin-bottom: 1.5rem; font-size: 1.25rem; }
    .fee-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; transition: all 0.3s ease; overflow: hidden; position: relative; }
    .fee-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
    .fee-card-header { display: flex; align-items: center; padding: 1.5rem 1.5rem 1rem; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
    .fee-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.5rem; }
    .registration-card .fee-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
    .trip-card .fee-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .tuition-card .fee-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .practical-card .fee-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
    .student-card-fee .fee-icon { background: linear-gradient(135deg, #ec4899, #be185d); color: white; }
    .fee-title h6 { margin: 0; font-weight: 600; color: #1e293b; font-size: 1rem; }
    .fee-card-body { padding: 0 1.5rem 1.5rem; }
    .fee-amount { font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; }
    .fee-amount span { font-size: 1rem; font-weight: 500; color: #64748b; }
    .fee-details { margin-bottom: 1.5rem; }
    .fee-detail-item { display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.875rem; }
    .fee-detail-item.paid { color: #059669; }
    .fee-detail-item.remaining { color: #dc2626; }
    .fee-detail-item i { margin-right: 0.5rem; width: 16px; }
    .btn-pay { background: linear-gradient(135deg, #3b82f6, #1d4ed8); border: none; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; width: 100%; transition: all 0.3s ease; }
    .btn-pay:hover { background: linear-gradient(135deg, #1d4ed8, #1e40af); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.4); color: white; }
    .paid-badge { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-align: center; font-weight: 600; }
    .progress-bar { height: 4px; background: #e2e8f0; position: absolute; bottom: 0; left: 0; right: 0; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #1d4ed8); transition: width 0.5s ease; }
    .live-class-alert { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1.5rem; border-radius: 16px; display: flex; align-items: center; box-shadow: 0 8px 32px rgba(16,185,129,0.3); }
    .live-class-icon { font-size: 2rem; margin-right: 1.5rem; }
    .live-class-content { flex: 1; }
    .btn-join { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; }
    .btn-join:hover { background: rgba(255,255,255,0.3); color: white; }
    .info-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; height: 100%; }
    .info-card-header { padding: 1.5rem; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display: flex; align-items: center; justify-content: space-between; border-radius: 16px 16px 0 0; }
    .info-card-header h4 { margin: 0; color: #1e293b; font-weight: 600; font-size: 1.125rem; }
    .info-card-body { padding: 1.5rem; max-height: 400px; overflow-y: auto; }
    .announcement-item { padding: 1rem 0; border-bottom: 1px solid #f1f5f9; }
    .announcement-item:last-child { border-bottom: none; }
    .announcement-content h6 { color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; }
    .announcement-content p { color: #64748b; margin-bottom: 0.75rem; line-height: 1.5; }
    .announcement-date { color: #64748b; font-size: 0.875rem; }
    .alx-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); transition: all 0.3s ease; border: 1px solid #e5e7eb; height: 100%; display: flex; flex-direction: column; }
    .alx-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    .alx-card-header { padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; position: relative; }
    .alx-card-header.resources { background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); }
    .alx-card-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
    .alx-card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.4; }
    .alx-card-subtitle { font-size: 0.875rem; opacity: 0.9; margin-bottom: 1rem; }
    .alx-card-price { font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; }
    .alx-card-price.free { color: #10b981; }
    .alx-card-price.paid { color: #f59e0b; }
    .alx-card-actions { margin-top: auto; display: flex; flex-direction: column; gap: 0.75rem; }
    .alx-btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-align: center; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.875rem; }
    .alx-btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
    .alx-btn-secondary:hover { background: #e5e7eb; color: #374151; }
    .alx-btn-success { background: #10b981; color: white; }
    .alx-btn-success:hover { background: #059669; color: white; }
    .alx-meta-info { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
    .alx-meta-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #6b7280; }
    .alx-type-badges { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
    .alx-type-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
    .alx-share-btn { position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); color: white; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; }
    .alx-share-btn:hover { background: rgba(255,255,255,0.3); transform: scale(1.05); }
    .exam-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f1f5f9; }
    .exam-item:last-child { border-bottom: none; }
    .exam-content { flex: 1; }
    .exam-content h6 { color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; }
    .exam-date { color: #64748b; font-size: 0.875rem; margin: 0; }
    .exam-tokens { color: #059669; font-size: 0.875rem; margin: 0.25rem 0 0 0; font-weight: 500; }
    .exam-tokens i { color: #f59e0b; }
    .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; white-space: nowrap; }
    .status-badge.completed { background: #dcfce7; color: #166534; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.insufficient-tokens { background: #fee2e2; color: #dc2626; }
    .payment-form .form-control { border: 2px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; transition: border-color 0.3s ease; }
    .payment-form .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .payment-form .form-label { font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
    .token-overview { padding: 1rem 0; }
    .token-balance-display { text-align: center; padding: 2rem; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 16px; border: 2px solid #e2e8f0; }
    .balance-amount { display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; }
    .token-icon { font-size: 3rem; color: #f59e0b; margin-right: 1rem; animation: pulse 2s infinite; }
    .balance-number { font-size: 3.5rem; font-weight: 800; color: #1e293b; line-height: 1; }
    .balance-label { font-size: 1.2rem; color: #64748b; margin-left: 0.5rem; font-weight: 500; }
    .balance-details { display: flex; justify-content: space-around; margin-top: 1rem; }
    .detail-item { display: flex; align-items: center; font-size: 0.9rem; color: #64748b; }
    .detail-item i { margin-right: 0.5rem; font-size: 1.1rem; }
    .token-actions { display: flex; flex-direction: column; gap: 0.5rem; }
    @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
    .info-card-body::-webkit-scrollbar { width: 4px; }
    .info-card-body::-webkit-scrollbar-track { background: #f1f5f9; }
    .info-card-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .subscription-card { background: white; border-radius: 16px; border: 2px solid #e2e8f0; padding: 1.5rem; height: 100%; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .subscription-card:hover { transform: translateY(-5px); box-shadow: 0 12px 40px rgba(0,0,0,0.15); border-color: #3b82f6; }
    .subscription-card.recommended { border-color: #3b82f6; background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); }
    .recommended-badge { position: absolute; top: 0; right: 0; background: #3b82f6; color: white; padding: 0.5rem 1rem; border-bottom-left-radius: 8px; font-size: 0.75rem; font-weight: 600; }
    .subscription-header { text-align: center; margin-bottom: 1.5rem; }
    .subscription-header h5 { color: #1e293b; font-weight: 700; margin-bottom: 0.5rem; }
    .subscription-price { display: flex; align-items: baseline; justify-content: center; gap: 0.25rem; }
    .price-amount { font-size: 1.5rem; font-weight: 800; color: #3b82f6; }
    .price-period { font-size: 0.875rem; color: #6b7280; }
    .subscription-tokens { text-align: center; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 0.75rem; margin-bottom: 1rem; font-weight: 600; color: #0369a1; }
    .subscription-features { margin-bottom: 1.5rem; }
    .feature-item { display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.875rem; color: #4b5563; }
    .feature-item i { margin-right: 0.5rem; }
    @media (max-width: 1200px) {
        .fee-card { margin-bottom: 1rem; }
        .subscription-card { margin-bottom: 1rem; }
    }
    
    @media (max-width: 992px) {
        .alx-card { margin-bottom: 1rem; }
        .info-card { margin-bottom: 1rem; }
        .token-balance-display { padding: 1.5rem; }
        .balance-number { font-size: 2.8rem; }
        .token-icon { font-size: 2.5rem; }
    }
    
    @media (max-width: 768px) {
        .container-fluid { padding: 0.75rem; }
        .welcome-header { padding: 1.5rem; }
        .welcome-title { font-size: 1.5rem; }
        .welcome-subtitle { font-size: 1rem; }
        
        .section-title { font-size: 1.1rem; margin-bottom: 1rem; }
        .fee-card { margin-bottom: 1rem; }
        .fee-card-header { padding: 1rem; }
        .fee-card-body { padding: 0 1rem 1rem; }
        .fee-amount { font-size: 1.5rem; }
        .fee-icon { width: 40px; height: 40px; font-size: 1.25rem; }
        
        .live-class-alert { flex-direction: column; text-align: center; padding: 1.25rem; }
        .live-class-icon { margin-right: 0; margin-bottom: 1rem; font-size: 1.5rem; }
        .live-class-action { margin-top: 1rem; }
        
        .info-card { margin-bottom: 1rem; }
        .info-card-header { padding: 1rem; flex-direction: column; gap: 0.75rem; text-align: center; }
        .info-card-header h4 { font-size: 1rem; }
        .info-card-body { padding: 1rem; max-height: none; }
        
        .exam-item { flex-direction: column; align-items: flex-start; padding: 0.75rem 0; }
        .exam-status { margin-top: 0.75rem; align-self: stretch; }
        .status-badge { font-size: 0.8rem; padding: 0.4rem 0.8rem; }
        
        .token-overview { padding: 0.75rem 0; }
        .token-balance-display { padding: 1.25rem 0.75rem; }
        .balance-amount { flex-direction: column; margin-bottom: 1rem; }
        .token-icon { margin-right: 0; margin-bottom: 0.5rem; font-size: 2rem; }
        .balance-number { font-size: 2.5rem; }
        .balance-label { margin-left: 0; font-size: 1rem; }
        .balance-details { flex-direction: column; gap: 0.5rem; }
        
        .subscription-card { padding: 1rem; margin-bottom: 1rem; }
        .subscription-header h5 { font-size: 1rem; }
        .price-amount { font-size: 1.25rem; }
        .feature-item { font-size: 0.8rem; }
        
        .alx-card { margin-bottom: 1rem; }
        .alx-card-header { padding: 1rem; }
        .alx-card-body { padding: 1rem; }
        .alx-card-title { font-size: 1rem; }
        .alx-card-price { font-size: 1.1rem; }
        .alx-meta-info { gap: 0.5rem; }
        .alx-meta-item { font-size: 0.8rem; }
        .alx-btn { padding: 0.6rem 1rem; font-size: 0.8rem; }
        
        .announcement-item { padding: 0.75rem 0; }
        .announcement-content h6 { font-size: 0.95rem; }
        .announcement-content p { font-size: 0.85rem; }
        .announcement-date { font-size: 0.8rem; }
    }
    
    @media (max-width: 576px) {
        .container-fluid { padding: 0.5rem; }
        .welcome-header { padding: 1rem; margin-bottom: 0.75rem; }
        .welcome-title { font-size: 1.25rem; }
        .welcome-subtitle { font-size: 0.9rem; }
        
        .section-title { font-size: 1rem; margin-bottom: 0.75rem; }
        .fee-card { margin-bottom: 0.75rem; }
        .fee-card-header { padding: 0.75rem; }
        .fee-card-body { padding: 0 0.75rem 0.75rem; }
        .fee-amount { font-size: 1.25rem; }
        .fee-icon { width: 36px; height: 36px; font-size: 1.1rem; margin-right: 0.75rem; }
        .fee-title h6 { font-size: 0.9rem; }
        .fee-detail-item { font-size: 0.8rem; }
        .btn-pay { padding: 0.6rem 1rem; font-size: 0.85rem; }
        
        .alert { padding: 0.75rem; margin-bottom: 0.75rem; }
        .alert .btn { font-size: 0.8rem; padding: 0.25rem 0.5rem; }
        
        .live-class-alert { padding: 1rem; }
        .live-class-content h5 { font-size: 1rem; }
        .live-class-content p { font-size: 0.85rem; }
        .btn-join { padding: 0.5rem 1rem; font-size: 0.85rem; }
        
        .info-card { margin-bottom: 0.75rem; }
        .info-card-header { padding: 0.75rem; }
        .info-card-header h4 { font-size: 0.9rem; }
        .info-card-header .btn { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .info-card-body { padding: 0.75rem; }
        
        .exam-item { padding: 0.5rem 0; }
        .exam-content h6 { font-size: 0.9rem; }
        .exam-date { font-size: 0.8rem; }
        .exam-tokens { font-size: 0.8rem; }
        .status-badge { font-size: 0.75rem; padding: 0.35rem 0.7rem; }
        .btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        
        .token-overview { padding: 0.5rem 0; }
        .token-balance-display { padding: 1rem 0.5rem; }
        .balance-number { font-size: 2rem; }
        .balance-label { font-size: 0.9rem; }
        .token-icon { font-size: 1.75rem; }
        .detail-item { font-size: 0.8rem; }
        .token-actions .btn { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
        
        .subscription-card { padding: 0.75rem; }
        .subscription-header h5 { font-size: 0.9rem; }
        .price-amount { font-size: 1.1rem; }
        .price-period { font-size: 0.75rem; }
        .subscription-tokens { font-size: 0.8rem; padding: 0.5rem; }
        .feature-item { font-size: 0.75rem; }
        .subscription-card .btn { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
        
        .alx-card { margin-bottom: 0.75rem; }
        .alx-card-header { padding: 0.75rem; }
        .alx-card-title { font-size: 0.9rem; }
        .alx-card-subtitle { font-size: 0.8rem; }
        .alx-card-body { padding: 0.75rem; }
        .alx-card-price { font-size: 1rem; }
        .alx-meta-info { flex-direction: column; gap: 0.25rem; }
        .alx-meta-item { font-size: 0.75rem; }
        .alx-type-badges { gap: 0.25rem; }
        .alx-type-badge { font-size: 0.7rem; padding: 0.2rem 0.5rem; }
        .alx-btn { font-size: 0.75rem; padding: 0.5rem 0.75rem; }
        .alx-share-btn { padding: 0.35rem; font-size: 0.8rem; }
        
        .announcement-item { padding: 0.5rem 0; }
        .announcement-content h6 { font-size: 0.85rem; }
        .announcement-content p { font-size: 0.8rem; }
        .announcement-date { font-size: 0.75rem; }
        
        .modal-dialog { margin: 0.5rem; }
        .modal-header { padding: 1rem; }
        .modal-body { padding: 1rem; }
        .modal-footer { padding: 0.75rem 1rem; }
        .modal-title { font-size: 1rem; }
        .form-control { font-size: 0.9rem; }
        .form-label { font-size: 0.85rem; }
    }
    
    @media (max-width: 480px) {
        .welcome-header { padding: 0.75rem; }
        .welcome-title { font-size: 1.1rem; }
        .welcome-subtitle { font-size: 0.85rem; }
        
        .fee-card { border-radius: 12px; }
        .fee-card-header { padding: 0.5rem; }
        .fee-card-body { padding: 0 0.5rem 0.5rem; }
        .fee-icon { width: 32px; height: 32px; font-size: 1rem; margin-right: 0.5rem; }
        .fee-title h6 { font-size: 0.85rem; }
        .fee-amount { font-size: 1.1rem; }
        .fee-detail-item { font-size: 0.75rem; }
        .btn-pay { padding: 0.5rem 0.75rem; font-size: 0.8rem; }
        
        .info-card { border-radius: 12px; }
        .info-card-header { padding: 0.5rem; }
        .info-card-header h4 { font-size: 0.85rem; }
        .info-card-body { padding: 0.5rem; }
        
        .token-balance-display { padding: 0.75rem 0.25rem; }
        .balance-number { font-size: 1.75rem; }
        .balance-label { font-size: 0.85rem; }
        .token-icon { font-size: 1.5rem; }
        
        .subscription-card { padding: 0.5rem; border-radius: 12px; }
        .subscription-header h5 { font-size: 0.85rem; }
        .price-amount { font-size: 1rem; }
        .subscription-tokens { font-size: 0.75rem; }
        
        .alx-card { border-radius: 8px; }
        .alx-card-header { padding: 0.5rem; }
        .alx-card-body { padding: 0.5rem; }
        .alx-card-title { font-size: 0.85rem; }
        .alx-card-price { font-size: 0.9rem; }
        .alx-btn { font-size: 0.7rem; padding: 0.4rem 0.6rem; }
        
        .modal-dialog { margin: 0.25rem; }
        .modal-header { padding: 0.75rem; }
        .modal-body { padding: 0.75rem; }
        .modal-footer { padding: 0.5rem 0.75rem; }
    }
    
    /* Ensure all elements are visible on mobile */
    @media (max-width: 768px) {
        .d-flex { display: flex !important; }
        .justify-content-between { justify-content: space-between !important; }
        .align-items-center { align-items: center !important; }
        .text-end { text-align: right !important; }
        .text-center { text-align: center !important; }
        .mb-4 { margin-bottom: 1rem !important; }
        .mb-3 { margin-bottom: 0.75rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mt-4 { margin-top: 1rem !important; }
        .mt-3 { margin-top: 0.75rem !important; }
        .mt-2 { margin-top: 0.5rem !important; }
        .mt-1 { margin-top: 0.25rem !important; }
        .py-4 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
        .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
        .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
        .py-1 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
    }
    
    /* Fix overflow issues on mobile */
    @media (max-width: 576px) {
        .row { margin: 0; }
        .col-12, .col-lg-6, .col-lg-4, .col-lg-3, .col-md-6, .col-md-4, .col-md-8 { padding: 0 0.25rem; }
        .fee-card, .info-card, .alx-card, .subscription-card { overflow: hidden; }
        .btn { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .badge { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
    }
    
    /* Touch-friendly targets for mobile */
    @media (max-width: 768px) {
        .btn, .pay-fee-btn, .alx-btn { min-height: 44px; min-width: 44px; }
        .fee-card, .info-card, .alx-card, .subscription-card { margin-bottom: 0.75rem; }
        .modal-dialog { max-width: 95vw; }
    }
</style>
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
                amount:     document.getElementById('payAmountInput').value,
                total_fee:  document.getElementById('payAmountInput').value,
                term:       '{{ $student->academicYear->year ?? "Current" }}'
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

// Timetable functions
function viewTimetableImage(title, imageData) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('timetableModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'timetableModal';
        modal.className = 'timetable-modal';
        modal.innerHTML = `
            <span class="timetable-modal-close">&times;</span>
            <div class="timetable-modal-content">
                <img id="timetableModalImage" src="" alt="">
            </div>
        `;
        document.body.appendChild(modal);

        // Add close functionality
        modal.onclick = function(event) {
            if (event.target === modal || event.target.className === 'timetable-modal-close') {
                modal.style.display = 'none';
            }
        };
    }

    // Set image and show modal
    document.getElementById('timetableModalImage').src = imageData;
    document.getElementById('timetableModalImage').alt = title;
    modal.style.display = 'block';
}

function downloadTimetable(title, imageData) {
    const link = document.createElement('a');
    link.href = imageData;
    link.download = `${title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_timetable.jpg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Report Card Countdown Functionality
let countdownInterval;
let targetDate;

function updateReportCardStatus() {
    fetch('{{ route("student.report-cards.countdown") }}')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('report-card-status');
            const countdownContainer = document.getElementById('countdown-container');
            
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
        const now = new Date();
        const difference = targetDate - now;
        
        if (difference <= 0) {
            // Time's up - refresh status
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
        
        // Update display
        document.getElementById('months').textContent = months;
        document.getElementById('weeks').textContent = weeks;
        document.getElementById('days').textContent = days;
        document.getElementById('hours').textContent = hours;
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;
    }
    
    // Initial update
    updateCountdownDisplay();
    
    // Update every second
    countdownInterval = setInterval(updateCountdownDisplay, 1000);
}

// Initialize countdown when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateReportCardStatus();
    
    // Refresh status every 5 minutes in case of changes
    setInterval(updateReportCardStatus, 5 * 60 * 1000);
});
</script>
@endsection