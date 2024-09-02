<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id', 
        'link_name', 
        'link_url'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
