<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Admin Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">لوحة مؤشرات المدير 📊</h1>
                <p class="text-sm text-gray-500 mt-1">نظرة عامة على حالة الحضور والموظفين اليوم: {{ now('Asia/Riyadh')->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('roster-employees.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>إضافة موظف</span>
                </a>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>التقارير</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Active Employees -->
            <a href="{{ route('roster-employees.index') }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:border-blue-300 hover:shadow-md transition group">
                <div>
                    <span class="block text-xs font-semibold text-gray-500 group-hover:text-blue-600 transition">موظفو الكشوفات الفعّالون</span>
                    <span class="text-3xl font-extrabold text-gray-900 mt-1 block">{{ $activeEmployeesCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </a>

            <!-- Checked-in Today -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-gray-500">حضروا اليوم</span>
                    <span class="text-3xl font-extrabold text-gray-900 mt-1 block">{{ $todayCheckInsCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <!-- Open Sessions Currently -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-gray-500">جلسات مفتوحة حالياً</span>
                    <span class="text-3xl font-extrabold text-emerald-600 mt-1 block">{{ $openSessionsCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <!-- Incomplete Past Sessions -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-gray-500">سجلات غير مكتملة (سابقة)</span>
                    <span class="text-3xl font-extrabold text-amber-600 mt-1 block">{{ $incompleteSessionsCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>

        <!-- Attention Required: Incomplete Past Sessions Table -->
        @if($incompleteRecords->isNotEmpty())
            <div class="bg-amber-50/50 rounded-2xl border border-amber-200 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="p-2 rounded-lg bg-amber-100 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-amber-900">سجلات تتطلب تدخل المدير (غير مكتملة من أيام سابقة)</h2>
                        <p class="text-xs text-amber-700">قام الموظفون ببدء الجلسة ولم يسجلوا الانصراف في ذلك اليوم.</p>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white rounded-xl border border-amber-200 shadow-sm">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-amber-50 text-amber-900 text-xs font-semibold">
                            <tr>
                                <th class="py-3 px-4">الموظف</th>
                                <th class="py-3 px-4">القسم</th>
                                <th class="py-3 px-4">تاريخ الحضور</th>
                                <th class="py-3 px-4">وقت الحضور المسجل</th>
                                <th class="py-3 px-4 text-center">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($incompleteRecords as $record)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-gray-900">{{ $record->user->name }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $record->user->department?->name ?? '—' }}</td>
                                    <td class="py-3 px-4 text-gray-900">{{ $record->date->format('Y-m-d') }}</td>
                                    <td class="py-3 px-4 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckIn() }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('attendance.correct', $record) }}" class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-lg transition">
                                            <span>إدخال وقت الانصراف</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>
