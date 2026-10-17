<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterWorkflowEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'daily_roster_id',
        'user_id',
        'from_status',
        'to_status',
        'stage_label',
        'notes',
        'roster_version',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'roster_version' => 'integer',
    ];

    public function dailyRoster(): BelongsTo
    {
        return $this->belongsTo(DailyRoster::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
