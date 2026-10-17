<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserUnitAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'org_unit_id',
        'role',
        'include_sub_branches',
        'can_edit_approved',
        'can_manage_employees',
    ];

    protected $casts = [
        'include_sub_branches' => 'boolean',
        'can_edit_approved' => 'boolean',
        'can_manage_employees' => 'boolean',
    ];

    public const ROLE_SUPER_ADMIN = 'super_admin';                     // المسؤول الأول
    public const ROLE_MAIN_COMMAND_ADMIN = 'main_command_admin';         // مسؤول القيادة الرئيسية
    public const ROLE_BRANCH_COMMAND_ADMIN = 'branch_command_admin';     // مسؤول القيادة الفرعية
    public const ROLE_CENTER_ADMIN = 'center_admin';                     // مسؤول المركز
    public const ROLE_DIVISION_ADMIN = 'division_admin';                 // مسؤول الشعبة
    public const ROLE_DEPARTMENT_ADMIN = 'department_admin';             // مسؤول القسم

    public static function roleLabels(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'المسؤول الأول',
            self::ROLE_MAIN_COMMAND_ADMIN => 'مسؤول القيادة الرئيسية',
            self::ROLE_BRANCH_COMMAND_ADMIN => 'مسؤول القيادة الفرعية',
            self::ROLE_CENTER_ADMIN => 'مسؤول المركز',
            self::ROLE_DIVISION_ADMIN => 'مسؤول الشعبة',
            self::ROLE_DEPARTMENT_ADMIN => 'مسؤول القسم',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabels()[$this->role] ?? $this->role;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'org_unit_id');
    }
}
