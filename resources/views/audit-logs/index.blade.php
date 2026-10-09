<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">سجل تدقيق تصحيحات الحضور (Audit Logs)</h1>
                <p class="text-sm text-gray-500 mt-1">تتبع كافة التعديلات والتصحيحات يدويًا التي قام بها المديرون على أوقات الحضور والانصراف</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($logs->isEmpty())
                    <div class="text-center py-12 text-gray-500 text-sm">لا توجد سجلات تصحيح مُسجلة حتى الآن.</div>
                @else
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase">
                            <tr>
                                <th class="py-3.5 px-6">تاريخ ووقت التعديل</th>
                                <th class="py-3.5 px-6">المدير المنفذ</th>
                                <th class="py-3.5 px-6">الموظف المعني</th>
                                <th class="py-3.5 px-6">الحقل المعدل</th>
                                <th class="py-3.5 px-6">القيمة السابقة</th>
                                <th class="py-3.5 px-6">القيمة الجديدة</th>
                                <th class="py-3.5 px-6">سبب التعديل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 text-gray-500 font-mono text-xs dir-ltr text-right">
                                        {{ $log->created_at->setTimezone('Asia/Riyadh')->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-purple-900">{{ $log->admin->name }}</td>
                                    <td class="py-4 px-6 font-bold text-gray-900">{{ $log->attendanceRecord->user->name }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                                            {{ $log->field_changed === 'check_in' ? 'وقت الحضور' : 'وقت الانصراف' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-mono text-xs text-red-600 dir-ltr text-right">
                                        {{ $log->old_value ? $log->old_value->setTimezone('Asia/Riyadh')->format('Y-m-d H:i') : 'لا يوجد' }}
                                    </td>
                                    <td class="py-4 px-6 font-mono text-xs text-green-600 dir-ltr text-right">
                                        {{ $log->new_value ? $log->new_value->setTimezone('Asia/Riyadh')->format('Y-m-d H:i') : 'لا يوجد' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-700 max-w-xs bg-amber-50/40 rounded p-2 text-xs">
                                        {{ $log->reason }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
