<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTRequest extends Model
{
    use HasFactory;
    
    protected $table = 'ot_requests';

    protected $fillable = [
        'student_id',
        'date_request',
        'ot_start_time',
        'ot_end_time',
        'details',
        'proof_file_path',
        'status',
        'remarks',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
