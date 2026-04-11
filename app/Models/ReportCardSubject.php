<?php

namespace App\Models;

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
        // Both exam and test are already normalized to 100
        // Calculate the average of both to get final total out of 100
        if ($this->exam_mark > 0 && $this->test_average > 0) {
            // If both exam and test have marks, take average
            $this->total_mark = ($this->exam_mark + $this->test_average) / 2;
        } elseif ($this->exam_mark > 0) {
            // If only exam has marks, use exam mark
            $this->total_mark = $this->exam_mark;
        } elseif ($this->test_average > 0) {
            // If only test has marks, use test average
            $this->total_mark = $this->test_average;
        } else {
            // If no marks, set to 0
            $this->total_mark = 0;
        }
        
        // Cap all marks at 100
        $this->total_mark = min($this->total_mark, 100);
        $this->exam_mark = min($this->exam_mark, 100);
        $this->test_average = min($this->test_average, 100);
        
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

        // Normalize each test mark to 100, then calculate average
        $normalizedMarks = [];
        $totalOriginalMarks = 0;
        
        foreach ($testMarks as $mark) {
            $totalOriginalMarks += $mark->original_mark;
            
            // Normalize each test to 100-point scale
            if ($mark->max_mark > 0) {
                $normalizedMarks[] = ($mark->original_mark / $mark->max_mark) * 100;
            }
        }
        
        $this->test_total = $totalOriginalMarks;
        
        // Calculate average of the normalized marks
        if (!empty($normalizedMarks)) {
            $this->test_average = array_sum($normalizedMarks) / count($normalizedMarks);
        } else {
            $this->test_average = 0;
        }
        
        // Cap test_average at 100
        $this->test_average = min($this->test_average, 100);

        $this->save();
    }
}
