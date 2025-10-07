<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'marks_obtained',
        'grade',
        'grade_point',
        'status',
        'remarks',
        'entered_by',
        'entered_at',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'entered_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    // Belongs to Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Belongs to Exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Belongs to User (teacher who entered)
    public function enteredByTeacher()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Helper Methods
     */

    // Get percentage
    public function getPercentageAttribute(): float
    {
        if ($this->exam->total_marks == 0) {
            return 0.0;
        }

        return round(($this->marks_obtained / $this->exam->total_marks) * 100, 2);
    }

    // Get grade badge class
    public function getGradeBadgeClassAttribute(): string
    {
        return match($this->grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'primary',
            'C+', 'C' => 'info',
            'D' => 'warning',
            'F' => 'danger',
            default => 'secondary',
        };
    }

    // Get status badge class
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pass' => 'success',
            'fail' => 'danger',
            'absent' => 'warning',
            default => 'secondary',
        };
    }

    // Check if passed
    public function isPassed(): bool
    {
        return $this->status === 'pass';
    }

    // Check if failed
    public function isFailed(): bool
    {
        return $this->status === 'fail';
    }

    // Check if absent
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * Scopes
     */

    // Scope: Passed grades
    public function scopePassed($query)
    {
        return $query->where('status', 'pass');
    }

    // Scope: Failed grades
    public function scopeFailed($query)
    {
        return $query->where('status', 'fail');
    }

    // Scope: By student
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Scope: By exam
    public function scopeByExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    // Scope: By grade
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Boot method for auto-calculating grade and status
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($grade) {
            // Calculate grade and grade point
            $gradeData = self::calculateGrade($grade->marks_obtained, $grade->exam->total_marks);
            $grade->grade = $gradeData['grade'];
            $grade->grade_point = $gradeData['grade_point'];

            // Determine pass/fail status
            $grade->status = $grade->marks_obtained >= $grade->exam->passing_marks ? 'pass' : 'fail';

            // Set entered timestamp
            $grade->entered_at = now();
        });

        static::updating(function ($grade) {
            // Recalculate on update
            $gradeData = self::calculateGrade($grade->marks_obtained, $grade->exam->total_marks);
            $grade->grade = $gradeData['grade'];
            $grade->grade_point = $gradeData['grade_point'];

            // Determine pass/fail status
            $grade->status = $grade->marks_obtained >= $grade->exam->passing_marks ? 'pass' : 'fail';
        });
    }

    /**
     * Calculate letter grade and grade point from marks
     */
    public static function calculateGrade($marksObtained, $totalMarks): array
    {
        $percentage = ($marksObtained / $totalMarks) * 100;

        if ($percentage >= 90) {
            return ['grade' => 'A+', 'grade_point' => 4.00];
        } elseif ($percentage >= 85) {
            return ['grade' => 'A', 'grade_point' => 3.75];
        } elseif ($percentage >= 80) {
            return ['grade' => 'B+', 'grade_point' => 3.50];
        } elseif ($percentage >= 75) {
            return ['grade' => 'B', 'grade_point' => 3.25];
        } elseif ($percentage >= 70) {
            return ['grade' => 'C+', 'grade_point' => 3.00];
        } elseif ($percentage >= 65) {
            return ['grade' => 'C', 'grade_point' => 2.75];
        } elseif ($percentage >= 60) {
            return ['grade' => 'D', 'grade_point' => 2.50];
        } else {
            return ['grade' => 'F', 'grade_point' => 0.00];
        }
    }
}