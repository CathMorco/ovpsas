<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\RecentActivity; // <--- Import MUST be here

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
        View::composer('layouts.master', function ($view) {
            
            // REMOVED TRY-CATCH to allow debugging
            // If this crashes, it means your database table is missing or wrong.
            // Run "php artisan migrate" if that happens.
            
            $recentActivities = RecentActivity::with('user') // Ensure User model exists!
                ->latest()
                ->take(10)
                ->get();

            $view->with('recentActivities', $recentActivities);
        });
    }
}