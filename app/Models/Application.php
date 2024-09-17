<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'student_id',
        'status_id',
        'endorsement_letter_path',
        'cv_path',
        'date_posted',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function status()
    {
        return $this->belongsTo(ApplicationStatus::class, 'status_id');
    }

    public function interview()
    {
        return $this->hasOne(Interview::class, 'application_id');
    }

}
