<?php

namespace App\Models;

use App\Support\AssessmentMarkMath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_id',
        'lesson_id',
        'subject_name',
        'exam_mark',
        'test_total',
        'test_average',
        'total_mark',
        'grade',
        'remarks',
    ];

    protected $casts = [
        'exam_mark' => 'decimal:2',
        'test_total' => 'decimal:2',
        'test_average' => 'decimal:2',
        'total_mark' => 'decimal:2',
    ];

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function testMarks()
    {
        return $this->hasMany(ReportCardTestMark::class);
    }

    public function getGradeAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Auto-calculate grade based on total mark
        $total = $this->total_mark;
        if ($total >= 90) return 'A+';
        if ($total >= 80) return 'A';
        if ($total >= 70) return 'B+';
        if ($total >= 60) return 'B';
        if ($total >= 50) return 'C';
        if ($total >= 40) return 'D';
        return 'F';
    }

    public function calculateTotalMark()
    {
        $this->exam_mark = min(100.0, max(0.0, (float) $this->exam_mark));
        $this->test_average = min(100.0, max(0.0, (float) $this->test_average));
        $this->total_mark = AssessmentMarkMath::combinedLessonPercent($this->test_average, $this->exam_mark);
        $this->save();
    }

    public function calculateTestAverage()
    {
        $testMarks = $this->testMarks;

        if ($testMarks->isEmpty()) {
            $this->test_average = 0;
            $this->test_total = 0;
            $this->save();

            return;
        }

        // Pool all tests: sum(marks) / sum(max) * 100 → one score out of 100
        $sumOriginal = 0.0;
        foreach ($testMarks as $mark) {
            $sumOriginal += (float) $mark->original_mark;
        }
        $this->test_total = $sumOriginal;
        $this->test_average = AssessmentMarkMath::pooledTestPercentFromReportCardTestMarks($testMarks);
        $this->save();
    }
}
