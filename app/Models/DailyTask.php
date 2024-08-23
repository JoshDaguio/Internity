<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'task_description',
        'time_spent',
        'time_unit',
    ];

    public function report()
    {
        return $this->belongsTo(EndOfDayReport::class, 'report_id');
    }
}
