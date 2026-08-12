<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Timetable;
use App\Models\Student;
use Illuminate\Support\Carbon;

$student = Student::first();
if (! $student) {
    echo "No student records found.\n";
    exit(1);
}

echo 'Student: '.$student->id.' branch='.$student->branch_id.' course='.$student->course_id.' year='.$student->academic_year_id."\n";

$published = Timetable::published()->with(['sessions.courses', 'sessions.lesson'])->get();
echo 'Published count: '.count($published)."\n";

foreach ($published as $tt) {
    $active = $tt->valid_from && $tt->valid_until && Carbon::parse($tt->valid_from)->lte(Carbon::now()) && Carbon::parse($tt->valid_until)->gte(Carbon::now());
    echo 'Timetable '.$tt->id.' '.$tt->title.' branch='.$tt->branch_id.' year='.$tt->academic_year_id.' valid_from='.$tt->valid_from.' valid_until='.$tt->valid_until.' activeDate='.(($active) ? 'yes' : 'no')."\n";
    foreach ($tt->sessions as $s) {
        $hasCourse = $s->courses->pluck('id')->contains($student->course_id);
        $lessonMatch = $s->lesson && $s->lesson->course_id == $student->course_id;
        echo '  session '.$s->id.' lesson='.$s->lesson_id.' hasCourse='.(($hasCourse) ? 'yes' : 'no').' lessonMatch='.(($lessonMatch) ? 'yes' : 'no')."\n";
    }
}
