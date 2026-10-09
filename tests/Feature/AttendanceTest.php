<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Department $dept;
    private User $admin;
    private User $employee1;
    private User $employee2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dept = Department::create(['name' => 'التقنية']);

        $this->admin = User::create([
            'name' => 'المدير',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
            'department_id' => $this->dept->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->employee1 = User::create([
            'name' => 'موظف 1',
            'email' => 'emp1@test.com',
            'password' => 'password',
            'role' => 'employee',
            'department_id' => $this->dept->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->employee2 = User::create([
            'name' => 'موظف 2',
            'email' => 'emp2@test.com',
            'password' => 'password',
            'role' => 'employee',
            'department_id' => $this->dept->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }

    /** Admin can access employees management without 403 */
    public function test_admin_can_access_employees_management(): void
    {
        $response = $this->actingAs($this->admin->fresh())->get(route('employees.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin->fresh())->get(route('employees.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin->fresh())->get(route('employees.edit', $this->employee1));
        $response->assertStatus(200);
    }

    /** 1. Employee prevented from reading or modifying another employee's data */
    public function test_employee_cannot_access_other_employee_records(): void
    {
        $record = AttendanceRecord::create([
            'user_id' => $this->employee2->id,
            'date' => now('Asia/Riyadh')->format('Y-m-d'),
            'check_in' => now()->subHours(2),
            'status' => 'open',
        ]);

        // Employee 1 attempts to view attendance list containing employee 2
        $response = $this->actingAs($this->employee1)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertDontSee($this->employee2->name);

        // Employee 1 attempts to correct employee 2's record
        $response = $this->actingAs($this->employee1)->get(route('attendance.correct', $record));
        $response->assertStatus(403);
    }

    /** 2 & 4. Prevent duplicate check-in and prevent check-in with existing open session */
    public function test_prevent_duplicate_check_in_or_open_session(): void
    {
        // First check-in
        $response = $this->actingAs($this->employee1)->post(route('attendance.check-in'));
        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $this->employee1->id,
            'status' => 'open',
        ]);

        // Second check-in attempt while session is open
        $response = $this->actingAs($this->employee1)->post(route('attendance.check-in'));
        $response->assertSessionHas('error');
    }

    /** 3. Prevent check-out without open check-in */
    public function test_prevent_check_out_without_open_check_in(): void
    {
        $response = $this->actingAs($this->employee1)->post(route('attendance.check-out'));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('attendance_records', [
            'user_id' => $this->employee1->id,
            'status' => 'complete',
        ]);
    }

    /** 5. Handling session crossing midnight correctly */
    public function test_session_crossing_midnight_belongs_to_start_date(): void
    {
        // Check-in yesterday at 23:00 local time
        $yesterdayDate = now('Asia/Riyadh')->subDay()->format('Y-m-d');
        $checkInUtc = Carbon::parse($yesterdayDate . ' 23:00:00', 'Asia/Riyadh')->setTimezone('UTC');

        $record = AttendanceRecord::create([
            'user_id' => $this->employee1->id,
            'date' => $yesterdayDate,
            'check_in' => $checkInUtc,
            'status' => 'open',
        ]);

        // Freeze time at today 07:00 local time (8 hours later)
        $checkOutUtc = Carbon::parse($yesterdayDate . ' 23:00:00', 'Asia/Riyadh')->addHours(8)->setTimezone('UTC');
        Carbon::setTestNow($checkOutUtc);

        $response = $this->actingAs($this->employee1)->post(route('attendance.check-out'));
        $response->assertRedirect();

        $record->refresh();
        $this->assertEquals('complete', $record->status);
        $this->assertEquals($yesterdayDate, $record->date->format('Y-m-d'));
        $this->assertEquals(480, $record->duration_minutes); // 8 hours = 480 minutes

        Carbon::setTestNow();
    }

    /** 6 & 7. Reject invalid correction times & audit log recording */
    public function test_admin_correction_validation_and_audit_logging(): void
    {
        $record = AttendanceRecord::create([
            'user_id' => $this->employee1->id,
            'date' => '2026-10-01',
            'check_in' => Carbon::parse('2026-10-01 08:00:00', 'Asia/Riyadh')->setTimezone('UTC'),
            'check_out' => Carbon::parse('2026-10-01 16:00:00', 'Asia/Riyadh')->setTimezone('UTC'),
            'duration_minutes' => 480,
            'status' => 'complete',
        ]);

        // Attempt correction with check_out BEFORE check_in (invalid)
        $response = $this->actingAs($this->admin->fresh())->from(route('attendance.correct', $record))->post(route('attendance.store-correction', $record), [
            'field' => 'check_out',
            'value' => '2026-10-01 07:00', // Before check-in at 08:00
            'reason' => 'تصحيح خاطئ للتجربة',
        ]);

        $response->assertSessionHas('error');

        // Valid correction
        $response = $this->actingAs($this->admin->fresh())->from(route('attendance.correct', $record))->post(route('attendance.store-correction', $record), [
            'field' => 'check_out',
            'value' => '2026-10-01 17:00', // Extended by 1 hour
            'reason' => 'نسي الموظف تسجيل الانصراف في الوقت الفعلي',
        ]);

        $response->assertRedirect(route('attendance.index'));

        // Verify record updated
        $record->refresh();
        $this->assertEquals(540, $record->duration_minutes); // 9 hours = 540 minutes

        // Verify Audit Log created with exact details
        $this->assertDatabaseHas('attendance_audit_logs', [
            'attendance_record_id' => $record->id,
            'admin_id' => $this->admin->id,
            'field_changed' => 'check_out',
            'reason' => 'نسي الموظف تسجيل الانصراف في الوقت الفعلي',
        ]);
    }

    /** 8. Deactivated account prevented from logging in or using system */
    public function test_deactivated_user_cannot_login_or_use_session(): void
    {
        // Deactivate employee
        $this->employee1->update(['is_active' => false]);

        // Try login
        $response = $this->post(route('login'), [
            'email' => $this->employee1->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Active session gets booted by middleware
        $this->employee1->update(['is_active' => true]);
        $this->actingAs($this->employee1->fresh());

        // Deactivate while logged in
        $this->employee1->update(['is_active' => false]);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** 9. Report filters match export output */
    public function test_report_filters_match_csv_export(): void
    {
        AttendanceRecord::create([
            'user_id' => $this->employee1->id,
            'date' => '2026-10-05',
            'check_in' => Carbon::parse('2026-10-05 08:00:00', 'Asia/Riyadh')->setTimezone('UTC'),
            'check_out' => Carbon::parse('2026-10-05 16:00:00', 'Asia/Riyadh')->setTimezone('UTC'),
            'duration_minutes' => 480,
            'status' => 'complete',
        ]);

        // Filter by user_id = employee1
        $response = $this->actingAs($this->admin->fresh())->get(route('reports.export', [
            'user_id' => $this->employee1->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();

        $this->assertStringContainsString($this->employee1->name, $content);
        $this->assertStringNotContainsString($this->employee2->name, $content);
    }
}
