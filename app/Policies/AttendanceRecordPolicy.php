<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->id === $attendanceRecord->user_id) {
            return true;
        }

        // Check if the record owner's employee record belongs to a unit the user can access
        $employee = $attendanceRecord->user?->employeeProfile;
        if ($employee && $employee->org_unit_id) {
            return $user->canAccessUnit($employee->org_unit_id);
        }

        return false;
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

    /**
     * Legacy correction requires super_admin OR explicit can_edit_approved permission
     */
    public function correct(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->unitAssignments()
            ->where('can_edit_approved', true)
            ->exists();
    }
}
