<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        Carbon::setLocale('id');
        
        try {
            // Share Settings Globally to all views
            // Menggunakan try-catch agar tidak error saat running migration awal
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $globalSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
                
                // Helper untuk URL Logo
                $logoPath = $globalSettings['school_logo'] ?? null;
                $globalSettings['logo_url'] = ($logoPath && file_exists(public_path('storage/' . $logoPath)))
                    ? asset('storage/' . $logoPath)
                    : null; // Fallback handled in views or use default asset

                \Illuminate\Support\Facades\View::share('globalSettings', $globalSettings);
            }
        } catch (\Exception $e) {
            // Do nothing during migration/setup if table doesn't exist
        }
    }
}
