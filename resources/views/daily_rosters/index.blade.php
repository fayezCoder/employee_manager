<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    كشوفات التحضير اليومية
                </h2>
                <p class="text-xs text-gray-500 mt-1">متابعة وإدخال واعتماد حضور وغياب الموظفين في الجهات التنظيمية</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('rosters.incoming') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-semibold transition">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    الكشوفات الواردة للاعتماد
                </a>
            </div>
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

            <!-- Date Selector Bar -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-5">
                <form method="GET" action="{{ route('rosters.index') }}" class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <label for="date" class="text-xs font-bold text-gray-700">تاريخ الكشف اليومي:</label>
                        <input type="date" name="date" id="date" value="{{ $date }}"
                               onchange="this.form.submit()"
                               class="rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm font-medium">
                        <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                            عرض التاريخ
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('rosters.index', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}" class="p-2 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-xs flex items-center gap-1 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            اليوم السابق
                        </a>
                        <a href="{{ route('rosters.index', ['date' => now('Asia/Riyadh')->toDateString()]) }}" class="px-3 py-2 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 text-xs font-semibold transition">
                            اليوم
                        </a>
                        <a href="{{ route('rosters.index', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}" class="p-2 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-xs flex items-center gap-1 transition">
                            اليوم التالي
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Units Rosters Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($units as $unit)
                    @php
                        $roster = $rosters->get($unit->id);
                    @endphp
                    <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-5 flex flex-col justify-between hover:border-indigo-300 transition group">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-gray-100 text-gray-600">
                                    {{ $unit->type_label }}
                                </span>

                                @if(!$roster)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                        لم يُنشأ بعد
                                    </span>
                                @elseif($roster->status === \App\Models\DailyRoster::STATUS_DRAFT)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        مسودة
                                    </span>
                                @elseif($roster->status === \App\Models\DailyRoster::STATUS_CENTER_LOCKED)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        معتمد ومقفل من المركز
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        مكتمل ومعتمد نهائياً
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-bold text-gray-900 text-sm group-hover:text-indigo-600 transition">
                                {{ $unit->name }}
                            </h3>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                الرمز: {{ $unit->code }}
                            </div>

                            @if($roster)
                                <div class="mt-4 pt-3 border-t border-gray-100 text-xs space-y-1 text-gray-500">
                                    @php
                                        $itemsCount = $roster->items->count();
                                        $markedCount = $roster->items->whereNotNull('status')->count();
                                    @endphp
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span>إنجاز التحضير:</span>
                                        <span class="font-bold {{ $markedCount === $itemsCount && $itemsCount > 0 ? 'text-emerald-600' : 'text-gray-700' }}">
                                            {{ $markedCount }} من أصل {{ $itemsCount }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-indigo-600 h-full rounded-full transition-all"
                                             style="width: {{ $itemsCount > 0 ? ($markedCount / $itemsCount) * 100 : 0 }}%"></div>
                                    </div>

                                    @if($roster->was_corrected)
                                        <div class="mt-2 inline-flex items-center gap-1 text-[10px] text-purple-700 font-semibold bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                                            سجل خضع لتصحيح إداري
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">
                                التاريخ: {{ $date }}
                            </span>
                            <a href="{{ route('rosters.show', ['orgUnit' => $unit->id, 'date' => $date]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                {{ $roster ? 'فتح الكشف' : 'بدء التحضير' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500 bg-white rounded-2xl border border-gray-200">
                        لا توجد جهات تنظيمية مسندة إلى حسابك.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
