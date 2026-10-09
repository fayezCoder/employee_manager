<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">تعديل بيانات الموظف</h1>
                <a href="{{ route('employees.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">عودة للموظفين →</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="اسم الموظف الثلاثي" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $employee->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="البريد الإلكتروني" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-right" :value="old('email', $employee->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="department_id" value="القسم" />
                        <select id="department_id" name="department_id" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">اختر القسم...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" value="صلاحيات النظام" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="employee" {{ old('role', $employee->role) === 'employee' ? 'selected' : '' }}>موظف عادي</option>
                            <option value="admin" {{ old('role', $employee->role) === 'admin' ? 'selected' : '' }}>مدير نظام</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <label for="is_active" class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                            <div>
                                <span class="font-bold text-gray-900 text-sm block">حساب فعّال في النظام</span>
                                <span class="text-xs text-gray-500 block">عند إلغاء التفعيل (تعطيل الحساب)، يمنع الموظف فوراً من دخول النظام وتُحفظ جميع سجلاته التاريخية.</span>
                            </div>
                        </label>
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">إلغاء</a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">حفظ التحديثات</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
