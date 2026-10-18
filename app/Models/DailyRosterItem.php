<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyRosterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_roster_id',
        'employee_id',
        'date',
        'status',
        'notes',
        'employee_name_snapshot',
        'employee_national_id_snapshot',
        'employee_job_title_snapshot',
        'investigation_number',
        'investigation_status',
        'investigation_notes',
        'investigated_at',
        'investigated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'investigated_at' => 'datetime',
    ];

    public const INVESTIGATION_STATUS_COMPLETED = 'completed';
    public const INVESTIGATION_STATUS_PENDING = 'pending';

    /**
     * Check if investigation is pending or overdue (7 days limit)
     */
    public function getInvestigationState(): string
    {
        if ($this->status !== self::STATUS_ABSENT) {
            return 'not_applicable';
        }

        if (!empty($this->investigation_number)) {
            return 'investigated'; // تم التحقيق
        }

        $absenceDate = ($this->date ?? $this->created_at)->copy()->setTimezone('Asia/Riyadh')->startOfDay();
        $today = now('Asia/Riyadh')->startOfDay();
        $daysDiff = (int) $absenceDate->diffInDays($today, false);

        if ($daysDiff > 7) {
            return 'overdue'; // لم يتم التحقيق (انتهت المهلة)
        }

        return 'pending'; // قيد التحقيق (ضمن المهلة)
    }

    public function getInvestigationLabelAttribute(): string
    {
        $absenceDate = ($this->date ?? $this->created_at)->copy()->setTimezone('Asia/Riyadh')->startOfDay();
        $today = now('Asia/Riyadh')->startOfDay();
        $daysDiff = (int) $absenceDate->diffInDays($today, false);

        return match ($this->getInvestigationState()) {
            'investigated' => 'تم التحقيق (قيد: ' . $this->investigation_number . ')',
            'overdue' => 'لم يتم التحقيق',
            'pending' => 'قيد المتابعة (باقي ' . max(0, 7 - $daysDiff) . ' يوم)',
            default => '—',
        };
    }

    /**
     * The 14 exact operational attendance statuses
     */
    public const STATUS_PRESENT = 'حاضر';
    public const STATUS_ABSENT = 'غايب';
    public const STATUS_EXCUSED = 'مستأذن';
    public const STATUS_APPOINTMENT = 'موعد';
    public const STATUS_ASSIGNED = 'مكلف';
    public const STATUS_LATE = 'متأخر';
    public const STATUS_SUSPENDED = 'موقوف';
    public const STATUS_SECONDMENT = 'انتداب';
    public const STATUS_COURSE = 'دورة';
    public const STATUS_REGULAR_LEAVE = 'إجازة اعتيادية';
    public const STATUS_CASUAL_LEAVE = 'إجازة عرضية';
    public const STATUS_FIELD_LEAVE = 'إجازة ميدانية';
    public const STATUS_SICK_LEAVE = 'إجازة مرضية';
    public const STATUS_OTHER = 'أخرى';

    public static function allStatuses(): array
    {
        return [
            self::STATUS_PRESENT,
            self::STATUS_ABSENT,
            self::STATUS_EXCUSED,
            self::STATUS_APPOINTMENT,
            self::STATUS_ASSIGNED,
            self::STATUS_LATE,
            self::STATUS_SUSPENDED,
            self::STATUS_SECONDMENT,
            self::STATUS_COURSE,
            self::STATUS_REGULAR_LEAVE,
            self::STATUS_CASUAL_LEAVE,
            self::STATUS_FIELD_LEAVE,
            self::STATUS_SICK_LEAVE,
            self::STATUS_OTHER,
        ];
    }

    public function dailyRoster(): BelongsTo
    {
        return $this->belongsTo(DailyRoster::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(RosterAuditLog::class, 'daily_roster_item_id');
    }

    public function freezeEmployeeSnapshot(?Employee $employee = null): void
    {
        $emp = $employee ?? $this->employee;
        if ($emp) {
            $this->employee_name_snapshot = $emp->name;
            $this->employee_national_id_snapshot = $emp->masked_national_id;
            $this->employee_job_title_snapshot = $emp->job_title;
        }
    }
}
