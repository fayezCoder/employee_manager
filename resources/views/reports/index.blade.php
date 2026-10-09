<x-app-layout>
    <style>
        @media print {
            nav, header, .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-container { width: 100% !important; margin: 0 !important; padding: 0 !important; }
            table { border-collapse: collapse !important; width: 100% !important; }
            th, td { border: 1px solid #ddd !important; padding: 8px !important; text-align: right !important; }
        }
    </style>

    <div class="py-8 print-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Title & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">تقرير الحضور والانصراف التفصيلي</h1>
                    <p class="text-sm text-gray-500 mt-1">عرض قابل للطباعة والتصدير حسب المعايير المحددة</p>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>طباعة التقرير</span>
                    </button>

                    <a href="{{ route('reports.export', request()->query()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>تصدير ملف CSV</span>
                    </a>
                </div>
            </div>

            <!-- Filter Bar (Hidden in print) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 no-print">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">القسم</label>
                        <select name="department_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الأقسام</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">الموظف</label>
                        <select name="user_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الموظفين</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">حالة السجل</label>
                        <select name="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الحالات</option>
                            <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>مكتمل</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>غير مكتمل</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">عرض التقرير</button>
                        <a href="{{ route('reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">إعادة ضبط</a>
                    </div>
                </form>
            </div>

            <!-- Printable Report Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="hidden print:block text-center p-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold">تقرير حضور وانصراف الموظفين</h2>
                    <p class="text-xs text-gray-600">تاريخ استخراج التقرير: {{ now('Asia/Riyadh')->format('Y-m-d H:i') }}</p>
                </div>

                @if($records->isEmpty())
                    <div class="text-center py-12 text-gray-500 text-sm">لا توجد بيانات مطابقة للتقرير.</div>
                @else
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase">
                            <tr>
                                <th class="py-3 px-4">#</th>
                                <th class="py-3 px-4">اسم الموظف</th>
                                <th class="py-3 px-4">القسم</th>
                                <th class="py-3 px-4">تاريخ الحضور</th>
                                <th class="py-3 px-4">وقت الحضور</th>
                                <th class="py-3 px-4">وقت الانصراف</th>
                                <th class="py-3 px-4">ساعات العمل</th>
                                <th class="py-3 px-4">حالة السجل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($records as $index => $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-400 text-xs">{{ $records->firstItem() + $index }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-900">{{ $record->user->name }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $record->user->department?->name ?? '—' }}</td>
                                    <td class="py-3 px-4 font-medium text-gray-900">{{ $record->date->format('Y-m-d') }}</td>
                                    <td class="py-3 px-4 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckIn() }}</td>
                                    <td class="py-3 px-4 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckOut() ?? '—' }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-800">{{ $record->getFormattedDuration() }}</td>
                                    <td class="py-3 px-4">
                                        @if($record->status === 'complete')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">مكتمل</span>
                                        @elseif($record->status === 'open')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">مفتوح</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800">غير مكتمل</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-gray-100 no-print">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
