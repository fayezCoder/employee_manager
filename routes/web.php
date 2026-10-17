<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// All authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard - routes to admin or employee dashboard based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (from Breeze) - password change
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Attendance check-in/out (for all authenticated users)
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    // Attendance records (filtered by role in controller)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // --- New Organizational Hierarchy & Daily Roster Architecture ---
    // Daily Rosters (Scoped for assigned unit admins and super admin)
    Route::get('/rosters', [\App\Http\Controllers\DailyRosterController::class, 'index'])->name('rosters.index');
    Route::get('/rosters/incoming', [\App\Http\Controllers\DailyRosterController::class, 'incoming'])->name('rosters.incoming');
    Route::get('/rosters/unit/{orgUnit}', [\App\Http\Controllers\DailyRosterController::class, 'show'])->name('rosters.show');
    Route::put('/rosters/{roster}', [\App\Http\Controllers\DailyRosterController::class, 'update'])->name('rosters.update');
    Route::post('/rosters/{roster}/submit', [\App\Http\Controllers\DailyRosterController::class, 'submit'])->name('rosters.submit');
    Route::post('/rosters/{roster}/branch-approve', [\App\Http\Controllers\DailyRosterController::class, 'branchApprove'])->name('rosters.branch-approve');
    Route::post('/rosters/{roster}/correct', [\App\Http\Controllers\DailyRosterController::class, 'correct'])->name('rosters.correct');

    // Scoped Employee Management
    Route::resource('roster-employees', \App\Http\Controllers\EmployeeRosterController::class)
        ->parameters(['roster-employees' => 'employee'])
        ->except(['show', 'destroy']);
    Route::post('/roster-employees/{employee}/toggle-active', [\App\Http\Controllers\EmployeeRosterController::class, 'toggleActive'])->name('roster-employees.toggle-active');

    // Roster Reports & Audit
    Route::get('/roster-reports', [\App\Http\Controllers\RosterReportController::class, 'index'])->name('roster-reports.index');
    Route::get('/roster-reports/export', [\App\Http\Controllers\RosterReportController::class, 'export'])->name('roster-reports.export');
    Route::get('/roster-reports/audit-logs', [\App\Http\Controllers\RosterReportController::class, 'auditLogs'])->name('roster-reports.audit-logs');

    // Super Admin Only: Organizational Units & User Hierarchy Assignments
    Route::middleware(['can:super-admin'])->group(function () {
        Route::resource('org-units', \App\Http\Controllers\OrganizationalUnitController::class)
            ->parameters(['org-units' => 'orgUnit'])
            ->except(['show', 'destroy']);
        Route::post('/org-units/{orgUnit}/toggle-active', [\App\Http\Controllers\OrganizationalUnitController::class, 'toggleActive'])->name('org-units.toggle-active');

        Route::resource('user-management', \App\Http\Controllers\UserManagementController::class)
            ->parameters(['user-management' => 'user'])
            ->except(['show', 'destroy']);
        Route::post('/user-management/{user}/toggle-active', [\App\Http\Controllers\UserManagementController::class, 'toggleActive'])->name('user-management.toggle-active');
    });

    // Admin-only routes (Legacy features preserved)
    Route::middleware(['can:admin'])->group(function () {
        // Departments
        Route::resource('departments', DepartmentController::class)->except(['show']);

        // Employees
        Route::resource('employees', EmployeeController::class)->except(['show', 'destroy']);

        // Attendance correction
        Route::get('/attendance/{attendance}/correct', [AttendanceController::class, 'correct'])->name('attendance.correct');
        Route::post('/attendance/{attendance}/correct', [AttendanceController::class, 'storeCorrection'])->name('attendance.store-correction');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

// Hostinger / Web-based Migration and Cache Runner
Route::match(['GET', 'POST'], '/system/deploy-migrate', [\App\Http\Controllers\SystemDeploymentController::class, 'index'])
    ->name('system.deploy-migrate');

require __DIR__.'/auth.php';

