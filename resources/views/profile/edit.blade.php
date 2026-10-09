<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">تغيير كلمة المرور</h1>
                <p class="text-sm text-gray-500 mt-1">تحديث كلمة مرور الحساب الخاص بك في النظام</p>
            </div>

            <!-- Profile Info Box -->
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200 text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">اسم الموظف:</span>
                    <span class="font-bold text-gray-900">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">البريد الإلكتروني:</span>
                    <span class="font-mono text-gray-900 dir-ltr">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">القسم:</span>
                    <span class="font-bold text-gray-900">{{ Auth::user()->department?->name ?? 'غير محدد' }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                @if (session('status') === 'password-updated')
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-50 rounded-xl border border-green-200">
                        تم تحديث كلمة المرور بنجاح.
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="current_password" value="كلمة المرور الحالية" />
                        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full text-right" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="كلمة المرور الجديدة" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full text-right" required autocomplete="new-password" placeholder="8 أحرف على الأقل" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="تأكيد كلمة المرور الجديدة" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-right" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">حفظ كلمة المرور الجديدة</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
