<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes'; // Specify table name

    protected $fillable = [
        'name',
        'grade_level',
        'section',
        'class_teacher_id',
        'capacity',
        'description',
        'room_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Belongs to class teacher (User)
    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    // Has many Students
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // Has many exams
    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    // Has many attendance records
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    // Many-to-Many: Class has many teachers through teacher_subject pivot
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'class_id', 'teacher_id')
            ->withPivot('subject_id', 'academic_year')
            ->withTimestamps();
    }

    /**
     * Helper Methods
     */

    // Get full class name with section
    public function getFullNameAttribute(): string
    {
        return $this->section 
            ? $this->name . ' - Section ' . $this->section 
            : $this->name;
    }

    // Get total enrolled students
    public function getTotalStudentsAttribute(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    // Get available seats
    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->total_students);
    }

    // Check if class is full
    public function isFull(): bool
    {
        return $this->total_students >= $this->capacity;
    }

    // Get class teacher name
    public function getClassTeacherNameAttribute(): string
    {
        return $this->classTeacher ? $this->classTeacher->name : 'Not Assigned';
    }

    // Get average attendance percentage for the class
    public function getAverageAttendancePercentage($startDate = null, $endDate = null): float
    {
        $students = $this->students()->where('is_active', true)->get();

        if ($students->isEmpty()) {
            return 0.0;
        }

        $totalPercentage = 0;
        foreach ($students as $student) {
            $totalPercentage += $student->getAttendancePercentage($startDate, $endDate);
        }

        return round($totalPercentage / $students->count(), 2);
    }

    // Get class average GPA
    public function getAverageGPA($academicYear = null): float
    {
        $students = $this->students()->where('is_active', true)->get();

        if ($students->isEmpty()) {
            return 0.0;
        }

        $totalGPA = 0;
        foreach ($students as $student) {
            $totalGPA += $student->calculateGPA($academicYear);
        }

        return round($totalGPA / $students->count(), 2);
    }

    // Get students with low attendance (below 75%)
    public function getStudentsWithLowAttendance()
    {
        return $this->students()
            ->where('is_active', true)
            ->get()
            ->filter(function($student) {
                return $student->hasLowAttendance();
            });
    }

    // Get top performers (by GPA)
    public function getTopPerformers($limit = 5)
    {
        $students = $this->students()
            ->where('is_active', true)
            ->get()
            ->sortByDesc(function($student) {
                return $student->getCurrentSemesterGPA();
            });

        return $students->take($limit);
    }

    // Get subjects taught in this class
    public function getSubjects()
    {
        return Subject::whereHas('teachers', function($query) {
            $query->where('teacher_subject.class_id', $this->id);
        })->distinct()->get();
    }

    /**
     * Scopes
     */

    // Scope: Active classes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Classes by grade level
    public function scopeByGradeLevel($query, $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    // Scope: Classes with available seats
    public function scopeWithAvailableSeats($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND students.is_active = 1) < classes.capacity');
    }
}