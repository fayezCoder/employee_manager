<?php

namespace Tests\Feature;

use App\Models\DailyRoster;
use App\Models\DailyRosterItem;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\RosterAuditLog;
use App\Models\RosterWorkflowEvent;
use App\Models\StructureAuditLog;
use App\Models\User;
use App\Models\UserUnitAssignment;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationalRosterTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private OrganizationalUnit $mainCmd;
    private OrganizationalUnit $branchCmd1;
    private OrganizationalUnit $branchCmd2;
    private OrganizationalUnit $center1;
    private OrganizationalUnit $center2;
    private OrganizationalUnit $center3;
    private OrganizationalUnit $division1;
    private OrganizationalUnit $dept1;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Super Admin (Global scope)
        $this->superAdmin = User::create([
            'name' => 'المشرف العام',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        UserUnitAssignment::create([
            'user_id' => $this->superAdmin->id,
            'org_unit_id' => null, // Global
            'role' => UserUnitAssignment::ROLE_SUPER_ADMIN,
            'can_edit_approved' => true,
            'can_manage_employees' => true,
        ]);

        // 2. Build Organizational Tree
        // Main Command
        $this->mainCmd = OrganizationalUnit::create([
            'name' => 'القيادة الرئيسية',
            'code' => 'HQ-01',
            'type' => OrganizationalUnit::TYPE_MAIN_COMMAND,
            'parent_id' => null,
            'is_active' => true,
        ]);

        // Branch Commands
        $this->branchCmd1 = OrganizationalUnit::create([
            'name' => 'القيادة الفرعية الأولى',
            'code' => 'BR-01',
            'type' => OrganizationalUnit::TYPE_BRANCH_COMMAND,
            'parent_id' => $this->mainCmd->id,
            'is_active' => true,
        ]);

        $this->branchCmd2 = OrganizationalUnit::create([
            'name' => 'القيادة الفرعية الثانية',
            'code' => 'BR-02',
            'type' => OrganizationalUnit::TYPE_BRANCH_COMMAND,
            'parent_id' => $this->mainCmd->id,
            'is_active' => true,
        ]);

        // Centers
        $this->center1 = OrganizationalUnit::create([
            'name' => 'المركز الأول',
            'code' => 'CTR-01',
            'type' => OrganizationalUnit::TYPE_CENTER,
            'parent_id' => $this->branchCmd1->id,
            'is_active' => true,
        ]);

        $this->center2 = OrganizationalUnit::create([
            'name' => 'المركز الثاني',
            'code' => 'CTR-02',
            'type' => OrganizationalUnit::TYPE_CENTER,
            'parent_id' => $this->branchCmd1->id,
            'is_active' => true,
        ]);

        $this->center3 = OrganizationalUnit::create([
            'name' => 'المركز الثالث',
            'code' => 'CTR-03',
            'type' => OrganizationalUnit::TYPE_CENTER,
            'parent_id' => $this->branchCmd2->id,
            'is_active' => true,
        ]);

        // Division & Department
        $this->division1 = OrganizationalUnit::create([
            'name' => 'شعبة الإدارة',
            'code' => 'DIV-01',
            'type' => OrganizationalUnit::TYPE_DIVISION,
            'parent_id' => $this->mainCmd->id,
            'is_active' => true,
        ]);

        $this->dept1 = OrganizationalUnit::create([
            'name' => 'قسم المتابعة',
            'code' => 'DEP-01',
            'type' => OrganizationalUnit::TYPE_DEPARTMENT,
            'parent_id' => $this->division1->id,
            'is_active' => true,
        ]);
    }

    public function test_organizational_hierarchy_validation_rules()
    {
        // Second main command should fail validation
        $error = OrganizationalUnit::validateHierarchyRules(OrganizationalUnit::TYPE_MAIN_COMMAND, null);
        $this->assertNotNull($error);

        // Branch command without parent should fail
        $error = OrganizationalUnit::validateHierarchyRules(OrganizationalUnit::TYPE_BRANCH_COMMAND, null);
        $this->assertNotNull($error);

        // Center parent must be a branch_command, not main_command
        $error = OrganizationalUnit::validateHierarchyRules(OrganizationalUnit::TYPE_CENTER, $this->mainCmd->id);
        $this->assertNotNull($error);

        // Center under branch command is valid
        $error = OrganizationalUnit::validateHierarchyRules(OrganizationalUnit::TYPE_CENTER, $this->branchCmd1->id);
        $this->assertNull($error);

        // Department under division is valid
        $error = OrganizationalUnit::validateHierarchyRules(OrganizationalUnit::TYPE_DEPARTMENT, $this->division1->id);
        $this->assertNull($error);

        // Department under branch command should fail
        $error = OrganizationalUnit::validateHierarchyRules(OrganizationalUnit::TYPE_DEPARTMENT, $this->branchCmd1->id);
        $this->assertNotNull($error);
    }

    public function test_cycle_prevention_in_hierarchy()
    {
        // Try to set mainCmd's parent to center1 (cycle)
        $this->assertTrue($this->mainCmd->wouldCauseCycle($this->center1->id));
        $this->assertTrue($this->branchCmd1->wouldCauseCycle($this->center1->id));

        // It is not a cycle to set center3 under branchCmd1
        $this->assertFalse($this->center3->wouldCauseCycle($this->branchCmd1->id));
    }

    public function test_deactivation_prevention_rules()
    {
        $this->actingAs($this->superAdmin);

        // Cannot deactivate branchCmd1 because center1 and center2 are active children
        $response = $this->post(route('org-units.toggle-active', $this->branchCmd1));
        $response->assertSessionHas('error');
        $this->assertTrue($this->branchCmd1->fresh()->is_active);

        // Add active employee to center3
        Employee::create([
            'name' => 'موظف تجريبي',
            'national_id' => '1099887766',
            'org_unit_id' => $this->center3->id,
            'is_active' => true,
        ]);

        // Cannot deactivate center3 because it has an active employee
        $response = $this->post(route('org-units.toggle-active', $this->center3));
        $response->assertSessionHas('error');
        $this->assertTrue($this->center3->fresh()->is_active);
    }

    public function test_center_admin_scoped_access()
    {
        // Create Center 1 Admin
        $centerAdmin = User::create([
            'name' => 'مدير المركز الأول',
            'email' => 'center1@test.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'is_active' => true,
        ]);
        UserUnitAssignment::create([
            'user_id' => $centerAdmin->id,
            'org_unit_id' => $this->center1->id,
            'role' => UserUnitAssignment::ROLE_CENTER_ADMIN,
        ]);

        $this->actingAs($centerAdmin);

        // Can access Center 1 roster
        $response = $this->get(route('rosters.show', ['orgUnit' => $this->center1->id]));
        $response->assertOk();

        // CANNOT access Center 2 roster (Forbidden 403)
        $response = $this->get(route('rosters.show', ['orgUnit' => $this->center2->id]));
        $response->assertForbidden();

        // CANNOT access Center 3 roster (Forbidden 403)
        $response = $this->get(route('rosters.show', ['orgUnit' => $this->center3->id]));
        $response->assertForbidden();
    }

    public function test_branch_command_admin_scoped_approvals()
    {
        // Create Branch Command 1 Admin
        $branchAdmin = User::create([
            'name' => 'مدير القيادة الفرعية 1',
            'email' => 'branch1@test.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'is_active' => true,
        ]);
        UserUnitAssignment::create([
            'user_id' => $branchAdmin->id,
            'org_unit_id' => $this->branchCmd1->id,
            'role' => UserUnitAssignment::ROLE_BRANCH_COMMAND_ADMIN,
            'include_sub_branches' => true,
        ]);

        // Create rosters for center1 (under branchCmd1) and center3 (under branchCmd2)
        $roster1 = DailyRoster::create([
            'org_unit_id' => $this->center1->id,
            'date' => Carbon::today('Asia/Riyadh')->toDateString(),
            'status' => DailyRoster::STATUS_CENTER_LOCKED,
            'created_by' => $this->superAdmin->id,
            'submitted_by' => $this->superAdmin->id,
            'version' => 1,
        ]);

        $roster3 = DailyRoster::create([
            'org_unit_id' => $this->center3->id,
            'date' => Carbon::today('Asia/Riyadh')->toDateString(),
            'status' => DailyRoster::STATUS_CENTER_LOCKED,
            'created_by' => $this->superAdmin->id,
            'submitted_by' => $this->superAdmin->id,
            'version' => 1,
        ]);

        $this->actingAs($branchAdmin);

        // Branch 1 Admin CAN approve center 1 roster -> completed
        $response = $this->post(route('rosters.branch-approve', $roster1));
        $response->assertSessionHas('status');
        $this->assertEquals(DailyRoster::STATUS_COMPLETED, $roster1->fresh()->status);

        // Verify workflow event created
        $this->assertDatabaseHas('roster_workflow_events', [
            'daily_roster_id' => $roster1->id,
            'from_status' => DailyRoster::STATUS_CENTER_LOCKED,
            'to_status' => DailyRoster::STATUS_COMPLETED,
            'user_id' => $branchAdmin->id,
        ]);

        // Branch 1 Admin CANNOT approve center 3 roster (Belongs to Branch 2 -> Forbidden 403)
        $response = $this->post(route('rosters.branch-approve', $roster3));
        $response->assertForbidden();
        $this->assertEquals(DailyRoster::STATUS_CENTER_LOCKED, $roster3->fresh()->status);
    }

    public function test_roster_submission_locks_editing_and_blocks_unselected_items()
    {
        // Create employee in center1
        $emp = Employee::create([
            'name' => 'سعيد القحطاني',
            'national_id' => '1029384756',
            'org_unit_id' => $this->center1->id,
            'is_active' => true,
        ]);

        $centerAdmin = User::create([
            'name' => 'مدير المركز',
            'email' => 'admin_ctr1@test.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'is_active' => true,
        ]);
        UserUnitAssignment::create([
            'user_id' => $centerAdmin->id,
            'org_unit_id' => $this->center1->id,
            'role' => UserUnitAssignment::ROLE_CENTER_ADMIN,
        ]);

        $this->actingAs($centerAdmin);

        // Open roster (auto-creates draft and item with unselected status)
        $this->get(route('rosters.show', ['orgUnit' => $this->center1->id]));

        $roster = DailyRoster::where('org_unit_id', $this->center1->id)->first();
        $this->assertNotNull($roster);
        $this->assertEquals(DailyRoster::STATUS_DRAFT, $roster->status);

        $item = $roster->items->first();
        $this->assertNotNull($item);
        $this->assertNull($item->status); // Initial state unselected

        // Rule 6: Attempting to submit with unselected status should be REJECTED!
        $response = $this->post(route('rosters.submit', $roster), [
            'version' => $roster->version,
        ]);
        $response->assertSessionHas('error');
        $this->assertEquals(DailyRoster::STATUS_DRAFT, $roster->fresh()->status); // Not submitted

        // Center admin updates item to 'حاضر'
        $response = $this->put(route('rosters.update', $roster), [
            'version' => $roster->version,
            'items' => [
                $item->id => ['status' => 'حاضر', 'notes' => 'حضور في الوقت المحدد'],
            ],
        ]);
        $response->assertSessionHas('status');
        $this->assertEquals('حاضر', $item->fresh()->status);

        // Now submitting roster succeeds
        $response = $this->post(route('rosters.submit', $roster), [
            'version' => $roster->fresh()->version,
        ]);
        $response->assertSessionHas('status');

        $roster->refresh();
        $this->assertEquals(DailyRoster::STATUS_CENTER_LOCKED, $roster->status);

        // Once submitted, center editing is now FORBIDDEN (403)
        $response = $this->put(route('rosters.update', $roster), [
            'version' => $roster->version,
            'items' => [
                $item->id => ['status' => 'غايب'],
            ],
        ]);
        $response->assertForbidden();
    }

    public function test_approved_roster_correction_with_mandatory_reason_and_audit()
    {
        $roster = DailyRoster::create([
            'org_unit_id' => $this->division1->id,
            'date' => Carbon::today('Asia/Riyadh')->toDateString(),
            'status' => DailyRoster::STATUS_COMPLETED,
            'created_by' => $this->superAdmin->id,
            'version' => 1,
        ]);

        $emp = Employee::create([
            'name' => 'خالد السلمي',
            'national_id' => '1055443322',
            'org_unit_id' => $this->division1->id,
            'is_active' => true,
        ]);

        $item = DailyRosterItem::create([
            'daily_roster_id' => $roster->id,
            'employee_id' => $emp->id,
            'date' => $roster->date,
            'status' => DailyRosterItem::STATUS_ABSENT,
            'employee_name_snapshot' => 'خالد السلمي',
            'employee_national_id_snapshot' => '1055443322',
        ]);

        // Regular user cannot correct
        $regularUser = User::create([
            'name' => 'مستخدم عادي',
            'email' => 'regular@test.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'is_active' => true,
        ]);
        $this->actingAs($regularUser);

        $response = $this->post(route('rosters.correct', $roster), [
            'reason' => 'تم التأكد من حضوره لاحقاً',
            'items' => [$item->id => ['status' => DailyRosterItem::STATUS_PRESENT]],
        ]);
        $response->assertForbidden();

        // Super Admin CAN correct, but reason is mandatory
        $this->actingAs($this->superAdmin);

        // Without reason -> validation error
        $response = $this->post(route('rosters.correct', $roster), [
            'reason' => '',
            'items' => [$item->id => ['status' => DailyRosterItem::STATUS_PRESENT]],
        ]);
        $response->assertSessionHasErrors('reason');

        // With valid reason
        $response = $this->post(route('rosters.correct', $roster), [
            'reason' => 'تم تقديم عذر رسمي معتمد من الإدارة العليا',
            'items' => [$item->id => ['status' => DailyRosterItem::STATUS_COURSE, 'notes' => 'دورة تدريبية خارجية']],
        ]);
        $response->assertSessionHas('status');

        $this->assertEquals(DailyRosterItem::STATUS_COURSE, $item->fresh()->status);
        $this->assertTrue($roster->fresh()->was_corrected);

        // Verify audit log entry
        $this->assertDatabaseHas('roster_audit_logs', [
            'daily_roster_id' => $roster->id,
            'daily_roster_item_id' => $item->id,
            'user_id' => $this->superAdmin->id,
            'action' => 'corrected',
            'old_value' => DailyRosterItem::STATUS_ABSENT,
            'new_value' => DailyRosterItem::STATUS_COURSE,
            'reason' => 'تم تقديم عذر رسمي معتمد من الإدارة العليا',
        ]);
    }

    public function test_optimistic_locking_prevents_concurrent_overwrite()
    {
        $roster = DailyRoster::create([
            'org_unit_id' => $this->division1->id,
            'date' => Carbon::today('Asia/Riyadh')->toDateString(),
            'status' => DailyRoster::STATUS_DRAFT,
            'created_by' => $this->superAdmin->id,
            'version' => 5,
        ]);

        $emp = Employee::create([
            'name' => 'تجربة القفل',
            'national_id' => '1011223344',
            'org_unit_id' => $this->division1->id,
            'is_active' => true,
        ]);

        $item = DailyRosterItem::create([
            'daily_roster_id' => $roster->id,
            'employee_id' => $emp->id,
            'date' => $roster->date,
            'status' => 'حاضر',
            'employee_name_snapshot' => 'تجربة القفل',
        ]);

        $this->actingAs($this->superAdmin);

        // Submitting with stale version (e.g. 4 instead of 5)
        $response = $this->put(route('rosters.update', $roster), [
            'version' => 4,
            'items' => [
                $item->id => ['status' => 'غايب'],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('حاضر', $item->fresh()->status); // Remains unchanged
    }

    public function test_cannot_deactivate_or_strip_sole_super_admin()
    {
        $this->actingAs($this->superAdmin);

        // Try to deactivate the super admin
        $response = $this->post(route('user-management.toggle-active', $this->superAdmin));
        $response->assertSessionHas('error');
        $this->assertTrue($this->superAdmin->fresh()->is_active);

        // Try to update role to division admin
        $response = $this->put(route('user-management.update', $this->superAdmin), [
            'name' => $this->superAdmin->name,
            'email' => $this->superAdmin->email,
            'role' => UserUnitAssignment::ROLE_DIVISION_ADMIN,
            'org_unit_id' => $this->division1->id,
            'is_active' => '1',
        ]);
        $response->assertSessionHasErrors('role');
    }

    public function test_roster_reports_and_export_scoped_properly()
    {
        $roster = DailyRoster::create([
            'org_unit_id' => $this->center1->id,
            'date' => Carbon::today('Asia/Riyadh')->toDateString(),
            'status' => DailyRoster::STATUS_COMPLETED,
            'unit_name_snapshot' => 'المركز الأول',
            'created_by' => $this->superAdmin->id,
            'version' => 1,
        ]);

        $emp = Employee::create([
            'name' => 'فهد المطيري',
            'national_id' => '1088776655',
            'org_unit_id' => $this->center1->id,
            'is_active' => true,
        ]);

        DailyRosterItem::create([
            'daily_roster_id' => $roster->id,
            'employee_id' => $emp->id,
            'date' => $roster->date,
            'status' => 'انتداب',
            'notes' => 'انتداب رسمي',
            'employee_name_snapshot' => $emp->name,
            'employee_national_id_snapshot' => $emp->national_id,
        ]);

        $this->actingAs($this->superAdmin);

        // Report index view
        $response = $this->get(route('roster-reports.index'));
        $response->assertOk();
        $response->assertSee('فهد المطيري');
        $response->assertSee('انتداب');

        // CSV export
        $response = $this->get(route('roster-reports.export'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_employee_cannot_exist_in_two_rosters_on_same_date_database_constraint()
    {
        $date = Carbon::today('Asia/Riyadh')->toDateString();

        $emp = Employee::create([
            'name' => 'محمد الحربي',
            'national_id' => '1033221100',
            'org_unit_id' => $this->center1->id,
            'is_active' => true,
        ]);

        $roster1 = DailyRoster::create([
            'org_unit_id' => $this->center1->id,
            'date' => $date,
            'status' => DailyRoster::STATUS_DRAFT,
            'created_by' => $this->superAdmin->id,
            'version' => 1,
        ]);

        $roster2 = DailyRoster::create([
            'org_unit_id' => $this->center2->id,
            'date' => $date,
            'status' => DailyRoster::STATUS_DRAFT,
            'created_by' => $this->superAdmin->id,
            'version' => 1,
        ]);

        // Insert in roster 1
        DailyRosterItem::create([
            'daily_roster_id' => $roster1->id,
            'employee_id' => $emp->id,
            'date' => $date,
            'status' => 'حاضر',
        ]);

        // Trying to insert same employee on the same date into roster 2 MUST trigger unique constraint exception
        $this->expectException(QueryException::class);
        DailyRosterItem::create([
            'daily_roster_id' => $roster2->id,
            'employee_id' => $emp->id,
            'date' => $date,
            'status' => 'حاضر',
        ]);
    }

    public function test_legacy_user_migration_logic()
    {
        // 1. User with employee role
        $legacyEmp = User::create([
            'name' => 'موظف قديم',
            'email' => 'legacy_emp@test.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        // 2. Pure admin with no attendance records
        $pureAdmin = User::create([
            'name' => 'مدير إداري بحت',
            'email' => 'pure_admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Run the migration class up()
        $migration = require database_path('migrations/2026_10_17_012525_migrate_legacy_users_to_employees.php');
        $migration->up();

        // Employee user must be migrated with needs_review = true
        $this->assertDatabaseHas('employees', [
            'user_id' => $legacyEmp->id,
            'name' => 'موظف قديم',
            'needs_review' => true,
        ]);

        // Pure admin without attendance must NOT be converted into an employee
        $this->assertDatabaseMissing('employees', [
            'user_id' => $pureAdmin->id,
        ]);
    }

    public function test_permission_isolation_between_units()
    {
        // User assigned to Division 1 with can_edit_approved
        $divAdmin = User::create([
            'name' => 'مسؤول شعبة الإدارة',
            'email' => 'divadmin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        UserUnitAssignment::create([
            'user_id' => $divAdmin->id,
            'org_unit_id' => $this->division1->id,
            'role' => UserUnitAssignment::ROLE_DIVISION_ADMIN,
            'can_edit_approved' => true,
            'can_manage_employees' => true,
        ]);

        // He has permission on Division 1
        $this->assertTrue($divAdmin->canAccessUnit($this->division1));
        $this->assertTrue($divAdmin->hasUnitPermission($this->division1, 'can_edit_approved'));

        // He does NOT have permission on Center 1 or Branch Command 1
        $this->assertFalse($divAdmin->canAccessUnit($this->center1));
        $this->assertFalse($divAdmin->hasUnitPermission($this->center1, 'can_edit_approved'));
        $this->assertFalse($divAdmin->canAccessUnit($this->branchCmd1));
    }
}
