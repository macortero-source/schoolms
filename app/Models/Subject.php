<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'credit_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Many-to-Many: Subject is taught by many teachers
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject')
            ->withPivot('class_id', 'academic_year')
            ->withTimestamps();
    }

    // Has many exams
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Helper Methods
     */

    // Get full subject name with code
    public function getFullNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    // Get teachers teaching this subject
    public function getTeachingTeachers($academicYear = null)
    {
        $query = $this->teachers();

        if ($academicYear) {
            $query->wherePivot('academic_year', $academicYear);
        }

        return $query->get();
    }

    // Get classes where this subject is taught
    public function getClasses($academicYear = null)
    {
        $query = ClassRoom::whereHas('teachers.subjects', function($q) use ($academicYear) {
            $q->where('subjects.id', $this->id);
            if ($academicYear) {
                $q->where('teacher_subject.academic_year', $academicYear);
            }
        });

        return $query->distinct()->get();
    }

    // Get total students studying this subject
    public function getTotalStudentsCount($academicYear = null): int
    {
        $classes = $this->getClasses($academicYear);
        $classIds = $classes->pluck('id');

        return Student::whereIn('class_id', $classIds)
            ->where('is_active', true)
            ->count();
    }

    // Get average marks for this subject
    public function getAverageMarks($academicYear = null): float
    {
        $query = Grade::whereHas('exam', function($q) use ($academicYear) {
            $q->where('subject_id', $this->id);
            if ($academicYear) {
                $q->where('academic_year', $academicYear);
            }
        });

        return round($query->avg('marks_obtained') ?? 0, 2);
    }

    // Get pass percentage for this subject
    public function getPassPercentage($academicYear = null): float
    {
        $query = Grade::whereHas('exam', function($q) use ($academicYear) {
            $q->where('subject_id', $this->id);
            if ($academicYear) {
                $q->where('academic_year', $academicYear);
            }
        });

        $totalGrades = $query->count();
        if ($totalGrades === 0) {
            return 0.0;
        }

        $passedGrades = $query->where('status', 'pass')->count();

        return round(($passedGrades / $totalGrades) * 100, 2);
    }

    /**
     * Scopes
     */

    // Scope: Active subjects
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Core subjects
    public function scopeCore($query)
    {
        return $query->where('type', 'core');
    }

    // Scope: Elective subjects
    public function scopeElective($query)
    {
        return $query->where('type', 'elective');
    }

    // Scope: Search by name or code
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
    }
}