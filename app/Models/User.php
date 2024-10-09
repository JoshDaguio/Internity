<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'course_id',
        'role_id',
        'status_id',
        'profile_id',
        'academic_year_id', 
        'expiry_date',
        'is_irregular',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function status()
    {
        return $this->belongsTo(AccountStatus::class, 'status_id');
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class, 'uploaded_by');
    }

    public function priorities()
    {
        return $this->hasMany(Priority::class, 'student_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function requirements()
    {
        return $this->hasOne(Requirement::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function acceptedInternship()
    {
        return $this->hasOne(AcceptedInternship::class, 'student_id');
    }

}
