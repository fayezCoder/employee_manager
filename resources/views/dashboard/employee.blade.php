<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Welcome Banner -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">مرحباً، {{ Auth::user()->name }} 👋</h1>
                <p class="text-sm text-gray-500 mt-1">قسم {{ Auth::user()->department?->name ?? 'غير محدد' }} | اليوم: {{ now('Asia/Riyadh')->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="text-left font-mono text-lg bg-gray-50 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 dir-ltr">
                {{ now('Asia/Riyadh')->format('h:i A') }} (توقيت الرياض)
            </div>
        </div>

        @if(Auth::user()->unitAssignments()->exists())
            <!-- Unit Manager Notice & Quick Action Banner -->
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-xs font-semibold text-indigo-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>مهام الإشراف والتحضير للجهات</span>
                    </div>
                    <h2 class="text-lg font-bold">لديك صلاحيات إدارة وتحضير جهات في الهيكل التنظيمي</h2>
                    <p class="text-indigo-200 text-xs">
                        أنت معين كمسؤول على:
                        @foreach(Auth::user()->unitAssignments as $assignment)
                            <span class="inline-block bg-white/10 px-2 py-0.5 rounded text-white font-medium mr-1">{{ $assignment->role_label }} ({{ $assignment->organizationalUnit?->name ?? 'عام' }})</span>
                        @endforeach
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('rosters.index') }}" class="px-5 py-2.5 bg-white text-indigo-900 hover:bg-indigo-50 rounded-xl text-sm font-bold shadow-sm transition">
                        الانتقال لكشف التحضير اليومي ←
                    </a>
                </div>
            </div>
        @endif

        <!-- Attendance Action Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center max-w-2xl mx-auto">
            @if($openAttendance)
                <!-- Open Session Status -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-sm font-semibold mb-4 border border-emerald-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>جلسة حضور نشطة حالياً</span>
                </div>

                <div class="space-y-2 mb-6">
                    <p class="text-gray-500 text-sm">وقت الحضور المسجل:</p>
                    <p class="text-3xl font-extrabold text-gray-900 dir-ltr">{{ $openAttendance->getFormattedCheckIn() }}</p>
                    <p class="text-xs text-gray-400">تاريخ الجلسة: {{ $openAttendance->date->format('Y-m-d') }}</p>
                </div>

                <form method="POST" action="{{ route('attendance.check-out') }}" onsubmit="return confirm('هل أنت متأكد من تسجيل الانصراف الآن؟');">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-lg rounded-xl shadow-md transition duration-150 flex items-center justify-center gap-3 mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>تسجيل الانصراف</span>
                    </button>
                </form>
            @elseif($todayAttendance && $todayAttendance->isComplete())
                <!-- Completed Today -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-semibold mb-4 border border-blue-200">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>اكتملت جلسة اليوم</span>
                </div>

                <div class="grid grid-cols-3 gap-4 my-6 bg-gray-50 p-4 rounded-xl text-center">
                    <div>
                        <span class="block text-xs text-gray-500">الحضور</span>
                        <span class="font-bold text-gray-800 dir-ltr">{{ $todayAttendance->getFormattedCheckIn() }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">الانصراف</span>
                        <span class="font-bold text-gray-800 dir-ltr">{{ $todayAttendance->getFormattedCheckOut() }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">المدة</span>
                        <span class="font-bold text-indigo-600">{{ $todayAttendance->getFormattedDuration() }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400">لا يمكنك فتح جلسة جديدة اليوم.</p>
            @else
                <!-- No Session / Ready to Check-In -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 text-gray-600 text-sm font-medium mb-4">
                    <span>لم يتم تسجيل الحضور اليوم بعد</span>
                </div>

                <p class="text-gray-600 mb-6 text-sm">انقر على الزر أدناه لتسجيل وقت حضورك في النظام.</p>

                <form method="POST" action="{{ route('attendance.check-in') }}">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-xl shadow-md transition duration-150 flex items-center justify-center gap-3 mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>تسجيل الحضور</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- Recent Records Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">آخر سجلات الحضور</h2>
            @if($recentRecords->isEmpty())
                <div class="text-center py-8 text-gray-500 text-sm">لا توجد سجلات حضور سابقة بعد.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase">
                            <tr>
                                <th class="py-3 px-4">التاريخ</th>
                                <th class="py-3 px-4">وقت الحضور</th>
                                <th class="py-3 px-4">وقت الانصراف</th>
                                <th class="py-3 px-4">المدة الإجمالية</th>
                                <th class="py-3 px-4">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentRecords as $record)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-4 font-medium text-gray-900">{{ $record->date->format('Y-m-d') }}</td>
                                    <td class="py-3 px-4 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckIn() }}</td>
                                    <td class="py-3 px-4 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckOut() ?? '—' }}</td>
                                    <td class="py-3 px-4 font-medium text-gray-800">{{ $record->getFormattedDuration() }}</td>
                                    <td class="py-3 px-4">
                                        @if($record->status === 'complete')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">مكتمل</span>
                                        @elseif($record->status === 'open')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">مفتوح</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">سجل غير مكتمل</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
