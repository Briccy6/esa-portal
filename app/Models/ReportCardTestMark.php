<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardTestMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_subject_id',
        'exam_id',
        'test_name',
        'original_mark',
        'max_mark',
        'normalized_mark',
        'weight',
    ];

    protected $casts = [
        'original_mark' => 'decimal:2',
        'max_mark' => 'decimal:2',
        'normalized_mark' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function reportCardSubject()
    {
        return $this->belongsTo(ReportCardSubject::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function calculateNormalizedMark()
    {
        if ($this->max_mark > 0) {
            // Normalize to 100%
            $this->normalized_mark = ($this->original_mark / $this->max_mark) * 100;
        } else {
            $this->normalized_mark = 0;
        }
        $this->save();
    }

    public function getPercentageAttribute()
    {
        if ($this->max_mark > 0) {
            return ($this->original_mark / $this->max_mark) * 100;
        }
        return 0;
    }
}
