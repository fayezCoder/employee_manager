<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DepartmentController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Department::class);
        $departments = Department::withCount('users')->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create', Department::class);
        return view('departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        $this->authorize('create', Department::class);
        Department::create($request->validated());
        return redirect()->route('departments.index')->with('success', __('تم إنشاء القسم بنجاح.'));
    }

    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        return view('departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $this->authorize('update', $department);
        $department->update($request->validated());
        return redirect()->route('departments.index')->with('success', __('تم تحديث القسم بنجاح.'));
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        
        if ($department->users()->exists()) {
            return redirect()->route('departments.index')->with('error', __('لا يمكن حذف القسم لوجود موظفين مرتبطين به.'));
        }

        $department->delete();
        return redirect()->route('departments.index')->with('success', __('تم حذف القسم بنجاح.'));
    }
}
