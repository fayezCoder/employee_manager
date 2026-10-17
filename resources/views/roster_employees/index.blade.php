<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    إدارة الموظفين (المشمولين بالتحضير)
                </h2>
                <p class="text-xs text-gray-500 mt-1">سجل الموظفين التابعين للجهات التنظيمية لإدراجهم في كشوفات الحضور اليومية</p>
            </div>
            <a href="{{ route('roster-employees.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة موظف جديد
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-medium text-emerald-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm font-medium text-rose-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($needsReviewCount > 0)
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between text-xs text-amber-900">
                    <div class="flex items-center gap-2 font-semibold">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        تنبيه: يوجد {{ $needsReviewCount }} موظف مرحل من النظام السابق بحاجة لمراجعة المسؤول الأول لتحديث الهوية الوطنية والجهة التنظيمية.
                    </div>
                    <a href="{{ route('roster-employees.index', ['needs_review' => '1']) }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold transition">
                        عرض الموظفين للمراجعة
                    </a>
                </div>
            @endif

            <!-- Search & Filters -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-4 sm:p-5">
                <form method="GET" action="{{ route('roster-employees.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">بحث بالاسم أو الهوية أو المسمى</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..."
                               class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">الجهة التنظيمية</label>
                        <select name="org_unit_id" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الجهات المصرح بها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('org_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">الحالة</label>
                        <select name="status" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">الكل</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط (مشمول بالتحضير)</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">حالة التدقيق</label>
                        <select name="needs_review" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">كافة الموظفين</option>
                            <option value="1" {{ request('needs_review') === '1' ? 'selected' : '' }}>بحاجة لمراجعة المسؤول</option>
                            <option value="0" {{ request('needs_review') === '0' ? 'selected' : '' }}>معتمد ومكتمل التدقيق</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold transition">
                            تصفية
                        </button>
                        @if(request()->hasAny(['search', 'org_unit_id', 'status', 'needs_review']))
                            <a href="{{ route('roster-employees.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                                إلغاء
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Employees Table -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">اسم الموظف</th>
                                <th class="px-5 py-3.5">رقم الهوية الوطنية (مقنّع)</th>
                                <th class="px-5 py-3.5">المسمى الوظيفي</th>
                                <th class="px-5 py-3.5">الجهة التابع لها</th>
                                <th class="px-5 py-3.5 text-center">حالة التدقيق</th>
                                <th class="px-5 py-3.5 text-center">الحالة</th>
                                <th class="px-5 py-3.5 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employees as $employee)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4 font-bold text-gray-900">
                                        {{ $employee->name }}
                                        @if($employee->user)
                                            <div class="text-[10px] text-gray-400 font-normal mt-0.5">مرتبط بحساب: {{ $employee->user->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 font-mono text-gray-600 tracking-wider">
                                        {{ $employee->masked_national_id }}
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $employee->job_title ?: '—' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($employee->organizationalUnit)
                                            <div class="font-medium text-gray-800">{{ $employee->organizationalUnit->name }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $employee->organizationalUnit->type_label }}</div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                غير مسند لجهة
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($employee->needs_review)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="{{ $employee->review_notes }}">
                                                بحاجة لمراجعة
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                مكتمل التدقيق
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($employee->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                نشط
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                معطل
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @can('update', $employee)
                                                <a href="{{ route('roster-employees.edit', $employee) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition" title="تعديل بيانات الموظف">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                            @endcan
                                            @can('toggleActive', $employee)
                                                <form method="POST" action="{{ route('roster-employees.toggle-active', $employee) }}" class="inline" onsubmit="return confirm('هل تريد تأكيد تغيير حالة الموظف؟')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 {{ $employee->is_active ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $employee->is_active ? 'تعطيل الموظف' : 'تفعيل الموظف' }}">
                                                        @if($employee->is_active)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        @endif
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                        لا يوجد موظفون مسجلون وفق معايير البحث.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($employees->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
