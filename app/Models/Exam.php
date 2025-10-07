<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subject_id',
        'class_id',
        'exam_type',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'academic_year',
        'semester',
        'description',
        'instructions',
        'room_number',
        'created_by',
        'is_published',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Belongs to Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Belongs to Class
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    // Belongs to User (creator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Has many Grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Helper Methods
     */

    // Get exam type badge class
    public function getExamTypeBadgeClassAttribute(): string
    {
        return match($this->exam_type) {
            'quiz' => 'info',
            'midterm' => 'warning',
            'final' => 'danger',
            'assignment' => 'primary',
            'practical' => 'success',
            default => 'secondary',
        };
    }

    // Get full exam name
    public function getFullNameAttribute(): string
    {
        return $this->name . ' - ' . $this->subject->name . ' (' . $this->class->full_name . ')';
    }

    // Check if exam is upcoming
    public function isUpcoming(): bool
    {
        return $this->exam_date->isFuture();
    }

    // Check if exam is today
    public function isToday(): bool
    {
        return $this->exam_date->isToday();
    }

    // Check if exam is past
    public function isPast(): bool
    {
        return $this->exam_date->isPast();
    }

    // Get passing percentage
    public function getPassingPercentageAttribute(): float
    {
        return round(($this->passing_marks / $this->total_marks) * 100, 2);
    }

    // Get total students for this exam
    public function getTotalStudentsCount(): int
    {
        return $this->class->students()->where('is_active', true)->count();
    }

    // Get graded students count
    public function getGradedStudentsCount(): int
    {
        return $this->grades()->count();
    }

    // Get pending grades count
    public function getPendingGradesCount(): int
    {
        return $this->getTotalStudentsCount() - $this->getGradedStudentsCount();
    }

    // Check if all grades are entered
    public function isFullyGraded(): bool
    {
        return $this->getPendingGradesCount() === 0;
    }

    // Get average marks
    public function getAverageMarks(): float
    {
        return round($this->grades()->avg('marks_obtained') ?? 0, 2);
    }

    // Get highest marks
    public function getHighestMarks(): float
    {
        return round($this->grades()->max('marks_obtained') ?? 0, 2);
    }

    // Get lowest marks
    public function getLowestMarks(): float
    {
        return round($this->grades()->min('marks_obtained') ?? 0, 2);
    }

    // Get pass count
    public function getPassCount(): int
    {
        return $this->grades()->where('status', 'pass')->count();
    }

    // Get fail count
    public function getFailCount(): int
    {
        return $this->grades()->where('status', 'fail')->count();
    }

    // Get absent count
    public function getAbsentCount(): int
    {
        return $this->grades()->where('status', 'absent')->count();
    }

    // Get pass percentage
    public function getPassPercentage(): float
    {
        $total = $this->getGradedStudentsCount();
        if ($total === 0) {
            return 0.0;
        }

        return round(($this->getPassCount() / $total) * 100, 2);
    }

    // Get grade distribution
    public function getGradeDistribution(): array
    {
        return [
            'A+' => $this->grades()->where('grade', 'A+')->count(),
            'A' => $this->grades()->where('grade', 'A')->count(),
            'B+' => $this->grades()->where('grade', 'B+')->count(),
            'B' => $this->grades()->where('grade', 'B')->count(),
            'C+' => $this->grades()->where('grade', 'C+')->count(),
            'C' => $this->grades()->where('grade', 'C')->count(),
            'D' => $this->grades()->where('grade', 'D')->count(),
            'F' => $this->grades()->where('grade', 'F')->count(),
        ];
    }

    // Get top performers
    public function getTopPerformers($limit = 5)
    {
        return $this->grades()
            ->orderBy('marks_obtained', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Scopes
     */

    // Scope: Published exams
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope: Unpublished exams
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    // Scope: Upcoming exams
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>', Carbon::now());
    }

    // Scope: Past exams
    public function scopePast($query)
    {
        return $query->where('exam_date', '<', Carbon::now());
    }

    // Scope: Today's exams
    public function scopeToday($query)
    {
        return $query->whereDate('exam_date', Carbon::today());
    }

    // Scope: By exam type
    public function scopeByType($query, $type)
    {
        return $query->where('exam_type', $type);
    }

    // Scope: By academic year
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // Scope: By semester
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // Scope: By subject
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    // Scope: By class
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}