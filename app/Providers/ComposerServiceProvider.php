<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting; // Pastikan model Setting di-import
use Carbon\Carbon;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load settings dan bagikan ke semua view (atau hanya layout)
        view()->composer('*', function ($view) {
            // Ambil semua setting yang diperlukan
            $settings = Setting::pluck('value', 'key')->toArray();

            $view->with('settings', $settings);
        });
    }

    public function register()
    {
        //
    }
}