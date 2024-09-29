<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementStatus extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }
}
