<?php

namespace App\Http\Controllers;

use App\Models\DailyRosterItem;
use App\Models\OrganizationalUnit;
use App\Models\RosterAuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AbsenceInvestigationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of absent items with their investigation status and countdown.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $permittedUnitIds = $user->getPermittedOrgUnitIds();

        $query = DailyRosterItem::with(['dailyRoster.organizationalUnit', 'employee', 'dailyRoster'])
            ->where('status', DailyRosterItem::STATUS_ABSENT);

        if ($permittedUnitIds !== null) {
            $query->whereHas('dailyRoster', fn($q) => $q->whereIn('org_unit_id', $permittedUnitIds));
        }

        if ($request->filled('org_unit_id')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('org_unit_id', (int) $request->org_unit_id));
        }

        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'investigated') {
                $query->whereNotNull('investigation_number')->where('investigation_number', '!=', '');
            } elseif ($request->filter_status === 'pending') {
                $sevenDaysAgo = now('Asia/Riyadh')->subDays(7)->startOfDay();
                $query->where(function ($q) {
                    $q->whereNull('investigation_number')->orWhere('investigation_number', '');
                })->where('date', '>=', $sevenDaysAgo);
            } elseif ($request->filter_status === 'overdue') {
                $sevenDaysAgo = now('Asia/Riyadh')->subDays(7)->startOfDay();
                $query->where(function ($q) {
                    $q->whereNull('investigation_number')->orWhere('investigation_number', '');
                })->where('date', '<', $sevenDaysAgo);
            }
        }

        $items = $query->orderBy('date', 'desc')->paginate(20);

        // Calculate KPI metrics
        $baseKpiQuery = DailyRosterItem::where('status', DailyRosterItem::STATUS_ABSENT);
        if ($permittedUnitIds !== null) {
            $baseKpiQuery->whereHas('dailyRoster', fn($q) => $q->whereIn('org_unit_id', $permittedUnitIds));
        }

        $sevenDaysAgo = now('Asia/Riyadh')->subDays(7)->startOfDay();

        $totalAbsentCount = (clone $baseKpiQuery)->count();
        $investigatedCount = (clone $baseKpiQuery)->whereNotNull('investigation_number')->where('investigation_number', '!=', '')->count();
        $overdueCount = (clone $baseKpiQuery)
            ->where(function ($q) {
                $q->whereNull('investigation_number')->orWhere('investigation_number', '');
            })
            ->where('date', '<', $sevenDaysAgo)
            ->count();
        $pendingCount = (clone $baseKpiQuery)
            ->where(function ($q) {
                $q->whereNull('investigation_number')->orWhere('investigation_number', '');
            })
            ->where('date', '>=', $sevenDaysAgo)
            ->count();

        $unitsQuery = OrganizationalUnit::where('is_active', true)->orderBy('name');
        if ($permittedUnitIds !== null) {
            $unitsQuery->whereIn('id', $permittedUnitIds);
        }
        $units = $unitsQuery->get();

        return view('roster_reports.investigations', compact(
            'items',
            'units',
            'totalAbsentCount',
            'investigatedCount',
            'overdueCount',
            'pendingCount'
        ));
    }

    /**
     * Record investigation results / decision for an absent employee.
     */
    public function update(Request $request, DailyRosterItem $item)
    {
        $user = $request->user();

        // Check permission on the unit
        if (!$user->canAccessUnit($item->dailyRoster->organizationalUnit, 'can_manage_employees') && !$user->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بتسجيل نتائج التحقيق لهذه الجهة.');
        }

        $validated = $request->validate([
            'investigation_number' => ['required', 'string', 'max:100'],
            'investigation_status' => ['required', 'in:completed,pending'],
            'investigation_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'investigation_number.required' => 'حقل رقم التحقيق / قيد المساءلة إلزامي.',
        ]);

        $oldInvestigationNumber = $item->investigation_number;

        $item->update([
            'investigation_number' => $validated['investigation_number'],
            'investigation_status' => $validated['investigation_status'],
            'investigation_notes' => $validated['investigation_notes'] ?? null,
            'investigated_at' => now('Asia/Riyadh'),
            'investigated_by' => $user->id,
        ]);

        // Audit logging
        RosterAuditLog::create([
            'daily_roster_id' => $item->daily_roster_id,
            'daily_roster_item_id' => $item->id,
            'user_id' => $user->id,
            'action' => 'investigation_updated',
            'old_value' => $oldInvestigationNumber ?: 'لا يوجد',
            'new_value' => $validated['investigation_number'],
            'reason' => 'تسجيل نتيجة التحقيق الإداري: ' . ($validated['investigation_notes'] ?? 'تم إدخال رقم القيد'),
        ]);

        return redirect()->back()->with('status', 'تم حفظ بيانات التحقيق بنجاح برقم: ' . $validated['investigation_number']);
    }
}
