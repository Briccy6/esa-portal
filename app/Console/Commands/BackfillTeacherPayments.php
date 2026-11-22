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
    protected $signature = 'payments:backfill {--update-timestamps : Also update approved_at timestamps for approved dailies with null values}';

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

        $updateTimestamps = $this->option('update-timestamps');

        // Find approved teacher dailies without payment records
        $approvedDailies = TeacherDaily::where('approved', true)
            ->whereDoesntHave('payment')
            ->with(['teacher'])
            ->get();

        $this->info("Found {$approvedDailies->count()} approved dailies without payment records");

        if ($approvedDailies->isEmpty()) {
            $this->info('No payment records need to be created.');
        } else {
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

                    // Set approved_at timestamp if null
                    if (!$daily->approved_at) {
                        $daily->approved_at = now();
                        $daily->save();
                        $this->newLine();
                        $this->line("Set approved_at timestamp for daily ID {$daily->id}");
                    }

                    TeacherDailyPayment::create([
                        'teacher_daily_id' => $daily->id,
                        'teacher_id' => $daily->teacher_id,
                        'calculated_amount' => $calculatedAmount,
                        'amount_paid' => 0,
                        'status' => 'approved',
                        'work_date' => $daily->date,
                        'approved_at' => $daily->approved_at,
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

        // Update approved_at timestamps for approved dailies that have null values
        if ($updateTimestamps) {
            $this->newLine();
            $this->info('Updating approved_at timestamps for approved dailies with null values...');

            $dailiesWithNullTimestamp = TeacherDaily::where('approved', true)
                ->whereNull('approved_at')
                ->get();

            $this->info("Found {$dailiesWithNullTimestamp->count()} approved dailies with null approved_at");

            if ($dailiesWithNullTimestamp->isEmpty()) {
                $this->info('No timestamps need to be updated.');
            } else {
                $progressBar = $this->output->createProgressBar($dailiesWithNullTimestamp->count());

                $updated = 0;

                foreach ($dailiesWithNullTimestamp as $daily) {
                    $daily->approved_at = now();
                    $daily->save();
                    $updated++;
                    $progressBar->advance();
                }

                $progressBar->finish();
                $this->newLine();

                $this->info("Updated {$updated} approved_at timestamps.");
            }
        }
    }
}
