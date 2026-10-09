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

    // Admin-only routes
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

require __DIR__.'/auth.php';
