<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'marked_by',
        'remarks',
        'check_in_time',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
    ];

    /**
     * Relationships
     */

    // Belongs to Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Belongs to Class
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    // Belongs to User (teacher who marked)
    public function markedByTeacher()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Helper Methods
     */

    // Get status badge HTML class
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'excused' => 'info',
            default => 'secondary',
        };
    }

    // Check if marked late
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    // Check if absent
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    // Check if present
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Scopes
     */

    // Scope: Today's attendance
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    // Scope: This week's attendance
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    // Scope: This month's attendance
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year);
    }

    // Scope: By status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope: By date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Scope: By student
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Scope: By class
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}