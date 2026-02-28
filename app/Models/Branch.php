<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location'];
    
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
    
    // Many-to-many relationship with staff
    public function staffMembers()
    {
        return $this->belongsToMany(Staff::class, 'staff_branch');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
    
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
