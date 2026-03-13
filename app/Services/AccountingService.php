<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    /**
     * Create a journal entry with double-entry validation.
     */
    public static function createJournalEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            // Validate required fields
            if (!isset($data['date']) || !isset($data['description']) || !isset($data['items'])) {
                throw new Exception('Missing required fields: date, description, items');
            }

            if (count($data['items']) < 2) {
                throw new Exception('Journal entry must have at least 2 items');
            }

            // Validate that items balance
            $totalDebits = collect($data['items'])->sum('debit_amount');
            $totalCredits = collect($data['items'])->sum('credit_amount');

            if (abs($totalDebits - $totalCredits) > 0.01) {
                throw new Exception("Journal entry must balance. Debits: {$totalDebits}, Credits: {$totalCredits}");
            }

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_date' => $data['date'],
                'reference_number' => $data['reference_number'] ?? JournalEntry::generateReferenceNumber(),
                'description' => $data['description'],
                'total_amount' => $totalDebits,
                'status' => 'Draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Create journal items
            foreach ($data['items'] as $item) {
                // Validate account exists and is active
                $account = Account::active()->find($item['account_id']);
                if (!$account) {
                    throw new Exception("Account {$item['account_id']} not found or inactive");
                }

                // Validate debit/credit amounts
                if ($item['debit_amount'] > 0 && $item['credit_amount'] > 0) {
                    throw new Exception('Journal item cannot have both debit and credit amounts');
                }

                if ($item['debit_amount'] == 0 && $item['credit_amount'] == 0) {
                    throw new Exception('Journal item must have either debit or credit amount');
                }

                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $item['account_id'],
                    'debit_amount' => $item['debit_amount'] ?? 0,
                    'credit_amount' => $item['credit_amount'] ?? 0,
                    'description' => $item['description'] ?? null,
                    'reference_type' => $item['reference_type'] ?? null,
                    'reference_id' => $item['reference_id'] ?? null,
                ]);
            }

            return $journalEntry;
        });
    }

    /**
     * Post a journal entry.
     */
    public static function postJournalEntry(JournalEntry $journalEntry, $postedBy = null): bool
    {
        return DB::transaction(function () use ($journalEntry, $postedBy) {
            if (!$journalEntry->isBalanced()) {
                throw new Exception('Cannot post unbalanced journal entry');
            }

            return $journalEntry->post($postedBy);
        });
    }

    /**
     * Create and post a journal entry in one transaction.
     */
    public static function createAndPostJournalEntry(array $data): JournalEntry
    {
        $journalEntry = self::createJournalEntry($data);
        self::postJournalEntry($journalEntry);
        return $journalEntry;
    }

    /**
     * Record student fee payment.
     */
    public static function recordStudentPayment($studentTransaction): JournalEntry
    {
        $cashAccount = Account::where('account_code', '1000')->first(); // Cash/Bank
        $tuitionRevenueAccount = Account::where('account_code', '4000')->first(); // Tuition Revenue
        $registrationRevenueAccount = Account::where('account_code', '4010')->first(); // Registration Revenue

        if (!$cashAccount || !$tuitionRevenueAccount || !$registrationRevenueAccount) {
            throw new Exception('Required accounts not found. Please setup chart of accounts.');
        }

        $revenueAccount = $studentTransaction->type === 'registration' 
            ? $registrationRevenueAccount 
            : $tuitionRevenueAccount;

        $data = [
            'date' => $studentTransaction->paid_at ?? now(),
            'description' => "Student {$studentTransaction->type} payment - {$studentTransaction->student->full_name}",
            'items' => [
                [
                    'account_id' => $cashAccount->id,
                    'debit_amount' => $studentTransaction->amount,
                    'credit_amount' => 0,
                    'description' => 'Cash received from student',
                    'reference_type' => 'student_transaction',
                    'reference_id' => $studentTransaction->id,
                ],
                [
                    'account_id' => $revenueAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $studentTransaction->amount,
                    'description' => 'Revenue from student payment',
                    'reference_type' => 'student_transaction',
                    'reference_id' => $studentTransaction->id,
                ]
            ]
        ];

        return self::createAndPostJournalEntry($data);
    }

    /**
     * Record expense payment.
     */
    public static function recordExpense($expense): JournalEntry
    {
        $cashAccount = Account::where('account_code', '1000')->first(); // Cash/Bank
        $expenseAccount = Account::where('account_code', '5000')->first(); // General Expenses

        if (!$cashAccount || !$expenseAccount) {
            throw new Exception('Required accounts not found. Please setup chart of accounts.');
        }

        $data = [
            'date' => $expense->expense_date,
            'description' => "Expense: {$expense->description} - Paid to {$expense->paid_to}",
            'items' => [
                [
                    'account_id' => $expenseAccount->id,
                    'debit_amount' => $expense->amount,
                    'credit_amount' => 0,
                    'description' => $expense->description,
                    'reference_type' => 'expense',
                    'reference_id' => $expense->id,
                ],
                [
                    'account_id' => $cashAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $expense->amount,
                    'description' => 'Cash paid for expense',
                    'reference_type' => 'expense',
                    'reference_id' => $expense->id,
                ]
            ]
        ];

        return self::createAndPostJournalEntry($data);
    }

    /**
     * Validate journal entry before posting.
     */
    public static function validateJournalEntry(array $items): array
    {
        $errors = [];
        $warnings = [];

        // Check if entry balances
        $totalDebits = collect($items)->sum('debit_amount');
        $totalCredits = collect($items)->sum('credit_amount');
        
        if (abs($totalDebits - $totalCredits) > 0.01) {
            $errors[] = "Entry does not balance. Debits: {$totalDebits}, Credits: {$totalCredits}";
        }

        // Validate each item
        foreach ($items as $index => $item) {
            $account = Account::find($item['account_id']);
            
            if (!$account) {
                $errors[] = "Item " . ($index + 1) . ": Account not found";
                continue;
            }

            if (!$account->is_active) {
                $errors[] = "Item " . ($index + 1) . ": Account is inactive";
            }

            // Check if amounts make sense for account type
            if ($item['debit_amount'] > 0 && $account->normal_balance === 'Credit') {
                $warnings[] = "Item " . ($index + 1) . ": Debiting a credit-normal account";
            }

            if ($item['credit_amount'] > 0 && $account->normal_balance === 'Debit') {
                $warnings[] = "Item " . ($index + 1) . ": Crediting a debit-normal account";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits
        ];
    }
}
