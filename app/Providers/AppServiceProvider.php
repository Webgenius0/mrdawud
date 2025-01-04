<?php

namespace App\Providers;

use App\Models\DynamicPage;
use App\Models\SystemSetting;
use App\Services\TwilioService;
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
        View::composer('*', function ($view) {
            $setting = SystemSetting::first();
            $dynamicPage = DynamicPage::where('status', 1)->get();

            $view->with('setting', $setting);
            $view->with('dynamicPages', $dynamicPage);
        });
    }
}
