@extends('staff.partials.sidebar_sec')

@section('title', 'Payment Details')

@section('content')
<style>
    .payment-details-page {
        --primary: #0f766e;
        --primary-light: #14b8a6;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --bg-card: #ffffff;
        --bg-hover: #f8fafc;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .payment-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-item {
        padding: 1rem;
        background: var(--bg-hover);
        border-radius: 8px;
    }

    .info-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .amount-large {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        color: white;
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.875rem;
    }
</style>

<div class="container-fluid py-4 payment-details-page">
    <div class="page-header">
        <a href="{{ route('staff.accountant.teacher-payments.index') }}" class="btn btn-outline btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Back to Payments
        </a>
        <h1 class="page-title">Payment Details</h1>
    </div>

    <div class="payment-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Teacher</div>
                <div class="info-value">{{ $payment->teacher->teacher_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Telephone</div>
                <div class="info-value">{{ $payment->teacher->telephone ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Payment Period</div>
                <div class="info-value">{{ ucfirst($payment->teacher->payment_period ?? 'monthly') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Work Date</div>
                <div class="info-value">{{ $payment->work_date->format('Y-m-d') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Hours Studied</div>
                <div class="info-value">{{ $payment->teacherDaily->hours_studied ?? 0 }} hours</div>
            </div>
            <div class="info-item">
                <div class="info-label">Course</div>
                <div class="info-value">{{ $payment->teacherDaily->course->course_name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Lesson</div>
                <div class="info-value">{{ $payment->teacherDaily->lesson->title ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="info-item">
                    <div class="info-label">Calculated Amount</div>
                    <div class="amount-large">RWF {{ number_format($payment->calculated_amount, 0) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-item">
                    <div class="info-label">Amount Paid</div>
                    <div class="amount-large">RWF {{ number_format($payment->amount_paid, 0) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-item">
                    <div class="info-label">Remaining</div>
                    <div class="amount-large">RWF {{ number_format($payment->remaining_amount, 0) }}</div>
                </div>
            </div>
        </div>

        @if($payment->status !== 'paid')
            <form method="POST" action="{{ route('staff.accountant.teacher-payments.record', $payment->id) }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Payment Amount</label>
                            <input type="number" name="amount" class="form-control" 
                                   value="{{ $payment->remaining_amount }}" 
                                   min="0" max="{{ $payment->remaining_amount }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Notes (optional)</label>
                            <input type="text" name="notes" class="form-control" 
                                   placeholder="Payment notes">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cash"></i> Record Payment
                </button>
            </form>
        @else
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                This payment has been fully paid on {{ $payment->paid_at?->format('Y-m-d H:i:s') }}.
            </div>
        @endif
    </div>
</div>
@endsection
