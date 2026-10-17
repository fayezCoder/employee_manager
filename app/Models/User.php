<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'is_active',
        'force_password_change'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'force_password_change' => 'boolean'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function getTodayAttendance(): ?AttendanceRecord
    {
        $today = Carbon::now('Asia/Riyadh')->toDateString();
        return $this->attendanceRecords()->where('date', $today)->first();
    }

    public function getOpenAttendance(): ?AttendanceRecord
    {
        return $this->attendanceRecords()->whereNull('check_out')->first();
    }

    public function unitAssignments(): HasMany
    {
        return $this->hasMany(UserUnitAssignment::class);
    }

    public function employeeProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function isSuperAdmin(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->unitAssignments()
            ->where('role', UserUnitAssignment::ROLE_SUPER_ADMIN)
            ->exists();
    }

    /**
     * Get IDs of organizational units this user is allowed to manage/view.
     * If user is super_admin, returns null (meaning ALL units).
     */
    public function getPermittedOrgUnitIds(?string $requiredPermission = null): ?array
    {
        if ($this->isSuperAdmin()) {
            return null; // Null signifies unrestricted access to all units
        }

        $assignments = $this->unitAssignments;
        $unitIds = [];

        foreach ($assignments as $assignment) {
            if ($requiredPermission === 'can_edit_approved' && !$assignment->can_edit_approved) {
                continue;
            }
            if ($requiredPermission === 'can_manage_employees' && !$assignment->can_manage_employees) {
                continue;
            }

            if ($assignment->org_unit_id) {
                $unitIds[] = $assignment->org_unit_id;
                if ($assignment->include_sub_branches && $assignment->organizationalUnit) {
                    $unitIds = array_merge($unitIds, $assignment->organizationalUnit->getDescendantIds());
                }
            }
        }

        return array_values(array_unique($unitIds));
    }

    /**
     * Check if user has permission over a specific organizational unit
     */
    public function canAccessUnit(int|OrganizationalUnit $unit, ?string $requiredPermission = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $unitId = $unit instanceof OrganizationalUnit ? $unit->id : $unit;
        $permittedIds = $this->getPermittedOrgUnitIds($requiredPermission);

        return $permittedIds !== null && in_array($unitId, $permittedIds, true);
    }

    /**
     * Check if user has a specific granular permission over an organizational unit
     * ensuring permissions never leak across different unit assignments.
     */
    public function hasUnitPermission(int|OrganizationalUnit $unit, string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $unitId = $unit instanceof OrganizationalUnit ? $unit->id : $unit;

        foreach ($this->unitAssignments as $assignment) {
            if (!$assignment->$permission) {
                continue;
            }

            if ($assignment->org_unit_id === $unitId) {
                return true;
            }

            if ($assignment->include_sub_branches && $assignment->organizationalUnit) {
                if (in_array($unitId, $assignment->organizationalUnit->getDescendantIds(), true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
