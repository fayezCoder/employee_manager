<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة التحديثات وترحيل قاعدة البيانات | منظومة ضابط</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-indigo-200 shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7zm0 5h16M9 4v16" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900">أداة ترحيل وتحديث النظام (Hostinger Safe Runner)</h1>
                    <p class="text-sm text-slate-500">ترحيل الجداول، وتفريغ الذاكرة المؤقتة، والتهيئة بنقرة واحدة دون الحاجة لموجه الأوامر (Terminal)</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition shadow-sm">
                    <span>لوحة التحكم</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

        @if($success)
            <!-- Success Status Alert -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex items-start gap-4 text-emerald-900">
                <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h2 class="text-lg font-bold text-emerald-900">تم تنفيذ التحديثات بنجاح تام!</h2>
                    <p class="text-sm text-emerald-800 leading-relaxed">
                        تم فحص جداول قاعدة البيانات وترحيل كافة التغييرات الحديثة بأمان (قاعدة البيانات أصبحت مطابقة لآخر إصدار من الهيكل التنظيمي والتحضير اليومي)، كما تم تفريغ الذاكرة المؤقتة لكي تظهر التعديلات فوراً.
                    </p>
                </div>
            </div>
        @else
            <!-- Error Alert -->
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 flex items-start gap-4 text-rose-900">
                <div class="w-10 h-10 bg-rose-600 text-white rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h2 class="text-lg font-bold text-rose-900">حدث خطأ أثناء تنفيذ التحديثات</h2>
                    <p class="text-sm text-rose-800 leading-relaxed">{{ $error }}</p>
                </div>
            </div>
        @endif

        @if(!empty($seedOutput))
            <!-- Seed Output Notification -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-blue-900 text-sm">
                <strong>تهيئة الهيكل التنظيمي:</strong> {{ $seedOutput }}
            </div>
        @endif

        <!-- Quick Actions Panel -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b pb-3">إجراءات سريعة</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('system.deploy-migrate', array_filter(['key' => $key, 'action' => 'migrate'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>إعادة تشغيل ترحيل قاعدة البيانات (Migrate)</span>
                </a>

                <a href="{{ route('system.deploy-migrate', array_filter(['key' => $key, 'action' => 'migrate', 'seed_structure' => 1])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-sm font-medium transition"
                   onclick="return confirm('هل ترغب في إنشاء هيكل الجهات التنظيمي الافتراضي (القيادة الرئيسية، الشعب، المراكز)؟');">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>تهيئة الهيكل التنظيمي الافتراضي</span>
                </a>

                <a href="{{ route('system.deploy-migrate', array_filter(['key' => $key, 'action' => 'clear_cache'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>تفريغ الذاكرة المؤقتة فقط (Cache Clear)</span>
                </a>

                <a href="{{ route('system.deploy-migrate', array_filter(['key' => $key, 'action' => 'reset_demo'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-sm font-medium transition"
                   onclick="return confirmResetDemo();">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>تصفير البيانات التجريبية (لبدء إدخال بيانات حقيقية)</span>
                </a>
                <script>
                    function confirmResetDemo() {
                        const answer = prompt('⚠️ تحذير أمني شديد الحساسية:\n\nسيتم مسح كافة الموظفين وسجلات التحضير والمستخدمين، والإبقاء فقط على حساب المسؤول الأول والهيكل التنظيمي.\n\nلتأكيد المسح ومنع أي ضغط غير مقصود، اكتب كلمة (تصفير) في المربع أدناه ثم اضغط موافق:');
                        return answer === 'تصفير';
                    }
                </script>
            </div>
        </div>

        <!-- Database Tables Health Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b pb-3">حالة جداول قاعدة البيانات</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($tables as $table)
                    <div class="p-3 rounded-xl border {{ $table['exists'] ? 'bg-slate-50 border-slate-200' : 'bg-red-50 border-red-200' }} flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $table['label'] }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $table['table'] }}</p>
                        </div>
                        <div class="text-left">
                            @if($table['exists'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    جاهز ({{ $table['count'] }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800">
                                    غير متوفر
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Terminal / Artisan Output Logs -->
        @if(!empty($output) || !empty($cacheOutput))
            <div class="bg-slate-900 text-slate-200 rounded-2xl shadow-sm p-6 space-y-4 text-xs font-mono" dir="ltr">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2 text-slate-400">
                    <span class="font-bold">Migration & Console Execution Logs</span>
                    <span>Hostinger Safe Runner</span>
                </div>
                @if(!empty($output))
                    <div class="space-y-1">
                        <div class="text-emerald-400 font-semibold">$ php artisan migrate --force</div>
                        <pre class="whitespace-pre-wrap leading-relaxed text-slate-300">{{ trim($output) }}</pre>
                    </div>
                @endif
                @if(!empty($cacheOutput))
                    <div class="space-y-1 pt-2 border-t border-slate-800">
                        <div class="text-sky-400 font-semibold">$ php artisan optimize:clear</div>
                        <pre class="whitespace-pre-wrap leading-relaxed text-slate-300">{{ trim($cacheOutput) }}</pre>
                    </div>
                @endif
            </div>
        @endif

        <!-- Security & Usage Note -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-900 text-xs leading-relaxed space-y-2">
            <p class="font-bold text-amber-950 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>ملاحظة أمنية هامة:</span>
            </p>
            <p>
                هذا الرابط مخصص للاستخدام الآمن في الاستضافات المشتركة كـ Hostinger. يتم حمايته إما بتسجيل دخولك كمسؤول في النظام أو عبر مفتاح الأمان <code class="bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-mono font-bold">?key=deploy2026</code>. يمكنك في أي وقت تعديل المفتاح في ملف <code class="bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-mono font-bold">.env</code> عبر المتغير <code class="bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-mono font-bold">DEPLOY_KEY</code>.
            </p>
        </div>

    </div>
</body>
</html>
