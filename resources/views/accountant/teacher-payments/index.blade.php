@extends('staff.partials.sidebar_sec')

@section('title', 'Teacher Payments')

@section('content')
<style>
    .teacher-payments-page {
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

    .page-subtitle {
        color: var(--text-secondary);
        margin: 0;
    }

    .payments-table {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: var(--bg-hover);
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--bg-hover);
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-approved {
        background: #fef3c7;
        color: #92400e;
    }

    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }

    .status-partial {
        background: #dbeafe;
        color: #1e40af;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
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

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="container-fluid py-4 teacher-payments-page">
    <div class="page-header">
        <h1 class="page-title">Teacher Payments</h1>
        <p class="page-subtitle">Review and process approved teacher payments</p>
    </div>

    @if($payments->isEmpty())
        <div class="empty-state">
            <i class="bi bi-cash-coin"></i>
            <h3>No Pending Payments</h3>
            <p>There are no approved teacher payments waiting to be processed.</p>
        </div>
    @else
        <div class="payments-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Work Date</th>
                        <th>Hours</th>
                        <th>Calculated Amount</th>
                        <th>Amount Paid</th>
                        <th>Remaining</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>
                                <strong>{{ $payment->teacher->teacher_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $payment->teacher->payment_period ?? 'monthly' }}</small>
                            </td>
                            <td>{{ $payment->work_date->format('Y-m-d') }}</td>
                            <td>{{ $payment->teacherDaily->hours_studied ?? 0 }} hours</td>
                            <td>RWF {{ number_format($payment->calculated_amount, 0) }}</td>
                            <td>RWF {{ number_format($payment->amount_paid, 0) }}</td>
                            <td>RWF {{ number_format($payment->remaining_amount, 0) }}</td>
                            <td>
                                <span class="status-badge status-{{ $payment->status }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.accountant.teacher-payments.show', $payment->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
