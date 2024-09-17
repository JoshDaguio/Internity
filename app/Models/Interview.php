<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'interview_type',
        'interview_link',
        'interview_datetime',
        'message',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
