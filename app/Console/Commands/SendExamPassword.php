<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendExamPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:send-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send exam passwords to students on exam date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $exams = Exam::where('date', $today)->with('courses.students')->get();

        foreach ($exams as $exam) {
            foreach ($exam->courses as $course) {
                foreach ($course->students as $student) {
                    if ($student->email) {
                        Mail::to($student->email)->queue(new \App\Mail\ExamPasswordMail($exam, $student));
                    }
                }
            }
        }

        $this->info('Exam passwords sent to students.');
    }
}
