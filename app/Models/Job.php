<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'industry',
        'positions_available',
        'location',
        'work_type',
        'schedule',
        'description',
        'qualification',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function skillTags()
    {
        return $this->belongsToMany(SkillTag::class, 'job_skill_tag', 'job_id', 'skill_tag_id');
    }
    
    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }
}
