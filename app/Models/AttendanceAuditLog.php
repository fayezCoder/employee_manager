<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAuditLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'attendance_record_id',
        'admin_id',
        'field_changed',
        'old_value',
        'new_value',
        'reason',
        'created_at'
    ];

    protected $casts = [
        'old_value' => 'datetime',
        'new_value' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
