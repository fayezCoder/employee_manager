<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">إدارة حسابات الموظفين</h1>
                    <p class="text-sm text-gray-500 mt-1">عرض، إضافة وتحديث بيانات الموظفين والأقسام وحالة التفعيل</p>
                </div>
                <a href="{{ route('roster-employees.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>إضافة موظف جديد</span>
                </a>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث باسم الموظف أو البريد..." class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <select name="department_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الأقسام</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>فعّال فقط</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطّل فقط</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">تصفية</button>
                        <a href="{{ route('employees.index') }}" class="py-2 px-3 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">إعادة</a>
                    </div>
                </form>
            </div>

            <!-- Employees Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($employees->isEmpty())
                    <div class="text-center py-12 text-gray-500 text-sm">لا يوجد موظفون يطابقون خيارات البحث.</div>
                @else
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase">
                            <tr>
                                <th class="py-3.5 px-6">الموظف</th>
                                <th class="py-3.5 px-6">البريد الإلكتروني</th>
                                <th class="py-3.5 px-6">القسم</th>
                                <th class="py-3.5 px-6">الصلاحية</th>
                                <th class="py-3.5 px-6">الحالة</th>
                                <th class="py-3.5 px-6 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($employees as $employee)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-900">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                                {{ mb_substr($employee->name, 0, 1) }}
                                            </span>
                                            <span>{{ $employee->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 font-mono text-xs dir-ltr text-right">{{ $employee->email }}</td>
                                    <td class="py-4 px-6 text-gray-700 font-medium">{{ $employee->department?->name ?? 'غير محدد' }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $employee->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $employee->isAdmin() ? 'مدير نظام' : 'موظف' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($employee->is_active)
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">فعّال</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">معطّل</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a href="{{ route('employees.edit', $employee) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">تعديل والحالة</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-gray-100">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
