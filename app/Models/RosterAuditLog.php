<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'daily_roster_id',
        'daily_roster_item_id',
        'user_id',
        'action',
        'field_changed',
        'old_value',
        'new_value',
        'reason',
        'roster_version',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'roster_version' => 'integer',
    ];

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'corrected' => 'تصحيح إداري للسجل المعتمد',
            'submitted' => 'اعتماد وقفل أولي',
            'approved' => 'اعتماد نهائي',
            'branch_approved' => 'اعتماد القيادة الفرعية',
            default => $this->action,
        };
    }

    public function dailyRoster(): BelongsTo
    {
        return $this->belongsTo(DailyRoster::class);
    }

    public function dailyRosterItem(): BelongsTo
    {
        return $this->belongsTo(DailyRosterItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
