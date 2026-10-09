<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">تصحيح سجل الحضور (خاص بالمدير)</h1>
                <a href="{{ route('attendance.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">عودة للسجلات →</a>
            </div>

            <!-- Context Summary Box -->
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200 text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">اسم الموظف:</span>
                    <span class="font-bold text-gray-900">{{ $attendance->user->name }} ({{ $attendance->user->department?->name ?? 'بدون قسم' }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">تاريخ الجلسة:</span>
                    <span class="font-bold text-gray-900">{{ $attendance->date->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">وقت الحضور الحالي:</span>
                    <span class="font-mono text-gray-800 dir-ltr text-right">{{ $attendance->getFormattedCheckIn() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">وقت الانصراف الحالي:</span>
                    <span class="font-mono text-gray-800 dir-ltr text-right">{{ $attendance->getFormattedCheckOut() ?? 'غير مسجل' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">المدة الحالية:</span>
                    <span class="font-bold text-indigo-600">{{ $attendance->getFormattedDuration() }}</span>
                </div>
            </div>

            <!-- Correction Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('attendance.store-correction', $attendance) }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="field" value="الحقل المراد تصحيحه" />
                        <select id="field" name="field" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="check_out" {{ old('field') === 'check_out' ? 'selected' : '' }}>وقت الانصراف (Check-Out)</option>
                            <option value="check_in" {{ old('field') === 'check_in' ? 'selected' : '' }}>وقت الحضور (Check-In)</option>
                        </select>
                        <x-input-error :messages="$errors->get('field')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="value" value="الوقت والتاريخ الجديد (بتوقيت الرياض Local Time)" />
                        <x-text-input id="value" name="value" type="datetime-local" class="mt-1 block w-full"
                                      :value="old('value', $attendance->check_out ? $attendance->check_out->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i') : $attendance->check_in->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i'))" required />
                        <x-input-error :messages="$errors->get('value')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="reason" value="سبب التعديل (إلزامي للتدقيق)" />
                        <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="أدخل سبباً واضحاً للتصحيح..." required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>

                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                        ⚠️ <strong>تنبيه:</strong> هذا الإجراء سيقوم بتحديث وقت الجلسة وإعادة حساب المدة، وسيتم تسجيل العملية بالكامل مع القيمة السابقة والجديدة والسبب في سجل التدقيق الأمني (Audit Log).
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('attendance.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">إلغاء</a>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm rounded-lg shadow-sm transition">اعتماد التصحيح والتسجيل</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
