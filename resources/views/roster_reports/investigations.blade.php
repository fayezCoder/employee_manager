<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    متابعة تحقيقات الغياب (مهلة 7 أيام) ⚖️
                </h2>
                <p class="text-xs text-gray-500 mt-1">متابعة قيد التحقيق مع الموظفين الغائبين ورصد الحالات التي انتهت مهلتها دون تحقيق نظامي</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('roster-reports.index', ['status' => 'غايب']) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    كافة تقارير التحضير
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Session Status -->
            @if(session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold p-4 rounded-xl flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('investigations.index') }}" class="p-4 rounded-2xl bg-white border border-gray-200 shadow-sm flex items-center justify-between hover:border-gray-400 transition">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500">إجمالي حالات الغياب</span>
                        <span class="text-2xl font-extrabold text-gray-900 mt-1 block">{{ $totalAbsentCount }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </a>

                <a href="{{ route('investigations.index', ['filter_status' => 'pending']) }}" class="p-4 rounded-2xl bg-white border border-amber-200 shadow-sm flex items-center justify-between hover:border-amber-400 transition">
                    <div>
                        <span class="block text-xs font-semibold text-amber-700">قيد التحقيق (ضمن مهلة 7 أيام)</span>
                        <span class="text-2xl font-extrabold text-amber-700 mt-1 block">{{ $pendingCount }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </a>

                <a href="{{ route('investigations.index', ['filter_status' => 'overdue']) }}" class="p-4 rounded-2xl bg-white border border-rose-200 shadow-sm flex items-center justify-between hover:border-rose-400 transition">
                    <div>
                        <span class="block text-xs font-semibold text-rose-700">لم يتم التحقيق (تجاوزت 7 أيام)</span>
                        <span class="text-2xl font-extrabold text-rose-700 mt-1 block">{{ $overdueCount }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </a>

                <a href="{{ route('investigations.index', ['filter_status' => 'investigated']) }}" class="p-4 rounded-2xl bg-white border border-emerald-200 shadow-sm flex items-center justify-between hover:border-emerald-400 transition">
                    <div>
                        <span class="block text-xs font-semibold text-emerald-700">تم التحقيق وقيد المحضر</span>
                        <span class="text-2xl font-extrabold text-emerald-700 mt-1 block">{{ $investigatedCount }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </a>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-4">
                <form method="GET" action="{{ route('investigations.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">الجهة التنظيمية</label>
                        <select name="org_unit_id" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">كافة الجهات المصرح بها</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ request('org_unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">حالة التحقيق</label>
                        <select name="filter_status" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('filter_status') === 'pending' ? 'selected' : '' }}>قيد المتابعة (ضمن 7 أيام)</option>
                            <option value="overdue" {{ request('filter_status') === 'overdue' ? 'selected' : '' }}>لم يتم التحقيق (تجاوزت 7 أيام)</option>
                            <option value="investigated" {{ request('filter_status') === 'investigated' ? 'selected' : '' }}>تم التحقيق وتوثيق الرقم</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold transition">
                            تصفية
                        </button>
                        @if(request()->hasAny(['org_unit_id', 'filter_status']))
                            <a href="{{ route('investigations.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                                إلغاء
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Investigations Table -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">تاريخ الغياب</th>
                                <th class="px-5 py-3.5">الجهة</th>
                                <th class="px-5 py-3.5">الموظف الغائب</th>
                                <th class="px-5 py-3.5">الهوية الوطنية</th>
                                <th class="px-5 py-3.5">المهلة وحالة التحقيق</th>
                                <th class="px-5 py-3.5">رقم المحضر / التحقيق</th>
                                <th class="px-5 py-3.5 text-center">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $item)
                                @php
                                    $state = $item->getInvestigationState();
                                    $absenceDate = $item->date ?? $item->created_at;
                                    $daysPassed = (int) $absenceDate->diffInDays(now('Asia/Riyadh'), false);
                                    $daysLeft = max(0, 7 - $daysPassed);
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition {{ $state === 'overdue' ? 'bg-rose-50/20' : '' }}">
                                    <td class="px-5 py-4 font-mono font-medium text-gray-900 whitespace-nowrap">
                                        {{ $item->date->toDateString() }}
                                        <span class="block text-[10px] text-gray-400">منذ {{ $daysPassed }} يوم</span>
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
                                    <td class="px-5 py-4">
                                        @if($state === 'investigated')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>تم التحقيق</span>
                                            </span>
                                        @elseif($state === 'overdue')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>لم يتم التحقيق</span>
                                            </span>
                                            <span class="block text-[10px] text-rose-600 mt-0.5">انتهت مهلة 7 أيام دون إجراء</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <svg class="w-3.5 h-3.5 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>قيد المتابعة (متبقي {{ $daysLeft }} يوم)</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($item->investigation_number)
                                            <span class="font-mono font-bold text-gray-900 block text-xs">{{ $item->investigation_number }}</span>
                                            @if($item->investigation_notes)
                                                <span class="text-[11px] text-gray-500 block truncate max-w-xs">{{ $item->investigation_notes }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 font-mono text-[11px]">بانتظار القيد</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button type="button"
                                                onclick="openInvestigationModal('{{ $item->id }}', '{{ addslashes($item->employee->name ?? $item->employee_name_snapshot) }}', '{{ $item->investigation_number }}', '{{ addslashes($item->investigation_notes ?? '') }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-semibold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>{{ $item->investigation_number ? 'تعديل المحضر' : 'تسجيل التحقيق' }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                        لا توجد حالات غياب تتطلب المتابعة.
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

    <!-- Modal for recording investigation -->
    <div id="investigationModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-md w-full p-6 space-y-4 text-right">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900">تسجيل محضر التحقيق الإداري</h3>
                <button type="button" onclick="closeInvestigationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="investigationForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">الموظف المعني</label>
                    <input type="text" id="modalEmployeeName" readonly class="w-full text-xs rounded-xl bg-gray-50 border-gray-200 text-gray-700">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">رقم التحقيق / القيد الإداري <span class="text-red-500">*</span></label>
                    <input type="text" name="investigation_number" id="modalInvNumber" required placeholder="مثال: قيد/1447/980"
                           class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-[10px] text-gray-400 mt-1">يُشترط تسجيل رقم القيد أو المعاملة لرفع صفة (لم يتم التحقيق).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">حالة الإجراء</label>
                    <select name="investigation_status" class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="completed">تم التحقيق وإنهاء الإجراء</option>
                        <option value="pending">المساءلة جارية</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">نتيجة التحقيق / الملاحظات</label>
                    <textarea name="investigation_notes" id="modalInvNotes" rows="3" placeholder="مثال: تم قبول العذر الطبي / تم الرفع للجزاء..."
                              class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="closeInvestigationModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                        حفظ المحضر
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openInvestigationModal(itemId, employeeName, invNumber, invNotes) {
            const form = document.getElementById('investigationForm');
            form.action = '/investigations/' + itemId;
            document.getElementById('modalEmployeeName').value = employeeName;
            document.getElementById('modalInvNumber').value = invNumber || '';
            document.getElementById('modalInvNotes').value = invNotes || '';
            document.getElementById('investigationModal').classList.remove('hidden');
        }

        function closeInvestigationModal() {
            document.getElementById('investigationModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
