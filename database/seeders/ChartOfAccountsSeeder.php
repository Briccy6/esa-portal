<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Assets (1000-1999)
            ['code' => '1000', 'name' => 'Cash and Cash Equivalents', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => true, 'cash_flow' => 'Operating'],
            ['code' => '1100', 'name' => 'Bank Accounts', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => true, 'cash_flow' => 'Operating'],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '1300', 'name' => 'Student Fees Receivable', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '1400', 'name' => 'Prepaid Expenses', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '1500', 'name' => 'Furniture and Fixtures', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Investing'],
            ['code' => '1600', 'name' => 'Computer Equipment', 'type' => 'Asset', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Investing'],
            ['code' => '1700', 'name' => 'Accumulated Depreciation', 'type' => 'Asset', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Non-cash'],
            
            // Liabilities (2000-2999)
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'Liability', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '2200', 'name' => 'Unearned Revenue', 'type' => 'Liability', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '2300', 'name' => 'Taxes Payable', 'type' => 'Liability', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            
            // Equity (3000-3999)
            ['code' => '3000', 'name' => 'Owner\'s Capital', 'type' => 'Equity', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Financing'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'Equity', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Non-cash'],
            ['code' => '3200', 'name' => 'Drawings', 'type' => 'Equity', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Financing'],
            
            // Revenue (4000-4999)
            ['code' => '4000', 'name' => 'Tuition Fee Revenue', 'type' => 'Revenue', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '4010', 'name' => 'Registration Fee Revenue', 'type' => 'Revenue', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '4020', 'name' => 'Examination Fee Revenue', 'type' => 'Revenue', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '4030', 'name' => 'Book and Material Revenue', 'type' => 'Revenue', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '4040', 'name' => 'Other Income', 'type' => 'Revenue', 'normal_balance' => 'Credit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            
            // Expenses (5000-5999)
            ['code' => '5000', 'name' => 'General Expenses', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5010', 'name' => 'Salaries and Wages', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5020', 'name' => 'Rent Expense', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5030', 'name' => 'Utilities Expense', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5040', 'name' => 'Office Supplies', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5050', 'name' => 'Marketing and Advertising', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5060', 'name' => 'Maintenance and Repairs', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5070', 'name' => 'Transportation Expense', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5080', 'name' => 'Professional Fees', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Operating'],
            ['code' => '5090', 'name' => 'Depreciation Expense', 'type' => 'Expense', 'normal_balance' => 'Debit', 'cash_account' => false, 'cash_flow' => 'Non-cash'],
        ];

        foreach ($accounts as $account) {
            Account::create([
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'account_type' => $account['type'],
                'normal_balance' => $account['normal_balance'],
                'is_cash_account' => $account['cash_account'],
                'cash_flow_category' => $account['cash_flow'],
                'description' => $this->getAccountDescription($account['name'], $account['type']),
                'is_active' => true,
                'level' => 1
            ]);
        }

        $this->command->info('Chart of Accounts seeded successfully!');
    }

    private function getAccountDescription($name, $type): string
    {
        $descriptions = [
            'Cash and Cash Equivalents' => 'Physical cash and cash equivalents held by the school',
            'Bank Accounts' => 'Funds held in bank accounts',
            'Accounts Receivable' => 'Amounts owed to the school by various parties',
            'Student Fees Receivable' => 'Tuition and other fees owed by students',
            'Prepaid Expenses' => 'Expenses paid in advance',
            'Furniture and Fixtures' => 'Office and classroom furniture and fixtures',
            'Computer Equipment' => 'Computers, printers, and other IT equipment',
            'Accumulated Depreciation' => 'Cumulative depreciation of fixed assets',
            'Accounts Payable' => 'Amounts owed to suppliers and vendors',
            'Accrued Expenses' => 'Expenses incurred but not yet paid',
            'Unearned Revenue' => 'Revenue received but not yet earned',
            'Taxes Payable' => 'Taxes owed to government authorities',
            'Owner\'s Capital' => 'Capital invested by the owner(s)',
            'Retained Earnings' => 'Accumulated profits retained in the business',
            'Drawings' => 'Withdrawals made by the owner(s)',
            'Tuition Fee Revenue' => 'Revenue from student tuition fees',
            'Registration Fee Revenue' => 'Revenue from student registration fees',
            'Examination Fee Revenue' => 'Revenue from examination fees',
            'Book and Material Revenue' => 'Revenue from sale of books and materials',
            'Other Income' => 'Miscellaneous income not classified elsewhere',
            'General Expenses' => 'General operating expenses',
            'Salaries and Wages' => 'Staff salaries and wage expenses',
            'Rent Expense' => 'Rent for school premises',
            'Utilities Expense' => 'Electricity, water, and other utility expenses',
            'Office Supplies' => 'Office supplies and stationery',
            'Marketing and Advertising' => 'Marketing and advertising expenses',
            'Maintenance and Repairs' => 'Building and equipment maintenance',
            'Transportation Expense' => 'Vehicle and transportation expenses',
            'Professional Fees' => 'Fees paid to professional service providers',
            'Depreciation Expense' => 'Depreciation of fixed assets'
        ];

        return $descriptions[$name] ?? "A {$type} account for {$name}";
    }
}
