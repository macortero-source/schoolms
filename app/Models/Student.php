<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_number',
        'class_id',
        'admission_number',
        'admission_date',
        'academic_year',
        'date_of_birth',
        'gender',
        'blood_group',
        'nationality',
        'religion',
        'parent_name',
        'parent_phone',
        'parent_email',
        'parent_occupation',
        'parent_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'medical_conditions',
        'allergies',
        'previous_school',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'date_of_birth' => 'date',
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

    // Belongs to Class
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    // Has many Attendance records
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Has many Grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Helper Methods
     */

    // Get student's full name
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    // Get student's age
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    // Get attendance percentage
    public function getAttendancePercentage($startDate = null, $endDate = null): float
    {
        $query = $this->attendances();

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $totalDays = $query->count();
        if ($totalDays === 0) {
            return 0.0;
        }

        $presentDays = $query->where('status', 'present')->count();
        
        return round(($presentDays / $totalDays) * 100, 2);
    }

    // Get current month attendance percentage
    public function getCurrentMonthAttendance(): float
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        return $this->getAttendancePercentage($startOfMonth, $endOfMonth);
    }

    // Get total present days
    public function getTotalPresentDays($startDate = null, $endDate = null): int
    {
        $query = $this->attendances()->where('status', 'present');

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return $query->count();
    }

    // Get total absent days
    public function getTotalAbsentDays($startDate = null, $endDate = null): int
    {
        $query = $this->attendances()->where('status', 'absent');

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return $query->count();
    }

    // Calculate GPA (Grade Point Average)
    public function calculateGPA($academicYear = null, $semester = null): float
    {
        $query = $this->grades()
            ->whereHas('exam', function($q) use ($academicYear, $semester) {
                if ($academicYear) {
                    $q->where('academic_year', $academicYear);
                }
                if ($semester) {
                    $q->where('semester', $semester);
                }
            });

        $grades = $query->get();

        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalGradePoints = $grades->sum('grade_point');
        $totalSubjects = $grades->count();

        return round($totalGradePoints / $totalSubjects, 2);
    }

    // Get current semester GPA
    public function getCurrentSemesterGPA(): float
    {
        $currentYear = Carbon::now()->year;
        $academicYear = $currentYear . '-' . ($currentYear + 1);
        
        return $this->calculateGPA($academicYear);
    }

    // Get average marks for a specific exam type
    public function getAverageMarks($examType = null): float
    {
        $query = $this->grades();

        if ($examType) {
            $query->whereHas('exam', function($q) use ($examType) {
                $q->where('exam_type', $examType);
            });
        }

        $average = $query->avg('marks_obtained');
        
        return round($average ?? 0, 2);
    }

    // Check if student has low attendance (below 75%)
    public function hasLowAttendance(): bool
    {
        return $this->getCurrentMonthAttendance() < 75;
    }

    // Get failed exams count
    public function getFailedExamsCount(): int
    {
        return $this->grades()->where('status', 'fail')->count();
    }

    // Get total exams count
    public function getTotalExamsCount(): int
    {
        return $this->grades()->count();
    }

    // Get class rank based on GPA
    public function getClassRank(): int
    {
        if (!$this->class_id) {
            return 0;
        }

        $studentsInClass = self::where('class_id', $this->class_id)
            ->where('is_active', true)
            ->get();

        $rankedStudents = $studentsInClass->sortByDesc(function($student) {
            return $student->getCurrentSemesterGPA();
        })->values();

        $rank = $rankedStudents->search(function($student) {
            return $student->id === $this->id;
        });

        return $rank !== false ? $rank + 1 : 0;
    }

    /**
     * Scopes
     */

    // Scope: Active students
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Students in a specific class
    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // Scope: Students by academic year
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // Scope: Students by gender
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Boot method for auto-generating student number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (!$student->student_number) {
                $student->student_number = self::generateStudentNumber();
            }
        });
    }

    // Generate unique student number
    public static function generateStudentNumber(): string
    {
        $year = date('Y');
        $lastStudent = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastStudent ? intval(substr($lastStudent->student_number, -4)) + 1 : 1;

        return 'STU' . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}