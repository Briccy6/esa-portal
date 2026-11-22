<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeacherDaily;
use App\Models\TeacherDailyPayment;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class TeacherPaymentService
{
    /**
     * Calculate payment amount for a teacher daily record
     */
    public function calculatePayment(TeacherDaily $teacherDaily): float
    {
        $teacher = $teacherDaily->teacher;
        $paymentPeriod = $teacher->payment_period ?? 'monthly';
        $hoursStudied = $teacherDaily->hours_studied ?? 0;
        $salary = $teacher->salary ?? 0;

        return match ($paymentPeriod) {
            'hourly' => $salary * $hoursStudied,
            'daily' => $salary,
            'weekly' => ($salary / 5) * $hoursStudied, // Assuming 5 working days per week
            'monthly' => ($salary / 22) * $hoursStudied, // Assuming 22 working days per month
            default => 0,
        };
    }

    /**
     * Create or update payment record for a teacher daily
     */
    public function createPaymentRecord(TeacherDaily $teacherDaily): TeacherDailyPayment
    {
        $calculatedAmount = $this->calculatePayment($teacherDaily);

        return TeacherDailyPayment::updateOrCreate(
            ['teacher_daily_id' => $teacherDaily->id],
            [
                'teacher_id' => $teacherDaily->teacher_id,
                'calculated_amount' => $calculatedAmount,
                'status' => 'pending',
                'work_date' => $teacherDaily->date,
            ]
        );
    }

    /**
     * Approve payment (when DOS approves teacher daily)
     */
    public function approvePayment(TeacherDaily $teacherDaily, Staff $approver): TeacherDailyPayment
    {
        $payment = $this->createPaymentRecord($teacherDaily);
        $payment->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
        ]);

        return $payment;
    }

    /**
     * Record payment (when accountant pays)
     */
    public function recordPayment(TeacherDailyPayment $payment, float $amount, Staff $payer, string $notes = null): TeacherDailyPayment
    {
        $newAmountPaid = $payment->amount_paid + $amount;
        $remainingAmount = $payment->calculated_amount - $newAmountPaid;

        $status = match (true) {
            $remainingAmount <= 0 => 'paid',
            $newAmountPaid > 0 => 'partial',
            default => 'approved',
        };

        $payment->update([
            'amount_paid' => $newAmountPaid,
            'status' => $status,
            'paid_at' => $status === 'paid' ? now() : $payment->paid_at,
            'paid_by' => $status === 'paid' ? $payer->id : $payment->paid_by,
            'notes' => $notes,
        ]);

        // If fully paid, create expense record
        if ($status === 'paid') {
            $this->createExpenseRecord($payment, $payer);
        }

        return $payment;
    }

    /**
     * Create expense record for teacher payment
     */
    protected function createExpenseRecord(TeacherDailyPayment $payment, Staff $payer): void
    {
        $teacher = $payment->teacher;
        $teacherDaily = $payment->teacherDaily;

        \App\Models\Expense::create([
            'expense_date' => now()->toDateString(),
            'expense_type' => 'Teacher Salary Payment',
            'amount' => $payment->calculated_amount,
            'description' => "Payment to teacher {$teacher->teacher_name} for work on {$payment->work_date} ({$teacherDaily->hours_studied} hours)",
            'paid_by' => $payer->name,
            'paid_to' => $teacher->teacher_name,
            'paid_to_contact' => $teacher->telephone,
            'payment_method' => 'Bank Transfer',
            'branch_id' => $teacherDaily->branch_id,
            'term_id' => null,
        ]);
    }

    /**
     * Get pending payments for accountant
     */
    public function getPendingPaymentsForAccountant(Staff $accountant): \Illuminate\Database\Eloquent\Collection
    {
        $branchIds = $accountant->branches->pluck('id')->toArray();

        return TeacherDailyPayment::with(['teacher', 'teacherDaily'])
            ->where('status', 'approved')
            ->whereHas('teacherDaily', function ($query) use ($branchIds) {
                $query->whereIn('branch_id', $branchIds);
            })
            ->orderBy('work_date', 'desc')
            ->get();
    }

    /**
     * Get total pending payment amount for a teacher
     */
    public function getTeacherPendingPayments(Teacher $teacher): array
    {
        $payments = TeacherDailyPayment::where('teacher_id', $teacher->id)
            ->whereIn('status', ['approved', 'partial'])
            ->get();

        return [
            'total_amount' => $payments->sum('calculated_amount'),
            'total_paid' => $payments->sum('amount_paid'),
            'remaining' => $payments->sum('calculated_amount') - $payments->sum('amount_paid'),
            'count' => $payments->count(),
        ];
    }

    /**
     * Get unreviewed teacher dailies for DOS
     */
    public function getUnreviewedTeacherDailies(): \Illuminate\Database\Eloquent\Collection
    {
        return TeacherDaily::with(['teacher', 'course', 'lesson'])
            ->where('approval_status', 'pending')
            ->orderBy('date', 'desc')
            ->get();
    }
}
