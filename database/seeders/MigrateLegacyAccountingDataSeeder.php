<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\StudentTransaction;
use App\Models\Expense;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateLegacyAccountingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting migration of legacy accounting data...');

        // Check if there's existing data to migrate
        $studentTransactions = StudentTransaction::where('status', 'successful')->get();
        $expenses = Expense::all();

        if ($studentTransactions->isEmpty() && $expenses->isEmpty()) {
            $this->command->info('No legacy data found to migrate.');
            return;
        }

        // Get account mappings
        $accounts = $this->getAccountMappings();

        // Migrate student transactions
        $this->migrateStudentTransactions($studentTransactions, $accounts);

        // Migrate expenses
        $this->migrateExpenses($expenses, $accounts);

        $this->command->info('Legacy accounting data migration completed!');
    }

    private function getAccountMappings(): array
    {
        return [
            'cash' => Account::where('account_code', '1000')->first(),
            'bank' => Account::where('account_code', '1100')->first(),
            'tuition_revenue' => Account::where('account_code', '4000')->first(),
            'registration_revenue' => Account::where('account_code', '4010')->first(),
            'general_expenses' => Account::where('account_code', '5000')->first(),
        ];
    }

    private function migrateStudentTransactions($transactions, $accounts)
    {
        $this->command->info("Migrating {$transactions->count()} student transactions...");

        foreach ($transactions as $transaction) {
            try {
                DB::beginTransaction();

                // Determine revenue account based on transaction type
                $revenueAccount = $transaction->type === 'registration' 
                    ? $accounts['registration_revenue'] 
                    : $accounts['tuition_revenue'];

                // Use cash account as default
                $cashAccount = $accounts['cash'];

                if (!$revenueAccount || !$cashAccount) {
                    Log::warning("Required accounts not found for transaction {$transaction->id}");
                    DB::rollBack();
                    continue;
                }

                $data = [
                    'date' => $transaction->paid_at ? $transaction->paid_at : $transaction->created_at,
                    'description' => "Student {$transaction->type} payment - " . ($transaction->student ? $transaction->student->full_name : 'Unknown'),
                    'items' => [
                        [
                            'account_id' => $cashAccount->id,
                            'debit_amount' => $transaction->amount,
                            'credit_amount' => 0,
                            'description' => 'Cash received from student',
                            'reference_type' => 'student_transaction',
                            'reference_id' => $transaction->id,
                        ],
                        [
                            'account_id' => $revenueAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $transaction->amount,
                            'description' => 'Revenue from student payment',
                            'reference_type' => 'student_transaction',
                            'reference_id' => $transaction->id,
                        ]
                    ]
                ];

                AccountingService::createAndPostJournalEntry($data);
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to migrate student transaction {$transaction->id}: " . $e->getMessage());
                $this->command->error("Failed to migrate student transaction {$transaction->id}: " . $e->getMessage());
            }
        }
    }

    private function migrateExpenses($expenses, $accounts)
    {
        $this->command->info("Migrating {$expenses->count()} expenses...");

        foreach ($expenses as $expense) {
            try {
                DB::beginTransaction();

                $expenseAccount = $accounts['general_expenses'];
                $cashAccount = $accounts['cash'];

                if (!$expenseAccount || !$cashAccount) {
                    Log::warning("Required accounts not found for expense {$expense->id}");
                    DB::rollBack();
                    continue;
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

                AccountingService::createAndPostJournalEntry($data);
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to migrate expense {$expense->id}: " . $e->getMessage());
                $this->command->error("Failed to migrate expense {$expense->id}: " . $e->getMessage());
            }
        }
    }
}
