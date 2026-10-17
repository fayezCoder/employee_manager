<?php
/**
 * Hostinger Zero-Terminal Deployment & Database Migration Runner
 * Usage: https://demo.fayezcoder.com/deploy.php?key=deploy2026
 */

header('Content-Type: text/html; charset=utf-8');

// 1. Determine Secret Key
$secretKey = 'deploy2026';
if (file_exists(__DIR__ . '/../.env')) {
    $envContent = file_get_contents(__DIR__ . '/../.env');
    if (preg_match('/^DEPLOY_KEY=(.*)$/m', $envContent, $matches)) {
        $foundKey = trim($matches[1]);
        if (!empty($foundKey)) {
            $secretKey = $foundKey;
        }
    }
}

$providedKey = $_GET['key'] ?? '';

if (!hash_equals((string)$secretKey, (string)$providedKey)) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <title>403 غير مصرح بالوصول</title>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>body { font-family: 'Tajawal', sans-serif; }</style>
    </head>
    <body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center space-y-4">
            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl mx-auto flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-900">مفتاح الأمان غير صحيح</h1>
            <p class="text-sm text-slate-600 leading-relaxed">
                يرجى تمرير مفتاح الأمان في الرابط لتشغيل التحديثات:
            </p>
            <div class="bg-slate-100 text-slate-800 p-2.5 rounded-xl text-xs font-mono select-all" dir="ltr">
                deploy.php?key=deploy2026
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 2. Remove stale cache files to guarantee clean execution
$cacheFiles = [
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/events.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/services.php',
];
$clearedFiles = [];
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (@unlink($file)) {
            $clearedFiles[] = basename($file);
        }
    }
}

// 3. Boot Laravel Console
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$migrateOutput = '';
$optimizeOutput = '';
$seedOutput = '';
$error = null;
$success = true;

try {
    // Run Migrations
    Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = Artisan::output();

    // Clear Optimize Cache
    Artisan::call('optimize:clear');
    $optimizeOutput = Artisan::output();

    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }

    // Always run Real Organization Data Seeder to ensure all units and employees are mapped
    $seeder = new \Database\Seeders\RealOrganizationDataSeeder();
    $seeder->run();
    $seedOutput = 'تمت تهيئة وتغذية النظام بكامل الهيكل التنظيمي، وحسابات المسؤولين، وتوزيع كافة الموظفين على المراكز والشعب بنجاح!';
} catch (\Throwable $e) {
    $success = false;
    $error = $e->getMessage();
}

// Check tables
$coreTables = [
    'users' => 'المستخدمين',
    'organizational_units' => 'الجهات التنظيمية',
    'employees' => 'الموظفين',
    'user_unit_assignments' => 'تعيينات الصلاحيات للجهات',
    'daily_rosters' => 'سجلات التحضير اليومي',
    'daily_roster_items' => 'بنود التحضير للموظفين',
    'roster_audit_logs' => 'سجل تدقيق تصحيحات التحضير',
    'roster_workflow_events' => 'سجل أحداث الاعتمادات',
    'structure_audit_logs' => 'سجل تدقيق الهيكل والصلاحيات',
];

$tablesSummary = [];
foreach ($coreTables as $tableName => $tableLabel) {
    $exists = Schema::hasTable($tableName);
    $tablesSummary[] = [
        'name' => $tableName,
        'label' => $tableLabel,
        'exists' => $exists,
        'count' => $exists ? DB::table($tableName)->count() : 0,
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>أداة التحديث المباشر وقاعدة البيانات | Hostinger Direct Runner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-emerald-200 shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900">أداة التحديث المباشر لاستضافة Hostinger</h1>
                    <p class="text-sm text-slate-500">تم التحديث بنجاح عبر الملف المباشر deploy.php دون الحاجة لموجه الأوامر</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition shadow-sm">
                    <span>الانتقال للوحة التحكم</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

        @if($success)
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex items-start gap-4 text-emerald-900">
            <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="space-y-1">
                <h2 class="text-lg font-bold text-emerald-900">تم تنفيذ التحديثات وترحيل الجداول بنجاح!</h2>
                <p class="text-sm text-emerald-800 leading-relaxed">
                    تم ترحيل كافة جداول قاعدة البيانات الخاصة بالهيكل التنظيمي، الموظفين، سجلات التحضير، وسجلات التدقيق. كما تم حذف الكاش القديم لتطبيق التغييرات فوراً.
                </p>
            </div>
        </div>
        @else
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 flex items-start gap-4 text-rose-900">
            <div class="w-10 h-10 bg-rose-600 text-white rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="space-y-1">
                <h2 class="text-lg font-bold text-rose-900">حدث خطأ أثناء تنفيذ التحديثات</h2>
                <p class="text-sm text-rose-800 leading-relaxed"><?php echo htmlspecialchars($error); ?></p>
            </div>
        </div>
        @endif

        <?php if (!empty($seedOutput)): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-blue-900 text-sm">
            <strong>تهيئة الهيكل:</strong> <?php echo htmlspecialchars($seedOutput); ?>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b pb-3">إجراءات سريعة</h3>
            <div class="flex flex-wrap gap-3">
                <a href="deploy.php?key=<?php echo urlencode($providedKey); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-medium transition">
                    <span>إعادة تشغيل التحديثات (Re-run Migrate)</span>
                </a>

                <a href="deploy.php?key=<?php echo urlencode($providedKey); ?>&seed=1"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-sm font-medium transition"
                   onclick="return confirm('هل ترغب في إنشاء هيكل الجهات التنظيمي الافتراضي؟');">
                    <span>تهيئة الهيكل التنظيمي الافتراضي (القيادة الرئيسية، الشعب، المراكز)</span>
                </a>

                <a href="/login"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium transition">
                    <span>تسجيل الدخول</span>
                </a>
            </div>
        </div>

        <!-- Tables Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b pb-3">حالة جداول قاعدة البيانات الحالية</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <?php foreach ($tablesSummary as $table): ?>
                    <div class="p-3 rounded-xl border <?php echo $table['exists'] ? 'bg-slate-50 border-slate-200' : 'bg-red-50 border-red-200'; ?> flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($table['label']); ?></p>
                            <p class="text-xs font-mono text-slate-500"><?php echo htmlspecialchars($table['name']); ?></p>
                        </div>
                        <div class="text-left">
                            <?php if ($table['exists']): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    جاهز (<?php echo $table['count']; ?>)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800">
                                    غير متوفر
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Output Log -->
        <?php if (!empty($migrateOutput) || !empty($optimizeOutput)): ?>
            <div class="bg-slate-900 text-slate-200 rounded-2xl shadow-sm p-6 space-y-4 text-xs font-mono" dir="ltr">
                <div class="border-b border-slate-800 pb-2 text-slate-400 font-bold">Migration Console Log</div>
                <?php if (!empty($migrateOutput)): ?>
                    <pre class="whitespace-pre-wrap leading-relaxed text-slate-300"><?php echo htmlspecialchars(trim($migrateOutput)); ?></pre>
                <?php endif; ?>
                <?php if (!empty($optimizeOutput)): ?>
                    <div class="pt-2 border-t border-slate-800">
                        <pre class="whitespace-pre-wrap leading-relaxed text-sky-400"><?php echo htmlspecialchars(trim($optimizeOutput)); ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
