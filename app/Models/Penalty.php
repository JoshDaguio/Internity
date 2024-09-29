<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penalty extends Model
{
    use HasFactory, SoftDeletes; // Include SoftDeletes trait;

    protected $fillable = [
        'violation',
        'penalty_hours',
        'conditions',
        'penalty_type',
    ];

    public function calculateConditionalPenalty($minutesLate)
    {
        if ($this->penalty_type === 'conditional') {
            if ($this->violation === 'Tardiness (Less than 30 minutes)') {
                return ceil($minutesLate / 10); // 1 hour for every 10 minutes late
            }
        }

        return $this->penalty_hours; // Default for fixed penalties
    }
}
