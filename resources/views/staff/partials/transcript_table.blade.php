@php
    $student = $student ?? null;
    $payments = $payments ?? collect();
    $isAuthorizedUser = auth('staff')->check();
@endphp

<div class="transcript-container">
    <style>
        .transcript-container {
            font-size: 14px;
        }
        .transcript-container table {
            margin-bottom: 0;
        }
        .transcript-container .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    </style>

    @if($student)
        <div class="mb-3">
            <strong>Student:</strong> {{ $student->full_name ?? 'N/A' }}<br>
            <strong>Registration Number:</strong> {{ $student->reg_number ?? 'N/A' }}<br>
            <strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}<br>
            <strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}
        </div>
    @endif

    @if($payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
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
                            $meta = is_array($payment->meta) ? $payment->meta : (json_decode($payment->meta, true) ?: []);
                            $method = $meta['method'] ?? 'online';

                            $feeTypeMap = [
                                'registration_fee' => 'Registration',
                                'registration' => 'Registration',
                                'school_fee' => 'School Fee',
                                'tuition_fee' => 'School Fee',
                                'tuition' => 'School Fee',
                                'practical_fee' => 'Practical',
                                'practical' => 'Practical',
                                'trip_fee' => 'Trip',
                                'trip' => 'Trip',
                                'student_card' => 'Student Card',
                                'studentcard' => 'Student Card'
                            ];
                            $displayType = $feeTypeMap[$payment->type] ?? ucfirst(str_replace('_', ' ', $payment->type));
                            $displayDate = $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') : ($payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'N/A');
                            $termName = $payment->term ? $payment->term->name : 'No Term';
                        @endphp
                        <tr>
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
                                            onclick="openEditPaymentModal({{ $payment->id }}, {{ $student->id ?? 0 }})"
                                            title="Edit Transaction">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="confirmDeletePayment({{ $payment->id }}, {{ $student->id ?? 0 }})"
                                            title="Delete Transaction">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="4" class="text-end"><strong>Total Paid:</strong></td>
                        <td colspan="3"><strong class="text-success">{{ number_format($payments->sum('amount'), 2) }} RWF</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <i class="fas fa-receipt fa-2x mb-2"></i>
            <div>No payment records found for this student.</div>
        </div>
    @endif
</div>

@if($isAuthorizedUser)
    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editPaymentModalBody">
                    <div class="text-center">
                        <span class="spinner-border"></span> Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openEditPaymentModal(paymentId, studentId) {
        const url = `/transactions/${paymentId}/edit`;
        const modal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
        const body = document.getElementById('editPaymentModalBody');
        body.innerHTML = '<div class="text-center"><span class="spinner-border"></span> Loading...</div>';
        modal.show();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(resp => {
            if (!resp.ok) {
                throw new Error(`HTTP Error ${resp.status}: ${resp.statusText}`);
            }
            return resp.text();
        })
        .then(html => {
            body.innerHTML = html;
            const submitForm = body.querySelector('form');
            if (submitForm) {
                submitForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(submitForm);
                    const submitBtn = submitForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

                    fetch(submitForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(resp => resp.json())
                    .then(json => {
                        if (json.success) {
                            modal.hide();
                            alert('Transaction updated successfully!');
                            loadTranscript(studentId);
                        } else {
                            alert(json.message || 'Failed to update transaction');
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message);
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
    }

    function confirmDeletePayment(paymentId, studentId) {
        if (!confirm('Are you sure you want to delete this transaction? This action cannot be undone.')) {
            return;
        }

        const url = `/transactions/${paymentId}`;
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(resp => {
            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }
            return resp.json();
        })
        .then(json => {
            if (json.success) {
                alert('Transaction deleted successfully!');
                loadTranscript(studentId);
            } else {
                alert(json.message || 'Failed to delete transaction');
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
        });
    }
    </script>
@endif
