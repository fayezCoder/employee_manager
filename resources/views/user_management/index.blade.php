<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    إدارة المستخدمين والصلاحيات
                </h2>
                <p class="text-xs text-gray-500 mt-1">تحديد الأدوار، ونطاق الإشراف على الجهات التنظيمية، وصلاحيات التعديل والاعتماد</p>
            </div>
            <a href="{{ route('user-management.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة مستخدم جديد
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

            <!-- Search and Filter Bar -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-4 sm:p-5">
                <form method="GET" action="{{ route('user-management.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">بحث بالاسم أو البريد</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المستخدم أو بريده..."
                               class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">حالة الحساب</label>
                        <select name="status" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold transition">
                            تطبيق التصفية
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('user-management.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                                إلغاء
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">المستخدم</th>
                                <th class="px-5 py-3.5">الدور وصلاحيات النظام</th>
                                <th class="px-5 py-3.5">نطاق الإشراف (الجهة المخصصة)</th>
                                <th class="px-5 py-3.5">الصلاحيات المتقدمة</th>
                                <th class="px-5 py-3.5 text-center">الحالة</th>
                                <th class="px-5 py-3.5 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                @php
                                    $assignment = $user->unitAssignments->first();
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($assignment)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ $assignment->role_label }}
                                            </span>
                                        @elseif($user->role === 'admin')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                مسؤول أول (نظام قديم)
                                            </span>
                                        @else
                                            <span class="text-gray-400">بدون تعيين نطاق</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($assignment && $assignment->organizationalUnit)
                                            <div class="font-medium text-gray-800">{{ $assignment->organizationalUnit->name }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $assignment->organizationalUnit->type_label }} ({{ $assignment->organizationalUnit->code }})</div>
                                            @if($assignment->include_sub_branches)
                                                <span class="inline-block mt-1 text-[10px] text-emerald-700 font-medium">✓ يشمل كافة الفروع والمراكز التابعة</span>
                                            @endif
                                        @elseif($assignment && $assignment->role === \App\Models\UserUnitAssignment::ROLE_SUPER_ADMIN)
                                            <span class="text-indigo-600 font-medium">شامل لكافة وحدات الهيكل التنظيمي</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 space-y-1">
                                        @if($assignment)
                                            <div>
                                                @if($assignment->can_edit_approved)
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-700 font-medium bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                        تصحيح المعتمد متاح
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-[10px]">لا يمكنه تعديل المعتمد</span>
                                                @endif
                                            </div>
                                            <div>
                                                @if($assignment->can_manage_employees)
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-blue-700 font-medium bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                                        إدارة الموظفين مفعّلة
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($user->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                نشط
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                معطل
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('user-management.edit', $user) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition" title="تعديل المستخدم">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('user-management.toggle-active', $user) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من تغيير حالة هذا المستخدم؟')">
                                                @csrf
                                                <button type="submit" class="p-1.5 {{ $user->is_active ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $user->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                                    @if($user->is_active)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                        لا يوجد مستخدمون مطابقون لمعايير البحث.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
