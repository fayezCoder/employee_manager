<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    إضافة موظف جديد
                </h2>
                <p class="text-xs text-gray-500 mt-1">تسجيل موظف وإسناده للجهة التنظيمية ليتم إدراجه في كشف التحضير</p>
            </div>
            <a href="{{ route('roster-employees.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                العودة لقائمة الموظفين
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-6 sm:p-8">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                        <div class="flex items-center gap-2 text-rose-800 font-semibold text-sm mb-2">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            يرجى تصحيح الأخطاء التالية:
                        </div>
                        <ul class="list-disc list-inside text-xs text-rose-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('roster-employees.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">الاسم الرباعي للموظف <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm"
                               placeholder="الاسم الكامل كما في الهوية الوطنية">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="national_id" class="block text-xs font-semibold text-gray-700 mb-1">رقم الهوية الوطنية / الإقامة <span class="text-rose-500">*</span></label>
                            <input type="text" name="national_id" id="national_id" value="{{ old('national_id') }}" required
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm font-mono"
                                   placeholder="10XXXXXXXX">
                            <p class="text-[11px] text-gray-400 mt-1">يتم تقنيع رقم الهوية تلقائياً في شاشات العرض لحماية الخصوصية.</p>
                        </div>

                        <div>
                            <label for="job_title" class="block text-xs font-semibold text-gray-700 mb-1">المسمى الوظيفي / الرتبة</label>
                            <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm"
                                   placeholder="مثال: أخصائي إداري، فني اتصالات...">
                        </div>
                    </div>

                    <div>
                        <label for="org_unit_id" class="block text-xs font-semibold text-gray-700 mb-1">الجهة التنظيمية المسند إليها <span class="text-rose-500">*</span></label>
                        <select name="org_unit_id" id="org_unit_id" required
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                            <option value="">-- اختر الجهة التابع لها الموظف --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('org_unit_id', request('org_unit_id')) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->type_label }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">يحق لأي جهة تنظيمية (قيادة رئيسية، قيادة فرعية، مركز، شعبة، قسم) ضم موظفين مباشرة.</p>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5">
                            <div>
                                <span class="text-xs font-semibold text-gray-700">الموظف نشط ومدرج تلقائياً في كشف التحضير اليومي</span>
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('roster-employees.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            إلغاء
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            حفظ بيانات الموظف
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
