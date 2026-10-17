<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 غير مصرح بالوصول | أداة التحديثات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Tajawal', sans-serif; } </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center space-y-4">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl mx-auto flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-slate-900">غير مصرح بالوصول إلى هذه الصفحة</h1>
        <p class="text-sm text-slate-600 leading-relaxed">
            لأسباب أمنية، يتطلب هذا المسار إما تسجيل الدخول بحساب مسؤول (Admin / Super Admin) أو تمرير مفتاح الأمان المعتمد في الرابط مثل:
        </p>
        <div class="bg-slate-100 text-slate-800 p-2.5 rounded-xl text-xs font-mono select-all" dir="ltr">
            /system/deploy-migrate?key=deploy2026
        </div>
        <div class="pt-4">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
                تسجيل الدخول للنظام
            </a>
        </div>
    </div>
</body>
</html>
