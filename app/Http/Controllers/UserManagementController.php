<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use App\Models\StructureAuditLog;
use App\Models\User;
use App\Models\UserUnitAssignment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بإدارة المستخدمين.');
        }

        $query = User::with(['unitAssignments.organizationalUnit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->paginate(20);

        return view('user_management.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $units = OrganizationalUnit::where('is_active', true)->orderBy('name')->get();
        $roles = UserUnitAssignment::roleLabels();

        return view('user_management.create', compact('units', 'roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
            'role' => ['required', 'string', Rule::in(array_keys(UserUnitAssignment::roleLabels()))],
            'org_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'include_sub_branches' => ['sometimes', 'boolean'],
            'can_edit_approved' => ['sometimes', 'boolean'],
            'can_manage_employees' => ['sometimes', 'boolean'],
        ]);

        // Validate unit assignment necessity: super_admin is global, other roles require org_unit_id
        if ($validated['role'] !== UserUnitAssignment::ROLE_SUPER_ADMIN && empty($validated['org_unit_id'])) {
            return back()->withInput()->withErrors(['org_unit_id' => 'يجب تحديد الجهة التنظيمية لهذا الدور.']);
        }

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] === UserUnitAssignment::ROLE_SUPER_ADMIN ? 'admin' : 'employee',
                'is_active' => $request->has('is_active'),
                'force_password_change' => false,
            ]);

            $assignment = UserUnitAssignment::create([
                'user_id' => $user->id,
                'org_unit_id' => $validated['role'] === UserUnitAssignment::ROLE_SUPER_ADMIN ? null : $validated['org_unit_id'],
                'role' => $validated['role'],
                'include_sub_branches' => $request->has('include_sub_branches'),
                'can_edit_approved' => $request->has('can_edit_approved'),
                'can_manage_employees' => $request->has('can_manage_employees'),
            ]);

            StructureAuditLog::create([
                'user_id' => auth()->id(),
                'target_type' => 'user_assignment',
                'target_id' => $user->id,
                'action' => 'user_created_with_assignment',
                'old_values' => null,
                'new_values' => [
                    'email' => $user->email,
                    'role' => $assignment->role,
                    'org_unit_id' => $assignment->org_unit_id,
                    'include_sub_branches' => $assignment->include_sub_branches,
                ],
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('user-management.index')->with('status', 'تم إنشاء حساب المستخدم وتعيين دوره ونطاقه بنجاح.');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user->load('unitAssignments.organizationalUnit');
        $units = OrganizationalUnit::where('is_active', true)->orderBy('name')->get();
        $roles = UserUnitAssignment::roleLabels();

        return view('user_management.edit', compact('user', 'units', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
            'role' => ['required', 'string', Rule::in(array_keys(UserUnitAssignment::roleLabels()))],
            'org_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'include_sub_branches' => ['sometimes', 'boolean'],
            'can_edit_approved' => ['sometimes', 'boolean'],
            'can_manage_employees' => ['sometimes', 'boolean'],
        ]);

        // Safety: Do not allow deactivating or stripping the last active super_admin
        if ($user->isSuperAdmin()) {
            $isChangingRole = $validated['role'] !== UserUnitAssignment::ROLE_SUPER_ADMIN;
            $isDeactivating = !$request->has('is_active');

            if ($isChangingRole || $isDeactivating) {
                $otherActiveSuperAdmins = User::where('id', '!=', $user->id)
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->where('role', 'admin')
                          ->orWhereHas('unitAssignments', fn($sub) => $sub->where('role', UserUnitAssignment::ROLE_SUPER_ADMIN));
                    })
                    ->count();

                if ($otherActiveSuperAdmins === 0) {
                    return back()->withInput()->withErrors([
                        'role' => 'لا يمكن تعطيل أو تجريد آخر مسؤول أول فعّال في النظام من صلاحياته.'
                    ]);
                }
            }
        }

        if ($validated['role'] !== UserUnitAssignment::ROLE_SUPER_ADMIN && empty($validated['org_unit_id'])) {
            return back()->withInput()->withErrors(['org_unit_id' => 'يجب تحديد الجهة التنظيمية لهذا الدور.']);
        }

        DB::transaction(function () use ($validated, $request, $user) {
            $oldAssignment = $user->unitAssignments()->first();
            $oldValues = [
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'role' => $oldAssignment?->role,
                'org_unit_id' => $oldAssignment?->org_unit_id,
            ];

            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $request->has('is_active'),
                'role' => $validated['role'] === UserUnitAssignment::ROLE_SUPER_ADMIN ? 'admin' : 'employee',
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            $targetUnitId = $validated['role'] === UserUnitAssignment::ROLE_SUPER_ADMIN ? null : $validated['org_unit_id'];

            if ($oldAssignment) {
                $oldAssignment->update([
                    'org_unit_id' => $targetUnitId,
                    'role' => $validated['role'],
                    'include_sub_branches' => $request->has('include_sub_branches'),
                    'can_edit_approved' => $request->has('can_edit_approved'),
                    'can_manage_employees' => $request->has('can_manage_employees'),
                ]);
            } else {
                UserUnitAssignment::create([
                    'user_id' => $user->id,
                    'org_unit_id' => $targetUnitId,
                    'role' => $validated['role'],
                    'include_sub_branches' => $request->has('include_sub_branches'),
                    'can_edit_approved' => $request->has('can_edit_approved'),
                    'can_manage_employees' => $request->has('can_manage_employees'),
                ]);
            }

            StructureAuditLog::create([
                'user_id' => auth()->id(),
                'target_type' => 'user_assignment',
                'target_id' => $user->id,
                'action' => 'user_assignment_updated',
                'old_values' => $oldValues,
                'new_values' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'role' => $validated['role'],
                    'org_unit_id' => $targetUnitId,
                ],
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('user-management.index')->with('status', 'تم تحديث حساب المستخدم وصلاحياته بنجاح.');
    }

    public function toggleActive(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Prevent deactivating the last active super_admin
        if ($user->is_active && $user->isSuperAdmin()) {
            $otherActiveSuperAdmins = User::where('id', '!=', $user->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('role', 'admin')
                      ->orWhereHas('unitAssignments', fn($sub) => $sub->where('role', UserUnitAssignment::ROLE_SUPER_ADMIN));
                })
                ->count();

            if ($otherActiveSuperAdmins === 0) {
                return back()->with('error', 'لا يمكن تعطيل آخر مسؤول أول فعّال في النظام.');
            }
        }

        $oldActive = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        StructureAuditLog::create([
            'user_id' => auth()->id(),
            'target_type' => 'user_assignment',
            'target_id' => $user->id,
            'action' => $user->is_active ? 'user_activated' : 'user_deactivated',
            'old_values' => ['is_active' => $oldActive],
            'new_values' => ['is_active' => $user->is_active],
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $msg = $user->is_active ? 'تم تفعيل الحساب بنجاح.' : 'تم تعطيل الحساب بنجاح.';
        return back()->with('status', $msg);
    }
}
