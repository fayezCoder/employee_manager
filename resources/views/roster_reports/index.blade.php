<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    تقارير التحضير والحضور
                </h2>
                <p class="text-xs text-gray-500 mt-1">استعلام شامل عن حالات الحضور والغياب والإجازات وتصدير البيانات</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('roster-reports.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصدير إكسل (CSV)
                </a>
                @can('super-admin')
                    <a href="{{ route('roster-reports.audit-logs') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        سجل التدقيق الشامل
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Panel -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-5">
                <form method="GET" action="{{ route('roster-reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">الجهة التنظيمية</label>
                        <select name="org_unit_id" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الجهات المصرح بها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('org_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">حالة الحضور</label>
                        <select name="status" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">كافة الحالات</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold transition">
                            تطبيق التصفية
                        </button>
                        @if(request()->hasAny(['date_from', 'date_to', 'org_unit_id', 'status']))
                            <a href="{{ route('roster-reports.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                                إلغاء
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Report Table -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">التاريخ</th>
                                <th class="px-5 py-3.5">الجهة التنظيمية</th>
                                <th class="px-5 py-3.5">اسم الموظف</th>
                                <th class="px-5 py-3.5">رقم الهوية الوطنية</th>
                                <th class="px-5 py-3.5">الحالة المسجلة</th>
                                <th class="px-5 py-3.5">الملاحظات</th>
                                <th class="px-5 py-3.5 text-center">حالة الكشف</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $item)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 font-mono font-medium text-gray-900 whitespace-nowrap">
                                        {{ $item->dailyRoster->date->toDateString() }}
                                    </td>
                                    <td class="px-5 py-4 font-medium text-gray-800">
                                        {{ $item->dailyRoster->unit_name_snapshot ?? ($item->dailyRoster->organizationalUnit->name ?? '—') }}
                                    </td>
                                    <td class="px-5 py-4 font-bold text-gray-900">
                                        {{ $item->employee->name ?? $item->employee_name_snapshot }}
                                    </td>
                                    <td class="px-5 py-4 font-mono text-gray-600">
                                        {{ $item->employee->masked_national_id ?? $item->employee_national_id_snapshot }}
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-900">
                                        @if($item->status)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                                {{ $item->status }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">غير محدد</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-gray-500">
                                        {{ $item->notes ?: '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                            {{ $item->dailyRoster->isCompleted() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $item->dailyRoster->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                        لا توجد سجلات تحضير تطابق معايير البحث المحددة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
