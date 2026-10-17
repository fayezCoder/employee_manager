<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeeRosterPolicy
{
    /**
     * Determine whether the user can view employees in their scope.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->unitAssignments()->exists();
    }

    /**
     * Determine whether the user can view an employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$employee->org_unit_id) {
            return false; // unassigned employees only visible to super admin
        }

        return $user->canAccessUnit($employee->org_unit_id);
    }

    /**
     * Determine whether the user can create employees.
     * Allowed for super_admin or user with can_manage_employees for the target unit (or any unit if not specified).
     */
    public function create(User $user, ?int $orgUnitId = null): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // If specific unit requested, verify permission on that unit
        if ($orgUnitId) {
            return $user->canAccessUnit($orgUnitId, 'can_manage_employees');
        }

        // When entering the create page without pre-selected unit, check if user has permission in at least one unit
        $permittedIds = $user->getPermittedOrgUnitIds('can_manage_employees');
        return !empty($permittedIds);
    }

    /**
     * Determine whether the user can update an employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$employee->org_unit_id) {
            return false;
        }

        return $user->canAccessUnit($employee->org_unit_id, 'can_manage_employees');
    }

    /**
     * Soft toggle active state
     */
    public function toggleActive(User $user, Employee $employee): bool
    {
        return $this->update($user, $employee);
    }

    /**
     * Deletion is not allowed; must use toggleActive/deactivation
     */
    public function delete(User $user, Employee $employee): bool
    {
        return false;
    }
}
