<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">تسجيل الدخول</h1>
        <p class="text-sm text-gray-500 mt-1">أدخل بيانات الحساب للوصول لنظام الحضور</p>
    </div>

    <!-- Demo Banner & One-Click Auto-Fill -->
    <div class="mb-6 p-4 bg-slate-50 border border-indigo-100 rounded-xl">
        <div class="flex items-center gap-2 text-indigo-700 text-sm font-semibold mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>حسابات العرض التجريبي (Demo Credentials)</span>
        </div>
        <p class="text-xs text-slate-600 mb-3 leading-relaxed">
            يمكنك الضغط على أي زر أدناه للتعبئة الفورية وتجربة النظام بالدور المناسب:
        </p>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="fillCredentials('admin@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-indigo-50 border border-indigo-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                <span class="text-indigo-600 font-bold group-hover:text-indigo-700 flex items-center justify-between">
                    <span>👑 حساب مدير</span>
                    <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded">تعبئة</span>
                </span>
                <span class="text-slate-500 text-[11px] dir-ltr text-right">admin@example.com</span>
            </button>
            <button type="button" onclick="fillCredentials('sara@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                <span class="text-emerald-600 font-bold group-hover:text-emerald-700 flex items-center justify-between">
                    <span>👨‍💼 حساب موظف</span>
                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">تعبئة</span>
                </span>
                <span class="text-slate-500 text-[11px] dir-ltr text-right">sara@example.com</span>
            </button>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block mt-1 w-full text-right" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="كلمة المرور" />
            <x-text-input id="password" class="block mt-1 w-full text-right" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">تذكرني على هذا الجهاز</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition duration-150 ease-in-out flex items-center justify-center gap-2">
                <span>تسجيل الدخول</span>
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </form>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
