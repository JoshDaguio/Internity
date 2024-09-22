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
        'start_date', 
        'work_type', 
        'start_time', 
        'end_time'
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
