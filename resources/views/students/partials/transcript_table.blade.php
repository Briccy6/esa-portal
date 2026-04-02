@php
    // Show edit/delete buttons to staff users only (you can narrow this later)
    $isAuthorizedUser = auth('staff')->check();

    $course = \App\Models\Course::find($student->course_id);
    $discount = $student->discount ?? 0;

    $registrationFee = $course ? floatval($course->registration_fee) : 0;
    $tuitionFee = $course ? floatval($course->tuition_fee) : 0;
    $practicalFee = $course ? floatval($course->practical_fee) : 0;
    $tripFee = $course ? floatval($course->trip_fee) : 0;
    $studentCardFee = 2000;

    $discountableFees = $tuitionFee + $practicalFee + $tripFee;
    $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;

    $totalToPay = $studentCardFee + $registrationFee + ($discountableFees - $discountAmount);
    $totalPaid = $payments->sum('amount');
    $outstanding = $totalToPay - $totalPaid;
@endphp

<style>
/* Print-specific styles to hide Actions column */
@media print {
    /* Hide the Actions column header */
    .transcript-container table th:nth-child(7),
    .transcript-container table td:nth-child(7) {
        display: none !important;
    }
    
    /* Hide the Edit Mode badge */
    .transcript-container .badge.bg-warning {
        display: none !important;
    }
    
    /* Ensure proper table formatting for print */
    .transcript-container table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .transcript-container table th,
    .transcript-container table td {
        border: 1px solid #000 !important;
        padding: 8px !important;
        text-align: left !important;
    }
    
    /* Remove card shadows and backgrounds for clean printing */
    .transcript-container .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        margin-bottom: 1rem !important;
    }
    
    .transcript-container .card-header {
        background: #f8f9fa !important;
        border-bottom: 1px solid #000 !important;
    }
    
    /* Ensure text is black for printing */
    .transcript-container * {
        color: #000 !important;
    }
    
    /* Hide modals and other interactive elements */
    .transcript-container .modal,
    .transcript-container button {
        display: none !important;
    }
}
</style>

<div class="transcript-container">
    <!-- Student Information -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Student Details</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $student->full_name }}</p>
                    <p class="mb-1"><strong>Reg Number:</strong> {{ $student->reg_number }}</p>
                    <p class="mb-1"><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Fee Summary</h6>
                    <p class="mb-1"><strong>Total Fees:</strong> <span class="badge bg-primary">{{ number_format($totalToPay, 2) }} RWF</span></p>
                    <p class="mb-1"><strong>Total Paid:</strong> <span class="badge bg-success">{{ number_format($totalPaid, 2) }} RWF</span></p>
                    <p class="mb-1"><strong>Outstanding:</strong> <span class="badge bg-warning text-dark">{{ number_format($outstanding, 2) }} RWF</span></p>
                    @if($discount > 0)
                        <p class="mb-0"><strong>Discount:</strong> <span class="badge bg-info">{{ $discount }}% ({{ number_format($discountAmount, 2) }} RWF)</span></p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Payment History</h6>
            @if($isAuthorizedUser)
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-key"></i> Edit Mode (staff)
                </span>
            @endif
        </div>

        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Term</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                @if($isAuthorizedUser)
                                    <th class="text-center">Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($payments as $index => $payment)
                                @php
                                    $meta = json_decode($payment->meta, true) ?: [];
                                    $method = $meta['method'] ?? 'online';

                                    $feeTypeMap = [
                                        'registration_fee' => 'Registration',
                                        'school_fee' => 'School Fee',
                                        'practical_fee' => 'Practical',
                                        'trip_fee' => 'Trip',
                                        'student_card' => 'Student Card'
                                    ];
                                    $displayType = $feeTypeMap[$payment->type] ?? ucfirst(str_replace('_', ' ', $payment->type));
                                    $displayDate = $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') : ($payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'N/A');
                                    $termName = $payment->term ? $payment->term->name : 'No Term';
                                @endphp
                                <tr id="payment-row-{{ $payment->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $displayDate }}</td>
                                    <td><span class="badge bg-info">{{ $termName }}</span></td>
                                    <td><span class="badge bg-primary">{{ $displayType }}</span></td>
                                    <td><strong>{{ number_format($payment->amount, 2) }} RWF</strong></td>
                                    <td>
                                        @if(strtolower($method) === 'cash')
                                            <span class="badge bg-success"><i class="fas fa-money-bill-wave"></i> Cash</span>
                                        @else
                                            <span class="badge bg-primary"><i class="fas fa-credit-card"></i> Online</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($payment->status, ['successful', 'paid']))
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>

                                    @if($isAuthorizedUser)
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning me-1"
                                                    onclick="openEditModal({{ $payment->id }})"
                                                    title="Edit Transaction">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDeleteTransaction({{ $payment->id }})"
                                                    title="Delete Transaction">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <div>No payment records found for this student.</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editTransactionModalBody">
                <div class="text-center">
                    <span class="spinner-border"></span> Loading...
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(paymentId) {
        // Use correct transaction route
        const url = '/transactions/' + paymentId + '/edit';
        const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
        const body = document.getElementById('editTransactionModalBody');
        body.innerHTML = '<div class="text-center"><span class="spinner-border"></span> Loading...</div>';
        modal.show();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(resp => {
            if (!resp.ok) throw new Error('Failed to load edit form');
            return resp.text();
        })
        .then(html => {
            body.innerHTML = html;

            // Add form submission handler
            const form = body.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                    fetch(form.action, {
                        method: 'PUT',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(resp => resp.json())
                    .then(json => {
                        if (json.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editTransactionModal'));
                            if (modal) modal.hide();

                            // Show success message
                            alert(json.message || 'Transaction updated successfully.');

                            // Reload page to show updated data
                            location.reload();
                        } else {
                            alert(json.message || 'Failed to update transaction.');
                        }
                    })
                    .catch(err => {
                        console.error('Error updating transaction:', err);
                        alert('An error occurred while updating the transaction.');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        })
        .catch(err => {
            body.innerHTML = '<div class="alert alert-danger">Unable to load edit form.</div>';
            console.error(err);
        });
    }

    function confirmDeleteTransaction(paymentId) {
        if (!confirm('Are you sure you want to delete this transaction? This action cannot be undone.')) {
            return;
        }

        const url = '/transactions/' + paymentId;
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(resp => resp.json())
        .then(json => {
            if (json.success) {
                const row = document.getElementById('payment-row-' + paymentId);
                if (row) row.remove();
                alert(json.message || 'Transaction deleted');
            } else {
                alert(json.message || 'Failed to delete transaction');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while deleting transaction.');
        });
    }
</script>
@endpush