<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportCard;
use App\Models\Student;
use App\Http\Controllers\ReportCardController;

class RegenerateReportCards extends Command
{
    protected $signature = 'report-cards:regenerate {--student_id=} {--student_name=}';
    protected $description = 'Regenerate report cards with corrected exam mark calculations';

    public function handle()
    {
        $this->info('Regenerating report cards with corrected calculations...');

        $query = ReportCard::query();
        
        if ($this->option('student_id')) {
            $query->where('student_id', $this->option('student_id'));
            $this->info('Filtering by student_id: ' . $this->option('student_id'));
        }
        
        if ($this->option('student_name')) {
            $studentIds = Student::where('full_name', 'like', '%' . $this->option('student_name') . '%')
                ->pluck('id')
                ->toArray();
            
            if (empty($studentIds)) {
                $this->error('No students found with name like: ' . $this->option('student_name'));
                return;
            }
            
            $query->whereIn('student_id', $studentIds);
            $this->info('Filtering by student name: ' . $this->option('student_name'));
        }

        $reportCards = $query->get();
        
        if ($reportCards->isEmpty()) {
            $this->error('No report cards found');
            return;
        }
        
        $this->info("Found {$reportCards->count()} report card(s) to regenerate");

        $controller = new ReportCardController();
        $count = 0;

        foreach ($reportCards as $reportCard) {
            try {
                $this->line("Regenerating Report Card ID: {$reportCard->id}");
                $this->line("  Student: {$reportCard->student->full_name}");
                $this->line("  Term: {$reportCard->term->name}");
                
                // Delete existing subjects to regenerate
                $reportCard->subjects()->delete();
                
                // Regenerate with corrected logic
                $controller->generateReportCardData($reportCard);
                
                $this->line("  ✓ Successfully regenerated");
                $count++;
            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
            }
        }

        $this->info("✓ Regenerated {$count} report card(s)!");
    }
}
