<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo',
        'is_active',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // One-to-One: User has one Student profile
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // One-to-One: User has one Teacher profile
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    // One-to-Many: User has many announcements (if admin/teacher)
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'posted_by');
    }

    // One-to-Many: User has marked many attendance records (if teacher)
    public function markedAttendance()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    // One-to-Many: User has created many exams (if teacher)
    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    // One-to-Many: User has entered many grades (if teacher)
    public function enteredGrades()
    {
        return $this->hasMany(Grade::class, 'entered_by');
    }

    /**
     * Helper Methods
     */

    // Check if user is admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Check if user is teacher
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    // Check if user is student
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // Check if user is parent
    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    // Get profile photo URL
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        // Return default avatar based on gender or role
        return asset('images/default-avatar.png');
    }

    // Get full profile (student or teacher)
    public function getProfile()
    {
        if ($this->isStudent()) {
            return $this->student;
        } elseif ($this->isTeacher()) {
            return $this->teacher;
        }
        return null;
    }

    // Scope: Get active users only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Get users by role
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}