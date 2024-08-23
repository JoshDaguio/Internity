<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndOfDayReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'key_successes',
        'main_challenges',
        'plans_for_tomorrow',
        'date_submitted',
    ];

    protected $casts = [
        'date_submitted' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(DailyTask::class, 'report_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
