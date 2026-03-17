<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingSystemSeeder extends Seeder
{
    public function run()
    {
        // Create chart of accounts
        $accounts = [
            // Asset Accounts
            ['account_code' => '1001', 'account_name' => 'Cash', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'is_cash_account' => true, 'is_active' => true],
            ['account_code' => '1002', 'account_name' => 'Bank Account', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'is_cash_account' => false, 'is_active' => true],
            
            // Revenue Accounts
            ['account_code' => '4001', 'account_name' => 'Token Purchase Revenue', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'is_cash_account' => false, 'is_active' => true],
            ['account_code' => '4002', 'account_name' => 'Live Class Revenue', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'is_cash_account' => false, 'is_active' => true],
            ['account_code' => '4003', 'account_name' => 'Resource Upload Revenue', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'is_cash_account' => false, 'is_active' => true],
            ['account_code' => '4004', 'account_name' => 'Subscription Revenue', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'is_cash_account' => false, 'is_active' => true],
            
            // Expense Accounts
            ['account_code' => '5001', 'account_name' => 'Salaries and Wages', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'is_cash_account' => false, 'is_active' => true],
            ['account_code' => '5002', 'account_name' => 'Office Rent', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'is_cash_account' => false, 'is_active' => true],
            ['account_code' => '5003', 'account_name' => 'Utilities', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'is_cash_account' => false, 'is_active' => true],
            ['account_code' => '5004', 'account_name' => 'Marketing Expenses', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'is_cash_account' => false, 'is_active' => true],
        ];

        foreach ($accounts as $account) {
            DB::table('esa.accounts')->insert(array_merge($account, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Get account IDs
        $cashAccount = DB::table('esa.accounts')->where('account_code', '1001')->first();
        $tokenRevenueAccount = DB::table('esa.accounts')->where('account_code', '4001')->first();
        $liveClassRevenueAccount = DB::table('esa.accounts')->where('account_code', '4002')->first();
        $resourceRevenueAccount = DB::table('esa.accounts')->where('account_code', '4003')->first();
        $subscriptionRevenueAccount = DB::table('esa.accounts')->where('account_code', '4004')->first();
        $salariesExpenseAccount = DB::table('esa.accounts')->where('account_code', '5001')->first();
        $rentExpenseAccount = DB::table('esa.accounts')->where('account_code', '5002')->first();
        $utilitiesExpenseAccount = DB::table('esa.accounts')->where('account_code', '5003')->first();

        // Create sample journal entries for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // Revenue entries
            $this->createJournalEntry(
                $date->copy()->startOfMonth()->addDays(5),
                'Token Purchase Revenue - Monthly',
                [
                    ['account_id' => $cashAccount->id, 'debit_amount' => 50000, 'credit_amount' => 0],
                    ['account_id' => $tokenRevenueAccount->id, 'debit_amount' => 0, 'credit_amount' => 50000],
                ]
            );

            $this->createJournalEntry(
                $date->copy()->startOfMonth()->addDays(10),
                'Live Class Revenue - Monthly',
                [
                    ['account_id' => $cashAccount->id, 'debit_amount' => 75000, 'credit_amount' => 0],
                    ['account_id' => $liveClassRevenueAccount->id, 'debit_amount' => 0, 'credit_amount' => 75000],
                ]
            );

            $this->createJournalEntry(
                $date->copy()->startOfMonth()->addDays(15),
                'Resource Upload Revenue - Monthly',
                [
                    ['account_id' => $cashAccount->id, 'debit_amount' => 30000, 'credit_amount' => 0],
                    ['account_id' => $resourceRevenueAccount->id, 'debit_amount' => 0, 'credit_amount' => 30000],
                ]
            );

            $this->createJournalEntry(
                $date->copy()->startOfMonth()->addDays(20),
                'Subscription Revenue - Monthly',
                [
                    ['account_id' => $cashAccount->id, 'debit_amount' => 120000, 'credit_amount' => 0],
                    ['account_id' => $subscriptionRevenueAccount->id, 'debit_amount' => 0, 'credit_amount' => 120000],
                ]
            );

            // Expense entries
            $this->createJournalEntry(
                $date->copy()->startOfMonth()->addDays(25),
                'Monthly Salaries - Kigali Branch',
                [
                    ['account_id' => $salariesExpenseAccount->id, 'debit_amount' => 80000, 'credit_amount' => 0],
                    ['account_id' => $cashAccount->id, 'debit_amount' => 0, 'credit_amount' => 80000],
                ]
            );

            $this->createJournalEntry(
                $date->copy()->startOfMonth()->addDays(28),
                'Monthly Office Rent - Kigali Branch',
                [
                    ['account_id' => $rentExpenseAccount->id, 'debit_amount' => 35000, 'credit_amount' => 0],
                    ['account_id' => $cashAccount->id, 'debit_amount' => 0, 'credit_amount' => 35000],
                ]
            );

            $this->createJournalEntry(
                $date->copy()->endOfMonth()->subDays(2),
                'Monthly Utilities - Kigali Branch',
                [
                    ['account_id' => $utilitiesExpenseAccount->id, 'debit_amount' => 15000, 'credit_amount' => 0],
                    ['account_id' => $cashAccount->id, 'debit_amount' => 0, 'credit_amount' => 15000],
                ]
            );
        }

        $this->command->info('Accounting system seeded successfully!');
        $this->command->info('Created chart of accounts and sample journal entries for the last 6 months.');
    }

    private function createJournalEntry($date, $description, $items)
    {
        // Calculate total amount
        $totalAmount = collect($items)->sum('debit_amount');
        
        // Create journal entry
        $journalEntryId = DB::table('esa.journal_entries')->insertGetId([
            'reference_number' => 'JE-' . $date->format('Y-m') . '-' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT),
            'entry_date' => $date,
            'description' => $description,
            'total_amount' => $totalAmount,
            'status' => 'Posted',
            'posted_by' => 1, // Assuming admin user ID 1
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create journal items
        foreach ($items as $item) {
            DB::table('esa.journal_items')->insert([
                'journal_entry_id' => $journalEntryId,
                'account_id' => $item['account_id'],
                'debit_amount' => $item['debit_amount'],
                'credit_amount' => $item['credit_amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
