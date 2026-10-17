<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\StructureAuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganizationalUnitController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', OrganizationalUnit::class);

        // Fetch tree starting from root (main_command or units without parent)
        $units = OrganizationalUnit::with(['children.children.children', 'employees'])
            ->withCount(['employees', 'children'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $rootUnits = $units->whereNull('parent_id');

        return view('org_units.index', compact('units', 'rootUnits'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', OrganizationalUnit::class);

        $parents = OrganizationalUnit::where('is_active', true)->orderBy('name')->get();
        $typeLabels = OrganizationalUnit::typeLabels();

        return view('org_units.create', compact('parents', 'typeLabels'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', OrganizationalUnit::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:organizational_units,code'],
            'type' => ['required', 'string', Rule::in(array_keys(OrganizationalUnit::typeLabels()))],
            'parent_id' => ['nullable', 'exists:organizational_units,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Check hierarchy rule
        $hierarchyError = OrganizationalUnit::validateHierarchyRules($validated['type'], $validated['parent_id'] ? (int)$validated['parent_id'] : null);
        if ($hierarchyError) {
            return back()->withInput()->withErrors(['type' => $hierarchyError]);
        }

        $unit = OrganizationalUnit::create($validated);

        StructureAuditLog::create([
            'user_id' => $request->user()->id,
            'target_type' => 'unit',
            'target_id' => $unit->id,
            'action' => 'unit_created',
            'old_values' => null,
            'new_values' => $unit->only(['name', 'code', 'type', 'parent_id', 'is_active']),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return redirect()->route('org-units.index')->with('status', 'تمت إضافة الوحدة التنظيمية بنجاح.');
    }

    public function edit(OrganizationalUnit $orgUnit)
    {
        $this->authorize('update', $orgUnit);

        $parents = OrganizationalUnit::where('id', '!=', $orgUnit->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $typeLabels = OrganizationalUnit::typeLabels();

        return view('org_units.edit', compact('orgUnit', 'parents', 'typeLabels'));
    }

    public function update(Request $request, OrganizationalUnit $orgUnit)
    {
        $this->authorize('update', $orgUnit);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('organizational_units')->ignore($orgUnit->id)],
            'type' => ['required', 'string', Rule::in(array_keys(OrganizationalUnit::typeLabels()))],
            'parent_id' => ['nullable', 'exists:organizational_units,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $newParentId = $validated['parent_id'] ? (int)$validated['parent_id'] : null;

        // Prevent circular reference
        if ($orgUnit->wouldCauseCycle($newParentId)) {
            return back()->withInput()->withErrors(['parent_id' => 'لا يمكن اختيار جهة تابعة أو الوحدة نفسها لتفادي التكرار الدائري.']);
        }

        // Hierarchy rule validation
        $hierarchyError = OrganizationalUnit::validateHierarchyRules($validated['type'], $newParentId, $orgUnit->id);
        if ($hierarchyError) {
            return back()->withInput()->withErrors(['type' => $hierarchyError]);
        }

        // Prevent breaking historical records or changing structure if records exist
        if ($orgUnit->type !== $validated['type'] || (int)$orgUnit->parent_id !== (int)$newParentId) {
            if ($orgUnit->dailyRosters()->exists()) {
                return back()->withInput()->withErrors([
                    'type' => 'لا يمكن تعديل نوع الجهة أو تبعيتها لوجود سجلات تحضير تاريخية مرتبطة بها.'
                ]);
            }
        }

        $oldValues = $orgUnit->only(['name', 'code', 'type', 'parent_id', 'is_active']);
        $orgUnit->update($validated);

        StructureAuditLog::create([
            'user_id' => $request->user()->id,
            'target_type' => 'unit',
            'target_id' => $orgUnit->id,
            'action' => 'unit_updated',
            'old_values' => $oldValues,
            'new_values' => $orgUnit->only(['name', 'code', 'type', 'parent_id', 'is_active']),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return redirect()->route('org-units.index')->with('status', 'تم تحديث بيانات الوحدة التنظيمية بنجاح.');
    }

    public function toggleActive(OrganizationalUnit $orgUnit)
    {
        $this->authorize('toggleActive', $orgUnit);

        // If trying to deactivate, check active children or active employees
        if ($orgUnit->is_active) {
            if ($orgUnit->activeChildren()->exists()) {
                return back()->with('error', 'لا يمكن تعطيل هذه الجهة لوجود جهات فرعية تابعة لا تزال نشطة.');
            }

            if ($orgUnit->activeEmployees()->exists()) {
                return back()->with('error', 'لا يمكن تعطيل هذه الجهة لوجود موظفين نشطين مسجلين عليها.');
            }
        }

        $oldActive = $orgUnit->is_active;
        $orgUnit->update(['is_active' => !$orgUnit->is_active]);

        StructureAuditLog::create([
            'user_id' => auth()->id(),
            'target_type' => 'unit',
            'target_id' => $orgUnit->id,
            'action' => $orgUnit->is_active ? 'unit_activated' : 'unit_deactivated',
            'old_values' => ['is_active' => $oldActive],
            'new_values' => ['is_active' => $orgUnit->is_active],
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $msg = $orgUnit->is_active ? 'تم تفعيل الجهة بنجاح.' : 'تم تعطيل الجهة بنجاح.';
        return back()->with('status', $msg);
    }
}
