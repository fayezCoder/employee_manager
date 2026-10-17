<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only run if users table exists
        if (!Schema::hasTable('users') || !Schema::hasTable('employees')) {
            return;
        }

        // Get users that are either:
        // 1. Role is 'employee'
        // 2. OR have existing attendance records in attendance_records
        // This avoids treating purely administrative accounts as employees automatically.
        $legacyUsers = DB::table('users')
            ->where(function ($query) {
                $query->where('role', 'employee');
                if (Schema::hasTable('attendance_records')) {
                    $query->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('attendance_records')
                            ->whereColumn('attendance_records.user_id', 'users.id');
                    });
                }
            })
            ->get();

        foreach ($legacyUsers as $user) {
            // Check if employee already exists for this user_id
            $alreadyExists = DB::table('employees')->where('user_id', $user->id)->exists();
            if ($alreadyExists) {
                continue;
            }

            $placeholderNationalId = 'LEGACY-' . str_pad((string)$user->id, 8, '0', STR_PAD_LEFT);

            // If an employee with this national_id somehow exists, append random suffix
            if (DB::table('employees')->where('national_id', $placeholderNationalId)->exists()) {
                $placeholderNationalId .= '-' . substr(md5((string)time() . (string)$user->id), 0, 4);
            }

            DB::table('employees')->insert([
                'name' => $user->name,
                'national_id' => $placeholderNationalId,
                'job_title' => null,
                'org_unit_id' => null, // Left for super_admin review & assignment
                'user_id' => $user->id,
                'is_active' => (bool)$user->is_active,
                'needs_review' => true,
                'review_notes' => 'تم استيراد الموظف من حساب النظام السابق. يتطلب مراجعة واعتماد المسؤول الأول لتحديث رقم الهوية وتحديد الجهة التنظيمية.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Non-destructive: do not drop employee records on rollback
    }
};
