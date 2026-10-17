<?php

namespace App\Policies;

use App\Models\DailyRoster;
use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Models\UserUnitAssignment;

class DailyRosterPolicy
{
    /**
     * Determine whether the user can view any daily rosters.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->unitAssignments()->exists();
    }

    /**
     * Determine whether the user can view a specific daily roster.
     */
    public function view(User $user, DailyRoster $roster): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canAccessUnit($roster->org_unit_id);
    }

    /**
     * Determine whether the user can create/start a draft roster for a unit.
     */
    public function create(User $user, OrganizationalUnit $unit): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canAccessUnit($unit);
    }

    /**
     * Determine whether the user can edit a draft roster.
     * Only allowed when roster status is 'draft' and user has access to unit.
     */
    public function update(User $user, DailyRoster $roster): bool
    {
        if ($roster->isLocked()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canAccessUnit($roster->org_unit_id);
    }

    /**
     * Determine whether the unit manager can submit their unit roster.
     * Allowed only when in 'draft' and user has access to this unit.
     */
    public function submit(User $user, DailyRoster $roster): bool
    {
        if ($roster->status !== DailyRoster::STATUS_DRAFT) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canAccessUnit($roster->org_unit_id);
    }

    /**
     * Determine whether branch command can approve a center roster.
     * Only applicable when roster status is center_locked and belongs to a center child.
     */
    public function branchApprove(User $user, DailyRoster $roster): bool
    {
        if ($roster->status !== DailyRoster::STATUS_CENTER_LOCKED) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $unit = $roster->organizationalUnit;
        if (!$unit || $unit->type !== OrganizationalUnit::TYPE_CENTER || !$unit->parent_id) {
            return false;
        }

        $branchId = $unit->parent_id;

        // User must have an assignment for this specific parent branch command
        return $user->unitAssignments()
            ->where('org_unit_id', $branchId)
            ->where('role', UserUnitAssignment::ROLE_BRANCH_COMMAND_ADMIN)
            ->exists();
    }

    /**
     * Determine whether the user can correct a locked/completed roster.
     * Rules:
     * - Only super_admin OR main_command_admin with `can_edit_approved` = true.
     */
    public function correct(User $user, DailyRoster $roster): bool
    {
        if (!$roster->isLocked()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->unitAssignments()
            ->where('role', UserUnitAssignment::ROLE_MAIN_COMMAND_ADMIN)
            ->where('can_edit_approved', true)
            ->exists();
    }

    /**
     * Determine whether the user can export roster data for a given unit.
     */
    public function export(User $user, ?OrganizationalUnit $unit = null): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($unit === null) {
            return $user->unitAssignments()->exists();
        }

        return $user->canAccessUnit($unit);
    }
}
