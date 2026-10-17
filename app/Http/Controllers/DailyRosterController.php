<?php

namespace App\Http\Controllers;

use App\Models\DailyRoster;
use App\Models\DailyRosterItem;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\RosterAuditLog;
use App\Models\RosterWorkflowEvent;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyRosterController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', DailyRoster::class);
        $user = $request->user();
        $date = $request->input('date', Carbon::now('Asia/Riyadh')->toDateString());

        $permittedUnitIds = $user->getPermittedOrgUnitIds();

        // Retrieve units in user scope
        $unitsQuery = OrganizationalUnit::where('is_active', true)->orderBy('name');
        if ($permittedUnitIds !== null) {
            $unitsQuery->whereIn('id', $permittedUnitIds);
        }
        $units = $unitsQuery->get();

        // Get rosters for these units on selected date
        $rosters = DailyRoster::with(['organizationalUnit', 'creator', 'submitter', 'branchApprover', 'fullApprover', 'items'])
            ->where('date', $date)
            ->when($permittedUnitIds !== null, fn($q) => $q->whereIn('org_unit_id', $permittedUnitIds))
            ->get()
            ->keyBy('org_unit_id');

        return view('daily_rosters.index', compact('units', 'rosters', 'date'));
    }

    /**
     * Show incoming rosters pending review / approval
     */
    public function incoming(Request $request)
    {
        $user = $request->user();
        $permittedUnitIds = $user->getPermittedOrgUnitIds();

        $query = DailyRoster::with(['organizationalUnit.parent', 'submitter', 'branchApprover'])
            ->orderBy('date', 'desc');

        if ($user->isSuperAdmin()) {
            $query->where('status', DailyRoster::STATUS_CENTER_LOCKED);
        } else {
            // Check if user has branch command assignments
            $branchUnitIds = $user->unitAssignments()
                ->where('role', \App\Models\UserUnitAssignment::ROLE_BRANCH_COMMAND_ADMIN)
                ->pluck('org_unit_id')
                ->filter()
                ->toArray();

            if (!empty($branchUnitIds)) {
                // Find all centers under these branch commands
                $centerIds = OrganizationalUnit::whereIn('parent_id', $branchUnitIds)->pluck('id')->toArray();
                $query->whereIn('org_unit_id', $centerIds)
                      ->where('status', DailyRoster::STATUS_CENTER_LOCKED);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $incomingRosters = $query->paginate(20);

        return view('daily_rosters.incoming', compact('incomingRosters'));
    }

    /**
     * Create or edit a draft roster for a unit on a given date
     */
    public function show(Request $request, OrganizationalUnit $orgUnit)
    {
        $user = $request->user();
        $date = $request->input('date', Carbon::now('Asia/Riyadh')->toDateString());

        if (!$user->canAccessUnit($orgUnit)) {
            abort(403, 'غير مصرح لك بالوصول لتحضير هذه الجهة.');
        }

        // Find or create draft roster
        $roster = DailyRoster::firstOrCreate(
            ['org_unit_id' => $orgUnit->id, 'date' => $date],
            [
                'status' => DailyRoster::STATUS_DRAFT,
                'created_by' => $user->id,
                'version' => 1,
            ]
        );

        if (!$roster->unit_name_snapshot) {
            $roster->freezeUnitSnapshot();
            $roster->save();
        }

        // Ensure all active employees in this unit have an item row in the roster
        $employees = $orgUnit->activeEmployees()->orderBy('name')->get();
        foreach ($employees as $employee) {
            DailyRosterItem::firstOrCreate(
                [
                    'daily_roster_id' => $roster->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'date' => $roster->date,
                    'status' => null, // Left unselected in draft
                    'employee_name_snapshot' => $employee->name,
                    'employee_national_id_snapshot' => $employee->masked_national_id,
                    'employee_job_title_snapshot' => $employee->job_title,
                ]
            );
        }

        $roster->load(['items.employee', 'auditLogs.user', 'workflowEvents.user']);
        $statuses = DailyRosterItem::allStatuses();

        return view('daily_rosters.show', compact('orgUnit', 'roster', 'date', 'statuses'));
    }

    /**
     * Save draft modifications (with atomic pessimistic locking and version increment)
     */
    public function update(Request $request, DailyRoster $roster)
    {
        $this->authorize('update', $roster);

        $validated = $request->validate([
            'version' => ['required', 'integer'],
            'items' => ['required', 'array'],
            'items.*.status' => ['nullable', 'string', 'in:' . implode(',', DailyRosterItem::allStatuses())],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $submittedVersion = (int) $validated['version'];

        $result = DB::transaction(function () use ($roster, $submittedVersion, $validated) {
            $lockedRoster = DailyRoster::where('id', $roster->id)->lockForUpdate()->firstOrFail();

            if ($lockedRoster->version !== $submittedVersion) {
                return ['error' => 'تم تعديل هذا السجل بواسطة مستخدم آخر. يرجى تحديث الصفحة والمحاولة مرة أخرى.'];
            }

            if ($lockedRoster->isLocked()) {
                return ['error' => 'لا يمكن تعديل هذا الكشف؛ السجل معتمد ومقفل.'];
            }

            foreach ($validated['items'] as $itemId => $itemData) {
                DailyRosterItem::where('id', $itemId)
                    ->where('daily_roster_id', $lockedRoster->id)
                    ->update([
                        'status' => $itemData['status'] ?: null,
                        'notes' => $itemData['notes'] ?: null,
                    ]);
            }

            $lockedRoster->increment('version');
            return ['success' => 'تم حفظ مسودة التحضير بنجاح.'];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('status', $result['success']);
    }

    /**
     * Submit / Initial Approval of the roster:
     * - Center: draft -> center_locked (locks center, awaits branch command approval)
     * - Branch Command / Division / Department / Main Command: draft -> completed directly
     * - Rule 6: Prohibits submitting if any employee is left unselected.
     */
    public function submit(Request $request, DailyRoster $roster)
    {
        $this->authorize('submit', $roster);
        $user = $request->user();

        $result = DB::transaction(function () use ($roster, $request, $user) {
            $lockedRoster = DailyRoster::where('id', $roster->id)->lockForUpdate()->firstOrFail();

            if ($request->filled('version') && $lockedRoster->version !== (int)$request->version) {
                return ['error' => 'تم تعديل هذا السجل مؤخراً بواسطة مستخدم آخر. يرجى تحديث الصفحة.'];
            }

            if ($lockedRoster->isLocked()) {
                return ['error' => 'هذا السجل مقفل مسبقاً ضد الاعتماد.'];
            }

            // If items are submitted in this request, update them before validating
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemId => $itemData) {
                    if (is_array($itemData)) {
                        DailyRosterItem::where('id', $itemId)
                            ->where('daily_roster_id', $lockedRoster->id)
                            ->update([
                                'status' => !empty($itemData['status']) ? $itemData['status'] : null,
                                'notes' => !empty($itemData['notes']) ? $itemData['notes'] : null,
                            ]);
                    }
                }
            }

            // Rule 6: Validate that NO employee has an unselected status
            $unselectedCount = $lockedRoster->items()->whereNull('status')->count();
            if ($unselectedCount > 0) {
                return ['error' => "لا يمكن اعتماد الكشف؛ يوجد {$unselectedCount} موظف لم يتم تحديد حالتهم بعد. يجب تحديد حالة لجميع الموظفين قبل الاعتماد."];
            }

            $unit = $lockedRoster->organizationalUnit;
            $now = Carbon::now('Asia/Riyadh');
            $oldStatus = $lockedRoster->status;

            if ($unit->type === OrganizationalUnit::TYPE_CENTER) {
                $newStatus = DailyRoster::STATUS_CENTER_LOCKED;
                $stageLabel = 'اعتماد وقفل أولي من مسؤول المركز (بانتظار القيادة الفرعية)';

                $lockedRoster->update([
                    'status' => $newStatus,
                    'submitted_by' => $user->id,
                    'submitted_at' => $now,
                    'version' => $lockedRoster->version + 1,
                ]);
            } else {
                $newStatus = DailyRoster::STATUS_COMPLETED;
                $stageLabel = 'اعتماد نهائي واكتمال مسار التحضير المباشر';

                $lockedRoster->update([
                    'status' => $newStatus,
                    'submitted_by' => $user->id,
                    'submitted_at' => $now,
                    'fully_approved_by' => $user->id,
                    'fully_approved_at' => $now,
                    'version' => $lockedRoster->version + 1,
                ]);
            }

            // Freeze snapshots
            $lockedRoster->freezeUnitSnapshot();
            $lockedRoster->save();

            $lockedRoster->load('items');
            foreach ($lockedRoster->items as $item) {
                $item->freezeEmployeeSnapshot();
                $item->save();
            }

            // Rule 4: Record workflow event
            RosterWorkflowEvent::create([
                'daily_roster_id' => $lockedRoster->id,
                'user_id' => $user->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'stage_label' => $stageLabel,
                'notes' => 'اعتماد وتثبيت كشف الحضور والغياب اليومي',
                'roster_version' => $lockedRoster->version,
                'created_at' => $now,
            ]);

            return ['success' => 'تم اعتماد الكشف وقفل التحرير بنجاح.'];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('status', $result['success']);
    }

    /**
     * Branch command approves center roster -> completed (Workflow complete, no 3rd approval needed).
     */
    public function branchApprove(Request $request, DailyRoster $roster)
    {
        $this->authorize('branchApprove', $roster);
        $user = $request->user();

        $result = DB::transaction(function () use ($roster, $user) {
            $lockedRoster = DailyRoster::where('id', $roster->id)->lockForUpdate()->firstOrFail();

            if ($lockedRoster->status !== DailyRoster::STATUS_CENTER_LOCKED) {
                return ['error' => 'حالة السجل لا تسمح باعتماد القيادة الفرعية.'];
            }

            $unselectedCount = $lockedRoster->items()->whereNull('status')->count();
            if ($unselectedCount > 0) {
                return ['error' => "لا يمكن اعتماد الكشف؛ يوجد {$unselectedCount} موظف لم يتم تحديد حالتهم بعد."];
            }

            $now = Carbon::now('Asia/Riyadh');
            $oldStatus = $lockedRoster->status;
            $newStatus = DailyRoster::STATUS_COMPLETED;

            $lockedRoster->update([
                'status' => $newStatus,
                'branch_approved_by' => $user->id,
                'branch_approved_at' => $now,
                'fully_approved_by' => $user->id,
                'fully_approved_at' => $now,
                'version' => $lockedRoster->version + 1,
            ]);

            $lockedRoster->freezeUnitSnapshot();
            $lockedRoster->save();

            RosterWorkflowEvent::create([
                'daily_roster_id' => $lockedRoster->id,
                'user_id' => $user->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'stage_label' => 'اعتماد القيادة الفرعية واكتمال مسار كشف المركز',
                'notes' => 'تم اعتماد تحضير المركز من القيادة الفرعية واكتمال المسار',
                'roster_version' => $lockedRoster->version,
                'created_at' => $now,
            ]);

            return ['success' => 'تم اعتماد كشف المركز واكتمال دورة التحضير بنجاح.'];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('status', $result['success']);
    }

    /**
     * Correct an already approved roster (Requires super_admin OR main_command_admin with can_edit_approved)
     * Mandatory Reason required, recorded in immutable audit logs.
     */
    public function correct(Request $request, DailyRoster $roster)
    {
        $this->authorize('correct', $roster);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'items' => ['required', 'array'],
            'items.*.status' => ['required', 'string', 'in:' . implode(',', DailyRosterItem::allStatuses())],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($roster, $validated, $user) {
            $lockedRoster = DailyRoster::where('id', $roster->id)->lockForUpdate()->firstOrFail();
            $now = Carbon::now('Asia/Riyadh');
            $newVersion = $lockedRoster->version + 1;

            foreach ($validated['items'] as $itemId => $itemData) {
                $item = DailyRosterItem::where('id', $itemId)
                    ->where('daily_roster_id', $lockedRoster->id)
                    ->first();

                if ($item && ($item->status !== $itemData['status'] || $item->notes !== ($itemData['notes'] ?? null))) {
                    RosterAuditLog::create([
                        'daily_roster_id' => $lockedRoster->id,
                        'daily_roster_item_id' => $item->id,
                        'user_id' => $user->id,
                        'action' => 'corrected',
                        'field_changed' => 'status',
                        'old_value' => $item->status,
                        'new_value' => $itemData['status'],
                        'reason' => $validated['reason'],
                        'roster_version' => $newVersion,
                        'created_at' => $now,
                    ]);

                    $item->update([
                        'status' => $itemData['status'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            $lockedRoster->update([
                'was_corrected' => true,
                'version' => $newVersion,
            ]);
        });

        return back()->with('status', 'تم تصحيح السجل المعتمد وتوثيق سبب التعديل في سجل التدقيق بنجاح.');
    }
}
