<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Course;
use App\Models\Branch; // Import Branch model

class Teacher extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'teacher_name', 'teacher_id_number', 'id_number', 'telephone', 'email',
        'photo', 'branch_id', 'department', 'employment_type', 'level_of_education', 'salary', 'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'salary' => 'decimal:2',
    ];

    /**
     * Get the courses that belong to the teacher.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teacher_course', 'teacher_id', 'course_id')
                    ->select('courses.*');
    }
    

    /**
     * Get the teacher's full photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/teachers/' . $this->photo);
        }
        return asset('images/default-avatar.png'); // Default avatar if no photo
    }

    /**
     * Get the teacher's formatted salary.
     */
    public function getFormattedSalaryAttribute()
    {
        return number_format($this->salary, 2) . ' RWF';
    }

    /**
     * Scope to filter teachers by department.
     */
    public function scopeByDepartment($query, $department)
    {
        if ($department && $department !== 'All') {
            return $query->where('department', $department);
        }
        return $query;
    }

    /**
     * Scope to filter teachers by employment type.
     */
    public function scopeByEmploymentType($query, $employmentType)
    {
        return $query->where('employment_type', $employmentType);
    }

    /**
     * Get teachers with permanent employment.
     */
    public function scopePermanent($query)
    {
        return $query->where('employment_type', 'Permanent');
    }

    /**
     * Get teachers with temporary employment (Short Term or Casual).
     */
    public function scopeTemporary($query)
    {
        return $query->whereIn('employment_type', ['Short Term', 'Casual']);
    }
    public function branch()
{
    return $this->belongsTo(Branch::class);
}
public function branches()
{
    // Assuming your pivot table is 'branch_teacher' with 'teacher_id' and 'branch_id'
    return $this->belongsToMany(Branch::class, 'branch_teacher', 'teacher_id', 'branch_id');
}
public function lessons()
{
    return $this->hasMany(\App\Models\Lesson::class, 'teacher_id');
}
}