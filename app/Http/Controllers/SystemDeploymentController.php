<?php

namespace App\Http\Controllers;

use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemDeploymentController extends Controller
{
    /**
     * Check authorization via deploy key or authenticated admin.
     */
    protected function isAuthorized(Request $request): bool
    {
        $expectedKey = env('DEPLOY_KEY', 'deploy2026');

        if ($request->filled('key') && hash_equals((string) $expectedKey, (string) $request->query('key'))) {
            return true;
        }

        if (auth()->check()) {
            $user = auth()->user();
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
            if ($user->role === 'admin') {
                return true;
            }
        }

        return false;
    }

    /**
     * Display the migration and maintenance dashboard or execute requested action.
     */
    public function index(Request $request)
    {
        if (! $this->isAuthorized($request)) {
            return response()->view('system.unauthorized', [], 403);
        }

        $action = $request->query('action', 'migrate');
        $output = '';
        $cacheOutput = '';
        $seedOutput = '';
        $error = null;
        $success = true;

        try {
            if ($action === 'migrate' || $request->isMethod('post')) {
                // Run database migrations forcefully for production
                Artisan::call('migrate', ['--force' => true]);
                $output = Artisan::output();

                // Clear all Laravel caches so new routes and config apply immediately
                Artisan::call('optimize:clear');
                $cacheOutput = Artisan::output();

                // If user requested seeding the sample organization structure
                if ($request->boolean('seed_structure') || $request->query('seed') === '1') {
                    $seedOutput = $this->seedDefaultStructure();
                }
            } elseif ($action === 'clear_cache') {
                Artisan::call('optimize:clear');
                $cacheOutput = Artisan::output();
                $output = 'تم تفريغ كافة ملفات الذاكرة المؤقتة (Config, Route, View, Compiled Cache) بنجاح.';
            }
        } catch (\Throwable $e) {
            $success = false;
            $error = $e->getMessage();
        }

        // Get database table summary for peace of mind
        $tables = $this->getDatabaseTablesSummary();

        return view('system.deploy-migrate', [
            'success' => $success,
            'output' => $output,
            'cacheOutput' => $cacheOutput,
            'seedOutput' => $seedOutput,
            'error' => $error,
            'tables' => $tables,
            'key' => $request->query('key'),
        ]);
    }

    /**
     * Seed initial organizational hierarchy if none exists.
     */
    protected function seedDefaultStructure(): string
    {
        try {
            $seeder = new \Database\Seeders\RealOrganizationDataSeeder();
            $seeder->run();

            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            return 'تم إنشاء الهيكل التنظيمي المعتمد وتغذية بيانات الموظفين والمسؤولين بنجاح.';
        } catch (\Throwable $e) {
            return 'خطأ أثناء تهيئة الهيكل: ' . $e->getMessage();
        }
    }

    /**
     * Ensure demo managers exist for immediate testing.
     */
    public function ensureDemoManagersExist(?int $branchUnitId = null, ?int $centerUnitId = null): void
    {
        if ($branchUnitId === null) {
            $branchUnitId = OrganizationalUnit::where('code', 'SUB-01')->value('id');
        }
        if ($centerUnitId === null) {
            $centerUnitId = OrganizationalUnit::where('code', 'CTR-01')->value('id');
        }

        if ($branchUnitId) {
            $branchUser = \App\Models\User::firstOrCreate(
                ['email' => 'branch_admin@example.com'],
                [
                    'name' => 'قائد القيادة الفرعية الأولى',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );

            \App\Models\UserUnitAssignment::firstOrCreate(
                [
                    'user_id' => $branchUser->id,
                    'org_unit_id' => $branchUnitId,
                    'role' => \App\Models\UserUnitAssignment::ROLE_BRANCH_COMMAND_ADMIN,
                ],
                [
                    'include_sub_branches' => true,
                    'can_edit_approved' => false,
                    'can_manage_employees' => true,
                ]
            );
        }

        if ($centerUnitId) {
            $centerUser = \App\Models\User::firstOrCreate(
                ['email' => 'unit_admin@example.com'],
                [
                    'name' => 'مسؤول المركز الأول',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );

            \App\Models\UserUnitAssignment::firstOrCreate(
                [
                    'user_id' => $centerUser->id,
                    'org_unit_id' => $centerUnitId,
                    'role' => \App\Models\UserUnitAssignment::ROLE_CENTER_ADMIN,
                ],
                [
                    'include_sub_branches' => false,
                    'can_edit_approved' => false,
                    'can_manage_employees' => true,
                ]
            );
        }
    }

    /**
     * Check which core tables exist and row counts.
     */
    protected function getDatabaseTablesSummary(): array
    {
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

        $summary = [];
        foreach ($coreTables as $table => $label) {
            $exists = Schema::hasTable($table);
            $count = $exists ? DB::table($table)->count() : 0;
            $summary[] = [
                'table' => $table,
                'label' => $label,
                'exists' => $exists,
                'count' => $count,
            ];
        }

        return $summary;
    }
}
