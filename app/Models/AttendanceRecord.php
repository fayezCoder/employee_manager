<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'duration_minutes',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AttendanceAuditLog::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status', 'incomplete');
    }

    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }

    public function calculateDuration(): ?int
    {
        if (!$this->check_out || !$this->check_in) {
            return null;
        }

        return $this->check_in->diffInMinutes($this->check_out);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'complete' => __('مكتمل'),
            'open' => __('مفتوح'),
            'incomplete' => __('غير مكتمل'),
            default => __($this->status),
        };
    }

    public function getFormattedCheckIn(): string
    {
        if (!$this->check_in) return '-';
        return Carbon::parse($this->check_in)->timezone('Asia/Riyadh')->format('H:i');
    }

    public function getFormattedCheckOut(): ?string
    {
        if (!$this->check_out) {
            return null;
        }

        return Carbon::parse($this->check_out)->timezone('Asia/Riyadh')->format('H:i');
    }

    public function getFormattedDuration(): string
    {
        if ($this->duration_minutes === null) {
            return '-';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        return "{$hours}h {$minutes}m";
    }
}
