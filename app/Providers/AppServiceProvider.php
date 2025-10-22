<?php

namespace App\Providers;

use App\Models\Payment;
use App\Observers\PaymentObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(MaintenanceMode::class, function ($app) {
            return new LaravelMaintenanceMode($app);
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Payment::observe(PaymentObserver::class);
    }
}
