{{--
    resources/views/students/partials/payments_table.blade.php
    (also used as transcript_table.blade.php — same variable names)

    Variables expected:
        $payments  — collection of StudentTransaction
        $student   — Student model
--}}

<div class="alert-container-payments mb-3"></div>

@if($payments->count())
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0" id="paymentsTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Method</th>
                <th>Reference</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                @php
                    $meta   = is_array($payment->meta) ? $payment->meta : (json_decode($payment->meta, true) ?? []);
                    $method = $meta['method'] ?? '-';
                    $notes  = $meta['notes']  ?? '';
                @endphp

                <tr id="paymentRow{{ $payment->id }}">
                    <td class="text-muted small">{{ $loop->iteration }}</td>

                    <td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y') : '-' }}</td>

                    <td>{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</td>

                    <td class="fw-semibold">
                        {{ number_format($payment->amount, 0) }}
                        <small class="text-muted">{{ $payment->currency ?? 'RWF' }}</small>
                    </td>

                    <td>
                        <span class="badge bg-{{
                            in_array($payment->status, ['successful','paid'])
                                ? 'success'
                                : ($payment->status === 'pending' ? 'warning' : 'danger')
                        }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>

                    <td>{{ $method }}</td>

                    <td><small class="text-muted font-monospace">{{ $payment->tx_ref ?? '-' }}</small></td>

                    <td class="text-center" style="white-space:nowrap;">
                        @php
                            // Check if current user is the authorized accountant
                            $staff = auth('staff')->user();
                            $isAuthorizedAccountant = $staff && str_ends_with($staff->email, '@excellentservantacademy.com');
                        @endphp
                        
                        @if($isAuthorizedAccountant)
                            {{-- Edit — data-bs-toggle="modal" ONLY, no tooltip conflict --}}
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit Payment"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPaymentModal{{ $payment->id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Delete --}}
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger ms-1"
                                    title="Delete Payment"
                                    onclick="confirmDeletePayment({{ $payment->id }}, '{{ number_format($payment->amount, 0) }}', '{{ addslashes(ucfirst(str_replace('_', ' ', $payment->type))) }}')">
                                <i class="fas fa-trash"></i>
                            </button>

                            {{-- Hidden delete form — POST + _method=DELETE (Laravel method spoofing) --}}
                            <form id="deletePaymentForm{{ $payment->id }}"
                                  action="{{ route('staff.transactions.destroy', $payment->id) }}"
                                  method="POST"
                                  style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @else
                            <span class="text-muted small" title="Only the accountant can edit or delete payments">
                                <i class="fas fa-lock"></i> Restricted
                            </span>
                        @endif
                    </td>
                </tr>

                {{-- ═══════════════════════════════
                     EDIT MODAL for payment #{{ $payment->id }}
                ════════════════════════════════ --}}
                <div class="modal fade"
                     id="editPaymentModal{{ $payment->id }}"
                     tabindex="-1"
                     aria-labelledby="editPaymentLabel{{ $payment->id }}"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="editPaymentLabel{{ $payment->id }}">
                                    <i class="fas fa-edit me-2"></i>Edit Payment
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <form id="editPaymentForm{{ $payment->id }}"
                                  onsubmit="handleEditPayment(event, {{ $payment->id }})">
                                @csrf

                                <div class="modal-body">
                                    <div class="alert alert-secondary py-2 small mb-3">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Ref:</strong> {{ $payment->tx_ref ?? 'N/A' }}
                                        &nbsp;|&nbsp;
                                        <strong>Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('d M Y') : '-' }}
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Amount (RWF) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               name="amount"
                                               class="form-control"
                                               value="{{ $payment->amount }}"
                                               min="1" step="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Fee Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" class="form-select" required>
                                            @foreach([
                                                'registration_fee' => 'Registration Fee',
                                                'school_fee'       => 'School / Tuition Fee',
                                                'practical_fee'    => 'Practical Fee',
                                                'trip_fee'         => 'Trip Fee',
                                                'student_card'     => 'Student Card',
                                            ] as $val => $label)
                                                <option value="{{ $val }}" {{ $payment->type === $val ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Notes (Optional)</label>
                                        <textarea name="notes"
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Optional notes">{{ $notes }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit"
                                            class="btn btn-primary"
                                            id="editPaymentBtn{{ $payment->id }}">
                                        <i class="fas fa-save me-1"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @endforeach
        </tbody>

        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="3" class="text-end">Total Paid:</td>
                <td colspan="5">
                    {{ number_format($payments->whereIn('status', ['successful','paid'])->sum('amount'), 0) }} RWF
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@else
    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle me-2"></i>No payments found for this student.
    </div>
@endif

<script>
function showPaymentAlert(type, message) {
    const icons  = { success: 'check-circle', danger: 'exclamation-circle', warning: 'exclamation-triangle' };
    const titles = { success: 'Success', danger: 'Error', warning: 'Warning' };
    const html = `
        <div class="alert alert-${type} alert-dismissible fade show d-flex align-items-start" role="alert">
            <i class="fas fa-${icons[type] || 'info-circle'} me-2 mt-1 flex-shrink-0"></i>
            <div><strong>${titles[type] || ''}</strong> — ${message}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>`;

    let c = document.querySelector('.alert-container-payments');
    if (!c) {
        c = document.createElement('div');
        c.className = 'alert-container-payments mb-3';
        const t = document.getElementById('paymentsTable');
        if (t) t.before(c); else document.body.prepend(c);
    }
    c.innerHTML = html;
    c.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    setTimeout(() => { const a = c.querySelector('.alert'); if (a) { a.classList.remove('show'); setTimeout(() => a.remove(), 300); } }, 6000);
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// ── Edit Payment ──────────────────────────────────────────────────────────────
// Hits: PUT /staff/transactions/{id}  →  staff.transactions.update
// The controller method updateTransaction() is MISSING — see web.php fix below.
function handleEditPayment(event, paymentId) {
    event.preventDefault();
    const form      = event.target;
    const submitBtn = document.getElementById(`editPaymentBtn${paymentId}`);
    const amount    = parseFloat(form.querySelector('[name="amount"]').value);

    if (!amount || amount <= 0) {
        showPaymentAlert('danger', 'Please enter a valid amount greater than 0.');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    fetch(`/staff/transactions/${paymentId}`, {
        method: 'POST',   // POST + _method:PUT  (Laravel method spoofing via JSON body)
        headers: {
            'Content-Type':     'application/json',
            'Accept':           'application/json',
            'X-CSRF-TOKEN':     getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            _method: 'PUT',
            amount:  amount,
            type:    form.querySelector('[name="type"]').value,
            notes:   form.querySelector('[name="notes"]').value || '',
        }),
        credentials: 'same-origin',
    })
    .then(async r => {
        const ct = r.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await r.json();
        } else {
            const text = await r.text();
            try { data = JSON.parse(text); }
            catch { data = { success: r.ok, message: r.ok ? 'Payment updated.' : 'Update failed.' }; }
        }
        return { ok: r.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            bootstrap.Modal.getInstance(document.getElementById(`editPaymentModal${paymentId}`))?.hide();
            showPaymentAlert('success', data.message || 'Payment updated successfully.');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showPaymentAlert('danger', data.message || data.error || 'Failed to update payment.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save Changes';
        }
    })
    .catch(err => {
        console.error('Edit error:', err);
        showPaymentAlert('danger', 'Update failed. Check your connection and try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save Changes';
    });
}

// ── Delete Payment ────────────────────────────────────────────────────────────
// Hits: DELETE /staff/transactions/{id}  →  staff.transactions.destroy
// Controller method deleteTransaction() already exists and returns JSON — works as-is.
function confirmDeletePayment(paymentId, amount, type) {
    if (!confirm(`Delete this payment?\n\nType:    ${type}\nAmount:  ${amount} RWF\n\nThis cannot be undone.`)) return;

    const form = document.getElementById(`deletePaymentForm${paymentId}`);
    if (!form) { showPaymentAlert('danger', 'Delete form not found. Please refresh the page.'); return; }

    fetch(form.action, {
        method: 'POST',   // POST + hidden _method=DELETE field
        headers: {
            'Accept':           'application/json',
            'X-CSRF-TOKEN':     getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new FormData(form),
        credentials: 'same-origin',
    })
    .then(async r => {
        const ct = r.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await r.json();
        } else {
            const text = await r.text();
            try { data = JSON.parse(text); }
            catch { data = { success: r.ok, message: r.ok ? 'Payment deleted.' : 'Delete failed.' }; }
        }
        return { ok: r.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            const row = document.getElementById(`paymentRow${paymentId}`);
            if (row) { row.style.transition = 'opacity 0.4s'; row.style.opacity = '0'; setTimeout(() => row.remove(), 400); }
            showPaymentAlert('success', data.message || 'Payment deleted successfully.');
            setTimeout(() => window.location.reload(), 1800);
        } else {
            showPaymentAlert('danger', data.message || data.error || 'Failed to delete payment.');
        }
    })
    .catch(err => {
        console.error('Delete error:', err);
        showPaymentAlert('danger', 'Delete failed. Check your connection and try again.');
    });
}
</script>