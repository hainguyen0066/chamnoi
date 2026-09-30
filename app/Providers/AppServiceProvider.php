<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        // Giới hạn độ dài mặc định của string() còn 191 ký tự để unique index
        // trên cột utf8mb4 (191 * 4 = 764 byte) không vượt giới hạn khoá của MySQL.
        Schema::defaultStringLength(191);
    }
}
