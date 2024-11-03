<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'subject', 
        'reason', 
        'attachment_path',
        'status', 
        'penalty_type',
        'academic_year_id', 
        'admin_remarks',
        'absence_date'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
