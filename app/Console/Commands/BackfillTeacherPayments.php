<?php

namespace App\Console\Commands;

use App\Models\TeacherDaily;
use App\Models\TeacherDailyPayment;
use App\Services\TeacherPaymentService;
use Illuminate\Console\Command;

class BackfillTeacherPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create payment records for approved teacher dailies that are missing them';

    /**
     * Execute the console command.
     */
    public function handle(TeacherPaymentService $paymentService)
    {
        $this->info('Starting backfill of teacher payments...');

        // Find approved teacher dailies without payment records
        $approvedDailies = TeacherDaily::where('approved', true)
            ->whereDoesntHave('payment')
            ->with(['teacher'])
            ->get();

        $this->info("Found {$approvedDailies->count()} approved dailies without payment records");

        if ($approvedDailies->isEmpty()) {
            $this->info('No payment records need to be created.');
            return;
        }

        $progressBar = $this->output->createProgressBar($approvedDailies->count());

        $created = 0;
        $errors = 0;

        foreach ($approvedDailies as $daily) {
            try {
                $calculatedAmount = $paymentService->calculatePayment($daily);

                // Get a valid staff ID
                $staffId = $daily->approved_by;
                if (!$staffId) {
                    $firstStaff = \App\Models\Staff::first();
                    $staffId = $firstStaff ? $firstStaff->id : null;
                }

                TeacherDailyPayment::create([
                    'teacher_daily_id' => $daily->id,
                    'teacher_id' => $daily->teacher_id,
                    'calculated_amount' => $calculatedAmount,
                    'amount_paid' => 0,
                    'status' => 'approved',
                    'work_date' => $daily->date,
                    'approved_at' => $daily->approved_at ?? now(),
                    'approved_by' => $staffId,
                ]);

                $created++;
                $this->newLine();
                $this->line("Created payment for daily ID {$daily->id} (Teacher: {$daily->teacher->teacher_name}, Amount: {$calculatedAmount})");
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Failed to create payment for daily ID {$daily->id}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Backfill complete. Created: {$created}, Errors: {$errors}");
    }
}
