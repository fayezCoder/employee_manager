<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    سجل التدقيق الشامل والأحداث (Audit Trail)
                </h2>
                <p class="text-xs text-gray-500 mt-1">سجل رقمي مؤمّن وموثق يشتمل على تصحيحات التحضير، ومراحل الاعتمادات، وتغييرات الهيكل والصلاحيات</p>
            </div>
            <a href="{{ route('roster-reports.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                العودة للتقارير
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: '{{ request('tab', 'corrections') }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Tab Buttons -->
            <div class="flex items-center gap-2 border-b border-gray-200 pb-3">
                <button type="button" @click="activeTab = 'corrections'"
                        :class="activeTab === 'corrections' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    سجل تصحيحات الكشوفات ({{ $correctionLogs->total() }})
                </button>

                <button type="button" @click="activeTab = 'workflow'"
                        :class="activeTab === 'workflow' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    سجل أحداث الاعتمادات ومراحل التحضير ({{ $workflowEvents->total() }})
                </button>

                <button type="button" @click="activeTab = 'structure'"
                        :class="activeTab === 'structure' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    سجل تدقيق الهيكل والصلاحيات ({{ $structureLogs->total() }})
                </button>
            </div>

            <!-- Tab 1: Corrections Audit Log -->
            <div x-show="activeTab === 'corrections'" class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm">
                        تصحيحات الكشوفات بعد الاعتماد (مع توثيق الأسباب الإلزامية)
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">الوقت والتاريخ</th>
                                <th class="px-5 py-3.5">المسؤول المنفذ</th>
                                <th class="px-5 py-3.5">الجهة والكشف</th>
                                <th class="px-5 py-3.5">الموظف المعني</th>
                                <th class="px-5 py-3.5">التعديل من -> إلى</th>
                                <th class="px-5 py-3.5">سبب التبرير الإلزامي</th>
                                <th class="px-5 py-3.5 text-center">الإصدار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($correctionLogs as $log)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 font-mono text-gray-500 whitespace-nowrap">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-5 py-4 font-bold text-gray-900">
                                        {{ $log->user->name ?? 'مستخدم' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-800">{{ $log->dailyRoster->unit_name_snapshot ?? ($log->dailyRoster->organizationalUnit->name ?? '—') }}</div>
                                        <div class="text-[10px] text-gray-400">تاريخ: {{ $log->dailyRoster?->date?->toDateString() }}</div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        {{ $log->dailyRosterItem->employee_name_snapshot ?? ($log->dailyRosterItem->employee->name ?? '—') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="line-through text-gray-400">{{ $log->old_value ?: 'غير محدد' }}</span>
                                        <span class="text-gray-400 mx-1">-></span>
                                        <span class="font-bold text-emerald-600">{{ $log->new_value }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-xs text-rose-800 bg-rose-50 p-2 rounded-lg border border-rose-100 max-w-xs break-words">
                                            {{ $log->reason }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center font-mono text-gray-500">
                                        v{{ $log->roster_version }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                        لا توجد تصحيحات مسجلة على الكشوفات المعتمدة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($correctionLogs->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $correctionLogs->links() }}
                    </div>
                @endif
            </div>

            <!-- Tab 2: Workflow Events Log -->
            <div x-show="activeTab === 'workflow'" class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm">
                        أحداث دورة التحضير والاعتمادات الرسمية
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">الوقت والتاريخ</th>
                                <th class="px-5 py-3.5">المسؤول المنفذ</th>
                                <th class="px-5 py-3.5">الجهة والكشف</th>
                                <th class="px-5 py-3.5">المرحلة والإجراء</th>
                                <th class="px-5 py-3.5">انتقال الحالة</th>
                                <th class="px-5 py-3.5 text-center">الإصدار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($workflowEvents as $event)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 font-mono text-gray-500 whitespace-nowrap">
                                        {{ $event->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-5 py-4 font-bold text-gray-900">
                                        {{ $event->user->name ?? 'مستخدم' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-800">{{ $event->dailyRoster->unit_name_snapshot ?? ($event->dailyRoster->organizationalUnit->name ?? '—') }}</div>
                                        <div class="text-[10px] text-gray-400">تاريخ: {{ $event->dailyRoster?->date?->toDateString() }}</div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        {{ $event->stage_label }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-mono text-gray-500">{{ $event->from_status }}</span>
                                        <span class="text-gray-400 mx-1">-></span>
                                        <span class="font-mono font-bold text-indigo-600">{{ $event->to_status }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center font-mono text-gray-500">
                                        v{{ $event->roster_version }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                        لا توجد أحداث اعتماد مسجلة حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($workflowEvents->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $workflowEvents->links() }}
                    </div>
                @endif
            </div>

            <!-- Tab 3: Structure & Permissions Audit Log -->
            <div x-show="activeTab === 'structure'" class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm">
                        تدقيق التغييرات على الهيكل التنظيمي وصلاحيات المستخدمين
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">الوقت والتاريخ</th>
                                <th class="px-5 py-3.5">المسؤول المنفذ</th>
                                <th class="px-5 py-3.5">نوع العنصر</th>
                                <th class="px-5 py-3.5">نوع العملية</th>
                                <th class="px-5 py-3.5">التفاصيل والتغييرات</th>
                                <th class="px-5 py-3.5 text-center">عنوان IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($structureLogs as $sLog)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 font-mono text-gray-500 whitespace-nowrap">
                                        {{ $sLog->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-5 py-4 font-bold text-gray-900">
                                        {{ $sLog->user->name ?? 'مستخدم' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                            {{ $sLog->target_type === 'unit' ? 'جهة تنظيمية' : 'مستخدم وصلاحية' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        {{ $sLog->action }}
                                    </td>
                                    <td class="px-5 py-4 font-mono text-[11px] text-gray-600 max-w-xs break-words">
                                        @if($sLog->new_values)
                                            {{ json_encode($sLog->new_values, JSON_UNESCAPED_UNICODE) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center font-mono text-gray-400">
                                        {{ $sLog->ip_address ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                        لا توجد حركات مسجلة لتعديلات الهيكل أو الصلاحيات.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($structureLogs->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $structureLogs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
