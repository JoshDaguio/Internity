<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id', 
        'user_id', 
        'total_score'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}
