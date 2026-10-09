<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900">تغيير كلمة المرور المؤقتة</h1>
        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3 mt-3">
            تم إنشاء حسابك بكلمة مرور مؤقتة. يلزم اختيار كلمة مرور جديدة خاصة بك قبل متابعة الاستخدام.
        </p>
    </div>

    <form method="POST" action="{{ route('password.force-update') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="كلمة المرور الجديدة" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="8 أحرف على الأقل" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="تأكيد كلمة المرور الجديدة" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="إعادة كتابة كلمة المرور" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
                حفظ كلمة المرور والدخول
            </button>
        </div>
    </form>
</x-guest-layout>
