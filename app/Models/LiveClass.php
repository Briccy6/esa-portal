<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = [
        'summary',
        'meet_link',
        'start_time',
        'end_time',
        'branch_id',
        'course_id',
        'lesson_id',
    ];
}
