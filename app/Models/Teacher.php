<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_number',
        'qualification',
        'specialization',
        'salary',
        'joining_date',
        'date_of_birth',
        'gender',
        'blood_group',
        'nationality',
        'employment_type',
        'experience',
        'certifications',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'date_of_birth' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function teacher()
{
    return $this->hasOne(\App\Models\Teacher::class, 'user_id');
}

    // Many-to-Many: Teacher teaches many subjects in many classes
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject')
            ->withPivot('class_id', 'academic_year')
            ->withTimestamps();
    }

    // Has many classes through teacher_subject pivot
    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'teacher_subject', 'teacher_id', 'class_id')
            ->withPivot('subject_id', 'academic_year')
            ->withTimestamps();
    }

    // One-to-Many: Teacher is class teacher of many classes
    public function assignedClasses()
    {
        return $this->hasMany(ClassRoom::class, 'class_teacher_id');
    }

    /**
     * Helper Methods
     */

    // Get teacher's full name
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    // Get teacher's age
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    // Get years of service
    public function getYearsOfServiceAttribute(): int
    {
        return $this->joining_date->diffInYears(now());
    }

    // Get all subjects teacher teaches (unique)
    public function getTeachingSubjects()
    {
        return $this->subjects()->distinct()->get();
    }

    // Get all classes teacher teaches (unique)
    public function getTeachingClasses()
    {
        return $this->classes()->distinct()->get();
    }

    // Get subject-class combinations for current academic year
    public function getCurrentAssignments($academicYear = null)
    {
        if (!$academicYear) {
            $currentYear = date('Y');
            $academicYear = $currentYear . '-' . ($currentYear + 1);
        }

        return $this->subjects()
            ->wherePivot('academic_year', $academicYear)
            ->get()
            ->map(function($subject) {
                return [
                    'subject' => $subject,
                    'class_id' => $subject->pivot->class_id,
                    'class' => ClassRoom::find($subject->pivot->class_id),
                ];
            });
    }

    // Check if teacher is class teacher
    public function isClassTeacher(): bool
    {
        return $this->assignedClasses()->exists();
    }

    // Get total students taught (across all classes)
    public function getTotalStudentsCount(): int
    {
        $classIds = $this->classes()->pluck('classes.id');
        return Student::whereIn('class_id', $classIds)->count();
    }

    // Get total exams created
    public function getTotalExamsCreated(): int
    {
        return $this->user->createdExams()->count();
    }

    // Get total grades entered
    public function getTotalGradesEntered(): int
    {
        return $this->user->enteredGrades()->count();
    }

    /**
     * Scopes
     */

    // Scope: Active teachers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Teachers by employment type
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    // Scope: Teachers teaching a specific subject
    public function scopeTeachingSubject($query, $subjectId)
    {
        return $query->whereHas('subjects', function($q) use ($subjectId) {
            $q->where('subjects.id', $subjectId);
        });
    }

    // Scope: Teachers teaching a specific class
    public function scopeTeachingClass($query, $classId)
    {
        return $query->whereHas('classes', function($q) use ($classId) {
            $q->where('classes.id', $classId);
        });
    }

    /**
     * Boot method for auto-generating employee number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($teacher) {
            if (!$teacher->employee_number) {
                $teacher->employee_number = self::generateEmployeeNumber();
            }
        });
    }

    // Generate unique employee number
    public static function generateEmployeeNumber(): string
    {
        $year = date('Y');
        $lastTeacher = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastTeacher ? intval(substr($lastTeacher->employee_number, -4)) + 1 : 1;

        return 'EMP' . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}