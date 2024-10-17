<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'log_date',
        'log_times',  // JSON field to store log in/out times
        'total_hours_worked',
        'remaining_hours'
    ];

    protected $casts = [
        'log_times' => 'array',  // Automatically cast log_times JSON field to an array
        // 'log_date' => 'date', 
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

}
