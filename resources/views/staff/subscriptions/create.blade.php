@extends('layouts.staff')

@section('title', 'Record Subscription Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Record Subscription Payment</h5>
                    <small class="text-muted">Record a subscription payment and automatically add tokens to student account</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('staff.subscriptions.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student *</label>
                                    <select name="student_id" id="student_id" class="form-select" required>
                                        <option value="">Select a student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" 
                                                    data-tokens="{{ $student->token_balance ?? 0 }}">
                                                {{ $student->full_name }} ({{ $student->reg_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subscription_id" class="form-label">Subscription Plan *</label>
                                    <select name="subscription_id" id="subscription_id" class="form-select" required>
                                        <option value="">Select a subscription</option>
                                        @foreach($subscriptions as $subscription)
                                            <option value="{{ $subscription->id }}" 
                                                    data-price="{{ $subscription->price }}"
                                                    data-tokens="{{ $subscription->tokens_included }}"
                                                    data-duration="{{ $subscription->duration_days }}">
                                                {{ $subscription->name }} - {{ number_format($subscription->tokens_included) }} tokens - {{ number_format($subscription->price, 0) }} RWF
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Amount Paid (RWF) *</label>
                                    <input type="number" name="amount_paid" id="amount_paid" 
                                           class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method *</label>
                                    <select name="payment_method" id="payment_method" class="form-select" required>
                                        <option value="">Select method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date *</label>
                                    <input type="date" name="payment_date" id="payment_date" 
                                           class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="payment_reference" class="form-label">Payment Reference</label>
                                    <input type="text" name="payment_reference" id="payment_reference" 
                                           class="form-control" placeholder="Transaction ID, receipt number, etc.">
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Details Preview -->
                        <div class="alert alert-info" id="subscription-preview" style="display: none;">
                            <h6>Subscription Details:</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Tokens to Add:</strong> <span id="preview-tokens">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Duration:</strong> <span id="preview-duration">0</span> days
                                </div>
                                <div class="col-md-4">
                                    <strong>Student Current Tokens:</strong> <span id="current-tokens">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.subscriptions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const subscriptionSelect = document.getElementById('subscription_id');
    const amountInput = document.getElementById('amount_paid');
    const preview = document.getElementById('subscription-preview');
    const previewTokens = document.getElementById('preview-tokens');
    const previewDuration = document.getElementById('preview-duration');
    const currentTokensSpan = document.getElementById('current-tokens');

    function updatePreview() {
        const selectedStudent = studentSelect.options[studentSelect.selectedIndex];
        const selectedSubscription = subscriptionSelect.options[subscriptionSelect.selectedIndex];

        if (selectedSubscription && selectedSubscription.value) {
            const tokens = selectedSubscription.dataset.tokens;
            const duration = selectedSubscription.dataset.duration;
            const price = selectedSubscription.dataset.price;

            previewTokens.textContent = tokens;
            previewDuration.textContent = duration;
            
            // Auto-fill amount if not already filled
            if (!amountInput.value && price) {
                amountInput.value = price;
            }

            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }

        // Update current tokens display
        if (selectedStudent && selectedStudent.value) {
            currentTokensSpan.textContent = selectedStudent.dataset.tokens || '0';
        } else {
            currentTokensSpan.textContent = '0';
        }
    }

    studentSelect.addEventListener('change', updatePreview);
    subscriptionSelect.addEventListener('change', updatePreview);
});
</script>
@endsection
@endsection
