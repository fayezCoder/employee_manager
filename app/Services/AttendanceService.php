<?php

namespace App\Services;

use App\Exceptions\AttendanceException;
use App\Models\AttendanceAuditLog;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function checkIn(User $user): AttendanceRecord
    {
        return DB::transaction(function () use ($user) {
            $openRecord = $user->attendanceRecords()->whereNull('check_out')->first();
            if ($openRecord) {
                throw new AttendanceException(__('لديك تسجيل دخول مفتوح بالفعل.'));
            }

            $localDate = Carbon::now('Asia/Riyadh')->toDateString();

            $existingRecord = $user->attendanceRecords()->where('date', $localDate)->first();
            if ($existingRecord) {
                throw new AttendanceException(__('لقد قمت بتسجيل الحضور مسبقاً لهذا اليوم.'));
            }

            return $user->attendanceRecords()->create([
                'date' => $localDate,
                'check_in' => Carbon::now('UTC'),
                'status' => 'open'
            ]);
        });
    }

    public function checkOut(User $user): AttendanceRecord
    {
        return DB::transaction(function () use ($user) {
            $openRecord = AttendanceRecord::where('user_id', $user->id)
                ->whereNull('check_out')
                ->first();

            if (!$openRecord) {
                throw new AttendanceException(__('لا يوجد تسجيل دخول مفتوح.'));
            }

            $nowUtc = Carbon::now('UTC');

            $rawCheckIn = $openRecord->getRawOriginal('check_in');
            $checkInUtc = $rawCheckIn ? Carbon::parse($rawCheckIn, 'UTC') : $openRecord->check_in;

            if ($nowUtc->lt($checkInUtc)) {
                throw new AttendanceException(__('وقت تسجيل الخروج يجب أن يكون بعد وقت تسجيل الدخول.'));
            }

            $durationMinutes = (int) $checkInUtc->diffInMinutes($nowUtc);

            $openRecord->update([
                'check_out' => $nowUtc,
                'duration_minutes' => $durationMinutes,
                'status' => 'complete'
            ]);

            return $openRecord;
        });
    }

    public function correctRecord(AttendanceRecord $record, User $admin, array $data): AttendanceRecord
    {
        return DB::transaction(function () use ($record, $admin, $data) {
            $field = $data['field'];

            // Parse incoming local time string as Asia/Riyadh and convert to UTC
            $newValueLocal = Carbon::parse($data['value'], 'Asia/Riyadh');
            $newValueUtc = (clone $newValueLocal)->setTimezone('UTC');

            $oldValue = $record->{$field};

            // Get exact UTC timestamps from DB raw values
            $rawCheckIn = $record->getRawOriginal('check_in');
            $rawCheckOut = $record->getRawOriginal('check_out');

            $checkInUtc = $rawCheckIn ? Carbon::parse($rawCheckIn, 'UTC') : $record->check_in;
            $checkOutUtc = $rawCheckOut ? Carbon::parse($rawCheckOut, 'UTC') : $record->check_out;

            if ($field === 'check_in') {
                if ($checkOutUtc && $newValueUtc->gt($checkOutUtc)) {
                    throw new AttendanceException(__('وقت تسجيل الدخول يجب أن يكون قبل وقت تسجيل الخروج.'));
                }
                $checkInUtc = $newValueUtc;
            } elseif ($field === 'check_out') {
                if ($checkInUtc && $newValueUtc->lt($checkInUtc)) {
                    throw new AttendanceException(__('وقت تسجيل الخروج يجب أن يكون بعد وقت تسجيل الدخول.'));
                }
                $checkOutUtc = $newValueUtc;
            }

            $durationMinutes = null;
            $status = 'open';

            if ($checkInUtc && $checkOutUtc) {
                $durationMinutes = (int) $checkInUtc->diffInMinutes($checkOutUtc);
                $status = 'complete';
            }

            $record->update([
                $field => $newValueUtc,
                'duration_minutes' => $durationMinutes,
                'status' => $status,
            ]);

            AttendanceAuditLog::create([
                'attendance_record_id' => $record->getKey(),
                'admin_id' => $admin->getKey(),
                'field_changed' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValueUtc,
                'reason' => $data['reason'],
                'created_at' => Carbon::now('UTC'),
            ]);

            return $record;
        });
    }
}
