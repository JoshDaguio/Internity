<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedInternship extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id', 
        'company_id', 
        'job_id', 
        'schedule', 
        'custom_schedule',  // Add this 
        'start_date', 
        'work_type', 
        'start_time', 
        'end_time',
    ];

    protected $casts = [
        'schedule' => 'array',
        'custom_schedule' => 'array',  // Automatically cast to array
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

}
