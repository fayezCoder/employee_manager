<?php

namespace App\Policies;

use App\Models\OrganizationalUnit;
use App\Models\User;

class OrganizationalUnitPolicy
{
    /**
     * Determine whether the user can view any organizational units.
     */
    public function viewAny(User $user): bool
    {
        // Any user who is a super_admin or has any unit assignment can view units within their scope
        return $user->isSuperAdmin() || $user->unitAssignments()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganizationalUnit $unit): bool
    {
        return $user->canAccessUnit($unit);
    }

    /**
     * Only super_admin can create, update, or deactivate organizational units
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, OrganizationalUnit $unit): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, OrganizationalUnit $unit): bool
    {
        // Deletion is permanently disabled in favor of deactivation
        return false;
    }

    public function toggleActive(User $user, OrganizationalUnit $unit): bool
    {
        return $user->isSuperAdmin();
    }
}
