<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">تسجيل الدخول للنظام</h2>
        <p class="text-xs text-slate-500 mt-1">الرجاء إدخال بيانات الاعتماد للوصول إلى لوحة المتابعة</p>
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

        // Only consider system in demo mode if other demo accounts exist (before reset)
        $hasDemoManagers = $demoUsers->except('admin@example.com')->isNotEmpty();
    @endphp

    @if($hasDemoManagers)
        <!-- Demo Credentials Drawer / Quick Auto-fill -->
        <div x-data="{ showDemo: true }" class="mb-6">
            <div class="p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-2xl">
                <div class="flex items-center justify-between cursor-pointer" @click="showDemo = !showDemo">
                    <div class="flex items-center gap-2 text-indigo-900 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        <span>حسابات التجربة السريعة (Demo)</span>
                    </div>
                    <span class="text-[10px] bg-indigo-100 text-indigo-700 font-semibold px-2 py-0.5 rounded-lg" x-text="showDemo ? 'إخفاء' : 'عرض'"></span>
                </div>

                <div x-show="showDemo" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-3 pt-3 border-t border-indigo-100/60 space-y-2">
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        اختر أحد الأدوار القيادية لتعبئة الحساب تلقائياً واستعراض شاشات النظام:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @if(isset($demoUsers['admin@example.com']))
                            <button type="button" onclick="fillCredentials('{{ $demoUsers['admin@example.com']->email }}', 'password')" class="text-right p-2 bg-white hover:bg-indigo-50 border border-indigo-200/80 rounded-xl text-xs font-medium text-slate-800 transition flex items-center justify-between shadow-sm">
                                <span class="font-bold text-indigo-950 truncate">👑 {{ $demoUsers['admin@example.com']->name }}</span>
                                <span class="text-[10px] text-indigo-600 font-semibold shrink-0">تعبئة</span>
                            </button>
                        @endif

                        @if(isset($demoUsers['branch_admin@example.com']))
                            <button type="button" onclick="fillCredentials('branch_admin@example.com', 'password')" class="text-right p-2 bg-white hover:bg-purple-50 border border-purple-200/80 rounded-xl text-xs font-medium text-slate-800 transition flex items-center justify-between shadow-sm">
                                <span class="font-bold text-purple-950 truncate">🏢 {{ $demoUsers['branch_admin@example.com']->name }} (قيادة فرعية)</span>
                                <span class="text-[10px] text-purple-600 font-semibold shrink-0">تعبئة</span>
                            </button>
                        @endif

                        @if(isset($demoUsers['unit_admin@example.com']))
                            <button type="button" onclick="fillCredentials('unit_admin@example.com', 'password')" class="text-right p-2 bg-white hover:bg-amber-50 border border-amber-200/80 rounded-xl text-xs font-medium text-slate-800 transition flex items-center justify-between shadow-sm">
                                <span class="font-bold text-amber-950 truncate">📋 {{ $demoUsers['unit_admin@example.com']->name }} (مركز 1)</span>
                                <span class="text-[10px] text-amber-600 font-semibold shrink-0">تعبئة</span>
                            </button>
                        @endif

                        @if(isset($demoUsers['division_admin@example.com']))
                            <button type="button" onclick="fillCredentials('division_admin@example.com', 'password')" class="text-right p-2 bg-white hover:bg-sky-50 border border-sky-200/80 rounded-xl text-xs font-medium text-slate-800 transition flex items-center justify-between shadow-sm">
                                <span class="font-bold text-sky-950 truncate">🏢 {{ $demoUsers['division_admin@example.com']->name }} (شعبة إدارة)</span>
                                <span class="text-[10px] text-sky-600 font-semibold shrink-0">تعبئة</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني / الحساب</label>
            <div class="relative">
                <input id="email" class="block w-full text-sm rounded-xl border-slate-200 bg-slate-50/50 pr-10 pl-3 py-2.5 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition text-right"
                       type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@domain.gov.sa" />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور</label>
            <div class="relative">
                <input id="password" class="block w-full text-sm rounded-xl border-slate-200 bg-slate-50/50 pr-10 pl-3 py-2.5 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition text-right font-mono"
                       type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded-lg border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500/20" name="remember">
                <span class="ms-2 text-xs font-medium text-slate-600">تذكر تسجيل دخولي على هذا الجهاز</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/25 transition duration-150 ease-in-out flex items-center justify-center gap-2 group">
                <span>دخول النظام</span>
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
