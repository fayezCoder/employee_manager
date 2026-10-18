<nav x-data="{ open: false }" class="bg-white border-b border-slate-200/80 sticky top-0 z-40 shadow-sm/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <!-- Brand Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 bg-gradient-to-tr from-indigo-600 to-indigo-500 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="font-black text-slate-900 text-lg tracking-tight">ضـابط</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links (Clean & Organized) -->
                <div class="hidden md:flex items-center space-x-reverse space-x-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>لوحة التحكم</span>
                    </a>

                    @if(Auth::user()->isSuperAdmin() || Auth::user()->unitAssignments()->exists())
                        <!-- Operations Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('rosters.*', 'roster-employees.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>العمليات الميدانية</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('rosters.index')" class="flex items-center gap-2 text-xs py-2.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>كشوفات التحضير اليومية</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('roster-employees.index')" class="flex items-center gap-2 text-xs py-2.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>سجل الموظفين والأفراد</span>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                        <!-- Reports & Investigations -->
                        <a href="{{ route('roster-reports.index') }}"
                           class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('roster-reports.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>التقارير</span>
                        </a>

                        <a href="{{ route('investigations.index') }}"
                           class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('investigations.*') ? 'bg-amber-50 text-amber-800 shadow-sm border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            <span>متابعة التحقيقات</span>
                        </a>
                    @endif

                    @can('super-admin')
                        <!-- System Administration Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('org-units.*', 'user-management.*', 'system.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>الإدارة والنظام</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('org-units.index')" class="flex items-center gap-2 text-xs py-2.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span>الهيكل التنظيمي</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('user-management.index')" class="flex items-center gap-2 text-xs py-2.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span>إدارة المستخدمين والصلاحيات</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('system.deploy-migrate')" class="flex items-center gap-2 text-xs py-2.5 text-indigo-700 font-semibold border-t border-slate-100">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7zm0 5h16M9 4v16"/></svg>
                                    <span>لوحة التحديثات والتصفير</span>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    @endcan
                </div>
            </div>

            <!-- User Menu Desktop -->
            <div class="hidden md:flex items-center gap-3">
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 px-3 py-1.5 border border-slate-200 rounded-xl bg-slate-50/70 hover:bg-slate-100/70 text-slate-800 text-xs font-bold transition">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-xs py-2">
                            تغيير كلمة المرور
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-rose-600 hover:text-rose-700 text-xs py-2 border-t border-slate-100">
                                تسجيل الخروج
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger menu button for mobile -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu with clear sections -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-4 shadow-xl">
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>لوحة التحكم</span>
            </a>

            @if(Auth::user()->isSuperAdmin() || Auth::user()->unitAssignments()->exists())
                <div class="pt-2 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3">العمليات والتحضير</div>
                <a href="{{ route('rosters.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('rosters.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>كشوفات التحضير اليومية</span>
                </a>
                <a href="{{ route('roster-employees.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('roster-employees.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>سجل الموظفين</span>
                </a>
                <a href="{{ route('roster-reports.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('roster-reports.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>تقارير التحضير</span>
                </a>
                <a href="{{ route('investigations.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold text-amber-800 bg-amber-50/70 border border-amber-200">
                    <span>متابعة التحقيقات (مهلة 7 أيام)</span>
                </a>
            @endif

            @can('super-admin')
                <div class="pt-3 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3">الإدارة العليا والنظام</div>
                <a href="{{ route('org-units.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <span>الهيكل التنظيمي</span>
                </a>
                <a href="{{ route('user-management.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <span>إدارة المستخدمين</span>
                </a>
                <a href="{{ route('system.deploy-migrate') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                    <span>لوحة التحديثات والتصفير</span>
                </a>
            @endcan
        </div>

        <!-- User profile area in mobile -->
        <div class="pt-3 border-t border-slate-200 space-y-1">
            <div class="px-3 py-2 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ Auth::user()->isAdmin() ? 'مدير' : 'موظف' }}
                </span>
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 rounded-lg">تغيير كلمة المرور</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-right px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-lg">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</nav>
