<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_log'; // Specify the correct table name

    protected $fillable = ['admin_id', 'action', 'target', 'changes'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    protected $casts = [
        'changes' => 'array', // Cast the changes column to array
    ];
}
