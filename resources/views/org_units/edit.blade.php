<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    تعديل الجهة التنظيمية: {{ $orgUnit->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">تحديث بيانات الجهة وتبعية الهيكل الإداري</p>
            </div>
            <a href="{{ route('org-units.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                العودة للهيكل
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

                <form method="POST" action="{{ route('org-units.update', $orgUnit) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">اسم الجهة التنظيمية <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $orgUnit->name) }}" required
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-1">الرمز التعريفي الفريد <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code', $orgUnit->code) }}" required
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-1">نوع الجهة <span class="text-rose-500">*</span></label>
                            <select name="type" id="type" required
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                                @foreach($typeLabels as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('type', $orgUnit->type) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="parent_id" class="block text-sm font-semibold text-gray-700 mb-1">الجهة الأعلى (التبعية)</label>
                        <select name="parent_id" id="parent_id"
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                            <option value="">-- بدون جهة أعلى (خاص بالقيادة الرئيسية فقط) --</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $orgUnit->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }} ({{ $parent->type_label }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                            • يمنع النظام اختيار الجهة نفسها أو أحد فروعها التابعة لتفادي التكرار الدائري.<br>
                            • في حال وجود سجلات تحضير سابقة مرتبطة بالجهة، يمنع تغيير التبعية حفاظاً على سلامة السجلات التاريخية.
                        </p>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $orgUnit->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                            <span class="text-sm font-medium text-gray-700">الجهة مفعلة ونشطة في النظام</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('org-units.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            إلغاء
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
