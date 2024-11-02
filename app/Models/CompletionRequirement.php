<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletionRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'intern_evaluation',
        'exit_form',
        'certificate_completion',
    ];

    // Define relationship with the User (Student)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
