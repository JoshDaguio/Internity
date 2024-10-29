<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pullout extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',
        'pullout_date',
        'status',
        'excuse_detail',
        'company_remark',
        'academic_year_id',
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'pullout_students', 'pullout_id', 'student_id');
    }
    
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
