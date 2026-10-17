<?php

namespace App\Http\Controllers;

use App\Models\DailyRoster;
use App\Models\DailyRosterItem;
use App\Models\OrganizationalUnit;
use App\Models\RosterAuditLog;
use App\Models\RosterWorkflowEvent;
use App\Models\StructureAuditLog;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RosterReportController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', DailyRoster::class);
        $user = $request->user();
        $permittedUnitIds = $user->getPermittedOrgUnitIds();

        // Scope check for requested unit
        if ($request->filled('org_unit_id')) {
            $unit = OrganizationalUnit::findOrFail((int)$request->org_unit_id);
            if (!$user->canAccessUnit($unit)) {
                abort(403, 'غير مصرح لك بالوصول لبيانات هذه الجهة التنظيمية.');
            }
        }

        $query = DailyRosterItem::with(['dailyRoster.organizationalUnit', 'employee']);

        if ($permittedUnitIds !== null) {
            $query->whereHas('dailyRoster', fn($q) => $q->whereIn('org_unit_id', $permittedUnitIds));
        }

        if ($request->filled('org_unit_id')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('org_unit_id', (int)$request->org_unit_id));
        }

        if ($request->filled('date_from')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('date', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('date', '<=', $request->date_to));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(25);

        $unitsQuery = OrganizationalUnit::where('is_active', true)->orderBy('name');
        if ($permittedUnitIds !== null) {
            $unitsQuery->whereIn('id', $permittedUnitIds);
        }
        $units = $unitsQuery->get();
        $statuses = DailyRosterItem::allStatuses();

        return view('roster_reports.index', compact('items', 'units', 'statuses'));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $permittedUnitIds = $user->getPermittedOrgUnitIds();

        // Strict Policy and Scope Validation on Export
        if ($request->filled('org_unit_id')) {
            $unit = OrganizationalUnit::findOrFail((int)$request->org_unit_id);
            if (!$user->canAccessUnit($unit)) {
                abort(403, 'غير مصرح لك بتصدير بيانات هذه الجهة.');
            }
        } elseif (!$user->isSuperAdmin() && empty($permittedUnitIds)) {
            abort(403, 'لا تملك أي صلاحية لتصدير بيانات الكشوفات.');
        }

        $query = DailyRosterItem::with(['dailyRoster.organizationalUnit', 'employee']);

        if ($permittedUnitIds !== null) {
            $query->whereHas('dailyRoster', fn($q) => $q->whereIn('org_unit_id', $permittedUnitIds));
        }

        if ($request->filled('org_unit_id')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('org_unit_id', (int)$request->org_unit_id));
        }

        if ($request->filled('date_from')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('date', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('dailyRoster', fn($q) => $q->where('date', '<=', $request->date_to));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('created_at', 'desc')->get();

        $filename = 'attendance-roster-report-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['التاريخ', 'الجهة التنظيمية', 'المسار الإداري', 'اسم الموظف', 'رقم الهوية', 'الحالة', 'ملاحظات', 'حالة السجل']);

            foreach ($records as $item) {
                fputcsv($handle, [
                    $item->dailyRoster->date->toDateString(),
                    $item->dailyRoster->unit_name_snapshot ?? ($item->dailyRoster->organizationalUnit->name ?? ''),
                    $item->dailyRoster->unit_path_snapshot ?? '',
                    $item->employee_name_snapshot ?? ($item->employee->name ?? ''),
                    $item->employee_national_id_snapshot ?? ($item->employee->masked_national_id ?? ''),
                    $item->status ?? 'غير محدد',
                    $item->notes ?? '',
                    $item->dailyRoster->status_label,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function auditLogs(Request $request)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'غير مصرح لك باستعراض سجل التدقيق الشامل.');
        }

        $type = $request->input('type', 'corrections');

        $correctionLogs = RosterAuditLog::with(['dailyRoster.organizationalUnit', 'dailyRosterItem.employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(25, ['*'], 'corrections_page');

        $workflowEvents = RosterWorkflowEvent::with(['dailyRoster.organizationalUnit', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(25, ['*'], 'workflow_page');

        $structureLogs = StructureAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25, ['*'], 'structure_page');

        return view('roster_reports.audit_logs', compact('correctionLogs', 'workflowEvents', 'structureLogs', 'type'));
    }
}
