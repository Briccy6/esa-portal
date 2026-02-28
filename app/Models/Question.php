<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'marks'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
       
    protected $casts = [
        'exam_id' => 'integer',
        'marks' => 'integer',
        'options' => 'array', // Assuming options are stored as an array
    ];
}
