<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'منظومة ضابط') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Tajawal', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-slate-800 antialiased bg-slate-900 min-h-screen relative overflow-x-hidden selection:bg-indigo-500 selection:text-white" dir="rtl">
        <!-- Modern Subtle Ambient Gradients -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl"></div>
        </div>

        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Branding Header -->
            <div class="mb-6 text-center space-y-2">
                <a href="/" class="inline-flex items-center gap-3 group">
                    <div class="w-13 h-13 p-3 bg-gradient-to-tr from-indigo-600 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-white tracking-tight block">منظومة ضـابط</span>
                        <span class="text-[11px] font-semibold text-indigo-300 tracking-wider uppercase block">إدارة الحضور والجاهزية الميدانية</span>
                    </div>
                </a>
            </div>

            <!-- Login Card Container -->
            <div class="w-full sm:max-w-md bg-white/95 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-white/20">
                {{ $slot }}
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-8 text-center text-xs text-slate-400">
                <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} — منظومة ضـابط الإلكترونية المعتمدة</p>
            </div>
        </div>
    </body>
</html>
