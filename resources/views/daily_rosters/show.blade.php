<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $orgUnit->type_label }}
                    </span>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">
                        كشف تحضير: {{ $orgUnit->name }}
                    </h2>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-500 mt-1 font-medium">
                    <span>التاريخ: {{ $date }}</span>
                    <span>•</span>
                    <span>الرمز: {{ $orgUnit->code }}</span>
                    <span>•</span>
                    <span>الإصدار: v{{ $roster->version }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('rosters.index', ['date' => $date]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    العودة للكشوفات
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $isLocked = $roster->isLocked();
        $canUpdate = auth()->user()->can('update', $roster);
        $canSubmit = auth()->user()->can('submit', $roster);
        $canBranchApprove = auth()->user()->can('branchApprove', $roster);
        $canCorrect = auth()->user()->can('correct', $roster);
    @endphp

    <div class="py-8" x-data="{ showCorrectionModal: false }">
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

            <!-- Roster Status & Action Banner -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl {{ $isLocked ? 'bg-emerald-50 text-emerald-600' : ($roster->status === \App\Models\DailyRoster::STATUS_CENTER_LOCKED ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600') }}">
                        @if($isLocked)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @elseif($roster->status === \App\Models\DailyRoster::STATUS_CENTER_LOCKED)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500">حالة الكشف:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $roster->status_label }}</span>
                            @if($roster->was_corrected)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    معدل بعد الاعتماد
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if($roster->isCompleted())
                                الكشف مكتمل ومعتمد نهائياً ومقفل ضد التعديل المباشر.
                            @elseif($roster->status === \App\Models\DailyRoster::STATUS_CENTER_LOCKED)
                                تم اعتماد الكشف من المركز وقفل التحرير، وبانتظار اعتماد القيادة الفرعية التابع لها.
                            @else
                                الكشف في طور المسودة، يمكنك تعديل الحالات ثم الحفظ أو الاعتماد.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
                    @if($canBranchApprove)
                        <form method="POST" action="{{ route('rosters.branch-approve', $roster) }}" onsubmit="return confirm('هل تريد تأكيد اعتماد كشف هذا المركز وقفل التحرير؟')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                اعتماد كشف المركز (كقيادة فرعية)
                            </button>
                        </form>
                    @endif

                    @if($isLocked && $canCorrect)
                        <button type="button" @click="showCorrectionModal = true"
                                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            تصحيح الكشف المعتمد (مع تبرير إلزامي)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Attendance Grid Form -->
            <form id="rosterForm" method="POST" action="{{ route('rosters.update', $roster) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="version" value="{{ $roster->version }}">

                <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                        <div class="font-bold text-gray-800 text-xs sm:text-sm">
                            قائمة موظفي الجهة ({{ $roster->items->count() }} موظف)
                        </div>
                        <div class="text-[11px] text-gray-500">
                            الحالات غير المحددة مسبقاً تظهر فارغة لتعبئتها اليومية بدقة
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs text-gray-700">
                            <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3.5 w-12 text-center">#</th>
                                    <th class="px-5 py-3.5">اسم الموظف</th>
                                    <th class="px-5 py-3.5">رقم الهوية الوطنية</th>
                                    <th class="px-5 py-3.5 min-w-[220px]">حالة الحضور والغياب (14 حالة رسمية)</th>
                                    <th class="px-5 py-3.5 min-w-[240px]">ملاحظات وتفاصيل التكليف / العذر</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($roster->items as $index => $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-4 py-3.5 text-center text-gray-400 font-mono">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-5 py-3.5 font-bold text-gray-900">
                                            {{ $item->employee->name ?? $item->employee_name_snapshot }}
                                            @if($item->employee && $item->employee->job_title)
                                                <div class="text-[10px] text-gray-400 font-normal mt-0.5">{{ $item->employee->job_title }}</div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 font-mono text-gray-600 tracking-wider">
                                            {{ $item->employee->masked_national_id ?? $item->employee_national_id_snapshot }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <select name="items[{{ $item->id }}][status]"
                                                    {{ !$canUpdate ? 'disabled' : '' }}
                                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm {{ $item->status ? 'font-semibold text-gray-900' : 'text-gray-400' }}">
                                                <option value="" class="text-gray-400">-- غير محدد --</option>
                                                @foreach($statuses as $status)
                                                    <option value="{{ $status }}" {{ $item->status === $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <input type="text" name="items[{{ $item->id }}][notes]"
                                                   value="{{ $item->notes }}"
                                                   {{ !$canUpdate ? 'disabled' : '' }}
                                                   placeholder="أي ملاحظات أو مستند مبرر..."
                                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                                            لا يوجد موظفون نشطون مسجلون في هذه الجهة. يمكنك إضافة موظفين أولاً من تبويب "الموظفون".
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($canUpdate || $canSubmit)
                        <div class="p-4 sm:p-5 bg-slate-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-4">
                            <div class="text-xs text-gray-500">
                                تأكد من حفظ التعديلات بانتظام قبل اعتماد الكشف.
                            </div>
                            <div class="flex items-center gap-3">
                                @if($canUpdate)
                                    <button type="submit" class="px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                        حفظ المسودة
                                    </button>
                                @endif

                                @if($canSubmit)
                                    <button type="button" onclick="submitRosterDirectly()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $orgUnit->type === \App\Models\OrganizationalUnit::TYPE_CENTER ? 'اعتماد أولي وإرسال للقيادة الفرعية' : 'اعتماد نهائي وقفل الكشف' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </form>

            <!-- Separate Form for Submitting / Locking -->
            @if($canSubmit)
                <form id="submitRosterForm" method="POST" action="{{ route('rosters.submit', $roster) }}" class="hidden">
                    @csrf
                    <input type="hidden" name="version" value="{{ $roster->version }}">
                </form>
                <script>
                    function submitRosterDirectly() {
                        if (confirm('هل أنت متأكد من رغبتك في اعتماد هذا الكشف وقفل التحرير؟ لا يمكن التراجع إلا بصلاحية تصحيح خاصة ومبرر إلزامي.')) {
                            document.getElementById('submitRosterForm').submit();
                        }
                    }
                </script>
            @endif

            <!-- Audit Trail / History Section -->
            @if($roster->auditLogs->isNotEmpty())
                <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-5 sm:p-6">
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        سجل التعديلات والاعتمادات (غير قابل للتعديل)
                    </h3>
                    <div class="space-y-3">
                        @foreach($roster->auditLogs as $log)
                            <div class="p-3 bg-slate-50 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-2">
                                <div>
                                    <span class="font-bold text-gray-900">{{ $log->user->name ?? 'مستخدم غير معرف' }}</span>
                                    <span class="text-gray-500">({{ $log->action_label }}):</span>
                                    <span class="text-gray-700 mr-1 font-medium">{{ $log->reason ?: '—' }}</span>
                                    @if($log->field_changed)
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            الحقل المعدل: {{ $log->field_changed }} (من: {{ $log->old_value }} إلى: {{ $log->new_value }})
                                        </div>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-400 font-mono whitespace-nowrap">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Modal for Mandatory Correction Reason -->
        @if($isLocked && $canCorrect)
            <div x-show="showCorrectionModal" style="display: none;"
                 class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div @click.away="showCorrectionModal = false"
                     class="bg-white rounded-2xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">تصحيح الكشف المعتمد</h3>
                            <p class="text-xs text-rose-600 mt-1 font-medium">يتطلب النظام كتابة سبب تصحيح إلزامي ومفصل يتم توثيقه في سجل التدقيق.</p>
                        </div>
                        <button type="button" @click="showCorrectionModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                    </div>

                    <form method="POST" action="{{ route('rosters.correct', $roster) }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="reason" class="block text-xs font-bold text-gray-800 mb-1">
                                سبب وتبرير التصحيح الإداري <span class="text-rose-500">* (إلزامي)</span>
                            </label>
                            <textarea name="reason" id="reason" rows="3" required minlength="5"
                                      class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm"
                                      placeholder="اذكر بالتفصيل مبرر تعديل الكشف بعد اعتماده النهائي..."></textarea>
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden max-h-80 overflow-y-auto">
                            <table class="w-full text-right text-xs text-gray-700">
                                <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2.5">الموظف</th>
                                        <th class="px-4 py-2.5">الحالة الحالية -> الحالة المصححة</th>
                                        <th class="px-4 py-2.5">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($roster->items as $item)
                                        <tr>
                                            <td class="px-4 py-2.5 font-bold text-gray-800">
                                                {{ $item->employee->name ?? $item->employee_name_snapshot }}
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <select name="items[{{ $item->id }}][status]" required
                                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                                                    @foreach($statuses as $status)
                                                        <option value="{{ $status }}" {{ $item->status === $status ? 'selected' : '' }}>
                                                            {{ $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="text" name="items[{{ $item->id }}][notes]"
                                                       value="{{ $item->notes }}"
                                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showCorrectionModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                إلغاء
                            </button>
                            <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                حفظ التصحيح الإداري وتوثيقه
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
