<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-bold text-gray-800 text-lg">نظام الحضور</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-reverse space-x-4 sm:flex items-center">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        لوحة التحكم
                    </a>

                    <a href="{{ route('rosters.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('rosters.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        التحضير اليومي
                    </a>

                    <a href="{{ route('roster-employees.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('roster-employees.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        سجل الموظفين
                    </a>

                    <a href="{{ route('roster-reports.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('roster-reports.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        تقارير التحضير
                    </a>

                    @can('super-admin')
                        <a href="{{ route('org-units.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('org-units.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            الهيكل التنظيمي
                        </a>

                        <a href="{{ route('user-management.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('user-management.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            إدارة المستخدمين
                        </a>

                        <a href="{{ route('system.deploy-migrate') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('system.deploy-migrate') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            تحديث قاعدة البيانات
                        </a>
                    @endcan

                    @can('admin')
                        <a href="{{ route('attendance.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                            النظام السابق
                        </a>
                    @endcan
                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </span>
                                <span>{{ Auth::user()->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ Auth::user()->isAdmin() ? 'مدير' : 'موظف' }}
                                </span>
                            </div>
                            <svg class="fill-current h-4 w-4 ms-2" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            تغيير كلمة المرور
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700">
                                تسجيل الخروج
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger menu for mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200 bg-gray-50 px-4 pt-2 pb-3 space-y-1">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">لوحة التحكم</a>
        <a href="{{ route('rosters.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">التحضير اليومي</a>
        <a href="{{ route('roster-employees.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">سجل الموظفين</a>
        <a href="{{ route('roster-reports.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">تقارير التحضير</a>

        @can('super-admin')
            <a href="{{ route('org-units.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-700 hover:bg-indigo-50 font-semibold">الهيكل التنظيمي</a>
            <a href="{{ route('user-management.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-700 hover:bg-indigo-50 font-semibold">إدارة المستخدمين</a>
            <a href="{{ route('system.deploy-migrate') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-700 hover:bg-indigo-50 font-semibold">تحديث قاعدة البيانات</a>
        @endcan

        @can('admin')
            <a href="{{ route('attendance.index') }}" class="block px-3 py-2 rounded-md text-sm text-gray-500 hover:bg-gray-100">النظام السابق (الحضور والانصراف)</a>
        @endcan
        <div class="border-t border-gray-200 pt-2">
            <div class="px-3 py-1 font-semibold text-gray-800">{{ Auth::user()->name }}</div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">تغيير كلمة المرور</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-right px-3 py-2 rounded-md text-sm text-red-600 hover:bg-red-50">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</nav>
