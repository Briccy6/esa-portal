<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all unique course + academic_year + term combinations from report cards
$combinations = \App\Models\ReportCard::select('course_id', 'academic_year_id', 'term_id')->distinct()->get();

$count = 0;
foreach ($combinations as $combo) {
    \App\Models\ReportCard::recalculatePositionsForCohort(
        (int)$combo->course_id,
        (int)$combo->academic_year_id,
        (int)$combo->term_id
    );
    $count++;
    echo "✓ Recalculated positions for course {$combo->course_id}, year {$combo->academic_year_id}, term {$combo->term_id}\n";
}

echo "\n✅ Done! Recalculated {$count} cohorts\n";
?>
