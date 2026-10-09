<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">إدارة الأقسام</h1>
                    <p class="text-sm text-gray-500 mt-1">عرض وإضافة الأقسام التنظيمية بالمنشأة</p>
                </div>
                <a href="{{ route('departments.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>إضافة قسم جديد</span>
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($departments->isEmpty())
                    <div class="text-center py-12 text-gray-500 text-sm">لم يتم تسجيل أي أقسام بعد.</div>
                @else
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase">
                            <tr>
                                <th class="py-3.5 px-6">اسم القسم</th>
                                <th class="py-3.5 px-6">عدد الموظفين المنتسبين</th>
                                <th class="py-3.5 px-6">تاريخ الإنشاء</th>
                                <th class="py-3.5 px-6 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($departments as $dept)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-900">{{ $dept->name }}</td>
                                    <td class="py-4 px-6 text-gray-700 font-medium">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $dept->users_count ?? $dept->users()->count() }} موظف
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 text-xs dir-ltr text-right">{{ $dept->created_at->format('Y-m-d') }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('departments.edit', $dept) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 rounded-md transition">تعديل</a>

                                            @if(($dept->users_count ?? $dept->users()->count()) === 0)
                                                <form method="POST" action="{{ route('departments.destroy', $dept) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs px-2.5 py-1 bg-red-50 hover:bg-red-100 rounded-md transition">حذف</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
