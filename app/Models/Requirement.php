<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'waiver_form', 
        'medical_certificate', 
        'endorsement_letter', 
        'status_id',
        'waiver_status_id',  // Add this field
        'medical_status_id', // Add this field
    ];


    // Relationship with the User model (Student)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship with RequirementStatus model
    public function status()
    {
        return $this->belongsTo(RequirementStatus::class, 'status_id');
    }

    public function step1Completed()
    {
        return $this->waiver_status_id == 2 && $this->medical_status_id == 2; // Both are accepted
    }
    
    public function waiverStatus()
    {
        return $this->belongsTo(RequirementStatus::class, 'waiver_status_id');
    }

    public function medicalStatus()
    {
        return $this->belongsTo(RequirementStatus::class, 'medical_status_id');
    }
}
