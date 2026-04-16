@extends('students.app')

@section('title', 'My Report Cards')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="bi bi-file-earmark-text me-2"></i>My Report Cards
                </h1>
                <p class="page-subtitle">View and download your academic report cards</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Access Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-shield-check me-2"></i>Access Status</h4>
                </div>
                <div class="info-card-body">
                    @if($accessStatus['can_access'])
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Full Access Granted</strong><br>
                            <small class="text-muted">{{ $accessStatus['reason'] }}</small>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Access Restricted</strong><br>
                            <small class="text-muted">{{ $accessStatus['reason'] }}</small>
                        </div>
                        
                        @if(!$accessStatus['payment_status']['is_fully_paid'])
                            <div class="mt-3">
                                <h6>Payment Status:</h6>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $accessStatus['payment_status']['payment_percentage'] }}%"
                                         aria-valuenow="{{ $accessStatus['payment_status']['payment_percentage'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($accessStatus['payment_status']['payment_percentage'], 1) }}% Paid
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>Paid: {{ number_format($accessStatus['payment_status']['total_paid']) }} Rwf</span>
                                    <span>Required: {{ number_format($accessStatus['payment_status']['required_fees']) }} Rwf</span>
                                    <span>Outstanding: {{ number_format($accessStatus['payment_status']['outstanding']) }} Rwf</span>
                                </div>
                                <div class="mt-2">
                                    <button 
                                        id="reportCardPayBtn" 
                                        class="btn btn-primary"
                                        onclick="initiateReportCardPayment()"
                                    >
                                        <i class="bi bi-credit-card me-2"></i>Complete Payment
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @if($accessStatus['countdown_data'])
                            <div class="mt-3">
                                <h6>Time Until Available:</h6>
                                <div class="countdown-display-small">
                                    <span class="badge bg-primary me-1">
                                        {{ $accessStatus['countdown_data']['months'] }} months
                                    </span>
                                    <span class="badge bg-primary me-1">
                                        {{ $accessStatus['countdown_data']['weeks'] }} weeks
                                    </span>
                                    <span class="badge bg-primary me-1">
                                        {{ $accessStatus['countdown_data']['days'] }} days
                                    </span>
                                    <span class="badge bg-primary me-1">
                                        {{ $accessStatus['countdown_data']['hours'] }} hours
                                    </span>
                                    <span class="badge bg-primary me-1">
                                        {{ $accessStatus['countdown_data']['minutes'] }} minutes
                                    </span>
                                </div>
                                <small class="text-muted">
                                    Available on: {{ \Carbon\Carbon::parse($accessStatus['available_date'])->format('M j, Y g:i A') }}
                                </small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards List -->
    <div class="row">
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-collection me-2"></i>Your Report Cards</h4>
                </div>
                <div class="info-card-body">
                    @if($reportCards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Academic Year</th>
                                        <th>Term</th>
                                        <th>Course</th>
                                        <th>Average Marks</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportCards as $reportCard)
                                        <tr>
                                            <td>{{ $reportCard->academicYear->year ?? 'N/A' }}</td>
                                            <td>{{ $reportCard->term->name ?? 'N/A' }}</td>
                                            <td>{{ $reportCard->course->course_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($reportCard->canAccessThisTerm)
                                                    @if($reportCard->average_marks)
                                                        <span class="badge bg-{{ $reportCard->average_marks >= 70 ? 'success' : ($reportCard->average_marks >= 50 ? 'warning' : 'danger') }}">
                                                            {{ number_format($reportCard->average_marks, 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-info">Position: {{ $reportCard->position_display ?? 'N/A' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($reportCard->canAccessThisTerm)
                                                    @if($reportCard->status === 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($reportCard->status === 'printed')
                                                        <span class="badge bg-info">Printed</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($reportCard->canAccessThisTerm)
                                                        <a href="{{ route('student.report-cards.show', $reportCard) }}" 
                                                           class="btn btn-outline-primary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('student.report-cards.download', $reportCard) }}" 
                                                           class="btn btn-outline-success" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    @else
                                                        <button class="btn btn-outline-secondary" disabled title="Access Restricted">
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Report Cards Available</h5>
                            <p class="text-muted">
                                @if($accessStatus['can_access'])
                                    Your report cards haven't been generated yet. Check back later.
                                @else
                                    Complete your fee payments and wait for the report cards to be released by administration.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.countdown-display-small .badge {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}

.info-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.info-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-card-header h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.info-card-body {
    padding: 1.5rem;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #666;
    font-size: 1.1rem;
}
</style>

<!-- Flutterwave Script -->
<script src="https://checkout.flutterwave.com/v3.js"></script>

<script>
function initiateReportCardPayment() {
    const btn = document.getElementById('reportCardPayBtn');
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Processing...';

    // Get outstanding amount from the progress bar text
    const outstandingText = document.querySelector('.d-flex.justify-content-between.text-muted.small span:last-child')?.textContent;
    const outstandingAmount = outstandingText ? parseFloat(outstandingText.replace(/[^0-9.]/g, '')) : 0;

    if (outstandingAmount <= 0) {
        alert('No outstanding fees to pay.');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        return;
    }

    // Make request to initialize payment
    fetch('{{ route("flutterwave.payment.initialize-reportcard-fees") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            amount: outstandingAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to Flutterwave payment page
            window.location.href = data.redirect_url;
        } else {
            alert('Error: ' + (data.message || 'Failed to initialize payment'));
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}
</script>
@endsection
