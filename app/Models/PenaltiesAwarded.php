<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenaltiesAwarded extends Model
{
    use HasFactory;
    
    protected $table = 'penalties_awarded';
    
    protected $fillable = [
        'student_id',
        'penalty_id',
        'dtr_id',
        'awarded_date',
        'penalty_hours',
        'remarks',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function penalty()
    {
        return $this->belongsTo(Penalty::class, 'penalty_id');
    }

    public function dailyTimeRecord()
    {
        return $this->belongsTo(DailyTimeRecord::class, 'dtr_id');
    }
}
