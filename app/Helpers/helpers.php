<?php

if (!function_exists('formatDate')) {
    /**
     * Format date to human readable format
     */
    function formatDate($date, $format = 'M d, Y')
    {
        if (!$date) return 'N/A';
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format datetime to human readable format
     */
    function formatDateTime($datetime, $format = 'M d, Y h:i A')
    {
        if (!$datetime) return 'N/A';
        return \Carbon\Carbon::parse($datetime)->format($format);
    }
}

if (!function_exists('getStatusBadge')) {
    /**
     * Get Bootstrap badge class for status
     */
    function getStatusBadge($status)
    {
        return match($status) {
            'active', 'present', 'pass' => 'success',
            'inactive', 'absent', 'fail' => 'danger',
            'late', 'pending' => 'warning',
            'excused' => 'info',
            default => 'secondary',
        };
    }
}

if (!function_exists('getGradeBadge')) {
    /**
     * Get Bootstrap badge class for grade
     */
    function getGradeBadge($grade)
    {
        return match($grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'primary',
            'C+', 'C' => 'info',
            'D' => 'warning',
            'F' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('currentAcademicYear')) {
    /**
     * Get current academic year
     */
    function currentAcademicYear()
    {
        $year = date('Y');
        $month = date('n');
        
        // If before September, use previous year
        if ($month < 9) {
            return ($year - 1) . '-' . $year;
        }
        
        return $year . '-' . ($year + 1);
    }
}

if (!function_exists('userCan')) {
    /**
     * Check if authenticated user has role
     */
    function userCan($role)
    {
        if (!auth()->check()) return false;
        
        if (is_array($role)) {
            return in_array(auth()->user()->role, $role);
        }
        
        return auth()->user()->role === $role;
    }
}

if (!function_exists('greetUser')) {
    /**
     * Get greeting based on time of day
     */
    function greetUser()
    {
        $hour = date('H');
        
        if ($hour < 12) {
            return 'Good Morning';
        } elseif ($hour < 17) {
            return 'Good Afternoon';
        } else {
            return 'Good Evening';
        }
    }
}

if (!function_exists('userAvatar')) {
    /**
     * Get user avatar URL with fallback
     */
    function userAvatar($user, $size = 150)
    {
        if (!$user) {
            return 'https://ui-avatars.com/api/?name=User&size=' . $size;
        }

        if ($user->profile_photo) {
            return asset('storage/' . $user->profile_photo);
        }

        $name = urlencode($user->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&size={$size}";
    }
}