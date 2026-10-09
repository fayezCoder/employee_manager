<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">إضافة موظف جديد</h1>
                <a href="{{ route('employees.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">عودة للموظفين →</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('employees.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="name" value="اسم الموظف الثلاثي" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="مثال: عبد الله أحمد المنصور" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="البريد الإلكتروني (اسم المستخدم)" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-right" :value="old('email')" required placeholder="employee@company.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="كلمة المرور المؤقتة" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full text-right" required placeholder="8 أحرف على الأقل" />
                        <p class="text-xs text-gray-500 mt-1">سيُطلب من الموظف تغيير كلمة المرور هذه إجبارياً عند أول دخول.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="department_id" value="القسم" />
                        <select id="department_id" name="department_id" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">اختر القسم...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" value="صلاحيات النظام" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>موظف عادي</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مدير نظام</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">إلغاء</a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">إنشاء الحساب</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
