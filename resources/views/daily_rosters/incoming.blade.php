<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    الكشوفات الواردة للاعتماد
                </h2>
                <p class="text-xs text-gray-500 mt-1">مراجعة كشوفات المراكز والفروع التابعة واعتمادها</p>
            </div>
            <a href="{{ route('rosters.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                العودة للكشوفات
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

            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm">
                        الكشوفات المحالة للاعتماد الإداري
                    </h3>
                    <span class="text-xs text-gray-500">
                        العدد: {{ $incomingRosters->total() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-gray-700">
                        <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3.5">تاريخ الكشف</th>
                                <th class="px-5 py-3.5">الجهة التابعة</th>
                                <th class="px-5 py-3.5">الجهة الأعلى</th>
                                <th class="px-5 py-3.5">مرسل الكشف</th>
                                <th class="px-5 py-3.5">حالة الكشف</th>
                                <th class="px-5 py-3.5 text-center">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($incomingRosters as $roster)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 font-bold text-gray-900 font-mono">
                                        {{ $roster->date->toDateString() }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-800">{{ $roster->organizationalUnit->name }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $roster->organizationalUnit->type_label }} ({{ $roster->organizationalUnit->code }})</div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $roster->organizationalUnit->parent ? $roster->organizationalUnit->parent->name : 'القيادة الرئيسية' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-800">{{ $roster->submitter->name ?? '—' }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">{{ $roster->submitted_at ? $roster->submitted_at->format('Y-m-d H:i') : '' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $roster->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('rosters.show', ['orgUnit' => $roster->org_unit_id, 'date' => $roster->date->toDateString()]) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                            مراجعة واعتماد
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                        لا توجد كشوفات بانتظار الاعتماد حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($incomingRosters->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $incomingRosters->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
