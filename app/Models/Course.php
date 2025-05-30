<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Course extends Model {
    protected $fillable = ['course_code', 'course_name', 'abbreviation', 'course_type'];

    protected static function booted()
    {
        static::creating(function ($course) {
            $last = static::where('abbreviation', $course->abbreviation)
                          ->orderBy('id', 'desc')
                          ->first();

            if ($last && preg_match('/\d+$/', $last->course_code, $matches)) {
                $number = intval($matches[0]) + 1;
            } else {
                $number = 1;
            }

            $course->course_code = $course->abbreviation . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
