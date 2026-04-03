<?php

namespace App\Console\Commands;

use App\Models\Term;
use App\Services\BabyeyiService;
use Illuminate\Console\Command;

class GenerateBabyeyis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'babyeyi:generate 
                            {--term-id= : The term ID to generate babyeyis for}
                            {--academic-year-id= : The academic year ID (optional, defaults to term\'s academic year)}
                            {--publish : Auto-publish all generated babyeyis}
                            {--create-by=1 : The user ID who created the babyeyis (defaults to 1)}
                            {--force : Force generation even if babyeyis already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate babyeyi documents for all courses in a term';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $termId = $this->option('term-id');
        
        if (!$termId) {
            $this->error('Please provide a term ID using --term-id');
            return Command::FAILURE;
        }

        $term = Term::find($termId);
        if (!$term) {
            $this->error("Term with ID {$termId} not found");
            return Command::FAILURE;
        }

        $academicYearId = $this->option('academic-year-id') ?? $term->academic_year_id;
        $createdBy = $this->option('create-by') ?? 1;
        $publish = $this->option('publish');

        $this->info("Generating babyeyis for {$term->name} ({$term->academicYear->year})...");

        $service = new BabyeyiService();
        
        try {
            $result = $service->generateBabyeyisForTerm($termId, $academicYearId, $createdBy);

            $this->info("✅ Created {$result['created']} babyeyis");
            $this->info("⏭️  Skipped {$result['skipped']} (already exist or no fees)");

            if ($publish) {
                $publishCount = $service->publishBabyeyisForTerm($termId);
                $this->info("✅ Published {$publishCount} babyeyis");
            } else {
                $this->info("📋 Generated babyeyis are in DRAFT status. Use --publish to auto-publish them.");
            }

            if (!empty($result['errors'])) {
                $this->warn("⚠️  Warnings:");
                foreach ($result['errors'] as $error) {
                    $this->warn("  - {$error}");
                }
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
