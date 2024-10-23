<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id', 
        'question_text', 
        'question_type'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
