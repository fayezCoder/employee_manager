<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Departments
        $hr = Department::create(['name' => 'الموارد البشرية']);
        $it = Department::create(['name' => 'تقنية المعلومات']);
        $ops = Department::create(['name' => 'العمليات والتطوير']);

        // 2. Create Admin
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => $hr->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        // 3. Create 8 Employees
        $employeesData = [
            ['name' => 'أحمد علي', 'email' => 'ahmed@example.com', 'dept' => $it],
            ['name' => 'سارة محمد', 'email' => 'sara@example.com', 'dept' => $hr],
            ['name' => 'خالد عبد الله', 'email' => 'khaled@example.com', 'dept' => $ops],
            ['name' => 'فاطمة الزهراء', 'email' => 'fatima@example.com', 'dept' => $it],
            ['name' => 'عمر الفاروق', 'email' => 'omar@example.com', 'dept' => $ops],
            ['name' => 'مريم حسن', 'email' => 'maryam@example.com', 'dept' => $hr],
            ['name' => 'يوسف إبراهيم', 'email' => 'youssef@example.com', 'dept' => $it],
            ['name' => 'نورة التميمي', 'email' => 'noura@example.com', 'dept' => $ops],
        ];

        $employees = [];
        foreach ($employeesData as $data) {
            $employees[] = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department_id' => $data['dept']->id,
                'is_active' => true,
                'force_password_change' => false,
            ]);
        }

        // 4. Create Seeded Attendance Records
        $today = Carbon::now('Asia/Riyadh')->startOfDay();
        $yesterday = (clone $today)->subDay();
        $twoDaysAgo = (clone $today)->subDays(2);
        $threeDaysAgo = (clone $today)->subDays(3);

        // Completed records for past days
        foreach ($employees as $index => $employee) {
            // Completed record 3 days ago
            $checkIn3 = (clone $threeDaysAgo)->setHour(8)->setMinute(0 + ($index * 5))->setTimezone('UTC');
            $checkOut3 = (clone $threeDaysAgo)->setHour(16)->setMinute(30 + ($index * 2))->setTimezone('UTC');
            $duration3 = (int) $checkIn3->diffInMinutes($checkOut3);

            AttendanceRecord::create([
                'user_id' => $employee->id,
                'date' => $threeDaysAgo->format('Y-m-d'),
                'check_in' => $checkIn3,
                'check_out' => $checkOut3,
                'duration_minutes' => $duration3,
                'status' => 'complete',
            ]);

            // Completed record 2 days ago
            $checkIn2 = (clone $twoDaysAgo)->setHour(8)->setMinute(15)->setTimezone('UTC');
            $checkOut2 = (clone $twoDaysAgo)->setHour(17)->setMinute(0)->setTimezone('UTC');
            $duration2 = (int) $checkIn2->diffInMinutes($checkOut2);

            AttendanceRecord::create([
                'user_id' => $employee->id,
                'date' => $twoDaysAgo->format('Y-m-d'),
                'check_in' => $checkIn2,
                'check_out' => $checkOut2,
                'duration_minutes' => $duration2,
                'status' => 'complete',
            ]);
        }

        // Yesterday: Some completed, one INCOMPLETE (forgot check-out)
        // Employee 0 & 1: Completed yesterday
        for ($i = 0; $i < 2; $i++) {
            $checkInY = (clone $yesterday)->setHour(8)->setMinute(30)->setTimezone('UTC');
            $checkOutY = (clone $yesterday)->setHour(16)->setMinute(45)->setTimezone('UTC');
            AttendanceRecord::create([
                'user_id' => $employees[$i]->id,
                'date' => $yesterday->format('Y-m-d'),
                'check_in' => $checkInY,
                'check_out' => $checkOutY,
                'duration_minutes' => (int) $checkInY->diffInMinutes($checkOutY),
                'status' => 'complete',
            ]);
        }

        // Employee 2: Incomplete record yesterday (check-in only)
        $checkInIncomplete = (clone $yesterday)->setHour(9)->setMinute(0)->setTimezone('UTC');
        AttendanceRecord::create([
            'user_id' => $employees[2]->id,
            'date' => $yesterday->format('Y-m-d'),
            'check_in' => $checkInIncomplete,
            'check_out' => null,
            'duration_minutes' => null,
            'status' => 'incomplete',
        ]);

        // Today: Open sessions for Employees 0, 1, 3
        for ($i = 0; $i < 4; $i++) {
            if ($i == 2) continue; // Employee 2 can't check in today because of yesterday's open session!

            $checkInToday = (clone $today)->setHour(8)->setMinute(10 + ($i * 10))->setTimezone('UTC');
            AttendanceRecord::create([
                'user_id' => $employees[$i]->id,
                'date' => $today->format('Y-m-d'),
                'check_in' => $checkInToday,
                'check_out' => null,
                'duration_minutes' => null,
                'status' => 'open',
            ]);
        }
    }
}
