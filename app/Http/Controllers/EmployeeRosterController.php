<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\StructureAuditLog;
use App\Models\User;
use App\Models\UserUnitAssignment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeRosterController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $user = $request->user();

        $permittedUnitIds = $user->getPermittedOrgUnitIds();

        $query = Employee::with(['organizationalUnit', 'user']);

        if ($permittedUnitIds !== null) {
            $query->whereIn('org_unit_id', $permittedUnitIds);
        }

        if ($request->filled('org_unit_id')) {
            $reqUnitId = (int) $request->org_unit_id;
            if ($permittedUnitIds === null || in_array($reqUnitId, $permittedUnitIds, true)) {
                $query->where('org_unit_id', $reqUnitId);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('needs_review')) {
            $query->where('needs_review', $request->needs_review === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(20);

        // Filter units for the dropdown based on permitted scope
        $unitsQuery = OrganizationalUnit::where('is_active', true)->orderBy('name');
        if ($permittedUnitIds !== null) {
            $unitsQuery->whereIn('id', $permittedUnitIds);
        }
        $units = $unitsQuery->get();

        $needsReviewCount = Employee::where('needs_review', true)->count();

        return view('roster_employees.index', compact('employees', 'units', 'needsReviewCount'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $this->authorize('create', [Employee::class, $request->org_unit_id ? (int)$request->org_unit_id : null]);

        $permittedUnitIds = $user->getPermittedOrgUnitIds('can_manage_employees');
        $unitsQuery = OrganizationalUnit::where('is_active', true)->orderBy('name');
        if ($permittedUnitIds !== null) {
            $unitsQuery->whereIn('id', $permittedUnitIds);
        }
        $units = $unitsQuery->get();

        return view('roster_employees.create', compact('units'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $orgUnitId = $request->filled('org_unit_id') ? (int)$request->org_unit_id : null;

        $this->authorize('create', [Employee::class, $orgUnitId]);

        $permittedUnitIds = $user->getPermittedOrgUnitIds('can_manage_employees');
        if ($permittedUnitIds !== null && !in_array($orgUnitId, $permittedUnitIds, true)) {
            abort(403, 'غير مصرح لك بإضافة موظف في هذه الجهة.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'national_id' => ['required', 'string', 'max:50', 'unique:employees,national_id'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'org_unit_id' => ['required', 'exists:organizational_units,id'],
            'is_active' => ['sometimes', 'boolean'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8', 'required_with:email'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['needs_review'] = false;

        $employee = DB::transaction(function () use ($validated, $user, $request) {
            $createdUserId = null;

            if (!empty($validated['email'])) {
                $newUser = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'employee',
                    'is_active' => $validated['is_active'],
                    'force_password_change' => false,
                ]);

                $createdUserId = $newUser->id;
            }

            $employeeData = [
                'name' => $validated['name'],
                'national_id' => $validated['national_id'],
                'job_title' => $validated['job_title'] ?? null,
                'org_unit_id' => $validated['org_unit_id'],
                'user_id' => $createdUserId,
                'is_active' => $validated['is_active'],
                'needs_review' => false,
            ];

            $employee = Employee::create($employeeData);

            StructureAuditLog::create([
                'user_id' => $user->id,
                'target_type' => 'employee',
                'target_id' => $employee->id,
                'action' => 'employee_created',
                'old_values' => null,
                'new_values' => $employee->only(['name', 'national_id', 'org_unit_id', 'job_title', 'user_id']),
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);

            return $employee;
        });

        return redirect()->route('roster-employees.index')->with('status', 'تمت إضافة الموظف بنجاح.');
    }

    public function edit(Employee $employee)
    {
        $this->authorize('update', $employee);
        $user = auth()->user();

        $permittedUnitIds = $user->getPermittedOrgUnitIds('can_manage_employees');
        $unitsQuery = OrganizationalUnit::where('is_active', true)->orderBy('name');
        if ($permittedUnitIds !== null) {
            $unitsQuery->whereIn('id', $permittedUnitIds);
        }
        $units = $unitsQuery->get();

        return view('roster_employees.edit', compact('employee', 'units'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $user = $request->user();

        $newUnitId = $request->filled('org_unit_id') ? (int)$request->org_unit_id : null;
        $permittedUnitIds = $user->getPermittedOrgUnitIds('can_manage_employees');
        if ($permittedUnitIds !== null && !in_array($newUnitId, $permittedUnitIds, true)) {
            abort(403, 'غير مصرح لك بنقل الموظف إلى هذه الجهة.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'national_id' => ['required', 'string', 'max:50', Rule::unique('employees')->ignore($employee->id)],
            'job_title' => ['nullable', 'string', 'max:150'],
            'org_unit_id' => ['required', 'exists:organizational_units,id'],
            'is_active' => ['sometimes', 'boolean'],
            'clear_review' => ['sometimes', 'boolean'],
        ]);

        $oldValues = $employee->only(['name', 'national_id', 'org_unit_id', 'job_title', 'is_active', 'needs_review']);

        $validated['is_active'] = $request->has('is_active');
        if ($request->has('clear_review')) {
            $validated['needs_review'] = false;
        }

        $employee->update($validated);

        StructureAuditLog::create([
            'user_id' => $user->id,
            'target_type' => 'employee',
            'target_id' => $employee->id,
            'action' => 'employee_updated',
            'old_values' => $oldValues,
            'new_values' => $employee->only(['name', 'national_id', 'org_unit_id', 'job_title', 'is_active', 'needs_review']),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return redirect()->route('roster-employees.index')->with('status', 'تم تحديث بيانات الموظف بنجاح.');
    }

    public function toggleActive(Employee $employee)
    {
        $this->authorize('toggleActive', $employee);

        $employee->update(['is_active' => !$employee->is_active]);

        $msg = $employee->is_active ? 'تم تفعيل الموظف بنجاح.' : 'تم تعطيل الموظف بنجاح.';
        return back()->with('status', $msg);
    }
}
