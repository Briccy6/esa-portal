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
<div class="modal fade" id="attendanceDetailsModal" tabindex="-1" aria-labelledby="attendanceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceDetailsModalLabel">
                        <i class="bi bi-calendar-check me-2"></i>
                        Attendance Details
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
                                            <a href="{{ route('lesson.show', $attendance->lesson_id) }}" class="btn btn-sm btn-primary">
                                                View Lesson
                                            </a>
                                        @else
                                            @if($attendance->lesson && $attendance->lesson->quiz_link)
                                                <a href="{{ $attendance->lesson->quiz_link }}" class="btn btn-sm btn-warning" target="_blank">
                                                    Class Quiz
                                                </a>
                                            @endif
                                            @if($attendance->lesson && $attendance->lesson->marking_guide_link)
                                                <a href="{{ $attendance->lesson->marking_guide_link }}" class="btn btn-sm btn-info" target="_blank">
                                                    Marking Guide
                                                </a>
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
        $hasStudentCard = \App\Models\StudentCard::where('student_id', $student->id)->exists();
        $hasPaidStudentCard = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->exists();
    @endphp
     {{-- Attendance Overview --}}
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
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-credit-card-2-front me-2"></i>
                Fee Overview
            </h3>
        </div>
        
        {{-- Registration Fee --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card registration-card">
                <div class="fee-card-header">
                    <div class="fee-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="fee-title">
                        <h6>Registration Fee</h6>
                    </div>
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
                    @if(($registrationFee - ($paidRegistration ?? 0)) > 0)
                        <button type="button" class="btn btn-pay pay-fee-btn"
                                data-feetype="registration"
                                data-amount="{{ $registrationFee - ($paidRegistration ?? 0) }}">
                            <i class="bi bi-credit-card me-1"></i>
                            Pay Now
                        </button>
                    @else
                        <div class="paid-badge">
                            <i class="bi bi-check-circle me-1"></i>
                            Fully Paid
                        </div>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $registrationFee > 0 ? (($paidRegistration ?? 0) / $registrationFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Trip Fee --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card trip-card">
                <div class="fee-card-header">
                    <div class="fee-icon">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <div class="fee-title">
                        <h6>Trip Fee</h6>
                    </div>
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
                    @if(($tripFee - ($paidTrip ?? 0)) > 0)
                        <button type="button" class="btn btn-pay pay-fee-btn"
                                data-feetype="trip"
                                data-amount="{{ $tripFee - ($paidTrip ?? 0) }}">
                            <i class="bi bi-credit-card me-1"></i>
                            Pay Now
                        </button>
                    @else
                        <div class="paid-badge">
                            <i class="bi bi-check-circle me-1"></i>
                            Fully Paid
                        </div>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $tripFee > 0 ? (($paidTrip ?? 0) / $tripFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Tuition Fee --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card tuition-card">
                <div class="fee-card-header">
                    <div class="fee-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <div class="fee-title">
                        <h6>Tuition Fee</h6>
                    </div>
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
                    @if(($tuitionFee - ($paidTuition ?? 0)) > 0)
                        <button type="button" class="btn btn-pay pay-fee-btn"
                                data-feetype="tuition"
                                data-amount="{{ $tuitionFee - ($paidTuition ?? 0) }}">
                            <i class="bi bi-credit-card me-1"></i>
                            Pay Now
                        </button>
                    @else
                        <div class="paid-badge">
                            <i class="bi bi-check-circle me-1"></i>
                            Fully Paid
                        </div>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $tuitionFee > 0 ? (($paidTuition ?? 0) / $tuitionFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Practical Fee --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card practical-card">
                <div class="fee-card-header">
                    <div class="fee-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="fee-title">
                        <h6>Practical Fee</h6>
                    </div>
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
                    @if(($praticalFee - ($paidPractical ?? 0)) > 0)
                        <button type="button" class="btn btn-pay pay-fee-btn"
                                data-feetype="practical"
                                data-amount="{{ $praticalFee - ($paidPractical ?? 0) }}">
                            <i class="bi bi-credit-card me-1"></i>
                            Pay Now
                        </button>
                    @else
                        <div class="paid-badge">
                            <i class="bi bi-check-circle me-1"></i>
                            Fully Paid
                        </div>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $praticalFee > 0 ? (($paidPractical ?? 0) / $praticalFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Student Card Fee --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="fee-card student-card-fee">
                <div class="fee-card-header">
                    <div class="fee-icon">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="fee-title">
                        <h6>Student Card</h6>
                    </div>
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
                    @if(($studentCardFee - $paidStudentCard) > 0)
                        <button type="button" class="btn btn-pay pay-fee-btn"
                                data-feetype="student_card"
                                data-amount="{{ $studentCardFee - $paidStudentCard }}">
                            <i class="bi bi-credit-card me-1"></i>
                            Pay Now
                        </button>
                    @else
                        <div class="paid-badge">
                            <i class="bi bi-check-circle me-1"></i>
                            Fully Paid
                        </div>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $studentCardFee > 0 ? ($paidStudentCard / $studentCardFee) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Class Alert -->
    @if($liveClass)
        <div class="row mb-4">
            <div class="col-12">
                <div class="live-class-alert">
                    <div class="live-class-icon">
                        <i class="bi bi-camera-video"></i>
                    </div>
                    <div class="live-class-content">
                        <h5>Upcoming Live Class</h5>
                        <p class="mb-1">{{ $liveClass->title }}</p>
                        <small>Starts at: {{ \Carbon\Carbon::parse($liveClass->start_time)->format('d M Y H:i') }}</small>
                    </div>
                    <div class="live-class-action">
                        <button class="btn btn-join">
                            <i class="bi bi-play-circle me-1"></i>
                            Join Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Live Classes Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-broadcast me-2"></i>
                Live Classes
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4>
                        <i class="bi bi-camera-video me-2"></i>
                        Available Live Classes
                    </h4>
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

    <!-- Exams Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-file-earmark-text me-2"></i>
                Exams
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4>
                        <i class="bi bi-journal-check me-2"></i>
                        Upcoming Exams
                    </h4>
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
            <h3 class="section-title">
                <i class="bi bi-shield-check me-2"></i>
                NESA Registration
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4>
                        <i class="bi bi-award me-2"></i>
                        Registration Status
                    </h4>
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
                            <span id="nesa-status-badge" class="badge bg-secondary">Checking...</span>
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
                        <h4>
                            <i class="bi bi-megaphone me-2"></i>
                            Latest Announcements
                        </h4>
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
                        <h4>
                            <i class="bi bi-clipboard-check me-2"></i>
                            Exams & Results
                        </h4>
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
                                </div>
                                <div class="exam-status">
                                    @if(\Carbon\Carbon::parse($exam->class_date)->isPast())
                                        @if(isset($marks[$exam->id]))
                                            <span class="status-badge completed">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Mark: {{ $marks[$exam->id] }}
                                            </span>
                                        @else
                                            <span class="status-badge pending">
                                                <i class="bi bi-clock me-1"></i>
                                                No mark yet
                                            </span>
                                        @endif
                                    @else
                                        <span class="status-badge upcoming">
                                            <i class="bi bi-calendar-plus me-1"></i>
                                            Upcoming
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
            <input type="hidden" name="fee_type" id="feeTypeInput">
            <div class="modal-content payment-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="payFeeModalLabel">
                        <i class="bi bi-credit-card me-2"></i>
                        Pay Fee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="payment-form">
                        <div class="mb-3">
                            <label for="payAmountInput" class="form-label">
                                <i class="bi bi-currency-exchange me-1"></i>
                                Amount (Rwf)
                            </label>
                            <input type="number" class="form-control form-control-lg" id="payAmountInput" name="pay_amount" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="payNumberInput" class="form-label">
                                <i class="bi bi-phone me-1"></i>
                                Phone/Account Number
                            </label>
                            <input type="text" class="form-control form-control-lg" id="payNumberInput" name="pay_number" required>
                        </div>
                        <div id="payFeeError" class="alert alert-danger d-none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-credit-card me-1"></i>
                        Process Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    }

    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Section Titles */
    .section-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    /* Fee Cards */
    .fee-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .fee-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .fee-card-header {
        display: flex;
        align-items: center;
        padding: 1.5rem 1.5rem 1rem;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .fee-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
    }

    .registration-card .fee-icon {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .trip-card .fee-icon {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .tuition-card .fee-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .practical-card .fee-icon {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .fee-title h6 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
    }

    .fee-card-body {
        padding: 0 1.5rem 1.5rem;
    }

    .fee-amount {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .fee-amount span {
        font-size: 1rem;
        font-weight: 500;
        color: #64748b;
    }

    .fee-details {
        margin-bottom: 1.5rem;
    }

    .fee-detail-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .fee-detail-item.paid {
        color: #059669;
    }

    .fee-detail-item.remaining {
        color: #dc2626;
    }

    .fee-detail-item i {
        margin-right: 0.5rem;
        width: 16px;
    }

    .btn-pay {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-pay:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .paid-badge {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        width: 100%;
    }

    .progress-bar {
        height: 4px;
        background: #e2e8f0;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        transition: width 0.5s ease;
    }

    /* Live Class Alert */
    .live-class-alert {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
        display: flex;
        align-items: center;
        box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
    }

    .live-class-icon {
        font-size: 2rem;
        margin-right: 1.5rem;
    }

    .live-class-content {
        flex: 1;
    }

    .live-class-content h5 {
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .live-class-content p {
        margin-bottom: 0.25rem;
        font-size: 1.1rem;
    }

    .btn-join {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .btn-join:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .info-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .info-card-header h4 {
        margin: 0;
        color: #1e293b;
        font-weight: 600;
        font-size: 1.125rem;
    }

    .info-card-body {
        padding: 1.5rem;
        max-height: 400px;
        overflow-y: auto;
    }

    /* Announcement Items */
    .announcement-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .announcement-item:last-child {
        border-bottom: none;
    }

    .announcement-content h6 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .announcement-content p {
        color: #64748b;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .announcement-date {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    /* Exam Items */
    .exam-item {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .exam-item:last-child {
        border-bottom: none;
    }

    .exam-content {
        flex: 1;
    }

    .exam-content h6 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .exam-date {
        color: #64748b;
        font-size: 0.875rem;
        margin: 0;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-badge.completed {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.upcoming {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Payment Modal */
    .payment-modal .modal-header {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border-bottom: none;
    }

    .payment-modal .btn-close {
        filter: brightness(0) invert(1);
    }

    .payment-form .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: border-color 0.3s ease;
    }

    .payment-form .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .payment-form .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.5rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
        }

        .live-class-alert {
            flex-direction: column;
            text-align: center;
        }

        .live-class-icon {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .exam-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .exam-status {
            margin-top: 0.5rem;
        }
    }

    /* Custom Scrollbar */
    .info-card-body::-webkit-scrollbar {
        width: 4px;
    }

    .info-card-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .info-card-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    .info-card-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pay-fee-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('feeTypeInput').value = btn.getAttribute('data-feetype');
            document.getElementById('payAmountInput').value = btn.getAttribute('data-amount');
            var modal = new bootstrap.Modal(document.getElementById('payFeeModal'));
            modal.show();
        });
    });

    document.getElementById('payFeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var errorDiv = document.getElementById('payFeeError');
        var submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i>Processing...';
        submitBtn.disabled = true;
        
        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                fee_type: form.fee_type.value,
                pay_amount: form.pay_amount.value,
                pay_number: form.pay_number.value,
                pay_method: 'momo'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.payment_link) {
                window.location.href = data.payment_link;
            } else if (data.message) {
                errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + data.message;
                errorDiv.classList.remove('d-none');
            }
        })
        .catch(err => {
            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Payment failed. Please try again.';
            errorDiv.classList.remove('d-none');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = '<i class="bi bi-credit-card me-1"></i>Process Payment';
            submitBtn.disabled = false;
        });
    });
});

// NESA Registration Modal
function showNesaModal() {
    const modal = new bootstrap.Modal(document.getElementById('nesaModal'));
    modal.show();
    
    // Simulate checking NESA registration status
    setTimeout(() => {
        const nesaContent = document.getElementById('nesa-content');
        const nesaLoading = document.getElementById('nesa-loading');
        const nesaFooter = document.getElementById('nesa-modal-footer');
        const nesaBadge = document.getElementById('nesa-status-badge');
        
        // Simulate different scenarios (randomly for demo)
        const isRegistered = Math.random() > 0.5;
        
        if (isRegistered) {
            // Show registered status
            nesaContent.innerHTML = `
                <div class="alert alert-success">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1">NESA Registration Complete</h6>
                            <p class="mb-0">You are successfully registered with NESA for the current academic year.</p>
                        </div>
                    </div>
                </div>
                
                <div class="registration-info">
                    <h6 class="mb-3">Registration Details:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Registration Number:</strong> NESA-2024-${Math.floor(Math.random() * 10000)}</p>
                            <p><strong>Academic Year:</strong> 2024-2025</p>
                            <p><strong>Registration Date:</strong> ${new Date().toLocaleDateString()}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                            <p><strong>Examination Center:</strong> Kigali Center</p>
                            <p><strong>Next Exam:</strong> Final Examinations</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="mb-3">Available Downloads:</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" onclick="downloadNesaForm('registration')">
                            <i class="bi bi-download me-1"></i> Registration Form
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="downloadNesaForm('examination')">
                            <i class="bi bi-download me-1"></i> Examination Form
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="downloadNesaForm('certificate')">
                            <i class="bi bi-download me-1"></i> Registration Certificate
                        </button>
                    </div>
                </div>
            `;
            
            nesaFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="printNesaCertificate()">
                    <i class="bi bi-printer me-1"></i> Print Certificate
                </button>
            `;
            
            nesaBadge.className = 'badge bg-success';
            nesaBadge.textContent = 'Registered';
        } else {
            // Show not registered status with appeal option
            nesaContent.innerHTML = `
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1">NESA Registration Required</h6>
                            <p class="mb-0">You are not currently registered with NESA for this academic year.</p>
                        </div>
                    </div>
                </div>
                
                <div class="registration-info">
                    <h6 class="mb-3">Why NESA Registration is Important:</h6>
                    <ul>
                        <li>Required for national examinations</li>
                        <li>Validates your academic credentials</li>
                        <li>Enables certificate issuance</li>
                        <li>Required for further education opportunities</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-info">
                        <h6 class="mb-2">Registration Process:</h6>
                        <ol class="mb-0">
                            <li>Submit registration form with required documents</li>
                            <li>Pay registration fees</li>
                            <li>Wait for NESA approval</li>
                            <li>Receive registration confirmation</li>
                        </ol>
                    </div>
                </div>
            `;
            
            nesaFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="submitNesaAppeal()">
                    <i class="bi bi-envelope me-1"></i> Submit Appeal
                </button>
            `;
            
            nesaBadge.className = 'badge bg-warning text-dark';
            nesaBadge.textContent = 'Not Registered';
        }
        
        nesaLoading.classList.add('d-none');
        nesaContent.classList.remove('d-none');
    }, 1500);
}

// Download NESA Forms
function downloadNesaForm(type) {
    // Simulate form download
    alert(`Downloading ${type} form... This would normally download the actual PDF form.`);
}

// Print NESA Certificate
function printNesaCertificate() {
    window.print();
}

// Submit NESA Appeal
function submitNesaAppeal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('nesaModal'));
    modal.hide();
    
    // Show appeal form modal or redirect
    alert('Appeal form would open here. This would allow students to submit their NESA registration appeal with required documents.');
}

// Load Live Classes
function loadLiveClasses() {
    fetch('/api/live-classes/homepage')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('live-classes-container');
            
            if (data.liveClasses && data.liveClasses.length > 0) {
                container.innerHTML = data.liveClasses.map(liveClass => `
                    <div class="live-class-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">${liveClass.title}</h6>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    ${new Date(liveClass.start_time).toLocaleString()}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">Active</span>
                                <br>
                                <button class="btn btn-sm btn-primary mt-2" onclick="joinLiveClass(${liveClass.id})">
                                    <i class="bi bi-camera-video me-1"></i> Join
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-camera-video-off fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No live classes available right now</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading live classes:', error);
            document.getElementById('live-classes-container').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-danger">Error loading live classes</p>
                </div>
            `;
        });
}

// Join Live Class
function joinLiveClass(classId) {
    window.location.href = `/student/live-classes/${classId}/join`;
}

// Load live classes when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadLiveClasses();
});

</script>
@endsection