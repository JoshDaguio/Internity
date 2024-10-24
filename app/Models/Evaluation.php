<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'evaluation_type', 
        'created_by',
        'academic_year_id'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function results()
    {
        return $this->hasMany(EvaluationResult::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
