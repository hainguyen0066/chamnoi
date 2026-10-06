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

        // Share global site settings (Ads, Analytics, SEO) across all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $settings = \Illuminate\Support\Facades\Cache::remember('global_site_settings_array', 60, function () {
                    return \App\Models\SiteSetting::all()->pluck('value', 'key')->toArray();
                });
                $view->with('globalSiteSettings', $settings);
            } catch (\Throwable $e) {
                $view->with('globalSiteSettings', []);
            }
        });
    }
}
