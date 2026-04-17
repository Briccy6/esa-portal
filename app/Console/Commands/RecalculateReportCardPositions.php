<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportCard;

class RecalculateReportCardPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reportcard:recalculate-positions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate student positions for all report cards based on updated formula';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting position recalculation...');
        
        // Get all unique course + academic_year + term combinations
        $combinations = ReportCard::select('course_id', 'academic_year_id', 'term_id')
            ->distinct()
            ->get();

        $count = 0;
        foreach ($combinations as $combo) {
            ReportCard::recalculatePositionsForCohort(
                (int) $combo->course_id,
                (int) $combo->academic_year_id,
                (int) $combo->term_id
            );
            $count++;
            $this->line("✓ Recalculated positions for course {$combo->course_id}, year {$combo->academic_year_id}, term {$combo->term_id}");
        }

        $this->info("\n✅ Done! Recalculated {$count} cohorts successfully");
        
        return Command::SUCCESS;
    }
}
