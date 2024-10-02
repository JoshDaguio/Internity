<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_year',
        'end_year',
        'is_current',
    ];

    /**
     * Get the full academic year as a string.
     * Example: "2024-2025"
     */
    public function fullYear()
    {
        return "{$this->start_year}-{$this->end_year}";
    }

    /**
     * Scope to get the current academic year.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
