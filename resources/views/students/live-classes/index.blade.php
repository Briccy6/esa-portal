@extends('students.app')

@section('title', 'My Live Classes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="section-title">
                    <i class="bi bi-broadcast me-2"></i>
                    My Live Classes
                </h3>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            {{ session('info') }}
        </div>
    @endif

    @if($liveClasses->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-camera-video-off fs-1 text-muted mb-3"></i>
                        <h4>No Live Classes Scheduled</h4>
                        <p class="text-muted">There are no live classes scheduled for your course at the moment.</p>
                        <p class="text-muted">Check back later or contact your instructor for more information.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($liveClasses as $liveClass)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 live-class-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $liveClass->title }}</h5>
                            @switch($liveClass->status)
                                @case('scheduled')
                                    <span class="badge bg-warning">Scheduled</span>
                                    @break
                                @case('active')
                                    <span class="badge bg-success">Live Now</span>
                                    @break
                                @case('ended')
                                    <span class="badge bg-secondary">Ended</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $liveClass->status }}</span>
                            @endswitch
                        </div>
                        <div class="card-body">
                            @if($liveClass->description)
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($liveClass->description, 100) }}</p>
                            @endif

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-book"></i>
                                    {{
                                        ($liveClass->courses?->pluck('course_name')?->filter()?->implode(', '))
                                        ?: ($liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('course.course_name') ?? collect())?->filter()?->unique()?->implode(', ') ?? 'N/A')
                                    }}
                                </small>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i>
                                    {{
                                        ($liveClass->teacher?->teacher_name)
                                        ?: ($liveClass->lessons?->flatMap(fn($l) => $l->branchCourses?->pluck('teacher.teacher_name') ?? collect())?->filter()?->unique()?->implode(', ') ?? 'No teacher assigned')
                                    }}
                                </small>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> 
                                    {{ $liveClass->start_time->format('M j, Y H:i') }} - 
                                    {{ $liveClass->end_time->format('H:i') }}
                                </small>
                            </div>

                            @if($liveClass->access_type === 'public' && $liveClass->public_fee > 0)
                                <div class="mb-2">
                                    <small class="text-info">
                                        <i class="bi bi-currency-exchange"></i> 
                                        Public Fee: {{ $liveClass->currency }} {{ number_format($liveClass->public_fee, 2) }}
                                    </small>
                                </div>
                            @endif

                            @if($liveClass->max_participants)
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-people"></i> 
                                        {{ $liveClass->getActiveParticipantsCount() ?? 0 }}/{{ $liveClass->max_participants }} participants
                                    </small>
                                </div>
                            @endif

                            @if($liveClass->status === 'active')
                                <div class="alert alert-success py-2">
                                    <i class="bi bi-broadcast-tower"></i> This class is currently live!
                                </div>
                            @elseif($liveClass->start_time->isFuture())
                                <div class="alert alert-info py-2">
                                    <i class="bi bi-clock"></i> Starts in {{ $liveClass->start_time->diffForHumans() }}
                                </div>
                            @endif

                            @if($liveClass->isFull())
                                <div class="alert alert-warning py-2">
                                    <i class="bi bi-people"></i> Class is full
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            @php
                                $student = auth('student')->user();
                                $canJoin = $liveClass->canStudentJoin($student);
                            @endphp

                            @if($liveClass->status === 'active' && $canJoin['allowed'])
                                <button type="button" class="btn btn-success btn-block" onclick="checkAndJoin({{ $liveClass->id }})">
                                    <i class="bi bi-camera-video"></i> Join Live Class
                                </button>
                            @elseif($liveClass->status === 'active' && isset($canJoin['can_rejoin']) && $canJoin['can_rejoin'])
                                <button type="button" class="btn btn-primary btn-block" onclick="checkAndJoin({{ $liveClass->id }})">
                                    <i class="bi bi-arrow-return-left"></i> Rejoin Live Class
                                </button>
                            @elseif($liveClass->status === 'active' && isset($canJoin['requires_payment']))
                                <div class="payment-required">
                                    <div class="alert alert-warning py-2 mb-2">
                                        <i class="bi bi-currency-exchange"></i> Payment Required
                                    </div>
                                    <div class="payment-info mb-2">
                                        <small class="text-muted">
                                            Fee: {{ $liveClass->currency }} {{ number_format($canJoin['payment_amount'], 2) }}
                                        </small>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-primary" onclick="makePayment({{ $liveClass->id }}, {{ $canJoin['payment_amount'] }}, '{{ $canJoin['currency'] }}')">
                                            <i class="bi bi-credit-card"></i> Pay Now
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="contactSupport()">
                                            <i class="bi bi-envelope"></i> Contact Support
                                        </button>
                                    </div>
                                </div>
                            @elseif($liveClass->status === 'active' && isset($canJoin['payment_mismatch']))
                                <div class="payment-mismatch">
                                    <div class="alert alert-danger py-2 mb-2">
                                        <i class="bi bi-exclamation-triangle"></i> Payment Mismatch
                                    </div>
                                    <div class="payment-info mb-2">
                                        <small class="text-muted">
                                            Amount Paid: {{ $canJoin['currency'] }} {{ number_format($canJoin['amount_paid'], 2) }}<br>
                                            Required: {{ $canJoin['currency'] }} {{ number_format($canJoin['payment_amount'], 2) }}<br>
                                            Remaining: {{ $canJoin['currency'] }} {{ number_format($canJoin['remaining_amount'], 2) }}
                                        </small>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-primary" onclick="makePayment({{ $liveClass->id }}, {{ $canJoin['remaining_amount'] }}, '{{ $canJoin['currency'] }}')">
                                            <i class="bi bi-credit-card"></i> Pay Remaining
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="contactSupport()">
                                            <i class="bi bi-envelope"></i> Contact Support
                                        </button>
                                    </div>
                                </div>
                            @elseif($liveClass->status === 'active' && isset($canJoin['requires_approval']))
                                <a href="{{ route('live-classes.request', $liveClass) }}" class="btn btn-warning btn-block">
                                    <i class="bi bi-hand-paper"></i> Request Access
                                </a>
                            @elseif($liveClass->status === 'active' && !$canJoin['allowed'])
                                <button class="btn btn-outline-secondary btn-block" disabled>
                                    <i class="bi bi-ban"></i> {{ $canJoin['reason'] }}
                                </button>
                            @elseif($liveClass->start_time->isFuture())
                                <button class="btn btn-outline-secondary btn-block" disabled>
                                    <i class="bi bi-clock"></i> Not Started Yet
                                </button>
                            @else
                                <button class="btn btn-outline-secondary btn-block" disabled>
                                    <i class="bi bi-archive"></i> Class Ended
                                </button>
                            @endif

                            @if(isset($canJoin['current_percentage']))
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Your payment: {{ $canJoin['current_percentage'] }}% 
                                        (Required: {{ $canJoin['required_percentage'] }}%)
                                    </small>
                                </div>
                            @endif

                            <a href="{{ route('student.live-classes.show', $liveClass) }}" class="btn btn-outline-info btn-block mt-2">
                                <i class="bi bi-info-circle"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $liveClasses->links() }}
        </div>
    @endif
</div>

<script>
function checkAndJoin(classId) {
    fetch(`/api/live-classes/${classId}/check-access`)
        .then(response => response.json())
        .then(data => {
            if (data.canJoin) {
                window.location.href = `/student/live-classes/${classId}/join`;
            } else if (data.requiresPayment) {
                makePayment(classId, data.paymentAmount, data.currency);
            } else {
                alert(data.reason || 'Unable to join class');
            }
        })
        .catch(error => {
            console.error('Error checking access:', error);
            alert('Error checking access. Please try again.');
        });
}

function makePayment(classId, amount, currency) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/student/live-classes/${classId}/payment`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

function contactSupport() {
    alert('Please contact our support team for assistance with payment issues.\n\nEmail: support@esa.ac.rw\nPhone: +250 788 123 456');
}
</script>
@endsection
