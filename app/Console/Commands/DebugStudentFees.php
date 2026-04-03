<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\FeePaymentService;
use Illuminate\Console\Command;

class DebugStudentFees extends Command
{
    protected $signature = 'debug:student-fees {student_id? : The student ID to debug}';
    protected $description = 'Debug fee calculations for a student - shows course fees and payments';

    public function handle()
    {
        $studentId = $this->argument('student_id');

        if (!$studentId) {
            $this->info('Fetching all students with outstanding fees...\n');
            
            $students = Student::all();
            $studentsWithFees = [];

            foreach ($students as $student) {
                $status = FeePaymentService::getPaymentStatus($student);
                if ($status['outstanding'] > 0) {
                    $studentsWithFees[] = [
                        $student->id,
                        $student->full_name,
                        'RWF ' . number_format($status['outstanding'], 2),
                        round($status['payment_percentage'], 1) . '%'
                    ];
                }
            }

            if (!empty($studentsWithFees)) {
                $this->table(
                    ['Student ID', 'Name', 'Outstanding', 'Payment %'],
                    $studentsWithFees
                );
                $this->info("\nRun with a Student ID to see detailed breakdown:");
                $this->info("php artisan debug:student-fees <student_id>");
            } else {
                $this->info('✓ No students with outstanding fees found!');
            }
            return;
        }

        $student = Student::find($studentId);
        if (!$student) {
            $this->error("Student #{$studentId} not found!");
            return;
        }

        $debug = FeePaymentService::debugFeeCalculation($student);

        $this->line('');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('STUDENT FEE DEBUG INFORMATION');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->line('');

        // Student Info
        $this->info('Student Information:');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $debug['student_id']],
                ['Name', $debug['student_name']],
                ['Course ID', $debug['course_id'] ?? 'N/A'],
                ['Academic Year ID', $debug['academic_year_id'] ?? 'N/A'],
            ]
        );
        $this->line('');

        // Course Term Fees
        if (!empty($debug['course_term_fees'])) {
            $this->info('Course Term Fees (Required):');
            $this->table(['Term', 'Amount'], $debug['course_term_fees']);
            $this->line('');
        }

        // Student Transactions
        if (!empty($debug['student_transactions'])) {
            $this->info('Student Payments (School Fees):');
            $this->table(
                ['Date', 'Status', 'Amount', 'Reference'],
                $debug['student_transactions']
            );
            $this->line('');
        } else {
            $this->warn('No school fee payments recorded for this student');
            $this->line('');
        }

        // Calculation
        $this->info('Fee Calculation:');
        $calc = $debug['calculation'];
        $this->table(
            ['Item', 'Amount'],
            [
                ['Total Required', 'RWF ' . number_format($calc['total_required'], 2)],
                ['Total Paid (Successful)', 'RWF ' . number_format($calc['total_paid_successful'], 2)],
                ['Outstanding Balance', 'RWF ' . number_format($calc['outstanding'], 2)],
                ['Payment Percentage', $calc['percentage_paid'] . '%'],
            ]
        );
        $this->line('');

        // Final Status
        $status = $debug['payment_status'];
        if ($status['is_fully_paid']) {
            $this->info('✓ ELIGIBLE: Student can generate report cards (all fees paid)');
        } else {
            $this->error('✗ INELIGIBLE: Student has outstanding fees, cannot generate report cards');
            $this->error("   Outstanding: RWF " . number_format($status['outstanding'], 2));
        }
        $this->line('');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->line('');
    }
}
