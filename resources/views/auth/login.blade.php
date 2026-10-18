<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">تسجيل الدخول — نظام إدارة الحضور ✨</h1>
        <p class="text-sm text-gray-500 mt-1">أدخل بيانات الحساب للوصول لنظام الحضور</p>
    </div>

    @php
        $demoUsers = \App\Models\User::whereIn('email', [
            'admin@example.com',
            'branch_admin@example.com',
            'unit_admin@example.com',
            'division_admin@example.com',
            'dept_admin@example.com',
            'sara@example.com',
        ])->get()->keyBy('email');

        // Only consider system in demo mode if other demo accounts exist
        $hasDemoManagers = $demoUsers->except('admin@example.com')->isNotEmpty();
    @endphp

    @if($hasDemoManagers)
        <!-- Demo Banner & One-Click Auto-Fill (Visible only during demo mode) -->
        <div class="mb-6 p-4 bg-slate-50 border border-indigo-100 rounded-xl">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 text-indigo-700 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>حسابات العرض التجريبي (Demo Credentials)</span>
                </div>
                <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">وضع العرض التجريبي</span>
            </div>
            <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                يمكنك الضغط على أي حساب أدناه للتعبئة الفورية وتجربة النظام بالدور القيادي المناسب (تختفي هذه الحسابات تلقائياً عند اعتماد النظام وتصفير البيانات التجريبية):
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @if(isset($demoUsers['admin@example.com']))
                    <button type="button" onclick="fillCredentials('{{ $demoUsers['admin@example.com']->email }}', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-indigo-50 border border-indigo-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                        <span class="text-indigo-600 font-bold group-hover:text-indigo-700 flex items-center justify-between">
                            <span>👑 {{ $demoUsers['admin@example.com']->name }}</span>
                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded">تعبئة</span>
                        </span>
                        <span class="text-slate-500 text-[11px] dir-ltr text-right">{{ $demoUsers['admin@example.com']->email }}</span>
                    </button>
                @endif

                @if(isset($demoUsers['branch_admin@example.com']))
                    <button type="button" onclick="fillCredentials('branch_admin@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-purple-50 border border-purple-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                        <span class="text-purple-600 font-bold group-hover:text-purple-700 flex items-center justify-between">
                            <span>🏢 القيادة الفرعية 1 (العميد إبراهيم)</span>
                            <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">تعبئة</span>
                        </span>
                        <span class="text-slate-500 text-[11px] dir-ltr text-right">branch_admin@example.com</span>
                    </button>
                @endif

                @if(isset($demoUsers['unit_admin@example.com']))
                    <button type="button" onclick="fillCredentials('unit_admin@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-amber-50 border border-amber-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                        <span class="text-amber-600 font-bold group-hover:text-amber-700 flex items-center justify-between">
                            <span>📋 قائد المركز 1 (الرائد عبد الرحمن)</span>
                            <span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded">تعبئة</span>
                        </span>
                        <span class="text-slate-500 text-[11px] dir-ltr text-right">unit_admin@example.com</span>
                    </button>
                @endif

                @if(isset($demoUsers['division_admin@example.com']))
                    <button type="button" onclick="fillCredentials('division_admin@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-sky-50 border border-sky-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                        <span class="text-sky-600 font-bold group-hover:text-sky-700 flex items-center justify-between">
                            <span>🏢 شعبة الإدارة (العقيد محمد)</span>
                            <span class="text-[10px] bg-sky-100 text-sky-700 px-1.5 py-0.5 rounded">تعبئة</span>
                        </span>
                        <span class="text-slate-500 text-[11px] dir-ltr text-right">division_admin@example.com</span>
                    </button>
                @endif

                @if(isset($demoUsers['dept_admin@example.com']))
                    <button type="button" onclick="fillCredentials('dept_admin@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-teal-50 border border-teal-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                        <span class="text-teal-600 font-bold group-hover:text-teal-700 flex items-center justify-between">
                            <span>📋 قسم المتابعة (النقيب خالد)</span>
                            <span class="text-[10px] bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded">تعبئة</span>
                        </span>
                        <span class="text-slate-500 text-[11px] dir-ltr text-right">dept_admin@example.com</span>
                    </button>
                @endif

                @if(isset($demoUsers['sara@example.com']))
                    <button type="button" onclick="fillCredentials('sara@example.com', 'password')" class="w-full text-right p-2.5 bg-white hover:bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-medium text-slate-800 transition flex flex-col gap-0.5 shadow-sm group">
                        <span class="text-emerald-600 font-bold group-hover:text-emerald-700 flex items-center justify-between">
                            <span>👨‍💼 سارة محمد (موظفة عادية)</span>
                            <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">تعبئة</span>
                        </span>
                        <span class="text-slate-500 text-[11px] dir-ltr text-right">sara@example.com</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

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
