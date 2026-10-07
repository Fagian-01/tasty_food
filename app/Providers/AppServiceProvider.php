<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\View;
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
        // Badge "Pesanan (N)" di sidebar admin.
        View::composer('layouts.admin', function ($view) {
            try {
                $view->with('pendingOrders', Order::where('status', 'pending')->count());
            } catch (\Throwable $e) {
                $view->with('pendingOrders', 0);
            }
        });
    }
}
