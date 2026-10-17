<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationalUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const TYPE_MAIN_COMMAND = 'main_command';     // القيادة الرئيسية
    public const TYPE_BRANCH_COMMAND = 'branch_command'; // قيادة فرعية
    public const TYPE_CENTER = 'center';                 // مركز
    public const TYPE_DIVISION = 'division';             // شعبة
    public const TYPE_DEPARTMENT = 'department';         // قسم

    public static function typeLabels(): array
    {
        return [
            self::TYPE_MAIN_COMMAND => 'القيادة الرئيسية',
            self::TYPE_BRANCH_COMMAND => 'قيادة فرعية',
            self::TYPE_CENTER => 'مركز',
            self::TYPE_DIVISION => 'شعبة',
            self::TYPE_DEPARTMENT => 'قسم',
        ];
    }

    public function getTypeAttribute($value): ?string
    {
        $map = [
            'مركز' => self::TYPE_CENTER,
            'قيادة فرعية' => self::TYPE_BRANCH_COMMAND,
            'قيادة رئيسية' => self::TYPE_MAIN_COMMAND,
            'شعبة' => self::TYPE_DIVISION,
            'قسم' => self::TYPE_DEPARTMENT,
        ];

        return $map[$value] ?? $value;
    }

    public function isCenter(): bool
    {
        return $this->type === self::TYPE_CENTER;
    }

    public function isBranchCommand(): bool
    {
        return $this->type === self::TYPE_BRANCH_COMMAND;
    }

    public function isMainCommand(): bool
    {
        return $this->type === self::TYPE_MAIN_COMMAND;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class, 'parent_id');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'org_unit_id');
    }

    public function activeEmployees(): HasMany
    {
        return $this->employees()->where('is_active', true);
    }

    public function userAssignments(): HasMany
    {
        return $this->hasMany(UserUnitAssignment::class, 'org_unit_id');
    }

    public function dailyRosters(): HasMany
    {
        return $this->hasMany(DailyRoster::class, 'org_unit_id');
    }

    /**
     * Get all descendants recursive IDs
     */
    public function getDescendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }
        return $ids;
    }

    /**
     * Prevent circular reference when assigning parent
     */
    public function wouldCauseCycle(?int $newParentId): bool
    {
        if (!$newParentId) {
            return false;
        }

        if ($this->id === $newParentId) {
            return true;
        }

        return in_array($newParentId, $this->getDescendantIds(), true);
    }

    /**
     * Validate organizational hierarchy rules:
     * - main_command: parent must be null
     * - branch_command: parent must be main_command
     * - center: parent must be branch_command
     * - division: parent must be main_command
     * - department: parent must be division
     */
    public static function validateHierarchyRules(string $type, ?int $parentId, ?int $ignoreId = null): ?string
    {
        if ($type === self::TYPE_MAIN_COMMAND) {
            if ($parentId !== null) {
                return 'القيادة الرئيسية لا يمكن أن تتبع أي جهة أعلى.';
            }

            $existingMain = self::where('type', self::TYPE_MAIN_COMMAND)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if ($existingMain) {
                return 'النظام يسمح بوجود قيادة رئيسية واحدة فقط.';
            }

            return null;
        }

        if ($parentId === null) {
            return 'يجب تحديد جهة أعلى لهذه الوحدة.';
        }

        $parent = self::find($parentId);
        if (!$parent) {
            return 'الجهة الأعلى المحددة غير موجودة.';
        }

        switch ($type) {
            case self::TYPE_BRANCH_COMMAND:
                if ($parent->type !== self::TYPE_MAIN_COMMAND) {
                    return 'القيادة الفرعية يجب أن تتبع القيادة الرئيسية حصراً.';
                }
                break;

            case self::TYPE_CENTER:
                if ($parent->type !== self::TYPE_BRANCH_COMMAND) {
                    return 'المركز يجب أن يتبع قيادة فرعية حصراً.';
                }
                break;

            case self::TYPE_DIVISION:
                if ($parent->type !== self::TYPE_MAIN_COMMAND) {
                    return 'الشعبة يجب أن تتبع القيادة الرئيسية حصراً.';
                }
                break;

            case self::TYPE_DEPARTMENT:
                if ($parent->type !== self::TYPE_DIVISION) {
                    return 'القسم يجب أن يتبع شعبة حصراً.';
                }
                break;

            default:
                return 'نوع الوحدة التنظيمية غير صالح.';
        }

        return null;
    }
}
