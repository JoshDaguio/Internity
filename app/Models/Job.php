<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'industry',
        'positions_available',
        'location',
        'work_type',
        'schedule',
        'description',
        'qualification',
        'preferred_skills',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
}
