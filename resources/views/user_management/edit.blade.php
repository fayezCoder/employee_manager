<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    تعديل بيانات المستخدم: {{ $user->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">تحديث بيانات الدخول والدور ونطاق الجهة التنظيمية</p>
            </div>
            <a href="{{ route('user-management.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                العودة لقائمة المستخدمين
            </a>
        </div>
    </x-slot>

    @php
        $assignment = $user->unitAssignments->first();
        $currentRole = old('role', $assignment ? $assignment->role : ($user->role === 'admin' ? 'super_admin' : 'unit_admin'));
        $currentUnitId = old('org_unit_id', $assignment ? $assignment->org_unit_id : null);
        $includeSubs = old('include_sub_branches', $assignment ? $assignment->include_sub_branches : false);
        $canEditApproved = old('can_edit_approved', $assignment ? $assignment->can_edit_approved : false);
        $canManageEmployees = old('can_manage_employees', $assignment ? $assignment->can_manage_employees : true);
        $isActive = old('is_active', $user->is_active);
    @endphp

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-6 sm:p-8" x-data="{ selectedRole: '{{ $currentRole }}' }">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                        <div class="flex items-center gap-2 text-rose-800 font-semibold text-sm mb-2">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            يرجى تصحيح الأخطاء التالية:
                        </div>
                        <ul class="list-disc list-inside text-xs text-rose-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('user-management.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="border-b border-gray-100 pb-5">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">بيانات الحساب الشخصي</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">اسم المسؤول / المستخدم <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">كلمة المرور الجديدة (اترك الحقل فارغاً إذا كنت لا ترغب بتغييرها)</label>
                                <input type="password" name="password" id="password"
                                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm"
                                       placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-100 pb-5">
                        <h3 class="text-sm font-bold text-gray-800 mb-4">الدور ونطاق التكليف التنظيمي</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="role" class="block text-xs font-semibold text-gray-700 mb-1">الدور الإداري <span class="text-rose-500">*</span></label>
                                <select name="role" id="role" x-model="selectedRole" required
                                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                                    @foreach($roles as $key => $label)
                                        <option value="{{ $key }}" {{ $currentRole === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="selectedRole !== 'super_admin'" x-transition>
                                <label for="org_unit_id" class="block text-xs font-semibold text-gray-700 mb-1">الجهة التنظيمية المسندة <span class="text-rose-500">*</span></label>
                                <select name="org_unit_id" id="org_unit_id"
                                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                                    <option value="">-- اختر الجهة التابع لها --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ (string)$currentUnitId === (string)$unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->type_label }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-500 mt-1">يحدد هذا الخيار الجهة التي يحق لهذا المستخدم الوصول إلى بياناتها وتحضير موظفيها.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-3">خيارات وتصاريح إضافية</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="include_sub_branches" value="1" {{ $includeSubs ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5">
                                <div>
                                    <span class="text-xs font-semibold text-gray-700">شمل الفروع والمراكز التابعة في العرض والمتابعة</span>
                                    <p class="text-[11px] text-gray-500">يتيح لمسؤولي القيادات الإشراف على الجهات التابعة لجهتهم.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="can_edit_approved" value="1" {{ $canEditApproved ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5">
                                <div>
                                    <span class="text-xs font-semibold text-gray-700">تصريح تصحيح وتعديل الكشوفات بعد الاعتماد النهائي</span>
                                    <p class="text-[11px] text-gray-500">يتطلب كتابة سبب إلزامي ويتم توثيقه في سجل التدقيق غير القابل للتعديل.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="can_manage_employees" value="1" {{ $canManageEmployees ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5">
                                <div>
                                    <span class="text-xs font-semibold text-gray-700">صلاحية إدارة موظفي الجهة (إضافة وتعديل بيانات الموظفين)</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 cursor-pointer pt-2">
                                <input type="checkbox" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5">
                                <div>
                                    <span class="text-xs font-semibold text-gray-700">الحساب مفعل ونشط</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('user-management.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            إلغاء
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
