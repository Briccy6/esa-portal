<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating Sample Students...\n\n";

// Check existing students
$existingStudents = DB::table('students')->count();
echo "Existing students: {$existingStudents}\n";

if ($existingStudents == 0) {
    // Create sample students
    $students = [
        [
            'full_name' => 'John Doe',
            'sex' => 'Male',
            'email' => 'john@example.com',
            'phone' => '0781234567',
            'branch_id' => 1,
            'course_id' => 1,
            'academic_year_id' => 1,
            'reg_number' => 'REG001',
            'nesa_registered' => true,
            'payed_registration_fees' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'full_name' => 'Jane Smith',
            'sex' => 'Female',
            'email' => 'jane@example.com',
            'phone' => '0787654321',
            'branch_id' => 1,
            'course_id' => 1,
            'academic_year_id' => 1,
            'reg_number' => 'REG002',
            'nesa_registered' => true,
            'payed_registration_fees' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'full_name' => 'Bob Johnson',
            'sex' => 'Male',
            'email' => 'bob@example.com',
            'phone' => '0789876543',
            'branch_id' => 1,
            'course_id' => 1,
            'academic_year_id' => 1,
            'reg_number' => 'REG003',
            'nesa_registered' => true,
            'payed_registration_fees' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];

    foreach ($students as $student) {
        DB::table('students')->insert($student);
    }

    echo "Created 3 sample students\n";
} else {
    echo "Students already exist\n";
}

echo "\nNow importing transactions...\n";

// Clear existing data
DB::table('student_transactions')->truncate();
DB::table('expenses')->truncate();

// Get student IDs
$studentIds = DB::table('students')->pluck('id')->toArray();

if (empty($studentIds)) {
    echo "No students found! Please check the students table.\n";
    exit;
}

// Insert sample student transactions using real student IDs
$studentTransactions = [
    [
        'student_id' => $studentIds[0],
        'tx_ref' => 'CASH-' . $studentIds[0] . '-' . time() . '-test1',
        'transaction_id' => null,
        'type' => 'registration',
        'amount' => 10000.00,
        'currency' => 'RWF',
        'status' => 'paid',
        'meta' => json_encode(['method' => 'cash', 'recorded_by' => 'staff']),
        'paid_at' => '2025-08-02 13:26:43',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'student_id' => $studentIds[1],
        'tx_ref' => 'CASH-' . $studentIds[1] . '-' . time() . '-test2',
        'transaction_id' => null,
        'type' => 'registration',
        'amount' => 10000.00,
        'currency' => 'RWF',
        'status' => 'paid',
        'meta' => json_encode(['method' => 'cash', 'recorded_by' => 'staff']),
        'paid_at' => '2025-08-03 10:15:30',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'student_id' => $studentIds[2],
        'tx_ref' => 'CASH-' . $studentIds[2] . '-' . time() . '-test3',
        'transaction_id' => null,
        'type' => 'student_card',
        'amount' => 2000.00,
        'currency' => 'RWF',
        'status' => 'paid',
        'meta' => json_encode(['method' => 'cash', 'recorded_by' => 'staff']),
        'paid_at' => '2025-08-04 14:20:15',
        'created_at' => now(),
        'updated_at' => now(),
    ]
];

// Insert sample expenses
$expenses = [
    [
        'expense_date' => '2025-08-05',
        'expense_type' => 'Markers',
        'amount' => 14000.00,
        'description' => 'markers for teaching',
        'paid_by' => 1,
        'paid_to' => 'papertry',
        'paid_to_contact' => '0734544895',
        'payment_method' => 'cash',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'expense_date' => '2025-08-06',
        'expense_type' => 'ADVERTISEMENT',
        'amount' => 500000.00,
        'description' => 'Radio&TV-10 (MENTION in INZU Y\'IBITO) Both Radio and TV-10 (on Saturday)!',
        'paid_by' => 1,
        'paid_to' => 'RADIO&TV-10',
        'paid_to_contact' => '0784069299',
        'payment_method' => 'cash',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'expense_date' => '2025-08-07',
        'expense_type' => 'Markers',
        'amount' => 7000.00,
        'description' => 'markers',
        'paid_by' => 1,
        'paid_to' => 'T2000 supermarket',
        'paid_to_contact' => '0788656496',
        'payment_method' => 'cash',
        'created_at' => now(),
        'updated_at' => now(),
    ]
];

// Insert the data
foreach ($studentTransactions as $transaction) {
    DB::table('student_transactions')->insert($transaction);
}

foreach ($expenses as $expense) {
    DB::table('expenses')->insert($expense);
}

echo "Inserted " . count($studentTransactions) . " student transactions\n";
echo "Inserted " . count($expenses) . " expenses\n\n";

// Test the cashbook
use App\Models\AccountingReport;
$data = AccountingReport::getCashBookData();

echo "Cashbook Test Results:\n";
echo "Total Transactions: " . $data['transactions']->count() . "\n";
echo "Total Receipts: " . number_format($data['total_receipts'], 2) . " RWF\n";
echo "Total Payments: " . number_format($data['total_payments'], 2) . " RWF\n";
echo "Net Cash: " . number_format($data['net_cash'], 2) . " RWF\n\n";

echo "Sample Transactions:\n";
foreach ($data['transactions']->take(5) as $tx) {
    echo "- {$tx->entry_date}: {$tx->entry_type} - {$tx->amount} RWF ({$tx->description})\n";
}

echo "\nSample data import completed!\n";
echo "You can now test the cashbook at: /admin/accounting/cashbook\n";
