<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipHours extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'hours'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public $timestamps = false;

}
