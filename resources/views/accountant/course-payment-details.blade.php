@extends('staff.partials.sidebar_sec')

@section('title', 'Course Payment Details - ' . $course->course_name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-graduation-cap me-2"></i>{{ $course->course_name }} Payment Details</h2>
            <p class="text-muted">{{ $course->abbreviation }} - Complete payment breakdown by term</p>
        </div>
        <div>
            <a href="{{ route('staff.accountant.payment-schedule.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Schedule
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Course Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-info-circle me-2"></i>Course Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Course Code:</strong><br>
                    {{ $course->course_code }}
                </div>
                <div class="col-md-3">
                    <strong>Course Type:</strong><br>
                    {{ $course->course_type }}
                </div>
                <div class="col-md-3">
                    <strong>Total Students:</strong><br>
                    {{ $course->students->count() }}
                </div>
                <div class="col-md-3">
                    <strong>Active Terms:</strong><br>
                    {{ $course->activeTermFees()->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Term-wise Breakdown -->
    @foreach($termDetails as $termDetail)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5>
                <i class="fas fa-calendar me-2"></i>
                {{ $termDetail['term']->name }} - {{ $termDetail['term']->academicYear->year ?? '' }}
                ({{ $termDetail['term']->start_date->format('M Y') }} - {{ $termDetail['term']->end_date->format('M Y') }})
            </h5>
        </div>
        <div class="card-body">
            <!-- Term Summary -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $termDetail['total_students'] }}</h4>
                            <small>Total Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ $termDetail['paid_students'] }}</h4>
                            <small>Fully Paid</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>{{ $termDetail['partial_students'] }}</h4>
                            <small>Partial</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h4>{{ $termDetail['unpaid_students'] }}</h4>
                            <small>Unpaid</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($termDetail['total_expected'], 0) }}</h4>
                            <small>Expected (RWF)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($termDetail['total_collected'], 0) }}</h4>
                            <small>Collected (RWF)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Structure -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h6>Fee Structure:</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="badge bg-primary">Registration: {{ number_format($termDetail['term_fee']->registration_fee, 0) }} RWF</span>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-primary">Tuition: {{ number_format($termDetail['term_fee']->tuition_fee, 0) }} RWF</span>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-primary">Practical: {{ number_format($termDetail['term_fee']->practical_fee, 0) }} RWF</span>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-primary">Trip: {{ number_format($termDetail['term_fee']->trip_fee, 0) }} RWF</span>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-primary">Card: {{ number_format($termDetail['term_fee']->student_card_fee, 0) }} RWF</span>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-success">Total: {{ number_format($termDetail['term_fee']->total_fees, 0) }} RWF</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Payment Details -->
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Student</th>
                            <th>Reg Number</th>
                            <th>Total Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Last Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($termDetail['students'] as $studentPayment)
                        <tr>
                            <td>{{ $studentPayment['student']->full_name }}</td>
                            <td>{{ $studentPayment['student']->reg_number }}</td>
                            <td>
                                {{ number_format($studentPayment['paid_amount'], 0) }} RWF
                                <br>
                                <small class="text-muted">
                                    Reg: {{ number_format($studentPayment['paid_breakdown']['registration'], 0) }} |
                                    Tuition: {{ number_format($studentPayment['paid_breakdown']['tuition'], 0) }} |
                                    Practical: {{ number_format($studentPayment['paid_breakdown']['practical'], 0) }} |
                                    Trip: {{ number_format($studentPayment['paid_breakdown']['trip'], 0) }} |
                                    Card: {{ number_format($studentPayment['paid_breakdown']['student_card'], 0) }}
                                </small>
                            </td>
                            <td>
                                {{ number_format($studentPayment['remaining_amount'], 0) }} RWF
                            </td>
                            <td>
                                @if($studentPayment['payment_status'] === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($studentPayment['payment_status'] === 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @if($studentPayment['last_payment'])
                                    {{ $studentPayment['last_payment']->paid_at->format('M d, Y') }}
                                    <br>
                                    <small class="text-muted">{{ number_format($studentPayment['last_payment']->amount, 0) }} RWF</small>
                                @else
                                    <span class="text-muted">No payments</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.accountant.payment-schedule.student-details', $studentPayment['student']->id) }}"
                                       class="btn btn-outline-info" title="Student Details">
                                        <i class="fas fa-user"></i>
                                    </a>
                                    <button onclick="recordPayment({{ $studentPayment['student']->id }}, {{ $termDetail['term']->id }})" 
                                            class="btn btn-outline-success" title="Record Payment">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
function recordPayment(studentId, termId) {
    // Redirect to payment recording with pre-selected student and term
    window.location.href = `/students/${studentId}/pay-cash?term_id=${termId}`;
}
</script>

<style>
@media print {
    .btn, .card-header h5 {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
}
</style>
@endsection
