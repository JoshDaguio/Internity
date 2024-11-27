<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'id_number',
        'about',
        'address',
        'contact_number',
        'cv_file_path',
        'profile_picture',
        'is_irregular',
        'moa_file_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function skillTags()
    {
        return $this->belongsToMany(SkillTag::class, 'profile_skill_tag');
    }
}
