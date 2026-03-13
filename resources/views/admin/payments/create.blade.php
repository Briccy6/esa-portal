@extends('admin.layout')
@section('title', 'Admin Payments Create')

@push('styles')
<style>
    /* Custom Professional Styling */
    .payment-creation-wrapper {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .form-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        background-size: 400% 400%;
        animation: gradientShift 4s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .form-header {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
        padding: 2.5rem 2rem 1.5rem;
        text-align: center;
        position: relative;
    }

    .form-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 3px;
    }

    .form-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .form-subtitle {
        color: #718096;
        font-size: 1rem;
        margin: 0;
        font-weight: 400;
    }

    .form-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.75rem;
        position: relative;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .label-icon {
        width: 18px;
        height: 18px;
        opacity: 0.7;
    }

    .form-control,
    .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.875rem 1rem;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        position: relative;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        transform: translateY(-2px);
    }

    .form-control:hover,
    .form-select:hover {
        border-color: #cbd5e0;
        transform: translateY(-1px);
    }

    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23667eea' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px 12px;
        padding-right: 3rem;
    }

    .form-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .optional-badge {
        background: linear-gradient(135deg, #ffeaa7, #fab1a0);
        color: #2d3748;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .input-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    .btn-primary:active {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    /* Floating label effect */
    .floating-label {
        position: relative;
    }

    .floating-label input,
    .floating-label select {
        padding-top: 1.25rem;
        padding-bottom: 0.5rem;
    }

    .floating-label label {
        position: absolute;
        top: 0.875rem;
        left: 1rem;
        color: #a0aec0;
        transition: all 0.3s ease;
        pointer-events: none;
        font-size: 1rem;
        font-weight: 400;
    }

    .floating-label input:focus + label,
    .floating-label input:not(:placeholder-shown) + label,
    .floating-label select:focus + label,
    .floating-label select:not([value=""]) + label {
        transform: translateY(-0.5rem) scale(0.85);
        color: #667eea;
        font-weight: 600;
    }

    /* Icons */
    .icon-user::before { content: "👤"; }
    .icon-target::before { content: "🎯"; }
    .icon-required::before { content: "⚡"; }
    .icon-money::before { content: "💰"; }
    .icon-discount::before { content: "🏷️"; }

    /* Responsive Design */
    @media (max-width: 768px) {
        .payment-creation-wrapper {
            padding: 1rem;
        }

        .form-card {
            margin: 0 1rem;
        }

        .form-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .form-title {
            font-size: 1.75rem;
        }

        .form-body {
            padding: 1.5rem;
        }

        .input-group {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .btn-primary {
            padding: 1.25rem 2rem;
        }
    }

    @media (max-width: 480px) {
        .form-title {
            font-size: 1.5rem;
        }

        .form-subtitle {
            font-size: 0.9rem;
        }

        .input-group {
            gap: 1rem;
        }
    }

    /* Loading Animation */
    .btn-primary.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-primary.loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
    }

    @keyframes spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }

    /* Success/Error states */
    .form-control.is-valid {
        border-color: #48bb78;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2348bb78' d='m2.3 6.73.94-.94 2.94 2.94L7.88 7 9 8.12 5.24 11.88z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px 16px;
    }

    .form-control.is-invalid {
        border-color: #f56565;
    }
</style>
@endpush

@section('content')
<div class="payment-creation-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title">Create Payment Type</h1>
            <p class="form-subtitle">Configure new payment settings for your organization</p>
        </div>

        <div class="form-body">
            <form method="POST" action="{{ route('admin.payments.store') }}" id="paymentForm">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">
                        <span class="icon-user"></span>
                        Payment Name
                    </label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           placeholder="Enter payment type name">
                </div>

                <div class="form-group">
                    <label for="applies_to" class="form-label">
                        <span class="icon-target"></span>
                        Applies To
                    </label>
                    <select id="applies_to" name="applies_to" class="form-select" required>
                        <option value="">Select user type</option>
                        <option value="student">👨‍🎓 Student</option>
                        <option value="teacher">👨‍🏫 Teacher</option>
                        <option value="staff">👨‍💼 Staff</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="is_required" class="form-label">
                        <span class="icon-required"></span>
                        Payment Required
                    </label>
                    <select id="is_required" name="is_required" class="form-select" required>
                        <option value="">Select requirement status</option>
                        <option value="1">✅ Yes - Mandatory</option>
                        <option value="0">🔵 No - Optional</option>
                    </select>
                </div>

                <div class="input-group">
                    <div class="form-group">
                        <label for="default_amount" class="form-label">
                            <span class="icon-money"></span>
                            Default Amount
                        </label>
                        <input type="number" id="default_amount" name="default_amount" 
                               class="form-control" required min="0" step="0.01" 
                               placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="default_discount" class="form-label">
                            <span class="icon-discount"></span>
                            Discount
                            <span class="optional-badge">Optional</span>
                        </label>
                        <input type="number" id="default_discount" name="default_discount" 
                               min="0" max="100" class="form-control" placeholder="0" step="0.01">
                        <small class="form-text">
                            💡 Enter discount percentage (0-100%). Leave empty for no discount.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Create Payment Type
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Enhanced form interactions
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Add loading state on form submission
    form.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = 'Creating Payment Type...';
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        });
    });
    
    // Amount formatting
    const amountInput = document.getElementById('default_amount');
    const discountInput = document.getElementById('default_discount');
    
    amountInput.addEventListener('input', function() {
        let value = parseFloat(this.value);
        if (value < 0) this.value = 0;
    });
    
    discountInput.addEventListener('input', function() {
        let value = parseFloat(this.value);
        if (value < 0) this.value = 0;
        if (value > 100) this.value = 100;
    });
});
</script>
@endsection