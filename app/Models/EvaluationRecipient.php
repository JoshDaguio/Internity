<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'user_id',
        'is_answered',
    ];

    /**
     * Relationship with the Evaluation model.
     */
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
