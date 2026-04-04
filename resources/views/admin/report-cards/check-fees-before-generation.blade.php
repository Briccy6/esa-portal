@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-money-check-alt"></i> Check Fees Before Generating Reports
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Summary Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Course</span>
                                    <span class="info-box-number">{{ $course->course_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Year</span>
                                    <span class="info-box-number">{{ $academicYear->year }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Students</span>
                                    <span class="info-box-number">{{ $totalStudents }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Unpaid Students</span>
                                    <span class="info-box-number">{{ $studentsWithOutstanding }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="mb-0">Financial Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <strong>Total Required Fees:</strong>
                                        </div>
                                        <div class="col-6 text-right">
                                            <span class="badge badge-info">RWF {{ number_format($totalRequired, 0) }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <strong>Total Paid:</strong>
                                        </div>
                                        <div class="col-6 text-right">
                                            <span class="badge badge-success">RWF {{ number_format($totalPaid, 0) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Total Outstanding:</strong>
                                        </div>
                                        <div class="col-6 text-right">
                                            <span class="badge badge-danger">RWF {{ number_format($totalOutstanding, 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="mb-0">Selected Terms</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        @foreach($terms as $term)
                                            <li class="mb-1">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <strong>{{ $term->name }}</strong>
                                                ({{ $term->start_date?->format('d M Y') }} - {{ $term->end_date?->format('d M Y') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Student Payment Status</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 15%">Student ID</th>
                                        <th style="width: 20%">Name</th>
                                        <th style="width: 12%" class="text-right">Required</th>
                                        <th style="width: 12%" class="text-right">Paid</th>
                                        <th style="width: 12%" class="text-right">Outstanding</th>
                                        <th style="width: 8%" class="text-center">Progress</th>
                                        <th style="width: 8%" class="text-center">Status</th>
                                        <th style="width: 12%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($studentFeeDetails as $index => $detail)
                                        <tr class="{{ $detail['outstanding'] > 0 ? 'bg-light-danger' : 'bg-light-success' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $detail['student_id'] }}</strong></td>
                                            <td>
                                                <strong>{{ $detail['full_name'] }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $detail['email'] }}</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-info">RWF {{ number_format($detail['required_fees'], 0) }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-success">RWF {{ number_format($detail['total_paid'], 0) }}</span>
                                            </td>
                                            <td class="text-right">
                                                @if($detail['outstanding'] > 0)
                                                    <span class="badge badge-danger">RWF {{ number_format($detail['outstanding'], 0) }}</span>
                                                @else
                                                    <span class="badge badge-success">Paid</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="progress progress-sm" style="height: .5rem;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: {{ $detail['payment_percentage'] }}%"
                                                         aria-valuenow="{{ $detail['payment_percentage'] }}"
                                                         aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small>{{ round($detail['payment_percentage'], 1) }}%</small>
                                            </td>
                                            <td class="text-center">
                                                @if($detail['eligible_for_report'])
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ready</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fas fa-exclamation"></i> Unpaid</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="collapse"
                                                        data-target="#payments-{{ $detail['student']['id'] }}"
                                                        title="View Payment History">
                                                    <i class="fas fa-history"></i> History
                                                </button>
                                                @if($detail['outstanding'] > 0)
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                            onclick="sendPaymentReminder({{ $detail['student']['id'] }}, '{{ $detail['full_name'] }}', {{ $detail['outstanding'] }})"
                                                            title="Send Payment Reminder Email">
                                                        <i class="fas fa-envelope"></i> Email
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- Payment History Row -->
                                        <tr>
                                            <td colspan="9">
                                                <div class="collapse" id="payments-{{ $detail['student']['id'] }}">
                                                    <div class="card card-sm bg-light">
                                                        <div class="card-body">
                                                            @if($detail['payments']->count() > 0)
                                                                <h6><i class="fas fa-receipt"></i> Payment History</h6>
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <thead>
                                                                        <tr class="bg-light-secondary">
                                                                            <th style="width: 20%">Date</th>
                                                                            <th style="width: 20%">Amount</th>
                                                                            <th style="width: 20%">Status</th>
                                                                            <th style="width: 40%">Transaction Ref</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($detail['payments'] as $payment)
                                                                            <tr>
                                                                                <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                                                                <td><strong>RWF {{ number_format($payment->amount, 0) }}</strong></td>
                                                                                <td>
                                                                                    @if($payment->status === 'successful')
                                                                                        <span class="badge badge-success">✓ {{ ucfirst($payment->status) }}</span>
                                                                                    @elseif($payment->status === 'pending')
                                                                                        <span class="badge badge-warning">⏳ {{ ucfirst($payment->status) }}</span>
                                                                                    @else
                                                                                        <span class="badge badge-danger">✗ {{ ucfirst($payment->status) }}</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td><code>{{ $payment->tx_ref ?? 'N/A' }}</code></td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @else
                                                                <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> No payment history available for selected terms</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No students found in this class</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.report-cards.create') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Report Card Form
                                </a>

                                @if($studentsWithOutstanding > 0)
                                    <button type="button" class="btn btn-warning" onclick="bulkSendPaymentReminders()">
                                        <i class="fas fa-envelope"></i> Send Reminders to All Unpaid Students
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('admin.report-cards.generate-class') }}" class="d-inline"
                                      id="generateForm">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                                    @foreach($termIds as $termId)
                                        <input type="hidden" name="term_ids[]" value="{{ $termId }}">
                                    @endforeach

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i>
                                        Proceed to Generate Reports
                                        @if($studentsWithOutstanding > 0)
                                            ({{ $totalStudents - $studentsWithOutstanding }}/{{ $totalStudents }} eligible)
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Payment Reminder Modal -->
<div class="modal fade" id="paymentReminderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Payment Reminder</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <p id="reminderText"></p>
                <div class="form-group">
                    <label>Message to Include (optional):</label>
                    <textarea class="form-control" id="customMessage" rows="3"
                              placeholder="Leave blank to use default message"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmSendReminder()">Send Email</button>
            </div>
        </div>
    </div>
</div>

<script>
let pendingStudentId = null;
let pendingStudentName = null;
let pendingOutstanding = null;

function sendPaymentReminder(studentId, studentName, outstanding) {
    pendingStudentId = studentId;
    pendingStudentName = studentName;
    pendingOutstanding = outstanding;

    const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        useGrouping: true
    }).format(outstanding);
    const reminderText = `Send a payment reminder to <strong>${studentName}</strong> for outstanding fees of <strong>RWF ${formatted}</strong>?`;
    document.getElementById('reminderText').innerHTML = reminderText;
    document.getElementById('customMessage').value = '';

    $('#paymentReminderModal').modal('show');
}

function confirmSendReminder() {
    const customMessage = document.getElementById('customMessage').value;

    // Format the outstanding amount
    const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        useGrouping: true
    }).format(pendingOutstanding);

    // Here you would send the email via AJAX
    // For now, just show a notification
    alert(`Payment reminder would be sent to ${pendingStudentName} with outstanding amount RWF ${formatted}`);

    $('#paymentReminderModal').modal('hide');
}

function bulkSendPaymentReminders() {
    if (confirm('Send payment reminders to all {{ $studentsWithOutstanding }} students with outstanding fees?')) {
        alert(`Bulk payment reminders would be sent to all unpaid students`);
        // Here you would send bulk emails
    }
}
</script>
@endsection
