<?php

namespace App\Providers;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Policies\AttendanceRecordPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EmployeePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        User::class => EmployeePolicy::class,
        Department::class => DepartmentPolicy::class,
        AttendanceRecord::class => AttendanceRecordPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Simple gate: is the user an admin?
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });
    }
}
