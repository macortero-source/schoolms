<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'target_audience',
        'priority',
        'is_active',
        'publish_date',
        'expiry_date',
        'send_email',
        'email_sent',
        'posted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'send_email' => 'boolean',
        'email_sent' => 'boolean',
        'publish_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Relationships
     */

    // Belongs to User (poster)
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Helper Methods
     */

    // Get priority badge class
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    // Get target audience badge class
    public function getTargetAudienceBadgeClassAttribute(): string
    {
        return match($this->target_audience) {
            'all' => 'primary',
            'students' => 'success',
            'teachers' => 'info',
            'parents' => 'warning',
            'admin' => 'dark',
            default => 'secondary',
        };
    }

    // Check if announcement is published
    public function isPublished(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->publish_date && $this->publish_date->isFuture()) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }

        return true;
    }

    // Check if announcement is expired
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    // Check if announcement is scheduled
    public function isScheduled(): bool
    {
        return $this->publish_date && $this->publish_date->isFuture();
    }

    // Get excerpt (first 100 characters of content)
    public function getExcerptAttribute(): string
    {
        return strlen($this->content) > 100 
            ? substr($this->content, 0, 100) . '...' 
            : $this->content;
    }

    // Get formatted publish date
    public function getFormattedPublishDateAttribute(): string
    {
        return $this->publish_date ? $this->publish_date->format('M d, Y') : 'Not Set';
    }

    // Get formatted expiry date
    public function getFormattedExpiryDateAttribute(): string
    {
        return $this->expiry_date ? $this->expiry_date->format('M d, Y') : 'No Expiry';
    }

    /**
     * Scopes
     */

    // Scope: Active announcements
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Published announcements
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', Carbon::now());
            })
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', Carbon::now());
            });
    }

    // Scope: Expired announcements
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now());
    }

    // Scope: Scheduled announcements
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('publish_date')
            ->where('publish_date', '>', Carbon::now());
    }

    // Scope: By target audience
    public function scopeForAudience($query, $audience)
    {
        return $query->where(function($q) use ($audience) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', $audience);
        });
    }

    // Scope: By priority
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Scope: Urgent announcements
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    // Scope: Recent announcements
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }
}