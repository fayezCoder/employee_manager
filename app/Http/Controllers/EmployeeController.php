<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('department');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $employees = $query->paginate(15);
        $departments = Department::all();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $departments = Department::all();
        return view('employees.create', compact('departments'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->authorize('create', User::class);
        
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['force_password_change'] = true;

        User::create($data);

        return redirect()->route('employees.index')->with('success', __('تم إضافة الموظف بنجاح.'));
    }

    public function edit(User $employee)
    {
        $this->authorize('update', $employee);
        $departments = Department::all();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $this->authorize('update', $employee);

        $data = $request->validated();

        if ($employee->isAdmin() && $data['is_active'] == false) {
            $activeAdminsCount = User::admins()->active()->where('id', '!=', $employee->id)->count();
            if ($activeAdminsCount === 0) {
                return back()->with('error', __('لا يمكن تعطيل حساب المدير الأخير.'));
            }
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', __('تم تحديث بيانات الموظف بنجاح.'));
    }
}
