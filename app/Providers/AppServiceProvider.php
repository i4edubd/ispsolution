<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define authorization gates for new features
        Gate::define('view-audit-logs', function ($user) {
            // Only allow admins, super admins, developers, and operators to view audit logs
            return $user->operator_level <= 30; // Developer, Super Admin, Admin, Operator
        });

        Gate::define('manage-api-keys', function ($user) {
            // Only allow admins and higher to manage API keys
            return $user->operator_level <= 20; // Developer, Super Admin, Admin
        });

        Gate::define('view-analytics', function ($user) {
            // Only allow operators and higher to view analytics
            return $user->operator_level <= 40; // Developer, Super Admin, Admin, Operator, Sub-operator
        });
    }
}
