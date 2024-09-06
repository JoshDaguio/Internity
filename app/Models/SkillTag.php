<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillTag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_skill_tag');
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_skill_tag', 'skill_tag_id', 'job_id');
    }
}


