<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_unit_id',
        'date',
        'status',
        'version',
        'unit_name_snapshot',
        'unit_code_snapshot',
        'unit_type_snapshot',
        'unit_path_snapshot',
        'created_by',
        'submitted_by',
        'submitted_at',
        'branch_approved_by',
        'branch_approved_at',
        'fully_approved_by',
        'fully_approved_at',
        'was_corrected',
    ];

    protected $casts = [
        'date' => 'date',
        'version' => 'integer',
        'was_corrected' => 'boolean',
        'submitted_at' => 'datetime',
        'branch_approved_at' => 'datetime',
        'fully_approved_at' => 'datetime',
    ];

    /**
     * Explicit workflow states
     */
    public const STATUS_DRAFT = 'draft';                   // مسودة: مفتوح للتعديل
    public const STATUS_CENTER_LOCKED = 'center_locked';   // معتمد ومقفل من المركز (بانتظار القيادة الفرعية)
    public const STATUS_COMPLETED = 'completed';           // مكتمل ومعتمد نهائياً: مقفل تماماً

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_CENTER_LOCKED => 'معتمد ومقفل من المركز (بانتظار القيادة الفرعية)',
            self::STATUS_COMPLETED => 'مكتمل ومعتمد نهائياً',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'org_unit_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailyRosterItem::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(RosterAuditLog::class);
    }

    public function workflowEvents(): HasMany
    {
        return $this->hasMany(RosterWorkflowEvent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function branchApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'branch_approved_by');
    }

    public function fullApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fully_approved_by');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, [self::STATUS_CENTER_LOCKED, self::STATUS_COMPLETED], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Build and freeze unit snapshots including organizational hierarchy path
     */
    public function freezeUnitSnapshot(): void
    {
        $unit = $this->organizationalUnit;
        if (!$unit) {
            return;
        }

        // Build organizational breadcrumb path
        $pathNames = [];
        $current = $unit;
        while ($current) {
            array_unshift($pathNames, $current->name);
            $current = $current->parent;
        }

        $this->unit_name_snapshot = $unit->name;
        $this->unit_code_snapshot = $unit->code;
        $this->unit_type_snapshot = $unit->type;
        $this->unit_path_snapshot = implode(' > ', $pathNames);
    }
}
