<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        // Add other fillable attributes as needed
    ];

    // Define relationship with faculty
    public function faculty()
    {
        return $this->hasMany(User::class)->where('role_id', 3); // Assuming role_id 3 is Faculty
    }

    // Define relationship with students
    public function students()
    {
        return $this->hasMany(User::class)->where('role_id', 5); // Assuming role_id 5 is Student
    }
}
