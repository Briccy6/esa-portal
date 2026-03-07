<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Lesson;
use App\Models\Course;

$course = Course::find(18);
echo 'Course: ' . $course->course_name . "\n";

$lessons = Lesson::where('course_id', 18)->get();
echo 'Lessons count: ' . $lessons->count() . "\n";

foreach($lessons as $lesson) {
    echo 'Lesson: ' . $lesson->title . ' (ID: ' . $lesson->id . ')' . "\n";
}

echo "\nTest completed!\n";
