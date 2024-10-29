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

    public function nonAcceptedApplicationsCount()
    {
        $acceptedStatusId = ApplicationStatus::where('status', 'Accepted')->first()->id;
        $rejectedStatusId = ApplicationStatus::where('status', 'Rejected')->first()->id;

        
        return $this->applications()
                    ->whereNotIn('status_id', [$acceptedStatusId, $rejectedStatusId])
                    ->count();
    }


    public function acceptedApplicantsCount()
    {
        return $this->hasMany(AcceptedInternship::class, 'job_id')->count();
    }

}
