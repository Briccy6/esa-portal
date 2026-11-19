@extends('staff.layout')

@section('title', 'Record Cash Payment - ' . $student->full_name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-money-bill-wave me-2"></i>Record Cash Payment</h2>
            <p class="text-muted">{{ $student->full_name }} ({{ $student->reg_number }})</p>
        </div>
        <div>
            <a href="javascript:window.history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Student Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user me-2"></i>Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Full Name:</strong><br>{{ $student->full_name }}</p>
                            <p><strong>Registration Number:</strong><br>{{ $student->reg_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Course:</strong><br>{{ $student->course->course_name ?? 'N/A' }}</p>
                            <p><strong>Course Code:</strong><br>{{ $student->course->course_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Recording Form -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-receipt me-2"></i>Payment Details</h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm" action="{{ route('staff.students.pay-cash', $student) }}" method="POST" 
                          onsubmit="handlePaymentSubmit(event)">
                        @csrf
                        
                        <!-- Step 1: Select Term -->
                        <div class="mb-3">
                            <label class="form-label required">
                                <i class="fas fa-calendar-alt me-1"></i>Academic Term
                            </label>
                            <select name="term_id" id="termSelect" class="form-select" required 
                                    onchange="loadTermFees()">
                                <option value="">-- Select Term --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" 
                                            @if($selectedTerm && $selectedTerm->id == $term->id) selected @endif>
                                        {{ $term->name }} — {{ $term->academicYear->year ?? '' }}
                                        ({{ $term->start_date->format('M d') }} - {{ $term->end_date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select the term for which payment is being made</small>
                        </div>

                        <!-- Term Summary -->
                        <div id="termSummary" class="alert alert-info d-none mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Term Summary:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Total Fees: <strong id="totalTermFees">0</strong> RWF</li>
                                <li>Total Paid: <strong id="totalTermPaid">0</strong> RWF</li>
                                <li>Remaining: <strong id="totalTermRemaining">0</strong> RWF</li>
                            </ul>
                        </div>

                        <!-- Payment Summary (Breakdown by Fee Type) -->
                        <div id="paymentSummary" class="alert alert-success d-none mb-3">
                            <i class="fas fa-receipt me-2"></i>
                            <strong>Payment Summary:</strong>
                            <div id="paymentSummaryContent" class="mt-2"></div>
                        </div>

                        <!-- Step 2: Select Fee Type (Hidden Until Term Selected) -->
                        <div id="feeTypeContainer" class="mb-3 d-none">
                            <label class="form-label required">
                                <i class="fas fa-receipt me-1"></i>Fee Type
                            </label>
                            <select name="fee_type" id="feeTypeSelect" class="form-select" required 
                                    onchange="updateFeeInfo()">
                                <option value="">-- Select a Fee --</option>
                            </select>
                            <small class="text-muted">Select which fee type you are recording payment for</small>
                            <div id="feeInfo" class="mt-2"></div>
                        </div>

                        <!-- Step 3: Enter Amount (Hidden Until Fee Selected) -->
                        <div id="amountContainer" class="mb-3 d-none">
                            <label class="form-label required">
                                <i class="fas fa-money-bill me-1"></i>Amount to Pay (RWF)
                            </label>
                            <input type="number" name="amount_paid" id="amountInput" class="form-control form-control-lg"
                                   placeholder="Enter amount received from student"
                                   min="1" step="1" onchange="validateAmount()">
                            <small class="text-muted">Enter the exact amount received from the student</small>
                            <div id="amountError" class="text-danger small mt-1"></div>
                        </div>

                        <!-- Step 4: Notes (Optional) -->
                        <div id="notesContainer" class="mb-3 d-none">
                            <label class="form-label">Payment Notes (Optional)</label>
                            <textarea name="payment_notes" id="notesInput" class="form-control" rows="2"
                                      placeholder="Add any additional notes about this payment"></textarea>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loadingIndicator" class="text-center d-none mb-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="text-muted ms-2">Loading fees for selected term...</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-2">
                            <button type="button" onclick="location.href=document.referrer" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                                <i class="fas fa-check me-1"></i>Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-lightbulb me-2"></i>Instructions</h5>
                </div>
                <div class="card-body">
                    <ol class="small">
                        <li>Select the <strong>academic term</strong> for which payment is being made</li>
                        <li>Choose the <strong>fee type</strong> (tuition, registration, etc.)</li>
                        <li>Enter the <strong>exact amount</strong> received from the student in RWF</li>
                        <li>Optionally add <strong>payment notes</strong> (e.g., late payment, partial payment)</li>
                        <li>Click <strong>Record Payment</strong> to save the transaction</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Important</h5>
                </div>
                <div class="card-body small">
                    <ul>
                        <li>Amount must not exceed the remaining balance for the selected fee type</li>
                        <li>All amounts should be in Rwandan Francs (RWF)</li>
                        <li>The transaction will be immediately recorded in the system</li>
                        <li>Student will receive an email confirmation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load term fees via AJAX
async function loadTermFees() {
    const termSelect = document.getElementById('termSelect');
    const termId = termSelect.value;
    
    if (!termId) {
        // Hide all fields when term is cleared
        document.getElementById('termSummary').classList.add('d-none');
        document.getElementById('paymentSummary').classList.add('d-none');
        document.getElementById('feeTypeContainer').classList.add('d-none');
        document.getElementById('amountContainer').classList.add('d-none');
        document.getElementById('notesContainer').classList.add('d-none');
        document.getElementById('submitBtn').disabled = true;
        return;
    }

    // Show loading indicator
    document.getElementById('loadingIndicator').classList.remove('d-none');
    document.getElementById('feeTypeContainer').classList.add('d-none');
    document.getElementById('amountContainer').classList.add('d-none');
    document.getElementById('notesContainer').classList.add('d-none');

    try {
        const response = await fetch(`/staff/students/{{ $student->id }}/term-fees?term_id=${termId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            // Update summary
            document.getElementById('totalTermFees').textContent =
                new Intl.NumberFormat('en-US').format(Math.round(data.summary.total_fees));
            document.getElementById('totalTermPaid').textContent =
                new Intl.NumberFormat('en-US').format(Math.round(data.summary.total_paid));
            document.getElementById('totalTermRemaining').textContent =
                new Intl.NumberFormat('en-US').format(Math.round(data.summary.total_remaining));
            document.getElementById('termSummary').classList.remove('d-none');

            // Populate payment summary breakdown
            const summaryContent = document.getElementById('paymentSummaryContent');
            let summaryHtml = '<table class="table table-sm mb-0">';
            summaryHtml += '<thead><tr><th>Fee Type</th><th>Total</th><th>Paid</th><th>Remaining</th></tr></thead>';
            summaryHtml += '<tbody>';
            data.fees.forEach(fee => {
                const statusClass = fee.remaining === 0 ? 'text-success' : 'text-danger';
                summaryHtml += `<tr>
                    <td>${fee.name} ${fee.remaining === 0 ? '<span class="badge bg-success">Paid</span>' : ''}</td>
                    <td>${new Intl.NumberFormat('en-US').format(Math.round(fee.total))} RWF</td>
                    <td>${new Intl.NumberFormat('en-US').format(Math.round(fee.paid))} RWF</td>
                    <td class="${statusClass}">${fee.remaining === 0 ? '0' : new Intl.NumberFormat('en-US').format(Math.round(fee.remaining))} RWF</td>
                </tr>`;
            });
            summaryHtml += '</tbody></table>';
            summaryContent.innerHTML = summaryHtml;
            document.getElementById('paymentSummary').classList.remove('d-none');

            // Populate fee dropdown - exclude fully paid fees
            const feeSelect = document.getElementById('feeTypeSelect');
            feeSelect.innerHTML = '<option value="">-- Select a Fee --</option>';

            // Filter out fees that are fully paid (remaining = 0)
            const unpaidFees = data.fees.filter(fee => fee.remaining > 0);

            if (unpaidFees.length === 0) {
                feeSelect.innerHTML += '<option disabled>All fees have been paid for this term!</option>';
                document.getElementById('feeTypeContainer').classList.add('d-none');
            } else {
                unpaidFees.forEach(fee => {
                    const option = document.createElement('option');
                    option.value = fee.type;
                    option.textContent = `${fee.name} — Remaining: ${new Intl.NumberFormat('en-US').format(Math.round(fee.remaining))} RWF`;
                    option.dataset.total = fee.total;
                    option.dataset.paid = fee.paid;
                    option.dataset.remaining = fee.remaining;
                    feeSelect.appendChild(option);
                });
                document.getElementById('feeTypeContainer').classList.remove('d-none');
            }
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to load fees. Please try again.');
    } finally {
        document.getElementById('loadingIndicator').classList.add('d-none');
    }
}

// Update fee info when fee type changes
function updateFeeInfo() {
    const feeSelect = document.getElementById('feeTypeSelect');
    const feeType = feeSelect.value;
    const amountContainer = document.getElementById('amountContainer');
    const notesContainer = document.getElementById('notesContainer');
    const amountInput = document.getElementById('amountInput');
    const submitBtn = document.getElementById('submitBtn');
    const feeInfoDiv = document.getElementById('feeInfo');

    if (!feeType) {
        amountContainer.classList.add('d-none');
        notesContainer.classList.add('d-none');
        feeInfoDiv.innerHTML = '';
        submitBtn.disabled = true;
        return;
    }

    const option = feeSelect.querySelector(`option[value="${feeType}"]`);
    const total = parseFloat(option.dataset.total) || 0;
    const paid = parseFloat(option.dataset.paid) || 0;
    const remaining = parseFloat(option.dataset.remaining) || 0;

    // Show detailed fee breakdown info
    feeInfoDiv.innerHTML = `
        <div class="alert alert-info m-0 small">
            <div class="row g-2">
                <div class="col-6">
                    <strong>Total Fee:</strong><br>${new Intl.NumberFormat('en-US').format(Math.round(total))} RWF
                </div>
                <div class="col-6">
                    <strong>Already Paid:</strong><br>${new Intl.NumberFormat('en-US').format(Math.round(paid))} RWF
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <strong class="text-danger">Remaining Balance:</strong><br>
                <strong class="text-danger">${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF</strong>
            </div>
        </div>
    `;

    amountInput.max = remaining;
    amountInput.value = '';
    amountInput.placeholder = `Max: ${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF`;
    
    // Force show the containers
    amountContainer.classList.remove('d-none');
    notesContainer.classList.remove('d-none');
    
    // Scroll amount input into view if needed
    setTimeout(() => {
        amountInput.focus();
        amountInput.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
    
    submitBtn.disabled = true;
}

// Validate amount before submission
function validateAmount() {
    const amountInput = document.getElementById('amountInput');
    const feeSelect = document.getElementById('feeTypeSelect');
    const errorDiv = document.getElementById('amountError');
    const submitBtn = document.getElementById('submitBtn');

    const amount = parseFloat(amountInput.value) || 0;
    const option = feeSelect.querySelector(`option[value="${feeSelect.value}"]`);
    const remaining = parseFloat(option?.dataset.remaining) || 0;

    errorDiv.textContent = '';

    if (amount <= 0) {
        errorDiv.textContent = '⚠️ Amount must be greater than 0';
        submitBtn.disabled = true;
        return false;
    }

    if (amount > remaining) {
        errorDiv.textContent = `⚠️ Amount exceeds remaining balance of ${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF`;
        submitBtn.disabled = true;
        return false;
    }

    // Amount is valid
    errorDiv.textContent = '';
    submitBtn.disabled = false;
    return true;
}

// Handle payment form submission
async function handlePaymentSubmit(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('paymentForm');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
    
    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            alert('Payment recorded successfully!');
            window.location.href = data.redirect || document.referrer;
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while recording the payment.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
    }
}

// Auto-load term if pre-selected
window.addEventListener('DOMContentLoaded', function() {
    const termSelect = document.getElementById('termSelect');
    if (termSelect.value) {
        loadTermFees();
    }
});
</script>
@endsection
