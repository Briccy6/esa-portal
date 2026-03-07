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

        $totalNormalized = 0;
        $totalWeight = 0;

        foreach ($testMarks as $testMark) {
            $totalNormalized += $testMark->normalized_mark * $testMark->weight;
            $totalWeight += $testMark->weight;
        }

        if ($totalWeight > 0) {
            $this->test_average = $totalNormalized / $totalWeight;
            $this->test_total = $testMarks->sum('original_mark');
        } else {
            $this->test_average = 0;
            $this->test_total = 0;
        }

        $this->save();
    }
}
