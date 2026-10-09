<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        // Both admin and employee can view index (with different filters applied in controller)
        return true;
    }

    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $attendanceRecord->user_id;
    }

    public function checkIn(User $user): bool
    {
        return $user->is_active;
    }

    public function checkOut(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if (!$user->is_active) {
            return false;
        }
        return $user->id === $attendanceRecord->user_id;
    }

    public function correct(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }
}
