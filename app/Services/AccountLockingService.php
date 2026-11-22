<?php

namespace App\Services;

use App\Models\AccountLockingSetting;
use App\Models\AccountLockStatus;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AccountLockingService
{
    /**
     * Check and enforce locking conditions for accountant
     */
    public function checkAccountantConditions(Staff $accountant)
    {
        $branchId = $accountant->branch_id;
        $branch = $accountant->branch;

        if (!$branch) {
            return ['status' => 'ok', 'message' => 'No branch assigned'];
        }

        $issues = [];

        // Check 1: Student count limit
        $studentCountLimit = AccountLockingSetting::getValue('accountant', 'student_count_limit', $branchId);
        if ($studentCountLimit) {
            $currentStudentCount = Student::where('branch_id', $branchId)
                ->whereNotIn('id', \App\Models\DismissedStudent::pluck('original_student_id')->toArray())
                ->count();

            $limit = $studentCountLimit['value'] ?? $studentCountLimit;

            if ($currentStudentCount >= $limit) {
                $issues[] = [
                    'type' => 'student_count_exceeded',
                    'message' => "Student count limit exceeded. Current: {$currentStudentCount}, Limit: {$limit}",
                    'details' => [
                        'current_count' => $currentStudentCount,
                        'limit' => $limit,
                    ],
                    'action_required' => 'contact IT staff to increase limit',
                ];
            }
        }

        // Check 2: Expense recording requirement
        $expenseDays = AccountLockingSetting::getValue('accountant', 'expense_recording_days', $branchId);
        if ($expenseDays) {
            $days = $expenseDays['value'] ?? 3; // Default 3 days
            $cutoffDate = Carbon::now()->subDays($days);

            $lastExpense = Expense::where('branch_id', $branchId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastExpense || $lastExpense->created_at->lt($cutoffDate)) {
                $daysSinceExpense = $lastExpense
                    ? Carbon::now()->diffInDays($lastExpense->created_at)
                    : $days + 1;

                if ($daysSinceExpense >= $days) {
                    // Lock the account
                    AccountLockStatus::lockUser(
                        'staff',
                        $accountant->id,
                        'expense_not_recorded',
                        [
                            'days_without_expense' => $daysSinceExpense,
                            'required_days' => $days,
                            'last_expense_date' => $lastExpense?->created_at,
                        ],
                        null // No auto-unlock
                    );

                    $issues[] = [
                        'type' => 'expense_not_recorded',
                        'message' => "No expense recorded for {$daysSinceExpense} days. Account will be locked.",
                        'details' => [
                            'days_without_expense' => $daysSinceExpense,
                            'required_days' => $days,
                        ],
                        'action_required' => 'record expenses immediately',
                        'lock_imminent' => true,
                    ];
                } else {
                    $issues[] = [
                        'type' => 'expense_recording_reminder',
                        'message' => "Please record expenses. Last recorded {$daysSinceExpense} days ago.",
                        'details' => [
                            'days_without_expense' => $daysSinceExpense,
                            'required_days' => $days,
                        ],
                        'action_required' => 'record expenses within ' . ($days - $daysSinceExpense) . ' days',
                        'warning' => true,
                    ];
                }
            }
        }

        // Check 3: Students with unpaid fees
        $showUnpaidReminder = AccountLockingSetting::getValue('accountant', 'show_unpaid_reminder', $branchId);
        if ($showUnpaidReminder) {
            $unpaidStudents = Student::where('branch_id', $branchId)
                ->whereNotIn('id', \App\Models\DismissedStudent::pluck('original_student_id')->toArray())
                ->whereDoesntHave('transactions', function($q) {
                    $q->where('status', 'successful');
                })
                ->count();

            if ($unpaidStudents > 0) {
                $issues[] = [
                    'type' => 'unpaid_students',
                    'message' => "{$unpaidStudents} students have not paid any fees.",
                    'details' => [
                        'unpaid_count' => $unpaidStudents,
                    ],
                    'action_required' => 'follow up with students for payments',
                    'warning' => true,
                ];
            }
        }

        return [
            'status' => empty($issues) ? 'ok' : 'issues_found',
            'issues' => $issues,
        ];
    }

    /**
     * Check and enforce locking conditions for student
     */
    public function checkStudentConditions(Student $student)
    {
        $issues = [];

        // Check payment percentage requirement
        $paymentPercentageThreshold = AccountLockingSetting::getValue(
            'student',
            'payment_percentage_threshold',
            $student->branch_id
        );

        if ($paymentPercentageThreshold) {
            $threshold = $paymentPercentageThreshold['value'] ?? 50; // Default 50%

            // Calculate student's payment percentage
            $totalFees = $this->calculateTotalFees($student);
            $paidAmount = $this->calculatePaidAmount($student);

            $paymentPercentage = $totalFees > 0
                ? ($paidAmount / $totalFees) * 100
                : 0;

            if ($paymentPercentage < $threshold) {
                // Lock the student account
                AccountLockStatus::lockUser(
                    'student',
                    $student->id,
                    'payment_percentage_not_met',
                    [
                        'current_percentage' => $paymentPercentage,
                        'required_percentage' => $threshold,
                        'total_fees' => $totalFees,
                        'paid_amount' => $paidAmount,
                    ],
                    null // No auto-unlock
                );

                $issues[] = [
                    'type' => 'payment_percentage_not_met',
                    'message' => "Payment percentage below requirement. Current: " . round($paymentPercentage, 2) . "%, Required: {$threshold}%",
                    'details' => [
                        'current_percentage' => $paymentPercentage,
                        'required_percentage' => $threshold,
                        'total_fees' => $totalFees,
                        'paid_amount' => $paidAmount,
                    ],
                    'action_required' => 'contact finance office or complete payment',
                    'lock_active' => true,
                ];
            }
        }

        return [
            'status' => empty($issues) ? 'ok' : 'issues_found',
            'issues' => $issues,
        ];
    }

    /**
     * Check if user is locked
     */
    public function isUserLocked($userType, $userId)
    {
        return AccountLockStatus::isLocked($userType, $userId);
    }

    /**
     * Get lock details for user
     */
    public function getLockDetails($userType, $userId)
    {
        return AccountLockStatus::getLockDetails($userType, $userId);
    }

    /**
     * Unlock user account
     */
    public function unlockUser($userType, $userId, $unlockedBy = null, $notes = null)
    {
        return AccountLockStatus::unlockUser($userType, $userId, $unlockedBy, $notes);
    }

    /**
     * Calculate total fees for a student
     */
    private function calculateTotalFees(Student $student)
    {
        // This should match your existing fee calculation logic
        // For now, return 0 - you'll need to implement based on your fee structure
        $totalFees = \DB::table('student_fees')
            ->where('student_id', $student->id)
            ->sum('amount');

        return $totalFees;
    }

    /**
     * Calculate paid amount for a student
     */
    private function calculatePaidAmount(Student $student)
    {
        $paidAmount = \DB::table('transactions')
            ->where('student_id', $student->id)
            ->where('status', 'successful')
            ->sum('amount');

        return $paidAmount;
    }

    /**
     * Get all locked accounts for IT dashboard
     */
    public function getLockedAccounts($role = null)
    {
        $query = AccountLockStatus::locked();

        if ($role) {
            $query->where('user_type', $role);
        }

        return $query->with('unlockedBy')->get();
    }
}
