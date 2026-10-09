<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">سجلات الحضور والانصراف</h1>
                    <p class="text-sm text-gray-500 mt-1">عرض وتصفية سجلات العمل اليومية</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @can('admin')
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
                    @endcan

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">حالة السجل</label>
                        <select name="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الحالات</option>
                            <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>مكتمل</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوح (اليوم)</option>
                            <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>غير مكتمل (سابق)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">تطبيق التصفية</button>
                        <a href="{{ route('attendance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">إعادة ضبط</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($records->isEmpty())
                    <div class="text-center py-12 text-gray-500 text-sm">لا توجد سجلات حضور تطابق معايير التصفية.</div>
                @else
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase">
                            <tr>
                                <th class="py-3.5 px-6">الموظف</th>
                                <th class="py-3.5 px-6">القسم</th>
                                <th class="py-3.5 px-6">التاريخ</th>
                                <th class="py-3.5 px-6">وقت الحضور</th>
                                <th class="py-3.5 px-6">وقت الانصراف</th>
                                <th class="py-3.5 px-6">المدة</th>
                                <th class="py-3.5 px-6">الحالة</th>
                                @can('admin')
                                    <th class="py-3.5 px-6 text-center">الإجراء</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($records as $record)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-900">{{ $record->user->name }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $record->user->department?->name ?? '—' }}</td>
                                    <td class="py-4 px-6 font-medium text-gray-900">{{ $record->date->format('Y-m-d') }}</td>
                                    <td class="py-4 px-6 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckIn() }}</td>
                                    <td class="py-4 px-6 font-mono text-gray-700 dir-ltr text-right">{{ $record->getFormattedCheckOut() ?? '—' }}</td>
                                    <td class="py-4 px-6 font-medium text-gray-800">{{ $record->getFormattedDuration() }}</td>
                                    <td class="py-4 px-6">
                                        @if($record->status === 'complete')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">مكتمل</span>
                                        @elseif($record->status === 'open')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">مفتوح</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">سجل غير مكتمل</span>
                                        @endif
                                    </td>
                                    @can('admin')
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('attendance.correct', $record) }}" class="text-amber-700 hover:text-amber-900 font-semibold text-xs px-2.5 py-1 bg-amber-50 hover:bg-amber-100 rounded-md transition border border-amber-200">تصحيح</a>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-gray-100">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
